<?php

namespace Tests\Feature;

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
}
