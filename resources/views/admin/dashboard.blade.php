@extends('layouts.admin')

@section('title', 'Dashboard Admin - Ação RR Veículos')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-800 pb-6">
        <div>
            <h1 class="text-3xl font-extrabold font-display text-white flex items-center gap-2">
                <i class="fa-solid fa-chart-line" style="color: var(--accent);"></i> Painel Administrativo
            </h1>
            <p class="text-slate-400 text-sm mt-1">
                Acompanhe as vendas, receitas e realize Ações Promocionais em tempo real.
            </p>
        </div>
        <a href="{{ route('admin.raffles.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-5 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Criar Nova Ação Promocional
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

        <!-- Ações Promocionais Ativas -->
        <div class="glass-card rounded-xl p-6 flex items-center gap-4">
            <div class="p-3.5 bg-purple-500/10 border border-purple-500/20 text-purple-400 rounded-lg text-2xl">
                <i class="fa-solid fa-car"></i>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Ações Promocionais Ativas</div>
                <div class="text-2xl font-bold text-white mt-1">{{ $kpis['active_raffles'] }}</div>
            </div>
        </div>
    </div>

    <!-- Gráficos & Relatórios -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Vendas Chart (2/3 colunas) -->
        <div class="glass-card rounded-2xl p-6 lg:col-span-2 space-y-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fa-regular fa-chart-bar text-blue-500"></i> Desempenho de Vendas
            </h3>
            <div class="h-64 relative">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Atividades Recentes (1/3 coluna) -->
        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-blue-500"></i> Atividades Recentes
            </h3>
            <div class="space-y-4 text-xs max-h-64 overflow-y-auto pr-2">
                <div class="flex gap-3 border-b border-slate-800/80 pb-3">
                    <span class="text-emerald-400 font-bold bg-emerald-500/10 px-2 py-0.5 rounded h-fit">PIX</span>
                    <div>
                        <div class="text-white font-medium">Nova compra confirmada</div>
                        <div class="text-slate-500 mt-0.5">Cliente Teste comprou 3 bilhetes</div>
                    </div>
                </div>
                <div class="flex gap-3 border-b border-slate-800/80 pb-3">
                    <span class="text-blue-400 font-bold bg-blue-500/10 px-2 py-0.5 rounded h-fit">AÇÃO</span>
                    <div>
                        <div class="text-white font-medium">Nova Ação Promocional Publicada</div>
                        <div class="text-slate-500 mt-0.5">Gol Quadrado AP Turbo criada por Admin</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Ações Promocionais -->
    <div class="space-y-4">
        <h2 class="text-xl font-bold text-white flex items-center gap-2">
            <span class="w-2.5 h-5 bg-blue-500 rounded-full"></span>
            Gerenciar Ações Promocionais
        </h2>

        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase tracking-wider bg-slate-900/50">
                            <th class="px-6 py-4">Ação Promocional</th>
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
                                    R$ {{ number_format($raffle->startingPrice(), 2, ',', '.') }}
                                    <span class="block text-[10px] text-slate-500 font-normal">a partir de</span>
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
                                    <div class="flex flex-wrap gap-2 items-center">
                                        @if($raffle->status === 'active')
                                            <a href="{{ route('admin.raffles.draw', $raffle->id) }}" class="bg-amber-600 hover:bg-amber-500 text-amber-950 font-bold px-3 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5 w-fit">
                                                <i class="fa-solid fa-circle-play"></i> Apurar
                                            </a>
                                        @else
                                            @php $raffle->load('draw'); @endphp
                                            <div class="text-xs text-slate-400">
                                                Ganhou: <strong class="text-white">{{ $raffle->draw?->winning_number }}</strong>
                                            </div>
                                        @endif
                                        <a href="{{ route('admin.raffles.edit', $raffle->id) }}" class="bg-slate-800 hover:bg-slate-700 text-white font-semibold px-2.5 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5 w-fit border border-slate-700">
                                            <i class="fa-solid fa-pen-to-square"></i> Editar
                                        </a>
                                        <form action="{{ route('admin.raffles.destroy.request', $raffle->id) }}" method="POST" onsubmit="return confirm('Solicitar exclusão desta Ação Promocional? Um código será enviado ao e-mail do administrador.');">
                                            @csrf
                                            <button type="submit" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 font-semibold px-2.5 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5 w-fit border border-red-500/30">
                                                <i class="fa-solid fa-trash"></i> Excluir
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                    Nenhuma Ação Promocional cadastrada ainda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    // Gradiente para a linha
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(225, 29, 46, 0.4)');
    gradient.addColorStop(1, 'rgba(225, 29, 46, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
            datasets: [{
                label: 'Receita Diária (R$)',
                data: [350, 480, 220, 690, 820, 1100, 950],
                borderColor: '#e11d2e',
                borderWidth: 3,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#ff4d5a',
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)'
                    },
                    ticks: {
                        color: '#94a3b8'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#94a3b8'
                    }
                }
            }
        }
    });
});
</script>
@endsection
