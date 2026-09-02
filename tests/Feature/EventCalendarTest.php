<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventCalendarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Event::create([
            'title' => 'AI Study Group Sesi #1: AI untuk Produktivitas',
            'slug' => 'ai-study-group-sesi-1-ai-untuk-produktivitas',
            'description' => 'Sesi belajar bersama perdana mengenal dan mempraktikkan AI untuk efisiensi pekerjaan harian.',
            'event_date' => now()->addDays(5),
            'duration_minutes' => 90,
            'location_url' => 'https://zoom.us/j/123456789',
            'speaker_name' => 'Fasilitator IKMAS AI',
            'speaker_title' => 'Praktisi AI & Alumni Assalaam',
            'status' => 'upcoming',
        ]);

        Event::create([
            'title' => 'Sharing Session: Pengantar Komunitas IKMAS AI',
            'slug' => 'sharing-session-pengantar-komunitas-ikmas-ai',
            'description' => 'Pertemuan daring menyatukan alumni penggiat teknologi dan kecerdasan buatan.',
            'event_date' => now()->subDays(10),
            'duration_minutes' => 60,
            'location_url' => 'https://zoom.us/j/987654321',
            'speaker_name' => 'Garuda Team',
            'speaker_title' => 'Pengurus IKMAS AI',
            'status' => 'completed',
            'recording_url' => 'https://youtube.com/watch?v=sample',
        ]);
    }

    public function test_event_calendar_page_can_be_viewed(): void
    {
        $response = $this->get('/agenda');

        $response->assertStatus(200);
        $response->assertSee('Kalender Agenda & Study Group');
        $response->assertSee('AI Study Group Sesi #1: AI untuk Produktivitas');
        $response->assertSee('Upcoming');
    }

    public function test_upcoming_and_past_events_are_categorized_properly(): void
    {
        $response = $this->get('/agenda');

        $response->assertStatus(200);
        $response->assertSee('AI Study Group Sesi #1: AI untuk Produktivitas');
        $response->assertSee('Sharing Session: Pengantar Komunitas IKMAS AI');
        $response->assertSee('Selesai');
    }

    public function test_event_detail_page_can_be_rendered(): void
    {
        $response = $this->get('/agenda/ai-study-group-sesi-1-ai-untuk-produktivitas');

        $response->assertStatus(200);
        $response->assertSee('AI Study Group Sesi #1: AI untuk Produktivitas');
        $response->assertSee('Fasilitator IKMAS AI');
        $response->assertSee('https://zoom.us/j/123456789');
    }
}
