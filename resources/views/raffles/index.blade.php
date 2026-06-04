@extends('layouts.public')

@section('title', 'Rifas Ativas - Ação RR Veículos')

@section('content')
<div class="space-y-8">
    
    <!-- Rotative Banner -->
    @if(isset($banners) && $banners->isNotEmpty())
        <div class="relative overflow-hidden rounded-2xl glass-card h-80 sm:h-96 w-full">
            <div id="banner-container" class="relative w-full h-full">
                @foreach($banners as $index => $banner)
                    <div class="banner-slide absolute inset-0 w-full h-full transition-opacity duration-1000 ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/30 to-transparent z-10"></div>
                        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                        <div class="absolute inset-y-0 left-0 flex flex-col justify-center px-8 sm:px-16 z-20 space-y-4 max-w-xl">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-500/30 w-fit">
                                <i class="fa-solid fa-star"></i> Destaque da Semana
                            </span>
                            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                                {{ $banner->title }}
                            </h1>
                            <p class="text-slate-300 text-sm sm:text-base">
                                {{ $banner->subtitle }}
                            </p>
                            <a href="#rifas" class="bg-blue-600 hover:bg-blue-500 text-white font-medium px-6 py-2.5 rounded-lg shadow-lg hover:shadow-blue-500/20 transition text-center w-fit">
                                Comprar Cotas
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Slider Controls -->
            <button onclick="prevBanner()" class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white p-2.5 rounded-full z-30 transition">
                <i class="fa-solid fa-chevron-left text-sm"></i>
            </button>
            <button onclick="nextBanner()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white p-2.5 rounded-full z-30 transition">
                <i class="fa-solid fa-chevron-right text-sm"></i>
            </button>
        </div>

        <script>
            let currentSlide = 0;
            const slides = document.querySelectorAll('.banner-slide');
            
            function showSlide(index) {
                slides[currentSlide].classList.remove('opacity-100', 'z-10');
                slides[currentSlide].classList.add('opacity-0', 'z-0');
                currentSlide = (index + slides.length) % slides.length;
                slides[currentSlide].classList.remove('opacity-0', 'z-0');
                slides[currentSlide].classList.add('opacity-100', 'z-10');
            }

            function nextBanner() {
                showSlide(currentSlide + 1);
            }

            function prevBanner() {
                showSlide(currentSlide - 1);
            }

            // Auto rotation every 6 seconds
            setInterval(nextBanner, 6000);
        </script>
    @else
        <!-- Header Hero (Fallback) -->
        <div class="relative overflow-hidden rounded-2xl glass-card p-8 sm:p-12 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-indigo-600/10 pointer-events-none"></div>
            <div class="space-y-4 max-w-xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/30">
                    <i class="fa-solid fa-star"></i> Sorteios 100% Auditados
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                    Concorra aos melhores veículos com preços imperdíveis!
                </h1>
                <p class="text-slate-400 text-base sm:text-lg">
                    Escolha sua ação, selecione seus números da sorte e realize o pagamento via PIX para participar. O sorteio é realizado ao vivo!
                </p>
            </div>
            <div class="w-full md:w-auto flex flex-col sm:flex-row gap-4">
                <a href="#rifas" class="bg-blue-600 hover:bg-blue-500 text-white font-medium px-6 py-3 rounded-lg shadow-lg hover:shadow-blue-500/20 transition text-center">
                    Ver Rifas Ativas
                </a>
            </div>
        </div>
    @endif

    <!-- Active Raffles Grid -->
    <div id="rifas" class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                <span class="w-2.5 h-6 bg-blue-500 rounded-full"></span>
                Ações Ativas
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @forelse($raffles as $raffle)
                <div class="glass-card rounded-2xl overflow-hidden hover:border-slate-700 transition flex flex-col h-full group">
                    <!-- Image -->
                    <div class="h-56 w-full relative overflow-hidden bg-slate-950">
                        <img src="{{ $raffle->image_url }}" alt="{{ $raffle->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute top-4 right-4 bg-emerald-500 text-emerald-950 font-bold px-3 py-1 rounded-full text-sm">
                            R$ {{ number_format($raffle->price, 2, ',', '.') }}
                        </div>
                    </div>
                    <!-- Details -->
                    <div class="p-6 flex-grow flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <h3 class="text-xl font-bold text-white group-hover:text-blue-400 transition">
                                {{ $raffle->title }}
                            </h3>
                            <p class="text-slate-400 text-sm line-clamp-3">
                                {{ $raffle->description }}
                            </p>
                        </div>

                        <div class="border-t border-slate-800/80 pt-4 flex items-center justify-between text-xs text-slate-400">
                            <span>
                                <i class="fa-regular fa-calendar-days mr-1"></i> Sorteio: {{ $raffle->draw_date->format('d/m/Y') }}
                            </span>
                            <span>
                                <i class="fa-solid fa-ticket mr-1"></i> {{ $raffle->total_numbers }} números
                            </span>
                        </div>

                        <a href="{{ route('raffles.show', $raffle->id) }}" class="w-full bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white font-semibold py-3 px-4 rounded-xl transition flex items-center justify-center gap-2">
                            Quero Participar <i class="fa-solid fa-chevron-right text-xs"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="glass-card p-12 text-center rounded-2xl md:col-span-2">
                    <div class="text-slate-500 mb-4">
                        <i class="fa-solid fa-receipt text-5xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Nenhuma rifa ativa encontrada</h3>
                    <p class="text-slate-400 mt-1">Volte mais tarde ou consulte a administração.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
