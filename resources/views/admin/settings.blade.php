@extends('layouts.admin')

@section('title', 'Configurações Globais - Ação RR Veículos')

@section('content')
<!-- Quill.js Rich Text Editor Styles and Scripts -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<div class="max-w-5xl mx-auto space-y-6">
    <div class="border-b pb-6" style="border-color: var(--border-color);">
        <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
            <i class="fa-solid fa-gears" style="color: var(--accent);"></i> Configurações Globais
        </h1>
        <p class="text-slate-400 text-sm mt-1">
            Gerencie de forma organizada os parâmetros gerais, integrações, gateways bancários e o conteúdo das páginas institucionais com ajuda de IA.
        </p>
    </div>

    <!-- Navegação por Abas (Tabs) -->
    <div class="flex flex-wrap gap-2 border-b pb-1" style="border-color: var(--border-color);">
        <button type="button" onclick="switchTab('tab-geral')" id="btn-tab-geral" class="tab-btn px-4 py-2 text-sm font-semibold rounded-t-xl transition border-b-2 text-white border-blue-500 bg-slate-900/40">
            <i class="fa-solid fa-sliders mr-1.5 text-blue-400"></i> Geral & Limites
        </button>
        <button type="button" onclick="switchTab('tab-gateways')" id="btn-tab-gateways" class="tab-btn px-4 py-2 text-sm font-semibold rounded-t-xl transition border-b-2 text-slate-400 border-transparent hover:text-white">
            <i class="fa-solid fa-credit-card mr-1.5 text-emerald-400"></i> Pix & Gateways
        </button>
        <button type="button" onclick="switchTab('tab-integracoes')" id="btn-tab-integracoes" class="tab-btn px-4 py-2 text-sm font-semibold rounded-t-xl transition border-b-2 text-slate-400 border-transparent hover:text-white">
            <i class="fa-brands fa-google mr-1.5 text-red-400"></i> APIs & Integrações
        </button>
        <button type="button" onclick="switchTab('tab-paginas')" id="btn-tab-paginas" class="tab-btn px-4 py-2 text-sm font-semibold rounded-t-xl transition border-b-2 text-slate-400 border-transparent hover:text-white">
            <i class="fa-solid fa-file-lines mr-1.5 text-indigo-400"></i> Páginas Institucionais
        </button>
        <button type="button" onclick="switchTab('tab-template')" id="btn-tab-template" class="tab-btn px-4 py-2 text-sm font-semibold rounded-t-xl transition border-b-2 text-slate-400 border-transparent hover:text-white">
            <i class="fa-solid fa-palette mr-1.5" style="color: var(--accent);"></i> Editor de Template
        </button>
        <button type="button" onclick="switchTab('tab-ai-writer')" id="btn-tab-ai-writer" class="tab-btn px-4 py-2 text-sm font-semibold rounded-t-xl transition border-b-2 text-slate-400 border-transparent hover:text-white">
            <i class="fa-solid fa-wand-magic-sparkles mr-1.5 text-pink-400 animate-pulse"></i> Assistente de IA / FAQs
        </button>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" id="settings-form" class="space-y-6">
        @csrf

        <!-- ================= TAB: GERAL ================= -->
        <div id="tab-geral" class="tab-content glass-card rounded-2xl p-6 sm:p-8 space-y-6">
            <div class="space-y-4">
                <h3 class="text-base font-bold text-white border-b pb-2 flex items-center gap-2" style="border-color: var(--border-color);">
                    <i class="fa-solid fa-sliders text-sm text-slate-400"></i> Parâmetros Gerais
                </h3>
                <div class="space-y-1.5">
                    <label class="text-xs text-slate-400 font-semibold uppercase">Nome do Aplicativo:</label>
                    <input type="text" name="app_name" value="{{ $settings['app_name'] }}" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs text-slate-400 font-semibold uppercase">E-mail de segurança (códigos de exclusão):</label>
                    <input type="email" name="admin_security_email" value="{{ $settings['admin_security_email'] }}" required placeholder="contato@rrsorteio.com" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    <p class="text-[11px] text-slate-500">Os códigos de confirmação para excluir ações serão enviados para este endereço.</p>
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

                <div class="rounded-xl border p-4 flex flex-col sm:flex-row sm:items-center gap-4" style="border-color: var(--border-color); background: color-mix(in srgb, var(--accent) 8%, transparent);">
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-white flex items-center gap-2"><i class="fa-solid fa-flask text-amber-400"></i> Sorteio teste</h4>
                        <p class="text-xs text-slate-400 mt-1">Simule a cerimônia completa (6 dígitos + ganhador fictício) sem afetar bilhetes reais.</p>
                    </div>
                    <a href="{{ route('admin.draws.test') }}" class="shrink-0 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold" style="background: var(--accent); color: var(--on-accent);">
                        Abrir sala de teste
                    </a>
                </div>
            </div>
        </div>

        <!-- ================= TAB: GATEWAYS ================= -->
        <div id="tab-gateways" class="tab-content glass-card rounded-2xl p-6 sm:p-8 space-y-6 hidden">
            <!-- Gateways Intermediários -->
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
                        <label class="text-xs text-slate-400 font-semibold uppercase">Asaas Sandbox:</label>
                        <label class="flex items-center gap-2 mt-2 text-sm text-slate-300 cursor-pointer">
                            <input type="checkbox" name="asaas_sandbox" value="1" {{ ($settings['asaas_sandbox'] ?? '1') == '1' ? 'checked' : '' }} class="rounded bg-slate-900 border-slate-700 text-red-600 focus:ring-0">
                            Usar ambiente de testes (sandbox)
                        </label>
                        <p class="text-[10px] text-slate-500">Webhook: <code class="text-slate-400">/api/webhook/asaas</code></p>
                    </div>
                    <div class="space-y-1.5 md:col-span-2">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Token do Webhook Asaas:</label>
                        <input type="password" name="asaas_webhook_token" value="{{ $settings['asaas_webhook_token'] }}" placeholder="Mesmo token configurado no painel Asaas (header asaas-access-token)" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                        <p class="text-[10px] text-slate-500">Obrigatório em produção. Configure o mesmo valor no Asaas como access token do webhook.</p>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Mercado Pago Access Token:</label>
                        <input type="password" name="gateway_mercadopago_key" value="{{ $settings['gateway_mercadopago_key'] }}" placeholder="Chave privada Mercado Pago" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Token do Webhook Mercado Pago:</label>
                        <input type="password" name="mercadopago_webhook_token" value="{{ $settings['mercadopago_webhook_token'] }}" placeholder="Token enviado no header X-Webhook-Token ou Authorization" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Itaú API Pix Direct -->
            <div class="space-y-4 p-4 rounded-xl border bg-slate-900/20" style="border-color: var(--border-color);">
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
            <div class="space-y-4 p-4 rounded-xl border bg-slate-900/20" style="border-color: var(--border-color);">
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
        </div>

        <!-- ================= TAB: INTEGRAÇÕES ================= -->
        <div id="tab-integracoes" class="tab-content glass-card rounded-2xl p-6 sm:p-8 space-y-6 hidden">
            <!-- Google reCAPTCHA -->
            <div class="space-y-4 p-4 rounded-xl border bg-slate-900/20" style="border-color: var(--border-color);">
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
                <p class="text-xs text-slate-400 leading-relaxed">
                    Crie as chaves em
                    <a href="https://www.google.com/recaptcha/admin" target="_blank" rel="noopener" class="underline" style="color: var(--accent);">google.com/recaptcha/admin</a>
                    para o domínio <strong class="text-slate-200">rrsorteio.com</strong> (e <code class="text-slate-300">localhost</code> se for testar local).
                    Protege login, cadastro, recuperação de senha e completar perfil.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Versão:</label>
                        <select name="recaptcha_version" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                            <option value="v3" {{ ($settings['recaptcha_version'] ?? 'v3') === 'v3' ? 'selected' : '' }}>v3 — Invisível (score)</option>
                            <option value="v2" {{ ($settings['recaptcha_version'] ?? 'v3') === 'v2' ? 'selected' : '' }}>v2 — Checkbox “Não sou um robô”</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Score mínimo (só v3):</label>
                        <input type="number" step="0.1" min="0" max="1" name="recaptcha_min_score" value="{{ $settings['recaptcha_min_score'] ?? '0.5' }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>
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
            <div class="space-y-4 p-4 rounded-xl border bg-slate-900/20" style="border-color: var(--border-color);">
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
                <p class="text-[11px] text-slate-500 leading-relaxed">
                    No Google Cloud Console, adicione esta URI de redirecionamento autorizada:<br>
                    <code class="text-slate-300 break-all">{{ url('/auth/google/callback') }}</code>
                </p>
            </div>

            <!-- SEO Google -->
            <div class="space-y-4 p-4 rounded-xl border bg-slate-900/20" style="border-color: var(--border-color);">
                <div class="border-b pb-2" style="border-color: var(--border-color);">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass-chart text-sm text-emerald-400"></i> SEO / Google Search
                    </h3>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs text-slate-400 font-semibold uppercase">Título SEO (padrão):</label>
                    <input type="text" name="seo_title" value="{{ $settings['seo_title'] }}" placeholder="RR Veículos | Ações Promocionais" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs text-slate-400 font-semibold uppercase">Meta Description:</label>
                    <textarea name="seo_description" rows="3" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none" placeholder="Descrição para o Google">{{ $settings['seo_description'] }}</textarea>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs text-slate-400 font-semibold uppercase">Keywords:</label>
                    <input type="text" name="seo_keywords" value="{{ $settings['seo_keywords'] }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs text-slate-400 font-semibold uppercase">Google Search Console (código de verificação):</label>
                    <input type="text" name="google_site_verification" value="{{ $settings['google_site_verification'] }}" placeholder="conteúdo do meta google-site-verification" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    <p class="text-[11px] text-slate-500">Sitemap: <a href="{{ url('/sitemap.xml') }}" target="_blank" class="underline text-slate-300">{{ url('/sitemap.xml') }}</a> · Robots: <a href="{{ url('/robots.txt') }}" target="_blank" class="underline text-slate-300">{{ url('/robots.txt') }}</a></p>
                </div>
            </div>

            <!-- Google Maps API -->
            <div class="space-y-4 p-4 rounded-xl border bg-slate-900/20" style="border-color: var(--border-color);">
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
        </div>

        <!-- ================= TAB: PÁGINAS INSTITUCIONAIS ================= -->
        <div id="tab-paginas" class="tab-content glass-card rounded-2xl p-6 sm:p-8 space-y-6 hidden">
            <div class="space-y-6">
                <div class="flex justify-between items-center border-b pb-2" style="border-color: var(--border-color);">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-file-lines text-sm text-blue-400"></i> Editor Visual de Conteúdo
                    </h3>
                    <span class="text-[10px] text-slate-400 uppercase font-semibold">Suporta Rich Text / HTML</span>
                </div>

                <!-- Quill.js Editor Blocks -->
                <div class="space-y-6">
                    <!-- Sobre Nós -->
                    <div class="space-y-2">
                        <label class="text-xs text-slate-400 font-bold uppercase tracking-wider">Página "Sobre Nós":</label>
                        <input type="hidden" name="page_about_us" id="input_page_about_us">
                        <div class="quill-wrapper rounded-xl overflow-hidden border border-slate-800">
                            <div id="editor_page_about_us" class="quill-editor h-48 bg-slate-900/50 text-slate-200">
                                {!! $settings['page_about_us'] !!}
                            </div>
                        </div>
                    </div>

                    <!-- Contato -->
                    <div class="space-y-4 p-4 rounded-xl border bg-slate-900/20" style="border-color: var(--border-color);">
                        <h4 class="text-sm font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-headset text-emerald-400"></i> Página Contato — canais oficiais
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-xs text-slate-400 font-semibold uppercase">WhatsApp:</label>
                                <input type="text" name="contact_whatsapp" value="{{ $settings['contact_whatsapp'] }}" placeholder="(66) 99999-9999" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs text-slate-400 font-semibold uppercase">E-mail:</label>
                                <input type="email" name="contact_email" value="{{ $settings['contact_email'] }}" placeholder="contato@rrsorteio.com" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                            </div>
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-xs text-slate-400 font-semibold uppercase">Endereço:</label>
                                <input type="text" name="contact_address" value="{{ $settings['contact_address'] }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs text-slate-400 font-semibold uppercase">Cidade:</label>
                                <input type="text" name="contact_city" value="{{ $settings['contact_city'] }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs text-slate-400 font-semibold uppercase">Horário (semana):</label>
                                <input type="text" name="contact_hours_weekdays" value="{{ $settings['contact_hours_weekdays'] }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                            </div>
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-xs text-slate-400 font-semibold uppercase">Horário (sábado):</label>
                                <input type="text" name="contact_hours_saturday" value="{{ $settings['contact_hours_saturday'] }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs text-slate-400 font-bold uppercase tracking-wider">Texto adicional (opcional):</label>
                            <input type="hidden" name="page_contact" id="input_page_contact">
                            <div class="quill-wrapper rounded-xl overflow-hidden border border-slate-800">
                                <div id="editor_page_contact" class="quill-editor h-48 bg-slate-900/50 text-slate-200">
                                    {!! $settings['page_contact'] !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dúvidas Frequentes -->
                    <div class="space-y-2">
                        <label class="text-xs text-slate-400 font-bold uppercase tracking-wider">Página "Dúvidas Frequentes" (FAQs):</label>
                        <input type="hidden" name="page_faqs" id="input_page_faqs">
                        <div class="quill-wrapper rounded-xl overflow-hidden border border-slate-800">
                            <div id="editor_page_faqs" class="quill-editor h-64 bg-slate-900/50 text-slate-200">
                                {!! $settings['page_faqs'] !!}
                            </div>
                        </div>
                    </div>

                    <!-- Política de Privacidade -->
                    <div class="space-y-2">
                        <label class="text-xs text-slate-400 font-bold uppercase tracking-wider">Política de Privacidade:</label>
                        <input type="hidden" name="page_privacy_policy" id="input_page_privacy_policy">
                        <div class="quill-wrapper rounded-xl overflow-hidden border border-slate-800">
                            <div id="editor_page_privacy_policy" class="quill-editor h-48 bg-slate-900/50 text-slate-200">
                                {!! $settings['page_privacy_policy'] !!}
                            </div>
                        </div>
                    </div>

                    <!-- Termos de Uso -->
                    <div class="space-y-2">
                        <label class="text-xs text-slate-400 font-bold uppercase tracking-wider">Termos de Uso:</label>
                        <input type="hidden" name="page_terms_of_use" id="input_page_terms_of_use">
                        <div class="quill-wrapper rounded-xl overflow-hidden border border-slate-800">
                            <div id="editor_page_terms_of_use" class="quill-editor h-48 bg-slate-900/50 text-slate-200">
                                {!! $settings['page_terms_of_use'] !!}
                            </div>
                        </div>
                    </div>

                    <!-- Regulamento -->
                    <div class="space-y-2">
                        <label class="text-xs text-slate-400 font-bold uppercase tracking-wider">Página "Regulamento":</label>
                        <input type="hidden" name="page_regulation" id="input_page_regulation">
                        <div class="quill-wrapper rounded-xl overflow-hidden border border-slate-800">
                            <div id="editor_page_regulation" class="quill-editor h-80 bg-slate-900/50 text-slate-200">
                                {!! $settings['page_regulation'] !!}
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-500">Página pública: <a href="{{ route('pages.regulation') }}" target="_blank" class="text-blue-400 hover:text-blue-300">/regulamento</a></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= TAB: EDITOR DE TEMPLATE ================= -->
        <div id="tab-template" class="tab-content glass-card rounded-2xl p-6 sm:p-8 space-y-8 hidden">
            <div class="space-y-2 border-b pb-4" style="border-color: var(--border-color);">
                <h3 class="text-base font-bold theme-title flex items-center gap-2">
                    <i class="fa-solid fa-palette" style="color: var(--accent);"></i> Editor de Template / Cores
                </h3>
                <p class="text-xs theme-muted">
                    Site público e painel admin têm paletas <strong class="theme-title">separadas</strong>.
                    Assim mudar o site não quebra o admin. Vermelho padrão da marca: <strong class="theme-title">#E61E25</strong>.
                </p>
                <div class="flex flex-wrap gap-2 pt-1">
                    <button type="button" onclick="resetThemeDefaults('light')" class="text-xs font-semibold px-3 py-1.5 rounded-lg border theme-muted" style="border-color: var(--border-color);">Restaurar site claro</button>
                    <button type="button" onclick="resetThemeDefaults('dark')" class="text-xs font-semibold px-3 py-1.5 rounded-lg border theme-muted" style="border-color: var(--border-color);">Restaurar site escuro</button>
                    <button type="button" onclick="resetThemeDefaults('admin_light')" class="text-xs font-semibold px-3 py-1.5 rounded-lg border theme-muted" style="border-color: var(--border-color);">Restaurar admin claro</button>
                    <button type="button" onclick="resetThemeDefaults('admin_dark')" class="text-xs font-semibold px-3 py-1.5 rounded-lg border theme-muted" style="border-color: var(--border-color);">Restaurar admin escuro</button>
                </div>
            </div>

            @php
                $colorLikeKeys = ['bg_primary','bg_sidebar','bg_card','panel_bg','input_bg','input_border','input_text','text_primary','text_secondary','on_accent','accent','accent_hover','accent_soft','badge_text','danger','metal'];
                $themeSections = [
                    'light' => ['label' => 'Site — Tema Claro', 'icon' => 'fa-sun', 'palette' => $themeLight],
                    'dark' => ['label' => 'Site — Tema Escuro', 'icon' => 'fa-moon', 'palette' => $themeDark],
                    'admin_light' => ['label' => 'Admin — Tema Claro', 'icon' => 'fa-user-shield', 'palette' => $themeAdminLight],
                    'admin_dark' => ['label' => 'Admin — Tema Escuro', 'icon' => 'fa-user-shield', 'palette' => $themeAdminDark],
                ];
            @endphp

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                @foreach($themeSections as $mode => $meta)
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold theme-title uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid {{ $meta['icon'] }}" style="color: var(--accent);"></i>
                            {{ $meta['label'] }}
                        </h4>

                        @php $groups = collect($themeDefinitions)->groupBy('group', true); @endphp
                        @foreach($groups as $groupName => $fields)
                            <div class="rounded-xl border p-4 space-y-3" style="border-color: var(--border-color); background: var(--panel-bg);">
                                <div class="text-[11px] font-bold uppercase tracking-wider theme-muted">{{ $groupName }}</div>
                                <div class="space-y-3">
                                    @foreach($fields as $key => $def)
                                        @php
                                            $value = old("theme_{$mode}.{$key}", $meta['palette'][$key] ?? '');
                                            $isColorPicker = in_array($key, $colorLikeKeys, true) && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value);
                                            $pickerValue = $isColorPicker ? $value : '#e61e25';
                                        @endphp
                                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center">
                                            <div class="sm:col-span-5">
                                                <label class="text-xs font-semibold theme-title">{{ $def['label'] }}</label>
                                                <p class="text-[10px] theme-muted">{{ $def['hint'] }}</p>
                                            </div>
                                            <div class="sm:col-span-7 flex items-center gap-2">
                                                <input
                                                    type="color"
                                                    value="{{ $pickerValue }}"
                                                    class="theme-color-picker h-10 w-12 rounded cursor-pointer bg-transparent border-0"
                                                    data-target="theme-{{ $mode }}-{{ $key }}"
                                                    title="Seletor"
                                                >
                                                <input
                                                    type="text"
                                                    id="theme-{{ $mode }}-{{ $key }}"
                                                    name="theme_{{ $mode }}[{{ $key }}]"
                                                    value="{{ $value }}"
                                                    class="theme-color-text flex-1 w-full rounded-xl px-3 py-2 text-xs font-mono focus:outline-none border"
                                                    style="background: var(--input-bg); border-color: var(--input-border); color: var(--input-text);"
                                                    placeholder="{{ $themeDefaults[$mode][$key] ?? '' }}"
                                                >
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="rounded-xl border p-4 space-y-3" style="border-color: var(--border-color);">
                            <div class="text-[11px] font-bold uppercase tracking-wider theme-muted">Prévia rápida</div>
                            <div id="theme-preview-{{ $mode }}" class="rounded-lg p-4 border" style="background: {{ $meta['palette']['bg_card'] }}; border-color: {{ $meta['palette']['border_color'] }}; color: {{ $meta['palette']['text_primary'] }};">
                                <div class="theme-preview-secondary text-xs mb-2" style="color: {{ $meta['palette']['text_secondary'] }};">Texto secundário</div>
                                <div class="font-bold mb-3">Exemplo de card</div>
                                <input type="text" readonly value="Campo de exemplo" class="theme-preview-input w-full mb-3 rounded-lg px-3 py-2 text-xs border" style="background: {{ $meta['palette']['input_bg'] }}; border-color: {{ $meta['palette']['input_border'] }}; color: {{ $meta['palette']['input_text'] }};">
                                <button type="button" class="theme-preview-btn px-3 py-2 rounded-lg text-xs font-bold" style="background: {{ $meta['palette']['accent'] }}; color: {{ $meta['palette']['on_accent'] ?? '#fff' }};">Botão destaque</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ================= TAB: ASSISTENTE DE IA ================= -->
        <div id="tab-ai-writer" class="tab-content glass-card rounded-2xl p-6 sm:p-8 space-y-6 hidden">
            <div class="space-y-4">
                <div class="border-b pb-3" style="border-color: var(--border-color);">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-wand-magic-sparkles text-pink-400"></i> Redator de FAQs com Inteligência Artificial
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">Gere blocos de perguntas e respostas bem estruturados e insira-os diretamente na sua página de Dúvidas ou Sobre Nós.</p>
                </div>

                <div class="space-y-3">
                    <label class="text-xs text-slate-300 font-semibold uppercase">Escolha um Tema Recomendado ou Digite sua Dúvida:</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="setAITopic('Como funciona o Pix e aprovação?')" class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-xs transition">
                            ⚡ Pix & Aprovação
                        </button>
                        <button type="button" onclick="setAITopic('Como é realizada a Ação Promocional do veículo?')" class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-xs transition">
                            🏆 Realização da Ação Promocional
                        </button>
                        <button type="button" onclick="setAITopic('Qual o prazo para pagamento das cotas reservadas?')" class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-xs transition">
                            ⏳ Prazo de Reservas
                        </button>
                        <button type="button" onclick="setAITopic('Como validar a autenticidade do meu bilhete?')" class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-xs transition">
                            🛡️ Validação de Bilhetes
                        </button>
                    </div>

                    <div class="flex gap-2">
                        <input type="text" id="ai-topic-input" placeholder="Ex: Como posso entrar em contato com o suporte?" class="flex-grow bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                        <button type="button" onclick="generateFAQWithAI()" class="bg-pink-600 hover:bg-pink-500 text-white font-bold px-5 py-3 rounded-xl text-sm transition flex items-center gap-1.5 shadow">
                            <i class="fa-solid fa-microchip animate-pulse"></i> Gerar FAQ
                        </button>
                    </div>
                </div>

                <!-- Painel de Resultados do Assistente de IA -->
                <div id="ai-result-panel" class="hidden p-4 rounded-xl border border-slate-800 bg-slate-900/30 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-pink-400 flex items-center gap-1">
                            <i class="fa-solid fa-robot"></i> Redação Sugerida pela IA:
                        </span>
                        <div class="flex gap-2">
                            <button type="button" onclick="insertAIGenerated('editor_page_faqs')" class="bg-indigo-600 hover:bg-indigo-500 text-white px-3 py-1.5 rounded-lg text-[10px] font-bold transition">
                                <i class="fa-solid fa-circle-plus"></i> Inserir em Dúvidas Frequentes
                            </button>
                            <button type="button" onclick="insertAIGenerated('editor_page_about_us')" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded-lg text-[10px] font-bold transition">
                                <i class="fa-solid fa-circle-plus"></i> Inserir em Sobre Nós
                            </button>
                        </div>
                    </div>

                    <div id="ai-faq-preview" class="p-4 bg-slate-950 border border-slate-850 rounded-lg text-xs text-slate-300 space-y-2 leading-relaxed max-h-60 overflow-y-auto">
                        <!-- Conteúdo do FAQ será injetado aqui -->
                    </div>
                </div>

                <!-- Indicador de Carregamento da IA -->
                <div id="ai-loading" class="hidden flex flex-col items-center justify-center p-8 space-y-3">
                    <i class="fa-solid fa-spinner text-pink-500 text-3xl animate-spin"></i>
                    <p class="text-xs text-slate-400 font-semibold animate-pulse">IA está redigindo respostas profissionais...</p>
                </div>
            </div>
        </div>

        <!-- Botão de Ação Fixo no Rodapé do Form -->
        <div class="flex justify-end pt-4">
            <button type="submit" onclick="syncQuillInputs()" class="w-full sm:w-auto bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-500 hover:to-green-500 text-white font-bold py-3.5 px-8 rounded-xl transition text-sm flex items-center justify-center gap-2 shadow-lg">
                <i class="fa-solid fa-floppy-disk"></i> Salvar Todas as Configurações
            </button>
        </div>
    </form>
