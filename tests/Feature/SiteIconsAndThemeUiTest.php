<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteIconsAndThemeUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_includes_site_icons_and_soft_hero_mask(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('icons/favicon.ico', false);
        $response->assertSee('icons/apple-touch-icon.png', false);
        $response->assertSee('icons/site.webmanifest', false);
        $response->assertSee('hero-photo-mask', false);
        $response->assertDontSee('from-[#0c0e12]', false);
        $response->assertSee('nav-link-quiet', false);
        $response->assertSee('theme-title', false);
    }

    public function test_icon_assets_are_publicly_available(): void
    {
        $this->assertFileExists(public_path('icons/favicon.ico'));
        $this->assertFileExists(public_path('icons/android-chrome-192x192.png'));
        $this->assertFileExists(public_path('icons/android-chrome-512x512.png'));
        $this->assertFileExists(public_path('icons/apple-touch-icon.png'));
        $this->assertFileExists(public_path('icons/site.webmanifest'));
    }
}
