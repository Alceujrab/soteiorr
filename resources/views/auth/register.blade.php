@extends('layouts.public')

@section('title', 'Cadastrar-se - Ação RR Veículos')

@section('content')
@php
    $showRegisterForm = $errors->any() || filled(old());
@endphp
<div class="max-w-2xl mx-auto space-y-6 pt-6 pb-10">
    <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-6">
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-bold text-white flex items-center justify-center gap-2">
                <i class="fa-solid fa-user" style="color: var(--accent);"></i> Acesso à conta
            </h1>
            <p class="text-slate-400 text-sm">Escolha como deseja continuar. Somente maiores de 18 anos podem se cadastrar.</p>
        </div>

        <div id="auth-choice" class="space-y-3 {{ $showRegisterForm ? 'hidden' : '' }}">
            @include('partials.google-auth-button')
            <a href="{{ route('login') }}"
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3.5 rounded-xl text-sm font-semibold text-white border border-slate-700 bg-slate-900/80 hover:bg-slate-800 transition">
                <i class="fa-solid fa-right-to-bracket"></i> Já possuo conta
            </a>
            <button type="button" id="show-register-form"
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3.5 rounded-xl text-sm font-semibold text-white transition"
                style="background-color: var(--accent);">
                <i class="fa-solid fa-user-plus"></i> Criar conta
            </button>
        </div>

        <div id="register-panel" class="space-y-5 {{ $showRegisterForm ? '' : 'hidden' }}">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-plus" style="color: var(--accent);"></i> Criar conta
                </h2>
                <button type="button" id="back-to-choice" class="text-xs font-semibold text-slate-400 hover:text-white transition">
                    Voltar
                </button>
            </div>

            @if ($errors->any())
                <div class="rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200 space-y-2">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                    @if ($errors->has('email') || $errors->has('cpf'))
                        <p class="pt-1">
                            <a href="{{ route('login') }}" class="underline font-semibold" style="color: var(--accent);">Fazer login</a>
                            <span class="text-slate-400"> ou </span>
                            <a href="{{ route('password.request') }}" class="underline font-semibold" style="color: var(--accent);">recuperar senha</a>
                        </p>
                    @endif
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-5" id="register-form" novalidate>
                @csrf

                <div class="space-y-1">
                    <label class="text-xs text-slate-400 font-semibold block">Nome Completo</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Seu nome completo"
                        class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs text-slate-400 font-semibold block">CPF</label>
                        <input type="text" name="cpf" id="cpf" value="{{ old('cpf') }}" required inputmode="numeric" maxlength="14" autocomplete="off" placeholder="000.000.000-00"
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700"
                            aria-describedby="cpf-feedback">
                        <p id="cpf-feedback" class="text-xs hidden" role="status"></p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs text-slate-400 font-semibold block">Data de nascimento</label>
                        <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" required max="{{ now()->subYears(18)->toDateString() }}"
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700"
                            aria-describedby="birth-feedback">
                        <p id="birth-feedback" class="text-xs hidden" role="status"></p>
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

                <x-recaptcha action="register" form-id="register-form" />

                <button type="submit" id="register-submit" class="w-full text-white font-semibold py-3 rounded-xl transition text-sm" style="background-color: var(--accent);">
                    Criar Minha Conta
                </button>
            </form>
        </div>
    </div>
</div>

