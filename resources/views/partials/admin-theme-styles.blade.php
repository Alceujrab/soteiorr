{{-- Paleta exclusiva do painel admin (independente do site público) --}}
<style>
    body.admin-panel {
{!! \App\Support\ThemePalette::toCssVariables(\App\Support\ThemePalette::adminLight()) !!}
    }

    body.admin-panel.dark-theme {
{!! \App\Support\ThemePalette::toCssVariables(\App\Support\ThemePalette::adminDark()) !!}
    }

    body.admin-panel .text-white {
        color: var(--text-primary) !important;
    }
    body.admin-panel .text-slate-200,
    body.admin-panel .text-slate-300 {
        color: var(--input-text) !important;
    }
    body.admin-panel .text-slate-400,
    body.admin-panel .text-slate-500,
    body.admin-panel .text-slate-600 {
        color: var(--text-secondary) !important;
    }
    body.admin-panel .bg-slate-900,
    body.admin-panel .bg-slate-950,
    body.admin-panel .bg-slate-800 {
        background-color: var(--input-bg) !important;
        color: var(--input-text) !important;
    }
    body.admin-panel .bg-slate-900\/50,
    body.admin-panel .bg-slate-900\/40,
    body.admin-panel .bg-slate-900\/20 {
        background-color: var(--panel-bg) !important;
    }
    body.admin-panel .border-slate-800,
    body.admin-panel .border-slate-700 {
        border-color: var(--input-border) !important;
    }
    body.admin-panel input:not([type="color"]):not([type="checkbox"]):not([type="radio"]):not([type="file"]),
    body.admin-panel select,
    body.admin-panel textarea {
        background-color: var(--input-bg) !important;
        border-color: var(--input-border) !important;
        color: var(--input-text) !important;
    }
    body.admin-panel .ql-toolbar.ql-snow,
    body.admin-panel .ql-container.ql-snow {
        border-color: var(--input-border) !important;
    }
    body.admin-panel .ql-toolbar.ql-snow {
        background-color: var(--panel-bg) !important;
    }
    body.admin-panel .ql-editor {
        background-color: var(--input-bg) !important;
        color: var(--input-text) !important;
    }
</style>
