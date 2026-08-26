@extends('layouts.public')

@section('title', 'Recuperar senha - Ação RR Veículos')

@section('content')
<div class="max-w-md mx-auto space-y-6 pt-10">
    <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-6">
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-bold text-white flex items-center justify-center gap-2">
                <i class="fa-solid fa-key" style="color: var(--accent);"></i> Recuperar senha
            </h1>
            <p class="text-slate-400 text-sm">Informe o e-mail cadastrado para receber o link de redefinição.</p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-1">
                <label class="text-xs text-slate-400 font-semibold block">E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
            </div>
            <button type="submit" class="w-full text-white font-semibold py-3 rounded-xl transition text-sm" style="background-color: var(--accent);">
                Enviar link
            </button>
        </form>

        <div class="border-t border-slate-800/80 pt-4 text-center">
            <a href="{{ route('login') }}" class="text-xs font-semibold hover:opacity-80" style="color: var(--accent);">Voltar ao login</a>
        </div>
    </div>
</div>
@endsection
