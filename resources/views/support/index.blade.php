@extends('layouts.customer')

@section('title', 'Suporte & FAQs - Ação RR Veículos')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- FAQs section (Left 2 columns) -->
    <div class="lg:col-span-2 space-y-6">
        <div class="border-b border-slate-800 pb-4">
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-circle-question text-blue-500"></i> Perguntas Frequentes (FAQ)
            </h1>
            <p class="text-slate-400 text-sm mt-1">Dúvidas comuns sobre o processo de compra e sorteio.</p>
        </div>

        <div class="space-y-4">
            <!-- FAQ 1 -->
            <div class="glass-card rounded-xl p-5 space-y-2">
                <h3 class="font-semibold text-white text-base">Como faço para participar?</h3>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Basta navegar na página inicial, escolher a ação ativa, selecionar seus números da sorte e clicar em reservar. Depois, realize o pagamento via PIX copiando o código ou lendo o QR Code gerado para confirmar seus bilhetes.
                </p>
            </div>
            <!-- FAQ 2 -->
            <div class="glass-card rounded-xl p-5 space-y-2">
                <h3 class="font-semibold text-white text-base">O sorteio é confiável?</h3>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Sim! Nossos sorteios são auditados e transmitidos ao vivo através das nossas redes sociais oficiais. O histórico de todos os bilhetes vendidos e do vencedor fica publicado na plataforma para consulta pública.
                </p>
            </div>
            <!-- FAQ 3 -->
            <div class="glass-card rounded-xl p-5 space-y-2">
                <h3 class="font-semibold text-white text-base">Qual o prazo de compensação do PIX?</h3>
                <p class="text-slate-400 text-sm leading-relaxed">
                    O pagamento por PIX é reconhecido automaticamente em nossa plataforma em poucos segundos após a transferência. Assim que o gateway aprova, seus bilhetes mudam para o status "Pago".
                </p>
            </div>
        </div>
    </div>

    <!-- Submit Ticket section (Right 1 column) -->
    <div class="space-y-6">
        <div class="glass-card rounded-2xl p-6 space-y-4">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-headset text-blue-500"></i> Suporte ao Cliente
                </h3>
                <p class="text-slate-400 text-xs mt-1">Abra um ticket de suporte caso tenha problemas adicionais.</p>
            </div>

            <form action="{{ route('support.store-ticket') }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label class="text-xs text-slate-400 font-semibold block">Assunto:</label>
                    <input type="text" name="subject" required placeholder="Ex: Problema com o PIX" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs text-slate-400 font-semibold block">Categoria:</label>
                        <select name="category" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-300 focus:outline-none">
                            <option value="financeiro">Financeiro</option>
                            <option value="suporte_tecnico">Suporte Técnico</option>
                            <option value="duvidas">Dúvidas</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs text-slate-400 font-semibold block">Prioridade:</label>
                        <select name="priority" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-300 focus:outline-none">
                            <option value="baixa">Baixa</option>
                            <option value="media" selected>Média</option>
                            <option value="alta">Alta</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs text-slate-400 font-semibold block">Mensagem:</label>
                    <textarea name="message" rows="4" required placeholder="Descreva seu problema..." class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none"></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 rounded-xl transition text-sm">
                    Enviar Ticket
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
