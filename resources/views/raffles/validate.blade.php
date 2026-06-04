@extends('layouts.public')

@section('title', 'Validador de Bilhetes - Ação RR Veículos')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    <!-- Title / Header -->
    <div class="border-b pb-4 text-center" style="border-color: var(--border-color);">
        <h1 class="text-2xl font-extrabold text-white flex items-center justify-center gap-2">
            <i class="fa-solid fa-shield-halved" style="color: var(--accent);"></i> Valide seu Bilhete
        </h1>
        <p class="text-slate-400 text-xs mt-1">Verifique a autenticidade e validade de qualquer bilhete do Ação RR Veículos.</p>
    </div>

    <!-- Search / Code Input Form -->
    <div class="glass-card rounded-2xl p-6 space-y-4">
        <form action="{{ route('raffles.validate-ticket-post') }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-1.5">
                <label class="text-xs text-slate-400 font-semibold uppercase">Código do Recibo ou Transação:</label>
                <div class="flex gap-2">
                    <input type="text" name="code" placeholder="Digite o código (ex: tx_abcdefgh)" value="{{ $payment ? $payment->gateway_transaction_id : '' }}" required class="flex-grow bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-6 py-3 rounded-xl text-sm transition">
                        Verificar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Validation Result Block -->
    @if($payment)
        <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-6 border border-emerald-500/20 bg-emerald-500/[0.02]">
            
            <!-- Result Badge -->
            <div class="text-center space-y-2 border-b pb-6" style="border-color: var(--border-color);">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-emerald-500/20 text-emerald-400 text-3xl mb-1">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h2 class="text-xl font-bold text-white">Bilhete Autêntico e Verificado!</h2>
                <p class="text-slate-400 text-xs">Este bilhete é oficial e registrado em nossa base de dados.</p>
            </div>

            <!-- Validation Details -->
            <div class="space-y-4">
                
                <!-- Masked / Full Buyer Data -->
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xs text-slate-400 font-bold uppercase tracking-wider">Titular da Compra</h3>
                        @if(!Auth::check())
                            <span class="text-[10px] text-slate-500 uppercase font-semibold">Máscara LGPD Ativa</span>
                        @endif
                    </div>
                    <div class="bg-slate-900/50 p-4 rounded-xl border space-y-3" style="border-color: var(--border-color);">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Nome</span>
                            <span class="text-slate-200 font-medium">{{ $maskedUser->name }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">CPF</span>
                            <span class="text-slate-200 font-medium">{{ $maskedUser->cpf }}</span>
                        </div>
                        
                        @if(!Auth::check())
                            <!-- Alert login warning for visitors -->
                            <div class="pt-3 border-t text-center text-xs text-slate-400 space-y-2" style="border-color: var(--border-color);">
                                <p><i class="fa-solid fa-lock text-amber-500 mr-1"></i> Para visualizar os dados completos deste comprador, faça login.</p>
                                <a href="{{ route('login') }}" class="inline-block bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-1.5 rounded-lg text-xs font-semibold border border-slate-700 transition">
                                    Fazer Login
                                </a>
                            </div>
                        @else
                            <div class="flex justify-between text-sm border-t pt-2" style="border-color: var(--border-color);">
                                <span class="text-slate-500">E-mail</span>
                                <span class="text-slate-200 font-medium">{{ $maskedUser->email }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Transaction details -->
                <div class="space-y-2">
                    <h3 class="text-xs text-slate-400 font-bold uppercase tracking-wider">Informações da Transação</h3>
                    <div class="bg-slate-900/50 p-4 rounded-xl border space-y-3" style="border-color: var(--border-color);">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Rifa Associada</span>
                            <span class="text-slate-200 font-medium">{{ $payment->tickets->first()->raffle->title ?? 'Sorteio' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Status do Pagamento</span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wide {{ $payment->status === 'approved' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                                {{ $payment->status === 'approved' ? 'Aprovado' : 'Pendente' }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Data de Compra</span>
                            <span class="text-slate-200 font-medium">{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Números Adquiridos</span>
                            <span class="text-blue-400 font-bold">
                                @foreach($payment->tickets as $ticket)
                                    {{ sprintf('%02d', $ticket->number) }}@if(!$loop->last), @endif
                                @endforeach
                            </span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    @endif

</div>
@endsection
