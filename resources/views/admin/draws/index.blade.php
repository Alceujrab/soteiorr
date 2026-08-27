@extends('layouts.admin')

@section('title', 'Sorteio ao vivo')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.18em]" style="color: var(--accent);">Cerimônia</p>
            <h1 class="font-display text-2xl sm:text-3xl font-bold theme-title mt-1">Sorteio ao vivo</h1>
            <p class="text-sm theme-muted mt-1">Controle a revelação dígito a dígito e acompanhe a transmissão no site público.</p>
        </div>
        <a href="{{ route('admin.draws.test') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border text-sm font-semibold theme-title" style="border-color: var(--border-color);">
            <i class="fa-solid fa-flask" style="color: var(--accent);"></i> Sorteio teste
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border px-4 py-3 text-sm" style="border-color: color-mix(in srgb, var(--accent) 40%, var(--border-color)); background: color-mix(in srgb, var(--accent) 10%, transparent); color: var(--text-primary);">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        <div class="xl:col-span-2 space-y-3">
            @foreach($raffles as $raffle)
                @php $draw = $raffle->draw; @endphp
                <div class="glass-card rounded-2xl border p-5 flex flex-col sm:flex-row sm:items-center gap-4" style="border-color: var(--border-color);">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h2 class="font-display text-lg font-bold theme-title truncate">{{ $raffle->title }}</h2>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-lg border theme-muted" style="border-color: var(--border-color);">{{ $raffle->status }}</span>
                        </div>
                        <p class="text-sm theme-muted">{{ $raffle->prize_name }} · {{ number_format($raffle->paid_tickets_count) }} bilhetes pagos</p>
                        @if($draw)
                            <p class="text-xs mt-1" style="color: var(--accent);">
                                Sorteio {{ $draw->status }}
                                @if($draw->public_slug)
                                    · <a class="underline" href="{{ route('draws.watch', $draw->public_slug) }}" target="_blank">abrir página pública</a>
                                @endif
                            </p>
                        @endif
                    </div>
                    <a href="{{ route('admin.draws.room', $raffle) }}" class="shrink-0 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold" style="background: var(--accent); color: var(--on-accent);">
                        <i class="fa-solid fa-clover"></i> Sala de sorteio
                    </a>
                </div>
            @endforeach
        </div>

        <aside class="glass-card rounded-2xl border p-5 space-y-3 h-fit" style="border-color: var(--border-color);">
            <h3 class="font-display text-base font-bold theme-title">Últimos sorteios</h3>
            @forelse($liveDraws as $item)
                <div class="rounded-xl border p-3" style="border-color: var(--border-color);">
                    <div class="text-sm font-semibold theme-title">{{ $item->raffle?->title ?? 'Ação #'.$item->raffle_id }}</div>
                    <div class="text-xs theme-muted mt-1">{{ strtoupper($item->status) }} · {{ $item->revealed_digits }}/{{ $item->digit_length }} dígitos</div>
                    @if($item->public_slug)
                        <a href="{{ route('draws.watch', $item->public_slug) }}" target="_blank" class="text-xs font-semibold underline mt-1 inline-block" style="color: var(--badge-text);">Página pública</a>
                    @endif
                </div>
            @empty
                <p class="text-sm theme-muted">Nenhum sorteio iniciado ainda.</p>
            @endforelse
        </aside>
    </div>
</div>
@endsection
