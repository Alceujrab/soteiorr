@extends('layouts.public')

@section('title', 'Finalizar Pagamento - Ação RR Veículos')

@section('content')
@php
    $raffleTitle = $payment->tickets->first()?->raffle?->title
        ?? $payment->package?->raffle?->title
        ?? 'Ação Promocional';
    $secondsRemaining = $payment->reservationSecondsRemaining();
@endphp
<div class="max-w-2xl mx-auto space-y-6">
    <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-6">
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-bold text-white flex items-center justify-center gap-2">
                <i class="fa-solid fa-credit-card text-blue-500"></i> Pagamento Seguro
            </h1>
            <p class="text-slate-400 text-sm">
                Finalize o PIX para confirmar suas cotas.
            </p>
        </div>

        <div class="p-4 bg-slate-900/60 rounded-xl border border-slate-800 space-y-3">
            <div class="flex justify-between items-center text-sm">
                <span class="text-slate-400">Ação Promocional:</span>
                <span class="text-white font-bold">{{ $raffleTitle }}</span>
            </div>
            @if($payment->tickets->isNotEmpty())
            <div class="flex justify-between items-center text-sm">
                <span class="text-slate-400">Bilhetes Escolhidos:</span>
                <div class="flex flex-wrap gap-1 justify-end max-w-xs">
                    @foreach($payment->tickets as $ticket)
                        <span class="px-2 py-0.5 bg-white text-black border border-slate-200 rounded text-xs font-bold">
                            {{ sprintf('%02d', $ticket->number) }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif
            <div class="border-t border-slate-800 pt-3 flex justify-between items-center">
                <span class="text-slate-400 text-sm">Valor Total:</span>
                <span class="text-lg font-bold text-emerald-400">R$ {{ number_format($payment->amount, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-slate-400">Status:</span>
                @if($payment->status === 'approved')
                    <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Aprovado</span>
                @elseif($payment->status === 'expired')
                    <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-red-500/10 text-red-400 border border-red-500/20">Expirado</span>
                @else
                    <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/20 animate-pulse">Aguardando Pagamento</span>
                @endif
            </div>
            @if($payment->status === 'pending')
            <div class="flex justify-between items-center text-sm pt-1">
                <span class="text-slate-400">Tempo restante da reserva:</span>
                <span id="reservation-countdown" class="font-mono font-bold text-amber-300" data-seconds="{{ $secondsRemaining }}">--:--</span>
            </div>
            <p class="text-[11px] text-slate-500">Sem pagamento em até 30 minutos, os números voltam para outros participantes.</p>
            @endif
        </div>

        @if($payment->status === 'pending' && !empty($upsellSuggestions))
            <div class="rounded-2xl border p-5 space-y-3" style="border-color: var(--border-color); background: var(--bg-card);">
                <h3 class="text-sm font-bold theme-title">Quer mais chances?</h3>
                <p class="text-xs theme-muted">Adicione cotas agora. O valor do PIX será atualizado automaticamente.</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    @foreach($upsellSuggestions as $offer)
                        <form method="POST" action="{{ route('payments.upsell', $payment) }}">
                            @csrf
                            <input type="hidden" name="extra_numbers" value="{{ $offer['qty'] }}">
                            <button type="submit" class="w-full rounded-xl border px-3 py-3 text-xs font-bold theme-title hover:opacity-90 transition" style="border-color: var(--border-color);">
                                {{ $offer['label'] }}
                                <span class="block mt-1" style="color: var(--accent);">+ R$ {{ number_format($offer['price'], 2, ',', '.') }}</span>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        @endif

        @php
            $helpWhatsapp = \App\Support\ContactInfo::whatsappUrl(
                'Olá! Preciso de ajuda com o pagamento PIX #'.$payment->id
            );
        @endphp
        @if($helpWhatsapp && in_array($payment->status, ['pending', 'expired'], true))
            <a href="{{ $helpWhatsapp }}" target="_blank" rel="noopener" class="inline-flex w-full justify-center items-center gap-2 rounded-xl bg-[#25D366]/15 hover:bg-[#25D366]/25 border border-[#25D366]/30 text-[#25D366] font-semibold py-3 px-4 transition text-sm">
                <i class="fa-brands fa-whatsapp text-lg"></i> Precisa de ajuda? Fale no WhatsApp
            </a>
        @endif

        @if($payment->status === 'expired')
            <div class="text-center py-6 space-y-4">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-500/10 text-red-400 border border-red-500/30 text-3xl">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="text-xl font-bold text-white">Reserva expirada</h3>
                    <p class="text-slate-400 text-sm">O prazo de 30 minutos acabou e os números foram liberados. Escolha um novo pacote para continuar.</p>
                </div>
                <a href="{{ route('raffles.index') }}" class="inline-flex bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-6 rounded-xl transition">
                    Ver Ações Promocionais
                </a>
            </div>
        @elseif($payment->status !== 'approved')
            <div class="space-y-3">
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
            </div>

            @if(app()->environment(['local', 'testing']))
            <div class="border-t border-slate-800 pt-6 space-y-3 text-center">
                <p class="text-xs text-slate-400">
                    Clique no botão abaixo para simular a aprovação instantânea da transação de testes.
                </p>
                <a href="{{ route('payments.confirm', $payment->id) }}" id="confirm-btn" class="w-full inline-flex justify-center items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-3.5 px-4 rounded-xl shadow-lg transition">
                    <i class="fa-solid fa-circle-check"></i> Simular Confirmação Instantânea (Aprovar PIX)
                </a>
            </div>
            @endif
        @else
            <div class="text-center py-6 space-y-4">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-3xl">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="text-xl font-bold text-white">Pagamento Confirmado!</h3>
                    <p class="text-slate-400 text-sm">Seus números já estão pagos sob o seu perfil.</p>
                </div>
                <div class="flex flex-col sm:flex-row justify-center gap-3">
                    <a href="{{ route('payments.receipt', $payment->id) }}" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-6 rounded-xl transition flex items-center justify-center gap-1.5 shadow">
                        <i class="fa-solid fa-receipt"></i> Ver Recibo de Compra
                    </a>
                    <a href="{{ route('raffles.my-tickets') }}" class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white font-semibold py-3 px-6 rounded-xl transition">
                        Ver Meus Números
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    function toggleAccordion(methodId) {
        const target = document.getElementById(methodId);
        const icon = document.getElementById(methodId + '-icon');
        if (!target || !icon) return;

        if (target.classList.contains('hidden')) {
            target.classList.remove('hidden');
            icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
        } else {
            target.classList.add('hidden');
            icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
        }
    }

    function formatCountdown(totalSeconds) {
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;
        return String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
    }

    @if($payment->status === 'pending')
        const countdownEl = document.getElementById('reservation-countdown');
        let remaining = parseInt(countdownEl?.dataset.seconds || '0', 10);

        function tickCountdown() {
            if (!countdownEl) return;
            if (remaining <= 0) {
                countdownEl.textContent = '00:00';
                window.location.reload();
                return;
            }
            countdownEl.textContent = formatCountdown(remaining);
            remaining -= 1;
        }

        tickCountdown();
        setInterval(tickCountdown, 1000);

        const checkStatusUrl = "{{ route('payments.check-status', $payment->id) }}";

        function checkPaymentStatus() {
            fetch(checkStatusUrl)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'approved' || data.status === 'expired') {
                        window.location.reload();
                    } else if (typeof data.seconds_remaining === 'number') {
                        remaining = data.seconds_remaining;
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
                .catch(() => {
                    const status = document.getElementById("copy-status");
                    status.textContent = "Erro ao copiar.";
                });
        }
    @endif
</script>
@endsection