</div>

<!-- JavaScript para Abas, Quill e Assistente de IA -->
<script>
    // Gerenciador de Abas
    function switchTab(tabId) {
        // Ocultar todos os conteúdos de abas
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        
        // Exibir a aba ativa
        document.getElementById(tabId).classList.remove('hidden');

        // Resetar estilos de botões
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('text-white', 'border-blue-500', 'bg-slate-900/40');
            btn.classList.add('text-slate-400', 'border-transparent');
        });

        // Setar estilo no botão ativo
        const activeBtn = document.getElementById('btn-' + tabId);
        activeBtn.classList.remove('text-slate-400', 'border-transparent');
        activeBtn.classList.add('text-white', 'border-blue-500', 'bg-slate-900/40');
    }

    // Inicialização do Quill.js para cada editor
    const quillEditors = {};
    const editorsConfig = [
        'editor_page_about_us',
        'editor_page_contact',
        'editor_page_faqs',
        'editor_page_privacy_policy',
        'editor_page_terms_of_use',
        'editor_page_regulation'
    ];

    editorsConfig.forEach(id => {
        quillEditors[id] = new Quill('#' + id, {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'clean']
                ]
            }
        });
    });

    // Sincronizar dados do Quill com inputs ocultos antes do submit
    function syncQuillInputs() {
        document.getElementById('input_page_about_us').value = quillEditors['editor_page_about_us'].root.innerHTML;
        document.getElementById('input_page_contact').value = quillEditors['editor_page_contact'].root.innerHTML;
        document.getElementById('input_page_faqs').value = quillEditors['editor_page_faqs'].root.innerHTML;
        document.getElementById('input_page_privacy_policy').value = quillEditors['editor_page_privacy_policy'].root.innerHTML;
        document.getElementById('input_page_terms_of_use').value = quillEditors['editor_page_terms_of_use'].root.innerHTML;
        document.getElementById('input_page_regulation').value = quillEditors['editor_page_regulation'].root.innerHTML;
    }

    // Sincronizar na submissão do formulário
    document.getElementById('settings-form').addEventListener('submit', function() {
        syncQuillInputs();
    });

    // Assistente de IA: Configurar tema
    function setAITopic(text) {
        document.getElementById('ai-topic-input').value = text;
    }

    // Gerador de FAQs simulado local de altíssima qualidade
    const aiDatabase = {
        'como funciona o pix e aprovação?': `
            <div class="faq-item mb-4" style="margin-bottom: 20px;">
                <h3 style="font-size: 15px; font-weight: bold; color: #ef4444; margin-bottom: 5px;">Como funciona o pagamento via PIX e em quanto tempo é aprovado?</h3>
                <p style="font-size: 13px; color: #94a3b8; line-height: 1.6;">O pagamento via <strong>PIX</strong> é totalmente automatizado e integrado. Ao escanear o QR Code ou utilizar o código "Copia e Cola", a transação é aprovada instantaneamente. O sistema reconhece a aprovação do banco e, em menos de 10 segundos, atualiza o status de suas cotas para "Pago", gerando e enviando o comprovante diretamente no seu painel de usuário.</p>
            </div>
        `,
        'como é realizada a ação promocional do veículo?': `
            <div class="faq-item mb-4" style="margin-bottom: 20px;">
                <h3 style="font-size: 15px; font-weight: bold; color: #ef4444; margin-bottom: 5px;">Como e quando é realizada a Ação Promocional do prêmio?</h3>
                <p style="font-size: 13px; color: #94a3b8; line-height: 1.6;">Todas as nossas Ações Promocionais são executadas com total transparência. A data e o horário oficiais do sorteio ficam divulgados na página da ação. O sorteio é realizado <strong>ao vivo pelo site e pelo canal no YouTube</strong>, com revelação do número contemplado entre os bilhetes pagos. O ganhador é notificado pelos canais oficiais (e-mail e WhatsApp, quando disponíveis).</p>
            </div>
        `,
        'qual o prazo para pagamento das cotas reservadas?': `
            <div class="faq-item mb-4" style="margin-bottom: 20px;">
                <h3 style="font-size: 15px; font-weight: bold; color: #ef4444; margin-bottom: 5px;">Qual o prazo de validade das minhas cotas reservadas antes do pagamento?</h3>
                <p style="font-size: 13px; color: #94a3b8; line-height: 1.6;">Após selecionar e reservar seus números da sorte, você tem o prazo padrão de <strong>30 minutos</strong> para concluir o pagamento via PIX. Caso o pagamento não seja identificado dentro deste período, o sistema libera os números automaticamente de volta para o grid público para que outros participantes possam adquiri-los.</p>
            </div>
        `,
        'como validar a autenticidade do meu bilhete?': `
            <div class="faq-item mb-4" style="margin-bottom: 20px;">
                <h3 style="font-size: 15px; font-weight: bold; color: #ef4444; margin-bottom: 5px;">Como posso ter certeza de que o meu recibo e bilhete são autênticos?</h3>
                <p style="font-size: 13px; color: #94a3b8; line-height: 1.6;">Todo recibo oficial gerado possui um <strong>QR Code exclusivo de validação eletrônica</strong> e um código hash de transação. Você pode validar seu bilhete apontando a câmera do celular para o QR Code ou digitando o código de transação diretamente no validador oficial na página: <code>/validate-ticket</code>. Para visitantes comuns, os dados são exibidos de forma mascarada respeitando a LGPD.</p>
            </div>
        `
    };

    let lastGeneratedHtml = "";

    function generateFAQWithAI() {
        const input = document.getElementById('ai-topic-input').value.trim();
        if (!input) {
            alert('Por favor, digite um assunto ou pergunta.');
            return;
        }

        const loading = document.getElementById('ai-loading');
        const result = document.getElementById('ai-result-panel');
        const preview = document.getElementById('ai-faq-preview');

        // Mostrar loading
        loading.classList.remove('hidden');
        result.classList.add('hidden');

        // Simulação da geração em 1.5s
        setTimeout(() => {
            loading.classList.add('hidden');
            result.classList.remove('hidden');

            const key = input.toLowerCase();
            let htmlResult = "";

            // Procurar chave ou gerar dinamicamente
            let found = false;
            for (let dbKey in aiDatabase) {
                if (key.includes(dbKey.substring(0, 10)) || dbKey.includes(key.substring(0, 10))) {
                    htmlResult = aiDatabase[dbKey];
                    found = true;
                    break;
                }
            }

            if (!found) {
                // Geração dinâmica inteligente com base no input
                htmlResult = `
                    <div class="faq-item mb-4" style="margin-bottom: 20px;">
                        <h3 style="font-size: 15px; font-weight: bold; color: #ef4444; margin-bottom: 5px;">${input}</h3>
                        <p style="font-size: 13px; color: #94a3b8; line-height: 1.6;">Prezado participante, a respeito de <strong>"${input}"</strong>, esclarecemos que nossa plataforma atua sob rígidos padrões de transparência na Ação RR Veículos. Para suporte ou detalhes específicos desta questão, sinta-se à vontade para entrar em contato com nosso atendimento online pelo WhatsApp de Suporte ou enviando um e-mail direto à nossa equipe de regulação.</p>
                    </div>
                `;
            }

            lastGeneratedHtml = htmlResult;
            preview.innerHTML = htmlResult;
        }, 1500);
    }

    // Inserir no editor selecionado
    function insertAIGenerated(editorId) {
        if (!lastGeneratedHtml) return;
        
        const editor = quillEditors[editorId];
        
        // Obter o conteúdo HTML atual e concatenar
        const currentHtml = editor.root.innerHTML;
        editor.root.innerHTML = currentHtml + lastGeneratedHtml;
        
        alert('Conteúdo gerado pela IA inserido com sucesso ao final do editor!');
    }

    document.querySelectorAll('.theme-color-picker').forEach((picker) => {
        const textInput = document.getElementById(picker.dataset.target);
        if (!textInput) {
            return;
        }

        picker.addEventListener('input', () => {
            textInput.value = picker.value;
            refreshThemePreview(picker.dataset.target);
        });

        textInput.addEventListener('input', () => {
            if (/^#[0-9A-Fa-f]{6}$/.test(textInput.value.trim())) {
                picker.value = textInput.value.trim();
            }
            refreshThemePreview(textInput.id);
        });
    });

    const themeDefaults = @json($themeDefaults);

    function refreshThemePreview(inputId) {
        const match = String(inputId).match(/^theme-((?:admin_)?(?:light|dark))-(.+)$/);
        if (!match) {
            return;
        }

        const theme = match[1];
        const preview = document.getElementById(`theme-preview-${theme}`);
        if (!preview) {
            return;
        }

        const read = (key) => {
            const el = document.getElementById(`theme-${theme}-${key}`);
            return el ? el.value : '';
        };

        preview.style.background = read('bg_card');
        preview.style.borderColor = read('border_color');
        preview.style.color = read('text_primary');

        const secondary = preview.querySelector('.theme-preview-secondary');
        if (secondary) {
            secondary.style.color = read('text_secondary');
        }

        const input = preview.querySelector('.theme-preview-input');
        if (input) {
            input.style.background = read('input_bg');
            input.style.borderColor = read('input_border');
            input.style.color = read('input_text');
        }

        const btn = preview.querySelector('.theme-preview-btn');
        if (btn) {
            btn.style.background = read('accent');
            btn.style.color = read('on_accent') || '#ffffff';
        }
    }

    function resetThemeDefaults(theme) {
        const defaults = themeDefaults[theme] || {};
        Object.entries(defaults).forEach(([key, value]) => {
            const textInput = document.getElementById(`theme-${theme}-${key}`);
            const picker = document.querySelector(`.theme-color-picker[data-target="theme-${theme}-${key}"]`);
            if (textInput) {
                textInput.value = value;
            }
            if (picker && /^#[0-9A-Fa-f]{6}$/.test(String(value))) {
                picker.value = value;
            }
        });
        refreshThemePreview(`theme-${theme}-accent`);
    }
</script>

<style>
    /* Custom Styling para Quill no Tema Escuro do Admin */
    .ql-toolbar.ql-snow {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
    }
    .ql-container.ql-snow {
        border-color: #334155 !important;
        border-bottom-left-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
        font-family: inherit;
    }
    .ql-editor {
        font-size: 0.875rem;
        color: #e2e8f0 !important;
        background-color: #0f172a !important;
        min-height: 150px;
    }
    .ql-snow .ql-stroke {
        stroke: #94a3b8 !important;
    }
    .ql-snow .ql-fill {
        fill: #94a3b8 !important;
    }
    .ql-snow .ql-picker {
        color: #94a3b8 !important;
    }
    .ql-snow .ql-picker-options {
        background-color: #1e293b !important;
        border-color: #334155 !important;
    }
</style>
@endsection
