@extends('layouts.customer')

@section('title', 'Meus Números - Ação RR Veículos')

@section('content')
<div class="space-y-8">
    <div class="border-b border-slate-800 pb-6">
        <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
            <i class="fa-solid fa-ticket text-blue-500"></i> Meus Números
        </h1>
        <p class="text-slate-400 text-sm mt-1">
            Acompanhe cotas pagas e reservas aguardando PIX (válidas por 30 minutos).
        </p>
    </div>

    @php
        $grouped = $tickets->groupBy('raffle_id');
    @endphp

    <div class="space-y-6">
        @forelse($grouped as $raffleId => $raffleTickets)
            @php
                $raffle = $raffleTickets->first()->raffle;
                $paidTickets = $raffleTickets->where('status', 'paid');
                $reservedTickets = $raffleTickets->where('status', 'reserved');
                $pendingPayment = $reservedTickets->first()?->payment;
                $secondsRemaining = $pendingPayment?->reservationSecondsRemaining() ?? 0;
            @endphp
            <div class="glass-card rounded-2xl overflow-hidden p-6 space-y-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-900 border border-slate-800 hidden sm:block">
                            <img src="{{ $raffle->image_url }}" alt="{{ $raffle->title }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">{{ $raffle->title }}</h3>
                            <p class="text-xs text-slate-500">Sorteio em: {{ $raffle->draw_date->format('d/m/Y H:i') }}</p>
                            <p class="text-xs text-slate-400 mt-1">
                                {{ $paidTickets->count() }} pago(s)
                                @if($reservedTickets->isNotEmpty())
                                    · <span class="text-amber-400">{{ $reservedTickets->count() }} aguardando PIX</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if($pendingPayment)
                            <a href="{{ route('payments.show', $pendingPayment->id) }}" class="bg-amber-600 hover:bg-amber-500 text-white font-bold px-4 py-2 rounded-xl text-xs transition flex items-center gap-1.5 shadow">
                                <i class="fa-solid fa-circle-exclamation text-amber-300 animate-pulse"></i>
                                Pagar agora
                                @if($secondsRemaining > 0)
                                    <span class="font-mono">({{ sprintf('%02d:%02d', intdiv($secondsRemaining, 60), $secondsRemaining % 60) }})</span>
                                @endif
                            </a>
                        @endif

                        @php $firstPaidPaymentId = $paidTickets->isNotEmpty() ? $paidTickets->first()->payment_id : null; @endphp
                        @if($firstPaidPaymentId)
                            <a href="{{ route('payments.receipt', $firstPaidPaymentId) }}" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-4 py-2 rounded-xl text-xs transition flex items-center gap-1.5 shadow">
                                <i class="fa-solid fa-receipt"></i> Ver Recibo
                            </a>
                        @endif

                        <a href="{{ route('draws.raffle', $raffle) }}" class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white font-bold px-4 py-2 rounded-xl text-xs transition flex items-center gap-1.5">
                            <i class="fa-solid fa-play"></i> Ver sorteio
                        </a>
                    </div>
                </div>

                <div class="border-t border-slate-800/80 pt-4 space-y-4">
                    @if($paidTickets->isNotEmpty())
                        <div>
                            <div class="text-xs text-emerald-400 font-semibold mb-2">Cotas pagas</div>
                            <div class="grid grid-cols-4 sm:grid-cols-8 md:grid-cols-12 gap-3">
                                @foreach($paidTickets as $t)
                                    <div class="p-3 border rounded-xl flex flex-col items-center justify-center gap-1 select-none bg-white text-black border-slate-200">
                                        <span class="text-lg font-bold text-black">{{ sprintf('%02d', $t->number) }}</span>
                                        <span class="text-[9px] uppercase tracking-wider font-bold text-emerald-600">Pago</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($reservedTickets->isNotEmpty())
                        <div>
                            <div class="text-xs text-amber-400 font-semibold mb-2">Aguardando pagamento (expira em até 30 min)</div>
                            <div class="grid grid-cols-4 sm:grid-cols-8 md:grid-cols-12 gap-3">
                                @foreach($reservedTickets as $t)
                                    <div class="p-3 border rounded-xl flex flex-col items-center justify-center gap-1 select-none bg-amber-50 text-black border-amber-200">
                                        <span class="text-lg font-bold text-black">{{ sprintf('%02d', $t->number) }}</span>
                                        <span class="text-[9px] uppercase tracking-wider font-bold text-amber-700">Reservado</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="glass-card p-12 text-center rounded-2xl">
                <div class="text-slate-500 mb-4">
                    <i class="fa-solid fa-ticket text-5xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-white">Nenhum número ainda</h3>
                <p class="text-slate-400 mt-1">Escolha um pacote em uma ação ativa para participar.</p>
                <a href="{{ route('raffles.index') }}" class="mt-4 inline-flex bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 px-6 rounded-xl transition">
                    Ver Ações Promocionais
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
