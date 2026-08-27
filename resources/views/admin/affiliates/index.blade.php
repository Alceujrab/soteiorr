@extends('layouts.admin')

@section('title', 'Afiliados')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-white">Afiliados</h1>
        <p class="text-sm text-slate-400 mt-1">
            Links no formato <code class="text-slate-300">?ref=CODIGO</code>. Comissão estimada: {{ number_format($commissionRate, 1, ',', '.') }}% (configurável em settings: affiliate_commission_percent).
        </p>
    </div>

    <div class="glass-card rounded-2xl border overflow-hidden" style="border-color: var(--border-color);">
        <table class="w-full text-sm">
            <thead class="text-left text-xs uppercase text-slate-500 border-b" style="border-color: var(--border-color);">
                <tr>
                    <th class="px-4 py-3">Afiliado</th>
                    <th class="px-4 py-3">Código / Link</th>
                    <th class="px-4 py-3">Indicações</th>
                    <th class="px-4 py-3">Volume pago</th>
                    <th class="px-4 py-3">Comissão est.</th>
                </tr>
            </thead>
            <tbody>
                @forelse($affiliates as $affiliate)
                    <tr class="border-b border-slate-800/60">
                        <td class="px-4 py-3 text-white">
                            {{ $affiliate->name }}
                            <div class="text-[11px] text-slate-500">{{ $affiliate->email }} · {{ $affiliate->role }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-mono text-emerald-400">{{ $affiliate->affiliate_code }}</div>
                            <div class="text-[11px] text-slate-500 break-all">{{ url('/?ref='.$affiliate->affiliate_code) }}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-300">{{ $affiliate->approved_count }} / {{ $affiliate->referred_count }}</td>
                        <td class="px-4 py-3 text-slate-300">R$ {{ number_format((float) ($affiliate->approved_volume ?? 0), 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-emerald-400 font-semibold">R$ {{ number_format((float) $affiliate->estimated_commission, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">Nenhum afiliado com código ainda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="glass-card rounded-2xl border p-5 space-y-3" style="border-color: var(--border-color);">
        <h2 class="text-white font-bold">Gerar código</h2>
        <div class="space-y-2">
            @forelse($candidates as $candidate)
                <form method="POST" action="{{ route('admin.affiliates.code', $candidate) }}" class="flex items-center justify-between gap-3 py-2 border-b border-slate-800/50">
                    @csrf
                    <div class="text-sm text-slate-300">
                        {{ $candidate->name }}
                        <span class="text-slate-500 text-xs">{{ $candidate->email }} · {{ $candidate->role }}</span>
                    </div>
                    <button class="text-xs font-bold px-3 py-1.5 rounded-lg" style="background: var(--accent); color: var(--on-accent);">Gerar</button>
                </form>
            @empty
                <p class="text-sm text-slate-500">Todos os candidatos já possuem código.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
