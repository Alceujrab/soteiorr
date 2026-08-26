<?php

namespace App\Support;

use App\Models\Setting;

class ThemePalette
{
    public const SETTING_LIGHT = 'theme_colors_light';

    public const SETTING_DARK = 'theme_colors_dark';

    public const SETTING_ADMIN_LIGHT = 'theme_colors_admin_light';

    public const SETTING_ADMIN_DARK = 'theme_colors_admin_dark';

    /**
     * @return array<string, string>
     */
    public static function settingKeys(): array
    {
        return [
            'light' => self::SETTING_LIGHT,
            'dark' => self::SETTING_DARK,
            'admin_light' => self::SETTING_ADMIN_LIGHT,
            'admin_dark' => self::SETTING_ADMIN_DARK,
        ];
    }

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
                'hint' => 'Cor de fundo geral',
            ],
            'bg_sidebar' => [
                'label' => 'Fundo sidebar / nav',
                'group' => 'Fundos',
                'hint' => 'Menu lateral e barras',
            ],
            'bg_card' => [
                'label' => 'Fundo dos cards',
                'group' => 'Fundos',
                'hint' => 'Cards e painéis',
            ],
            'panel_bg' => [
                'label' => 'Fundo de blocos / abas',
                'group' => 'Fundos',
                'hint' => 'Áreas secundárias e highlight de abas',
            ],
            'input_bg' => [
                'label' => 'Fundo dos inputs',
                'group' => 'Formulários',
                'hint' => 'Campos de texto, select e textarea',
            ],
            'input_border' => [
                'label' => 'Borda dos inputs',
                'group' => 'Formulários',
                'hint' => 'Contorno dos campos',
            ],
            'input_text' => [
                'label' => 'Texto dos inputs',
                'group' => 'Formulários',
                'hint' => 'Cor digitada nos campos',
            ],
            'text_primary' => [
                'label' => 'Texto principal',
                'group' => 'Textos',
                'hint' => 'Títulos e textos fortes',
            ],
            'text_secondary' => [
                'label' => 'Texto secundário',
                'group' => 'Textos',
                'hint' => 'Legendas e labels',
            ],
            'on_accent' => [
                'label' => 'Texto sobre o destaque',
                'group' => 'Textos',
                'hint' => 'Texto em botões vermelhos',
            ],
            'accent' => [
                'label' => 'Cor de destaque (vermelho RR)',
                'group' => 'Marca',
                'hint' => 'Botões, badges e CTAs — #E61E25',
            ],
            'accent_hover' => [
                'label' => 'Destaque (hover)',
                'group' => 'Marca',
                'hint' => 'Estado ao passar o mouse',
            ],
            'accent_soft' => [
                'label' => 'Destaque suave (gradientes)',
                'group' => 'Marca',
                'hint' => 'Segunda cor de barras',
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
                'hint' => 'Sombra dos botões CTA',
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
     * @return array<string, array<string, string>>
     */
    public static function defaults(): array
    {
        $siteLight = [
            'bg_primary' => '#f3f4f6',
            'bg_sidebar' => '#ffffff',
            'bg_card' => '#ffffff',
            'panel_bg' => '#eef2f7',
            'input_bg' => '#ffffff',
            'input_border' => '#d1d5db',
            'input_text' => '#111827',
            'text_primary' => '#111827',
            'text_secondary' => '#4b5563',
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
        ];

        $siteDark = [
            'bg_primary' => '#0c0e12',
            'bg_sidebar' => '#08090c',
            'bg_card' => 'rgba(22, 26, 33, 0.96)',
            'panel_bg' => 'rgba(15, 23, 42, 0.55)',
            'input_bg' => '#0f172a',
            'input_border' => '#1e293b',
            'input_text' => '#e2e8f0',
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
        ];

        $adminLight = array_merge($siteLight, [
            'bg_primary' => '#f1f5f9',
            'bg_sidebar' => '#ffffff',
            'bg_card' => '#ffffff',
            'panel_bg' => '#e2e8f0',
            'input_bg' => '#ffffff',
            'input_border' => '#cbd5e1',
            'input_text' => '#0f172a',
            'text_primary' => '#0f172a',
            'text_secondary' => '#475569',
            'border_color' => 'rgba(15, 23, 42, 0.12)',
            'card_shadow' => '0 8px 24px rgba(15, 23, 42, 0.08)',
        ]);

        $adminDark = array_merge($siteDark, [
            'bg_primary' => '#0b1220',
            'bg_sidebar' => '#070b14',
            'bg_card' => 'rgba(15, 23, 42, 0.92)',
            'panel_bg' => 'rgba(15, 23, 42, 0.65)',
            'input_bg' => '#0f172a',
            'input_border' => '#1e293b',
            'input_text' => '#e2e8f0',
            'text_primary' => '#f8fafc',
            'text_secondary' => '#94a3b8',
            'border_color' => 'rgba(148, 163, 184, 0.22)',
        ]);

        return [
            'light' => $siteLight,
            'dark' => $siteDark,
            'admin_light' => $adminLight,
            'admin_dark' => $adminDark,
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
     * @return array<string, string>
     */
    public static function adminLight(): array
    {
        return self::resolve(self::SETTING_ADMIN_LIGHT, self::defaults()['admin_light']);
    }

    /**
     * @return array<string, string>
     */
    public static function adminDark(): array
    {
        return self::resolve(self::SETTING_ADMIN_DARK, self::defaults()['admin_dark']);
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
     * @param  array<string, string>  $palette
     */
    public static function toCssVariables(array $palette): string
    {
        $map = [
            'bg_primary' => '--bg-primary',
            'bg_sidebar' => '--bg-sidebar',
            'bg_card' => '--bg-card',
            'panel_bg' => '--panel-bg',
            'input_bg' => '--input-bg',
            'input_border' => '--input-border',
            'input_text' => '--input-text',
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
