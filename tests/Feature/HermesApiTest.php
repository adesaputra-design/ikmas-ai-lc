<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\LearningMaterial;
use App\Models\Prompt;
use App\Models\Showcase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HermesApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'hermes.shared_secret' => 'test-hermes-secret',
            'hermes.allowed_actors' => ['telegram:12345'],
        ]);
    }

    public function test_hermes_endpoint_rejects_missing_secret(): void
    {
        $response = $this->getJson('/api/hermes/status');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized Hermes request.']);
    }

    public function test_hermes_endpoint_rejects_unlisted_actor(): void
    {
        $response = $this->withHeader('X-Hermes-Secret', 'test-hermes-secret')
            ->withHeader('X-Hermes-Actor', 'telegram:999')
            ->getJson('/api/hermes/status');

        $response->assertStatus(403)
            ->assertJson(['message' => 'Hermes actor is not allowed.']);
    }

    public function test_status_returns_service_health_for_allowed_actor(): void
    {
        $response = $this->hermesGet('/api/hermes/status');

        $response->assertStatus(200)
            ->assertJsonPath('ok', true)
            ->assertJsonPath('service', 'ikmas-ai-lc')
            ->assertJsonStructure(['environment', 'database', 'cache', 'checked_at']);
    }

    public function test_stats_returns_read_only_admin_counts(): void
    {
        User::factory()->create(['role' => 'admin']);
        User::factory()->count(2)->create(['role' => 'member']);
        LearningMaterial::create([
            'title' => 'Materi Published',
            'slug' => 'materi-published',
            'content' => 'Isi materi',
            'is_published' => true,
        ]);
        LearningMaterial::create([
            'title' => 'Materi Draft',
            'slug' => 'materi-draft',
            'content' => 'Isi materi draft',
            'is_published' => false,
        ]);
        Prompt::create([
            'title' => 'Prompt Ringkas',
            'slug' => 'prompt-ringkas',
            'target_role' => 'Umum',
            'target_tool' => 'ChatGPT',
            'prompt_text' => 'Ringkas teks ini.',
        ]);
        Showcase::create([
            'user_id' => User::factory()->create(['role' => 'member'])->id,
            'title' => 'Showcase Pending',
            'slug' => 'showcase-pending',
            'description' => 'Deskripsi',
            'tools_used' => 'Claude',
            'status' => 'pending',
        ]);
        Event::create([
            'title' => 'Agenda AI',
            'slug' => 'agenda-ai',
            'description' => 'Diskusi AI',
            'event_date' => now()->addDay(),
            'duration_minutes' => 60,
            'speaker_name' => 'Tim IKMAS',
            'status' => 'upcoming',
        ]);

        $response = $this->hermesGet('/api/hermes/stats');

        $response->assertStatus(200)
            ->assertJsonPath('admins', 1)
            ->assertJsonPath('members', 3)
            ->assertJsonPath('pending_members', 0)
            ->assertJsonPath('materials.total', 2)
            ->assertJsonPath('materials.published', 1)
            ->assertJsonPath('materials.draft', 1)
            ->assertJsonPath('prompts', 1)
            ->assertJsonPath('showcases.pending', 1)
            ->assertJsonPath('events.upcoming', 1);
    }

    public function test_latest_returns_recent_materials_showcases_and_events(): void
    {
        $member = User::factory()->create(['name' => 'Ahmad Alumni', 'role' => 'member']);
        LearningMaterial::create([
            'title' => 'Materi Terbaru',
            'slug' => 'materi-terbaru',
            'content' => 'Isi materi',
            'is_published' => true,
        ]);
        Showcase::create([
            'user_id' => $member->id,
            'title' => 'Karya AI',
            'slug' => 'karya-ai',
            'description' => 'Deskripsi karya',
            'tools_used' => 'ChatGPT',
            'status' => 'approved',
        ]);
        Event::create([
            'title' => 'Sharing AI',
            'slug' => 'sharing-ai',
            'description' => 'Belajar AI bersama.',
            'event_date' => now()->addDay(),
            'duration_minutes' => 60,
            'speaker_name' => 'Fasilitator',
            'status' => 'upcoming',
        ]);

        $response = $this->hermesGet('/api/hermes/latest');

        $response->assertStatus(200)
            ->assertJsonPath('materials.0.slug', 'materi-terbaru')
            ->assertJsonPath('showcases.0.member', 'Ahmad Alumni')
            ->assertJsonPath('events.0.slug', 'sharing-ai');
    }

    public function test_pending_members_returns_recent_members_with_schema_note(): void
    {
        User::factory()->create([
            'name' => 'Member Baru',
            'role' => 'member',
            'whatsapp_number' => '628123456789',
            'alumni_year' => '2010',
        ]);

        $response = $this->hermesGet('/api/hermes/members/pending');

        $response->assertStatus(200)
            ->assertJsonPath('pending_members', [])
            ->assertJsonPath('recent_members.0.name', 'Member Baru')
            ->assertJsonStructure(['note']);
    }

    public function test_event_preview_returns_broadcast_text_by_slug(): void
    {
        Event::create([
            'title' => 'Bedah AI untuk UMKM',
            'slug' => 'bedah-ai-untuk-umkm',
            'description' => 'Sesi praktik menggunakan AI untuk usaha alumni.',
            'event_date' => '2026-09-20 20:00:00',
            'duration_minutes' => 90,
            'location_url' => 'https://zoom.us/j/ikmas',
            'speaker_name' => 'Kak Alumni',
            'speaker_title' => 'Praktisi UMKM',
            'status' => 'upcoming',
        ]);

        $response = $this->hermesGet('/api/hermes/events/bedah-ai-untuk-umkm/preview');

        $response->assertStatus(200)
            ->assertJsonPath('event.slug', 'bedah-ai-untuk-umkm');

        $this->assertStringContainsString('IKMAS AI LEARNING CENTER', $response->json('broadcast_text'));
        $this->assertStringContainsString('Bedah AI untuk UMKM', $response->json('broadcast_text'));
        $this->assertStringContainsString('https://zoom.us/j/ikmas', $response->json('broadcast_text'));
    }

    private function hermesGet(string $uri)
    {
        return $this->withHeader('X-Hermes-Secret', 'test-hermes-secret')
            ->withHeader('X-Hermes-Actor', 'telegram:12345')
            ->getJson($uri);
    }
}
