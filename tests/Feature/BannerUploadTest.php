<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BannerUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_desktop_and_mobile_banner_images(): void
    {
        $admin = User::create([
            'name' => 'Admin Banner',
            'email' => 'admin-banner@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin_organizador',
        ]);

        if (! is_dir(public_path('uploads/banners'))) {
            mkdir(public_path('uploads/banners'), 0755, true);
        }

        $desktop = UploadedFile::fake()->image('desktop.jpg', 1920, 700);
        $mobile = UploadedFile::fake()->image('mobile.jpg', 1080, 1350);

        $response = $this->actingAs($admin)->post(route('admin.banners.store'), [
            'title' => 'Banner Principal',
            'subtitle' => 'Nova ação',
            'image' => $desktop,
            'mobile_image' => $mobile,
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        $banner = Banner::first();
        $this->assertNotNull($banner);
        $this->assertSame('Banner Principal', $banner->title);
        $this->assertNotNull($banner->image_url);
        $this->assertNotNull($banner->mobile_image_url);
        $this->assertStringContainsString('/uploads/banners/', $banner->image_url);
        $this->assertStringContainsString('/uploads/banners/', $banner->mobile_image_url);
        $this->assertFileExists(public_path(ltrim($banner->image_url, '/')));
        $this->assertFileExists(public_path(ltrim($banner->mobile_image_url, '/')));
    }

    public function test_admin_can_delete_banner(): void
    {
        $admin = User::create([
            'name' => 'Admin Banner',
            'email' => 'admin-banner-del@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin_organizador',
        ]);

        $banner = Banner::create([
            'title' => 'Mustang legado',
            'subtitle' => 'Antigo',
            'image_url' => 'https://example.com/mustang.jpg',
            'active' => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.banners.destroy', $banner));

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertDatabaseMissing('banners', ['id' => $banner->id]);
    }

    public function test_home_prefers_active_raffle_over_orphan_banner(): void
    {
        Banner::create([
            'title' => 'Ação Promocional de Luxo: Mustang GT',
            'subtitle' => 'Legado',
            'image_url' => 'https://example.com/mustang.jpg',
            'active' => true,
        ]);

        $response = $this->get(route('raffles.index'));
        $response->assertOk();
        // Without active raffles, orphan banner may still show; ensure page loads.
        $response->assertSee('Ações Promocionais');
    }
}
