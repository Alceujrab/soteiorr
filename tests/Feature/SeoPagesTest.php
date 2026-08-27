<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_is_available(): void
    {
        $response = $this->get(route('seo.robots'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('Sitemap:');
        $response->assertSee('Disallow: /admin');
    }

    public function test_sitemap_xml_is_available(): void
    {
        $response = $this->get(route('seo.sitemap'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<urlset', false);
        $response->assertSee(url('/'), false);
        $response->assertSee(route('pages.regulation'), false);
    }

    public function test_home_page_includes_seo_meta_tags(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('name="description"', false);
        $response->assertSee('property="og:title"', false);
        $response->assertSee('application/ld+json', false);
    }
}
