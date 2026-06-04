@extends('layouts.admin')

@section('title', 'Participantes - Ação RR Veículos')

@section('content')
<div class="space-y-8">
    <div class="border-b border-slate-800 pb-6">
        <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
            <i class="fa-solid fa-users text-blue-500"></i> Gestão de Participantes
        </h1>
        <p class="text-slate-400 text-sm mt-1">
            Cadastro completo dos clientes, históricos de compras e números adquiridos.
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
                        <th class="px-6 py-4">Telefone</th>
                        <th class="px-6 py-4">Bilhetes Adquiridos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-sm text-slate-300">
                    @forelse($participants as $client)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 font-semibold text-white">
                                {{ $client->name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $client->email }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $client->cpf ?: 'Não cadastrado' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $client->phone ?: 'Não cadastrado' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-blue-500/15 text-blue-400 border border-blue-500/25 rounded-full font-bold text-xs">
                                    {{ $client->tickets_count }} cotas
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                Nenhum participante cadastrado ainda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
