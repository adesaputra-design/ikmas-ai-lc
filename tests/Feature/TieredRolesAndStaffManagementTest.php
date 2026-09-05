<?php

namespace Tests\Feature;

use App\Models\Showcase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TieredRolesAndStaffManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_team_management_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/team');

        $response->assertStatus(200);
        $response->assertSee('Kelola Tim');
        $response->assertSee('Administrator');
        $response->assertSee('Staf Pengurus');
    }

    public function test_staff_and_member_cannot_access_team_management_page(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'permissions' => ['materials', 'events'],
        ]);
        $member = User::factory()->create(['role' => 'member']);

        // Staf dilarang mengakses kelola tim
        $responseStaff = $this->actingAs($staff)->get('/admin/team');
        $responseStaff->assertStatus(403);

        // Member dilarang mengakses kelola tim
        $responseMember = $this->actingAs($member)->get('/admin/team');
        $responseMember->assertStatus(403);
    }

    public function test_admin_can_promote_member_to_staff_with_modular_permissions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($admin)->post("/admin/team/{$user->id}/role", [
            'role' => 'staff',
            'permissions' => ['events', 'curation'],
        ]);

        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertEquals('staff', $user->role);
        $this->assertTrue($user->isStaff());
        $this->assertTrue($user->hasPermission('events'));
        $this->assertTrue($user->hasPermission('curation'));
        $this->assertFalse($user->hasPermission('materials'));
    }

    public function test_get_role_url_redirects_to_team_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($admin)->get("/admin/team/{$user->id}/role");
        $response->assertRedirect(route('admin.team.index'));
    }

    public function test_staff_can_only_access_assigned_modules(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'permissions' => ['events'],
        ]);

        // Modul Agenda diizinkan
        $responseAllowed = $this->actingAs($staff)->get('/admin/agenda');
        $responseAllowed->assertStatus(200);

        // Modul Materi diblokir (403)
        $responseBlocked = $this->actingAs($staff)->get('/admin/materi');
        $responseBlocked->assertStatus(403);
    }

    public function test_admin_has_unrestricted_access_to_all_modules(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/materi')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/agenda')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/curation')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/alumni')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/team')->assertStatus(200);
    }

    public function test_anti_lockout_prevents_sole_admin_from_demoting_self(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post("/admin/team/{$admin->id}/role", [
            'role' => 'member',
        ]);

        $response->assertSessionHas('error');
        $admin->refresh();
        $this->assertEquals('admin', $admin->role);
    }

    public function test_admin_can_soft_delete_member_and_showcases_are_hidden(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'member']);

        $showcase = Showcase::create([
            'user_id' => $member->id,
            'title' => 'Project AI Member',
            'slug' => 'project-ai-member',
            'description' => 'Contoh aplikasi AI',
            'tools_used' => 'Claude, Python',
            'status' => 'approved',
        ]);

        // Sebelum dihapus, karya tampil di showcase publik
        $this->get('/showcase')->assertSee('Project AI Member');

        // Admin menonaktifkan akun member
        $response = $this->actingAs($admin)->delete("/admin/team/{$member->id}");
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('users', ['id' => $member->id]);

        // Setelah dinonaktifkan, karya showcase otomatis tersembunyi dari publik
        $this->get('/showcase')->assertDontSee('Project AI Member');
    }

    public function test_soft_deleted_member_cannot_login(): void
    {
        $member = User::factory()->create([
            'email' => 'terhapus@alumni.test',
            'password' => bcrypt('password123'),
            'role' => 'member',
        ]);

        $member->delete(); // Soft delete

        $response = $this->post('/login', [
            'email' => 'terhapus@alumni.test',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_restore_soft_deleted_member(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'member']);

        $showcase = Showcase::create([
            'user_id' => $member->id,
            'title' => 'Karya Dipulihkan',
            'slug' => 'karya-dipulihkan',
            'description' => 'Contoh aplikasi AI dipulihkan',
            'tools_used' => 'ChatGPT',
            'status' => 'approved',
        ]);

        $member->delete();
        $this->assertTrue($member->fresh()->trashed());

        // Admin memulihkan akun
        $response = $this->actingAs($admin)->post("/admin/team/{$member->id}/restore");
        $response->assertSessionHas('success');

        $this->assertFalse($member->fresh()->trashed());

        // Showcase kembali muncul
        $this->get('/showcase')->assertSee('Karya Dipulihkan');
    }

    public function test_anti_lockout_prevents_admin_from_deleting_self(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->delete("/admin/team/{$admin->id}");

        $response->assertSessionHas('error');
        $this->assertFalse($admin->fresh()->trashed());
    }

    public function test_staff_cannot_delete_member(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'permissions' => ['alumni'],
        ]);
        $member = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($staff)->delete("/admin/alumni/{$member->id}");

        $response->assertStatus(403);
        $this->assertFalse($member->fresh()->trashed());
    }
}
