@extends('layouts.app')

@section('title', 'Finalizar Pagamento - Ação RR Veículos')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-6">
        <!-- Header -->
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-bold text-white flex items-center justify-center gap-2">
                <i class="fa-solid fa-qrcode text-blue-500"></i> Checkout PIX
            </h1>
            <p class="text-slate-400 text-sm">
                Realize o pagamento para garantir seus números na rifa.
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
            <!-- QR Code -->
            <div class="flex flex-col items-center justify-center space-y-4">
                <div class="p-4 bg-white rounded-xl shadow-lg border border-slate-200">
                    <img src="{{ $payment->pix_qr_code_url }}" alt="QR Code PIX" class="w-48 h-48">
                </div>
                <div class="w-full space-y-2">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Código Copia e Cola:</label>
                    <div class="flex gap-2">
                        <input type="text" value="{{ $payment->pix_qr_code }}" readonly id="pix-code" class="flex-grow bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none focus:border-slate-700">
                        <button onclick="copyPixCode()" class="bg-blue-600 hover:bg-blue-500 text-white font-medium px-4 py-2 rounded-lg text-sm transition flex items-center gap-1.5">
                            <i class="fa-solid fa-copy"></i> Copiar
                        </button>
                    </div>
                    <p class="text-center text-xs text-slate-500 mt-2" id="copy-status"></p>
                </div>
            </div>

            <!-- Simular Pagamento -->
            <div class="border-t border-slate-800 pt-6 space-y-4 text-center">
                <p class="text-xs text-slate-400">
                    Ambiente de Testes: Clique no botão abaixo para simular a aprovação instantânea deste PIX.
                </p>
                <a href="{{ route('payments.confirm', $payment->id) }}" class="w-full inline-flex justify-center items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-emerald-500/10 transition">
                    <i class="fa-solid fa-circle-check"></i> Simular Pagamento Aprovado (PIX)
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

@if($payment->status !== 'approved')
<script>
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

    // Checar a cada 3 segundos
    setInterval(checkPaymentStatus, 3000);

    function copyPixCode() {
        const copyText = document.getElementById("pix-code");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value)
            .then(() => {
                const status = document.getElementById("copy-status");
                status.textContent = "Código PIX copiado para a área de transferência!";
                status.classList.remove("text-red-400");
                status.classList.add("text-emerald-400");
                setTimeout(() => status.textContent = "", 3000);
            })
            .catch(err => {
                const status = document.getElementById("copy-status");
                status.textContent = "Erro ao copiar código PIX.";
                status.classList.remove("text-emerald-400");
                status.classList.add("text-red-400");
            });
    }
</script>
@endif
@endsection
