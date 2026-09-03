<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEventAndBroadcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_events_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Event::create([
            'title' => 'Workshop AI Canva & Desain',
            'slug' => 'workshop-ai-canva-desain',
            'topic' => 'Desain Visual Berbasis AI',
            'description' => 'Mempelajari fitur Magic Studio Canva.',
            'event_date' => now()->addDays(3),
            'duration_minutes' => 90,
            'speaker_name' => 'Fulan Assalaam',
            'speaker_title' => 'Creative Designer',
            'status' => 'upcoming',
        ]);

        $response = $this->actingAs($admin)->get('/admin/agenda');

        $response->assertStatus(200);
        $response->assertSee('Agenda Kegiatan');
        $response->assertSee('Workshop AI Canva & Desain');
    }

    public function test_admin_can_create_event(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/agenda', [
            'title' => 'AI Study Group #2: Deep Dive Claude',
            'topic' => 'Eksplorasi Model Claude 3.5 Sonnet',
            'description' => 'Membahas pemanfaatan Claude Artifacts.',
            'event_date' => '2026-10-15 20:00:00',
            'duration_minutes' => 75,
            'location_url' => 'https://meet.google.com/sample-room',
            'speaker_name' => 'Dr. H. Alumni',
            'speaker_title' => 'AI Researcher',
            'status' => 'upcoming',
        ]);

        $response->assertRedirect('/admin/agenda');

        $this->assertDatabaseHas('events', [
            'title' => 'AI Study Group #2: Deep Dive Claude',
            'slug' => 'ai-study-group-2-deep-dive-claude',
            'status' => 'upcoming',
        ]);
    }

    public function test_admin_can_update_event_to_completed_with_recording(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $event = Event::create([
            'title' => 'Sharing Session Freelance AI',
            'slug' => 'sharing-session-freelance-ai',
            'topic' => 'Peluang Karir Global',
            'description' => 'Mencari klien luar negeri.',
            'event_date' => now()->subDay(),
            'duration_minutes' => 60,
            'speaker_name' => 'Rahmat Alumni',
            'speaker_title' => 'Freelance Prompt Engineer',
            'status' => 'upcoming',
        ]);

        $response = $this->actingAs($admin)->put("/admin/agenda/{$event->id}", [
            'title' => 'Sharing Session Freelance AI',
            'topic' => 'Peluang Karir Global',
            'description' => 'Mencari klien luar negeri.',
            'event_date' => now()->subDay()->format('Y-m-d H:i:s'),
            'duration_minutes' => 60,
            'speaker_name' => 'Rahmat Alumni',
            'speaker_title' => 'Freelance Prompt Engineer',
            'status' => 'completed',
            'recording_url' => 'https://youtube.com/watch?v=rekaman123',
            'materials_url' => 'https://drive.google.com/slide123',
        ]);

        $response->assertRedirect('/admin/agenda');

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'status' => 'completed',
            'recording_url' => 'https://youtube.com/watch?v=rekaman123',
        ]);
    }

    public function test_admin_can_generate_whatsapp_broadcast_text(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $event = Event::create([
            'title' => 'Bedah Paper LLM Terbaru',
            'slug' => 'bedah-paper-llm-terbaru',
            'topic' => 'Teknologi AI',
            'description' => 'Analisis paper terobosan AI.',
            'event_date' => '2026-11-20 19:30:00',
            'duration_minutes' => 60,
            'location_url' => 'https://zoom.us/j/12345678',
            'speaker_name' => 'Ir. Alumni',
            'speaker_title' => 'Tech Lead',
            'status' => 'upcoming',
        ]);

        $response = $this->actingAs($admin)->getJson("/admin/agenda/{$event->id}/broadcast-text");

        $response->assertStatus(200);
        $response->assertJsonStructure(['broadcast_text']);
        
        $json = $response->json();
        $this->assertStringContainsString('IKMAS AI LEARNING CENTER', $json['broadcast_text']);
        $this->assertStringContainsString('Bedah Paper LLM Terbaru', $json['broadcast_text']);
        $this->assertStringContainsString('Ir. Alumni', $json['broadcast_text']);
        $this->assertStringContainsString('https://zoom.us/j/12345678', $json['broadcast_text']);
    }
}
