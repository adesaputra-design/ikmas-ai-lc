<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavbarAestheticAndResponsiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_desktop_navbar_renders_clean_brand_and_grouped_dropdown_menus(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        
        // Brand elements
        $response->assertSee('brand-logo-img');
        $response->assertSee('brand-divider');
        $response->assertSee('AI Learning Center', false);
        $response->assertSee('Ekosistem Alumni Assalaam', false);

        // Grouped dropdown menu items
        $response->assertSee('desktop-nav desktop-only', false);
        $response->assertSee('nav-dropdown');
        $response->assertSee('>Belajar<', false);
        $response->assertSee('>Komunitas<', false);
        $response->assertSee('Materi Belajar');
        $response->assertSee('Prompt Library');
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
        
        // Mobile elements inside offcanvas drawer
        $response->assertSee('ikmas-offcanvas');
        $response->assertSee('offcanvas-footer');
        $response->assertSee('offcanvas-portal-btn');
        $response->assertSee('Portal Pusat m.ikmas.com');
        $response->assertSee('offcanvas-auth');
    }

    public function test_landing_page_no_longer_renders_extra_hero_logo(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('hero-logo-emblem');
        $response->assertDontSee('hero-logo-frame');
        $response->assertDontSee('hero-logo-img');
        $response->assertDontSee('images/ikmas-ai-logo.jpg');
    }

    public function test_css_contains_tablet_breakpoint_and_hidden_offcanvas_defaults(): void
    {
        $css = file_get_contents(public_path('css/app.css'));

        $this->assertStringContainsString('@media (max-width: 1024px)', $css);
        $this->assertStringContainsString('.ikmas-offcanvas {', $css);
        $this->assertStringContainsString('transform: translateX(100%);', $css);
    }

    public function test_authenticated_user_sees_dashboard_in_navbar_and_mobile_drawer(): void
    {
        $user = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Area Member');
    }
}
