<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Support\ThemePalette;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemePaletteSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_outputs_theme_css_variables_with_brand_red(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('--accent: #e61e25', false);
        $response->assertSee('--bg-primary:', false);
        $response->assertSee('--text-primary:', false);
    }

    public function test_admin_settings_page_shows_template_editor(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin_organizador',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.settings'));

        $response->assertOk();
        $response->assertSee('Editor de Template', false);
        $response->assertSee('Tema Claro', false);
        $response->assertSee('Tema Escuro', false);
        $response->assertSee('theme_light[accent]', false);
        $response->assertSee('theme_dark[accent]', false);
    }

    public function test_admin_can_save_light_and_dark_theme_colors(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin_organizador',
        ]);

        $light = ThemePalette::defaults()['light'];
        $dark = ThemePalette::defaults()['dark'];
        $light['accent'] = '#112233';
        $light['bg_primary'] = '#fafafa';
        $dark['accent'] = '#aabbcc';

        $payload = [
            'app_name' => 'Ação RR Veículos',
            'admin_security_email' => 'contato@rrsorteio.com',
            'min_tickets' => 1,
            'max_tickets' => 100,
            'page_regulation' => '',
            'page_about_us' => '',
            'page_contact' => '',
            'page_faqs' => '',
            'page_privacy_policy' => '',
            'page_terms_of_use' => '',
            'theme_light' => $light,
            'theme_dark' => $dark,
        ];

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), $payload);

        $response->assertRedirect(route('admin.settings'));

        $savedLight = json_decode((string) Setting::get(ThemePalette::SETTING_LIGHT), true);
        $savedDark = json_decode((string) Setting::get(ThemePalette::SETTING_DARK), true);

        $this->assertSame('#112233', $savedLight['accent']);
        $this->assertSame('#fafafa', $savedLight['bg_primary']);
        $this->assertSame('#aabbcc', $savedDark['accent']);

        $this->get('/')
            ->assertOk()
            ->assertSee('--accent: #112233', false)
            ->assertSee('--bg-primary: #fafafa', false);
    }
}
