@extends('layouts.public')

@section('title', 'Dúvidas Frequentes - RR Veículos')

@section('content')
<x-institutional-layout
    eyebrow="Central de ajuda"
    title="Dúvidas frequentes"
    subtitle="Respostas objetivas sobre compra de cotas, PIX, sorteio e acompanhamento dos bilhetes."
    current="pages.faqs"
>
    <div id="raw-faq-content" class="hidden">
        {!! $content !!}
    </div>

    <div id="faq-accordion" class="space-y-3"></div>

    <div id="faq-fallback" class="hidden rounded-2xl border px-6 py-8 sm:px-10" style="border-color: var(--border-color); background: var(--bg-card);">
        <div class="institutional-prose">
            {!! $content !!}
        </div>
    </div>
</x-institutional-layout>

<style>
    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.28s ease, padding 0.28s ease;
    }
    .faq-icon {
        transition: transform 0.28s ease;
    }
    .faq-icon.is-open {
        transform: rotate(180deg);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rawContent = document.getElementById('raw-faq-content');
        const accordionContainer = document.getElementById('faq-accordion');
        const fallbackContainer = document.getElementById('faq-fallback');
        if (!rawContent || !accordionContainer) return;

        const headers = rawContent.querySelectorAll('h2, h3');
        if (!headers.length) {
            fallbackContainer.classList.remove('hidden');
            return;
        }

        headers.forEach((header) => {
            const questionText = header.innerText.replace(/^\d+\.\s*/, '');
            let answerHtml = '';
            let nextEl = header.nextElementSibling;

            while (nextEl && !['H2', 'H3'].includes(nextEl.tagName)) {
                answerHtml += nextEl.outerHTML;
                nextEl = nextEl.nextElementSibling;
            }

            const item = document.createElement('div');
            item.className = 'faq-item rounded-xl border overflow-hidden';
            item.style.borderColor = 'var(--border-color)';
            item.style.background = 'var(--bg-card)';

            item.innerHTML = `
                <button type="button" class="faq-trigger w-full flex items-center justify-between gap-4 px-5 sm:px-6 py-4 text-left focus:outline-none">
                    <span class="font-semibold text-sm sm:text-base theme-title">${questionText}</span>
                    <span class="shrink-0 w-8 h-8 rounded-lg border flex items-center justify-center" style="border-color: var(--border-color); background: var(--bg-primary);">
                        <i class="fa-solid fa-chevron-down text-xs faq-icon" style="color: var(--accent);"></i>
                    </span>
                </button>
                <div class="faq-answer border-t" style="border-color: var(--border-color); background: var(--bg-primary);">
                    <div class="px-5 sm:px-6 py-4 text-sm theme-muted leading-relaxed institutional-prose">
                        ${answerHtml}
                    </div>
                </div>
            `;

            accordionContainer.appendChild(item);
        });

        accordionContainer.addEventListener('click', function (event) {
            const btn = event.target.closest('.faq-trigger');
            if (!btn) return;

            const item = btn.closest('.faq-item');
            const answer = item.querySelector('.faq-answer');
            const icon = item.querySelector('.faq-icon');
            const isOpen = answer.style.maxHeight && answer.style.maxHeight !== '0px';

            accordionContainer.querySelectorAll('.faq-item').forEach((other) => {
                if (other === item) return;
                const otherAnswer = other.querySelector('.faq-answer');
                const otherIcon = other.querySelector('.faq-icon');
                otherAnswer.style.maxHeight = '0px';
                otherIcon.classList.remove('is-open');
                other.style.borderColor = 'var(--border-color)';
            });

            if (!isOpen) {
                answer.style.maxHeight = answer.scrollHeight + 'px';
                icon.classList.add('is-open');
                item.style.borderColor = 'var(--accent)';
            } else {
                answer.style.maxHeight = '0px';
                icon.classList.remove('is-open');
                item.style.borderColor = 'var(--border-color)';
            }
        });
    });
</script>
@endsection
