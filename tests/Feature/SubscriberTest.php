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
}
