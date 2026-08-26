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
        $response->assertDontSee('class="admin-panel', false);
        $response->assertDontSee('partials.admin-theme', false);
    }

    public function test_admin_settings_page_shows_site_and_admin_template_editors(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin_organizador',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.settings'));

        $response->assertOk();
        $response->assertSee('Editor de Template', false);
        $response->assertSee('Site — Tema Claro', false);
        $response->assertSee('Admin — Tema Claro', false);
        $response->assertSee('Admin — Tema Escuro', false);
        $response->assertSee('theme_light[accent]', false);
        $response->assertSee('theme_admin_light[accent]', false);
        $response->assertSee('theme_admin_dark[input_bg]', false);
        $response->assertSee('admin-panel', false);
    }

    public function test_admin_can_save_site_and_admin_theme_colors_independently(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin_organizador',
        ]);

        $light = ThemePalette::defaults()['light'];
        $dark = ThemePalette::defaults()['dark'];
        $adminLight = ThemePalette::defaults()['admin_light'];
        $adminDark = ThemePalette::defaults()['admin_dark'];

        $light['accent'] = '#112233';
        $light['bg_primary'] = '#fafafa';
        $dark['accent'] = '#aabbcc';
        $adminLight['input_bg'] = '#fefefe';
        $adminDark['input_bg'] = '#101820';

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
            'theme_admin_light' => $adminLight,
            'theme_admin_dark' => $adminDark,
        ];

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), $payload);

        $response->assertRedirect(route('admin.settings'));

        $savedLight = json_decode((string) Setting::get(ThemePalette::SETTING_LIGHT), true);
        $savedDark = json_decode((string) Setting::get(ThemePalette::SETTING_DARK), true);
        $savedAdminLight = json_decode((string) Setting::get(ThemePalette::SETTING_ADMIN_LIGHT), true);
        $savedAdminDark = json_decode((string) Setting::get(ThemePalette::SETTING_ADMIN_DARK), true);

        $this->assertSame('#112233', $savedLight['accent']);
        $this->assertSame('#fafafa', $savedLight['bg_primary']);
        $this->assertSame('#aabbcc', $savedDark['accent']);
        $this->assertSame('#fefefe', $savedAdminLight['input_bg']);
        $this->assertSame('#101820', $savedAdminDark['input_bg']);

        $this->get('/')
            ->assertOk()
            ->assertSee('--accent: #112233', false)
            ->assertSee('--bg-primary: #fafafa', false)
            ->assertDontSee('--input-bg: #fefefe', false);

        $this->actingAs($admin)->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('--input-bg: #fefefe', false);
    }
}
