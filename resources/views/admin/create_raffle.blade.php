@extends('layouts.admin')

@section('title', 'Criar Nova Rifa - Ação RR Veículos')

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
                <i class="fa-solid fa-plus-circle text-blue-500"></i> Criar Nova Rifa
            </h1>
            <p class="text-slate-400 text-sm mt-1">
                Insira as informações do prêmio e configure os valores para o sorteio.
            </p>
        </div>

        <form action="{{ route('admin.raffles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Título -->
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-300">Título da Rifa:</label>
                <input type="text" name="title" required placeholder="Ex: Gol Quadrado AP Turbo" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-slate-700">
            </div>

            <!-- Descrição -->
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-300">Descrição Detalhada:</label>
                <textarea name="description" rows="3" placeholder="Insira os detalhes do carro, regulamento, etc." class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-slate-700"></textarea>
            </div>

            <!-- Grid de Valores e Números -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-300">Preço por Número (R$):</label>
                    <input type="number" step="0.01" min="0.01" name="price" required placeholder="10.00" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-slate-700">
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-300">Quantidade de Números:</label>
                    <select name="total_numbers" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-slate-700">
                        <option value="50">50 números</option>
                        <option value="100" selected>100 números</option>
                        <option value="200">200 números</option>
                        <option value="500">500 números</option>
                        <option value="1000">1000 números</option>
                    </select>
                </div>
            </div>

            <!-- Prêmio -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-300">Nome do Prêmio:</label>
                    <input type="text" name="prize_name" required placeholder="Ex: Gol Quadrado 1.8 AP Turbo" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-slate-700">
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-300">Data e Hora do Sorteio:</label>
                    <input type="datetime-local" name="draw_date" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-slate-700">
                </div>
            </div>

            <!-- Upload de Múltiplas Imagens do Prêmio -->
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-300">Imagens do Prêmio (Upload - Selecione múltiplas se desejar):</label>
                <input type="file" name="images[]" multiple accept="image/*" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-400 focus:outline-none focus:border-slate-700 file:mr-4 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-600/20 file:text-blue-400 hover:file:bg-blue-600/30">
                <p class="text-[10px] text-slate-500">Dica: Selecione várias imagens ao mesmo tempo para criar uma galeria de fotos.</p>
            </div>

            <!-- Link do Vídeo do YouTube -->
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-300">Link de Vídeo do YouTube (Opcional):</label>
                <input type="url" name="youtube_url" placeholder="Ex: https://www.youtube.com/watch?v=dQw4w9WgXcQ" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-slate-700">
            </div>

            <button type="submit" class="w-full text-white font-bold py-3.5 px-4 rounded-xl transition flex items-center justify-center gap-2" style="background-color: var(--accent);">
                <i class="fa-solid fa-plus-circle"></i> Salvar e Publicar Rifa
            </button>
        </form>
    </div>
</div>
@endsection
