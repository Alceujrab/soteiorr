@extends('layouts.admin')

@section('title', 'Gestão de Usuários - Ação RR Veículos')

@section('content')
<div class="space-y-8">
    <div class="border-b border-slate-800 pb-6">
        <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
            <i class="fa-solid fa-user-gear text-blue-500"></i> Gestão de Usuários & Permissões
        </h1>
        <p class="text-slate-400 text-sm mt-1">
            Gerencie os níveis de acesso de organizadores, gerentes, vendedores e auditores do sistema.
        </p>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase tracking-wider bg-slate-900/50">
                        <th class="px-6 py-4">Nome</th>
                        <th class="px-6 py-4">E-mail</th>
                        <th class="px-6 py-4">CPF</th>
                        <th class="px-6 py-4">Nível de Acesso</th>
                        <th class="px-6 py-4">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-sm text-slate-300">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 font-semibold text-white">
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $user->cpf ?: 'Não informado' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider 
                                    @if($user->role === 'admin_organizador') bg-blue-500/10 text-blue-400 border border-blue-500/20
                                    @elseif($user->role === 'cliente') bg-slate-850 text-slate-400 border border-slate-700
                                    @else bg-purple-500/10 text-purple-400 border border-purple-500/20 @endif">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <button onclick="alert('Funcionalidade de edição de cargo para {{ $user->name }} (Simulado)...')" class="bg-slate-800 hover:bg-slate-700 text-white text-xs px-3 py-1.5 rounded-lg border border-slate-700 transition">
                                    Alterar Nível
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
