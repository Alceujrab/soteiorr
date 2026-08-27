@extends('layouts.public')

@section('title', 'Escolher números - '.$package->name)

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="glass-card rounded-2xl border p-6 space-y-4" style="border-color: var(--border-color);">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.16em]" style="color: var(--accent);">Checkout</p>
            <h1 class="font-display text-2xl font-bold theme-title mt-1">{{ $package->name }} · {{ $package->numbers_qty }} números</h1>
            <p class="text-sm theme-muted mt-1">{{ $raffle->title }} · R$ {{ number_format($package->price, 2, ',', '.') }}</p>
        </div>

        @if($errors->any())
            <div class="rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('checkout.select.store') }}" class="space-y-3">
            @csrf
            <input type="hidden" name="mode" value="surprise">
            <button type="submit" class="w-full rounded-xl py-3.5 font-bold text-sm" style="background: var(--accent); color: var(--on-accent);">
                Surpresinha (números aleatórios)
            </button>
        </form>

        <div class="relative text-center text-xs theme-muted">
            <span class="px-2" style="background: var(--bg-card);">ou</span>
            <div class="absolute inset-x-0 top-1/2 border-t -z-10" style="border-color: var(--border-color);"></div>
        </div>

        <form method="POST" action="{{ route('checkout.select.store') }}" class="space-y-3">
            @csrf
            <input type="hidden" name="mode" value="manual">
            <label class="text-xs font-semibold theme-muted uppercase">Digite {{ $package->numbers_qty }} números livres (separados por vírgula)</label>
            <textarea name="numbers" rows="4" placeholder="Ex: 12, 45, 908, ..." class="w-full rounded-xl border bg-slate-950 px-3 py-3 text-sm theme-title" style="border-color: var(--border-color);" required></textarea>
            <p class="text-[11px] theme-muted">Somente números disponíveis entre 1 e {{ number_format($raffle->total_numbers, 0, ',', '.') }}.</p>
            <button type="submit" class="w-full rounded-xl border py-3.5 font-bold text-sm theme-title" style="border-color: var(--border-color);">
                Confirmar minha escolha
            </button>
        </form>
    </div>
</div>
@endsection
