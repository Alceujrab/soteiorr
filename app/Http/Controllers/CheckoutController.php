<?php

namespace App\Http\Controllers;

use App\Actions\CompleteCheckoutAction;
use App\Actions\ReserveTicketsAction;
use App\Actions\UpsellPaymentAction;
use App\Models\Payment;
use App\Models\Raffle;
use App\Models\RafflePackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function start(Request $request, Raffle $raffle)
    {
        $request->validate([
            'package_id' => ['required', 'integer', 'exists:raffle_packages,id'],
        ]);

        $package = RafflePackage::where('raffle_id', $raffle->id)
            ->where('id', $request->integer('package_id'))
            ->firstOrFail();

        $request->session()->put('checkout', [
            'raffle_id' => $raffle->id,
            'package_id' => $package->id,
            'mode' => 'surprise',
            'numbers' => null,
        ]);

        if (Auth::check() && Auth::user()->role === 'cliente') {
            if (! Auth::user()->hasCompleteCheckoutProfile()) {
                return redirect()->route('profile.complete')
                    ->with('success', 'Complete seu cadastro para continuar a compra.');
            }

            if ($package->allows_selection) {
                return redirect()->route('checkout.select');
            }

            return redirect()->route('checkout.continue');
        }

        return redirect()->route('register')
            ->with('success', 'Para continuar a compra, complete seu cadastro. Se já tem conta, faça login.');
    }

    public function select(Request $request)
    {
        if (! Auth::check() || Auth::user()->role !== 'cliente') {
            return redirect()->route('login');
        }

        $checkout = $request->session()->get('checkout');
        if (! is_array($checkout) || empty($checkout['raffle_id']) || empty($checkout['package_id'])) {
            return redirect()->route('raffles.index')
                ->withErrors(['error' => 'Nenhuma compra em andamento.']);
        }

        $raffle = Raffle::with('packages')->findOrFail($checkout['raffle_id']);
        $package = RafflePackage::where('raffle_id', $raffle->id)
            ->where('id', $checkout['package_id'])
            ->firstOrFail();

        if (! $package->allows_selection) {
            return redirect()->route('checkout.continue');
        }

        return view('checkout.select-numbers', compact('raffle', 'package'));
    }

    public function storeSelection(Request $request)
    {
        if (! Auth::check() || Auth::user()->role !== 'cliente') {
            return redirect()->route('login');
        }

        $checkout = $request->session()->get('checkout');
        if (! is_array($checkout) || empty($checkout['package_id'])) {
            return redirect()->route('raffles.index');
        }

        $package = RafflePackage::findOrFail($checkout['package_id']);

        $mode = $request->input('mode', 'surprise');

        if ($mode === 'manual') {
            $raw = (string) $request->input('numbers', '');
            $numbers = collect(preg_split('/[\s,;]+/', $raw) ?: [])
                ->filter(fn ($n) => $n !== '')
                ->map(fn ($n) => (int) $n)
                ->values()
                ->all();

            $request->session()->put('checkout.mode', 'manual');
            $request->session()->put('checkout.numbers', $numbers);
        } else {
            $request->session()->put('checkout.mode', 'surprise');
            $request->session()->put('checkout.numbers', null);
        }

        return redirect()->route('checkout.continue');
    }

    public function continue(
        Request $request,
        ReserveTicketsAction $reserveAction,
        CompleteCheckoutAction $completeCheckout
    ) {
        if (! Auth::check() || Auth::user()->role !== 'cliente') {
            return redirect()->route('login')
                ->with('success', 'Entre na sua conta para concluir o pagamento.');
        }

        if (! Auth::user()->hasCompleteCheckoutProfile()) {
            return redirect()->route('profile.complete')
                ->with('success', 'Complete seu cadastro para continuar a compra.');
        }

        $checkout = $request->session()->get('checkout');
        if (! is_array($checkout) || empty($checkout['raffle_id']) || empty($checkout['package_id'])) {
            return redirect()->route('raffles.index')
                ->withErrors(['error' => 'Nenhuma compra em andamento. Escolha um pacote novamente.']);
        }

        $raffle = Raffle::findOrFail($checkout['raffle_id']);
        $package = RafflePackage::where('raffle_id', $raffle->id)
            ->where('id', $checkout['package_id'])
            ->firstOrFail();

        if ($package->allows_selection && ($checkout['mode'] ?? null) === null) {
            return redirect()->route('checkout.select');
        }

        $chosen = (($checkout['mode'] ?? 'surprise') === 'manual')
            ? ($checkout['numbers'] ?? [])
            : null;

        try {
            $payment = $completeCheckout->execute(
                Auth::user(),
                $raffle,
                $package,
                $reserveAction,
                $chosen
            );
            $request->session()->forget('checkout');

            return redirect()->route('payments.show', $payment->id)
                ->with('success', "Pacote {$package->name} reservado! Efetue o pagamento PIX para confirmar.");
        } catch (\Exception $e) {
            return redirect()->route($package->allows_selection ? 'checkout.select' : 'raffles.show', $package->allows_selection ? [] : [$raffle])
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function upsell(Request $request, Payment $payment, UpsellPaymentAction $upsell, ReserveTicketsAction $reserveAction)
    {
        abort_unless(Auth::check(), 403);
        abort_unless(
            Auth::id() === $payment->user_id
                || in_array(Auth::user()->role, ['admin_organizador', 'super_admin'], true),
            403
        );

        $validated = $request->validate([
            'extra_numbers' => ['required', 'integer', 'min:1', 'max:500'],
        ]);

        try {
            $result = $upsell->execute($payment, (int) $validated['extra_numbers'], $reserveAction);
        } catch (\Exception $e) {
            return redirect()->route('payments.show', $payment)
                ->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('payments.show', $result['payment'])
            ->with('success', "Adicionamos {$result['added']} números. Pague o novo valor do PIX.");
    }
}
