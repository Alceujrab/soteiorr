<?php

namespace App\Http\Controllers;

use App\Models\Raffle;
use App\Models\User;
use App\Models\Ticket;
use App\Actions\ReserveTicketsAction;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RaffleController extends Controller
{
    /**
     * Listar todas as rifas ativas.
     */
    public function index()
    {
        $raffles = Raffle::where('status', 'active')->get();
        return view('raffles.index', compact('raffles'));
    }

    /**
     * Exibir detalhes de uma rifa e o grid de números.
     */
    public function show(Raffle $raffle)
    {
        // Carregar bilhetes ocupados (reservados e pagos)
        $takenTickets = Ticket::where('raffle_id', $raffle->id)
            ->get()
            ->keyBy('number');

        return view('raffles.show', compact('raffle', 'takenTickets'));
    }

    /**
     * Processar a compra/reserva de números.
     */
    public function buy(Request $request, Raffle $raffle, ReserveTicketsAction $reserveAction, PaymentService $paymentService)
    {
        $request->validate([
            'numbers' => 'required|array|min:1',
            'numbers.*' => 'integer',
        ]);

        // Simular login do cliente de teste se não estiver logado
        if (!Auth::check()) {
            $user = User::where('role', 'cliente')->first() ?: User::first();
            Auth::login($user);
        }

        $user = Auth::user();

        try {
            // 1. Reservar os números
            $tickets = $reserveAction->execute($user, $raffle, $request->input('numbers'));

            // 2. Criar o pagamento correspondente
            $payment = $paymentService->createPayment($user, $tickets);

            return redirect()->route('payments.show', $payment->id)
                ->with('success', 'Números reservados! Efetue o pagamento PIX para confirmar.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Exibir os bilhetes adquiridos pelo cliente logado.
     */
    public function myTickets()
    {
        if (!Auth::check()) {
            $user = User::where('role', 'cliente')->first() ?: User::first();
            Auth::login($user);
        }

        $user = Auth::user();

        $tickets = Ticket::with(['raffle', 'payment'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('raffles.my_tickets', compact('tickets'));
    }
}
