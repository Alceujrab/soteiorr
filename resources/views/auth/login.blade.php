@extends('layouts.public')

@section('title', 'Entrar - Ação RR Veículos')

@section('content')
<div class="max-w-md mx-auto space-y-6 pt-10">
    <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-6">
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-bold text-white flex items-center justify-center gap-2">
                <i class="fa-solid fa-right-to-bracket text-blue-500"></i> Acessar Conta
            </h1>
            <p class="text-slate-400 text-sm">Insira seu e-mail e senha cadastrados.</p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @include('partials.google-auth-button')

        <form action="{{ route('login') }}" method="POST" class="space-y-4" id="login-form">
            @csrf

            <div class="space-y-1">
                <label class="text-xs text-slate-400 font-semibold block">E-mail:</label>
                <input type="email" name="email" required placeholder="seuemail@exemplo.com" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
            </div>

            <div class="space-y-1">
                <label class="text-xs text-slate-400 font-semibold block">Senha:</label>
                <input type="password" name="password" required placeholder="Sua senha secreta" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-xs text-slate-400 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded bg-slate-900 border-slate-800 text-blue-600 focus:ring-0">
                    Lembrar-me
                </label>
                <a href="{{ route('password.request') }}" class="text-xs font-semibold hover:opacity-80" style="color: var(--accent);">Esqueci a senha</a>
            </div>

            <x-recaptcha action="login" form-id="login-form" />

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 rounded-xl transition text-sm">
                Entrar
            </button>
        </form>

        <div class="border-t border-slate-800/80 pt-4 text-center">
            <span class="text-xs text-slate-500">Não tem uma conta?</span>
            <a href="{{ route('register') }}" class="text-xs font-semibold text-blue-400 hover:text-blue-300 ml-1">Cadastre-se</a>
        </div>
    </div>
</div>
@endsection
