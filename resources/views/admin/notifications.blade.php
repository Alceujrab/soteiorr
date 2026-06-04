@extends('layouts.admin')

@section('title', 'Notificações & Comunicação - Ação RR Veículos')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Send panel (Left 1 column) -->
    <div class="space-y-6">
        <div class="glass-card rounded-2xl p-6 space-y-4">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-bullhorn text-blue-500"></i> Disparo em Massa
                </h3>
                <p class="text-slate-400 text-xs mt-1">Envie notificações e-mail, SMS ou push para todos os clientes.</p>
            </div>

            <form action="{{ route('admin.notifications.send') }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label class="text-xs text-slate-400 font-semibold block">Canal de Comunicação:</label>
                    <select name="channel" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2.5 text-xs text-slate-300 focus:outline-none">
                        <option value="email">E-mail Corporativo</option>
                        <option value="sms">SMS Celular</option>
                        <option value="push">Notificação Push</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-xs text-slate-400 font-semibold block">Template / Objetivo:</label>
                    <select name="template_name" id="template-select" onchange="applyTemplate(this.value)" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2.5 text-xs text-slate-300 focus:outline-none">
                        <option value="custom">Mensagem Personalizada</option>
                        <option value="pagamento_aprovado">Pagamento Confirmado</option>
                        <option value="sorteio_ao_vivo">Convite Sorteio Ao Vivo</option>
                        <option value="promocao">Campanha Promocional</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-xs text-slate-400 font-semibold block">Mensagem:</label>
                    <textarea name="message" id="message-text" rows="5" required placeholder="Escreva a mensagem aqui..." class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none"></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 rounded-xl transition text-sm">
                    Disparar Mensagem
                </button>
            </form>
        </div>
    </div>

    <!-- History list (Right 2 columns) -->
    <div class="lg:col-span-2 space-y-6">
        <div class="border-b border-slate-800 pb-4">
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-blue-500"></i> Histórico de Disparos
            </h1>
            <p class="text-slate-400 text-sm mt-1">Registro cronológico de mensagens e status de envio.</p>
        </div>

        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase tracking-wider bg-slate-900/50">
                            <th class="px-6 py-4">Destinatário</th>
                            <th class="px-6 py-4">Canal</th>
                            <th class="px-6 py-4">Mensagem</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-sm text-slate-300">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="px-6 py-4 font-semibold text-white">
                                    {{ $log->user?->name ?: 'N/A' }}
                                </td>
                                <td class="px-6 py-4 font-bold text-xs uppercase text-blue-400">
                                    {{ $log->channel }}
                                </td>
                                <td class="px-6 py-4 text-xs max-w-xs truncate" title="{{ $log->message }}">
                                    {{ $log->message }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        {{ $log->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                    Nenhuma notificação registrada no histórico.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/20">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    const templates = {
        custom: "",
        pagamento_aprovado: "Olá! Confirmamos o seu pagamento. Seus bilhetes já foram validados e estão ativos. Boa sorte!",
        sorteio_ao_vivo: "Atenção! O sorteio do veículo está prestes a começar. Acesse nossa live pelo link oficial e acompanhe ao vivo!",
        promocao: "Nova ação de veículos liberada! Participe comprando cotas com desconto especial de lançamento por tempo limitado."
    };

    function applyTemplate(val) {
        document.getElementById('message-text').value = templates[val] || "";
    }
</script>
@endsection
