@extends('layouts.admin')

@section('title', 'Editar Ação Promocional - Ação RR Veículos')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.dashboard') }}" class="text-slate-400 hover:text-white transition">
            <i class="fa-solid fa-chevron-left"></i> Voltar
        </a>
    </div>

    <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square" style="color: var(--accent);"></i> Editar Ação Promocional
            </h1>
            <p class="text-slate-400 text-sm mt-1">
                Altere os detalhes do prêmio, preços e gerencie a galeria de imagens.
            </p>
        </div>

        <form action="{{ route('admin.raffles.update', $raffle->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Título -->
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-300">Título da Ação Promocional:</label>
                <input type="text" name="title" required value="{{ $raffle->title }}" placeholder="Ex: Gol Quadrado AP Turbo" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-slate-700">
            </div>

            <!-- Descrição -->
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-300">Descrição Detalhada:</label>
                <textarea name="description" rows="3" placeholder="Insira os detalhes do carro, regulamento, etc." class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-slate-700">{{ $raffle->description }}</textarea>
            </div>

            <!-- Grid de Valores e Números -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-300">Preço por Número (R$):</label>
                    <input type="number" step="0.01" min="0.01" name="price" required value="{{ $raffle->price }}" placeholder="10.00" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-slate-700">
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-300">Quantidade de Números:</label>
                    <select name="total_numbers" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-slate-700">
                        <option value="50" {{ $raffle->total_numbers == 50 ? 'selected' : '' }}>50 números</option>
                        <option value="100" {{ $raffle->total_numbers == 100 ? 'selected' : '' }}>100 números</option>
                        <option value="200" {{ $raffle->total_numbers == 200 ? 'selected' : '' }}>200 números</option>
                        <option value="500" {{ $raffle->total_numbers == 500 ? 'selected' : '' }}>500 números</option>
                        <option value="1000" {{ $raffle->total_numbers == 1000 ? 'selected' : '' }}>1000 números</option>
                    </select>
                </div>
            </div>

            <!-- Prêmio -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-300">Nome do Prêmio:</label>
                    <input type="text" name="prize_name" required value="{{ $raffle->prize_name }}" placeholder="Ex: Gol Quadrado 1.8 AP Turbo" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-slate-700">
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-300">Data e Hora da Ação Promocional:</label>
                    <input type="datetime-local" name="draw_date" required value="{{ $raffle->draw_date->format('Y-m-d\TH:i') }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-slate-700">
                </div>
            </div>

            <!-- Galeria de Imagens Existentes -->
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-300">Galeria de Fotos do Carro (Reordene ou remova):</label>
                <div class="space-y-2.5" id="image-list-container">
                    @if(!empty($raffle->images))
                        @foreach($raffle->images as $index => $image)
                            <div class="flex items-center gap-3 p-3 bg-slate-950 rounded-xl border border-slate-850 image-item" data-path="{{ $image }}">
                                <img src="{{ $image }}" class="w-12 h-12 object-cover rounded-lg border border-slate-800">
                                <span class="text-xs text-slate-300 truncate flex-grow">{{ basename($image) }}</span>
                                <input type="hidden" name="existing_images[]" value="{{ $image }}">
                                <div class="flex gap-1.5">
                                    <button type="button" onclick="moveImageUp(this)" class="p-1.5 bg-slate-900 text-slate-400 hover:text-white rounded-lg border border-slate-800 transition" title="Mover para cima">
                                        <i class="fa-solid fa-arrow-up text-xs"></i>
                                    </button>
                                    <button type="button" onclick="moveImageDown(this)" class="p-1.5 bg-slate-900 text-slate-400 hover:text-white rounded-lg border border-slate-800 transition" title="Mover para baixo">
                                        <i class="fa-solid fa-arrow-down text-xs"></i>
                                    </button>
                                    <button type="button" onclick="removeImageItem(this)" class="p-1.5 bg-red-950/40 text-red-400 hover:bg-red-900/60 rounded-lg border border-red-900/30 transition" title="Remover">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-xs text-slate-500 py-4">Nenhuma imagem cadastrada.</div>
                    @endif
                </div>
            </div>

            <!-- Adicionar Novas Imagens -->
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-300">Adicionar Novas Fotos (Upload):</label>
                <input type="file" name="new_images[]" multiple accept="image/*" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-400 focus:outline-none focus:border-slate-700 file:mr-4 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-600/20 file:text-blue-400 hover:file:bg-blue-600/30">
            </div>

            <!-- Link do Vídeo do YouTube -->
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-300">Link de Vídeo do YouTube (Opcional):</label>
                <input type="url" name="youtube_url" value="{{ $raffle->youtube_url }}" placeholder="Ex: https://www.youtube.com/watch?v=dQw4w9WgXcQ" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-slate-700">
            </div>

            <button type="submit" class="w-full text-white font-bold py-3.5 px-4 rounded-xl transition flex items-center justify-center gap-2 shadow-lg" style="background-color: var(--accent);">
                <i class="fa-solid fa-floppy-disk"></i> Salvar Alterações
            </button>
        </form>
    </div>
</div>

<script>
function moveImageUp(button) {
    const item = button.closest('.image-item');
    const previous = item.previousElementSibling;
    if (previous && previous.classList.contains('image-item')) {
        item.parentNode.insertBefore(item, previous);
    }
}

function moveImageDown(button) {
    const item = button.closest('.image-item');
    const next = item.nextElementSibling;
    if (next && next.classList.contains('image-item')) {
        item.parentNode.insertBefore(next, item);
    }
}

function removeImageItem(button) {
    const item = button.closest('.image-item');
    item.remove();
    
    const container = document.getElementById('image-list-container');
    if (container.querySelectorAll('.image-item').length === 0) {
        container.innerHTML = '<div class="text-center text-xs text-slate-500 py-4">Nenhuma imagem cadastrada.</div>';
    }
}
</script>
@endsection
