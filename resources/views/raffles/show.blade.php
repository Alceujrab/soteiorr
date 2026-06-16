@extends('layouts.public')

@section('title', $raffle->title . ' - Detalhes da Rifa')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Raffle Info (Left Column - 1 part) -->
    <div class="space-y-6 lg:col-span-1">
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="h-64 bg-slate-950 relative">
                <img src="{{ $raffle->image_url }}" alt="{{ $raffle->title }}" class="w-full h-full object-cover">
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
                        <span class="text-slate-400">Preço por número:</span>
                        <strong class="text-emerald-400 font-bold">R$ {{ number_format($raffle->price, 2, ',', '.') }}</strong>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Total de números:</span>
                        <span class="text-white">{{ $raffle->total_numbers }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Data do Sorteio:</span>
                        <span class="text-white font-medium">{{ $raffle->draw_date->format('d/m/Y \à\s H:i') }}</span>
                    </div>
                    @if(\App\Models\Setting::get('show_sold_qty', '1') === '1')
                        <div class="space-y-1.5 pt-2 border-t" style="border-color: var(--border-color);">
                            <div class="flex justify-between text-xs text-slate-400">
                                <span>Progresso de Vendas:</span>
                                <span class="font-bold text-white">{{ $takenTickets->count() }} / {{ $raffle->total_numbers }} vendidos</span>
                            </div>
                            <div class="w-full bg-slate-900 rounded-full h-2 overflow-hidden border" style="border-color: var(--border-color);">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($takenTickets->count() / $raffle->total_numbers) * 100 }}%"></div>
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
                </div>
            </div>
        </div>

        <!-- Compartilhar Rifa Card -->
        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-share-nodes text-blue-500"></i> Compartilhar Sorteio
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
                <i class="fa-solid fa-wand-magic-sparkles" style="color: var(--accent);"></i> Adquirir Cotas (Escolha Automática)
            </h2>

            <!-- Progresso de Vendas -->
            @if(\App\Models\Setting::get('show_sold_qty', '1') === '1')
                <div class="p-4 bg-slate-950 rounded-xl border space-y-3" style="border-color: var(--border-color);">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400 font-medium">Números Vendidos:</span>
                        <span class="text-white font-bold">{{ $takenTickets->count() }} / {{ $raffle->total_numbers }} vendidos</span>
                    </div>
                    <div class="w-full bg-slate-900 rounded-full h-3 overflow-hidden border" style="border-color: var(--border-color);">
                        <div class="h-3 rounded-full" style="width: {{ ($takenTickets->count() / $raffle->total_numbers) * 100 }}%; background-color: var(--accent);"></div>
                    </div>
                </div>
            @endif

            <!-- Automatic/Surpresinha Mode Container -->
            <div id="container-auto" class="space-y-6">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <button type="button" onclick="selectAutoQty(1)" class="p-4 rounded-xl text-center transition font-extrabold text-white shadow-md hover:opacity-90 transition-all duration-200" style="background-color: var(--accent);">
                        +1 Cota
                    </button>
                    <button type="button" onclick="selectAutoQty(5)" class="p-4 rounded-xl text-center transition font-extrabold text-white shadow-md hover:opacity-90 transition-all duration-200" style="background-color: var(--accent);">
                        +5 Cotas
                    </button>
                    <button type="button" onclick="selectAutoQty(10)" class="p-4 rounded-xl text-center transition font-extrabold text-white shadow-md hover:opacity-90 transition-all duration-200" style="background-color: var(--accent);">
                        +10 Cotas
                    </button>
                    <button type="button" onclick="selectAutoQty(20)" class="p-4 rounded-xl text-center transition font-extrabold text-white shadow-md hover:opacity-90 transition-all duration-200" style="background-color: var(--accent);">
                        +20 Cotas
                    </button>
                </div>

                <div class="border-t my-6" style="border-color: var(--border-color);"></div>

                <form action="{{ route('raffles.buy', $raffle->id) }}" method="POST" class="space-y-4 max-w-md">
                    @csrf
                    <input type="hidden" name="mode" value="auto">
                    
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-400 font-semibold uppercase">Quantidade Personalizada:</label>
                        <input type="number" id="auto-qty-input" name="quantity" min="1" max="100" value="1" oninput="updateAutoPrice()" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none">
                    </div>

                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-slate-400 font-semibold">Total a Pagar:</span>
                        <strong class="text-xl font-bold text-emerald-400" id="auto-total-label">R$ 0,00</strong>
                    </div>

                    <button type="submit" class="w-full text-white font-bold py-3.5 px-4 rounded-xl transition flex items-center justify-center gap-2 shadow-lg hover:opacity-95" style="background-color: var(--accent);">
                        Comprar Surpresinha <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pricePerNumber = {{ $raffle->price }};

    window.selectAutoQty = function(qty) {
        const input = document.getElementById('auto-qty-input');
        input.value = qty;
        updateAutoPrice();
    };

    window.updateAutoPrice = function() {
        const input = document.getElementById('auto-qty-input');
        const totalLabel = document.getElementById('auto-total-label');
        const qty = parseInt(input.value) || 0;
        const total = qty * pricePerNumber;
        totalLabel.textContent = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(total);
    };

    updateAutoPrice();
});
</script>
@endsection
