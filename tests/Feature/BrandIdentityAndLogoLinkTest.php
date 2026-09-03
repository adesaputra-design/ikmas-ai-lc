<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandIdentityAndLogoLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_ikmas_logo_and_link_to_m_ikmas_com(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('images/ikmas-logo.png');
        $response->assertSee('https://m.ikmas.com/');
        $response->assertSee('m.ikmas.com');
    }

    public function test_navbar_displays_ikmas_portal_link(): void
    {
        $response = $this->get('/materi');

        $response->assertStatus(200);
        $response->assertSee('images/ikmas-logo.png');
        $response->assertSee('https://m.ikmas.com/');
        $response->assertSee('Portal IKMAS');
    }

    public function test_admin_sidebar_displays_ikmas_logo(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('images/ikmas-logo.png');
    }
}