<script>
(() => {
    const choice = document.getElementById('auth-choice');
    const panel = document.getElementById('register-panel');
    const showBtn = document.getElementById('show-register-form');
    const backBtn = document.getElementById('back-to-choice');
    const form = document.getElementById('register-form');
    const cpfInput = document.getElementById('cpf');
    const birthInput = document.getElementById('birth_date');
    const cpfFeedback = document.getElementById('cpf-feedback');
    const birthFeedback = document.getElementById('birth-feedback');
    const maxAdultDate = birthInput?.getAttribute('max') || '';

    const showRegister = () => {
        choice.classList.add('hidden');
        panel.classList.remove('hidden');
        window.scrollTo({ top: panel.offsetTop - 24, behavior: 'smooth' });
    };

    const showChoice = () => {
        panel.classList.add('hidden');
        choice.classList.remove('hidden');
    };

    showBtn?.addEventListener('click', showRegister);
    backBtn?.addEventListener('click', showChoice);

    const toggle = document.getElementById('generate_password');
    const fields = document.getElementById('password-fields');
    const password = document.getElementById('password');
    const confirmation = document.getElementById('password_confirmation');

    const syncPassword = () => {
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

    toggle.addEventListener('change', syncPassword);
    syncPassword();

    const onlyDigits = (value) => (value || '').replace(/\D+/g, '');

    const formatCpf = (digits) => {
        const d = digits.slice(0, 11);
        if (d.length <= 3) return d;
        if (d.length <= 6) return d.slice(0, 3) + '.' + d.slice(3);
        if (d.length <= 9) return d.slice(0, 3) + '.' + d.slice(3, 6) + '.' + d.slice(6);
        return d.slice(0, 3) + '.' + d.slice(3, 6) + '.' + d.slice(6, 9) + '-' + d.slice(9);
    };

    const isValidCpf = (value) => {
        const cpf = onlyDigits(value);
        if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
            return false;
        }
        for (let t = 9; t < 11; t++) {
            let sum = 0;
            for (let i = 0; i < t; i++) {
                sum += Number(cpf[i]) * ((t + 1) - i);
            }
            const digit = ((10 * sum) % 11) % 10;
            if (Number(cpf[t]) !== digit) {
                return false;
            }
        }
        return true;
    };

    const setFieldState = (input, feedback, state, message) => {
        feedback.classList.remove('hidden', 'text-emerald-400', 'text-red-400', 'text-slate-400');
        input.classList.remove('border-emerald-500/60', 'border-red-500/60', 'border-slate-800');

        if (state === 'ok') {
            feedback.classList.add('text-emerald-400');
            input.classList.add('border-emerald-500/60');
        } else if (state === 'error') {
            feedback.classList.add('text-red-400');
            input.classList.add('border-red-500/60');
        } else {
            feedback.classList.add('text-slate-400', 'hidden');
            input.classList.add('border-slate-800');
            feedback.textContent = '';
            return true;
        }

        feedback.textContent = message;
        return state === 'ok';
    };

    const validateCpfLive = () => {
        const digits = onlyDigits(cpfInput.value);
        if (digits.length === 0) {
            return setFieldState(cpfInput, cpfFeedback, 'idle', '');
        }
        if (digits.length < 11) {
            return setFieldState(cpfInput, cpfFeedback, 'error', 'Digite os 11 dígitos do CPF.');
        }
        if (!isValidCpf(digits)) {
            return setFieldState(cpfInput, cpfFeedback, 'error', 'CPF inválido. Informe um número real.');
        }
        return setFieldState(cpfInput, cpfFeedback, 'ok', 'CPF válido.');
    };

    const ageFromDate = (isoDate) => {
        const birth = new Date(isoDate + 'T00:00:00');
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        return age;
    };

    const validateBirthLive = () => {
        const value = birthInput.value;
        if (!value) {
            return setFieldState(birthInput, birthFeedback, 'idle', '');
        }
        if (maxAdultDate && value > maxAdultDate) {
            return setFieldState(birthInput, birthFeedback, 'error', 'É obrigatório ter 18 anos ou mais.');
        }
        if (ageFromDate(value) < 18) {
            return setFieldState(birthInput, birthFeedback, 'error', 'É obrigatório ter 18 anos ou mais.');
        }
        return setFieldState(birthInput, birthFeedback, 'ok', 'Idade confirmada (maior de 18 anos).');
    };

    cpfInput.addEventListener('input', () => {
        const digits = onlyDigits(cpfInput.value);
        cpfInput.value = formatCpf(digits);
        validateCpfLive();
    });
    cpfInput.addEventListener('blur', validateCpfLive);

    birthInput.addEventListener('input', validateBirthLive);
    birthInput.addEventListener('change', validateBirthLive);
    birthInput.addEventListener('blur', validateBirthLive);

    form.addEventListener('submit', (event) => {
        const cpfOk = validateCpfLive();
        const birthOk = validateBirthLive();

        if (!cpfOk || !birthOk) {
            event.preventDefault();
            if (!cpfOk) {
                cpfInput.focus();
            } else if (!birthOk) {
                birthInput.focus();
            }
        }
    });

    if (cpfInput.value) {
        cpfInput.value = formatCpf(onlyDigits(cpfInput.value));
        validateCpfLive();
    }
    if (birthInput.value) {
        validateBirthLive();
    }
})();
</script>
@endsection
