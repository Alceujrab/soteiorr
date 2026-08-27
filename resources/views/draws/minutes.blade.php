@extends('layouts.public')

@section('title', 'Ata do sorteio - '.($draw->raffle?->title ?? 'RR Veículos'))

@section('content')
<div class="max-w-3xl mx-auto space-y-6 pb-10">
    <header class="rounded-2xl border px-6 py-8 space-y-2" style="border-color: var(--border-color); background: var(--bg-card);">
        <p class="text-[11px] font-bold uppercase tracking-[0.2em]" style="color: var(--accent);">Transparência</p>
        <h1 class="font-display text-3xl font-bold theme-title">Ata pública do sorteio</h1>
        <p class="text-sm theme-muted">{{ $draw->raffle?->title }} · {{ $draw->raffle?->prize_name }}</p>
    </header>

    <div class="glass-card rounded-2xl border p-6 space-y-4" style="border-color: var(--border-color);">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div>
                <div class="text-[10px] uppercase font-bold theme-muted">Status</div>
                <div class="theme-title font-semibold mt-1">{{ $draw->status === 'completed' ? 'Finalizado' : 'Em andamento' }}</div>
            </div>
            <div>
                <div class="text-[10px] uppercase font-bold theme-muted">Bilhetes elegíveis</div>
                <div class="theme-title font-semibold mt-1">{{ number_format((int) ($proof['eligible_count'] ?? 0), 0, ',', '.') }}</div>
            </div>
            <div class="sm:col-span-2">
                <div class="text-[10px] uppercase font-bold theme-muted">Hash da lista (SHA-256)</div>
                <div class="theme-title font-mono text-xs mt-1 break-all">{{ $proof['eligible_hash'] ?? '—' }}</div>
            </div>
            @if($proof['seed_revealed'] ?? false)
                <div class="sm:col-span-2">
                    <div class="text-[10px] uppercase font-bold theme-muted">Seed revelada</div>
                    <div class="theme-title font-mono text-xs mt-1 break-all">{{ $proof['draw_seed'] }}</div>
                </div>
                <div>
                    <div class="text-[10px] uppercase font-bold theme-muted">Índice selecionado</div>
                    <div class="theme-title font-semibold mt-1">{{ $proof['selection_index'] }}</div>
                </div>
                <div>
                    <div class="text-[10px] uppercase font-bold theme-muted">Número contemplado</div>
                    <div class="theme-title font-semibold mt-1 tracking-widest">{{ $proof['winning_number_padded'] }}</div>
                </div>
            @else
                <div class="sm:col-span-2 text-sm theme-muted">
                    A seed é revelada automaticamente quando a cerimônia termina, para que qualquer pessoa possa auditar o resultado.
                </div>
            @endif
        </div>

        @if($verified === true)
            <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                Verificação automática: a seed + lista elegível reproduzem o número contemplado.
            </div>
        @elseif($verified === false)
            <div class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                A verificação automática falhou. Contate o suporte.
            </div>
        @endif

        <div class="text-xs theme-muted space-y-1">
            <p>Algoritmo: <code class="theme-title">{{ $proof['algorithm'] }}</code></p>
            <p>1. Ordene os números pagos · 2. SHA-256 da lista · 3. SHA-256(seed|hash) · 4. Índice = primeiros 8 hex % N.</p>
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
            <a href="{{ route('draws.watch', $draw->public_slug) }}" class="inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold theme-title" style="border-color: var(--border-color);">
                Voltar ao sorteio
            </a>
            @if($draw->status === 'completed')
                <a href="{{ route('draws.minutes.pdf', $draw->public_slug) }}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white" style="background: var(--accent);">
                    Baixar ata PDF
                </a>
                <a href="{{ route('draws.eligible', $draw->public_slug) }}" class="inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold theme-title" style="border-color: var(--border-color);">
                    Lista elegível (JSON)
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
