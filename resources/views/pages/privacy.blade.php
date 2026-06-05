@extends('layouts.public')

@section('title', 'Política de Privacidade - Ação RR Veículos')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="glass-card rounded-2xl p-6 sm:p-10 text-slate-300 leading-relaxed shadow-xl border" style="border-color: var(--border-color);">
        <style>
            .dynamic-prose h1 { font-size: 1.875rem; font-weight: 800; color: #ffffff; margin-bottom: 1.25rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color); }
            .dynamic-prose h2 { font-size: 1.5rem; font-weight: 700; color: #ffffff; margin-top: 2rem; margin-bottom: 1rem; }
            .dynamic-prose p { margin-bottom: 1.25rem; font-size: 0.975rem; line-height: 1.75; }
            .dynamic-prose ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.25rem; }
            .dynamic-prose li { margin-bottom: 0.5rem; font-size: 0.975rem; }
            .dynamic-prose strong { color: #ffffff; font-weight: 600; }
        </style>
        <div class="dynamic-prose">
            {!! $content !!}
        </div>
    </div>
</div>
@endsection
