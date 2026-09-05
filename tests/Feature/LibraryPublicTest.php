<?php

namespace Tests\Feature;

use App\Models\LibraryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryPublicTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_library_catalog(): void
    {
        $book = LibraryItem::create([
            'title' => 'Deep Learning Revolution',
            'slug' => 'deep-learning-revolution',
            'type' => 'book',
            'category' => 'Fundamental AI',
            'summary_preview' => 'Sejarah dan fondasi kebangkitan AI modern.',
            'content' => 'Isi lengkap bab 1 sampai penutup...',
            'author_name' => 'Terrence Sejnowski',
            'reading_time' => '10 mnt baca',
            'status' => 'approved',
        ]);

        $response = $this->get('/library');

        $response->assertStatus(200);
        $response->assertSee('Pustaka AI');
        $response->assertSee('Deep Learning Revolution');
        $response->assertSee('Fundamental AI');
        $response->assertSee('10 mnt baca');
    }

    public function test_guest_can_filter_by_type(): void
    {
        LibraryItem::create([
            'title' => 'Buku AI Mastery',
            'slug' => 'buku-ai-mastery',
            'type' => 'book',
            'summary_preview' => 'Sinopsis buku...',
            'content' => 'Konten buku...',
            'status' => 'approved',
        ]);

        LibraryItem::create([
            'title' => 'Podcast AI Breakdown',
            'slug' => 'podcast-ai-breakdown',
            'type' => 'podcast',
            'summary_preview' => 'Sinopsis podcast...',
            'content' => 'Konten podcast...',
            'status' => 'approved',
        ]);

        // Filter Buku
        $responseBook = $this->get('/library?type=book');
        $responseBook->assertStatus(200);
        $responseBook->assertSee('Buku AI Mastery');
        $responseBook->assertDontSee('Podcast AI Breakdown');

        // Filter Podcast
        $responsePodcast = $this->get('/library?type=podcast');
        $responsePodcast->assertStatus(200);
        $responsePodcast->assertSee('Podcast AI Breakdown');
        $responsePodcast->assertDontSee('Buku AI Mastery');
    }

    public function test_guest_sees_teaser_and_login_cta_on_detail_page(): void
    {
        $book = LibraryItem::create([
            'title' => 'Prompt Engineering Secrets',
            'slug' => 'prompt-engineering-secrets',
            'type' => 'book',
            'category' => 'Prompting',
            'summary_preview' => 'Ini adalah cuplikan sinopsis yang dapat dibaca tamu.',
            'content' => 'KONTEN RAHASIA KHUSUS MEMBER YANG TIDAK BOLEH DIBACA TAMU LENGKAP.',
            'status' => 'approved',
        ]);

        $response = $this->get("/library/{$book->slug}");

        $response->assertStatus(200);
        $response->assertSee('Ini adalah cuplikan sinopsis yang dapat dibaca tamu.');
        $response->assertSee('Konten Eksklusif Member');
        $response->assertSee('Masuk');
        $response->assertDontSee('KONTEN RAHASIA KHUSUS MEMBER YANG TIDAK BOLEH DIBACA TAMU LENGKAP.');
    }

    public function test_authenticated_member_can_read_full_content(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        $podcast = LibraryItem::create([
            'title' => 'Podcast Masa Depan AGI',
            'slug' => 'podcast-masa-depan-agi',
            'type' => 'podcast',
            'summary_preview' => 'Sinopsis episode...',
            'content' => 'KONTEN RESUME LENGKAP UNTUK MEMBER ALUMNI.',
            'podcast_source' => 'Dwarkesh Podcast',
            'media_embed_url' => 'https://www.youtube.com/watch?v=sample123',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($member)->get("/library/{$podcast->slug}");

        $response->assertStatus(200);
        $response->assertSee('KONTEN RESUME LENGKAP UNTUK MEMBER ALUMNI.');
        $response->assertDontSee('Konten Eksklusif Member');
    }

    public function test_unapproved_library_item_returns_404_for_public(): void
    {
        $pendingBook = LibraryItem::create([
            'title' => 'Buku Belum Disetujui',
            'slug' => 'buku-belum-disetujui',
            'type' => 'book',
            'summary_preview' => 'Sinopsis...',
            'content' => 'Konten...',
            'status' => 'pending',
        ]);

        $response = $this->get("/library/{$pendingBook->slug}");
        $response->assertStatus(404);
    }
}
