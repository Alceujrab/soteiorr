@props([
    'eyebrow' => 'Institucional',
    'title',
    'subtitle' => null,
    'current' => null,
    'meta' => [],
])

@php
    $navItems = [
        ['route' => 'pages.about', 'label' => 'Sobre Nós', 'icon' => 'fa-building'],
        ['route' => 'pages.contact', 'label' => 'Contato', 'icon' => 'fa-headset'],
        ['route' => 'pages.faqs', 'label' => 'Dúvidas', 'icon' => 'fa-circle-question'],
        ['route' => 'pages.regulation', 'label' => 'Regulamento', 'icon' => 'fa-scale-balanced'],
        ['route' => 'pages.privacy', 'label' => 'Privacidade', 'icon' => 'fa-shield-halved'],
        ['route' => 'pages.terms', 'label' => 'Termos de Uso', 'icon' => 'fa-file-contract'],
    ];
@endphp

<style>
    .institutional-prose {
        color: var(--text-secondary);
        font-size: 0.98rem;
        line-height: 1.8;
    }
    .institutional-prose > h1:first-child {
        display: none;
    }
    .institutional-prose h1,
    .institutional-prose h2,
    .institutional-prose h3 {
        font-family: 'Space Grotesk', sans-serif;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        scroll-margin-top: 6.5rem;
    }
    .institutional-prose h1 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0 0 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-color);
    }
    .institutional-prose h2 {
        font-size: 1.2rem;
        font-weight: 700;
        margin: 2.25rem 0 0.85rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid color-mix(in srgb, var(--border-color) 80%, transparent);
    }
    .institutional-prose h2:first-of-type {
        margin-top: 0;
    }
    .institutional-prose h3 {
        font-size: 1.05rem;
        font-weight: 650;
        margin: 1.5rem 0 0.65rem;
    }
    .institutional-prose p {
        margin-bottom: 1.1rem;
    }
    .institutional-prose ul,
    .institutional-prose ol {
        margin: 0 0 1.15rem 1.2rem;
        padding: 0;
    }
    .institutional-prose ul { list-style: disc; }
    .institutional-prose ol { list-style: decimal; }
    .institutional-prose li {
        margin-bottom: 0.45rem;
    }
    .institutional-prose strong {
        color: var(--text-primary);
        font-weight: 600;
    }
    .institutional-prose a {
        color: var(--badge-text);
        text-decoration: underline;
        text-underline-offset: 2px;
    }
    .institutional-prose em {
        color: var(--text-primary);
    }
    .institutional-prose .regulation-closing {
        margin-top: 2.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .inst-nav-link {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.7rem 0.85rem;
        border-radius: 0.75rem;
        color: var(--text-secondary);
        font-size: 0.84rem;
        font-weight: 600;
        transition: color 0.15s ease, background-color 0.15s ease;
    }
    .inst-nav-link:hover {
        color: var(--text-primary);
        background: color-mix(in srgb, var(--accent) 8%, transparent);
    }
    .inst-nav-link.is-active {
        color: var(--on-accent);
        background: var(--accent);
    }
    .inst-nav-link.is-active i {
        color: var(--on-accent);
    }

    #regulation-toc a {
        display: block;
        padding: 0.45rem 0.65rem;
        border-radius: 0.6rem;
        color: var(--text-secondary);
        transition: all 0.15s ease;
        line-height: 1.35;
        font-size: 0.8rem;
    }
    #regulation-toc a:hover,
    #regulation-toc a.is-active {
        color: var(--text-primary);
        background: color-mix(in srgb, var(--accent) 8%, transparent);
    }
    #regulation-toc a.is-active {
        border-left: 2px solid var(--accent);
        padding-left: calc(0.65rem - 2px);
    }
</style>

<div class="institutional-page space-y-8 md:space-y-10">
    {{-- Header editorial --}}
    <header class="relative overflow-hidden rounded-2xl border" style="border-color: var(--border-color); background: var(--bg-card);">
        <div class="absolute inset-y-0 left-0 w-1.5" style="background: var(--accent);"></div>
        <div class="absolute inset-0 pointer-events-none opacity-60" style="background: radial-gradient(ellipse 70% 80% at 100% 0%, color-mix(in srgb, var(--accent) 12%, transparent), transparent 55%);"></div>
        <div class="relative px-6 py-8 sm:px-10 sm:py-11 space-y-4">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em]" style="color: var(--badge-text);">
                {{ $eyebrow }}
            </p>
            <h1 class="font-display text-3xl sm:text-4xl lg:text-[2.6rem] font-bold tracking-tight leading-[1.1] theme-title max-w-3xl">
                {{ $title }}
            </h1>
            @if($subtitle)
                <p class="text-sm sm:text-base theme-muted max-w-2xl leading-relaxed">
                    {{ $subtitle }}
                </p>
            @endif
            @if(! empty($meta))
                <div class="flex flex-wrap gap-2 pt-1">
                    @foreach($meta as $item)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs theme-muted border" style="border-color: var(--border-color); background: var(--bg-primary);">
                            @if(! empty($item['icon']))
                                <i class="fa-solid {{ $item['icon'] }}" style="color: var(--accent);"></i>
                            @endif
                            {{ $item['label'] }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- Navegação institucional --}}
        <aside class="lg:col-span-4 xl:col-span-3 space-y-5">
            <nav class="rounded-2xl border p-3 sticky top-24" style="border-color: var(--border-color); background: var(--bg-card);" aria-label="Páginas institucionais">
                <p class="px-2 pt-1 pb-2 text-[10px] font-bold uppercase tracking-[0.16em] theme-muted">
                    RR Veículos
                </p>
                <div class="space-y-0.5">
                    @foreach($navItems as $item)
                        <a href="{{ route($item['route']) }}"
                           class="inst-nav-link {{ $current === $item['route'] ? 'is-active' : '' }}">
                            <i class="fa-solid {{ $item['icon'] }} w-4 text-center text-sm" style="{{ $current === $item['route'] ? '' : 'color: var(--accent);' }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </nav>

            @isset($aside)
                {{ $aside }}
            @endisset
        </aside>

        {{-- Conteúdo --}}
        <div class="lg:col-span-8 xl:col-span-9 min-w-0">
            {{ $slot }}
        </div>
    </div>
</div>
