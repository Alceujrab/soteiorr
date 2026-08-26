<?php

namespace App\Support;

use App\Models\Setting;

class ThemePalette
{
    public const SETTING_LIGHT = 'theme_colors_light';

    public const SETTING_DARK = 'theme_colors_dark';

    /**
     * Tokens editáveis (chave CSS sem --).
     *
     * @return array<string, array{label: string, group: string, hint: string}>
     */
    public static function definitions(): array
    {
        return [
            'bg_primary' => [
                'label' => 'Fundo da página',
                'group' => 'Fundos',
                'hint' => 'Cor de fundo geral do site',
            ],
            'bg_sidebar' => [
                'label' => 'Fundo sidebar / nav',
                'group' => 'Fundos',
                'hint' => 'Menu, drawer e barra inferior',
            ],
            'bg_card' => [
                'label' => 'Fundo dos cards',
                'group' => 'Fundos',
                'hint' => 'Cards, formulários e painéis',
            ],
            'text_primary' => [
                'label' => 'Texto principal',
                'group' => 'Textos',
                'hint' => 'Títulos e textos fortes',
            ],
            'text_secondary' => [
                'label' => 'Texto secundário',
                'group' => 'Textos',
                'hint' => 'Legendas e textos auxiliares',
            ],
            'on_accent' => [
                'label' => 'Texto sobre o destaque',
                'group' => 'Textos',
                'hint' => 'Texto em botões vermelhos (geralmente branco)',
            ],
            'accent' => [
                'label' => 'Cor de destaque (vermelho RR)',
                'group' => 'Marca',
                'hint' => 'Botões, badges e CTAs — padrão da logo #E61E25',
            ],
            'accent_hover' => [
                'label' => 'Destaque (hover)',
                'group' => 'Marca',
                'hint' => 'Estado ao passar o mouse',
            ],
            'accent_soft' => [
                'label' => 'Destaque suave (gradientes)',
                'group' => 'Marca',
                'hint' => 'Segunda cor de barras de progresso',
            ],
            'badge_text' => [
                'label' => 'Texto de destaque / links',
                'group' => 'Marca',
                'hint' => 'Links e labels coloridos',
            ],
            'badge_bg' => [
                'label' => 'Fundo suave do destaque',
                'group' => 'Marca',
                'hint' => 'Fundos leves de badges (hex ou rgba)',
            ],
            'accent_glow' => [
                'label' => 'Brilho / sombra do destaque',
                'group' => 'Marca',
                'hint' => 'Sombra dos botões CTA (ex: rgba)',
            ],
            'danger' => [
                'label' => 'Cor de alerta / sair',
                'group' => 'Marca',
                'hint' => 'Logout e avisos críticos',
            ],
            'border_color' => [
                'label' => 'Bordas',
                'group' => 'Bordas & sombra',
                'hint' => 'Contornos de cards (hex ou rgba)',
            ],
            'metal' => [
                'label' => 'Cinza metálico',
                'group' => 'Bordas & sombra',
                'hint' => 'Detalhes e ícones neutros',
            ],
            'card_shadow' => [
                'label' => 'Sombra dos cards',
                'group' => 'Bordas & sombra',
                'hint' => 'Ex: 0 10px 30px rgba(15, 23, 42, 0.08)',
            ],
        ];
    }

    /**
     * Defaults alinhados ao vermelho da logomarca RR (#E61E25).
     *
     * @return array{light: array<string, string>, dark: array<string, string>}
     */
    public static function defaults(): array
    {
        return [
            'light' => [
                'bg_primary' => '#f3f4f6',
                'bg_sidebar' => '#ffffff',
                'bg_card' => '#ffffff',
                'text_primary' => '#1a1a1a',
                'text_secondary' => '#5c5c5c',
                'on_accent' => '#ffffff',
                'accent' => '#e61e25',
                'accent_hover' => '#c41820',
                'accent_soft' => '#ff5a67',
                'badge_text' => '#e61e25',
                'badge_bg' => 'rgba(230, 30, 37, 0.10)',
                'accent_glow' => 'rgba(230, 30, 37, 0.28)',
                'danger' => '#dc2626',
                'border_color' => 'rgba(26, 26, 26, 0.12)',
                'metal' => '#6b7280',
                'card_shadow' => '0 10px 30px rgba(15, 23, 42, 0.08)',
            ],
            'dark' => [
                'bg_primary' => '#0c0e12',
                'bg_sidebar' => '#08090c',
                'bg_card' => 'rgba(22, 26, 33, 0.96)',
                'text_primary' => '#f4f4f5',
                'text_secondary' => '#9ca3af',
                'on_accent' => '#ffffff',
                'accent' => '#e61e25',
                'accent_hover' => '#c41820',
                'accent_soft' => '#ff5a67',
                'badge_text' => '#ff4d5a',
                'badge_bg' => 'rgba(230, 30, 37, 0.14)',
                'accent_glow' => 'rgba(230, 30, 37, 0.40)',
                'danger' => '#f87171',
                'border_color' => 'rgba(230, 30, 37, 0.28)',
                'metal' => '#8b939e',
                'card_shadow' => '0 8px 28px rgba(0, 0, 0, 0.35)',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function light(): array
    {
        return self::resolve(self::SETTING_LIGHT, self::defaults()['light']);
    }

    /**
     * @return array<string, string>
     */
    public static function dark(): array
    {
        return self::resolve(self::SETTING_DARK, self::defaults()['dark']);
    }

    /**
     * @param  array<string, string>  $defaults
     * @return array<string, string>
     */
    private static function resolve(string $settingKey, array $defaults): array
    {
        $raw = Setting::get($settingKey);
        $saved = [];

        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $saved = $decoded;
            }
        }

        $merged = array_merge($defaults, array_intersect_key($saved, $defaults));

        foreach ($merged as $key => $value) {
            $merged[$key] = is_string($value) ? trim($value) : $defaults[$key];
            if ($merged[$key] === '') {
                $merged[$key] = $defaults[$key];
            }
        }

        return $merged;
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, string>
     */
    public static function sanitize(array $input, string $mode = 'light'): array
    {
        $defaults = self::defaults()[$mode] ?? self::defaults()['light'];
        $clean = [];

        foreach ($defaults as $key => $default) {
            $value = isset($input[$key]) ? trim((string) $input[$key]) : $default;
            $clean[$key] = $value !== '' ? $value : $default;
        }

        return $clean;
    }

    /**
     * CSS variables block for :root / body.dark-theme.
     *
     * @param  array<string, string>  $palette
     */
    public static function toCssVariables(array $palette): string
    {
        $map = [
            'bg_primary' => '--bg-primary',
            'bg_sidebar' => '--bg-sidebar',
            'bg_card' => '--bg-card',
            'text_primary' => '--text-primary',
            'text_secondary' => '--text-secondary',
            'on_accent' => '--on-accent',
            'accent' => '--accent',
            'accent_hover' => '--accent-hover',
            'accent_soft' => '--accent-soft',
            'badge_text' => '--badge-text',
            'badge_bg' => '--badge-bg',
            'accent_glow' => '--accent-glow',
            'danger' => '--danger',
            'border_color' => '--border-color',
            'metal' => '--metal',
            'card_shadow' => '--card-shadow',
        ];

        $lines = [];
        foreach ($map as $key => $cssVar) {
            if (! empty($palette[$key])) {
                $lines[] = '            '.$cssVar.': '.$palette[$key].';';
            }
        }

        return implode("\n", $lines);
    }
}
