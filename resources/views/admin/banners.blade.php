@extends('layouts.admin')

@section('title', 'Banners - Ação RR Veículos')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-images" style="color: var(--accent);"></i> Banners
            </h1>
            <p class="text-slate-400 text-sm mt-1">Crie, gere com IA, ative e gerencie os banners da página inicial.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200 space-y-1">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="space-y-6 lg:col-span-1">
            <div class="glass-card rounded-2xl p-6 space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b pb-2" style="border-color: var(--border-color);">
                    <i class="fa-solid fa-wand-magic-sparkles text-purple-400"></i> Gerar Banner com IA
                </h3>
                <form action="{{ route('admin.banners.generate') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-400 font-bold uppercase block">Título do Destaque:</label>
                        <input type="text" name="title" required value="{{ old('title') }}" placeholder="Ex: Ação Promocional do Mustang 68" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-400 font-bold uppercase block">Subtítulo/Descrição:</label>
                        <input type="text" name="subtitle" value="{{ old('subtitle') }}" placeholder="Ex: Números por apenas R$ 5,00" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-400 font-bold uppercase block">Descreva a imagem (Prompt):</label>
                        <textarea name="prompt" rows="3" required placeholder="Ex: Um mustang gt 1968 azul de luxo estacionado em uma pista de corrida ao pôr do sol, estilo cinematográfico" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none">{{ old('prompt') }}</textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2 rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Gerar com IA
                    </button>
                </form>
            </div>

            <div class="glass-card rounded-2xl p-6 space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b pb-2" style="border-color: var(--border-color);">
                    <i class="fa-solid fa-plus text-slate-400"></i> Adicionar Banner
                </h3>
                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-400 font-bold uppercase block">Título do Destaque:</label>
                        <input type="text" name="title" required placeholder="Ex: BMW M3 Competition" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-400 font-bold uppercase block">Subtítulo/Descrição:</label>
                        <input type="text" name="subtitle" placeholder="Ex: Ação Promocional no próximo domingo" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-400 font-bold uppercase block">Imagem Desktop (upload):</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-300 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-slate-800 file:text-white">
                        <p class="text-[10px] text-slate-500">Tamanho recomendado: <strong class="text-slate-300">1920 × 700 px</strong> (JPG/PNG, até 5 MB)</p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-400 font-bold uppercase block">Imagem Mobile (upload):</label>
                        <input type="file" name="mobile_image" accept="image/*" class="w-full text-xs text-slate-300 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-slate-800 file:text-white">
                        <p class="text-[10px] text-slate-500">Tamanho recomendado: <strong class="text-slate-300">1080 × 1350 px</strong> (retrato, JPG/PNG, até 5 MB)</p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-400 font-bold uppercase block">Ou URL da imagem desktop (opcional):</label>
                        <input type="url" name="image_url" placeholder="https://exemplo.com/imagem.jpg" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none">
                    </div>
                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-semibold py-2 rounded-xl text-xs transition border border-slate-700">
                        Salvar Banner
                    </button>
                </form>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-images" style="color: var(--accent);"></i> Banners cadastrados
                </h3>
                <span class="text-xs text-slate-500">{{ $banners->count() }} no total</span>
            </div>

            <div class="space-y-4 max-h-[720px] overflow-y-auto pr-2">
                @forelse($banners as $banner)
                    <div class="flex items-center gap-4 bg-slate-900/50 p-4 rounded-xl border relative group" style="border-color: var(--border-color);">
                        <div class="w-24 h-16 rounded-lg overflow-hidden bg-slate-950 flex-shrink-0">
                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-white truncate">{{ $banner->title }}</h4>
                            <p class="text-xs text-slate-400 truncate">{{ $banner->subtitle }}</p>
                            @if($banner->prompt)
                                <span class="inline-block text-[9px] text-purple-400 bg-purple-500/10 border border-purple-500/20 px-1.5 py-0.5 rounded mt-1 max-w-full truncate" title="{{ $banner->prompt }}">
                                    IA Prompt: {{ $banner->prompt }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <form action="{{ route('admin.banners.toggle', $banner->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $banner->active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-500 border border-slate-700' }}">
                                    {{ $banner->active ? 'Ativo' : 'Inativo' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Excluir este banner?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-red-400 border border-red-500/30 hover:bg-red-500/10 transition">
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500">
                        <i class="fa-solid fa-image text-3xl mb-2"></i>
                        <p class="text-xs">Nenhum banner cadastrado ainda.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
