@extends('layouts.public')

@section('title', ($draw->is_test ? 'Sorteio teste' : 'Sorteio ao vivo').' - RR Veículos')

@section('content')
@php
    $statusLabel = $state['status_label'] ?? $state['status'];
@endphp
<div class="draw-watch space-y-6 pb-10" id="draw-public-app"
     data-state-url="{{ route('draws.state', $draw->public_slug) }}">
    <header class="relative overflow-hidden rounded-2xl border px-6 py-8 sm:px-10" style="border-color: var(--border-color); background: var(--bg-card);">
        <div class="absolute inset-y-0 left-0 w-1.5" style="background: var(--accent);"></div>
        <div class="relative space-y-2">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em]" style="color: var(--accent);">
                {{ $draw->is_test ? 'Demonstração' : 'Transmissão ao vivo' }}
            </p>
            <h1 class="font-display text-3xl sm:text-4xl font-bold theme-title tracking-tight">
                {{ $draw->raffle?->title ?? 'Sorteio RR Veículos' }}
            </h1>
            <p class="text-sm theme-muted max-w-2xl">
                Acompanhe a revelação número a número. O ganhador aparece automaticamente ao fechar os 6 dígitos.
            </p>
        </div>
    </header>

    <div id="pending-banner" class="rounded-2xl border px-5 py-4 text-sm theme-title {{ ($state['status'] ?? '') === 'pending' ? '' : 'hidden' }}" style="border-color: var(--border-color); background: color-mix(in srgb, var(--accent) 12%, transparent);">
        <i class="fa-solid fa-clock mr-2" style="color: var(--accent);"></i>
        O sorteio ainda não começou. Esta página permanece aberta — a transmissão e os dígitos aparecem assim que a cerimônia iniciar.
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        <div class="xl:col-span-7 space-y-4">
            <div class="rounded-2xl border overflow-hidden" style="border-color: var(--border-color); background: #000;">
                @if($embedUrl)
                    <div class="relative w-full aspect-video">
                        <iframe class="absolute inset-0 w-full h-full" src="{{ $embedUrl }}" title="Transmissão ao vivo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    </div>
                @else
                    <div class="aspect-video flex items-center justify-center text-sm theme-muted p-8 text-center" style="background: var(--bg-card);">
                        Aguardando link da transmissão YouTube.
                    </div>
                @endif
            </div>
            <div class="glass-card rounded-2xl border p-4 flex flex-wrap gap-4 text-xs theme-muted" style="border-color: var(--border-color);">
                <span><i class="fa-solid fa-gift mr-1" style="color: var(--accent);"></i> {{ $draw->raffle?->prize_name ?? 'Prêmio da ação' }}</span>
                <span id="live-status-label"><i class="fa-solid fa-signal mr-1" style="color: var(--accent);"></i> Status: {{ $statusLabel }}</span>
            </div>
        </div>

        <div class="xl:col-span-5 space-y-4">
            <div class="glass-card rounded-2xl border p-5 sm:p-6 space-y-5" style="border-color: var(--border-color);">
                <div class="text-center space-y-1">
                    <p class="text-[11px] font-bold uppercase tracking-[0.18em]" style="color: var(--accent);">Número sorteado</p>
                    <h2 class="font-display text-xl font-bold theme-title">Revelação ao vivo</h2>
                </div>

                <x-draw-reels id-prefix="public-draw" :first-digit-max="$firstDigitMax" />

                <p class="text-center text-xs theme-muted" id="reveal-progress">
                    Dígitos revelados: {{ $state['revealed_digits'] }}/{{ $state['digit_length'] }}
                </p>
            </div>

            <div id="winner-public-card" class="glass-card rounded-2xl border p-5 sm:p-6 space-y-4 {{ ($state['winner'] ?? null) ? '' : 'hidden' }}" style="border-color: var(--border-color);">
                <div class="text-center space-y-1">
                    <p class="text-[11px] font-bold uppercase tracking-[0.18em]" style="color: var(--accent);">Resultado</p>
                    <h3 class="font-display text-2xl font-bold theme-title">Temos um ganhador!</h3>
                </div>
                <div class="space-y-3" id="winner-public-fields"></div>
                @unless($draw->is_test)
                <a href="{{ route('draws.minutes', $draw->public_slug) }}" class="inline-flex w-full justify-center items-center gap-2 rounded-xl border px-4 py-3 text-sm font-semibold theme-title" style="border-color: var(--border-color);">
                    <i class="fa-solid fa-file-shield" style="color: var(--accent);"></i> Ver ata e prova do sorteio
                </a>
                @endunless
            </div>

            @unless($draw->is_test)
            @if(!empty($state['proof']['eligible_hash']))
            <div class="glass-card rounded-2xl border p-5 space-y-3" style="border-color: var(--border-color);">
                <h3 class="text-sm font-bold theme-title">Transparência</h3>
                <p class="text-xs theme-muted">
                    {{ number_format((int) ($state['proof']['eligible_count'] ?? 0), 0, ',', '.') }} bilhete(s) elegível(is).
                    Hash: <span class="font-mono break-all theme-title">{{ \Illuminate\Support\Str::limit($state['proof']['eligible_hash'] ?? '—', 24) }}</span>
                </p>
                <a href="{{ route('draws.minutes', $draw->public_slug) }}" class="text-xs font-semibold underline" style="color: var(--badge-text);">
                    Abrir página de auditoria pública
                </a>
            </div>
            @endif
            @endunless
        </div>
    </div>
