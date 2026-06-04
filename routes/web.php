<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RaffleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminController;

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

// Rotas Admin
Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/admin/raffles/create', [AdminController::class, 'createRaffle'])->name('admin.raffles.create');
Route::post('/admin/raffles', [AdminController::class, 'storeRaffle'])->name('admin.raffles.store');
Route::get('/admin/raffles/{raffle}/draw', [AdminController::class, 'draw'])->name('admin.raffles.draw'); // Simulação
Route::get('/admin/logs', [AdminController::class, 'logs'])->name('admin.logs');
Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
Route::post('/admin/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
Route::get('/admin/participants', [AdminController::class, 'participants'])->name('admin.participants');

