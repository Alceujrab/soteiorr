<style>
    :root {
{!! \App\Support\ThemePalette::toCssVariables(\App\Support\ThemePalette::light()) !!}
    }

    body.dark-theme {
{!! \App\Support\ThemePalette::toCssVariables(\App\Support\ThemePalette::dark()) !!}
    }

    body {
        font-family: 'DM Sans', sans-serif;
        background-color: var(--bg-primary) !important;
        color: var(--text-primary) !important;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    h1, h2, h3, .font-display {
        font-family: 'Space Grotesk', sans-serif;
    }

    .glass-card {
        background: var(--bg-card) !important;
        border-color: var(--border-color) !important;
        box-shadow: var(--card-shadow);
    }

    .brand-logo {
        height: 2.25rem;
        width: auto;
        object-fit: contain;
    }

    .photo-brand-mark {
        position: absolute;
        right: 0.75rem;
        bottom: 0.75rem;
        height: 1.75rem;
        width: auto;
        opacity: 0.92;
        filter: drop-shadow(0 2px 6px rgba(0,0,0,0.45));
        pointer-events: none;
    }

    /* Títulos semânticos (não usar text-white no tema claro) */
    .theme-title {
        color: var(--text-primary) !important;
    }
    .theme-muted {
        color: var(--text-secondary) !important;
    }

    /* Menu / nav links — hover funciona no claro e no escuro */
    .nav-link {
        color: var(--text-secondary);
        transition: color 0.2s ease, background-color 0.2s ease;
    }
    .nav-link:hover {
        color: var(--text-primary) !important;
        background-color: color-mix(in srgb, var(--accent) 10%, transparent) !important;
    }
    .nav-link.is-active {
        color: var(--on-accent) !important;
        background-color: var(--accent) !important;
    }
    .nav-link-quiet {
        color: var(--text-secondary);
        transition: color 0.2s ease;
    }
    .nav-link-quiet:hover {
        color: var(--text-primary) !important;
    }

    /* Hero: máscara suave — sem faixa preta ofuscando a foto */
    .hero-photo-mask {
        background: linear-gradient(to top, rgba(15, 23, 42, 0.18), transparent 45%);
    }
    @media (min-width: 1024px) {
        .hero-photo-mask {
            background: linear-gradient(to right, rgba(15, 23, 42, 0.12), transparent 55%);
        }
    }
    body.dark-theme .hero-photo-mask {
        background: linear-gradient(to top, rgba(12, 14, 18, 0.55), transparent 50%);
    }
    @media (min-width: 1024px) {
        body.dark-theme .hero-photo-mask {
            background: linear-gradient(to right, rgba(12, 14, 18, 0.45), transparent 60%);
        }
    }

    /* Texto branco real só em cima de mídia / fundo escuro local */
    .on-media,
    .on-media .text-white {
        color: #ffffff !important;
    }

    .bg-blue-600, .bg-emerald-600, .bg-slate-800, .bg-slate-950, .bg-amber-600 {
        background-color: var(--accent) !important;
        color: var(--on-accent) !important;
    }
    .hover\:bg-blue-500:hover, .hover\:bg-emerald-500:hover, .hover\:bg-slate-700:hover, .hover\:bg-amber-500:hover {
        background-color: var(--accent-hover) !important;
    }
    .text-blue-500, .text-blue-400, .text-emerald-400, .text-amber-400,
    .text-indigo-400, .text-purple-400 {
        color: var(--badge-text) !important;
    }
    .bg-blue-500\/10, .bg-emerald-500\/10, .bg-blue-600\/10, .bg-amber-500\/10, .bg-indigo-500\/10, .bg-purple-500\/10 {
        background-color: var(--badge-bg) !important;
    }
    .border-blue-500\/30, .border-emerald-500\/30, .border-slate-800, .border-amber-500\/30, .border-indigo-500\/20, .border-purple-500\/20 {
        border-color: var(--border-color) !important;
    }
    .text-red-400, .text-red-500 {
        color: var(--danger) !important;
    }
    .text-amber-950 {
        color: var(--on-accent) !important;
    }
</style>
