@extends('layouts.public')

@section('title', 'Política de Privacidade - RR Veículos')

@section('content')
<x-institutional-layout
    eyebrow="LGPD & Dados"
    title="Política de Privacidade"
    subtitle="Como coletamos, usamos e protegemos os dados dos participantes, em conformidade com a LGPD."
    current="pages.privacy"
>
    <article class="rounded-2xl border px-6 py-8 sm:px-10 sm:py-10" style="border-color: var(--border-color); background: var(--bg-card);">
        <div class="institutional-prose">
            {!! $content !!}
        </div>
    </article>
</x-institutional-layout>
@endsection
