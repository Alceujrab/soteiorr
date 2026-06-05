@extends('layouts.public')

@section('title', 'Dúvidas Frequentes - Ação RR Veículos')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    
    <!-- Cabeçalho Centralizado -->
    <div class="text-center space-y-3">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20">
            <i class="fa-solid fa-circle-question"></i> FAQ / Central de Ajuda
        </span>
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Dúvidas Frequentes</h1>
        <p class="text-slate-400 text-sm max-w-lg mx-auto">
            Tem alguma pergunta sobre os sorteios, pagamentos ou segurança? Encontre a resposta rápida abaixo.
        </p>
    </div>

    <!-- Conteúdo Bruto do CMS (Invisível) -->
    <div id="raw-faq-content" class="hidden">
        {!! $content !!}
    </div>

    <!-- Accordion Central de Exibição -->
    <div id="faq-accordion" class="space-y-4">
        <!-- Os itens do FAQ serão renderizados de forma interativa aqui via JS -->
    </div>

    <!-- Fallback em caso de falha de carregamento ou conteúdo sem cabeçalhos -->
    <div id="faq-fallback" class="hidden glass-card rounded-2xl p-6 sm:p-8 text-slate-300 leading-relaxed shadow-xl border" style="border-color: var(--border-color);">
        <div class="dynamic-prose">
            {!! $content !!}
        </div>
    </div>
</div>

<!-- Estilos para Animação Suave -->
<style>
    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out, padding 0.3s ease-out;
    }
    .faq-item {
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .rotate-180 {
        transform: rotate(180deg);
    }
</style>

<!-- Script de Acordeão Inteligente -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const rawContent = document.getElementById("raw-faq-content");
        const accordionContainer = document.getElementById("faq-accordion");
        const fallbackContainer = document.getElementById("faq-fallback");

        // Encontrar todos os H2 e H3 (Perguntas) no conteúdo dinâmico
        const headers = rawContent.querySelectorAll("h2, h3");

        if (headers.length === 0) {
            // Se o administrador não formatou com H2/H3, mostra o conteúdo plano original no fallback
            fallbackContainer.classList.remove("hidden");
            return;
        }

        headers.forEach((header, index) => {
            const questionText = header.innerText.replace(/^\d+\.\s*/, ""); // Remove numeração ex: "1. "
            
            // Coletar todos os elementos seguintes até o próximo H2/H3 para compor a resposta
            let answerHtml = "";
            let nextEl = header.nextElementSibling;
            
            while (nextEl && !["H2", "H3"].includes(nextEl.tagName)) {
                answerHtml += nextEl.outerHTML;
                nextEl = nextEl.nextElementSibling;
            }

            // Criar estrutura do item do acordeão
            const faqItem = document.createElement("div");
            faqItem.className = "faq-item border rounded-xl overflow-hidden glass-card shadow-sm";
            faqItem.style.borderColor = "var(--border-color)";
            faqItem.style.background = "var(--bg-card)";

            faqItem.innerHTML = `
                <button type="button" onclick="toggleFaqItem(this)" class="w-full flex items-center justify-between px-6 py-4 text-left font-bold text-white hover:text-blue-400 transition gap-4 text-sm sm:text-base focus:outline-none">
                    <span>${questionText}</span>
                    <span class="p-1 rounded-lg bg-slate-800/40 border border-slate-700/60 flex items-center justify-center transition">
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-300 faq-icon"></i>
                    </span>
                </button>
                <div class="faq-answer" style="background-color: rgba(15, 23, 42, 0.25);">
                    <div class="px-6 py-4 text-xs sm:text-sm text-slate-400 leading-relaxed border-t border-slate-800/30">
                        ${answerHtml}
                    </div>
                </div>
            `;

            accordionContainer.appendChild(faqItem);
        });
    });

    function toggleFaqItem(btn) {
        const item = btn.parentElement;
        const answer = item.querySelector(".faq-answer");
        const icon = item.querySelector(".faq-icon");
        const iconContainer = icon.parentElement;

        // Verificar se está aberto
        const isOpen = answer.style.maxHeight && answer.style.maxHeight !== "0px";

        // Fechar todos os outros
        document.querySelectorAll(".faq-item").forEach(otherItem => {
            if (otherItem !== item) {
                const otherAnswer = otherItem.querySelector(".faq-answer");
                const otherIcon = otherItem.querySelector(".faq-icon");
                const otherIconContainer = otherIcon.parentElement;
                
                otherAnswer.style.maxHeight = "0px";
                otherIcon.classList.remove("rotate-180");
                otherIconContainer.style.borderColor = "rgba(100, 116, 139, 0.2)";
                otherItem.style.borderColor = "var(--border-color)";
                otherItem.style.boxShadow = "none";
            }
        });

        if (!isOpen) {
            // Abrir com animação baseada no scrollHeight
            answer.style.maxHeight = answer.scrollHeight + "px";
            icon.classList.add("rotate-180");
            iconContainer.style.borderColor = "var(--accent)";
            item.style.borderColor = "var(--accent)";
            item.style.boxShadow = "0 4px 20px rgba(239, 68, 68, 0.1)"; // Sutil brilho de destaque
        } else {
            // Fechar
            answer.style.maxHeight = "0px";
            icon.classList.remove("rotate-180");
            iconContainer.style.borderColor = "rgba(100, 116, 139, 0.2)";
            item.style.borderColor = "var(--border-color)";
            item.style.boxShadow = "none";
        }
    }
</script>
@endsection
