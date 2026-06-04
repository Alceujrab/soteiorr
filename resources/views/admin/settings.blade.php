@extends('layouts.admin')

@section('title', 'Configurações Globais - Ação RR Veículos')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="border-b pb-6" style="border-color: var(--border-color);">
        <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
            <i class="fa-solid fa-gears" style="color: var(--accent);"></i> Configurações Globais
        </h1>
        <p class="text-slate-400 text-sm mt-1">
            Configure gateways de pagamento, limites do sistema, chaves de serviços externos e APIs bancárias diretas (Itaú e Santander).
        </p>
    </div>

    <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-6">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Parâmetros Gerais -->
            <div class="space-y-4">
                <h3 class="text-base font-bold text-white border-b pb-2 flex items-center gap-2" style="border-color: var(--border-color);">
                    <i class="fa-solid fa-sliders text-sm text-slate-400"></i> Parâmetros Gerais
                </h3>
                <div class="space-y-1.5">
                    <label class="text-xs text-slate-400 font-semibold uppercase">Nome do Aplicativo:</label>
                    <input type="text" name="app_name" value="{{ $settings['app_name'] }}" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Mínimo de Cotas por Compra:</label>
                        <input type="number" name="min_tickets" value="{{ $settings['min_tickets'] }}" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Máximo de Cotas por Compra:</label>
                        <input type="number" name="max_tickets" value="{{ $settings['max_tickets'] }}" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900/40 border border-slate-800">
                    <span class="text-xs text-slate-300 font-medium">Exibir quantidade de cotas vendidas publicamente:</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="show_sold_qty" value="1" {{ $settings['show_sold_qty'] == '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-slate-300 after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>
            </div>

            <!-- Parâmetros de Gateways Intermediários -->
            <div class="space-y-4">
                <h3 class="text-base font-bold text-white border-b pb-2 flex items-center gap-2" style="border-color: var(--border-color);">
                    <i class="fa-solid fa-credit-card text-sm text-slate-400"></i> Gateways Intermediários (Opcional)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Asaas API Token:</label>
                        <input type="password" name="gateway_asaas_key" value="{{ $settings['gateway_asaas_key'] }}" placeholder="Chave secreta Asaas" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Mercado Pago Access Token:</label>
                        <input type="password" name="gateway_mercadopago_key" value="{{ $settings['gateway_mercadopago_key'] }}" placeholder="Chave privada Mercado Pago" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Itaú API Pix Direct -->
            <div class="space-y-4 p-4 rounded-xl border" style="border-color: var(--border-color); background-color: rgba(15, 23, 42, 0.3);">
                <div class="flex items-center justify-between border-b pb-2" style="border-color: var(--border-color);">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-building-columns text-sm text-orange-400"></i> Itaú API Pix Direta
                    </h3>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="itau_enabled" value="1" {{ $settings['itau_enabled'] == '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-slate-300 after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ml-2 text-xs text-slate-300 font-medium">Ativar</span>
                    </label>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Client ID:</label>
                        <input type="text" name="itau_client_id" value="{{ $settings['itau_client_id'] }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Client Secret:</label>
                        <input type="password" name="itau_client_secret" value="{{ $settings['itau_client_secret'] }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Caminho do Certificado (.pem):</label>
                        <input type="text" name="itau_cert_path" value="{{ $settings['itau_cert_path'] }}" placeholder="ex: /var/certs/itau.pem" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Caminho da Chave do Certificado (.key):</label>
                        <input type="text" name="itau_key_path" value="{{ $settings['itau_key_path'] }}" placeholder="ex: /var/certs/itau.key" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                    <div class="space-y-1.5 md:col-span-2">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Chave Pix cadastrada no Itaú:</label>
                        <input type="text" name="itau_pix_key" value="{{ $settings['itau_pix_key'] }}" placeholder="ex: CNPJ ou E-mail da chave" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Santander API Pix Direct -->
            <div class="space-y-4 p-4 rounded-xl border" style="border-color: var(--border-color); background-color: rgba(15, 23, 42, 0.3);">
                <div class="flex items-center justify-between border-b pb-2" style="border-color: var(--border-color);">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-building-columns text-sm text-red-500"></i> Santander API Pix Direta
                    </h3>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="santander_enabled" value="1" {{ $settings['santander_enabled'] == '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-slate-300 after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ml-2 text-xs text-slate-300 font-medium">Ativar</span>
                    </label>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Client ID:</label>
                        <input type="text" name="santander_client_id" value="{{ $settings['santander_client_id'] }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Client Secret:</label>
                        <input type="password" name="santander_client_secret" value="{{ $settings['santander_client_secret'] }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Caminho do Certificado (.pem):</label>
                        <input type="text" name="santander_cert_path" value="{{ $settings['santander_cert_path'] }}" placeholder="ex: /var/certs/santander.pem" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Caminho da Chave do Certificado (.key):</label>
                        <input type="text" name="santander_key_path" value="{{ $settings['santander_key_path'] }}" placeholder="ex: /var/certs/santander.key" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                    <div class="space-y-1.5 md:col-span-2">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Chave Pix cadastrada no Santander:</label>
                        <input type="text" name="santander_pix_key" value="{{ $settings['santander_pix_key'] }}" placeholder="ex: CNPJ ou E-mail da chave" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Google reCAPTCHA -->
            <div class="space-y-4 p-4 rounded-xl border" style="border-color: var(--border-color); background-color: rgba(15, 23, 42, 0.3);">
                <div class="flex items-center justify-between border-b pb-2" style="border-color: var(--border-color);">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fa-brands fa-google text-sm text-red-400"></i> Google reCAPTCHA v2 / v3
                    </h3>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="recaptcha_enabled" value="1" {{ $settings['recaptcha_enabled'] == '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-slate-300 after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ml-2 text-xs text-slate-300 font-medium">Ativar</span>
                    </label>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Site Key:</label>
                        <input type="text" name="recaptcha_site_key" value="{{ $settings['recaptcha_site_key'] }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Secret Key:</label>
                        <input type="password" name="recaptcha_secret_key" value="{{ $settings['recaptcha_secret_key'] }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Google Login (OAuth) -->
            <div class="space-y-4 p-4 rounded-xl border" style="border-color: var(--border-color); background-color: rgba(15, 23, 42, 0.3);">
                <div class="flex items-center justify-between border-b pb-2" style="border-color: var(--border-color);">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-right-to-bracket text-sm text-blue-400"></i> Google Login (OAuth)
                    </h3>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="google_login_enabled" value="1" {{ $settings['google_login_enabled'] == '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-slate-300 after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ml-2 text-xs text-slate-300 font-medium">Ativar</span>
                    </label>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Google Client ID:</label>
                        <input type="text" name="google_client_id" value="{{ $settings['google_client_id'] }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Google Client Secret:</label>
                        <input type="password" name="google_client_secret" value="{{ $settings['google_client_secret'] }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Google Maps API -->
            <div class="space-y-4 p-4 rounded-xl border" style="border-color: var(--border-color); background-color: rgba(15, 23, 42, 0.3);">
                <div class="flex items-center justify-between border-b pb-2" style="border-color: var(--border-color);">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-map-location-dot text-sm text-emerald-400"></i> Google Maps API
                    </h3>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="google_maps_enabled" value="1" {{ $settings['google_maps_enabled'] == '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-slate-300 after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ml-2 text-xs text-slate-300 font-medium">Ativar</span>
                    </label>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs text-slate-400 font-semibold uppercase">Google Maps API Key:</label>
                    <input type="text" name="google_maps_key" value="{{ $settings['google_maps_key'] }}" placeholder="AIzaSy..." class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                </div>
            </div>

            <button type="submit" class="w-full text-white font-semibold py-3.5 px-4 rounded-xl transition text-sm flex items-center justify-center gap-2 shadow-lg" style="background-color: var(--accent); hover:background-color: var(--accent-hover);">
                <i class="fa-solid fa-circle-check"></i> Salvar Configurações
            </button>
        </form>
    </div>
</div>
@endsection
