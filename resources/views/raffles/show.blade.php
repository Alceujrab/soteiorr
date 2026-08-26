@extends('layouts.public')

@section('title', $raffle->title . ' - Detalhes da Ação Promocional')

@section('content')
@php
    $images = ! empty($raffle->images) && is_array($raffle->images)
        ? $raffle->images
        : [$raffle->image_url ?: asset('images/logo-rr.png')];
    $images = array_values(array_filter($images));
    if (empty($images)) {
        $images = [asset('images/logo-rr.png')];
    }

    $videoId = '';
    if (! empty($raffle->youtube_url) && preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|watch\?v=)|youtu\.be/)([^"&?/ ]{11})%i', $raffle->youtube_url, $match)) {
        $videoId = $match[1];
    }

    $shareUrl = route('raffles.show', $raffle->id);
    $shareText = 'Olha essa ação incrível no Ação RR Veículos: '.$raffle->title.' (Prêmio: '.$raffle->prize_name.'). Participe em: '.$shareUrl;
    $pct = $raffle->total_numbers > 0 ? min(100, ($takenCount / $raffle->total_numbers) * 100) : 0;
    $showSold = \App\Models\Setting::get('show_sold_qty', '1') === '1';
@endphp

{{-- ===================== MOBILE: OPÇÃO C — Story Vertical ===================== --}}
<div class="lg:hidden space-y-6 pb-8">
    {{-- Hero --}}
    <section class="glass-card rounded-2xl overflow-hidden border" style="border-color: var(--border-color);">
        <div class="p-5 space-y-3">
            <p class="text-[11px] font-bold uppercase tracking-[0.16em]" style="color: var(--accent);">Ação entre amigos</p>
            <h1 class="font-display text-2xl font-bold leading-tight text-white">{{ $raffle->prize_name }}</h1>
            <p class="text-sm text-slate-400">{{ $raffle->description ?: 'Clássico, confiável e pronto para rodar!' }}</p>
            <div class="inline-flex items-center gap-2 text-white text-xs font-bold px-3 py-2 rounded-lg" style="background: var(--accent);">
                <i class="fa-solid fa-ticket"></i>
                Sorteio {{ $raffle->draw_date->format('d/m/Y') }} pela Loteria Federal
            </div>
        </div>
        <div class="relative aspect-[16/10] bg-slate-900">
            <img id="mobile-hero-img" src="{{ $images[0] }}" alt="{{ $raffle->title }}" class="w-full h-full object-cover">
            <img src="{{ asset('images/logo-rr.png') }}" alt="RR Veículos" class="photo-brand-mark">
        </div>
    </section>

    {{-- Galeria --}}
    <section class="space-y-3">
        <h2 class="font-display text-lg font-bold text-white px-1">Galeria do prêmio</h2>
        <div class="flex gap-3 overflow-x-auto pb-2 snap-x snap-mandatory -mx-1 px-1">
            @foreach($images as $index => $image)
                <button type="button" onclick="setGalleryImage({{ $index }})" class="gallery-thumb snap-start flex-shrink-0 w-28 h-28 rounded-xl overflow-hidden border-2 transition {{ $index === 0 ? 'border-[var(--accent)]' : 'border-transparent' }}" data-index="{{ $index }}">
                    <img src="{{ $image }}" alt="Foto {{ $index + 1 }}" class="w-full h-full object-cover">
                </button>
            @endforeach
        </div>
        <p class="text-xs text-slate-500 px-1 flex items-center gap-2">
            <i class="fa-solid fa-hand-pointer"></i> Deslize para ver mais fotos
        </p>
    </section>

    {{-- Vídeo --}}
    @if($videoId)
        <section class="space-y-3">
            <h2 class="font-display text-lg font-bold text-white px-1">Vídeo do prêmio</h2>
            <div class="relative w-full aspect-video rounded-2xl overflow-hidden border glass-card" style="border-color: var(--border-color);">
                <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $videoId }}" title="Vídeo do prêmio" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            </div>
        </section>
    @endif

    {{-- Detalhes --}}
    <section class="glass-card rounded-2xl p-5 border space-y-4" style="border-color: var(--border-color);">
        <h2 class="font-display text-lg font-bold text-white">Detalhes do veículo</h2>
        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-xl border p-3 space-y-1" style="border-color: var(--border-color);">
                <div class="text-[10px] uppercase font-bold text-slate-500"><i class="fa-solid fa-car mr-1" style="color: var(--accent);"></i> Prêmio</div>
                <div class="text-sm font-semibold text-white leading-snug">{{ $raffle->prize_name }}</div>
            </div>
            <div class="rounded-xl border p-3 space-y-1" style="border-color: var(--border-color);">
                <div class="text-[10px] uppercase font-bold text-slate-500"><i class="fa-solid fa-hashtag mr-1" style="color: var(--accent);"></i> Números</div>
                <div class="text-sm font-semibold text-white">{{ number_format($raffle->total_numbers, 0, ',', '.') }}</div>
            </div>
            <div class="rounded-xl border p-3 space-y-1" style="border-color: var(--border-color);">
                <div class="text-[10px] uppercase font-bold text-slate-500"><i class="fa-solid fa-calendar-day mr-1" style="color: var(--accent);"></i> Sorteio</div>
                <div class="text-sm font-semibold text-white">{{ $raffle->draw_date->format('d/m/Y H:i') }}</div>
            </div>
            <div class="rounded-xl border p-3 space-y-1" style="border-color: var(--border-color);">
                <div class="text-[10px] uppercase font-bold text-slate-500"><i class="fa-solid fa-tag mr-1" style="color: var(--accent);"></i> A partir de</div>
                <div class="text-sm font-semibold text-white">R$ {{ number_format($raffle->startingPrice(), 2, ',', '.') }}</div>
            </div>
        </div>
        @if($raffle->prize_description)
            <p class="text-sm text-slate-400 leading-relaxed">{{ $raffle->prize_description }}</p>
        @endif
        <p class="text-xs font-semibold flex items-center gap-2" style="color: var(--accent);">
            <i class="fa-solid fa-circle-check"></i> Veículo revisado e em ótimo estado de conservação!
        </p>
        @if($showSold)
            <div class="space-y-1.5 pt-1">
                <div class="flex justify-between text-xs text-slate-500">
                    <span>Progresso</span>
                    <span class="font-bold text-white">{{ number_format($takenCount, 0, ',', '.') }} / {{ number_format($raffle->total_numbers, 0, ',', '.') }}</span>
                </div>
                <div class="h-2 rounded-full overflow-hidden bg-black/10 border" style="border-color: var(--border-color);">
                    <div class="h-full rounded-full" style="width: {{ $pct }}%; background: var(--accent);"></div>
                </div>
            </div>
        @endif
    </section>

    {{-- Combos --}}
    <section class="space-y-3" id="pacotes-mobile">
        <h2 class="font-display text-lg font-bold text-white px-1">Combos de números</h2>
        <div class="space-y-3">
            @forelse($raffle->packages as $package)
                <form action="{{ route('raffles.buy', $raffle->id) }}" method="POST" class="relative glass-card rounded-2xl p-4 border flex items-center gap-3 {{ $package->is_featured ? 'ring-2' : '' }}" style="{{ $package->is_featured ? 'border-color: var(--accent); box-shadow: 0 0 0 1px var(--accent);' : 'border-color: var(--border-color);' }}">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                    @if($package->is_featured)
                        <span class="absolute -top-2.5 left-4 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded text-white" style="background: var(--accent);">Mais escolhido</span>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $package->name }}</div>
                        <div class="text-sm text-slate-400">{{ $package->numbers_qty }} {{ $package->numbers_qty === 1 ? 'número' : 'números' }}</div>
                        <div class="font-display text-xl font-bold mt-0.5" style="color: var(--accent);">R$ {{ number_format($package->price, 2, ',', '.') }}</div>
                    </div>
                    <button type="submit" class="shrink-0 px-3 py-2.5 rounded-xl text-[11px] font-bold text-white uppercase tracking-wide" style="background: var(--accent);">
                        Escolher
                    </button>
                </form>
            @empty
                <p class="text-sm text-slate-400">Nenhum pacote cadastrado.</p>
            @endforelse
        </div>
    </section>

    {{-- Trust + CTA --}}
    <section class="grid grid-cols-2 gap-3 text-center">
        <div class="glass-card rounded-xl p-3 border text-xs text-slate-500" style="border-color: var(--border-color);">
            <i class="fa-solid fa-landmark mb-1" style="color: var(--accent);"></i><br>Loteria Federal
        </div>
        <div class="glass-card rounded-xl p-3 border text-xs text-slate-500" style="border-color: var(--border-color);">
            <i class="fa-solid fa-shield-halved mb-1" style="color: var(--accent);"></i><br>Compra 100% segura
        </div>
        <div class="glass-card rounded-xl p-3 border text-xs text-slate-500" style="border-color: var(--border-color);">
            <i class="fa-solid fa-bolt mb-1" style="color: var(--accent);"></i><br>Número na hora
        </div>
        <div class="glass-card rounded-xl p-3 border text-xs text-slate-500" style="border-color: var(--border-color);">
            <i class="fa-solid fa-eye mb-1" style="color: var(--accent);"></i><br>Transparência
        </div>
    </section>

    <div class="rounded-2xl p-4 text-center text-white font-bold text-sm" style="background: var(--accent);">
        Garanta já seu número e boa sorte!
    </div>

    <a href="{{ route('pages.contact') }}" class="glass-card flex items-center justify-center gap-2 w-full py-3.5 rounded-2xl border text-sm font-bold text-white" style="border-color: var(--border-color);">
        <i class="fa-brands fa-whatsapp text-lg" style="color: #25D366;"></i> Fale conosco
    </a>
