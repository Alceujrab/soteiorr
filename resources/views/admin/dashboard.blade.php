@extends('layouts.app')

@section('title', 'Dashboard Admin - Ação RR Veículos')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-800 pb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-blue-500"></i> Painel Administrativo
            </h1>
            <p class="text-slate-400 text-sm mt-1">
                Acompanhe as vendas, receitas e realize sorteios em tempo real.
            </p>
        </div>
        <a href="{{ route('admin.raffles.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-5 py-2.5 rounded-lg transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Criar Nova Rifa
        </a>
    </div>

    <!-- KPIs Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Receita Total -->
        <div class="glass-card rounded-xl p-6 flex items-center gap-4">
            <div class="p-3.5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-lg text-2xl">
                <i class="fa-solid fa-dollar-sign"></i>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Receita Total</div>
                <div class="text-2xl font-bold text-white mt-1">R$ {{ number_format($kpis['total_revenue'], 2, ',', '.') }}</div>
            </div>
        </div>

        <!-- Bilhetes Vendidos -->
        <div class="glass-card rounded-xl p-6 flex items-center gap-4">
            <div class="p-3.5 bg-blue-500/10 border border-blue-500/20 text-blue-400 rounded-lg text-2xl">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Bilhetes Pagos</div>
                <div class="text-2xl font-bold text-white mt-1">{{ $kpis['total_sales'] }}</div>
            </div>
        </div>

        <!-- Participantes -->
        <div class="glass-card rounded-xl p-6 flex items-center gap-4">
            <div class="p-3.5 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 rounded-lg text-2xl">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Participantes</div>
                <div class="text-2xl font-bold text-white mt-1">{{ $kpis['total_participants'] }}</div>
            </div>
        </div>

        <!-- Rifas Ativas -->
        <div class="glass-card rounded-xl p-6 flex items-center gap-4">
            <div class="p-3.5 bg-purple-500/10 border border-purple-500/20 text-purple-400 rounded-lg text-2xl">
                <i class="fa-solid fa-car"></i>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Rifas Ativas</div>
                <div class="text-2xl font-bold text-white mt-1">{{ $kpis['active_raffles'] }}</div>
            </div>
        </div>
    </div>

    <!-- Rifas List -->
    <div class="space-y-4">
        <h2 class="text-xl font-bold text-white flex items-center gap-2">
            <span class="w-2.5 h-5 bg-blue-500 rounded-full"></span>
            Gerenciar Sorteios
        </h2>

        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase tracking-wider bg-slate-900/50">
                            <th class="px-6 py-4">Rifa</th>
                            <th class="px-6 py-4">Prêmio</th>
                            <th class="px-6 py-4">Valor Núm.</th>
                            <th class="px-6 py-4">Vendas</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-sm text-slate-300">
                        @forelse($raffles as $raffle)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="px-6 py-4 font-semibold text-white">
                                    {{ $raffle->title }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $raffle->prize_name }}
                                </td>
                                <td class="px-6 py-4 text-emerald-400 font-medium">
                                    R$ {{ number_format($raffle->price, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-white">{{ $raffle->tickets_count }}</span>
                                        <span class="text-slate-500">/ {{ $raffle->total_numbers }}</span>
                                        @php
                                            $percent = $raffle->total_numbers > 0 ? ($raffle->tickets_count / $raffle->total_numbers) * 100 : 0;
                                        @endphp
                                        <div class="w-16 bg-slate-800 rounded-full h-1.5 overflow-hidden hidden sm:block">
                                            <div class="bg-blue-500 h-1.5" style="width: {{ $percent }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $raffle->status === 'active' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-blue-500/10 text-blue-400 border border-blue-500/20' }}">
                                        {{ $raffle->status === 'active' ? 'Ativa' : 'Encerrada' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($raffle->status === 'active')
                                        <a href="{{ route('admin.raffles.draw', $raffle->id) }}" class="bg-amber-600 hover:bg-amber-500 text-amber-950 font-bold px-3 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5 w-fit">
                                            <i class="fa-solid fa-circle-play"></i> Realizar Sorteio
                                        </a>
                                    @else
                                        @php $raffle->load('draw'); @endphp
                                        <div class="text-xs text-slate-400">
                                            Vencedor: <strong class="text-white">{{ $raffle->draw?->winning_number }}</strong>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                    Nenhuma rifa cadastrada ainda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
