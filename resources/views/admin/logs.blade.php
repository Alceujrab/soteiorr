@extends('layouts.admin')

@section('title', 'Logs de Auditoria - Ação RR Veículos')

@section('content')
<div class="space-y-8">
    <div class="border-b border-slate-800 pb-6">
        <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
            <i class="fa-solid fa-shield-halved text-blue-500"></i> Logs de Auditoria & Compliance
        </h1>
        <p class="text-slate-400 text-sm mt-1">
            Registro de todas as ações importantes realizadas por usuários administrativos.
        </p>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase tracking-wider bg-slate-900/50">
                        <th class="px-6 py-4">Usuário</th>
                        <th class="px-6 py-4">Ação</th>
                        <th class="px-6 py-4">IP</th>
                        <th class="px-6 py-4">Navegador</th>
                        <th class="px-6 py-4">Data / Hora</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-sm text-slate-300">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 font-semibold text-white">
                                {{ $log->user?->name ?: 'Visitante / Sistema' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $log->action }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs">
                                {{ $log->ip_address }}
                            </td>
                            <td class="px-6 py-4 text-xs max-w-xs truncate" title="{{ $log->user_agent }}">
                                {{ $log->user_agent }}
                            </td>
                            <td class="px-6 py-4 text-xs">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                Nenhum log registrado até o momento.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/20">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
