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
}
