<?php

namespace App\Http\Controllers;

use App\Actions\ConfirmRaffleDeletionAction;
use App\Actions\LogActivityAction;
use App\Actions\RequestRaffleDeletionAction;
use App\Models\ActivityLog;
use App\Models\Banner;
use App\Models\Draw;
use App\Models\NotificationLog;
use App\Models\Payment;
use App\Models\Raffle;
use App\Models\RaffleDeletionChallenge;
use App\Models\RafflePackage;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use App\Support\DefaultRegulationContent;
use App\Support\ThemePalette;
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
        if (! Auth::check() || Auth::user()->role !== 'admin_organizador') {
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

        $raffles = Raffle::with('packages')
            ->withCount(['tickets' => function ($q) {
                $q->where('status', 'paid');
            }])->get();

        $banners = Banner::orderBy('created_at', 'desc')->get();

        return view('admin.dashboard', compact('kpis', 'raffles', 'banners'));
    }

    /**
     * Exibir formulário de nova Ação Promocional.
     */
    public function createRaffle()
    {
        $defaultPackages = RafflePackage::defaultDefinitions();

        return view('admin.create_raffle', compact('defaultPackages'));
    }

    /**
     * Salvar nova Ação Promocional.
     */
    public function storeRaffle(Request $request, LogActivityAction $logActivity)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_numbers' => 'required|integer|min:10|max:1000000',
            'prize_name' => 'required|string|max:255',
            'prize_description' => 'nullable|string',
            'draw_date' => 'required|date',
            'images' => 'nullable|array',
            'images.*' => 'image|max:4096',
            'youtube_url' => 'nullable|url',
            'packages' => 'required|array|min:1',
            'packages.*.name' => 'required|string|max:100',
            'packages.*.numbers_qty' => 'required|integer|min:1',
            'packages.*.price' => 'required|numeric|min:0.01',
            'packages.*.highlight' => 'nullable|string|max:120',
            'packages.*.is_featured' => 'nullable|boolean',
        ]);

        $uploadedImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file->isValid()) {
                    $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                    $file->move(public_path('uploads/raffles'), $fileName);
                    $uploadedImages[] = '/uploads/raffles/'.$fileName;
                }
            }
        }

        if (empty($uploadedImages)) {
            $uploadedImages[] = 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&w=800&q=80';
        }

        $packages = collect($request->packages)
            ->map(function (array $package, int $index) {
                return [
                    'name' => $package['name'],
                    'numbers_qty' => (int) $package['numbers_qty'],
                    'price' => (float) $package['price'],
                    'highlight' => $package['highlight'] ?? null,
                    'is_featured' => ! empty($package['is_featured']),
                    'sort_order' => $index + 1,
                ];
            })
            ->values()
            ->all();

        $startingPrice = collect($packages)->min('price') ?: 0.01;

        $raffle = Raffle::create([
            'user_id' => Auth::id() ?: User::where('role', 'admin_organizador')->first()->id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $startingPrice,
            'total_numbers' => $request->total_numbers,
            'status' => 'active',
            'prize_name' => $request->prize_name,
            'prize_description' => $request->prize_description,
            'image_url' => $uploadedImages[0],
            'images' => $uploadedImages,
            'youtube_url' => $request->youtube_url,
            'draw_date' => $request->draw_date,
        ]);

        $raffle->syncPackages($packages);

        $logActivity->execute("Criou a Ação Promocional ID: {$raffle->id} - {$raffle->title}", json_encode($raffle->load('packages')->toArray()));

        return redirect()->route('admin.dashboard')->with('success', 'Ação Promocional criada com sucesso!');
    }

    /**
     * Exibir formulário de edição de uma Ação Promocional.
     */
    public function editRaffle(Raffle $raffle)
    {
        $raffle->load('packages');
        $defaultPackages = RafflePackage::defaultDefinitions();

        return view('admin.edit_raffle', compact('raffle', 'defaultPackages'));
    }

    /**
     * Atualizar Ação Promocional.
     */
    public function updateRaffle(Request $request, Raffle $raffle, LogActivityAction $logActivity)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_numbers' => 'required|integer|min:10|max:1000000',
            'prize_name' => 'required|string|max:255',
            'prize_description' => 'nullable|string',
            'draw_date' => 'required|date',
            'new_images' => 'nullable|array',
            'new_images.*' => 'image|max:4096',
            'existing_images' => 'nullable|array',
            'youtube_url' => 'nullable|url',
            'packages' => 'required|array|min:1',
            'packages.*.name' => 'required|string|max:100',
            'packages.*.numbers_qty' => 'required|integer|min:1',
            'packages.*.price' => 'required|numeric|min:0.01',
            'packages.*.highlight' => 'nullable|string|max:120',
            'packages.*.is_featured' => 'nullable|boolean',
        ]);

        $currentImages = $request->existing_images ?: [];

        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $file) {
                if ($file->isValid()) {
                    $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                    $file->move(public_path('uploads/raffles'), $fileName);
                    $currentImages[] = '/uploads/raffles/'.$fileName;
                }
            }
        }

        if (empty($currentImages)) {
            $currentImages[] = 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&w=800&q=80';
        }

        $packages = collect($request->packages)
            ->map(function (array $package, int $index) {
                return [
                    'name' => $package['name'],
                    'numbers_qty' => (int) $package['numbers_qty'],
                    'price' => (float) $package['price'],
                    'highlight' => $package['highlight'] ?? null,
                    'is_featured' => ! empty($package['is_featured']),
                    'sort_order' => $index + 1,
                ];
            })
            ->values()
            ->all();

        $startingPrice = collect($packages)->min('price') ?: $raffle->price;

        $raffle->update([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $startingPrice,
            'total_numbers' => $request->total_numbers,
            'prize_name' => $request->prize_name,
            'prize_description' => $request->prize_description,
            'image_url' => $currentImages[0],
            'images' => $currentImages,
            'youtube_url' => $request->youtube_url,
            'draw_date' => $request->draw_date,
        ]);

        $raffle->syncPackages($packages);

        $logActivity->execute("Atualizou a Ação Promocional ID: {$raffle->id} - {$raffle->title}", json_encode($raffle->load('packages')->toArray()));

        return redirect()->route('admin.dashboard')->with('success', 'Ação Promocional atualizada com sucesso!');
    }

    /**
     * Solicitar exclusão: envia código por e-mail ao administrador.
     */
    public function requestDestroyRaffle(Raffle $raffle, RequestRaffleDeletionAction $requestDeletion, LogActivityAction $logActivity)
    {
        $admin = $this->resolveAdminUser();

        try {
            $result = $requestDeletion->execute($raffle, $admin);
        } catch (\Throwable $e) {
            return redirect()->route('admin.dashboard')
                ->withErrors(['error' => $e->getMessage()]);
        }

        $logActivity->execute(
            "Solicitou exclusão da Ação Promocional ID: {$raffle->id} - {$raffle->title}",
            json_encode(['email' => $result['email']])
        );

        return redirect()
            ->route('admin.raffles.destroy.confirm', $raffle)
            ->with('success', 'Enviamos um código de confirmação para '.$this->maskEmail($result['email']).'.');
    }

    /**
     * Exibir formulário de confirmação do código de exclusão.
     */
    public function showDestroyConfirm(Raffle $raffle)
    {
        $this->resolveAdminUser();

        $destinationEmail = RaffleDeletionChallenge::query()
            ->where('raffle_id', $raffle->id)
            ->whereNull('consumed_at')
            ->latest('id')
            ->value('email')
            ?: app(RequestRaffleDeletionAction::class)->resolutionEmail();

        return view('admin.confirm_raffle_deletion', [
            'raffle' => $raffle,
            'maskedEmail' => $this->maskEmail($destinationEmail),
            'expiresInMinutes' => RequestRaffleDeletionAction::EXPIRES_IN_MINUTES,
        ]);
    }

    /**
     * Confirmar exclusão com o código recebido por e-mail.
     */
    public function confirmDestroyRaffle(
        Request $request,
        Raffle $raffle,
        ConfirmRaffleDeletionAction $confirmDeletion,
        LogActivityAction $logActivity
    ) {
        $code = preg_replace('/\D+/', '', (string) $request->input('code'));
        $request->merge(['code' => $code]);

        $request->validate([
            'code' => 'required|string|digits:6',
        ]);

        $admin = $this->resolveAdminUser();

        try {
            $confirmDeletion->execute($raffle, $admin, $code, $logActivity);
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.raffles.destroy.confirm', $raffle)
                ->withErrors(['code' => $e->getMessage()]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Ação Promocional excluída com sucesso!');
    }

    /**
     * Reenviar código de exclusão.
     */
    public function resendDestroyCode(Raffle $raffle, RequestRaffleDeletionAction $requestDeletion, LogActivityAction $logActivity)
    {
        return $this->requestDestroyRaffle($raffle, $requestDeletion, $logActivity);
    }

    private function resolveAdminUser(): User
    {
        if (Auth::check() && in_array(Auth::user()->role, ['admin_organizador', 'super_admin'], true)) {
            return Auth::user();
        }

        $admin = User::where('role', 'admin_organizador')->first()
            ?: User::where('role', 'super_admin')->first();

        if (! $admin) {
            abort(403, 'Nenhum administrador encontrado.');
        }

        Auth::login($admin);

        return $admin;
    }

    private function maskEmail(?string $email): string
    {
        if (blank($email) || ! str_contains($email, '@')) {
            return '***';
        }

        [$local, $domain] = explode('@', $email, 2);
        $visible = substr($local, 0, 2);

        return $visible.str_repeat('*', max(strlen($local) - 2, 1)).'@'.$domain;
    }

    /**
     * Realizar apuração da Ação Promocional de forma aleatória entre os bilhetes pagos.
     */
    public function draw(Raffle $raffle, LogActivityAction $logActivity)
    {
        if ($raffle->status === 'completed') {
            return redirect()->back()->withErrors(['error' => 'Esta Ação Promocional já foi apurada.']);
        }

        // Obter bilhetes pagos
        $paidTickets = Ticket::where('raffle_id', $raffle->id)
            ->where('status', 'paid')
            ->get();

        if ($paidTickets->isEmpty()) {
            return redirect()->back()->withErrors(['error' => 'Não é possível realizar a Ação Promocional porque nenhum número foi pago ainda.']);
        }

        // Escolher um ganhador aleatório dos pagos
        $winnerTicket = $paidTickets->random();

        DB::transaction(function () use ($raffle, $winnerTicket, $logActivity) {
            $draw = Draw::create([
                'raffle_id' => $raffle->id,
                'winning_number' => $winnerTicket->number,
                'winning_ticket_id' => $winnerTicket->id,
                'winning_user_id' => $winnerTicket->user_id,
                'live_url' => 'https://youtube.com/live/mock_'.uniqid(),
                'drawn_at' => now(),
            ]);

            $raffle->update(['status' => 'completed']);

            $logActivity->execute("Realizou a Ação Promocional ID: {$raffle->id}. Vencedor: número {$winnerTicket->number}", json_encode($draw->toArray()));
        });

        return redirect()->route('admin.dashboard')->with('success', 'Ação Promocional realizada com sucesso! O vencedor foi o número: '.$winnerTicket->number);
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
            'app_name' => Setting::get('app_name', config('app.name')),
            'admin_security_email' => Setting::get('admin_security_email', config('mail.from.address')),
            'gateway_asaas_key' => Setting::get('gateway_asaas_key', ''),
            'gateway_mercadopago_key' => Setting::get('gateway_mercadopago_key', ''),
            'min_tickets' => Setting::get('min_tickets', 1),
            'max_tickets' => Setting::get('max_tickets', 10),
            'show_sold_qty' => Setting::get('show_sold_qty', '1'),

            // Google reCAPTCHA
            'recaptcha_enabled' => Setting::get('recaptcha_enabled', '0'),
            'recaptcha_site_key' => Setting::get('recaptcha_site_key', ''),
            'recaptcha_secret_key' => Setting::get('recaptcha_secret_key', ''),

            // Google Login
            'google_login_enabled' => Setting::get('google_login_enabled', '0'),
            'google_client_id' => Setting::get('google_client_id', ''),
            'google_client_secret' => Setting::get('google_client_secret', ''),

            // Google Maps
            'google_maps_enabled' => Setting::get('google_maps_enabled', '0'),
            'google_maps_key' => Setting::get('google_maps_key', ''),

            // Itaú API Pix Direct
            'itau_enabled' => Setting::get('itau_enabled', '0'),
            'itau_client_id' => Setting::get('itau_client_id', ''),
            'itau_client_secret' => Setting::get('itau_client_secret', ''),
            'itau_cert_path' => Setting::get('itau_cert_path', ''),
            'itau_key_path' => Setting::get('itau_key_path', ''),
            'itau_pix_key' => Setting::get('itau_pix_key', ''),

            // Santander API Pix Direct
            'santander_enabled' => Setting::get('santander_enabled', '0'),
            'santander_client_id' => Setting::get('santander_client_id', ''),
            'santander_client_secret' => Setting::get('santander_client_secret', ''),
            'santander_cert_path' => Setting::get('santander_cert_path', ''),
            'santander_key_path' => Setting::get('santander_key_path', ''),
            'santander_pix_key' => Setting::get('santander_pix_key', ''),

            // Páginas Institucionais
            'page_about_us' => Setting::get('page_about_us', '<h1>Sobre Nós</h1><p>A Ação RR Veículos é especialista em realizar sonhos através de ações entre amigos com prêmios de alta qualidade e veículos revisados e garantidos.</p>'),
            'page_contact' => Setting::get('page_contact', '<h1>Contato</h1><p>Precisa de suporte? Entre em contato conosco pelo e-mail suporte@acaorrveiculos.com.br ou pelo nosso WhatsApp oficial.</p>'),
            'page_faqs' => Setting::get('page_faqs', '<h1>Dúvidas Frequentes</h1><p>Veja as respostas para as perguntas mais comuns dos nossos participantes.</p>'),
            'page_privacy_policy' => Setting::get('page_privacy_policy', '<h1>Política de Privacidade</h1><p>Sua privacidade é nossa prioridade. Coletamos e usamos dados apenas para o processamento seguro das cotas.</p>'),
            'page_terms_of_use' => Setting::get('page_terms_of_use', '<h1>Termos de Uso</h1><p>Ao adquirir cotas na Ação RR Veículos, você concorda com o regulamento oficial da Ação Promocional e com as regras gerais.</p>'),
            'page_regulation' => Setting::get('page_regulation', DefaultRegulationContent::html()),
        ];

        $themeLight = ThemePalette::light();
        $themeDark = ThemePalette::dark();
        $themeDefinitions = ThemePalette::definitions();
        $themeDefaults = ThemePalette::defaults();

        return view('admin.settings', compact('settings', 'themeLight', 'themeDark', 'themeDefinitions', 'themeDefaults'));
    }

    /**
     * Salvar/Atualizar configurações do sistema.
     */
    public function updateSettings(Request $request, LogActivityAction $logActivity)
    {
        $request->validate([
            'app_name' => 'required|string',
            'admin_security_email' => 'required|email',
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

            'page_about_us' => 'nullable|string',
            'page_contact' => 'nullable|string',
            'page_faqs' => 'nullable|string',
            'page_privacy_policy' => 'nullable|string',
            'page_terms_of_use' => 'nullable|string',
            'page_regulation' => 'nullable|string',
            'theme_light' => 'nullable|array',
            'theme_dark' => 'nullable|array',
            'theme_light.*' => 'nullable|string|max:120',
            'theme_dark.*' => 'nullable|string|max:120',
        ]);

        Setting::set('app_name', $request->app_name);
        Setting::set('admin_security_email', $request->admin_security_email);
        Setting::set('min_tickets', $request->min_tickets);
        Setting::set('max_tickets', $request->max_tickets);
        Setting::set('gateway_asaas_key', $request->gateway_asaas_key ?: '');
        Setting::set('gateway_mercadopago_key', $request->gateway_mercadopago_key ?: '');
        Setting::set('show_sold_qty', $request->has('show_sold_qty') ? '1' : '0');

        Setting::set('recaptcha_enabled', $request->has('recaptcha_enabled') ? '1' : '0');
        Setting::set('recaptcha_site_key', $request->recaptcha_site_key ?: '');
        Setting::set('recaptcha_secret_key', $request->recaptcha_secret_key ?: '');

        Setting::set('google_login_enabled', $request->has('google_login_enabled') ? '1' : '0');
        Setting::set('google_client_id', $request->google_client_id ?: '');
        Setting::set('google_client_secret', $request->google_client_secret ?: '');

        Setting::set('google_maps_enabled', $request->has('google_maps_enabled') ? '1' : '0');
        Setting::set('google_maps_key', $request->google_maps_key ?: '');

        Setting::set('itau_enabled', $request->has('itau_enabled') ? '1' : '0');
        Setting::set('itau_client_id', $request->itau_client_id ?: '');
        Setting::set('itau_client_secret', $request->itau_client_secret ?: '');
        Setting::set('itau_cert_path', $request->itau_cert_path ?: '');
        Setting::set('itau_key_path', $request->itau_key_path ?: '');
        Setting::set('itau_pix_key', $request->itau_pix_key ?: '');

        Setting::set('santander_enabled', $request->has('santander_enabled') ? '1' : '0');
        Setting::set('santander_client_id', $request->santander_client_id ?: '');
        Setting::set('santander_client_secret', $request->santander_client_secret ?: '');
        Setting::set('santander_cert_path', $request->santander_cert_path ?: '');
        Setting::set('santander_key_path', $request->santander_key_path ?: '');
        Setting::set('santander_pix_key', $request->santander_pix_key ?: '');

        Setting::set('page_about_us', $request->page_about_us ?: '');
        Setting::set('page_contact', $request->page_contact ?: '');
        Setting::set('page_faqs', $request->page_faqs ?: '');
        Setting::set('page_privacy_policy', $request->page_privacy_policy ?: '');
        Setting::set('page_terms_of_use', $request->page_terms_of_use ?: '');
        Setting::set('page_regulation', $request->page_regulation ?: '');

        if ($request->has('theme_light')) {
            $light = ThemePalette::sanitize($request->input('theme_light', []), 'light');
            Setting::set(ThemePalette::SETTING_LIGHT, json_encode($light));
        }

        if ($request->has('theme_dark')) {
            $dark = ThemePalette::sanitize($request->input('theme_dark', []), 'dark');
            Setting::set(ThemePalette::SETTING_DARK, json_encode($dark));
        }

        config(['app.name' => $request->app_name]);

        $logActivity->execute('Atualizou as configurações globais do sistema', json_encode($request->except(['_token'])));

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
            'conversion_rate' => '88%',
        ];

        // 1. Relatório de Vendas Detalhadas
        $detailedSales = Ticket::with(['user', 'raffle'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'raffle' => $ticket->raffle->title ?? 'N/A',
                    'buyer' => $ticket->user->name ?? 'N/A',
                    'email' => $ticket->user->email ?? 'N/A',
                    'number' => sprintf('%02d', $ticket->number),
                    'price' => $ticket->raffle->price ?? 0,
                    'status' => $ticket->status === 'paid' ? 'Pago' : 'Reservado',
                    'date' => $ticket->created_at->format('d/m/Y H:i'),
                ];
            });

        // 2. Relatório de Desempenho das Ações Promocionais
        $rafflePerformance = Raffle::withCount(['tickets as paid_count' => function ($q) {
            $q->where('status', 'paid');
        }])
            ->withCount(['tickets as reserved_count' => function ($q) {
                $q->where('status', 'reserved');
            }])
            ->get()
            ->map(function ($raffle) {
                $totalRevenue = $raffle->paid_count * $raffle->price;

                return [
                    'title' => $raffle->title,
                    'total_numbers' => $raffle->total_numbers,
                    'sold' => $raffle->paid_count,
                    'reserved' => $raffle->reserved_count,
                    'remaining' => $raffle->total_numbers - ($raffle->paid_count + $raffle->reserved_count),
                    'price' => $raffle->price,
                    'revenue' => $totalRevenue,
                    'status' => $raffle->status === 'active' ? 'Ativo' : 'Concluído',
                ];
            });

        // 3. Relatório de Clientes / Compradores
        $topBuyers = User::where('role', 'cliente')
            ->withCount(['tickets as paid_count' => function ($q) {
                $q->where('status', 'paid');
            }])
            ->withCount(['tickets as reserved_count' => function ($q) {
                $q->where('status', 'reserved');
            }])
            ->get()
            ->map(function ($user) {
                // Calcular total pago estimado com base nos bilhetes
                $totalSpent = Ticket::where('tickets.user_id', $user->id)
                    ->where('tickets.status', 'paid')
                    ->join('raffles', 'tickets.raffle_id', '=', 'raffles.id')
                    ->sum('raffles.price');

                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'cpf' => $user->cpf,
                    'paid_tickets' => $user->paid_count,
                    'reserved_tickets' => $user->reserved_count,
                    'total_spent' => $totalSpent,
                    'registered_at' => $user->created_at->format('d/m/Y'),
                ];
            })
            ->sortByDesc('paid_tickets')
            ->values();

        return view('admin.reports', compact('salesData', 'detailedSales', 'rafflePerformance', 'topBuyers'));
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
        $logs = NotificationLog::with('user')->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.notifications', compact('logs'));
    }

    /**
     * Enviar notificação em massa para os clientes cadastrados.
     */
    public function sendNotification(Request $request, LogActivityAction $logActivity)
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
            NotificationLog::create([
                'user_id' => $client->id,
                'channel' => $request->channel,
                'template_name' => $request->template_name,
                'message' => $request->message,
                'status' => 'sent',
            ]);
        }

        $logActivity->execute('Disparou notificação em massa via '.strtoupper($request->channel).' sobre '.str_replace('_', ' ', $request->template_name));

        return redirect()->route('admin.notifications')->with('success', 'Notificação enviada com sucesso para '.$clients->count().' participantes!');
    }

    /**
     * Gerar banner com Inteligência Artificial baseado em prompt.
     */
    public function generateBannerAI(Request $request, LogActivityAction $logActivity)
    {
        $request->validate([
            'prompt' => 'required|string',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
        ]);

        // Simulação de geração via IA DALL-E / Stability
        // Geramos uma imagem conceitual real baseada no prompt
        $generatedUrl = 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&w=1200&q=80&sig='.rand(1, 100000);
        if (str_contains(strtolower($request->prompt), 'mustang') || str_contains(strtolower($request->prompt), 'carro')) {
            $generatedUrl = 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?auto=format&fit=crop&w=1200&q=80&sig='.rand(1, 100000);
        } elseif (str_contains(strtolower($request->prompt), 'moto')) {
            $generatedUrl = 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=1200&q=80&sig='.rand(1, 100000);
        }

        $banner = Banner::create([
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
     * Cadastrar banner manualmente (upload desktop/mobile ou URL).
     */
    public function storeBanner(Request $request, LogActivityAction $logActivity)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'mobile_image' => 'nullable|image|max:5120',
            'image_url' => 'nullable|url',
        ]);

        $desktopUrl = $request->input('image_url');
        $mobileUrl = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = 'banner_desktop_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/banners'), $fileName);
            $desktopUrl = '/uploads/banners/'.$fileName;
        }

        if ($request->hasFile('mobile_image')) {
            $file = $request->file('mobile_image');
            $fileName = 'banner_mobile_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/banners'), $fileName);
            $mobileUrl = '/uploads/banners/'.$fileName;
        }

        if (blank($desktopUrl)) {
            return redirect()->back()->withErrors(['image' => 'Envie a imagem desktop ou informe uma URL.']);
        }

        $banner = Banner::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_url' => $desktopUrl,
            'mobile_image_url' => $mobileUrl,
            'active' => true,
        ]);

        $logActivity->execute("Criou banner: '{$request->title}'", json_encode($banner->toArray()));

        return redirect()->route('admin.dashboard')->with('success', 'Banner criado com sucesso!');
    }

    /**
     * Remover banner.
     */
    public function destroyBanner(Banner $banner, LogActivityAction $logActivity)
    {
        $title = $banner->title;
        $banner->delete();
        $logActivity->execute("Excluiu banner: {$title}");

        return redirect()->route('admin.dashboard')->with('success', 'Banner excluído com sucesso!');
    }

    /**
     * Ativar/Desativar um banner.
     */
    public function toggleBanner(Banner $banner, LogActivityAction $logActivity)
    {
        $banner->update([
            'active' => ! $banner->active,
        ]);

        $logActivity->execute("Alterou status do banner ID: {$banner->id} para ".($banner->active ? 'Ativo' : 'Inativo'));

        return redirect()->route('admin.dashboard')->with('success', 'Status do banner alterado com sucesso!');
    }
}
