@extends('layouts.public')

@section('title', 'Recibo de Compra - Ação RR Veículos')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="border-b pb-4 flex justify-between items-center" style="border-color: var(--border-color);">
        <div>
            <h1 class="text-2xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-receipt" style="color: var(--accent);"></i> Recibo de Compra
            </h1>
            <p class="text-slate-400 text-xs mt-1">Este documento comprova a autenticidade da sua participação no sorteio.</p>
        </div>
        <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold px-4 py-2 rounded-xl text-xs transition flex items-center gap-1.5 border border-slate-700">
            <i class="fa-solid fa-print"></i> Imprimir
        </button>
    </div>

    <!-- Receipt Details Card -->
    <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-6">
        
        <!-- Status Header -->
        <div class="flex justify-between items-center border-b pb-4" style="border-color: var(--border-color);">
            <div>
                <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Código do Recibo:</span>
                <div class="text-sm font-bold text-white uppercase">{{ $payment->gateway_transaction_id ?: 'MOCK-RECEIPT-' . $payment->id }}</div>
            </div>
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $payment->status === 'approved' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                <i class="fa-solid {{ $payment->status === 'approved' ? 'fa-circle-check' : 'fa-clock' }} mr-1"></i>
                {{ $payment->status === 'approved' ? 'Pago' : 'Pendente' }}
            </span>
        </div>

        <!-- Buyer Info -->
        <div class="space-y-3">
            <h3 class="text-xs text-slate-400 font-bold uppercase tracking-wider">Dados do Comprador</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-900/50 p-4 rounded-xl border" style="border-color: var(--border-color);">
                <div>
                    <span class="text-[10px] text-slate-500 uppercase block font-semibold">Nome Completo</span>
                    <span class="text-sm text-slate-200 font-medium">{{ $payment->user->name }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-500 uppercase block font-semibold">CPF</span>
                    <span class="text-sm text-slate-200 font-medium">{{ $payment->user->cpf }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-500 uppercase block font-semibold">E-mail</span>
                    <span class="text-sm text-slate-200 font-medium">{{ $payment->user->email }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-500 uppercase block font-semibold">Data/Hora</span>
                    <span class="text-sm text-slate-200 font-medium">{{ $payment->created_at->format('d/m/Y H:i:s') }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Info & QR Code -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
            
            <!-- Left: Description and pricing -->
            <div class="md:col-span-2 space-y-4">
                <h3 class="text-xs text-slate-400 font-bold uppercase tracking-wider">Detalhamento do Pagamento</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between border-b pb-1.5" style="border-color: var(--border-color);">
                        <span class="text-slate-400">Rifa</span>
                        <span class="text-white font-medium">{{ $payment->tickets->first()->raffle->title ?? 'Sorteio' }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-1.5" style="border-color: var(--border-color);">
                        <span class="text-slate-400">Método de pagamento</span>
                        <span class="text-white uppercase font-medium">{{ $payment->payment_method }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-1.5" style="border-color: var(--border-color);">
                        <span class="text-slate-400">Valor Total Pago</span>
                        <span class="text-emerald-400 font-bold text-base">R$ {{ number_format($payment->amount, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Right: QR Code for validation -->
            <div class="flex flex-col items-center justify-center p-4 bg-slate-900 border rounded-xl" style="border-color: var(--border-color);">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('raffles.validate-ticket', $payment->id)) }}" alt="QR Code Validador" class="w-24 h-24 bg-white p-1 rounded">
                <span class="text-[9px] text-slate-500 font-bold mt-2 uppercase tracking-wide">Autenticação Online</span>
            </div>
        </div>

        <!-- Tickets / Selected numbers -->
        <div class="space-y-3">
            <h3 class="text-xs text-slate-400 font-bold uppercase tracking-wider">Números Adquiridos ({{ $payment->tickets->count() }})</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($payment->tickets as $ticket)
                    <span class="inline-flex items-center px-3 py-1.5 bg-blue-500/10 border text-blue-400 border-blue-500/20 font-bold rounded-lg text-sm">
                        {{ sprintf('%02d', $ticket->number) }}
                    </span>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
