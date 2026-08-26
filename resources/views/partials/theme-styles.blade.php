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

    .text-slate-400, .text-slate-500, .text-slate-300 {
        color: var(--text-secondary) !important;
    }

    /* Títulos/textos que usam text-white no markup seguem o texto do tema */
    .text-white {
        color: var(--text-primary) !important;
    }

    /* CTAs e badges sobre o vermelho da marca usam texto "on accent" */
    a[style*="var(--accent)"].text-white,
    button[style*="var(--accent)"].text-white,
    span[style*="var(--accent)"].text-white,
    div[style*="var(--accent)"].text-white,
    a[style*="var(--accent)"],
    button[style*="var(--accent)"],
    span[style*="background: var(--accent)"],
    div[style*="background: var(--accent)"],
    div[style*="background-color: var(--accent)"] {
        color: var(--on-accent) !important;
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
