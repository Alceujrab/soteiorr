@extends('layouts.public')

@section('title', 'Termos de Uso - RR Veículos')

@section('content')
<x-institutional-layout
    eyebrow="Regras da plataforma"
    title="Termos de Uso"
    subtitle="Condições de participação, elegibilidade, reservas e responsabilidades ao usar a plataforma."
    current="pages.terms"
>
    <article class="rounded-2xl border px-6 py-8 sm:px-10 sm:py-10" style="border-color: var(--border-color); background: var(--bg-card);">
        <div class="institutional-prose">
            {!! $content !!}
        </div>
    </article>
</x-institutional-layout>
@endsection
