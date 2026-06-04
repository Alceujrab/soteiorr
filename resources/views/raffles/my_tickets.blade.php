@extends('layouts.customer')

@section('title', 'Meus Bilhetes - Ação RR Veículos')

@section('content')
<div class="space-y-8">
    <div class="border-b border-slate-800 pb-6">
        <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
            <i class="fa-solid fa-ticket text-blue-500"></i> Meus Bilhetes
        </h1>
        <p class="text-slate-400 text-sm mt-1">
            Consulte o histórico de suas cotas e acompanhe o status de pagamento.
        </p>
    </div>

    @php
        // Agrupar bilhetes por Rifa
        $grouped = $tickets->groupBy('raffle_id');
    @endphp

    <div class="space-y-6">
        @forelse($grouped as $raffleId => $raffleTickets)
            @php $raffle = $raffleTickets->first()->raffle; @endphp
            <div class="glass-card rounded-2xl overflow-hidden p-6 space-y-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-900 border border-slate-800 hidden sm:block">
                            <img src="{{ $raffle->image_url }}" alt="{{ $raffle->title }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">{{ $raffle->title }}</h3>
                            <p class="text-xs text-slate-500">Sorteio em: {{ $raffle->draw_date->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @if($raffleTickets->where('status', 'reserved')->isNotEmpty())
                        @php $firstPaymentId = $raffleTickets->where('status', 'reserved')->first()->payment_id; @endphp
                        @if($firstPaymentId)
                            <a href="{{ route('payments.show', $firstPaymentId) }}" class="bg-amber-600 hover:bg-amber-500 text-amber-950 font-bold px-4 py-2 rounded-xl text-xs transition flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> Pagar Reservas Pendentes
                            </a>
                        @endif
                    @endif
                </div>

                <div class="border-t border-slate-800/80 pt-4">
                    <div class="text-xs text-slate-400 font-semibold mb-2">Suas Cotas Selecionadas:</div>
                    <div class="grid grid-cols-4 sm:grid-cols-8 md:grid-cols-12 gap-3">
                        @foreach($raffleTickets as $t)
                            <div class="p-3 border rounded-xl flex flex-col items-center justify-center gap-1 select-none {{ $t->status === 'paid' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-amber-500/10 border-amber-500/30 text-amber-400' }}">
                                <span class="text-lg font-bold">{{ sprintf('%02d', $t->number) }}</span>
                                <span class="text-[9px] uppercase tracking-wider font-semibold">{{ $t->status === 'paid' ? 'Pago' : 'Reservado' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="glass-card p-12 text-center rounded-2xl">
                <div class="text-slate-500 mb-4">
                    <i class="fa-solid fa-ticket text-5xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-white">Nenhum bilhete comprado ainda</h3>
                <p class="text-slate-400 mt-1">Navegue pelas ações ativas e escolha seus números!</p>
                <a href="{{ route('raffles.index') }}" class="mt-4 inline-flex bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 px-6 rounded-xl transition">
                    Ver Rifas
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
