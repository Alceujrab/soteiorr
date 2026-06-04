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
        $banners = \App\Models\Banner::where('active', true)->get();
        
        // Se não houver nenhum banner, cria alguns banners padrão para exibição inicial premium
        if ($banners->isEmpty()) {
            \App\Models\Banner::create([
                'title' => 'Sorteio de Luxo: Mustang GT',
                'subtitle' => 'Adquira seus bilhetes a partir de R$ 5,00 e concorra!',
                'image_url' => 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?auto=format&fit=crop&w=1200&q=80',
                'active' => true,
            ]);
            \App\Models\Banner::create([
                'title' => 'BMW M4 Competition',
                'subtitle' => 'O esportivo dos seus sonhos pode ser seu neste domingo.',
                'image_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=1200&q=80',
                'active' => true,
            ]);
            $banners = \App\Models\Banner::where('active', true)->get();
        }

        return view('raffles.index', compact('raffles', 'banners'));
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
        $mode = $request->input('mode', 'manual');

        if ($mode === 'auto') {
            $request->validate([
                'quantity' => 'required|integer|min:1|max:100',
            ]);

            $quantity = (int)$request->input('quantity');

            // Encontrar números disponíveis
            $takenNumbers = Ticket::where('raffle_id', $raffle->id)
                ->pluck('number')
                ->toArray();

            $availableNumbers = [];
            for ($i = 1; $i <= $raffle->total_numbers; $i++) {
                if (!in_array($i, $takenNumbers)) {
                    $availableNumbers[] = $i;
                }
            }

            if (count($availableNumbers) < $quantity) {
                return redirect()->back()->withErrors(['error' => 'Não há números disponíveis suficientes para a quantidade solicitada.']);
            }

            // Selecionar aleatoriamente
            shuffle($availableNumbers);
            $numbers = array_slice($availableNumbers, 0, $quantity);
        } else {
            $request->validate([
                'numbers' => 'required|array|min:1',
                'numbers.*' => 'integer',
            ]);
            $numbers = $request->input('numbers');
        }

        // Simular login do cliente de teste se não estiver logado
        if (!Auth::check()) {
            $user = User::where('role', 'cliente')->first() ?: User::first();
            Auth::login($user);
        }

        $user = Auth::user();

        try {
            // 1. Reservar os números
            $tickets = $reserveAction->execute($user, $raffle, $numbers);

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

    /**
     * Exibir o recibo de compra.
     */
    public function receipt(\App\Models\Payment $payment)
    {
        $payment->load(['user', 'tickets.raffle']);
        return view('payments.receipt', compact('payment'));
    }

    /**
     * Exibir validador de bilhetes público.
     */
    public function validateTicket(Request $request, $id = null)
    {
        $payment = null;
        if ($id) {
            $payment = \App\Models\Payment::with(['user', 'tickets.raffle'])->find($id);
        }

        $maskedUser = null;
        if ($payment && $payment->user) {
            $user = $payment->user;
            
            // Regra LGPD: Verificar se está logado e se é o proprietário ou admin
            $canViewFullDetails = Auth::check() && (Auth::id() === $user->id || in_array(Auth::user()->role, ['super_admin', 'admin_organizador']));
            
            if (!$canViewFullDetails) {
                $maskedUser = (object)[
                    'name' => $this->maskName($user->name),
                    'cpf' => $this->maskCpf($user->cpf),
                ];
            } else {
                $maskedUser = $user;
            }
        }

        return view('raffles.validate', compact('payment', 'maskedUser'));
    }

    public function validateTicketPost(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = str_replace('tx_', '', $request->input('code'));
        $payment = \App\Models\Payment::where('gateway_transaction_id', 'tx_' . $code)
            ->orWhere('gateway_transaction_id', $code)
            ->orWhere('id', $code)
            ->first();

        if (!$payment) {
            return redirect()->back()->withErrors(['error' => 'Código de bilhete ou recibo não encontrado ou inválido.']);
        }

        return redirect()->route('raffles.validate-ticket', $payment->id);
    }

    private function maskName($name)
    {
        $parts = explode(' ', $name);
        $maskedParts = array_map(function ($part) {
            if (strlen($part) <= 2) {
                return $part;
            }
            return substr($part, 0, 2) . str_repeat('*', strlen($part) - 2);
        }, $parts);
        return implode(' ', $maskedParts);
    }

    private function maskCpf($cpf)
    {
        $clean = preg_replace('/\D/', '', $cpf);
        if (strlen($clean) !== 11) {
            return $cpf;
        }
        return $clean[0] . str_repeat('*', 9) . $clean[10];
    }
}
