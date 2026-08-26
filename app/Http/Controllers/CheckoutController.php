<?php

namespace App\Http\Controllers;

use App\Actions\ReserveTicketsAction;
use App\Models\Raffle;
use App\Models\RafflePackage;
use App\Services\PaymentService;
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
        ]);

        if (Auth::check() && Auth::user()->role === 'cliente') {
            return redirect()->route('checkout.continue');
        }

        return redirect()->route('register')
            ->with('success', 'Para continuar a compra, complete seu cadastro. Se já tem conta, faça login.');
    }

    public function continue(Request $request, ReserveTicketsAction $reserveAction, PaymentService $paymentService)
    {
        if (! Auth::check() || Auth::user()->role !== 'cliente') {
            return redirect()->route('login')
                ->with('success', 'Entre na sua conta para concluir o pagamento.');
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

        $user = Auth::user();

        try {
            $numbers = $reserveAction->pickRandomAvailableNumbers($raffle, $package->numbers_qty);
            $tickets = $reserveAction->execute($user, $raffle, $numbers);
            $payment = $paymentService->createPayment(
                $user,
                $tickets,
                'asaas',
                'pix',
                (float) $package->price,
                $package->id
            );

            $request->session()->forget('checkout');

            return redirect()->route('payments.show', $payment->id)
                ->with('success', "Pacote {$package->name} reservado! Efetue o pagamento PIX para confirmar.");
        } catch (\Exception $e) {
            return redirect()->route('raffles.show', $raffle)
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
