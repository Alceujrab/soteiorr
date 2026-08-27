<?php

use App\Http\Controllers\Admin\AffiliateController;
use App\Http\Controllers\Admin\DrawCeremonyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\DrawController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RaffleController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SupportController;
use App\Models\Raffle;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/simulate-login/{role}', function (string $role) {
    $user = User::where('role', $role)->first();
    if ($user) {
        Auth::login($user);

        return redirect()->back()->with('success', 'Perfil alterado para: '.str_replace('_', ' ', $role));
    }

    return redirect()->back()->withErrors(['error' => 'Perfil não encontrado.']);
})->middleware('local.env')->name('simulate-login');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
Route::get('/completar-cadastro', [GoogleAuthController::class, 'showCompleteProfile'])->name('profile.complete');
Route::post('/completar-cadastro', [GoogleAuthController::class, 'completeProfile'])->name('profile.complete.submit');

Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');

Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

Route::get('/checkout/continue', [CheckoutController::class, 'continue'])->name('checkout.continue');
Route::get('/checkout/selecionar', [CheckoutController::class, 'select'])->middleware('auth')->name('checkout.select');
Route::post('/checkout/selecionar', [CheckoutController::class, 'storeSelection'])->middleware('auth')->name('checkout.select.store');
Route::post('/raffles/{raffle}/checkout', [CheckoutController::class, 'start'])->name('checkout.start');
Route::post('/payments/{payment}/upsell', [CheckoutController::class, 'upsell'])->middleware('auth')->name('payments.upsell');

Route::get('/customer', function () {
    return redirect()->route('raffles.my-tickets');
})->middleware('auth')->name('customer.dashboard');

Route::get('/', [RaffleController::class, 'index'])->name('raffles.index');
Route::get('/my-tickets', [RaffleController::class, 'myTickets'])->middleware('auth')->name('raffles.my-tickets');
Route::get('/raffles/{raffle}', [RaffleController::class, 'show'])->name('raffles.show');
Route::post('/raffles/{raffle}/buy', [RaffleController::class, 'buy'])->name('raffles.buy');

Route::middleware('auth')->group(function () {
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::post('/support/ticket', [SupportController::class, 'storeTicket'])->name('support.store-ticket');
});

Route::middleware('auth')->group(function () {
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{payment}/check-status', [PaymentController::class, 'checkStatus'])->name('payments.check-status');
    Route::get('/payments/{payment}/receipt', [RaffleController::class, 'receipt'])->name('payments.receipt');
});

Route::get('/payments/{payment}/confirm', [PaymentController::class, 'confirm'])
    ->middleware(['local.env', 'auth'])
    ->name('payments.confirm');

Route::get('/validate-ticket/{payment?}', [RaffleController::class, 'validateTicket'])->name('raffles.validate-ticket');
Route::post('/validate-ticket', [RaffleController::class, 'validateTicketPost'])->name('raffles.validate-ticket-post');

Route::get('/sobre-nos', [RaffleController::class, 'about'])->name('pages.about');
Route::get('/contato', [RaffleController::class, 'contact'])->name('pages.contact');
Route::get('/duvidas', [RaffleController::class, 'faqs'])->name('pages.faqs');
Route::get('/politica-de-privacidade', [RaffleController::class, 'privacy'])->name('pages.privacy');
Route::get('/termos-de-uso', [RaffleController::class, 'terms'])->name('pages.terms');
Route::get('/regulamento', [RaffleController::class, 'regulation'])->name('pages.regulation');

