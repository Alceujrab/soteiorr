@extends('layouts.public')

@section('title', 'Cadastrar-se - Ação RR Veículos')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 pt-6 pb-10">
    <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-6">
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-bold text-white flex items-center justify-center gap-2">
                <i class="fa-solid fa-user-plus" style="color: var(--accent);"></i> Criar Conta
            </h1>
            <p class="text-slate-400 text-sm">Cadastre-se para reservar seus números da sorte. Somente maiores de 18 anos.</p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-5" id="register-form">
            @csrf

            <div class="space-y-1">
                <label class="text-xs text-slate-400 font-semibold block">Nome Completo</label>
                <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Seu nome completo"
                    class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-xs text-slate-400 font-semibold block">CPF</label>
                    <input type="text" name="cpf" value="{{ old('cpf') }}" required inputmode="numeric" placeholder="000.000.000-00"
                        class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                </div>
                <div class="space-y-1">
                    <label class="text-xs text-slate-400 font-semibold block">Data de nascimento</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" required max="{{ now()->subYears(18)->toDateString() }}"
                        class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs text-slate-400 font-semibold block">E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="seuemail@exemplo.com"
                    class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-xs text-slate-400 font-semibold block">WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" required inputmode="tel" placeholder="(66) 99999-9999"
                        class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                </div>
                <div class="space-y-1">
                    <label class="text-xs text-slate-400 font-semibold block">Telefone extra <span class="text-slate-500 font-normal">(opcional)</span></label>
                    <input type="text" name="phone_extra" value="{{ old('phone_extra') }}" inputmode="tel" placeholder="(66) 3333-3333"
                        class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                </div>
            </div>

            <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4 space-y-3">
                <p class="text-xs font-semibold text-slate-300 uppercase tracking-wide">Endereço</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="space-y-1 sm:col-span-1">
                        <label class="text-xs text-slate-400 font-semibold block">CEP</label>
                        <input type="text" name="zip_code" value="{{ old('zip_code') }}" required inputmode="numeric" placeholder="00000-000"
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                    </div>
                    <div class="space-y-1 sm:col-span-2">
                        <label class="text-xs text-slate-400 font-semibold block">Rua / Avenida</label>
                        <input type="text" name="address_street" value="{{ old('address_street') }}" required placeholder="Rua, avenida..."
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs text-slate-400 font-semibold block">Número</label>
                        <input type="text" name="address_number" value="{{ old('address_number') }}" required placeholder="Nº"
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                    </div>
                    <div class="space-y-1 col-span-1 sm:col-span-3">
                        <label class="text-xs text-slate-400 font-semibold block">Complemento <span class="text-slate-500 font-normal">(opcional)</span></label>
                        <input type="text" name="address_complement" value="{{ old('address_complement') }}" placeholder="Apto, bloco..."
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs text-slate-400 font-semibold block">Bairro</label>
                        <input type="text" name="address_neighborhood" value="{{ old('address_neighborhood') }}" required
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs text-slate-400 font-semibold block">Cidade</label>
                        <input type="text" name="address_city" value="{{ old('address_city') }}" required
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs text-slate-400 font-semibold block">UF</label>
                        <input type="text" name="address_state" value="{{ old('address_state') }}" required maxlength="2" placeholder="MT"
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 uppercase focus:outline-none focus:border-slate-700">
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4 space-y-3">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="generate_password" value="1" id="generate_password" @checked(old('generate_password'))
                        class="mt-1 rounded bg-slate-900 border-slate-700 text-red-600 focus:ring-0">
                    <span class="text-sm text-slate-300">Gerar senha automaticamente e enviar no e-mail de confirmação</span>
                </label>

                <div id="password-fields" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs text-slate-400 font-semibold block">Senha</label>
                        <input type="password" name="password" id="password" minlength="6" placeholder="Mínimo 6 caracteres"
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs text-slate-400 font-semibold block">Confirmar senha</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" minlength="6" placeholder="Repita a senha"
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-amber-500/30 bg-amber-500/5 p-4 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <a href="{{ route('pages.regulation') }}" target="_blank" rel="noopener"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition"
                        style="background-color: var(--accent);">
                        <i class="fa-solid fa-scale-balanced"></i> Ler regulamento
                    </a>
                    <p class="text-xs text-slate-400">Abre em nova aba. Leia antes de concluir o cadastro.</p>
                </div>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="accepted_regulation" value="1" required @checked(old('accepted_regulation'))
                        class="mt-1 rounded bg-slate-900 border-slate-700 text-red-600 focus:ring-0">
                    <span class="text-sm text-slate-200">
                        Declaro que li e aceito o
                        <a href="{{ route('pages.regulation') }}" target="_blank" rel="noopener" class="underline font-semibold" style="color: var(--accent);">regulamento oficial</a>
                        da Ação Promocional. Sem este aceite o cadastro não será salvo.
                    </span>
                </label>
            </div>

            <button type="submit" class="w-full text-white font-semibold py-3 rounded-xl transition text-sm" style="background-color: var(--accent);">
                Criar Minha Conta
            </button>
        </form>

        <div class="border-t border-slate-800/80 pt-4 text-center">
            <span class="text-xs text-slate-500">Já possui uma conta?</span>
            <a href="{{ route('login') }}" class="text-xs font-semibold hover:opacity-80 ml-1" style="color: var(--accent);">Fazer Login</a>
        </div>
    </div>
</div>

<script>
(() => {
    const toggle = document.getElementById('generate_password');
    const fields = document.getElementById('password-fields');
    const password = document.getElementById('password');
    const confirmation = document.getElementById('password_confirmation');

    const sync = () => {
        const auto = toggle.checked;
        fields.classList.toggle('opacity-50', auto);
        password.required = !auto;
        confirmation.required = !auto;
        password.disabled = auto;
        confirmation.disabled = auto;
        if (auto) {
            password.value = '';
            confirmation.value = '';
        }
    };

    toggle.addEventListener('change', sync);
    sync();
})();
</script>
@endsection
