@extends('layouts.public')

@section('title', 'Contato - RR Veículos')

@section('content')
@php
    $whatsappUrl = $contact['whatsapp_url'] ?? null;
    $hasWhatsapp = filled($whatsappUrl);
    $mapUrl = filled($contact['map_query'] ?? null)
        ? 'https://www.google.com/maps/search/?api=1&query='.urlencode($contact['map_query'])
        : null;
    $plainExtra = trim(preg_replace('/\s+/', ' ', strip_tags($content ?? '')) ?? '');
    $hasExtraContent = $plainExtra !== ''
        && ! str_contains($plainExtra, '99999-9999')
        && ! str_contains($plainExtra, 'suporte@acaorrveiculos.com.br');
@endphp

<x-institutional-layout
    eyebrow="Atendimento"
    title="Fale conosco"
    subtitle="Suporte oficial para cotas, pagamentos PIX e dúvidas sobre as ações promocionais da RR Veículos."
    current="pages.contact"
    :meta="[
        ['icon' => 'fa-clock', 'label' => $contact['hours_weekdays']],
        ['icon' => 'fa-location-dot', 'label' => $contact['city']],
    ]"
>
    <div class="space-y-6 contact-page">
        @if ($hasWhatsapp)
            <a href="{{ $whatsappUrl }}"
               target="_blank"
               rel="noopener noreferrer"
               class="contact-hero-cta group relative block overflow-hidden rounded-2xl border px-6 py-7 sm:px-8 sm:py-8 transition"
               style="border-color: color-mix(in srgb, #25D366 45%, var(--border-color)); background: linear-gradient(135deg, color-mix(in srgb, #25D366 18%, var(--bg-card)), var(--bg-card));">
                <div class="absolute inset-0 pointer-events-none opacity-40" style="background: radial-gradient(ellipse 60% 80% at 0% 50%, color-mix(in srgb, #25D366 28%, transparent), transparent 60%);"></div>
                <div class="relative flex flex-col sm:flex-row sm:items-center gap-5">
                    <span class="shrink-0 w-14 h-14 rounded-2xl flex items-center justify-center text-white text-2xl" style="background: #25D366;">
                        <i class="fa-brands fa-whatsapp"></i>
                    </span>
                    <div class="min-w-0 flex-1 space-y-1">
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em]" style="color: #25D366;">Atendimento prioritário</p>
                        <h2 class="font-display text-xl sm:text-2xl font-bold theme-title tracking-tight">Falar no WhatsApp</h2>
                        <p class="text-sm theme-muted">{{ $contact['whatsapp'] }} · resposta no horário comercial</p>
                    </div>
                    <span class="inline-flex items-center gap-2 text-sm font-bold text-white px-4 py-2.5 rounded-xl self-start sm:self-center transition group-hover:translate-x-0.5" style="background: #25D366;">
                        Abrir conversa <i class="fa-solid fa-arrow-right text-xs"></i>
                    </span>
                </div>
            </a>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="mailto:{{ $contact['email'] }}"
               class="contact-channel rounded-2xl border p-5 sm:p-6 transition hover:border-[color:var(--accent)]"
               style="border-color: var(--border-color); background: var(--bg-card);">
                <div class="flex items-start gap-4">
                    <span class="shrink-0 w-11 h-11 rounded-xl border flex items-center justify-center" style="border-color: var(--border-color); background: var(--bg-primary); color: var(--accent);">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <div class="min-w-0 space-y-1">
                        <p class="text-[11px] font-bold uppercase tracking-[0.16em] theme-muted">E-mail</p>
                        <p class="font-display text-base font-bold theme-title break-all">{{ $contact['email'] }}</p>
                        <p class="text-xs theme-muted">Para protocolos, comprovantes e assuntos formais.</p>
                    </div>
                </div>
            </a>

            <div class="contact-channel rounded-2xl border p-5 sm:p-6"
                 style="border-color: var(--border-color); background: var(--bg-card);">
                <div class="flex items-start gap-4">
                    <span class="shrink-0 w-11 h-11 rounded-xl border flex items-center justify-center" style="border-color: var(--border-color); background: var(--bg-primary); color: var(--accent);">
                        <i class="fa-solid fa-clock"></i>
                    </span>
                    <div class="min-w-0 space-y-1">
                        <p class="text-[11px] font-bold uppercase tracking-[0.16em] theme-muted">Horário</p>
                        <p class="font-display text-base font-bold theme-title">{{ $contact['hours_weekdays'] }}</p>
                        @if (filled($contact['hours_saturday']))
                            <p class="text-sm theme-muted">{{ $contact['hours_saturday'] }}</p>
                        @endif
                        <p class="text-xs theme-muted pt-1">Fora do horário, deixe mensagem — retornamos no próximo expediente.</p>
                    </div>
                </div>
            </div>

            @if ($mapUrl)
                <a href="{{ $mapUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="contact-channel sm:col-span-2 rounded-2xl border p-5 sm:p-6 transition hover:border-[color:var(--accent)]"
                   style="border-color: var(--border-color); background: var(--bg-card);">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="flex items-start gap-4 flex-1 min-w-0">
                            <span class="shrink-0 w-11 h-11 rounded-xl border flex items-center justify-center" style="border-color: var(--border-color); background: var(--bg-primary); color: var(--accent);">
                                <i class="fa-solid fa-location-dot"></i>
                            </span>
                            <div class="min-w-0 space-y-1">
                                <p class="text-[11px] font-bold uppercase tracking-[0.16em] theme-muted">Endereço</p>
                                <p class="font-display text-base font-bold theme-title">{{ $contact['address'] }}</p>
                                <p class="text-xs theme-muted">RR Veículos · {{ $contact['city'] }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-2 text-xs font-bold px-3 py-2 rounded-lg border self-start" style="border-color: var(--border-color); color: var(--badge-text);">
                            Ver no mapa <i class="fa-solid fa-external-link text-[10px]"></i>
                        </span>
                    </div>
                </a>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <a href="{{ route('pages.faqs') }}"
               class="rounded-xl border px-4 py-3.5 flex items-center gap-3 transition hover:border-[color:var(--accent)]"
               style="border-color: var(--border-color); background: var(--bg-primary);">
                <i class="fa-solid fa-circle-question" style="color: var(--accent);"></i>
                <span class="text-sm font-semibold theme-title">Dúvidas frequentes</span>
            </a>
            <a href="{{ route('pages.regulation') }}"
               class="rounded-xl border px-4 py-3.5 flex items-center gap-3 transition hover:border-[color:var(--accent)]"
               style="border-color: var(--border-color); background: var(--bg-primary);">
                <i class="fa-solid fa-scale-balanced" style="color: var(--accent);"></i>
                <span class="text-sm font-semibold theme-title">Regulamento oficial</span>
            </a>
        </div>

        @if ($hasExtraContent)
            <article class="rounded-2xl border px-6 py-7 sm:px-8" style="border-color: var(--border-color); background: var(--bg-card);">
                <p class="text-[11px] font-bold uppercase tracking-[0.16em] theme-muted mb-4">Informações adicionais</p>
                <div class="institutional-prose">
                    {!! $content !!}
                </div>
            </article>
        @endif
    </div>
</x-institutional-layout>

<style>
    .contact-hero-cta:hover {
        transform: translateY(-1px);
    }
    .contact-channel:hover {
        background: color-mix(in srgb, var(--accent) 4%, var(--bg-card)) !important;
    }
</style>
@endsection