Route::get('/sorteio', [DrawController::class, 'index'])->name('draws.index');
Route::get('/sorteio/{slug}', [DrawController::class, 'watch'])->name('draws.watch');
Route::get('/sorteio/{slug}/estado', [DrawController::class, 'state'])->name('draws.state');
Route::get('/sorteio/{slug}/ata', [DrawController::class, 'minutes'])->name('draws.minutes');
Route::get('/sorteio/{slug}/ata.pdf', [DrawController::class, 'minutesPdf'])->name('draws.minutes.pdf');
Route::get('/sorteio/{slug}/elegiveis.json', [DrawController::class, 'eligible'])->name('draws.eligible');
Route::get('/raffles/{raffle}/sorteio', [DrawController::class, 'liveForRaffle'])->name('draws.raffle');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/raffles/create', [AdminController::class, 'createRaffle'])->name('admin.raffles.create');
    Route::post('/raffles', [AdminController::class, 'storeRaffle'])->name('admin.raffles.store');
    Route::get('/raffles/{raffle}/edit', [AdminController::class, 'editRaffle'])->name('admin.raffles.edit');
    Route::put('/raffles/{raffle}', [AdminController::class, 'updateRaffle'])->name('admin.raffles.update');
    Route::post('/raffles/{raffle}/destroy-request', [AdminController::class, 'requestDestroyRaffle'])->name('admin.raffles.destroy.request');
    Route::get('/raffles/{raffle}/destroy-confirm', [AdminController::class, 'showDestroyConfirm'])->name('admin.raffles.destroy.confirm');
    Route::post('/raffles/{raffle}/destroy-confirm', [AdminController::class, 'confirmDestroyRaffle'])->name('admin.raffles.destroy.confirm.submit');
    Route::post('/raffles/{raffle}/destroy-resend', [AdminController::class, 'resendDestroyCode'])->name('admin.raffles.destroy.resend');
    Route::get('/raffles/{raffle}/draw', function (Raffle $raffle) {
        return redirect()->route('admin.draws.room', $raffle);
    })->name('admin.raffles.draw');

    Route::get('/sorteio', [DrawCeremonyController::class, 'index'])->name('admin.draws.index');
    Route::get('/sorteio/teste', [DrawCeremonyController::class, 'test'])->name('admin.draws.test');
    Route::post('/sorteio/teste', [DrawCeremonyController::class, 'startTest'])->name('admin.draws.test.start');
    Route::get('/sorteio/{raffle}', [DrawCeremonyController::class, 'show'])->name('admin.draws.room');
    Route::post('/sorteio/{raffle}/iniciar', [DrawCeremonyController::class, 'start'])->name('admin.draws.start');
    Route::get('/sorteio/draw/{draw}/estado', [DrawCeremonyController::class, 'state'])->name('admin.draws.state');
    Route::post('/sorteio/draw/{draw}/sortear', [DrawCeremonyController::class, 'startAutoReveal'])->name('admin.draws.auto');
    Route::post('/sorteio/draw/{draw}/revelar', [DrawCeremonyController::class, 'reveal'])->name('admin.draws.reveal');
    Route::post('/sorteio/draw/{draw}/cancelar', [DrawCeremonyController::class, 'cancel'])->name('admin.draws.cancel');
    Route::post('/sorteio/draw/{draw}/checklist', [DrawCeremonyController::class, 'updateChecklist'])->name('admin.draws.checklist');

    Route::get('/logs', [AdminController::class, 'logs'])->name('admin.logs');
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
    Route::get('/participants', [AdminController::class, 'participants'])->name('admin.participants');
    Route::get('/afiliados', [AffiliateController::class, 'index'])->name('admin.affiliates');
    Route::post('/afiliados/{user}/codigo', [AffiliateController::class, 'ensureCode'])->name('admin.affiliates.code');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/notifications', [AdminController::class, 'notifications'])->name('admin.notifications');
    Route::post('/notifications/send', [AdminController::class, 'sendNotification'])->name('admin.notifications.send');
    Route::get('/banners', [AdminController::class, 'banners'])->name('admin.banners');
    Route::post('/banners', [AdminController::class, 'storeBanner'])->name('admin.banners.store');
    Route::post('/banners/generate', [AdminController::class, 'generateBannerAI'])->name('admin.banners.generate');
    Route::post('/banners/{banner}/toggle', [AdminController::class, 'toggleBanner'])->name('admin.banners.toggle');
    Route::delete('/banners/{banner}', [AdminController::class, 'destroyBanner'])->name('admin.banners.destroy');
});

Route::prefix('api')->group(function () {
    Route::get('/raffles', [ApiController::class, 'getRaffles'])->name('api.raffles');
    Route::get('/raffles/{raffle}', [ApiController::class, 'getRaffleDetails'])->name('api.raffles.show');
    Route::post('/webhook/asaas', [ApiController::class, 'webhookAsaas'])->name('api.webhook.asaas');
    Route::post('/webhook/mercadopago', [ApiController::class, 'webhookMercadoPago'])->name('api.webhook.mercadopago');
});

Route::match(['get', 'post'], '/internal/cron/run', [CronController::class, 'run'])->name('internal.cron.run');
