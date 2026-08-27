@extends('layouts.public')

@section('title', 'Nova senha - Ação RR Veículos')

@section('content')
<div class="max-w-md mx-auto space-y-6 pt-10">
    <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-6">
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-bold text-white flex items-center justify-center gap-2">
                <i class="fa-solid fa-lock" style="color: var(--accent);"></i> Definir nova senha
            </h1>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-4" id="reset-password-form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="space-y-1">
                <label class="text-xs text-slate-400 font-semibold block">E-mail</label>
                <input type="email" name="email" value="{{ old('email', $email) }}" required
                    class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
            </div>
            <div class="space-y-1">
                <label class="text-xs text-slate-400 font-semibold block">Nova senha</label>
                <input type="password" name="password" required minlength="6"
                    class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
            </div>
            <div class="space-y-1">
                <label class="text-xs text-slate-400 font-semibold block">Confirmar nova senha</label>
                <input type="password" name="password_confirmation" required minlength="6"
                    class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
            </div>
            <x-recaptcha action="reset_password" form-id="reset-password-form" />
            <button type="submit" class="w-full text-white font-semibold py-3 rounded-xl transition text-sm" style="background-color: var(--accent);">
                Salvar nova senha
            </button>
        </form>
    </div>
</div>
@endsection
