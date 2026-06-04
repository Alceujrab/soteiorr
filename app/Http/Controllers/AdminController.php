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
    public function storeRaffle(Request $request, \App\Actions\LogActivityAction $logActivity)
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

        $raffle = Raffle::create([
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

        $logActivity->execute("Criou a Rifa ID: {$raffle->id} - {$raffle->title}", json_encode($raffle->toArray()));

        return redirect()->route('admin.dashboard')->with('success', 'Rifa criada com sucesso!');
    }

    /**
     * Realizar sorteio de forma aleatória entre os bilhetes pagos.
     */
    public function draw(Raffle $raffle, \App\Actions\LogActivityAction $logActivity)
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

        DB::transaction(function () use ($raffle, $winnerTicket, $logActivity) {
            $draw = Draw::create([
                'raffle_id' => $raffle->id,
                'winning_number' => $winnerTicket->number,
                'winning_ticket_id' => $winnerTicket->id,
                'winning_user_id' => $winnerTicket->user_id,
                'live_url' => 'https://youtube.com/live/mock_' . uniqid(),
                'drawn_at' => now(),
            ]);

            $raffle->update(['status' => 'completed']);

            $logActivity->execute("Realizou o sorteio da Rifa ID: {$raffle->id}. Vencedor: número {$winnerTicket->number}", json_encode($draw->toArray()));
        });

        return redirect()->route('admin.dashboard')->with('success', 'Sorteio realizado com sucesso! O vencedor foi o número: ' . $winnerTicket->number);
    }

    /**
     * Exibir os logs de auditoria (Auditoria e Compliance).
     */
    public function logs()
    {
        $logs = ActivityLog::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.logs', compact('logs'));
    }

    /**
     * Exibir as configurações do sistema.
     */
    public function settings()
    {
        // Simulando parâmetros globais salvos em cache ou config
        $settings = [
            'app_name' => config('app.name'),
            'gateway_asaas_key' => '*****_asaas_secret_key_*****',
            'gateway_mercadopago_key' => '*****_mp_secret_key_*****',
            'min_tickets' => 1,
            'max_tickets' => 10,
        ];
        return view('admin.settings', compact('settings'));
    }

    /**
     * Salvar/Atualizar configurações do sistema.
     */
    public function updateSettings(Request $request, \App\Actions\LogActivityAction $logActivity)
    {
        $request->validate([
            'app_name' => 'required|string',
            'min_tickets' => 'required|integer',
            'max_tickets' => 'required|integer',
        ]);

        $logActivity->execute("Atualizou as configurações globais do sistema", json_encode($request->all()));

        return redirect()->route('admin.settings')->with('success', 'Configurações atualizadas com sucesso (Simulado)!');
    }

    /**
     * Listar todos os participantes (Gestão de Participantes).
     */
    public function participants()
    {
        $participants = User::where('role', 'cliente')
            ->withCount('tickets')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.participants', compact('participants'));
    }

    /**
     * Exibir a página de relatórios e analytics (Relatórios e Analytics).
     */
    public function reports()
    {
        $salesData = [
            'total_sales' => Ticket::where('status', 'paid')->count(),
            'total_revenue' => Payment::where('status', 'approved')->sum('amount'),
            'total_pending' => Payment::where('status', 'pending')->sum('amount'),
            'conversion_rate' => '85%', // Simulação
        ];

        return view('admin.reports', compact('salesData'));
    }

    /**
     * Exibir a listagem e gestão de usuários (Gestão de Usuários e Permissões).
     */
    public function users()
    {
        $users = User::orderBy('name', 'asc')->get();
        return view('admin.users', compact('users'));
    }

    /**
     * Exibir o histórico de notificações e templates (Notificações e Comunicação).
     */
    public function notifications()
    {
        $logs = \App\Models\NotificationLog::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.notifications', compact('logs'));
    }

    /**
     * Enviar notificação em massa para os clientes cadastrados.
     */
    public function sendNotification(Request $request, \App\Actions\LogActivityAction $logActivity)
    {
        $request->validate([
            'channel' => 'required|string',
            'template_name' => 'required|string',
            'message' => 'required|string',
        ]);

        // Selecionar todos os clientes cadastrados
        $clients = User::where('role', 'cliente')->get();

        if ($clients->isEmpty()) {
            return redirect()->back()->withErrors(['error' => 'Nenhum cliente cadastrado para receber notificações.']);
        }

        foreach ($clients as $client) {
            \App\Models\NotificationLog::create([
                'user_id' => $client->id,
                'channel' => $request->channel,
                'template_name' => $request->template_name,
                'message' => $request->message,
                'status' => 'sent',
            ]);
        }

        $logActivity->execute("Disparou notificação em massa via " . strtoupper($request->channel) . " sobre " . str_replace('_', ' ', $request->template_name));

        return redirect()->route('admin.notifications')->with('success', 'Notificação enviada com sucesso para ' . $clients->count() . ' participantes!');
    }
}
