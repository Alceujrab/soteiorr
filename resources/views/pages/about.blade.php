@extends('layouts.public')

@section('title', 'Sobre Nós - RR Veículos')

@section('content')
<x-institutional-layout
    eyebrow="Quem somos"
    title="Sobre a RR Veículos"
    subtitle="Transparência, credibilidade e veículos reais — a plataforma de ações promocionais de Água Boa - MT."
    current="pages.about"
>
    <article class="rounded-2xl border px-6 py-8 sm:px-10 sm:py-10" style="border-color: var(--border-color); background: var(--bg-card);">
        <div class="institutional-prose">
            {!! $content !!}
        </div>
    </article>
</x-institutional-layout>
@endsection
