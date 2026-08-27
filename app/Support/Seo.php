<?php

namespace App\Support;

use App\Models\Raffle;
use App\Models\Setting;
use Illuminate\Support\Str;

class Seo
{
    public static function siteName(): string
    {
        return (string) Setting::get('app_name', config('app.name', 'Ação RR Veículos'));
    }

    public static function title(?string $pageTitle = null): string
    {
        $default = (string) Setting::get('seo_title', self::siteName().' | Ações Promocionais');

        if (blank($pageTitle)) {
            return $default;
        }

        return $pageTitle.' | '.self::siteName();
    }

    public static function description(): string
    {
        return (string) Setting::get(
            'seo_description',
            'Participe das Ações Promocionais da RR Veículos. Escolha seu pacote, garanta seus números da sorte e concorra a veículos com transparência e segurança.'
        );
    }

    public static function keywords(): string
    {
        return (string) Setting::get(
            'seo_keywords',
            'ação promocional, RR Veículos, Água Boa, números da sorte, cotas, veículos, MT'
        );
    }

    public static function verificationCode(): ?string
    {
        $code = Setting::get('google_site_verification');

        return filled($code) ? (string) $code : null;
    }

    /**
     * @return array<string, mixed>
     */
    public static function organizationJsonLd(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => self::siteName(),
            'url' => url('/'),
            'logo' => asset('images/logo-rr.png'),
            'description' => self::description(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function websiteJsonLd(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => self::siteName(),
            'url' => url('/'),
            'description' => self::description(),
            'inLanguage' => 'pt-BR',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/').'?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * @return list<array{loc: string, lastmod: string, changefreq: string, priority: string}>
     */
    public static function sitemapUrls(): array
    {
        $urls = [
            ['loc' => url('/'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => route('pages.about'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => route('pages.contact'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => route('pages.faqs'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('pages.regulation'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('pages.privacy'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'yearly', 'priority' => '0.4'],
            ['loc' => route('pages.terms'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'yearly', 'priority' => '0.4'],
            ['loc' => route('register'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('login'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.5'],
        ];

        Raffle::query()
            ->where('status', 'active')
            ->orderByDesc('id')
            ->get(['id', 'updated_at'])
            ->each(function (Raffle $raffle) use (&$urls): void {
                $urls[] = [
                    'loc' => route('raffles.show', $raffle),
                    'lastmod' => optional($raffle->updated_at)->toAtomString() ?: now()->toAtomString(),
                    'changefreq' => 'daily',
                    'priority' => '0.9',
                ];
            });

        return $urls;
    }

    public static function slugify(string $value): string
    {
        return Str::slug($value);
    }
}
