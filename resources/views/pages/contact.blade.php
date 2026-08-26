@extends('layouts.public')

@section('title', 'Contato - RR Veículos')

@section('content')
<x-institutional-layout
    eyebrow="Atendimento"
    title="Fale conosco"
    subtitle="Canais oficiais de suporte para dúvidas sobre cotas, pagamentos e ações promocionais."
    current="pages.contact"
    :meta="[
        ['icon' => 'fa-clock', 'label' => 'Seg–Sex 08h–18h'],
        ['icon' => 'fa-location-dot', 'label' => 'Água Boa - MT'],
    ]"
>
    <article class="rounded-2xl border px-6 py-8 sm:px-10 sm:py-10" style="border-color: var(--border-color); background: var(--bg-card);">
        <div class="institutional-prose">
            {!! $content !!}
        </div>
    </article>
</x-institutional-layout>
@endsection
