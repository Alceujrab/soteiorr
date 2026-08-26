@extends('layouts.public')

@section('title', $raffle->title . ' - Detalhes da Ação Promocional')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Raffle Info (Left Column - 1 part) -->
    <div class="space-y-6 lg:col-span-1">
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="h-64 bg-slate-950 relative overflow-hidden group">
                @php
                    $images = !empty($raffle->images) && is_array($raffle->images) ? $raffle->images : [$raffle->image_url ?: 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&w=800&q=80'];
                @endphp
                
                <div class="w-full h-full flex transition-transform duration-500" id="carousel-slides" style="width: {{ count($images) * 100 }}%">
                    @foreach($images as $image)
                        <div class="w-full h-full flex-shrink-0 relative" style="width: calc(100% / {{ count($images) }})">
                            <img src="{{ $image }}" alt="{{ $raffle->title }}" class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>

                @if(count($images) > 1)
                    <button onclick="prevSlide()" class="absolute left-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/60 hover:bg-black/90 text-white flex items-center justify-center transition opacity-0 group-hover:opacity-100 z-10">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <button onclick="nextSlide()" class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/60 hover:bg-black/90 text-white flex items-center justify-center transition opacity-0 group-hover:opacity-100 z-10">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                    
                    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
                        @foreach($images as $index => $image)
                            <button onclick="setSlide({{ $index }})" class="w-2 h-2 rounded-full transition-all duration-300 carousel-dot {{ $index === 0 ? 'bg-[#aa7c11] scale-125' : 'bg-white/50' }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $raffle->status === 'active' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                        {{ $raffle->status === 'active' ? 'Ativa' : 'Encerrada' }}
                    </span>
                    <h1 class="text-2xl font-bold text-white mt-3">{{ $raffle->title }}</h1>
                    <p class="text-slate-400 text-sm mt-2">{{ $raffle->description }}</p>
                </div>

                <div class="border-t pt-4 space-y-3" style="border-color: var(--border-color);">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">A partir de:</span>
                        <strong class="text-emerald-400 font-bold">R$ {{ number_format($raffle->startingPrice(), 2, ',', '.') }}</strong>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Total de números:</span>
                        <span class="text-white">{{ number_format($raffle->total_numbers, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Data da Ação Promocional:</span>
                        <span class="text-white font-medium">{{ $raffle->draw_date->format('d/m/Y \à\s H:i') }}</span>
                    </div>
                    @if(\App\Models\Setting::get('show_sold_qty', '1') === '1')
                        <div class="space-y-1.5 pt-2 border-t" style="border-color: var(--border-color);">
                            <div class="flex justify-between text-xs text-slate-400">
                                <span>Progresso de Vendas:</span>
                                <span class="font-bold text-white">{{ number_format($takenCount, 0, ',', '.') }} / {{ number_format($raffle->total_numbers, 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-slate-900 rounded-full h-2 overflow-hidden border" style="border-color: var(--border-color);">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $raffle->total_numbers > 0 ? min(100, ($takenCount / $raffle->total_numbers) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @endif
                    @if($raffle->status === 'completed' && $raffle->draw)
                        <div class="p-3 bg-blue-500/10 border border-blue-500/30 rounded-xl text-blue-400 mt-4">
                            <div class="font-bold text-xs uppercase tracking-wider mb-1">Ganhador:</div>
                            <div class="text-sm font-semibold">Número {{ $raffle->draw->winning_number }}</div>
                            <div class="text-xs mt-1">Ganhador: {{ $raffle->draw->winningUser->name }}</div>
                        </div>
                    @endif

                    @php
                        $videoId = '';
                        if (!empty($raffle->youtube_url)) {
                            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|watch\?v=)|youtu\.be/)([^"&?/ ]{11})%i', $raffle->youtube_url, $match)) {
                                $videoId = $match[1];
                            }
                        }
                    @endphp
                    @if($videoId)
                        <div class="border-t pt-4 space-y-2" style="border-color: var(--border-color);">
                            <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Vídeo de Apresentação:</label>
                            <div class="relative w-full aspect-video rounded-xl overflow-hidden border" style="border-color: var(--border-color);">
                                <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $videoId }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Compartilhar Ação Promocional Card -->
        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-share-nodes text-blue-500"></i> Compartilhar Ação Promocional
            </h3>
            <p class="text-xs text-slate-400">Ajude a divulgar esta ação entre amigos nas redes sociais!</p>
            
            <div class="grid grid-cols-5 gap-2">
                <!-- WhatsApp -->
                <a href="https://api.whatsapp.com/send?text={{ urlencode('Olha essa ação incrível no Ação RR Veículos: ' . $raffle->title . ' (Prêmio: ' . $raffle->prize_name . '). Participe em: ' . route('raffles.show', $raffle->id)) }}" target="_blank" class="flex items-center justify-center p-3 rounded-xl bg-[#25D366] hover:bg-[#20ba5a] text-white transition shadow-md" title="Compartilhar no WhatsApp">
                    <i class="fa-brands fa-whatsapp text-lg"></i>
                </a>
                <!-- Facebook -->
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('raffles.show', $raffle->id)) }}" target="_blank" class="flex items-center justify-center p-3 rounded-xl bg-[#1877F2] hover:bg-[#166fe5] text-white transition shadow-md" title="Compartilhar no Facebook">
                    <i class="fa-brands fa-facebook-f text-lg"></i>
                </a>
                <!-- Twitter/X -->
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('raffles.show', $raffle->id)) }}&text={{ urlencode('Confira essa ação de prêmios no Ação RR Veículos!') }}" target="_blank" class="flex items-center justify-center p-3 rounded-xl bg-black hover:bg-slate-900 text-white border border-slate-800 transition shadow-md" title="Compartilhar no X (Twitter)">
                    <i class="fa-brands fa-x-twitter text-lg"></i>
                </a>
                <!-- LinkedIn -->
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('raffles.show', $raffle->id)) }}" target="_blank" class="flex items-center justify-center p-3 rounded-xl bg-[#0A66C2] hover:bg-[#0958a8] text-white transition shadow-md" title="Compartilhar no LinkedIn">
                    <i class="fa-brands fa-linkedin-in text-lg"></i>
                </a>
                <!-- Copy Link -->
                <button onclick="copyRaffleLink()" class="flex items-center justify-center p-3 rounded-xl bg-slate-700 hover:bg-slate-600 text-white transition shadow-md" title="Copiar Link">
                    <i class="fa-solid fa-link text-lg"></i>
                </button>
            </div>

            <div class="pt-2 space-y-2">
                <button onclick="shareRaffleNative()" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-white text-xs font-bold transition shadow" style="background-color: var(--accent);">
                    <i class="fa-solid fa-share-nodes"></i> Compartilhar no Celular (Instagram, Tik Tok...)
                </button>
                <button onclick="copyPromoPost()" class="w-full flex items-center justify-center gap-2 py-2 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 transition">
                    <i class="fa-solid fa-copy"></i> Copiar Texto de Divulgação (Redes Sociais)
                </button>
            </div>
            
            <p id="share-toast" class="text-center text-[10px] text-emerald-400 font-bold hidden">Link copiado com sucesso!</p>
            <p id="promo-toast" class="text-center text-[10px] text-emerald-400 font-bold hidden">Texto de divulgação copiado para colar no Instagram/TikTok/YouTube!</p>
        </div>

        <script>
            function copyRaffleLink() {
                const link = "{{ route('raffles.show', $raffle->id) }}";
                navigator.clipboard.writeText(link).then(() => {
                    const toast = document.getElementById('share-toast');
                    toast.classList.remove('hidden');
                    setTimeout(() => toast.classList.add('hidden'), 3000);
                });
            }

            function copyPromoPost() {
                const text = "🏆 Participe também da ação entre amigos da Ação RR Veículos!\n🔥 Ação: {{ $raffle->title }}\n🎁 Prêmio: {{ $raffle->prize_name }}\n👉 Garanta seus números da sorte em: {{ route('raffles.show', $raffle->id) }}";
                navigator.clipboard.writeText(text).then(() => {
                    const toast = document.getElementById('promo-toast');
                    toast.classList.remove('hidden');
                    setTimeout(() => toast.classList.add('hidden'), 3000);
                });
            }

            function shareRaffleNative() {
                if (navigator.share) {
                    navigator.share({
                        title: 'Ação RR Veículos - {{ $raffle->title }}',
                        text: 'Confira e participe desta ação premium para concorrer ao {{ $raffle->prize_name }}!',
                        url: '{{ route("raffles.show", $raffle->id) }}'
                    }).catch(console.error);
                } else {
                    copyRaffleLink();
                    alert('Navegador não suporta compartilhamento nativo. O link foi copiado para a área de transferência!');
                }
            }
        </script>
    </div>

    <!-- Purchase Column (Right Column - 2 parts) -->
    <div class="lg:col-span-2 space-y-6">
        <div class="glass-card rounded-2xl p-6 space-y-6">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked" style="color: var(--accent);"></i> Escolha seu pacote
            </h2>
            <p class="text-sm text-slate-400">Os números são atribuídos automaticamente após a confirmação do pagamento.</p>

            @if(\App\Models\Setting::get('show_sold_qty', '1') === '1')
                <div class="p-4 bg-slate-950 rounded-xl border space-y-3" style="border-color: var(--border-color);">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400 font-medium">Números reservados/pagos:</span>
                        <span class="text-white font-bold">{{ number_format($takenCount, 0, ',', '.') }} / {{ number_format($raffle->total_numbers, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-slate-900 rounded-full h-3 overflow-hidden border" style="border-color: var(--border-color);">
                        <div class="h-3 rounded-full" style="width: {{ $raffle->total_numbers > 0 ? min(100, ($takenCount / $raffle->total_numbers) * 100) : 0 }}%; background-color: var(--accent);"></div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse($raffle->packages as $package)
                    <form action="{{ route('raffles.buy', $raffle->id) }}" method="POST" class="relative glass-card rounded-2xl p-5 border flex flex-col gap-3 {{ $package->is_featured ? 'ring-1' : '' }}" style="{{ $package->is_featured ? 'ring-color: var(--accent); border-color: rgba(225,29,46,0.45);' : 'border-color: var(--border-color);' }}">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                        @if($package->is_featured)
                            <span class="absolute -top-2.5 left-4 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded text-white" style="background: var(--accent);">Mais escolhido</span>
                        @endif
                        <div>
                            <div class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">{{ $package->name }}</div>
                            <div class="font-display text-3xl font-bold text-white mt-2">R$ {{ number_format($package->price, 2, ',', '.') }}</div>
                            <div class="text-sm text-slate-400 mt-1">{{ $package->numbers_qty }} números</div>
                        </div>
                        @if($package->highlight)
                            <p class="text-xs text-slate-500">{{ $package->highlight }}</p>
                        @endif
                        <p class="text-[11px] text-slate-500">Custo efetivo: R$ {{ number_format($package->effectiveCostPerNumber(), 4, ',', '.') }} / número</p>
                        <button type="submit" class="mt-auto w-full text-center py-2.5 rounded-xl text-sm font-bold transition text-white" style="background: var(--accent);">
                            Comprar pacote
                        </button>
                    </form>
                @empty
                    <div class="sm:col-span-2 text-sm text-slate-400">
                        Nenhum pacote cadastrado para esta ação. Peça ao administrador para configurar os planos.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(count($images) > 1)
        let currentSlide = 0;
        const slidesCount = {{ count($images) }};

        window.setSlide = function(index) {
            if (index < 0 || index >= slidesCount) return;
            currentSlide = index;
            const slidesContainer = document.getElementById('carousel-slides');
            const dots = document.querySelectorAll('.carousel-dot');

            slidesContainer.style.transform = `translateX(-${(currentSlide * 100) / slidesCount}%)`;

            dots.forEach((dot, idx) => {
                if (idx === currentSlide) {
                    dot.classList.remove('bg-white/50');
                    dot.classList.add('bg-[#e11d2e]', 'scale-125');
                } else {
                    dot.classList.remove('bg-[#e11d2e]', 'scale-125');
                    dot.classList.add('bg-white/50');
                }
            });
        };

        window.prevSlide = function() {
            let prev = currentSlide - 1;
            if (prev < 0) prev = slidesCount - 1;
            setSlide(prev);
        };

        window.nextSlide = function() {
            let next = currentSlide + 1;
            if (next >= slidesCount) next = 0;
            setSlide(next);
        };
    @endif
});
</script>
@endsection
