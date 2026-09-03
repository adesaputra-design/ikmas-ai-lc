<?php

namespace Tests\Feature;

use App\Models\Showcase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($member)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@ikmas.ai',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@ikmas.ai',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_can_access_dashboard_and_see_metrics(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Panel Pengurus');
        $response->assertSee('Total Member');
        $response->assertSee('Menunggu Kurasi');
    }

    public function test_admin_can_approve_pending_showcase(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $showcase = Showcase::create([
            'user_id' => $user->id,
            'title' => 'Karya Baru Siap Kurasi',
            'slug' => 'karya-baru-siap-kurasi',
            'description' => 'Deskripsi karya',
            'tools_used' => 'Claude',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/admin/curation/{$showcase->id}/approve");

        $response->assertRedirect('/admin/dashboard');

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
            'title' => 'Karya Kurang Lengkap',
            'slug' => 'karya-kurang-lengkap',
            'description' => 'Deskripsi karya',
            'tools_used' => 'ChatGPT',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/admin/curation/{$showcase->id}/reject", [
            'admin_notes' => 'Tolong tambahkan screenshot bukti hasil penerapan karya.',
        ]);

        $response->assertRedirect('/admin/dashboard');

        $this->assertDatabaseHas('showcases', [
            'id' => $showcase->id,
            'status' => 'rejected',
            'admin_notes' => 'Tolong tambahkan screenshot bukti hasil penerapan karya.',
        ]);
    }
}
