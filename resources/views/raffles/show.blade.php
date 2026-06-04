@extends('layouts.app')

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

                <div class="border-t border-slate-800 pt-4 space-y-3">
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

        <!-- Selections Panel / Cart (Sticks below info on scroll) -->
        <div id="selection-panel" class="glass-card rounded-2xl p-6 hidden space-y-4">
            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                <i class="fa-solid fa-cart-shopping text-blue-500"></i> Números Selecionados
            </h3>
            <div id="selected-list" class="flex flex-wrap gap-2 max-h-32 overflow-y-auto pr-2">
                <!-- Selected items badge will go here -->
            </div>
            <div class="border-t border-slate-800 pt-4 flex justify-between items-center">
                <div>
                    <div class="text-xs text-slate-400">Valor Total:</div>
                    <div class="text-xl font-bold text-emerald-400" id="total-amount">R$ 0,00</div>
                </div>
            </div>
            <form action="{{ route('raffles.buy', $raffle->id) }}" method="POST" id="buy-form">
                @csrf
                <div id="hidden-inputs-container"></div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-3 px-4 rounded-xl transition flex items-center justify-center gap-2">
                    Reservar Números <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Numbers Grid (Right Column - 2 parts) -->
    <div class="lg:col-span-2 space-y-6">
        <div class="glass-card rounded-2xl p-6 space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="text-xl font-bold text-white">Escolha seus números da sorte</h2>
                <!-- Legenda -->
                <div class="flex flex-wrap items-center gap-4 text-xs">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded bg-slate-800 border border-slate-700 inline-block"></span>
                        <span class="text-slate-400">Livre</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded bg-blue-600 inline-block"></span>
                        <span class="text-slate-400">Selecionado</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded bg-amber-500/35 border border-amber-500/50 inline-block"></span>
                        <span class="text-slate-400">Reservado</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded bg-red-500/30 border border-red-500/40 inline-block"></span>
                        <span class="text-slate-400">Pago / Ocupado</span>
                    </div>
                </div>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-5 sm:grid-cols-10 gap-2.5 select-none" id="numbers-grid">
                @for($n = 1; $n <= $raffle->total_numbers; $n++)
                    @php
                        $ticket = $takenTickets->get($n);
                        $statusClass = 'bg-slate-800 hover:bg-slate-700 border-slate-700 text-slate-300 cursor-pointer number-item';
                        $disabled = false;
                        
                        if ($ticket) {
                            if ($ticket->status === 'paid') {
                                $statusClass = 'bg-red-500/20 border-red-500/30 text-red-500/70 border cursor-not-allowed';
                                $disabled = true;
                            } elseif ($ticket->status === 'reserved') {
                                $statusClass = 'bg-amber-500/20 border-amber-500/30 text-amber-500/70 border cursor-not-allowed';
                                $disabled = true;
                            }
                        }
                    @endphp
                    <div data-number="{{ $n }}" 
                         data-disabled="{{ $disabled ? 'true' : 'false' }}"
                         class="aspect-square flex items-center justify-center rounded-lg border font-bold text-sm sm:text-base transition {{ $statusClass }}">
                        {{ sprintf('%02d', $n) }}
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const numberItems = document.querySelectorAll('.number-item');
    const selectionPanel = document.getElementById('selection-panel');
    const selectedList = document.getElementById('selected-list');
    const totalAmountEl = document.getElementById('total-amount');
    const hiddenInputsContainer = document.getElementById('hidden-inputs-container');
    const buyForm = document.getElementById('buy-form');
    
    const pricePerNumber = {{ $raffle->price }};
    let selectedNumbers = [];

    numberItems.forEach(item => {
        item.addEventListener('click', function() {
            if (this.getAttribute('data-disabled') === 'true') return;

            const number = parseInt(this.getAttribute('data-number'));
            
            if (selectedNumbers.includes(number)) {
                // Deselecionar
                selectedNumbers = selectedNumbers.filter(n => n !== number);
                this.classList.remove('bg-blue-600', 'border-blue-500', 'text-white');
                this.classList.add('bg-slate-800', 'hover:bg-slate-700', 'border-slate-700', 'text-slate-300');
            } else {
                // Selecionar
                selectedNumbers.push(number);
                this.classList.remove('bg-slate-800', 'hover:bg-slate-700', 'border-slate-700', 'text-slate-300');
                this.classList.add('bg-blue-600', 'border-blue-500', 'text-white');
            }

            updateSelectionPanel();
        });
    });

    function updateSelectionPanel() {
        if (selectedNumbers.length === 0) {
            selectionPanel.classList.add('hidden');
            return;
        }

        selectionPanel.classList.remove('hidden');
        
        // Renderizar Badges
        selectedList.innerHTML = '';
        selectedNumbers.sort((a,b) => a - b).forEach(number => {
            const badge = document.createElement('span');
            badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 bg-blue-500/20 text-blue-400 border border-blue-500/30 rounded-lg text-sm font-semibold';
            badge.innerHTML = `${String(number).padStart(2, '0')} <i class="fa-solid fa-xmark text-xs cursor-pointer text-blue-400/70 hover:text-white" onclick="removeNumber(${number})"></i>`;
            selectedList.appendChild(badge);
        });

        // Atualizar Valor Total
        const total = selectedNumbers.length * pricePerNumber;
        totalAmountEl.textContent = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(total);

        // Preencher inputs escondidos
        hiddenInputsContainer.innerHTML = '';
        selectedNumbers.forEach(number => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'numbers[]';
            input.value = number;
            hiddenInputsContainer.appendChild(input);
        });
    }

    // Expor função globalmente para poder remover via botão do badge
    window.removeNumber = function(number) {
        selectedNumbers = selectedNumbers.filter(n => n !== number);
        const item = document.querySelector(`.number-item[data-number="${number}"]`);
        if (item) {
            item.classList.remove('bg-blue-600', 'border-blue-500', 'text-white');
            item.classList.add('bg-slate-800', 'hover:bg-slate-700', 'border-slate-700', 'text-slate-300');
        }
        updateSelectionPanel();
    };
});
</script>
@endsection
