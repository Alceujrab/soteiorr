<?php

use Illuminate\Support\Facades\Route;

Route::get('/simulate-login/{role}', function ($role) {
    $user = User::where('role', $role)->first();
    if ($user) {
        Auth::login($user);

        return redirect()->back()->with('success', 'Perfil alterado para: '.str_replace('_', ' ', $role));
    }

    return redirect()->back()->withErrors(['error' => 'Perfil não encontrado.']);
})->name('simulate-login');

use App\Http\Controllers\AuthController;

// Rotas de Autenticação
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/customer', function () {
    return redirect()->route('raffles.my-tickets');
})->name('customer.dashboard');

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RaffleController;
use App\Http\Controllers\SupportController;

// Rotas Clientes
Route::get('/', [RaffleController::class, 'index'])->name('raffles.index');
Route::get('/my-tickets', [RaffleController::class, 'myTickets'])->name('raffles.my-tickets');
Route::get('/raffles/{raffle}', [RaffleController::class, 'show'])->name('raffles.show');
Route::post('/raffles/{raffle}/buy', [RaffleController::class, 'buy'])->name('raffles.buy');

// Suporte / Tickets
Route::get('/support', [SupportController::class, 'index'])->name('support.index');
Route::post('/support/ticket', [SupportController::class, 'storeTicket'])->name('support.store-ticket');

// Rotas Pagamento
Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
Route::get('/payments/{payment}/check-status', [PaymentController::class, 'checkStatus'])->name('payments.check-status');
Route::get('/payments/{payment}/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm'); // Simulação
Route::get('/payments/{payment}/receipt', [RaffleController::class, 'receipt'])->name('payments.receipt');
Route::get('/validate-ticket/{payment?}', [RaffleController::class, 'validateTicket'])->name('raffles.validate-ticket');
Route::post('/validate-ticket', [RaffleController::class, 'validateTicketPost'])->name('raffles.validate-ticket-post');

// Páginas Institucionais Dinâmicas
Route::get('/sobre-nos', [RaffleController::class, 'about'])->name('pages.about');
Route::get('/contato', [RaffleController::class, 'contact'])->name('pages.contact');
Route::get('/duvidas', [RaffleController::class, 'faqs'])->name('pages.faqs');
Route::get('/politica-de-privacidade', [RaffleController::class, 'privacy'])->name('pages.privacy');
Route::get('/termos-de-uso', [RaffleController::class, 'terms'])->name('pages.terms');

// Rotas Admin
Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/admin/raffles/create', [AdminController::class, 'createRaffle'])->name('admin.raffles.create');
Route::post('/admin/raffles', [AdminController::class, 'storeRaffle'])->name('admin.raffles.store');
Route::get('/admin/raffles/{raffle}/edit', [AdminController::class, 'editRaffle'])->name('admin.raffles.edit');
Route::put('/admin/raffles/{raffle}', [AdminController::class, 'updateRaffle'])->name('admin.raffles.update');
Route::get('/admin/raffles/{raffle}/draw', [AdminController::class, 'draw'])->name('admin.raffles.draw'); // Simulação
Route::get('/admin/logs', [AdminController::class, 'logs'])->name('admin.logs');
Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
Route::post('/admin/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
Route::get('/admin/participants', [AdminController::class, 'participants'])->name('admin.participants');
Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');
Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
Route::get('/admin/notifications', [AdminController::class, 'notifications'])->name('admin.notifications');
Route::post('/admin/notifications/send', [AdminController::class, 'sendNotification'])->name('admin.notifications.send');
Route::post('/admin/banners', [AdminController::class, 'storeBanner'])->name('admin.banners.store');
Route::post('/admin/banners/generate', [AdminController::class, 'generateBannerAI'])->name('admin.banners.generate');
Route::post('/admin/banners/{banner}/toggle', [AdminController::class, 'toggleBanner'])->name('admin.banners.toggle');

// Rotas de API e Webhooks
use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

Route::prefix('api')->group(function () {
    Route::get('/raffles', [ApiController::class, 'getRaffles'])->name('api.raffles');
    Route::get('/raffles/{raffle}', [ApiController::class, 'getRaffleDetails'])->name('api.raffles.show');
    Route::post('/webhook/asaas', [ApiController::class, 'webhookAsaas'])->name('api.webhook.asaas');
    Route::post('/webhook/mercadopago', [ApiController::class, 'webhookMercadoPago'])->name('api.webhook.mercadopago');
});
