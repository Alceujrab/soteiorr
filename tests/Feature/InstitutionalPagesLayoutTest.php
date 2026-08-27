<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstitutionalPagesLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_institutional_pages_use_shared_professional_layout(): void
    {
        $pages = [
            'pages.about' => 'Sobre a RR Veículos',
            'pages.contact' => 'Fale conosco',
            'pages.faqs' => 'Dúvidas frequentes',
            'pages.privacy' => 'Política de Privacidade',
            'pages.terms' => 'Termos de Uso',
            'pages.regulation' => 'Regulamento da Promoção',
        ];

        foreach ($pages as $route => $heading) {
            $response = $this->get(route($route));

            $response->assertOk();
            $response->assertSee($heading, false);
            $response->assertSee('institutional-page', false);
            $response->assertSee('inst-nav-link', false);
            $response->assertSee('Sobre Nós', false);
            $response->assertSee('Regulamento', false);
        }
    }

    public function test_contact_page_shows_structured_channels(): void
    {
        Setting::set('contact_whatsapp', '(66) 98111-2233');
        Setting::set('contact_email', 'contato@rrsorteio.com');
        Setting::set('contact_address', 'Centro, Água Boa - MT');
        Setting::set('page_contact', '');

        $response = $this->get(route('pages.contact'));

        $response->assertOk();
        $response->assertSee('Falar no WhatsApp', false);
        $response->assertSee('contato@rrsorteio.com', false);
        $response->assertSee('wa.me/5566981112233', false);
        $response->assertSee('Dúvidas frequentes', false);
        $response->assertDontSee('99999-9999', false);
    }
}
