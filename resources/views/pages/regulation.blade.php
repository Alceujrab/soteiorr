@extends('layouts.public')

@section('title', 'Regulamento - RR Veículos')

@section('content')
<x-institutional-layout
    eyebrow="Documento oficial"
    title="Regulamento da Promoção"
    subtitle="Um clássico. Uma chance. Uma história para continuar."
    current="pages.regulation"
    :meta="[
        ['icon' => 'fa-calendar', 'label' => 'Início: 20/09/2026'],
        ['icon' => 'fa-flag-checkered', 'label' => 'Encerramento: 20/12/2026'],
        ['icon' => 'fa-car', 'label' => 'Prêmio: Santana 1997'],
    ]"
>
    <x-slot:aside>
        <div class="rounded-2xl border p-5 sticky top-24 hidden lg:block" style="border-color: var(--border-color); background: var(--bg-card);">
            <h2 class="text-[10px] font-bold uppercase tracking-[0.16em] theme-muted mb-3 flex items-center gap-2">
                <i class="fa-solid fa-list-ul" style="color: var(--accent);"></i> Índice do regulamento
            </h2>
            <nav id="regulation-toc" class="max-h-[45vh] overflow-y-auto pr-1 space-y-0.5" aria-label="Índice">
                <p class="theme-muted text-xs px-1">Carregando seções...</p>
            </nav>
        </div>
    </x-slot:aside>

    <article class="rounded-2xl border px-6 py-8 sm:px-10 sm:py-10" style="border-color: var(--border-color); background: var(--bg-card);">
        <div class="lg:hidden mb-6 rounded-xl border p-4" style="border-color: var(--border-color); background: var(--bg-primary);">
            <h2 class="text-[10px] font-bold uppercase tracking-[0.16em] theme-muted mb-2">Índice</h2>
            <nav id="regulation-toc-mobile" class="space-y-0.5 text-sm"></nav>
        </div>

        <div id="regulation-content" class="institutional-prose">
            {!! $content !!}
        </div>
    </article>
</x-institutional-layout>

<script>
    (function () {
        const content = document.getElementById('regulation-content');
        const toc = document.getElementById('regulation-toc');
        const tocMobile = document.getElementById('regulation-toc-mobile');
        if (!content || !toc) return;

        const headings = content.querySelectorAll('h2');
        if (!headings.length) {
            toc.innerHTML = '<p class="theme-muted text-xs px-1">Sem seções disponíveis.</p>';
            return;
        }

        toc.innerHTML = '';
        if (tocMobile) tocMobile.innerHTML = '';
        const links = [];

        headings.forEach((heading, index) => {
            if (!heading.id) {
                heading.id = 'secao-' + (index + 1);
            }

            const makeLink = () => {
                const link = document.createElement('a');
                link.href = '#' + heading.id;
                link.textContent = heading.textContent.trim();
                return link;
            };

            const desktopLink = makeLink();
            toc.appendChild(desktopLink);
            links.push({ id: heading.id, el: desktopLink });

            if (tocMobile) {
                tocMobile.appendChild(makeLink());
            }
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }
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
