@extends('layouts.app')

@section('title', 'Configurações Globais - Ação RR Veículos')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="border-b border-slate-800 pb-6">
        <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
            <i class="fa-solid fa-gears text-blue-500"></i> Configurações Globais
        </h1>
        <p class="text-slate-400 text-sm mt-1">
            Configure gateways de pagamento ativos, limites e personalização visual.
        </p>
    </div>

    <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-6">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Parâmetros Gerais -->
            <div class="space-y-4">
                <h3 class="text-base font-bold text-white border-b border-slate-800 pb-2">Gerais</h3>
                <div class="space-y-1.5">
                    <label class="text-xs text-slate-400 font-semibold uppercase">Nome do Aplicativo:</label>
                    <input type="text" name="app_name" value="{{ $settings['app_name'] }}" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Mínimo de Cotas:</label>
                        <input type="number" name="min_tickets" value="{{ $settings['min_tickets'] }}" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Máximo de Cotas:</label>
                        <input type="number" name="max_tickets" value="{{ $settings['max_tickets'] }}" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Parâmetros de Gateways -->
            <div class="space-y-4">
                <h3 class="text-base font-bold text-white border-b border-slate-800 pb-2">Integração de Gateways</h3>
                <div class="space-y-1.5">
                    <label class="text-xs text-slate-400 font-semibold uppercase">Asaas API Token:</label>
                    <input type="password" name="gateway_asaas_key" value="{{ $settings['gateway_asaas_key'] }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs text-slate-400 font-semibold uppercase">Mercado Pago Access Token:</label>
                    <input type="password" name="gateway_mercadopago_key" value="{{ $settings['gateway_mercadopago_key'] }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3.5 px-4 rounded-xl transition text-sm">
                Salvar Configurações
            </button>
        </form>
    </div>
</div>
@endsection
