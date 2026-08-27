@extends('layouts.admin')

@section('title', 'Sorteio teste')

@section('content')
<div class="space-y-6" id="draw-test-app"
     @if($draw)
     data-draw-id="{{ $draw->id }}"
     data-state-url="{{ route('admin.draws.state', $draw) }}"
     data-reveal-url="{{ route('admin.draws.reveal', $draw) }}"
     data-auto-url="{{ route('admin.draws.auto', $draw) }}"
     data-interval-ms="5000"
     data-digit-length="{{ $draw->digit_length }}"
     data-auto-running="{{ $draw->status === 'live' && $draw->auto_reveal_started_at ? '1' : '0' }}"
     data-revealed="{{ $draw->revealed_digits }}"
     @endif
>
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.draws.index') }}" class="text-xs font-semibold theme-muted">← Sorteios</a>
            <h1 class="font-display text-2xl sm:text-3xl font-bold theme-title mt-2">Sorteio teste</h1>
            <p class="text-sm theme-muted mt-1">Simulação completa com ganhador fictício. Não altera bilhetes nem encerra ações reais.</p>
        </div>
        <a href="{{ route('admin.settings') }}" class="text-sm font-semibold theme-muted">Configurações</a>
    </div>

    <div class="rounded-xl border px-4 py-3 text-sm" style="border-color: color-mix(in srgb, #f59e0b 40%, var(--border-color)); background: color-mix(in srgb, #f59e0b 12%, transparent); color: var(--text-primary);">
        <i class="fa-solid fa-triangle-exclamation mr-1" style="color:#f59e0b;"></i>
        Modo demonstração — use antes da live real para validar animação, YouTube e tela do ganhador.
    </div>

    @if(session('success'))
        <div class="rounded-xl border px-4 py-3 text-sm theme-title" style="border-color: var(--border-color);">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-4 glass-card rounded-2xl border p-5 space-y-4 h-fit" style="border-color: var(--border-color);">
            <h2 class="font-display text-lg font-bold theme-title">Nova simulação</h2>
            <form method="POST" action="{{ route('admin.draws.test.start') }}" class="space-y-3">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-xs font-bold uppercase theme-muted">YouTube (opcional)</label>
                    <input type="url" name="live_url" value="{{ old('live_url') }}" class="w-full rounded-xl border px-3 py-2.5 text-sm theme-title" style="border-color: var(--border-color); background: var(--bg-primary);" placeholder="https://youtube.com/watch?v=...">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold uppercase theme-muted">Forçar número (1–{{ number_format($maxNumber, 0, ',', '.') }}, opcional)</label>
                    <input type="number" name="forced_number" min="1" max="{{ $maxNumber }}" value="{{ old('forced_number') }}" class="w-full rounded-xl border px-3 py-2.5 text-sm theme-title" style="border-color: var(--border-color); background: var(--bg-primary);" placeholder="Ex: {{ min(123456, $maxNumber) }}">
                    <p class="text-[11px] theme-muted">
                        Limitado aos bilhetes da ação
                        @if($raffle)
                            <strong class="theme-title">{{ $raffle->title }}</strong>
                        @endif
                        (máx. {{ number_format($maxNumber, 0, ',', '.') }}). 1º dígito: 0–{{ $firstDigitMax }}.
                    </p>
                </div>
                <button type="submit" class="w-full py-3 rounded-xl text-sm font-bold" style="background: var(--accent); color: var(--on-accent);">Iniciar teste</button>
            </form>
        </div>

        <div class="xl:col-span-8 space-y-5">
            @if($draw)
                <div class="glass-card rounded-2xl border p-6 space-y-5" style="border-color: var(--border-color);">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.16em]" style="color:#f59e0b;">TESTE</p>
                            <h2 class="font-display text-xl font-bold theme-title">Cerimônia simulada</h2>
                        </div>
                        @if($draw->public_slug)
                            <a href="{{ route('draws.watch', $draw->public_slug) }}" target="_blank" class="text-sm font-semibold underline" style="color: var(--badge-text);">Abrir visão pública</a>
                        @endif
                    </div>

                    <x-draw-reels id-prefix="test-draw" :first-digit-max="$firstDigitMax" />

                    @if(in_array($draw->status, ['live', 'completed'], true))
                        <div class="flex flex-wrap gap-3 items-center">
                            @if($draw->status === 'live')
                                <button type="button" id="btn-sortear" class="px-6 py-3.5 rounded-xl text-sm font-bold" style="background: var(--accent); color: var(--on-accent);" @disabled($draw->auto_reveal_started_at)>
                                    {{ $draw->auto_reveal_started_at ? 'Sorteando números...' : 'Sortear números' }}
                                </button>
                            @endif
                            <form method="POST" action="{{ route('admin.draws.cancel', $draw) }}" onsubmit="return confirm('Cancelar este sorteio de teste?');">
                                @csrf
                                <button type="submit" class="px-5 py-3.5 rounded-xl text-sm font-bold border" style="border-color: #ef4444; color: #fca5a5;">
                                    Cancelar sorteio
                                </button>
                            </form>
                        </div>
                        <p class="text-xs theme-muted">Todos os dígitos giram e param em sequência a cada 5 segundos. Cancele para ajustar e testar de novo.</p>
                    @endif

                    <div id="winner-admin-card" class="{{ $draw->status === 'completed' ? '' : 'hidden' }} space-y-3 pt-2">
                        <h3 class="font-display text-lg font-bold theme-title">Ganhador fictício (completo)</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm" id="winner-admin-fields"></div>
                    </div>
                </div>
            @else
                <div class="glass-card rounded-2xl border p-10 text-center theme-muted" style="border-color: var(--border-color);">
                    Inicie um teste ao lado para validar a animação dos 6 dígitos.
                </div>
            @endif
        </div>
    </div>
