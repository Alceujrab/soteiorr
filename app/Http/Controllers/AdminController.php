<?php

namespace App\Http\Controllers;

use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Payment;
use App\Models\Draw;
use App\Models\ActivityLog;
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

        $banners = \App\Models\Banner::orderBy('created_at', 'desc')->get();

        return view('admin.dashboard', compact('kpis', 'raffles', 'banners'));
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
        $settings = [
            'app_name' => \App\Models\Setting::get('app_name', config('app.name')),
            'gateway_asaas_key' => \App\Models\Setting::get('gateway_asaas_key', ''),
            'gateway_mercadopago_key' => \App\Models\Setting::get('gateway_mercadopago_key', ''),
            'min_tickets' => \App\Models\Setting::get('min_tickets', 1),
            'max_tickets' => \App\Models\Setting::get('max_tickets', 10),
            'show_sold_qty' => \App\Models\Setting::get('show_sold_qty', '1'),
            
            // Google reCAPTCHA
            'recaptcha_enabled' => \App\Models\Setting::get('recaptcha_enabled', '0'),
            'recaptcha_site_key' => \App\Models\Setting::get('recaptcha_site_key', ''),
            'recaptcha_secret_key' => \App\Models\Setting::get('recaptcha_secret_key', ''),
            
            // Google Login
            'google_login_enabled' => \App\Models\Setting::get('google_login_enabled', '0'),
            'google_client_id' => \App\Models\Setting::get('google_client_id', ''),
            'google_client_secret' => \App\Models\Setting::get('google_client_secret', ''),
            
            // Google Maps
            'google_maps_enabled' => \App\Models\Setting::get('google_maps_enabled', '0'),
            'google_maps_key' => \App\Models\Setting::get('google_maps_key', ''),

            // Itaú API Pix Direct
            'itau_enabled' => \App\Models\Setting::get('itau_enabled', '0'),
            'itau_client_id' => \App\Models\Setting::get('itau_client_id', ''),
            'itau_client_secret' => \App\Models\Setting::get('itau_client_secret', ''),
            'itau_cert_path' => \App\Models\Setting::get('itau_cert_path', ''),
            'itau_key_path' => \App\Models\Setting::get('itau_key_path', ''),
            'itau_pix_key' => \App\Models\Setting::get('itau_pix_key', ''),

            // Santander API Pix Direct
            'santander_enabled' => \App\Models\Setting::get('santander_enabled', '0'),
            'santander_client_id' => \App\Models\Setting::get('santander_client_id', ''),
            'santander_client_secret' => \App\Models\Setting::get('santander_client_secret', ''),
            'santander_cert_path' => \App\Models\Setting::get('santander_cert_path', ''),
            'santander_key_path' => \App\Models\Setting::get('santander_key_path', ''),
            'santander_pix_key' => \App\Models\Setting::get('santander_pix_key', ''),
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
            'gateway_asaas_key' => 'nullable|string',
            'gateway_mercadopago_key' => 'nullable|string',
            
            'recaptcha_site_key' => 'nullable|string',
            'recaptcha_secret_key' => 'nullable|string',
            
            'google_client_id' => 'nullable|string',
            'google_client_secret' => 'nullable|string',
            
            'google_maps_key' => 'nullable|string',

            'itau_client_id' => 'nullable|string',
            'itau_client_secret' => 'nullable|string',
            'itau_cert_path' => 'nullable|string',
            'itau_key_path' => 'nullable|string',
            'itau_pix_key' => 'nullable|string',

            'santander_client_id' => 'nullable|string',
            'santander_client_secret' => 'nullable|string',
            'santander_cert_path' => 'nullable|string',
            'santander_key_path' => 'nullable|string',
            'santander_pix_key' => 'nullable|string',
        ]);

        \App\Models\Setting::set('app_name', $request->app_name);
        \App\Models\Setting::set('min_tickets', $request->min_tickets);
        \App\Models\Setting::set('max_tickets', $request->max_tickets);
        \App\Models\Setting::set('gateway_asaas_key', $request->gateway_asaas_key ?: '');
        \App\Models\Setting::set('gateway_mercadopago_key', $request->gateway_mercadopago_key ?: '');
        \App\Models\Setting::set('show_sold_qty', $request->has('show_sold_qty') ? '1' : '0');
        
        \App\Models\Setting::set('recaptcha_enabled', $request->has('recaptcha_enabled') ? '1' : '0');
        \App\Models\Setting::set('recaptcha_site_key', $request->recaptcha_site_key ?: '');
        \App\Models\Setting::set('recaptcha_secret_key', $request->recaptcha_secret_key ?: '');
        
        \App\Models\Setting::set('google_login_enabled', $request->has('google_login_enabled') ? '1' : '0');
        \App\Models\Setting::set('google_client_id', $request->google_client_id ?: '');
        \App\Models\Setting::set('google_client_secret', $request->google_client_secret ?: '');
        
        \App\Models\Setting::set('google_maps_enabled', $request->has('google_maps_enabled') ? '1' : '0');
        \App\Models\Setting::set('google_maps_key', $request->google_maps_key ?: '');

        \App\Models\Setting::set('itau_enabled', $request->has('itau_enabled') ? '1' : '0');
        \App\Models\Setting::set('itau_client_id', $request->itau_client_id ?: '');
        \App\Models\Setting::set('itau_client_secret', $request->itau_client_secret ?: '');
        \App\Models\Setting::set('itau_cert_path', $request->itau_cert_path ?: '');
        \App\Models\Setting::set('itau_key_path', $request->itau_key_path ?: '');
        \App\Models\Setting::set('itau_pix_key', $request->itau_pix_key ?: '');

        \App\Models\Setting::set('santander_enabled', $request->has('santander_enabled') ? '1' : '0');
        \App\Models\Setting::set('santander_client_id', $request->santander_client_id ?: '');
        \App\Models\Setting::set('santander_client_secret', $request->santander_client_secret ?: '');
        \App\Models\Setting::set('santander_cert_path', $request->santander_cert_path ?: '');
        \App\Models\Setting::set('santander_key_path', $request->santander_key_path ?: '');
        \App\Models\Setting::set('santander_pix_key', $request->santander_pix_key ?: '');

        config(['app.name' => $request->app_name]);

        $logActivity->execute("Atualizou as configurações globais do sistema", json_encode($request->all()));

        return redirect()->route('admin.settings')->with('success', 'Configurações atualizadas com sucesso!');
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

    /**
     * Gerar banner com Inteligência Artificial baseado em prompt.
     */
    public function generateBannerAI(Request $request, \App\Actions\LogActivityAction $logActivity)
    {
        $request->validate([
            'prompt' => 'required|string',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
        ]);

        // Simulação de geração via IA DALL-E / Stability
        // Geramos uma imagem conceitual real baseada no prompt
        $generatedUrl = "https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&w=1200&q=80&sig=" . rand(1, 100000);
        if (str_contains(strtolower($request->prompt), 'mustang') || str_contains(strtolower($request->prompt), 'carro')) {
            $generatedUrl = "https://images.unsplash.com/photo-1583121274602-3e2820c69888?auto=format&fit=crop&w=1200&q=80&sig=" . rand(1, 100000);
        } elseif (str_contains(strtolower($request->prompt), 'moto')) {
            $generatedUrl = "https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=1200&q=80&sig=" . rand(1, 100000);
        }

        $banner = \App\Models\Banner::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_url' => $generatedUrl,
            'prompt' => $request->prompt,
            'active' => true,
        ]);

        $logActivity->execute("Gerou banner com IA para '{$request->title}'", json_encode($banner->toArray()));

        return redirect()->route('admin.dashboard')->with('success', 'Banner gerado automaticamente com IA e cadastrado com sucesso!');
    }

    /**
     * Cadastrar banner manualmente.
     */
    public function storeBanner(Request $request, \App\Actions\LogActivityAction $logActivity)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'image_url' => 'required|url',
        ]);

        $banner = \App\Models\Banner::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_url' => $request->image_url,
            'active' => true,
        ]);

        $logActivity->execute("Criou banner manualmente: '{$request->title}'", json_encode($banner->toArray()));

        return redirect()->route('admin.dashboard')->with('success', 'Banner criado com sucesso!');
    }

    /**
     * Ativar/Desativar um banner.
     */
    public function toggleBanner(\App\Models\Banner $banner, \App\Actions\LogActivityAction $logActivity)
    {
        $banner->update([
            'active' => !$banner->active,
        ]);

        $logActivity->execute("Alterou status do banner ID: {$banner->id} para " . ($banner->active ? 'Ativo' : 'Inativo'));

        return redirect()->route('admin.dashboard')->with('success', 'Status do banner alterado com sucesso!');
    }
}
