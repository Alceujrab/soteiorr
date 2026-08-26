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
        // Agrupar bilhetes por Ação Promocional
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
                            <p class="text-xs text-slate-500">Ação Promocional em: {{ $raffle->draw_date->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if($raffleTickets->where('status', 'reserved')->isNotEmpty())
                            @php $firstPaymentId = $raffleTickets->where('status', 'reserved')->first()->payment_id; @endphp
                            @if($firstPaymentId)
                                <a href="{{ route('payments.show', $firstPaymentId) }}" class="bg-amber-600 hover:bg-amber-500 text-white font-bold px-4 py-2 rounded-xl text-xs transition flex items-center gap-1.5 shadow">
                                    <i class="fa-solid fa-circle-exclamation text-amber-300 animate-pulse"></i> Pagar Reservas Pendentes
                                </a>
                            @endif
                        @endif

                        @php
                            $paidTickets = $raffleTickets->where('status', 'paid');
                            $firstPaidPaymentId = $paidTickets->isNotEmpty() ? $paidTickets->first()->payment_id : null;
                        @endphp
                        @if($firstPaidPaymentId)
                            <a href="{{ route('payments.receipt', $firstPaidPaymentId) }}" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-4 py-2 rounded-xl text-xs transition flex items-center gap-1.5 shadow">
                                <i class="fa-solid fa-receipt"></i> Ver Recibo / PDF
                            </a>
                        @endif
                    </div>
                </div>

                <div class="border-t border-slate-800/80 pt-4">
                    <div class="text-xs text-slate-400 font-semibold mb-2">Suas Cotas Selecionadas:</div>
                    <div class="grid grid-cols-4 sm:grid-cols-8 md:grid-cols-12 gap-3">
                        @foreach($raffleTickets as $t)
                            <div class="p-3 border rounded-xl flex flex-col items-center justify-center gap-1 select-none bg-white text-black border-slate-200">
                                <span class="text-lg font-bold text-black">{{ sprintf('%02d', $t->number) }}</span>
                                <span class="text-[9px] uppercase tracking-wider font-bold {{ $t->status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">{{ $t->status === 'paid' ? 'Pago' : 'Reservado' }}</span>
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
                    Ver Ações Promocionais
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
