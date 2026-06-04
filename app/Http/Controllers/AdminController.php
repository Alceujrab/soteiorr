<?php

namespace App\Http\Controllers;

use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Payment;
use App\Models\Draw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Exibir o Dashboard Administrativo com estatísticas em tempo real.
     */
    public function dashboard()
    {
        // Simular login do admin de teste se não estiver logado
        if (!Auth::check() || Auth::user()->role !== 'admin_organizador') {
            $admin = User::where('role', 'admin_organizador')->first();
            if ($admin) {
                Auth::login($admin);
            }
        }

        $kpis = [
            'total_sales' => Ticket::where('status', 'paid')->count(),
            'total_revenue' => Payment::where('status', 'approved')->sum('amount'),
            'total_participants' => User::where('role', 'cliente')->count(),
            'active_raffles' => Raffle::where('status', 'active')->count(),
        ];

        $raffles = Raffle::withCount(['tickets' => function($q) {
            $q->where('status', 'paid');
        }])->get();

        return view('admin.dashboard', compact('kpis', 'raffles'));
    }

    /**
     * Exibir formulário de nova rifa.
     */
    public function createRaffle()
    {
        return view('admin.create_raffle');
    }

    /**
     * Salvar nova rifa.
     */
    public function storeRaffle(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'total_numbers' => 'required|integer|min:10',
            'prize_name' => 'required|string|max:255',
            'prize_description' => 'nullable|string',
            'draw_date' => 'required|date',
            'image_url' => 'nullable|url',
        ]);

        Raffle::create([
            'user_id' => Auth::id() ?: User::where('role', 'admin_organizador')->first()->id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'total_numbers' => $request->total_numbers,
            'status' => 'active',
            'prize_name' => $request->prize_name,
            'prize_description' => $request->prize_description,
            'image_url' => $request->image_url ?: 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&w=800&q=80',
            'draw_date' => $request->draw_date,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Rifa criada com sucesso!');
    }

    /**
     * Realizar sorteio de forma aleatória entre os bilhetes pagos.
     */
    public function draw(Raffle $raffle)
    {
        if ($raffle->status === 'completed') {
            return redirect()->back()->withErrors(['error' => 'Esta rifa já foi sorteada.']);
        }

        // Obter bilhetes pagos
        $paidTickets = Ticket::where('raffle_id', $raffle->id)
            ->where('status', 'paid')
            ->get();

        if ($paidTickets->isEmpty()) {
            return redirect()->back()->withErrors(['error' => 'Não é possível realizar o sorteio porque nenhum número foi pago ainda.']);
        }

        // Escolher um ganhador aleatório dos pagos
        $winnerTicket = $paidTickets->random();

        DB::transaction(function () use ($raffle, $winnerTicket) {
            Draw::create([
                'raffle_id' => $raffle->id,
                'winning_number' => $winnerTicket->number,
                'winning_ticket_id' => $winnerTicket->id,
                'winning_user_id' => $winnerTicket->user_id,
                'live_url' => 'https://youtube.com/live/mock_' . uniqid(),
                'drawn_at' => now(),
            ]);

            $raffle->update(['status' => 'completed']);
        });

        return redirect()->route('admin.dashboard')->with('success', 'Sorteio realizado com sucesso! O vencedor foi o número: ' . $winnerTicket->number);
    }
}
