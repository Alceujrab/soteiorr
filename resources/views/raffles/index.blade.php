@extends('layouts.public')

@section('title', 'Ações Promocionais Ativas - RR Veículos')

@section('content')
@php
    $heroBanner = isset($banners) && $banners->isNotEmpty() ? $banners->first() : null;
    $heroRaffle = $raffles->first();
    $heroImage = $heroBanner->image_url
        ?? $heroRaffle?->image_url
        ?? 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1600&q=80';
    $heroTitle = $heroBanner->title
        ?? ($heroRaffle?->title ?? 'Seu próximo clássico pode ser seu');
    $heroSubtitle = $heroBanner->subtitle
        ?? 'Participe da Ação Promocional e concorra a veículos reais com total transparência.';

    $soldNumbers = $raffles->sum('paid_tickets_count');
    $totalNumbers = max(1, (int) $raffles->sum('total_numbers'));
    $metaPercent = min(100, round(($soldNumbers / $totalNumbers) * 100));
    $remainPercent = max(0, 100 - $metaPercent);
    $firstRaffleUrl = $heroRaffle ? route('raffles.show', $heroRaffle->id) : '#acoes-promocionais';
@endphp

<div class="space-y-10 md:space-y-14">
    <!-- Hero assimétrico -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-0 overflow-hidden rounded-2xl border" style="border-color: var(--border-color); background: var(--bg-card);">
        <div class="lg:col-span-7 relative min-h-[280px] sm:min-h-[360px] lg:min-h-[440px]">
            <img src="{{ $heroImage }}" alt="{{ $heroTitle }}" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t lg:bg-gradient-to-r from-[#0c0e12] via-[#0c0e12]/55 to-transparent"></div>
            <div class="absolute bottom-4 left-4 sm:bottom-6 sm:left-6 flex flex-wrap gap-2">
                <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded bg-black/55 border border-white/10 text-white">100% Seguro</span>
                <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded bg-black/55 border border-white/10 text-white">Apuração transparente</span>
                <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded bg-black/55 border border-white/10 text-white">Veículos reais</span>
            </div>
        </div>

        <div class="lg:col-span-5 p-6 sm:p-8 lg:p-10 flex flex-col justify-center space-y-5">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full animate-pulse" style="background: var(--accent);"></span>
                <span class="text-[11px] font-bold uppercase tracking-[0.18em]" style="color: var(--badge-text);">Ação Promocional</span>
            </div>

            <h1 class="font-display text-3xl sm:text-4xl lg:text-[2.55rem] font-bold leading-[1.1] tracking-tight text-white">
                {{ $heroTitle }}
            </h1>
            <p class="text-sm sm:text-base text-slate-400 leading-relaxed">
                {{ $heroSubtitle }}
            </p>

            <div class="space-y-2 pt-1">
                <div class="flex items-end justify-between gap-3">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-500">Meta da campanha</div>
                        <div class="font-display text-2xl font-bold text-white">{{ $metaPercent }}%</div>
                    </div>
                    <div class="text-right text-xs text-slate-500">
                        Faltam <strong class="text-white">{{ $remainPercent }}%</strong><br>para a meta
                    </div>
                </div>
                <div class="h-2.5 rounded-full overflow-hidden bg-black/40 border" style="border-color: var(--border-color);">
                    <div class="h-full rounded-full transition-all duration-700" style="width: {{ $metaPercent }}%; background: linear-gradient(90deg, var(--accent), #ff5a67);"></div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <a href="{{ $firstRaffleUrl }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-white font-bold text-sm transition shadow-lg" style="background-color: var(--accent); box-shadow: 0 10px 30px rgba(225,29,46,0.28);">
                    Quero participar <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
                <a href="#pacotes" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border text-sm font-semibold text-white hover:bg-white/5 transition" style="border-color: var(--border-color);">
                    Ver pacotes
                </a>
            </div>
        </div>
    </section>

    <!-- Pacotes -->
    <section id="pacotes" class="space-y-5 scroll-mt-24">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.18em]" style="color: var(--badge-text);">Escolha seu pacote</p>
                <h2 class="font-display text-2xl sm:text-3xl font-bold text-white mt-1">Mais números, mais chances</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            @php
                $homePackages = $heroRaffle && $heroRaffle->packages->isNotEmpty()
                    ? $heroRaffle->packages
                    : collect(\App\Models\RafflePackage::defaultDefinitions())->map(fn ($p) => (object) $p);
            @endphp

            @foreach($homePackages as $package)
                <div class="relative glass-card rounded-2xl p-5 border flex flex-col gap-4 {{ $package->is_featured ? 'ring-1' : '' }}" style="{{ $package->is_featured ? 'ring-color: var(--accent); border-color: rgba(225,29,46,0.45);' : '' }}">
                    @if($package->is_featured)
                        <span class="absolute -top-2.5 left-4 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded text-white" style="background: var(--accent);">Mais escolhido</span>
                    @endif
                    <div>
                        <div class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">{{ $package->name }}</div>
                        <div class="font-display text-3xl font-bold text-white mt-2">R$ {{ number_format((float) $package->price, 2, ',', '.') }}</div>
                        <div class="text-sm text-slate-400 mt-1">{{ $package->numbers_qty }} números</div>
                    </div>
                    @if(!empty($package->highlight))
                        <p class="text-xs text-slate-500">{{ $package->highlight }}</p>
                    @endif
                    <a href="{{ $firstRaffleUrl }}" class="mt-auto w-full text-center py-2.5 rounded-xl text-sm font-bold transition {{ $package->is_featured ? 'text-white' : 'border text-white hover:bg-white/5' }}" style="{{ $package->is_featured ? 'background: var(--accent);' : 'border-color: var(--border-color);' }}">
                        Comprar
                    </a>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Ações ativas -->
    <section id="acoes-promocionais" class="space-y-5 scroll-mt-24">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-display text-2xl sm:text-3xl font-bold text-white flex items-center gap-3">
                <span class="w-1.5 h-7 rounded-full" style="background: var(--accent);"></span>
                Ações Promocionais Ativas
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($raffles as $raffle)
                @php
                    $sold = $raffle->taken_tickets_count ?? 0;
                    $pct = $raffle->total_numbers > 0 ? min(100, round(($sold / $raffle->total_numbers) * 100)) : 0;
                @endphp
                <article class="glass-card rounded-2xl overflow-hidden border group flex flex-col h-full transition hover:-translate-y-0.5">
                    <div class="h-52 w-full relative overflow-hidden bg-black">
                        <img src="{{ $raffle->image_url }}" alt="{{ $raffle->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute top-4 right-4 text-white font-bold px-3 py-1 rounded-lg text-sm shadow" style="background: var(--accent);">
                            a partir de R$ {{ number_format($raffle->startingPrice(), 2, ',', '.') }}
                        </div>
                    </div>
                    <div class="p-5 sm:p-6 flex-grow flex flex-col gap-4">
                        <div class="space-y-2">
                            <h3 class="font-display text-xl font-bold text-white group-hover:opacity-90 transition">
                                {{ $raffle->title }}
                            </h3>
                            <p class="text-slate-400 text-sm line-clamp-2">{{ $raffle->description }}</p>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between text-[11px] text-slate-500">
                                <span>{{ number_format($sold, 0, ',', '.') }} / {{ number_format($raffle->total_numbers, 0, ',', '.') }} números</span>
                                <span>{{ $pct }}%</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-black/35 overflow-hidden">
                                <div class="h-full rounded-full" style="width: {{ $pct }}%; background: var(--accent);"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs text-slate-500 pt-1 border-t" style="border-color: var(--border-color);">
                            <span><i class="fa-regular fa-calendar-days mr-1"></i> {{ $raffle->draw_date->format('d/m/Y') }}</span>
                            <span><i class="fa-solid fa-ticket mr-1"></i> {{ $raffle->total_numbers }} cotas</span>
                        </div>

                        <a href="{{ route('raffles.show', $raffle->id) }}" class="w-full text-white font-bold py-3 px-4 rounded-xl transition flex items-center justify-center gap-2" style="background: var(--accent);">
                            Quero Participar <i class="fa-solid fa-chevron-right text-xs"></i>
                        </a>
                    </div>
                </article>
            @empty
                <div class="glass-card p-12 text-center rounded-2xl md:col-span-2 border">
                    <div class="text-slate-500 mb-4">
                        <i class="fa-solid fa-car text-5xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Nenhuma Ação Promocional ativa</h3>
                    <p class="text-slate-400 mt-1">Volte mais tarde ou fale com a administração.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
