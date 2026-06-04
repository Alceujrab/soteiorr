@extends('layouts.public')

@section('title', 'Cadastrar-se - Ação RR Veículos')

@section('content')
<div class="max-w-md mx-auto space-y-6 pt-6">
    <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-6">
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-bold text-white flex items-center justify-center gap-2">
                <i class="fa-solid fa-user-plus text-blue-500"></i> Criar Conta
            </h1>
            <p class="text-slate-400 text-sm">Cadastre-se para reservar seus números da sorte.</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <div class="space-y-1">
                <label class="text-xs text-slate-400 font-semibold block">Nome Completo:</label>
                <input type="text" name="name" required placeholder="Seu nome completo" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
            </div>

            <div class="space-y-1">
                <label class="text-xs text-slate-400 font-semibold block">E-mail:</label>
                <input type="email" name="email" required placeholder="seuemail@exemplo.com" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-xs text-slate-400 font-semibold block">CPF:</label>
                    <input type="text" name="cpf" required placeholder="000.000.000-00" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-300 focus:outline-none focus:border-slate-700">
                </div>
                <div class="space-y-1">
                    <label class="text-xs text-slate-400 font-semibold block">Telefone / WhatsApp:</label>
                    <input type="text" name="phone" required placeholder="(11) 99999-9999" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-300 focus:outline-none focus:border-slate-700">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs text-slate-400 font-semibold block">Senha:</label>
                <input type="password" name="password" required placeholder="Mínimo 6 caracteres" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
            </div>

            <div class="space-y-1">
                <label class="text-xs text-slate-400 font-semibold block">Confirmar Senha:</label>
                <input type="password" name="password_confirmation" required placeholder="Repita sua senha" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 rounded-xl transition text-sm">
                Criar Minha Conta
            </button>
        </form>

        <div class="border-t border-slate-800/80 pt-4 text-center">
            <span class="text-xs text-slate-500">Já possui uma conta?</span>
            <a href="{{ route('login') }}" class="text-xs font-semibold text-blue-400 hover:text-blue-300 ml-1">Fazer Login</a>
        </div>
    </div>
</div>
@endsection