</div>

@include('partials.draw-ceremony-js')
<script>
(() => {
    const app = document.getElementById('draw-test-app');
    if (!app?.dataset.drawId) return;
    const reels = document.getElementById('test-draw-reels');
    const sortearBtn = document.getElementById('btn-sortear');
    const winnerCard = document.getElementById('winner-admin-card');
    const winnerFields = document.getElementById('winner-admin-fields');
    let revealedSoFar = Number(app.dataset.revealed || 0);
    let lastRevealed = revealedSoFar;
    let autoRunning = app.dataset.autoRunning === '1';

    function renderWinner(full) {
        if (!full) return;
        winnerCard.classList.remove('hidden');
        const rows = [['Nome', full.name],['Número', full.number_padded],['Compra', full.purchased_at],['Telefone', full.phone],['Endereço', full.address],['E-mail', full.email]];
        winnerFields.innerHTML = rows.map(([l,v]) => `<div class="rounded-xl border p-3" style="border-color:var(--border-color)"><div class="text-[10px] uppercase font-bold theme-muted">${l}</div><div class="theme-title font-semibold mt-1">${v||'—'}</div></div>`).join('');
    }

    function apply(state) {
        revealedSoFar = Number(state.revealed_digits || 0);
        if (revealedSoFar !== lastRevealed || state.auto_running) {
            DrawCeremonyUI.applyState(reels, state, { animate: revealedSoFar > lastRevealed });
            lastRevealed = revealedSoFar;
        }
        if (state.status === 'completed') {
            renderWinner(state.winner_full);
            if (sortearBtn) {
                sortearBtn.disabled = true;
                sortearBtn.textContent = 'Sorteio concluído';
            }
        } else if ((state.auto_running || autoRunning) && sortearBtn) {
            sortearBtn.disabled = true;
            sortearBtn.textContent = 'Sorteando números...';
        }
    }

    async function fetchState() {
        if (autoRunning) return;
        const res = await fetch(app.dataset.stateUrl, { headers: { 'Accept': 'application/json' }});
        if (res.ok) apply(await res.json());
    }

    async function beginAutoReveal(resume = false) {
        if (autoRunning && !resume) return;
        autoRunning = true;
        if (sortearBtn) {
            sortearBtn.disabled = true;
            sortearBtn.textContent = 'Sorteando números...';
        }
        try {
            await DrawCeremonyUI.runAutoReveal({
                startUrl: resume ? null : app.dataset.autoUrl,
                revealUrl: app.dataset.revealUrl,
                csrfToken: '{{ csrf_token() }}',
                reelsRoot: reels,
                intervalMs: Number(app.dataset.intervalMs || 5000),
                digitLength: Number(app.dataset.digitLength || 6),
                initialRevealed: revealedSoFar,
                onState: apply,
            });
        } catch (e) {
            autoRunning = false;
            if (sortearBtn) {
                sortearBtn.disabled = false;
                sortearBtn.textContent = 'Sortear números';
            }
            alert(e.message || 'Erro no sorteio automático');
            return;
        }
        autoRunning = false;
    }

    sortearBtn?.addEventListener('click', () => beginAutoReveal(false));

    if (autoRunning && revealedSoFar < Number(app.dataset.digitLength || 6)) {
        beginAutoReveal(true);
    } else {
        fetchState();
    }
    setInterval(fetchState, 2000);
})();
</script>
@endsection
