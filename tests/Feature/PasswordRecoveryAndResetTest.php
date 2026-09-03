<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordRecoveryAndResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_has_whatsapp_forgot_password_link(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Lupa kata sandi?');
        $response->assertSee('wa.me/6285713257939');
    }

    public function test_admin_can_reset_member_password_from_alumni(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create([
            'role' => 'member',
            'password' => Hash::make('oldpassword123'),
        ]);

        $response = $this->actingAs($admin)->post("/admin/alumni/{$member->id}/reset-password", [
            'password' => 'newSecretPass123',
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('newSecretPass123', $member->fresh()->password));
    }

    public function test_admin_can_reset_password_from_team_panel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $targetUser = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($admin)->post("/admin/team/{$targetUser->id}/reset-password", [
            'password' => 'teamSecretPass456',
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('teamSecretPass456', $targetUser->fresh()->password));
    }

    public function test_staff_with_alumni_permission_can_reset_member_password(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'permissions' => ['alumni'],
        ]);
        $member = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($staff)->post("/admin/alumni/{$member->id}/reset-password", [
            'password' => 'staffResetPass789',
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('staffResetPass789', $member->fresh()->password));
    }

    public function test_staff_cannot_reset_admin_or_fellow_staff_password(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'permissions' => ['alumni'],
        ]);
        $admin = User::factory()->create(['role' => 'admin']);
        $fellowStaff = User::factory()->create(['role' => 'staff']);

        // Staf dilarang me-reset akun Admin
        $responseAdmin = $this->actingAs($staff)->post("/admin/alumni/{$admin->id}/reset-password", [
            'password' => 'hackAdminPass123',
        ]);
        $responseAdmin->assertStatus(403);

        // Staf dilarang me-reset akun sesama Staf
        $responseFellow = $this->actingAs($staff)->post("/admin/alumni/{$fellowStaff->id}/reset-password", [
            'password' => 'hackStaffPass123',
        ]);
        $responseFellow->assertStatus(403);
    }

    public function test_member_can_update_own_password_with_valid_current_password(): void
    {
        $member = User::factory()->create([
            'role' => 'member',
            'password' => Hash::make('initialPass123'),
        ]);

        $response = $this->actingAs($member)->post('/member/password', [
            'current_password' => 'initialPass123',
            'password' => 'myNewPrivatePass2026',
            'password_confirmation' => 'myNewPrivatePass2026',
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('myNewPrivatePass2026', $member->fresh()->password));
    }

    public function test_member_cannot_update_password_with_incorrect_current_password(): void
    {
        $member = User::factory()->create([
            'role' => 'member',
            'password' => Hash::make('realPassword123'),
        ]);

        $response = $this->actingAs($member)->post('/member/password', [
            'current_password' => 'wrongPassword999',
            'password' => 'newSecretPass2026',
            'password_confirmation' => 'newSecretPass2026',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('realPassword123', $member->fresh()->password));
    }
}
