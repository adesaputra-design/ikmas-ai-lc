<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavbarAestheticAndResponsiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_desktop_navbar_renders_clean_brand_and_one_line_menus(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        
        // Brand elements
        $response->assertSee('brand-logo-img');
        $response->assertSee('brand-divider');
        $response->assertSee('AI Learning Center', false);
        $response->assertSee('Ekosistem Alumni Assalaam', false);

        // One-line menu items
        $response->assertSee('>Beranda<', false);
        $response->assertSee('>Materi<', false);
        $response->assertSee('>Prompts<', false);
        $response->assertSee('>Showcase<', false);
        $response->assertSee('>Agenda<', false);
        $response->assertSee('>Komunitas<', false);
    }

    public function test_desktop_navbar_has_portal_pill_and_auth_actions(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        
        // Portal button with external link
        $response->assertSee('btn-portal-pill desktop-only', false);
        $response->assertSee('https://m.ikmas.com/');
        
        // Guest auth links
        $response->assertSee('>Masuk<', false);
        $response->assertSee('>Daftar Alumni<', false);
    }

    public function test_mobile_drawer_contains_portal_card_and_actions(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        
        // Mobile elements inside drawer
        $response->assertSee('mobile-drawer-footer');
        $response->assertSee('mobile-portal-card');
        $response->assertSee('Portal Pusat IKMAS (m.ikmas.com)');
        $response->assertSee('mobile-auth-cluster');
    }

    public function test_authenticated_user_sees_dashboard_in_navbar_and_mobile_drawer(): void
    {
        $user = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Area Member');
    }
}
