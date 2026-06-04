@extends('layouts.public')

@section('title', 'Recibo de Compra - Ação RR Veículos')

@section('content')
<!-- Include Html2Pdf library from CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="max-w-2xl mx-auto space-y-6">
    <div class="border-b pb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4" style="border-color: var(--border-color);">
        <div>
            <h1 class="text-2xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-receipt" style="color: var(--accent);"></i> Recibo de Compra
            </h1>
            <p class="text-slate-400 text-xs mt-1">Gere o PDF do comprovante oficial ou compartilhe nas suas redes sociais.</p>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <button onclick="downloadPDF()" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-4 py-2 rounded-xl text-xs transition flex items-center gap-1.5 shadow">
                <i class="fa-solid fa-file-pdf"></i> Baixar PDF
            </button>
            <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold px-4 py-2 rounded-xl text-xs transition flex items-center gap-1.5 border border-slate-700">
                <i class="fa-solid fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- Recibo Container (Que será exportado como PDF) -->
    <div id="receipt-pdf-content" class="bg-slate-950 rounded-2xl p-6 sm:p-8 space-y-6 border text-slate-350" style="border-color: var(--border-color); background-color: #020617;">
        
        <!-- Topo / Cabeçalho da Empresa Organizadora -->
        <div class="flex justify-between items-start border-b pb-6" style="border-color: var(--border-color);">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 rounded-lg text-white font-bold bg-red-600" style="background-color: var(--accent);">
                        <i class="fa-solid fa-car-side text-sm"></i>
                    </div>
                    <span class="text-base font-black text-white uppercase tracking-wider">Ação RR Veículos</span>
                </div>
                <div class="text-[10px] text-slate-500 leading-relaxed font-medium">
                    <div><strong>Ação RR Sorteios e Promoções Ltda.</strong></div>
                    <div>CNPJ: 12.345.678/0001-90</div>
                    <div>Suporte: suporte@acaorrveiculos.com.br</div>
                    <div>Av. das Nações, 1000 - São Paulo/SP</div>
                </div>
            </div>
            
            <div class="text-right space-y-1">
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    {{ $payment->status === 'approved' ? 'PAGO / CONFIRMADO' : 'AGUARDANDO PAGAMENTO' }}
                </span>
                <div class="text-[10px] text-slate-500 mt-2 font-medium">
                    Recibo Nº: <span class="text-slate-300 font-bold uppercase">{{ $payment->gateway_transaction_id ?: 'MOCK-' . $payment->id }}</span>
                </div>
            </div>
        </div>

        <!-- Dados do Comprador -->
        <div class="space-y-2">
            <h3 class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Dados do Participante</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-900/40 p-4 rounded-xl border border-slate-900" style="background-color: rgba(15, 23, 42, 0.4);">
                <div>
                    <span class="text-[9px] text-slate-500 uppercase block font-semibold">Nome Completo</span>
                    <span class="text-xs text-slate-200 font-bold">{{ $payment->user->name }}</span>
                </div>
                <div>
                    <span class="text-[9px] text-slate-500 uppercase block font-semibold">CPF</span>
                    <span class="text-xs text-slate-200 font-bold">{{ $payment->user->cpf }}</span>
                </div>
                <div>
                    <span class="text-[9px] text-slate-500 uppercase block font-semibold">E-mail</span>
                    <span class="text-xs text-slate-200 font-bold">{{ $payment->user->email }}</span>
                </div>
                <div>
                    <span class="text-[9px] text-slate-500 uppercase block font-semibold">Data da Operação</span>
                    <span class="text-xs text-slate-200 font-bold">{{ $payment->created_at->format('d/m/Y H:i:s') }}</span>
                </div>
            </div>
        </div>

        <!-- Dados da Rifa e do Sorteio -->
        <div class="space-y-2">
            <h3 class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Detalhamento da Ação & Prêmio</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-900/40 p-4 rounded-xl border border-slate-900" style="background-color: rgba(15, 23, 42, 0.4);">
                <div>
                    <span class="text-[9px] text-slate-500 uppercase block font-semibold">Ação / Rifa</span>
                    <span class="text-xs text-slate-200 font-bold">{{ $payment->tickets->first()->raffle->title ?? 'Sorteio Principal' }}</span>
                </div>
                <div>
                    <span class="text-[9px] text-slate-500 uppercase block font-semibold">Prêmio Prometido</span>
                    <span class="text-xs text-emerald-400 font-bold">{{ $payment->tickets->first()->raffle->prize_name ?? 'Prêmio Principal' }}</span>
                </div>
                <div>
                    <span class="text-[9px] text-slate-500 uppercase block font-semibold">Data/Hora Oficial do Sorteio</span>
                    <span class="text-xs text-slate-200 font-bold">{{ $payment->tickets->first()->raffle->draw_date->format('d/m/Y H:i') }}</span>
                </div>
                <div>
                    <span class="text-[9px] text-slate-500 uppercase block font-semibold">Valor Total Pago</span>
                    <span class="text-xs text-emerald-400 font-extrabold">R$ {{ number_format($payment->amount, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Detalhamento de Números Comprados & Validador -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
            
            <!-- Números da sorte -->
            <div class="md:col-span-2 space-y-2">
                <h3 class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Números da Sorte Reservados ({{ $payment->tickets->count() }} cotas)</h3>
                <div class="flex flex-wrap gap-1.5 max-h-40 overflow-y-auto p-1.5 bg-slate-900/30 rounded-xl border border-slate-900">
                    @foreach($payment->tickets as $ticket)
                        <span class="inline-block px-2.5 py-1 bg-blue-500/10 border text-blue-400 border-blue-500/20 font-extrabold rounded text-xs">
                            {{ sprintf('%02d', $ticket->number) }}
                        </span>
                    @endforeach
                </div>
            </div>

            <!-- QR Code de Autenticidade -->
            <div class="flex flex-col items-center justify-center p-4 bg-slate-900/30 border border-slate-900 rounded-xl">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('raffles.validate-ticket', $payment->id)) }}" alt="QR Code Validador" class="w-20 h-20 bg-white p-1 rounded">
                <span class="text-[8px] text-slate-500 font-bold mt-2 uppercase tracking-wider text-center">Validação Eletrônica</span>
            </div>
        </div>

        <!-- Termos e Condições Rodapé -->
        <div class="border-t border-slate-900 pt-4 text-center">
            <p class="text-[8px] text-slate-600 leading-normal">
                Este recibo digital é emitido eletronicamente e cumpre todas as regras do regulamento oficial da ação entre amigos da Ação RR Veículos. O sorteio oficial será transmitido ao vivo em nossas redes sociais. Para validar este recibo online, aponte a câmera do seu celular para o QR Code acima.
            </p>
        </div>
    </div>

    <!-- Compartilhar nas Redes Sociais -->
    <div class="glass-card rounded-2xl p-6 space-y-4">
        <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
            <i class="fa-solid fa-share-nodes text-blue-500"></i> Compartilhar Recibo / Participação
        </h3>
        <p class="text-xs text-slate-400">Divulgue seus números da sorte e o link de validação oficial nas redes sociais:</p>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
            <!-- WhatsApp -->
            <a href="https://api.whatsapp.com/send?text={{ urlencode('Estou participando do sorteio da ' . ($payment->tickets->first()->raffle->title ?? 'Rifa') . '! Comprei as cotas no Ação RR Veículos. Valide minha compra em: ' . route('raffles.validate-ticket', $payment->id)) }}" target="_blank" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 text-xs font-bold transition">
                <i class="fa-brands fa-whatsapp text-base"></i> WhatsApp
            </a>
            <!-- Facebook -->
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('raffles.validate-ticket', $payment->id)) }}" target="_blank" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 border border-blue-500/20 text-xs font-bold transition">
                <i class="fa-brands fa-facebook-f text-sm"></i> Facebook
            </a>
            <!-- LinkedIn -->
            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('raffles.validate-ticket', $payment->id)) }}" target="_blank" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 border border-indigo-500/20 text-xs font-bold transition">
                <i class="fa-brands fa-linkedin-in text-sm"></i> LinkedIn
            </a>
            <!-- Outros (Compartilhamento Nativo do Celular) -->
            <button onclick="shareNative()" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-xs font-bold transition">
                <i class="fa-solid fa-share text-sm"></i> Outros / Celular
            </button>
        </div>
    </div>
</div>

<script>
    function downloadPDF() {
        const element = document.getElementById('receipt-pdf-content');
        
        // Configurações do html2pdf
        const opt = {
            margin:       [10, 10, 10, 10],
            filename:     'recibo_acao_rr_{{ $payment->id }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, backgroundColor: '#020617' },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Gerar e salvar PDF
        html2pdf().set(opt).from(element).save();
    }

    function shareNative() {
        if (navigator.share) {
            navigator.share({
                title: 'Meu Recibo - Ação RR Veículos',
                text: 'Estou participando do sorteio da {{ $payment->tickets->first()->raffle->title ?? "Rifa" }}! Valide minha compra.',
                url: '{{ route("raffles.validate-ticket", $payment->id) }}'
            }).catch(console.error);
        } else {
            // Fallback: copiar link
            navigator.clipboard.writeText('{{ route("raffles.validate-ticket", $payment->id) }}').then(() => {
                alert('Link de validação copiado para a área de transferência!');
            });
        }
    }
</script>
@endsection
