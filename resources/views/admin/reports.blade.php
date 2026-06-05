@extends('layouts.admin')

@section('title', 'Relatórios & Análise - Ação RR Veículos')

@section('content')
<!-- Include SheetJS (for Excel exports) and Html2Pdf (for PDF exports) -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-800 pb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-blue-500"></i> Relatórios & Analytics
            </h1>
            <p class="text-slate-400 text-sm mt-1">Consulte o desempenho financeiro, ranking de compradores e exporte dados oficiais.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="exportToExcel()" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5 shadow">
                <i class="fa-solid fa-file-excel"></i> Exportar Excel (XLSX)
            </button>
            <button onclick="exportToPDF()" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5 shadow">
                <i class="fa-solid fa-file-pdf"></i> Exportar PDF
            </button>
            <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5 border border-slate-700">
                <i class="fa-solid fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- KPI Analytics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="glass-card rounded-2xl p-5 border border-slate-800">
            <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Recebido (Pix/Aprovados)</div>
            <div class="text-2xl font-black text-emerald-400 mt-2">R$ {{ number_format($salesData['total_revenue'], 2, ',', '.') }}</div>
            <span class="text-[10px] text-slate-500 mt-1 block">Aprovação imediata</span>
        </div>
        <div class="glass-card rounded-2xl p-5 border border-slate-800">
            <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">Valores Pendentes (Reservados)</div>
            <div class="text-2xl font-black text-amber-500 mt-2">R$ {{ number_format($salesData['total_pending'], 2, ',', '.') }}</div>
            <span class="text-[10px] text-slate-500 mt-1 block">Expira em 30 min se não pago</span>
        </div>
        <div class="glass-card rounded-2xl p-5 border border-slate-800">
            <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">Cotas Vendidas (Pagas)</div>
            <div class="text-2xl font-black text-white mt-2">{{ $salesData['total_sales'] }}</div>
            <span class="text-[10px] text-slate-500 mt-1 block">Cotas consolidadas no banco</span>
        </div>
        <div class="glass-card rounded-2xl p-5 border border-slate-800">
            <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">Taxa de Conversão de Pix</div>
            <div class="text-2xl font-black text-blue-400 mt-2">{{ $salesData['conversion_rate'] }}</div>
            <span class="text-[10px] text-slate-500 mt-1 block">Pix gerados vs pagos</span>
        </div>
    </div>

    <!-- Módulo de Relatórios e Filtros Avançados -->
    <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden">
        <!-- Abas do Módulo de Relatórios -->
        <div class="flex flex-wrap border-b border-slate-800 bg-slate-900/30">
            <button type="button" onclick="selectReport('vendas')" id="btn-rep-vendas" class="rep-tab-btn flex-1 sm:flex-none px-6 py-4 text-xs font-bold uppercase tracking-wider transition border-b-2 text-white border-blue-500">
                📊 Vendas Detalhadas
            </button>
            <button type="button" onclick="selectReport('desempenho')" id="btn-rep-desempenho" class="rep-tab-btn flex-1 sm:flex-none px-6 py-4 text-xs font-bold uppercase tracking-wider transition border-b-2 text-slate-400 border-transparent hover:text-white">
                🏎️ Desempenho de Rifas
            </button>
            <button type="button" onclick="selectReport('clientes')" id="btn-rep-clientes" class="rep-tab-btn flex-1 sm:flex-none px-6 py-4 text-xs font-bold uppercase tracking-wider transition border-b-2 text-slate-400 border-transparent hover:text-white">
                👥 Ranking de Compradores
            </button>
        </div>

        <div class="p-6 space-y-6">
            <!-- Barra de Filtros Rápidos -->
            <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                <div class="flex flex-wrap gap-3 w-full sm:w-auto">
                    <div class="space-y-1 w-full sm:w-44">
                        <label class="text-[10px] text-slate-400 font-bold uppercase block">Filtro Rápido (Período):</label>
                        <select id="filter-date" onchange="applyFilters()" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none">
                            <option value="all">Todo o Período</option>
                            <option value="7">Últimos 7 dias</option>
                            <option value="30">Últimos 30 dias</option>
                        </select>
                    </div>
                    <div class="space-y-1 w-full sm:w-44" id="filter-status-wrapper">
                        <label class="text-[10px] text-slate-400 font-bold uppercase block">Status da Cota:</label>
                        <select id="filter-status" onchange="applyFilters()" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none">
                            <option value="all">Todos</option>
                            <option value="Pago">Pago / Aprovado</option>
                            <option value="Reservado">Pendente / Reservado</option>
                        </select>
                    </div>
                </div>
                <div class="text-right text-xs text-slate-500 w-full sm:w-auto">
                    Total de registros exibidos: <strong class="text-slate-300" id="row-count">0</strong>
                </div>
            </div>

            <!-- Tabela de Dados Imprimível -->
            <div class="overflow-x-auto rounded-xl border border-slate-800/80" id="report-table-container">
                <table class="w-full text-left border-collapse" id="report-table">
                    <!-- Conteúdo será injetado dinamicamente via JS -->
                </table>
            </div>
        </div>
    </div>

    <!-- Gráficos Visuais de Apoio -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Pie Chart -->
        <div class="glass-card rounded-2xl p-6 space-y-4 border border-slate-800">
            <h3 class="font-bold text-white text-base">Distribuição de Formas de Pagamento</h3>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="paymentDistributionChart" class="max-w-[200px]"></canvas>
            </div>
        </div>

        <!-- Monthly Sales Bar Chart -->
        <div class="glass-card rounded-2xl p-6 space-y-4 border border-slate-800">
            <h3 class="font-bold text-white text-base">Receita Acumulada Mensal</h3>
            <div class="h-64 relative">
                <canvas id="monthlyRevenueChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    // Carregar dados estruturados vindos do controlador
    const detailedSales = @json($detailedSales);
    const rafflePerformance = @json($rafflePerformance);
    const topBuyers = @json($topBuyers);

    let currentReport = "vendas";

    // Inicialização da página
    document.addEventListener("DOMContentLoaded", function() {
        renderTable();
        initCharts();
    });

    // Alternar Relatório
    function selectReport(type) {
        currentReport = type;

        // Resetar abas
        document.querySelectorAll('.rep-tab-btn').forEach(btn => {
            btn.classList.remove('text-white', 'border-blue-500');
            btn.classList.add('text-slate-400', 'border-transparent');
        });

        // Marcar botão ativo
        const activeBtn = document.getElementById('btn-rep-' + type);
        activeBtn.classList.remove('text-slate-400', 'border-transparent');
        activeBtn.classList.add('text-white', 'border-blue-500');

        // Mostrar/ocultar filtros de acordo com o relatório
        const statusWrapper = document.getElementById('filter-status-wrapper');
        if (type === 'vendas') {
            statusWrapper.classList.remove('hidden');
        } else {
            statusWrapper.classList.add('hidden');
        }

        renderTable();
    }

    // Renderizar Tabela baseada nos filtros
    function renderTable() {
        const table = document.getElementById("report-table");
        const dateFilter = document.getElementById("filter-date").value;
        const statusFilter = document.getElementById("filter-status").value;

        let data = [];
        let html = "";

        if (currentReport === "vendas") {
            data = detailedSales;
            // Filtros de status
            if (statusFilter !== "all") {
                data = data.filter(row => row.status === statusFilter);
            }

            html = `
                <thead>
                    <tr class="bg-slate-900/50 text-[10px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <th class="px-6 py-4 font-bold">Cota ID</th>
                        <th class="px-6 py-4 font-bold">Rifa / Ação</th>
                        <th class="px-6 py-4 font-bold">Comprador</th>
                        <th class="px-6 py-4 font-bold">Contato</th>
                        <th class="px-6 py-4 font-bold text-center">Nº Sorte</th>
                        <th class="px-6 py-4 font-bold text-right">Valor</th>
                        <th class="px-6 py-4 font-bold">Status</th>
                        <th class="px-6 py-4 font-bold">Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850 text-xs text-slate-300">
            `;

            data.forEach(row => {
                const badgeClass = row.status === 'Pago' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border-amber-500/20';
                html += `
                    <tr class="hover:bg-slate-900/30 transition">
                        <td class="px-6 py-3.5 font-mono text-[11px] text-slate-500">#${row.id}</td>
                        <td class="px-6 py-3.5 font-semibold text-white">${row.raffle}</td>
                        <td class="px-6 py-3.5">${row.buyer}</td>
                        <td class="px-6 py-3.5 text-[11px] text-slate-400">${row.email}</td>
                        <td class="px-6 py-3.5 text-center font-bold"><span class="px-2 py-0.5 bg-blue-500/10 text-blue-400 rounded">${row.number}</span></td>
                        <td class="px-6 py-3.5 text-right font-semibold text-emerald-400">R$ ${parseFloat(row.price).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                        <td class="px-6 py-3.5"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold border ${badgeClass}">${row.status}</span></td>
                        <td class="px-6 py-3.5 text-[11px] text-slate-400">${row.date}</td>
                    </tr>
                `;
            });
            html += "</tbody>";

        } else if (currentReport === "desempenho") {
            data = rafflePerformance;

            html = `
                <thead>
                    <tr class="bg-slate-900/50 text-[10px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <th class="px-6 py-4 font-bold">Rifa / Ação</th>
                        <th class="px-6 py-4 font-bold text-center">Total Cotas</th>
                        <th class="px-6 py-4 font-bold text-center">Vendid. (Pagas)</th>
                        <th class="px-6 py-4 font-bold text-center">Reservadas</th>
                        <th class="px-6 py-4 font-bold text-center">Restantes</th>
                        <th class="px-6 py-4 font-bold text-right">Valor Cota</th>
                        <th class="px-6 py-4 font-bold text-right">Total Arrecadado</th>
                        <th class="px-6 py-4 font-bold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850 text-xs text-slate-300">
            `;

            data.forEach(row => {
                const badgeClass = row.status === 'Ativo' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-blue-500/10 text-blue-400';
                html += `
                    <tr class="hover:bg-slate-900/30 transition">
                        <td class="px-6 py-3.5 font-bold text-white">${row.title}</td>
                        <td class="px-6 py-3.5 text-center">${row.total_numbers}</td>
                        <td class="px-6 py-3.5 text-center text-emerald-400 font-bold">${row.sold}</td>
                        <td class="px-6 py-3.5 text-center text-amber-400 font-semibold">${row.reserved}</td>
                        <td class="px-6 py-3.5 text-center text-slate-500">${row.remaining}</td>
                        <td class="px-6 py-3.5 text-right font-medium">R$ ${parseFloat(row.price).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                        <td class="px-6 py-3.5 text-right font-bold text-emerald-400">R$ ${parseFloat(row.revenue).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                        <td class="px-6 py-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold ${badgeClass}">${row.status}</span></td>
                    </tr>
                `;
            });
            html += "</tbody>";

        } else if (currentReport === "clientes") {
            data = topBuyers;

            html = `
                <thead>
                    <tr class="bg-slate-900/50 text-[10px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <th class="px-6 py-4 font-bold">Nome Completo</th>
                        <th class="px-6 py-4 font-bold">CPF</th>
                        <th class="px-6 py-4 font-bold">E-mail</th>
                        <th class="px-6 py-4 font-bold text-center">Cotas Pagas</th>
                        <th class="px-6 py-4 font-bold text-center">Cotas Reservadas</th>
                        <th class="px-6 py-4 font-bold text-right">Total Investido</th>
                        <th class="px-6 py-4 font-bold">Data Cadastro</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850 text-xs text-slate-300">
            `;

            data.forEach(row => {
                html += `
                    <tr class="hover:bg-slate-900/30 transition">
                        <td class="px-6 py-3.5 font-bold text-white">${row.name}</td>
                        <td class="px-6 py-3.5 font-mono text-[11px]">${row.cpf}</td>
                        <td class="px-6 py-3.5 text-slate-400">${row.email}</td>
                        <td class="px-6 py-3.5 text-center text-emerald-400 font-bold">${row.paid_tickets}</td>
                        <td class="px-6 py-3.5 text-center text-amber-500">${row.reserved_tickets}</td>
                        <td class="px-6 py-3.5 text-right font-extrabold text-emerald-400">R$ ${parseFloat(row.total_spent).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                        <td class="px-6 py-3.5 text-slate-400">${row.registered_at}</td>
                    </tr>
                `;
            });
            html += "</tbody>";
        }

        table.innerHTML = html;
        document.getElementById("row-count").textContent = data.length;
    }

    // Executar filtros dinâmicos
    function applyFilters() {
        renderTable();
    }

    // Exportação para Excel (XLSX) usando SheetJS
    function exportToExcel() {
        const table = document.getElementById("report-table");
        const wb = XLSX.utils.table_to_book(table, { sheet: "Relatorio Acao RR" });
        
        let filename = `relatorio_acao_rr_${currentReport}_${new Date().toISOString().slice(0, 10)}.xlsx`;
        XLSX.writeFile(wb, filename);
    }

    // Exportação para PDF com Html2Pdf (Visual de Alta Resolução)
    function exportToPDF() {
        const element = document.getElementById("report-table-container");
        
        // Temporariamente aplica estilo claro para garantir legibilidade perfeita na impressão PDF
        const originalStyle = element.getAttribute("style");
        element.style.background = "#ffffff";
        element.style.color = "#1e293b";
        element.querySelectorAll("th, td").forEach(el => {
            el.style.color = "#1e293b";
            el.style.borderColor = "#e2e8f0";
        });
        element.querySelectorAll("tr").forEach(el => {
            el.style.borderColor = "#e2e8f0";
        });

        const opt = {
            margin:       [8, 8, 8, 8],
            filename:     `relatorio_acao_rr_${currentReport}_${new Date().toISOString().slice(0,10)}.pdf`,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, backgroundColor: '#ffffff' },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' } // Horizontal para tabelas largas
        };

        // Gerar PDF e restaurar estilos originais do dark mode após geração
        html2pdf().set(opt).from(element).save().then(() => {
            element.setAttribute("style", originalStyle || "");
            element.querySelectorAll("th, td, tr").forEach(el => el.removeAttribute("style"));
            renderTable(); // Recarrega com estilos corretos
        });
    }

    // Gráficos (Chart.js)
    function initCharts() {
        // 1. Payment Distribution Chart (Pie)
        new Chart(document.getElementById('paymentDistributionChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Pix', 'Cartão', 'Boleto'],
                datasets: [{
                    data: [82, 14, 4],
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
                    data: [12000, 19500, 31000, 54000, 42000, 68000],
                    backgroundColor: '#ef4444', // Vermelho Crimson
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
    }
</script>
@endsection
