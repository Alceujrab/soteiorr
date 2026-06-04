@extends('layouts.app')

@section('title', 'Finalizar Pagamento - Ação RR Veículos')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-6">
        <!-- Header -->
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-bold text-white flex items-center justify-center gap-2">
                <i class="fa-solid fa-credit-card text-blue-500"></i> Pagamento Seguro
            </h1>
            <p class="text-slate-400 text-sm">
                Escolha a melhor forma de pagamento para concluir sua compra.
            </p>
        </div>

        <!-- Summary -->
        <div class="p-4 bg-slate-900/60 rounded-xl border border-slate-800 space-y-3">
            <div class="flex justify-between items-center text-sm">
                <span class="text-slate-400">Rifa:</span>
                <span class="text-white font-bold">{{ $payment->tickets->first()->raffle->title }}</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-slate-400">Bilhetes Escolhidos:</span>
                <div class="flex flex-wrap gap-1 justify-end max-w-xs">
                    @foreach($payment->tickets as $ticket)
                        <span class="px-2 py-0.5 bg-slate-800 text-slate-300 rounded text-xs font-semibold">
                            {{ sprintf('%02d', $ticket->number) }}
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="border-t border-slate-800 pt-3 flex justify-between items-center">
                <span class="text-slate-400 text-sm">Valor Total:</span>
                <span class="text-lg font-bold text-emerald-400">R$ {{ number_format($payment->amount, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-slate-400">Status:</span>
                <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider {{ $payment->status === 'approved' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20 animate-pulse' }}">
                    {{ $payment->status === 'approved' ? 'Aprovado' : 'Aguardando Pagamento' }}
                </span>
            </div>
        </div>

        @if($payment->status !== 'approved')
            <!-- Accordion Métodos de Pagamento -->
            <div class="space-y-3">
                <!-- Método 1: PIX (Padrão e Expandido) -->
                <div class="border border-slate-800 rounded-xl overflow-hidden">
                    <button onclick="toggleAccordion('pix-method')" class="w-full flex items-center justify-between px-5 py-4 bg-slate-900/40 hover:bg-slate-900/80 text-white font-semibold text-sm transition">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-qrcode text-emerald-400"></i> Pagamento via PIX (Aprovação Instantânea)
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs" id="pix-method-icon"></i>
                    </button>
                    <div id="pix-method" class="p-6 bg-slate-900/20 border-t border-slate-800/60 flex flex-col items-center justify-center space-y-4">
                        <div class="p-4 bg-white rounded-xl shadow-lg border border-slate-200">
                            <img src="{{ $payment->pix_qr_code_url }}" alt="QR Code PIX" class="w-44 h-44">
                        </div>
                        <div class="w-full space-y-2">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Código Copia e Cola:</label>
                            <div class="flex gap-2">
                                <input type="text" value="{{ $payment->pix_qr_code }}" readonly id="pix-code" class="flex-grow bg-slate-900 border border-slate-850 rounded-lg px-3 py-2 text-xs text-slate-400 focus:outline-none">
                                <button onclick="copyPixCode()" class="bg-blue-600 hover:bg-blue-500 text-white font-medium px-4 py-2 rounded-lg text-xs transition flex items-center gap-1">
                                    <i class="fa-solid fa-copy"></i> Copiar
                                </button>
                            </div>
                            <p class="text-center text-xs text-slate-500 mt-2" id="copy-status"></p>
                        </div>
                    </div>
                </div>

                <!-- Método 2: Cartão de Crédito -->
                <div class="border border-slate-800 rounded-xl overflow-hidden">
                    <button onclick="toggleAccordion('card-method')" class="w-full flex items-center justify-between px-5 py-4 bg-slate-900/40 hover:bg-slate-900/80 text-white font-semibold text-sm transition">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-credit-card text-blue-400"></i> Cartão de Crédito (Sem Juros)
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs" id="card-method-icon"></i>
                    </button>
                    <div id="card-method" class="p-6 bg-slate-900/20 border-t border-slate-800/60 hidden space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs text-slate-400 font-medium">Número do Cartão:</label>
                                <input type="text" placeholder="0000 0000 0000 0000" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs text-slate-400 font-medium">Nome do Titular:</label>
                                <input type="text" placeholder="Ex: João da Silva" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs text-slate-400 font-medium">Validade:</label>
                                <input type="text" placeholder="MM/AA" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs text-slate-400 font-medium">CVV:</label>
                                <input type="text" placeholder="123" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none">
                            </div>
                        </div>
                        <button onclick="simulateSuccess()" class="w-full bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold py-2.5 px-4 rounded-lg transition">
                            Pagar com Cartão
                        </button>
                    </div>
                </div>

                <!-- Método 3: Boleto Bancário -->
                <div class="border border-slate-800 rounded-xl overflow-hidden">
                    <button onclick="toggleAccordion('boleto-method')" class="w-full flex items-center justify-between px-5 py-4 bg-slate-900/40 hover:bg-slate-900/80 text-white font-semibold text-sm transition">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-barcode text-amber-400"></i> Boleto Bancário (Compensação em até 3 dias)
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs" id="boleto-method-icon"></i>
                    </button>
                    <div id="boleto-method" class="p-6 bg-slate-900/20 border-t border-slate-800/60 hidden space-y-4 text-center">
                        <p class="text-xs text-slate-400">
                            Ao confirmar, o boleto será gerado e você poderá imprimi-lo ou copiar o código de barras.
                        </p>
                        <button onclick="simulateSuccess()" class="bg-amber-600 hover:bg-amber-500 text-amber-950 text-sm font-bold py-2.5 px-6 rounded-lg transition inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-barcode"></i> Gerar Boleto Bancário
                        </button>
                    </div>
                </div>
            </div>

            <!-- Simular Pagamento Rápido -->
            <div class="border-t border-slate-800 pt-6 space-y-3 text-center">
                <p class="text-xs text-slate-400">
                    Clique no botão abaixo para simular a aprovação instantânea da transação de testes.
                </p>
                <a href="{{ route('payments.confirm', $payment->id) }}" id="confirm-btn" class="w-full inline-flex justify-center items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-3.5 px-4 rounded-xl shadow-lg transition">
                    <i class="fa-solid fa-circle-check"></i> Simular Confirmação Instantânea (Aprovar PIX)
                </a>
            </div>
        @else
            <!-- Pagamento Concluído com Sucesso -->
            <div class="text-center py-6 space-y-4">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-3xl">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="text-xl font-bold text-white">Pagamento Confirmado!</h3>
                    <p class="text-slate-400 text-sm">Seus números já estão reservados e pagos sob o seu perfil.</p>
                </div>
                <a href="{{ route('raffles.index') }}" class="inline-flex bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white font-semibold py-3 px-6 rounded-xl transition">
                    Voltar para o Início
                </a>
            </div>
        @endif
    </div>
</div>

<script>
    function toggleAccordion(methodId) {
        const target = document.getElementById(methodId);
        const icon = document.getElementById(methodId + '-icon');
        const methods = ['pix-method', 'card-method', 'boleto-method'];
        
        methods.forEach(id => {
            const el = document.getElementById(id);
            const ic = document.getElementById(id + '-icon');
            if (id === methodId) {
                if (el.classList.contains('hidden')) {
                    el.classList.remove('hidden');
                    ic.classList.replace('fa-chevron-down', 'fa-chevron-up');
                } else {
                    el.classList.add('hidden');
                    ic.classList.replace('fa-chevron-up', 'fa-chevron-down');
                }
            } else {
                el.classList.add('hidden');
                ic.classList.replace('fa-chevron-up', 'fa-chevron-down');
            }
        });
    }

    function simulateSuccess() {
        document.getElementById('confirm-btn').click();
    }

    @if($payment->status !== 'approved')
        // Polling para checar o status do pagamento automaticamente
        const checkStatusUrl = "{{ route('payments.check-status', $payment->id) }}";
        
        function checkPaymentStatus() {
            fetch(checkStatusUrl)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'approved') {
                        window.location.reload();
                    }
                })
                .catch(err => console.error("Erro ao verificar status:", err));
        }

        setInterval(checkPaymentStatus, 3000);

        function copyPixCode() {
            const copyText = document.getElementById("pix-code");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value)
                .then(() => {
                    const status = document.getElementById("copy-status");
                    status.textContent = "Código PIX copiado!";
                    status.classList.remove("text-red-400");
                    status.classList.add("text-emerald-400");
                    setTimeout(() => status.textContent = "", 3000);
                })
                .catch(err => {
                    const status = document.getElementById("copy-status");
                    status.textContent = "Erro ao copiar.";
                });
        }
    @endif
</script>
@endsection
