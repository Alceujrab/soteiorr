@extends('layouts.admin')

@section('title', 'Confirmar exclusão - Ação RR Veículos')

@section('content')
<div class="max-w-lg mx-auto space-y-6">
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.dashboard') }}" class="text-slate-400 hover:text-white transition">
            <i class="fa-solid fa-chevron-left"></i> Voltar
        </a>
    </div>

    <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-6">
        <div class="space-y-2">
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-red-400"></i> Confirmar exclusão
            </h1>
            <p class="text-slate-400 text-sm">
                Para excluir a Ação Promocional <strong class="text-white">{{ $raffle->title }}</strong>,
                informe o código de 6 dígitos enviado para <strong class="text-white">{{ $maskedEmail }}</strong>.
            </p>
            <p class="text-xs text-slate-500">
                O código expira em {{ $expiresInMinutes }} minutos. Esta exclusão remove bilhetes, pacotes e registros vinculados.
            </p>
        </div>

        <form action="{{ route('admin.raffles.destroy.confirm.submit', $raffle) }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-300">Código de confirmação:</label>
                <input
                    type="text"
                    name="code"
                    inputmode="numeric"
                    pattern="[0-9]{6}"
                    maxlength="6"
                    minlength="6"
                    required
                    autocomplete="one-time-code"
                    placeholder="000000"
                    class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-center text-2xl tracking-[0.35em] font-bold text-white focus:outline-none focus:border-red-500/50"
                >
                @error('code')
                    <p class="text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-red-600 hover:bg-red-500 text-white font-bold py-3 rounded-xl transition">
                Confirmar exclusão definitiva
            </button>
        </form>

        <form action="{{ route('admin.raffles.destroy.resend', $raffle) }}" method="POST" class="pt-2 border-t" style="border-color: var(--border-color);">
            @csrf
            <button type="submit" class="w-full text-sm text-slate-400 hover:text-white transition py-2">
                Reenviar código por e-mail
            </button>
        </form>
    </div>
</div>
@endsection
