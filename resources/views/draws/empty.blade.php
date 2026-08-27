@extends('layouts.public')

@section('title', 'Sorteio - RR Veículos')

@section('content')
<div class="max-w-2xl mx-auto py-16 text-center space-y-4">
    <p class="text-[11px] font-bold uppercase tracking-[0.2em]" style="color: var(--accent);">Sorteio ao vivo</p>
    <h1 class="font-display text-3xl font-bold theme-title">Nenhuma ação disponível</h1>
    <p class="text-sm theme-muted">Assim que uma ação promocional estiver ativa, a página pública do sorteio ficará aqui.</p>
    <a href="{{ route('raffles.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-bold" style="background: var(--accent); color: var(--on-accent);">
        Voltar ao início
    </a>
</div>
@endsection
