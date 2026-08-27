@extends('layouts.admin')

@section('title', 'Sala de sorteio')

@section('content')
@php
    $isPending = $draw && $draw->status === 'pending';
    $isLive = $draw && $draw->status === 'live';
    $isDone = $draw && $draw->status === 'completed';
    $showCeremony = $isLive || $isDone;
@endphp
<div class="space-y-6" id="draw-admin-app"
     data-draw-id="{{ $showCeremony ? $draw->id : '' }}"
     data-state-url="{{ $showCeremony ? route('admin.draws.state', $draw) : '' }}"
     data-reveal-url="{{ $showCeremony ? route('admin.draws.reveal', $draw) : '' }}"
     data-auto-url="{{ $showCeremony ? route('admin.draws.auto', $draw) : '' }}"
     data-interval-ms="5000"
     data-digit-length="{{ $draw?->digit_length ?? 6 }}"
     data-auto-running="{{ $isLive && $draw?->auto_reveal_started_at ? '1' : '0' }}"
     data-revealed="{{ $draw?->revealed_digits ?? 0 }}">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <a href="{{ route('admin.draws.index') }}" class="text-xs font-semibold theme-muted hover:opacity-80">← Voltar</a>
            <h1 class="font-display text-2xl sm:text-3xl font-bold theme-title mt-2">{{ $raffle->title }}</h1>
            <p class="text-sm theme-muted mt-1">{{ $raffle->prize_name }} · {{ number_format($paidCount) }} bilhetes pagos · máx. {{ number_format($maxNumber, 0, ',', '.') }} números</p>
        </div>
        @if($draw?->public_slug)
            <a href="{{ route('draws.watch', $draw->public_slug) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border text-sm font-semibold theme-title" style="border-color: var(--border-color);">
                <i class="fa-solid fa-up-right-from-square"></i> Página pública
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="rounded-xl border px-4 py-3 text-sm theme-title" style="border-color: var(--border-color); background: color-mix(in srgb, var(--accent) 10%, transparent);">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">{{ $errors->first() }}</div>
    @endif

    @if($draw?->public_slug)
        <div class="glass-card rounded-2xl border p-4 space-y-2" style="border-color: var(--border-color);">
            <h3 class="text-sm font-bold theme-title">Página pública (sempre aberta)</h3>
            <p class="text-xs theme-muted">Compartilhe este link com os compradores antes do sorteio. Eles acompanham a espera, a live e o resultado no mesmo endereço.</p>
            <code class="block text-[11px] break-all theme-title p-2 rounded-lg border" style="border-color: var(--border-color); background: var(--bg-primary);">{{ route('draws.watch', $draw->public_slug) }}</code>
        </div>
    @endif

    @unless($showCeremony)
        <div class="glass-card rounded-2xl border p-6 space-y-4 max-w-2xl" style="border-color: var(--border-color);">
            <h2 class="font-display text-xl font-bold theme-title">Iniciar cerimônia</h2>
            <p class="text-sm theme-muted">O sistema escolhe aleatoriamente um bilhete <strong class="theme-title">pago</strong>. O número fica oculto até você revelar cada dígito. A página pública já está no ar aguardando.</p>
            <form method="POST" action="{{ route('admin.draws.start', $raffle) }}" class="space-y-4">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-xs font-bold uppercase tracking-wider theme-muted">URL da transmissão YouTube</label>
                    <input type="url" name="live_url" value="{{ old('live_url', $draw->live_url ?: ($raffle->live_url ?: $raffle->youtube_url)) }}" placeholder="https://www.youtube.com/watch?v=..." class="w-full rounded-xl border px-4 py-3 text-sm theme-title" style="border-color: var(--border-color); background: var(--bg-primary);">
                </div>
                <button type="submit" class="px-5 py-3 rounded-xl text-sm font-bold" style="background: var(--accent); color: var(--on-accent);" @disabled($paidCount < 1)>
                    Iniciar sorteio ao vivo
                </button>
                @if($paidCount < 1)
                    <p class="text-xs text-red-300">É necessário ter pelo menos 1 bilhete pago.</p>
                @endif
            </form>
        </div>
    @else
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
            <div class="xl:col-span-8 space-y-5">
                <div class="glass-card rounded-2xl border p-6 sm:p-8 space-y-6" style="border-color: var(--border-color);">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.18em]" style="color: var(--accent);">Painel do apresentador</p>
                            <h2 class="font-display text-xl font-bold theme-title">Revelação dos 6 dígitos</h2>
                        </div>
                        <span id="draw-status-badge" class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg border" style="border-color: var(--border-color); color: var(--text-secondary);">{{ $draw->status }}</span>
                    </div>

                    <x-draw-reels id-prefix="admin-draw" :first-digit-max="$firstDigitMax" />

                    <div class="flex flex-wrap gap-3 items-center">
                        @if($isLive)
                            <button type="button" id="btn-sortear" class="px-6 py-3.5 rounded-xl text-sm font-bold tracking-wide" style="background: var(--accent); color: var(--on-accent);" @disabled($draw->auto_reveal_started_at)>
                                {{ $draw->auto_reveal_started_at ? 'Sorteando números...' : 'Sortear números' }}
                            </button>
                        @endif
                        @if($isLive || $isDone)
                            <form method="POST" action="{{ route('admin.draws.cancel', $draw) }}" onsubmit="return confirm('Cancelar este sorteio? Você poderá iniciar novamente para ajustar e testar.');">
                                @csrf
                                <button type="submit" class="px-5 py-3.5 rounded-xl text-sm font-bold border" style="border-color: #ef4444; color: #fca5a5;">
                                    Cancelar sorteio
                                </button>
                            </form>
                        @endif
                        <div class="text-sm theme-muted self-center">
                            Revelados: <strong class="theme-title" id="revealed-count">{{ $draw->revealed_digits }}</strong>/{{ $draw->digit_length }}
                        </div>
                    </div>
                    <p class="text-xs theme-muted" id="sortear-help">
                        Ao clicar em <strong class="theme-title">Sortear números</strong>, todos os dígitos giram e param em sequência a cada 5 segundos. Use <strong class="theme-title">Cancelar sorteio</strong> enquanto estiver testando.
                    </p>
                </div>

                <div id="winner-admin-card" class="glass-card rounded-2xl border p-6 space-y-4 {{ $isDone ? '' : 'hidden' }}" style="border-color: var(--border-color);">
                    <h3 class="font-display text-lg font-bold theme-title">Ganhador (dados completos — admin)</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm" id="winner-admin-fields"></div>
                </div>

                @if($isDone && ! $draw->is_test)
                    @php
                        $checklist = $draw->ops_checklist ?: [
                            'contact_winner' => false,
                            'publish_minutes' => false,
                            'deliver_prize' => false,
                            'archive_recording' => false,
                        ];
                        $labels = [
                            'contact_winner' => 'Contatar ganhador (WhatsApp/e-mail)',
                            'publish_minutes' => 'Publicar/compartilhar ata pública',
                            'deliver_prize' => 'Agendar entrega do prêmio',
                            'archive_recording' => 'Arquivar gravação do YouTube',
                        ];
                    @endphp
                    <div class="glass-card rounded-2xl border p-6 space-y-4" style="border-color: var(--border-color);">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h3 class="font-display text-lg font-bold theme-title">Checklist pós-sorteio</h3>
                            <a href="{{ route('draws.minutes', $draw->public_slug) }}" target="_blank" class="text-xs font-semibold underline" style="color: var(--badge-text);">Abrir ata pública</a>
                        </div>
                        <form method="POST" action="{{ route('admin.draws.checklist', $draw) }}" class="space-y-3">
                            @csrf
                            @foreach($labels as $key => $label)
                                <label class="flex items-center gap-3 text-sm theme-title cursor-pointer">
                                    <input type="hidden" name="checklist[{{ $key }}]" value="0">
                                    <input type="checkbox" name="checklist[{{ $key }}]" value="1" {{ !empty($checklist[$key]) ? 'checked' : '' }} class="rounded border-slate-600 bg-slate-900 text-red-600 focus:ring-0">
                                    {{ $label }}
                                </label>
                            @endforeach
                            <button type="submit" class="mt-2 inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white" style="background: var(--accent);">
                                Salvar checklist
                            </button>
                        </form>
                        @if($draw->eligible_hash)
                            <div class="pt-3 border-t text-xs theme-muted space-y-1" style="border-color: var(--border-color);">
                                <p>Prova: {{ $draw->eligible_count }} elegíveis · índice {{ $draw->selection_index }}</p>
                                <p class="font-mono break-all">Hash {{ $draw->eligible_hash }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <aside class="xl:col-span-4 space-y-4">
                <div class="glass-card rounded-2xl border p-4 space-y-3" style="border-color: var(--border-color);">
                    <h3 class="text-sm font-bold theme-title">Transmissão</h3>
                    @if($draw->live_url)
                        <a href="{{ $draw->live_url }}" target="_blank" class="text-xs break-all underline" style="color: var(--badge-text);">{{ $draw->live_url }}</a>
                    @else
                        <p class="text-xs theme-muted">Nenhuma URL de YouTube informada.</p>
                    @endif
                </div>
                @if($draw->eligible_hash && in_array($draw->status, ['live', 'completed'], true))
                <div class="glass-card rounded-2xl border p-4 space-y-2" style="border-color: var(--border-color);">
                    <h3 class="text-sm font-bold theme-title">Prova criptográfica</h3>
                    <p class="text-[11px] theme-muted">{{ number_format((int) $draw->eligible_count, 0, ',', '.') }} bilhetes pagos congelados no início.</p>
                    <p class="text-[10px] font-mono break-all theme-title">{{ $draw->eligible_hash }}</p>
                    <a href="{{ route('draws.minutes', $draw->public_slug) }}" target="_blank" class="text-xs font-semibold underline" style="color: var(--badge-text);">Ver ata / auditoria</a>
                </div>
                @endif
                <div class="glass-card rounded-2xl border p-4 space-y-2" style="border-color: var(--border-color);">
                    <h3 class="text-sm font-bold theme-title">Número sorteado (admin)</h3>
                    <p class="font-display text-3xl font-bold tracking-widest" style="color: var(--accent);" id="admin-secret-number">
                        {{ $isDone ? $draw->winning_number_padded : '••••••' }}
                    </p>
                    <p class="text-[11px] theme-muted">Definido no início por seed + lista elegível. A seed só é revelada ao público após a conclusão.</p>
                </div>
            </aside>
        </div>
    @endunless
</div>

@include('partials.draw-ceremony-js')
<script>
(() => {
    const app = document.getElementById('draw-admin-app');
    if (!app || !app.dataset.drawId) return;

    const reels = document.getElementById('admin-draw-reels');
    const sortearBtn = document.getElementById('btn-sortear');
    const revealedCount = document.getElementById('revealed-count');
    const statusBadge = document.getElementById('draw-status-badge');
    const winnerCard = document.getElementById('winner-admin-card');
    const winnerFields = document.getElementById('winner-admin-fields');
    const secretNumber = document.getElementById('admin-secret-number');
    const help = document.getElementById('sortear-help');
    let revealedSoFar = Number(app.dataset.revealed || 0);
    let lastRevealed = revealedSoFar;
    let autoRunning = app.dataset.autoRunning === '1';

    function csrf() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    function renderWinner(full) {
        if (!full) return;
        winnerCard.classList.remove('hidden');
        const rows = [
            ['Nome', full.name],
            ['Número', full.number_padded || full.number],
            ['Compra', full.purchased_at],
            ['Telefone', full.phone || full.whatsapp],
            ['E-mail', full.email],
            ['CPF', full.cpf],
            ['Endereço', full.address],
        ];
        winnerFields.innerHTML = rows.map(([l,v]) => `
            <div class="rounded-xl border p-3" style="border-color: var(--border-color); background: var(--bg-primary);">
                <div class="text-[10px] uppercase font-bold theme-muted">${l}</div>
                <div class="theme-title font-semibold mt-1 break-words">${v || '—'}</div>
            </div>
        `).join('');
    }

    function apply(state) {
        revealedSoFar = Number(state.revealed_digits || 0);
        if (revealedCount) revealedCount.textContent = revealedSoFar;
        if (statusBadge) statusBadge.textContent = state.status_label || state.status;
        if (secretNumber && state.status === 'completed') {
            secretNumber.textContent = state.winning_number_padded;
        }
        if (revealedSoFar !== lastRevealed || state.auto_running) {
            DrawCeremonyUI.applyState(reels, state, { animate: revealedSoFar > lastRevealed });
            lastRevealed = revealedSoFar;
        }
        if (state.winner_full) renderWinner(state.winner_full);
        if (sortearBtn) {
            if (state.status !== 'live' || revealedSoFar >= state.digit_length) {
                sortearBtn.disabled = true;
                sortearBtn.classList.add('opacity-50');
                sortearBtn.textContent = state.status === 'completed' ? 'Sorteio concluído' : 'Sortear números';
            } else if (state.auto_running || autoRunning) {
                sortearBtn.disabled = true;
                sortearBtn.textContent = 'Sorteando números...';
                if (help) help.textContent = 'Revelação automática em andamento — um dígito a cada 5 segundos.';
            }
        }
    }

    async function poll() {
        if (autoRunning) return;
        try {
            const res = await fetch(app.dataset.stateUrl, { headers: { 'Accept': 'application/json' }});
            if (res.ok) apply(await res.json());
        } catch (e) {}
    }

    async function beginAutoReveal(resume = false) {
        if (autoRunning && !resume) return;
        autoRunning = true;
        if (sortearBtn) {
            sortearBtn.disabled = true;
            sortearBtn.textContent = 'Sorteando números...';
        }
        if (help) help.textContent = 'Revelação automática em andamento — um dígito a cada 5 segundos.';

        try {
            await DrawCeremonyUI.runAutoReveal({
                startUrl: resume ? null : app.dataset.autoUrl,
                revealUrl: app.dataset.revealUrl,
                csrfToken: csrf(),
                reelsRoot: reels,
                intervalMs: Number(app.dataset.intervalMs || 5000),
                digitLength: Number(app.dataset.digitLength || 6),
                initialRevealed: revealedSoFar,
                onState: apply,
            });
        } catch (e) {
            autoRunning = false;
            if (sortearBtn && revealedSoFar < Number(app.dataset.digitLength || 6)) {
                sortearBtn.disabled = false;
                sortearBtn.textContent = 'Sortear números';
            }
            alert(e.message || 'Falha na revelação automática.');
            return;
        }

        autoRunning = false;
    }

    if (sortearBtn) {
        sortearBtn.addEventListener('click', () => beginAutoReveal(false));
    }

    if (autoRunning && revealedSoFar < Number(app.dataset.digitLength || 6)) {
        beginAutoReveal(true);
    } else {
        poll();
    }

    setInterval(poll, 2000);
})();
</script>
@endsection
