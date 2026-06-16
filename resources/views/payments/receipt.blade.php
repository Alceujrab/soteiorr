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
            <p class="text-slate-400 text-xs mt-1">Gere o PDF oficial ou compartilhe diretamente no seu WhatsApp e redes sociais.</p>
        </div>
        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
            <button onclick="downloadPDF()" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-4 py-2 rounded-xl text-xs transition flex items-center gap-1.5 shadow">
                <i class="fa-solid fa-file-pdf"></i> Baixar PDF
            </button>
            <button onclick="sharePDFFile()" class="bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-4 py-2 rounded-xl text-xs transition flex items-center gap-1.5 shadow">
                <i class="fa-brands fa-whatsapp"></i> Compartilhar PDF
            </button>
            <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold px-4 py-2 rounded-xl text-xs transition flex items-center gap-1.5 border border-slate-700">
                <i class="fa-solid fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- Recibo Container (Que será exportado como PDF) -->
    <!-- Definimos estilos inline explícitos e cores fixas claras para que o PDF gerado seja perfeito para impressão e leitura -->
    <div id="receipt-pdf-content" class="bg-white rounded-2xl p-6 sm:p-8 space-y-6 border border-slate-200 text-slate-800" style="background-color: #ffffff; color: #1e293b; border-color: #e2e8f0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
        
        <!-- Topo / Cabeçalho da Empresa Organizadora -->
        <div class="flex justify-between items-start border-b pb-6" style="border-color: #e2e8f0;">
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-lg text-white font-bold" style="background-color: #ef4444; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 16px; font-weight: 900;">RR</span>
                    </div>
                    <span class="text-lg font-black uppercase tracking-wider" style="color: #ef4444;">Ação RR Veículos</span>
                </div>
                <div class="text-[10px] text-slate-500 leading-relaxed font-medium">
                    <div class="font-bold text-slate-700">Ação RR Veículos Sorteios e Promoções Ltda.</div>
                    <div>CNPJ: 12.345.678/0001-90</div>
                    <div>Suporte: suporte@acaorrveiculos.com.br | WhatsApp: (11) 99999-9999</div>
                    <div>Avenida das Nações, 1000 - Centro, São Paulo - SP</div>
                </div>
            </div>
            
            <div class="text-right space-y-1">
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider" style="background-color: #ecfdf5; color: #059669; border: 1px solid #d1fae5;">
                    {{ $payment->status === 'approved' ? 'PAGO / CONFIRMADO' : 'PENDENTE' }}
                </span>
                <div class="text-[10px] text-slate-500 mt-2 font-medium">
                    Recibo Nº: <span class="text-slate-800 font-bold uppercase">{{ $payment->gateway_transaction_id ?: 'MOCK-' . $payment->id }}</span>
                </div>
            </div>
        </div>

        <!-- Dados do Comprador -->
        <div class="space-y-2">
            <h3 class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Dados do Participante</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl border border-slate-100" style="background-color: #f8fafc;">
                <div>
                    <span class="text-[9px] text-slate-400 uppercase block font-semibold">Nome Completo</span>
                    <span class="text-xs text-slate-800 font-bold">{{ $payment->user->name }}</span>
                </div>
                <div>
                    <span class="text-[9px] text-slate-400 uppercase block font-semibold">CPF</span>
                    <span class="text-xs text-slate-800 font-bold">{{ $payment->user->cpf }}</span>
                </div>
                <div>
                    <span class="text-[9px] text-slate-400 uppercase block font-semibold">E-mail</span>
                    <span class="text-xs text-slate-800 font-bold">{{ $payment->user->email }}</span>
                </div>
                <div>
                    <span class="text-[9px] text-slate-400 uppercase block font-semibold">Data da Operação</span>
                    <span class="text-xs text-slate-800 font-bold">{{ $payment->created_at->format('d/m/Y H:i:s') }}</span>
                </div>
            </div>
        </div>

        <!-- Dados da Rifa e do Sorteio -->
        <div class="space-y-2">
            <h3 class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Detalhamento da Ação & Prêmio</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl border border-slate-100" style="background-color: #f8fafc;">
                <div>
                    <span class="text-[9px] text-slate-400 uppercase block font-semibold">Ação / Rifa</span>
                    <span class="text-xs text-slate-800 font-bold">{{ $payment->tickets->first()->raffle->title ?? 'Sorteio Principal' }}</span>
                </div>
                <div>
                    <span class="text-[9px] text-slate-400 uppercase block font-semibold">Prêmio da Ação</span>
                    <span class="text-xs font-bold" style="color: #ef4444;">{{ $payment->tickets->first()->raffle->prize_name ?? 'Prêmio Principal' }}</span>
                </div>
                <div>
                    <span class="text-[9px] text-slate-400 uppercase block font-semibold">Data/Hora Oficial do Sorteio</span>
                    <span class="text-xs text-slate-800 font-bold">{{ $payment->tickets->first()->raffle->draw_date->format('d/m/Y H:i') }}</span>
                </div>
                <div>
                    <span class="text-[9px] text-slate-400 uppercase block font-semibold">Valor Total Pago</span>
                    <span class="text-xs font-extrabold" style="color: #ef4444;">R$ {{ number_format($payment->amount, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Detalhamento de Números Comprados & Validador -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
            <!-- Números da sorte -->
            <div class="md:col-span-2 space-y-2">
                <h3 class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Números da Sorte Reservados ({{ $payment->tickets->count() }} cotas)</h3>
                <div class="flex flex-wrap gap-1.5 p-3 rounded-xl border border-slate-100" style="background-color: #f8fafc;">
                    @foreach($payment->tickets as $ticket)
                        <span class="inline-block px-2.5 py-1 font-extrabold rounded text-xs" style="background-color: #f1f5f9; color: #334155; border: 1px solid #e2e8f0;">
                            {{ sprintf('%02d', $ticket->number) }}
                        </span>
                    @endforeach
                </div>
            </div>

            <!-- QR Code de Autenticidade -->
            <div class="flex flex-col items-center justify-center p-4 border border-slate-100 rounded-xl" style="background-color: #f8fafc;">
                <!-- Usamos a URL de validação e solicitamos uma imagem QR Code limpa -->
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('raffles.validate-ticket', $payment->id)) }}" alt="QR Code Validador" class="w-20 h-20 bg-white p-1 rounded border border-slate-200">
                <span class="text-[8px] text-slate-500 font-bold mt-2 uppercase tracking-wider text-center">Autenticação Única</span>
                <span class="text-[7px] text-slate-400 text-center font-mono mt-0.5">{{ $payment->id }}-{{ substr(md5($payment->id), 0, 8) }}</span>
            </div>
        </div>

        <!-- Termos e Condições Rodapé -->
        <div class="border-t border-slate-100 pt-4 text-center">
            <p class="text-[8px] text-slate-400 leading-normal">
                Este recibo digital é emitido eletronicamente pela Ação RR Veículos e cumpre todas as regras do regulamento oficial da ação entre amigos. O sorteio oficial será transmitido ao vivo em nossas redes sociais. Para validar este recibo online, aponte a câmera do seu celular para o QR Code acima.
            </p>
        </div>
    </div>

    <!-- Compartilhar nas Redes Sociais -->
    <div class="glass-card rounded-2xl p-6 space-y-4">
        <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
            <i class="fa-solid fa-share-nodes text-blue-500"></i> Divulgar Participação & Compartilhar Sorteio
        </h3>
        <p class="text-xs text-slate-400">Divulgue seus números da sorte e o link oficial da ação com seus amigos:</p>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
            <!-- WhatsApp Link -->
            <a href="https://api.whatsapp.com/send?text={{ urlencode('Estou participando do sorteio da ' . ($payment->tickets->first()->raffle->title ?? 'Rifa') . '! Já garanti minhas cotas no Ação RR Veículos. Veja e participe você também: ' . route('raffles.show', $payment->tickets->first()->raffle_id ?? 1)) }}" target="_blank" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-[#25D366] hover:bg-[#20ba5a] text-white text-xs font-bold transition shadow-md border-none">
                <i class="fa-brands fa-whatsapp text-base"></i> WhatsApp
            </a>
            <!-- Facebook -->
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('raffles.show', $payment->tickets->first()->raffle_id ?? 1)) }}" target="_blank" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-[#1877F2] hover:bg-[#166fe5] text-white text-xs font-bold transition shadow-md border-none">
                <i class="fa-brands fa-facebook-f text-sm"></i> Facebook
            </a>
            <!-- LinkedIn -->
            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('raffles.show', $payment->tickets->first()->raffle_id ?? 1)) }}" target="_blank" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-[#0A66C2] hover:bg-[#0958a8] text-white text-xs font-bold transition shadow-md border-none">
                <i class="fa-brands fa-linkedin-in text-sm"></i> LinkedIn
            </a>
            <!-- Copiar Link / Texto Geral (Instagram, YouTube, TikTok, etc) -->
            <button onclick="copyPromoText()" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold transition shadow-md border-none">
                <i class="fa-solid fa-copy text-sm"></i> Copiar Post
            </button>
        </div>

        <div class="pt-2">
            <button onclick="sharePDFFile()" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-500 hover:to-green-500 text-white text-xs font-bold transition shadow-lg">
                <i class="fa-solid fa-share-nodes"></i> Enviar arquivo PDF no WhatsApp / Redes Sociais
            </button>
            <p id="share-toast" class="text-center text-[10px] text-emerald-400 font-bold mt-2 hidden">Texto de divulgação copiado com sucesso! Agora você pode postar no seu feed/status!</p>
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
            html2canvas:  { scale: 2, useCORS: true, backgroundColor: '#ffffff' },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Gerar e salvar PDF
        html2pdf().set(opt).from(element).save();
    }

    function sharePDFFile() {
        const element = document.getElementById('receipt-pdf-content');
        const opt = {
            margin:       [10, 10, 10, 10],
            filename:     'recibo_acao_rr_{{ $payment->id }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, backgroundColor: '#ffffff' },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Gerar o PDF como Blob e compartilhar
        html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
            const blob = pdf.output('blob');
            const file = new File([blob], 'recibo_acao_rr_{{ $payment->id }}.pdf', { type: 'application/pdf' });
            
            if (navigator.canShare && navigator.canShare({ files: [file] })) {
                navigator.share({
                    files: [file],
                    title: 'Recibo Ação RR Veículos',
                    text: 'Confira meu recibo da aposta oficial na Ação RR Veículos!'
                })
                .then(() => console.log('Compartilhado com sucesso!'))
                .catch((error) => console.log('Erro ao compartilhar:', error));
            } else {
                // Fallback para baixar
                pdf.save('recibo_acao_rr_{{ $payment->id }}.pdf');
                alert('O seu navegador não suporta compartilhamento direto de arquivos. O recibo PDF foi baixado para o seu aparelho.');
            }
        });
    }

    function copyPromoText() {
        const text = "🏆 Participe também da ação entre amigos da Ação RR Veículos!\n🔥 Rifa: {{ $payment->tickets->first()->raffle->title ?? 'Sorteio Especial' }}\n🎁 Prêmio: {{ $payment->tickets->first()->raffle->prize_name ?? 'Prêmio Principal' }}\n👉 Garanta seus números da sorte em: {{ route('raffles.show', $payment->tickets->first()->raffle_id ?? 1) }}";
        
        navigator.clipboard.writeText(text).then(() => {
            const toast = document.getElementById('share-toast');
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3500);
        });
    }
</script>
@endsection
