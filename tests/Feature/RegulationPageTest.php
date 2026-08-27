<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Support\DefaultRegulationContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegulationPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_regulation_page_is_publicly_accessible(): void
    {
        $response = $this->get(route('pages.regulation'));

        $response->assertOk();
        $response->assertSee('Regulamento da Promoção', false);
        $response->assertSee('Um clássico. Uma chance. Uma história para continuar.', false);
        $response->assertSee('RR VEÍCULOS LTDA', false);
        $response->assertSee('Volkswagen Santana', false);
    }

    public function test_admin_can_update_regulation_content_from_settings(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin_organizador',
        ]);

        $payload = [
            'app_name' => 'Ação RR Veículos',
            'admin_security_email' => 'contato@rrsorteio.com',
            'min_tickets' => 1,
            'max_tickets' => 100,
            'page_regulation' => '<h1>Regulamento Atualizado</h1><p>Conteúdo revisado pela administração.</p>',
            'page_about_us' => '',
            'page_contact' => '',
            'page_faqs' => '',
            'page_privacy_policy' => '',
            'page_terms_of_use' => '',
        ];

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), $payload);

        $response->assertRedirect(route('admin.settings'));
        $this->assertSame($payload['page_regulation'], Setting::get('page_regulation'));

        $this->get(route('pages.regulation'))
            ->assertOk()
            ->assertSee('Regulamento Atualizado', false)
            ->assertSee('Conteúdo revisado pela administração.', false);
    }

    public function test_default_regulation_content_contains_core_clauses(): void
    {
        $html = DefaultRegulationContent::html();

        $this->assertStringContainsString('Empresa Promotora', $html);
        $this->assertStringContainsString('45.946.061/0001-84', $html);
        $this->assertStringContainsString('140.000', $html);
        $this->assertStringContainsString('Projetos Sociais', $html);
        $this->assertStringContainsString('ao vivo', $html);
        $this->assertStringContainsString('YouTube', $html);
        $this->assertStringNotContainsString('Loteria Federal', $html);
    }

    public function test_public_pages_do_not_mention_loteria_federal(): void
    {
        $this->get(route('pages.regulation'))->assertOk()->assertDontSee('Loteria Federal', false);
        $this->get(route('pages.faqs'))->assertOk()->assertDontSee('Loteria Federal', false);
    }
}