</div>

{{-- ===================== DESKTOP: OPÇÃO B — Vitrine ===================== --}}
<div class="hidden lg:block space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.16em]" style="color: var(--accent);">Ação Promocional</p>
            <h1 class="font-display text-3xl font-bold text-white mt-1">{{ $raffle->title }}</h1>
        </div>
        <div class="flex items-center gap-5 text-xs font-semibold text-slate-500">
            <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-shield-halved" style="color: var(--accent);"></i> Pagamento seguro</span>
            <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-trophy" style="color: var(--accent);"></i> Sorteio garantido</span>
            <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-headset" style="color: var(--accent);"></i> Suporte dedicado</span>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-8 items-start">
        {{-- Left: gallery + tabs --}}
        <div class="col-span-7 space-y-5">
            <div class="glass-card rounded-2xl overflow-hidden border" style="border-color: var(--border-color);">
                <div class="relative aspect-[16/10] bg-slate-900 group">
                    <img id="desktop-hero-img" src="{{ $images[0] }}" alt="{{ $raffle->title }}" class="w-full h-full object-cover">
                    <div class="absolute top-4 left-4 rounded-lg overflow-hidden shadow-lg">
                        <div class="px-3 py-1.5 text-white text-xs font-bold uppercase tracking-wide" style="background: var(--accent);">{{ $raffle->prize_name }}</div>
                        <div class="px-3 py-1 bg-black/80 text-[11px] text-white/90">{{ $raffle->title }}</div>
                    </div>
                    <span class="absolute top-4 right-4 text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded text-white" style="background: var(--accent);">Imagem ilustrativa</span>
                    <img src="{{ asset('images/logo-rr.png') }}" alt="RR Veículos" class="photo-brand-mark">

                    @if(count($images) > 1)
                        <button type="button" onclick="prevGallery()" class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white text-[var(--accent)] shadow flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button type="button" onclick="nextGallery()" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white text-[var(--accent)] shadow flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5">
                            @foreach($images as $index => $image)
                                <button type="button" onclick="setGalleryImage({{ $index }})" class="gallery-dot w-2 h-2 rounded-full transition {{ $index === 0 ? 'bg-[var(--accent)] scale-125' : 'bg-white/60' }}" data-index="{{ $index }}"></button>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if(count($images) > 1)
                    <div class="p-3 flex gap-2 overflow-x-auto border-t" style="border-color: var(--border-color);">
                        @foreach($images as $index => $image)
                            <button type="button" onclick="setGalleryImage({{ $index }})" class="gallery-thumb flex-shrink-0 w-20 h-16 rounded-lg overflow-hidden border-2 transition {{ $index === 0 ? 'border-[var(--accent)]' : 'border-transparent' }}" data-index="{{ $index }}">
                                <img src="{{ $image }}" alt="Miniatura {{ $index + 1 }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Tabs --}}
            <div class="glass-card rounded-2xl border overflow-hidden" style="border-color: var(--border-color);">
                <div class="flex border-b overflow-x-auto" style="border-color: var(--border-color);">
                    <button type="button" class="detail-tab px-5 py-3.5 text-sm font-semibold border-b-2 transition" data-tab="descricao" style="border-color: var(--accent); color: var(--accent);">Descrição</button>
                    <button type="button" class="detail-tab px-5 py-3.5 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-white transition" data-tab="video"><i class="fa-solid fa-play mr-1.5"></i>Vídeo</button>
                    <button type="button" class="detail-tab px-5 py-3.5 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-white transition" data-tab="regulamento"><i class="fa-solid fa-file-lines mr-1.5"></i>Regulamento</button>
                    <button type="button" class="detail-tab px-5 py-3.5 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-white transition" data-tab="compartilhar"><i class="fa-solid fa-share-nodes mr-1.5"></i>Compartilhar</button>
                </div>

                <div class="p-6">
                    <div id="tab-descricao" class="tab-panel space-y-5">
                        <p class="text-sm text-slate-400 leading-relaxed">{{ $raffle->description ?: $raffle->prize_description }}</p>
                        @if($raffle->prize_description && $raffle->description)
                            <p class="text-sm text-slate-400 leading-relaxed">{{ $raffle->prize_description }}</p>
                        @endif
                        <div class="grid grid-cols-3 gap-3">
                            <div class="rounded-xl border p-4 space-y-1" style="border-color: var(--border-color);">
                                <div class="text-[10px] uppercase font-bold text-slate-500"><i class="fa-solid fa-car mr-1" style="color: var(--accent);"></i> Prêmio</div>
                                <div class="text-sm font-semibold text-white">{{ $raffle->prize_name }}</div>
                            </div>
                            <div class="rounded-xl border p-4 space-y-1" style="border-color: var(--border-color);">
                                <div class="text-[10px] uppercase font-bold text-slate-500"><i class="fa-solid fa-hashtag mr-1" style="color: var(--accent);"></i> Números</div>
                                <div class="text-sm font-semibold text-white">{{ number_format($raffle->total_numbers, 0, ',', '.') }}</div>
                            </div>
                            <div class="rounded-xl border p-4 space-y-1" style="border-color: var(--border-color);">
                                <div class="text-[10px] uppercase font-bold text-slate-500"><i class="fa-solid fa-tag mr-1" style="color: var(--accent);"></i> A partir de</div>
                                <div class="text-sm font-semibold text-white">R$ {{ number_format($raffle->startingPrice(), 2, ',', '.') }}</div>
                            </div>
                            <div class="rounded-xl border p-4 space-y-1" style="border-color: var(--border-color);">
                                <div class="text-[10px] uppercase font-bold text-slate-500"><i class="fa-solid fa-calendar-day mr-1" style="color: var(--accent);"></i> Data</div>
                                <div class="text-sm font-semibold text-white">{{ $raffle->draw_date->format('d/m/Y') }}</div>
                            </div>
                            <div class="rounded-xl border p-4 space-y-1" style="border-color: var(--border-color);">
                                <div class="text-[10px] uppercase font-bold text-slate-500"><i class="fa-solid fa-clock mr-1" style="color: var(--accent);"></i> Horário</div>
                                <div class="text-sm font-semibold text-white">{{ $raffle->draw_date->format('H:i') }}</div>
                            </div>
                            <div class="rounded-xl border p-4 space-y-1" style="border-color: var(--border-color);">
                                <div class="text-[10px] uppercase font-bold text-slate-500"><i class="fa-solid fa-circle-info mr-1" style="color: var(--accent);"></i> Status</div>
                                <div class="text-sm font-semibold text-white">{{ $raffle->status === 'active' ? 'Ativa' : 'Encerrada' }}</div>
                            </div>
                        </div>
                        @if($showSold)
                            <div class="space-y-2">
                                <div class="flex justify-between text-xs text-slate-500">
                                    <span>Bilhetes reservados/pagos</span>
                                    <span class="font-bold text-white">{{ number_format($takenCount, 0, ',', '.') }} / {{ number_format($raffle->total_numbers, 0, ',', '.') }}</span>
                                </div>
                                <div class="h-2.5 rounded-full overflow-hidden bg-black/10 border" style="border-color: var(--border-color);">
                                    <div class="h-full rounded-full" style="width: {{ $pct }}%; background: var(--accent);"></div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div id="tab-video" class="tab-panel hidden">
                        @if($videoId)
                            <div class="relative w-full aspect-video rounded-xl overflow-hidden border" style="border-color: var(--border-color);">
                                <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $videoId }}" title="Vídeo do prêmio" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                            </div>
                        @else
                            <p class="text-sm text-slate-400">Nenhum vídeo cadastrado para esta ação.</p>
                        @endif
                    </div>

                    <div id="tab-regulamento" class="tab-panel hidden space-y-3">
                        <p class="text-sm text-slate-400">Consulte o regulamento oficial da promoção para regras, elegibilidade e critérios de apuração.</p>
                        <a href="{{ route('pages.regulation') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white" style="background: var(--accent);">
                            <i class="fa-solid fa-scale-balanced"></i> Abrir regulamento
                        </a>
                    </div>

                    <div id="tab-compartilhar" class="tab-panel hidden space-y-4">
                        <p class="text-sm text-slate-400">Ajude a divulgar esta ação entre amigos:</p>
                        <div class="grid grid-cols-5 gap-2">
                            <a href="https://api.whatsapp.com/send?text={{ urlencode($shareText) }}" target="_blank" class="flex items-center justify-center p-3 rounded-xl bg-[#25D366] text-white"><i class="fa-brands fa-whatsapp text-lg"></i></a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" class="flex items-center justify-center p-3 rounded-xl bg-[#1877F2] text-white"><i class="fa-brands fa-facebook-f text-lg"></i></a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode('Confira essa ação no Ação RR Veículos!') }}" target="_blank" class="flex items-center justify-center p-3 rounded-xl bg-black text-white border" style="border-color: var(--border-color);"><i class="fa-brands fa-x-twitter text-lg"></i></a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}" target="_blank" class="flex items-center justify-center p-3 rounded-xl bg-[#0A66C2] text-white"><i class="fa-brands fa-linkedin-in text-lg"></i></a>
                            <button type="button" onclick="copyRaffleLink()" class="flex items-center justify-center p-3 rounded-xl border text-white" style="border-color: var(--border-color); background: var(--bg-primary);"><i class="fa-solid fa-link text-lg"></i></button>
                        </div>
                        <p id="share-toast" class="text-xs font-bold hidden" style="color: var(--accent);">Link copiado!</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: sticky packages --}}
        <div class="col-span-5">
            <div class="sticky top-24 space-y-4">
                <div class="glass-card rounded-2xl border p-6 space-y-5" style="border-color: var(--border-color);">
                    <div>
                        <h2 class="font-display text-xl font-bold text-white uppercase tracking-tight">Escolha seu pacote e concorra!</h2>
                        <p class="text-sm text-slate-400 mt-1">Quanto mais números, maiores suas chances!</p>
                    </div>

                    <div class="space-y-3">
                        @forelse($raffle->packages as $package)
                            <form action="{{ route('raffles.buy', $raffle->id) }}" method="POST" class="relative rounded-xl border p-4 flex items-center gap-4 transition hover:shadow-md {{ $package->is_featured ? 'ring-2' : '' }}" style="{{ $package->is_featured ? 'border-color: var(--accent); box-shadow: 0 0 0 1px var(--accent);' : 'border-color: var(--border-color);' }}">
                                @csrf
                                <input type="hidden" name="package_id" value="{{ $package->id }}">
                                @if($package->is_featured)
                                    <span class="absolute -top-2.5 left-4 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded text-white" style="background: var(--accent);">Mais escolhido</span>
                                @endif
                                <div class="w-4 h-4 rounded-full border-2 flex-shrink-0" style="border-color: {{ $package->is_featured ? 'var(--accent)' : 'var(--border-color)' }}; background: {{ $package->is_featured ? 'var(--accent)' : 'transparent' }};"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $package->name }}</div>
                                    <div class="text-sm text-slate-400">{{ $package->numbers_qty }} números</div>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="font-display text-lg font-bold" style="color: var(--accent);">R$ {{ number_format($package->price, 2, ',', '.') }}</div>
                                    <button type="submit" class="mt-1 text-[11px] font-bold uppercase tracking-wide px-3 py-1.5 rounded-lg text-white" style="background: var(--accent);">
                                        Comprar agora
                                    </button>
                                </div>
                            </form>
                        @empty
                            <p class="text-sm text-slate-400">Nenhum pacote cadastrado.</p>
                        @endforelse
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="glass-card rounded-xl border p-3 text-center" style="border-color: var(--border-color);">
                        <div class="text-[10px] uppercase font-bold text-slate-500 mb-1">Data do sorteio</div>
                        <div class="text-xs font-bold text-white">{{ $raffle->draw_date->format('d/m/Y') }}<br>{{ $raffle->draw_date->format('H:i') }}</div>
                    </div>
                    <div class="glass-card rounded-xl border p-3 text-center" style="border-color: var(--border-color);">
                        <div class="text-[10px] uppercase font-bold text-slate-500 mb-1">Sorteio pela</div>
                        <div class="text-xs font-bold text-white">Loteria Federal</div>
                    </div>
                    <div class="glass-card rounded-xl border p-3 text-center" style="border-color: var(--border-color);">
                        <div class="text-[10px] uppercase font-bold text-slate-500 mb-1">Entrega</div>
                        <div class="text-xs font-bold text-white">Grátis p/ Brasil</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const images = @json($images);
    let current = 0;

    function syncThumbs() {
        document.querySelectorAll('.gallery-thumb').forEach((el) => {
            const idx = Number(el.getAttribute('data-index'));
            if (idx === current) {
                el.classList.add('border-[var(--accent)]');
                el.classList.remove('border-transparent');
            } else {
                el.classList.remove('border-[var(--accent)]');
                el.classList.add('border-transparent');
            }
        });
        document.querySelectorAll('.gallery-dot').forEach((el) => {
            const idx = Number(el.getAttribute('data-index'));
            if (idx === current) {
                el.classList.add('bg-[var(--accent)]', 'scale-125');
                el.classList.remove('bg-white/60');
            } else {
                el.classList.remove('bg-[var(--accent)]', 'scale-125');
                el.classList.add('bg-white/60');
            }
        });
    }

    window.setGalleryImage = function (index) {
        if (index < 0 || index >= images.length) return;
        current = index;
        const desktop = document.getElementById('desktop-hero-img');
        const mobile = document.getElementById('mobile-hero-img');
        if (desktop) desktop.src = images[current];
        if (mobile) mobile.src = images[current];
        syncThumbs();
    };

    window.prevGallery = function () {
        setGalleryImage((current - 1 + images.length) % images.length);
    };

    window.nextGallery = function () {
        setGalleryImage((current + 1) % images.length);
    };

    document.querySelectorAll('.detail-tab').forEach((btn) => {
        btn.addEventListener('click', () => {
            const tab = btn.getAttribute('data-tab');
            document.querySelectorAll('.detail-tab').forEach((b) => {
                b.style.borderColor = 'transparent';
                b.style.color = '';
                b.classList.add('text-slate-500');
            });
            btn.style.borderColor = 'var(--accent)';
            btn.style.color = 'var(--accent)';
            btn.classList.remove('text-slate-500');

            document.querySelectorAll('.tab-panel').forEach((panel) => panel.classList.add('hidden'));
            const target = document.getElementById('tab-' + tab);
            if (target) target.classList.remove('hidden');
        });
    });

    window.copyRaffleLink = function () {
        navigator.clipboard.writeText(@json($shareUrl)).then(() => {
            const toast = document.getElementById('share-toast');
            if (! toast) return;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 2500);
        });
    };
});
</script>
@endsection
