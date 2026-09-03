<?php

namespace Tests\Feature;

use App\Models\Showcase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminShowcaseCurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_curation_tabs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['name' => 'Fulan Alumni', 'alumni_year' => '2016']);

        Showcase::create([
            'user_id' => $user->id,
            'title' => 'Aplikasi Resep Masakan AI',
            'slug' => 'aplikasi-resep-masakan-ai',
            'description' => 'Aplikasi pencari resep berbasis bahan di kulkas.',
            'tools_used' => 'Claude API',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get('/admin/curation');

        $response->assertStatus(200);
        $response->assertSee('Kurasi Showcase Karya');
        $response->assertSee('Aplikasi Resep Masakan AI');
        $response->assertSee('Fulan Alumni');
    }

    public function test_admin_can_approve_showcase(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $showcase = Showcase::create([
            'user_id' => $user->id,
            'title' => 'Bot WhatsApp E-Commerce',
            'slug' => 'bot-whatsapp-e-commerce',
            'description' => 'Automasi penjualan.',
            'tools_used' => 'ChatGPT',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/admin/curation/{$showcase->id}/approve");

        $response->assertRedirect('/admin/curation');

        $this->assertDatabaseHas('showcases', [
            'id' => $showcase->id,
            'status' => 'approved',
        ]);
    }

    public function test_admin_can_reject_showcase_with_notes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $showcase = Showcase::create([
            'user_id' => $user->id,
            'title' => 'Karya Draf Belum Rapi',
            'slug' => 'karya-draf-belum-rapi',
            'description' => 'Deskripsi singkat.',
            'tools_used' => 'ChatGPT',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/admin/curation/{$showcase->id}/reject", [
            'admin_notes' => 'Harap lampirkan screenshot tampilan hasil karya.',
        ]);

        $response->assertRedirect('/admin/curation');

        $this->assertDatabaseHas('showcases', [
            'id' => $showcase->id,
            'status' => 'rejected',
            'admin_notes' => 'Harap lampirkan screenshot tampilan hasil karya.',
        ]);
    }

    public function test_admin_can_toggle_featured_showcase(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $showcase = Showcase::create([
            'user_id' => $user->id,
            'title' => 'Karya Paling Keren Alumni',
            'slug' => 'karya-paling-keren-alumni',
            'description' => 'Solusi luar biasa.',
            'tools_used' => 'Claude, Python',
            'status' => 'approved',
            'is_featured' => false,
        ]);

        $response = $this->actingAs($admin)->post("/admin/curation/{$showcase->id}/toggle-featured");

        $response->assertRedirect('/admin/curation');

        $this->assertDatabaseHas('showcases', [
            'id' => $showcase->id,
            'is_featured' => true,
        ]);
    }
}
