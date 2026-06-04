@extends('layouts.admin')

@section('title', 'Relatórios & Análise - Ação RR Veículos')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-center border-b border-slate-800 pb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-blue-500"></i> Relatórios & Analytics
            </h1>
            <p class="text-slate-400 text-sm mt-1">Consulte gráficos de vendas, conversão e baixe relatórios fiscais.</p>
        </div>
        <button onclick="alert('Exportando CSV de vendas...')" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
            <i class="fa-solid fa-download"></i> Exportar Dados (CSV)
        </button>
    </div>

    <!-- Analytics cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="glass-card rounded-xl p-5">
            <div class="text-slate-400 text-xs font-semibold uppercase">Total Arrecadado</div>
            <div class="text-2xl font-bold text-emerald-400 mt-2">R$ {{ number_format($salesData['total_revenue'], 2, ',', '.') }}</div>
        </div>
        <div class="glass-card rounded-xl p-5">
            <div class="text-slate-400 text-xs font-semibold uppercase">Pendentes de Confirmação</div>
            <div class="text-2xl font-bold text-amber-500 mt-2">R$ {{ number_format($salesData['total_pending'], 2, ',', '.') }}</div>
        </div>
        <div class="glass-card rounded-xl p-5">
            <div class="text-slate-400 text-xs font-semibold uppercase">Cotas Vendidas (Pagas)</div>
            <div class="text-2xl font-bold text-white mt-2">{{ $salesData['total_sales'] }}</div>
        </div>
        <div class="glass-card rounded-xl p-5">
            <div class="text-slate-400 text-xs font-semibold uppercase">Taxa de Conversão</div>
            <div class="text-2xl font-bold text-blue-400 mt-2">{{ $salesData['conversion_rate'] }}</div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Pie Chart -->
        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="font-bold text-white text-base">Distribuição de Formas de Pagamento</h3>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="paymentDistributionChart" class="max-w-[200px]"></canvas>
            </div>
        </div>

        <!-- Monthly Sales Bar Chart -->
        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="font-bold text-white text-base">Receita Acumulada Mensal</h3>
            <div class="h-64 relative">
                <canvas id="monthlyRevenueChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Payment Distribution Chart (Pie)
    new Chart(document.getElementById('paymentDistributionChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: ['Pix', 'Cartão', 'Boleto'],
            datasets: [{
                data: [75, 20, 5],
                backgroundColor: ['#10b981', '#2563eb', '#f59e0b'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#f1f5f9' }
                }
            }
        }
    });

    // 2. Monthly Revenue Chart (Bar)
    new Chart(document.getElementById('monthlyRevenueChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Receita (R$)',
                data: [1200, 1900, 3000, 5000, 4000, 6800],
                backgroundColor: '#2563eb',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } },
                x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
            }
        }
    });
});
</script>
@endsection
