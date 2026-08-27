@extends('layouts.public')

@section('title', 'Completar cadastro - Ação RR Veículos')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 pt-6 pb-10">
    <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-6">
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-bold text-white flex items-center justify-center gap-2">
                <i class="fa-solid fa-id-card" style="color: var(--accent);"></i> Completar cadastro
            </h1>
            <p class="text-slate-400 text-sm">
                Conta Google conectada{{ $user->email ? ' ('.$user->email.')' : '' }}.
                Para participar, complete os dados obrigatórios. Somente maiores de 18 anos.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('profile.complete.submit') }}" method="POST" class="space-y-5" id="register-form" novalidate>
            @csrf

            <div class="space-y-1">
                <label class="text-xs text-slate-400 font-semibold block">Nome Completo</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name"
                    class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-xs text-slate-400 font-semibold block">CPF</label>
                    <input type="text" name="cpf" id="cpf" value="{{ old('cpf', $user->cpf) }}" required inputmode="numeric" maxlength="14" autocomplete="off" placeholder="000.000.000-00"
                        class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700"
                        aria-describedby="cpf-feedback">
                    <p id="cpf-feedback" class="text-xs hidden" role="status"></p>
                </div>
                <div class="space-y-1">
                    <label class="text-xs text-slate-400 font-semibold block">Data de nascimento</label>
                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', optional($user->birth_date)->format('Y-m-d')) }}" required max="{{ now()->subYears(18)->toDateString() }}"
                        class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700"
                        aria-describedby="birth-feedback">
                    <p id="birth-feedback" class="text-xs hidden" role="status"></p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-xs text-slate-400 font-semibold block">WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}" required inputmode="tel" placeholder="(66) 99999-9999"
                        class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                </div>
                <div class="space-y-1">
                    <label class="text-xs text-slate-400 font-semibold block">Telefone extra <span class="text-slate-500 font-normal">(opcional)</span></label>
                    <input type="text" name="phone_extra" value="{{ old('phone_extra', $user->phone_extra) }}" inputmode="tel"
                        class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                </div>
            </div>

            <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4 space-y-3">
                <p class="text-xs font-semibold text-slate-300 uppercase tracking-wide">Endereço</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="space-y-1 sm:col-span-1">
                        <label class="text-xs text-slate-400 font-semibold block">CEP</label>
                        <input type="text" name="zip_code" value="{{ old('zip_code', $user->zip_code) }}" required inputmode="numeric"
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                    </div>
                    <div class="space-y-1 sm:col-span-2">
                        <label class="text-xs text-slate-400 font-semibold block">Rua / Avenida</label>
                        <input type="text" name="address_street" value="{{ old('address_street', $user->address_street) }}" required
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs text-slate-400 font-semibold block">Número</label>
                        <input type="text" name="address_number" value="{{ old('address_number', $user->address_number) }}" required
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                    </div>
                    <div class="space-y-1 col-span-1 sm:col-span-3">
                        <label class="text-xs text-slate-400 font-semibold block">Complemento</label>
                        <input type="text" name="address_complement" value="{{ old('address_complement', $user->address_complement) }}"
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs text-slate-400 font-semibold block">Bairro</label>
                        <input type="text" name="address_neighborhood" value="{{ old('address_neighborhood', $user->address_neighborhood) }}" required
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs text-slate-400 font-semibold block">Cidade</label>
                        <input type="text" name="address_city" value="{{ old('address_city', $user->address_city) }}" required
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs text-slate-400 font-semibold block">UF</label>
                        <input type="text" name="address_state" value="{{ old('address_state', $user->address_state) }}" required maxlength="2"
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 uppercase focus:outline-none focus:border-slate-700">
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-amber-500/30 bg-amber-500/5 p-4 space-y-3">
                <a href="{{ route('pages.regulation') }}" target="_blank" rel="noopener"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition"
                    style="background-color: var(--accent);">
                    <i class="fa-solid fa-scale-balanced"></i> Ler regulamento
                </a>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="accepted_regulation" value="1" required @checked(old('accepted_regulation') || $user->accepted_regulation_at)
                        class="mt-1 rounded bg-slate-900 border-slate-700 text-red-600 focus:ring-0">
                    <span class="text-sm text-slate-200">Declaro que li e aceito o regulamento oficial da Ação Promocional.</span>
                </label>
            </div>

            <x-recaptcha action="complete_profile" form-id="register-form" />

            <button type="submit" class="w-full text-white font-semibold py-3 rounded-xl transition text-sm" style="background-color: var(--accent);">
                Salvar e continuar
            </button>
        </form>
    </div>
</div>

<script>
(() => {
    const cpfInput = document.getElementById('cpf');
    const birthInput = document.getElementById('birth_date');
    const cpfFeedback = document.getElementById('cpf-feedback');
    const birthFeedback = document.getElementById('birth-feedback');
    const maxAdultDate = birthInput?.getAttribute('max') || '';
    const form = document.getElementById('register-form');
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
        if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;
        for (let t = 9; t < 11; t++) {
            let sum = 0;
            for (let i = 0; i < t; i++) sum += Number(cpf[i]) * ((t + 1) - i);
            if (Number(cpf[t]) !== ((10 * sum) % 11) % 10) return false;
        }
        return true;
    };
    const setFieldState = (input, feedback, state, message) => {
        feedback.classList.remove('hidden', 'text-emerald-400', 'text-red-400', 'text-slate-400');
        input.classList.remove('border-emerald-500/60', 'border-red-500/60', 'border-slate-800');
        if (state === 'ok') { feedback.classList.add('text-emerald-400'); input.classList.add('border-emerald-500/60'); }
        else if (state === 'error') { feedback.classList.add('text-red-400'); input.classList.add('border-red-500/60'); }
        else { feedback.classList.add('text-slate-400', 'hidden'); input.classList.add('border-slate-800'); feedback.textContent = ''; return true; }
        feedback.textContent = message;
        return state === 'ok';
    };
    const validateCpfLive = () => {
        const digits = onlyDigits(cpfInput.value);
        if (!digits.length) return setFieldState(cpfInput, cpfFeedback, 'idle', '');
        if (digits.length < 11) return setFieldState(cpfInput, cpfFeedback, 'error', 'Digite os 11 dígitos do CPF.');
        if (!isValidCpf(digits)) return setFieldState(cpfInput, cpfFeedback, 'error', 'CPF inválido. Informe um número real.');
        return setFieldState(cpfInput, cpfFeedback, 'ok', 'CPF válido.');
    };
    const validateBirthLive = () => {
        const value = birthInput.value;
        if (!value) return setFieldState(birthInput, birthFeedback, 'idle', '');
        if (maxAdultDate && value > maxAdultDate) return setFieldState(birthInput, birthFeedback, 'error', 'É obrigatório ter 18 anos ou mais.');
        return setFieldState(birthInput, birthFeedback, 'ok', 'Idade confirmada (maior de 18 anos).');
    };
    cpfInput.addEventListener('input', () => { cpfInput.value = formatCpf(onlyDigits(cpfInput.value)); validateCpfLive(); });
    birthInput.addEventListener('change', validateBirthLive);
    form.addEventListener('submit', (event) => {
        if (!validateCpfLive() || !validateBirthLive()) event.preventDefault();
    });
    if (cpfInput.value) { cpfInput.value = formatCpf(onlyDigits(cpfInput.value)); validateCpfLive(); }
    if (birthInput.value) validateBirthLive();
})();
</script>
@endsection
