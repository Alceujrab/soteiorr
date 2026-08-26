@extends('layouts.public')

@section('title', 'Regulamento - Ação RR Veículos')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <!-- Hero -->
    <header class="relative overflow-hidden rounded-2xl border px-6 py-10 sm:px-10 sm:py-12" style="border-color: var(--border-color); background: linear-gradient(135deg, rgba(170,124,17,0.12), rgba(15,23,42,0.85));">
        <div class="absolute inset-0 pointer-events-none opacity-40" style="background-image: radial-gradient(circle at 12% 20%, rgba(170,124,17,0.25), transparent 42%), radial-gradient(circle at 88% 10%, rgba(59,130,246,0.12), transparent 35%);"></div>
        <div class="relative space-y-4 max-w-3xl">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-semibold uppercase tracking-[0.14em] border" style="border-color: rgba(170,124,17,0.35); color: #d4a84b; background: rgba(170,124,17,0.08);">
                <i class="fa-solid fa-scale-balanced"></i> Documento Oficial
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                Regulamento da Promoção
            </h1>
            <p class="text-base sm:text-lg text-slate-300 italic leading-relaxed">
                “Um clássico. Uma chance. Uma história para continuar.”
            </p>
            <div class="flex flex-wrap gap-3 pt-2 text-xs text-slate-400">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-950/50 border" style="border-color: var(--border-color);">
                    <i class="fa-regular fa-calendar"></i> Início: 20/09/2026
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-950/50 border" style="border-color: var(--border-color);">
                    <i class="fa-solid fa-flag-checkered"></i> Encerramento: 20/12/2026
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-950/50 border" style="border-color: var(--border-color);">
                    <i class="fa-solid fa-car"></i> Prêmio: Santana 1997
                </span>
            </div>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- TOC -->
        <aside class="lg:col-span-4 xl:col-span-3">
            <div class="glass-card rounded-2xl p-5 border sticky top-24" style="border-color: var(--border-color);">
                <h2 class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-list-ul" style="color: var(--accent);"></i> Índice
                </h2>
                <nav id="regulation-toc" class="max-h-[70vh] overflow-y-auto pr-1 space-y-1 text-sm">
                    <p class="text-slate-500 text-xs">Carregando seções...</p>
                </nav>
            </div>
        </aside>

        <!-- Content -->
        <article class="lg:col-span-8 xl:col-span-9">
            <div class="glass-card rounded-2xl p-6 sm:p-10 border shadow-xl" style="border-color: var(--border-color);">
                <style>
                    .regulation-prose {
                        color: #cbd5e1;
                        font-size: 0.975rem;
                        line-height: 1.8;
                    }
                    .regulation-prose > h1:first-child,
                    .regulation-prose > p.regulation-tagline {
                        display: none;
                    }
                    .regulation-prose h2 {
                        scroll-margin-top: 6.5rem;
                        font-size: 1.2rem;
                        font-weight: 700;
                        color: #fff;
                        margin-top: 2.25rem;
                        margin-bottom: 0.9rem;
                        padding-bottom: 0.55rem;
                        border-bottom: 1px solid rgba(148, 163, 184, 0.18);
                        letter-spacing: -0.01em;
                    }
                    .regulation-prose h2:first-of-type {
                        margin-top: 0;
                    }
                    .regulation-prose h3 {
                        scroll-margin-top: 6.5rem;
                        font-size: 1.02rem;
                        font-weight: 650;
                        color: #f8fafc;
                        margin-top: 1.5rem;
                        margin-bottom: 0.65rem;
                    }
                    .regulation-prose p {
                        margin-bottom: 1rem;
                    }
                    .regulation-prose ul,
                    .regulation-prose ol {
                        margin: 0 0 1.1rem 1.15rem;
                        padding: 0;
                    }
                    .regulation-prose ul {
                        list-style: disc;
                    }
                    .regulation-prose ol {
                        list-style: decimal;
                    }
                    .regulation-prose li {
                        margin-bottom: 0.4rem;
                    }
                    .regulation-prose strong {
                        color: #fff;
                        font-weight: 600;
                    }
                    .regulation-prose em {
                        color: #e2e8f0;
                    }
                    .regulation-prose .regulation-closing {
                        margin-top: 2.5rem;
                        padding-top: 1.5rem;
                        border-top: 1px solid rgba(148, 163, 184, 0.18);
                        color: #94a3b8;
                        font-size: 0.9rem;
                    }
                    #regulation-toc a {
                        display: block;
                        padding: 0.45rem 0.65rem;
                        border-radius: 0.6rem;
                        color: #94a3b8;
                        transition: all 0.15s ease;
                        line-height: 1.35;
                        font-size: 0.8rem;
                    }
                    #regulation-toc a:hover,
                    #regulation-toc a.is-active {
                        color: #fff;
                        background: rgba(148, 163, 184, 0.08);
                    }
                    #regulation-toc a.is-active {
                        border-left: 2px solid var(--accent);
                        padding-left: calc(0.65rem - 2px);
                    }
                </style>

                <div id="regulation-content" class="regulation-prose">
                    {!! $content !!}
                </div>
            </div>
        </article>
    </div>
</div>

<script>
    (function () {
        const content = document.getElementById('regulation-content');
        const toc = document.getElementById('regulation-toc');
        if (!content || !toc) return;

        const headings = content.querySelectorAll('h2');
        if (!headings.length) {
            toc.innerHTML = '<p class="text-slate-500 text-xs">Sem seções disponíveis.</p>';
            return;
        }

        toc.innerHTML = '';
        const links = [];

        headings.forEach((heading, index) => {
            if (!heading.id) {
                heading.id = 'secao-' + (index + 1);
            }

            const link = document.createElement('a');
            link.href = '#' + heading.id;
            link.textContent = heading.textContent.trim();
            toc.appendChild(link);
            links.push({ id: heading.id, el: link });
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                links.forEach(({ id, el }) => {
                    el.classList.toggle('is-active', id === entry.target.id);
                });
            });
        }, {
            rootMargin: '-20% 0px -65% 0px',
            threshold: 0.01,
        });

        headings.forEach((heading) => observer.observe(heading));
    })();
</script>
@endsection
