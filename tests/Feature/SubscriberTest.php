<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriberTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_model_has_subscriber_methods(): void
    {
        $subscriber = User::factory()->create([
            'role' => 'subscriber',
            'status' => 'pending',
        ]);

        $this->assertTrue($subscriber->isSubscriber());
        $this->assertTrue($subscriber->isPending());
        $this->assertFalse($subscriber->isActive());
        $this->assertFalse($subscriber->isRejected());
        $this->assertFalse($subscriber->isMember());
    }

    public function test_existing_users_default_to_active_status(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        $this->assertEquals('active', $member->status);
        $this->assertTrue($member->isActive());
    }

    public function test_subscriber_role_badge_attribute(): void
    {
        $subscriber = User::factory()->create([
            'role' => 'subscriber',
            'status' => 'active',
        ]);

        $badge = $subscriber->role_badge;
        $this->assertEquals('Subscriber', $badge['label']);
        $this->assertEquals('badge-amber', $badge['class']);
    }

    // --- Task 2: Login Guard ---

    public function test_pending_subscriber_cannot_login(): void
    {
        User::factory()->create([
            'email'    => 'pending@test.com',
            'password' => bcrypt('password123'),
            'role'     => 'subscriber',
            'status'   => 'pending',
        ]);

        $response = $this->post('/login', [
            'email'    => 'pending@test.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        $errors = session('errors');
        $this->assertStringContainsString('peninjauan', $errors->first('email'));
    }

    public function test_rejected_subscriber_cannot_login(): void
    {
        User::factory()->create([
            'email'    => 'rejected@test.com',
            'password' => bcrypt('password123'),
            'role'     => 'subscriber',
            'status'   => 'rejected',
        ]);

        $response = $this->post('/login', [
            'email'    => 'rejected@test.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_active_subscriber_can_login(): void
    {
        $subscriber = User::factory()->create([
            'email'    => 'active@test.com',
            'password' => bcrypt('password123'),
            'role'     => 'subscriber',
            'status'   => 'active',
        ]);

        $response = $this->post('/login', [
            'email'    => 'active@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('member.dashboard'));
        $this->assertAuthenticatedAs($subscriber);
    }

    public function test_active_alumni_member_can_still_login(): void
    {
        $member = User::factory()->create([
            'email'    => 'alumni@test.com',
            'password' => bcrypt('password123'),
            'role'     => 'member',
            'status'   => 'active',
        ]);

        $response = $this->post('/login', [
            'email'    => 'alumni@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('member.dashboard'));
        $this->assertAuthenticatedAs($member);
    }

    // --- Task 3: Registration flows ---

    public function test_alumni_can_register_at_new_route(): void
    {
        $response = $this->post('/register/alumni', [
            'name'                  => 'Ahmad Rizki',
            'email'                 => 'rizki@alumni.test',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'whatsapp_number'       => '081234567890',
            'alumni_year'           => '2016',
        ]);

        $response->assertRedirect(route('member.dashboard'));
        $this->assertDatabaseHas('users', [
            'email'  => 'rizki@alumni.test',
            'role'   => 'member',
            'status' => 'active',
        ]);
    }

    public function test_subscriber_can_register(): void
    {
        $response = $this->post('/register/subscriber', [
            'name'                  => 'Budi Santoso',
            'email'                 => 'budi@publik.test',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'whatsapp_number'       => '082345678901',
        ]);

        $response->assertRedirect(route('register.subscriber.pending'));
        $this->assertDatabaseHas('users', [
            'email'  => 'budi@publik.test',
            'role'   => 'subscriber',
            'status' => 'pending',
        ]);
        $this->assertGuest();
    }

    public function test_register_old_route_redirects_to_alumni(): void
    {
        $response = $this->get('/register');

        $response->assertRedirect('/register/alumni');
    }

    public function test_subscriber_pending_page_is_accessible(): void
    {
        $response = $this->get('/register/subscriber/pending');

        $response->assertStatus(200);
        $response->assertSee('Pendaftaran');
    }

    // --- Task 4: Showcase Access Restriction ---

    public function test_active_subscriber_cannot_access_create_showcase_page(): void
    {
        $subscriber = User::factory()->create([
            'role'   => 'subscriber',
            'status' => 'active',
        ]);

        $response = $this->actingAs($subscriber)->get('/member/showcase/create');

        $response->assertStatus(403);
    }

    public function test_active_subscriber_cannot_submit_showcase(): void
    {
        $subscriber = User::factory()->create([
            'role'   => 'subscriber',
            'status' => 'active',
        ]);

        $response = $this->actingAs($subscriber)->post('/member/showcase', [
            'title'       => 'Karya Subscriber',
            'description' => 'Test showcase dari subscriber',
            'tools_used'  => 'ChatGPT',
        ]);

        $response->assertStatus(403);
    }

    public function test_active_alumni_member_can_access_create_showcase_page(): void
    {
        $member = User::factory()->create([
            'role'   => 'member',
            'status' => 'active',
        ]);

        $response = $this->actingAs($member)->get('/member/showcase/create');

        $response->assertStatus(200);
    }
}