</div>

@include('partials.draw-ceremony-js')
<script>
(() => {
    const app = document.getElementById('draw-public-app');
    const reels = document.getElementById('public-draw-reels');
    const progress = document.getElementById('reveal-progress');
    const statusLabel = document.getElementById('live-status-label');
    const pendingBanner = document.getElementById('pending-banner');
    const winnerCard = document.getElementById('winner-public-card');
    const winnerFields = document.getElementById('winner-public-fields');
    let lastRevealed = Number(@json($state['revealed_digits'] ?? 0));
    let autoSpinning = false;

    function renderWinner(winner) {
        if (!winner) return;
        winnerCard.classList.remove('hidden');
        const rows = [
            ['Ganhador', winner.name],
            ['Número da sorte', winner.number_padded || winner.number],
            ['Data/hora da compra', winner.purchased_at],
            ['Telefone', winner.phone],
            ['Endereço', winner.address],
        ];
        winnerFields.innerHTML = rows.map(([l,v]) => `
            <div class="rounded-xl border p-3" style="border-color: var(--border-color); background: var(--bg-primary);">
                <div class="text-[10px] uppercase font-bold theme-muted">${l}</div>
                <div class="theme-title font-semibold mt-1 text-sm sm:text-base break-words">${v || '—'}</div>
            </div>
        `).join('');
    }

    function apply(state) {
        if (progress) progress.textContent = `Dígitos revelados: ${state.revealed_digits}/${state.digit_length}`;
        const label = state.status_label || state.status;
        if (statusLabel) statusLabel.innerHTML = `<i class="fa-solid fa-signal mr-1" style="color: var(--accent);"></i> Status: ${label}`;
        if (pendingBanner) pendingBanner.classList.toggle('hidden', state.status !== 'pending');

        if (state.auto_running && !autoSpinning) {
            autoSpinning = true;
            DrawCeremonyUI.spinUnrevealed(reels, state.revealed_digits);
        }

        if (state.revealed_digits !== lastRevealed || state.auto_running) {
            DrawCeremonyUI.applyState(reels, state, { animate: state.revealed_digits > lastRevealed });
            lastRevealed = state.revealed_digits;
        }
        if (state.winner) renderWinner(state.winner);
        if (state.status === 'completed') autoSpinning = false;
    }

    apply(@json($state));

    async function poll() {
        try {
            const res = await fetch(app.dataset.stateUrl, { headers: { 'Accept': 'application/json' }});
            if (res.ok) apply(await res.json());
        } catch (e) {}
    }

    setInterval(poll, 1200);
})();
</script>
@endsection
