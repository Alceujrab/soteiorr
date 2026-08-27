@php
    $packageRows = old('packages', isset($raffle) && $raffle->packages->isNotEmpty()
        ? $raffle->packages->map(fn ($p) => [
            'name' => $p->name,
            'numbers_qty' => $p->numbers_qty,
            'price' => $p->price,
            'highlight' => $p->highlight,
            'is_featured' => $p->is_featured,
            'allows_selection' => $p->allows_selection,
        ])->values()->all()
        : ($defaultPackages ?? \App\Models\RafflePackage::defaultDefinitions()));
@endphp

<div class="space-y-3">
    <div class="flex items-center justify-between gap-3">
        <label class="text-sm font-semibold text-slate-300">Planos / Pacotes de pagamento:</label>
        <button type="button" onclick="addPackageRow()" class="text-xs font-bold px-3 py-1.5 rounded-lg border text-white hover:bg-white/5 transition" style="border-color: var(--border-color);">
            + Pacote
        </button>
    </div>
    <p class="text-[11px] text-slate-500">Defina os pacotes oferecidos na compra. O preço da ação será o menor pacote. Marque “Escolher nº” para permitir seleção manual.</p>

    <div id="packages-list" class="space-y-3">
        @foreach($packageRows as $index => $package)
            <div class="package-row grid grid-cols-1 sm:grid-cols-12 gap-2 p-3 rounded-xl border bg-slate-950/40" style="border-color: var(--border-color);">
                <div class="sm:col-span-3 space-y-1">
                    <label class="text-[10px] uppercase text-slate-500 font-bold">Nome</label>
                    <input type="text" name="packages[{{ $index }}][name]" value="{{ $package['name'] ?? '' }}" required class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200">
                </div>
                <div class="sm:col-span-2 space-y-1">
                    <label class="text-[10px] uppercase text-slate-500 font-bold">Números</label>
                    <input type="number" min="1" name="packages[{{ $index }}][numbers_qty]" value="{{ $package['numbers_qty'] ?? 1 }}" required class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200">
                </div>
                <div class="sm:col-span-2 space-y-1">
                    <label class="text-[10px] uppercase text-slate-500 font-bold">Preço (R$)</label>
                    <input type="number" step="0.01" min="0.01" name="packages[{{ $index }}][price]" value="{{ $package['price'] ?? '' }}" required class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200">
                </div>
                <div class="sm:col-span-2 space-y-1">
                    <label class="text-[10px] uppercase text-slate-500 font-bold">Destaque texto</label>
                    <input type="text" name="packages[{{ $index }}][highlight]" value="{{ $package['highlight'] ?? '' }}" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200">
                </div>
                <div class="sm:col-span-3 flex flex-col justify-end gap-2 pb-1">
                    <label class="inline-flex items-center gap-1.5 text-[11px] text-slate-400">
                        <input type="checkbox" name="packages[{{ $index }}][is_featured]" value="1" {{ !empty($package['is_featured']) ? 'checked' : '' }} class="rounded border-slate-700">
                        Em destaque
                    </label>
                    <label class="inline-flex items-center gap-1.5 text-[11px] text-slate-400">
                        <input type="checkbox" name="packages[{{ $index }}][allows_selection]" value="1" {{ !empty($package['allows_selection']) ? 'checked' : '' }} class="rounded border-slate-700">
                        Escolher nº
                    </label>
                    <button type="button" onclick="this.closest('.package-row').remove()" class="text-red-400 hover:text-red-300 text-xs self-start" title="Remover">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>

<template id="package-row-template">
    <div class="package-row grid grid-cols-1 sm:grid-cols-12 gap-2 p-3 rounded-xl border bg-slate-950/40" style="border-color: var(--border-color);">
        <div class="sm:col-span-3 space-y-1">
            <label class="text-[10px] uppercase text-slate-500 font-bold">Nome</label>
            <input type="text" data-name="name" required class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200">
        </div>
        <div class="sm:col-span-2 space-y-1">
            <label class="text-[10px] uppercase text-slate-500 font-bold">Números</label>
            <input type="number" min="1" data-name="numbers_qty" value="20" required class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200">
        </div>
        <div class="sm:col-span-2 space-y-1">
            <label class="text-[10px] uppercase text-slate-500 font-bold">Preço (R$)</label>
            <input type="number" step="0.01" min="0.01" data-name="price" value="9.90" required class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200">
        </div>
        <div class="sm:col-span-2 space-y-1">
            <label class="text-[10px] uppercase text-slate-500 font-bold">Destaque texto</label>
            <input type="text" data-name="highlight" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200">
        </div>
        <div class="sm:col-span-3 flex flex-col justify-end gap-2 pb-1">
            <label class="inline-flex items-center gap-1.5 text-[11px] text-slate-400">
                <input type="checkbox" data-name="is_featured" value="1" class="rounded border-slate-700">
                Em destaque
            </label>
            <label class="inline-flex items-center gap-1.5 text-[11px] text-slate-400">
                <input type="checkbox" data-name="allows_selection" value="1" class="rounded border-slate-700">
                Escolher nº
            </label>
            <button type="button" onclick="this.closest('.package-row').remove()" class="text-red-400 hover:text-red-300 text-xs self-start" title="Remover">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    </div>
</template>

<script>
    function addPackageRow() {
        const list = document.getElementById('packages-list');
        const template = document.getElementById('package-row-template');
        const index = list.querySelectorAll('.package-row').length;
        const node = template.content.cloneNode(true);

        node.querySelectorAll('[data-name]').forEach((input) => {
            const field = input.getAttribute('data-name');
            input.setAttribute('name', `packages[${index}][${field}]`);
            input.removeAttribute('data-name');
        });

        list.appendChild(node);
    }
</script>
