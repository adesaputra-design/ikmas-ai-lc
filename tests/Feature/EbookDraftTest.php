<?php

namespace Tests\Feature;

use App\Models\LibraryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EbookDraftTest extends TestCase
{
    use RefreshDatabase;

    private function createDraftEbook(): LibraryItem
    {
        return LibraryItem::create([
            'title' => 'Seri JARIB Draf',
            'slug' => 'seri-jarib-draf',
            'type' => 'ebook',
            'category' => 'Seri JARIB',
            'summary_preview' => 'Draf ebook.',
            'series_name' => 'Seri JARIB',
            'edition_number' => 1,
            'status' => 'pending',
        ]);
    }

    private function chapterPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Bab Pertama',
            'summary_points' => ['Poin penting pertama'],
            'body' => 'Isi **bab pertama** untuk pratinjau.',
            'research_box' => 'Catatan riset.',
            'action_title' => 'Coba pekan ini',
            'action_checklist' => ['Latihan pertama'],
        ], $overrides);
    }

    public function test_admin_creates_draft_chapter_and_previews_it_privately(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post('/admin/library', [
            'type' => 'ebook',
            'title' => 'Seri JARIB Uji',
            'category' => 'Seri JARIB',
            'summary_preview' => 'Ebook untuk pratinjau.',
            'author_name' => 'Narasumber Uji',
            'series_name' => 'Seri JARIB',
            'edition_number' => 1,
        ])->assertRedirect();

        $ebook = LibraryItem::where('title', 'Seri JARIB Uji')->firstOrFail();
        $this->assertSame('pending', $ebook->status);
        $this->assertFalse($ebook->is_public);
        $this->assertSame('Seri JARIB', $ebook->series_name);
        $this->assertSame(1, $ebook->edition_number);

        $this->actingAs($admin)->post("/admin/library/{$ebook->id}/chapters", $this->chapterPayload())
            ->assertRedirect();

        $chapter = $ebook->chapters()->firstOrFail();
        $this->assertSame('bab-pertama', $chapter->slug);
        $this->assertSame(1, $chapter->position);
        $this->assertSame(1, $chapter->reading_minutes);
        $this->assertSame(['Latihan pertama'], $chapter->action_checklist);

        $this->actingAs($admin)
            ->get("/admin/library/{$ebook->id}/preview")
            ->assertOk()
            ->assertSee('Bab Pertama');

        $this->actingAs($admin)
            ->get("/admin/library/{$ebook->id}/preview/bab/{$chapter->slug}")
            ->assertOk()
            ->assertSee('Isi <strong>bab pertama</strong>', false);
    }

    public function test_guest_and_member_cannot_open_draft_ebook_or_its_chapter(): void
    {
        $ebook = $this->createDraftEbook();
        $chapter = $ebook->chapters()->create($this->chapterPayload());
        $member = User::factory()->create(['role' => 'member']);

        $this->get("/library/{$ebook->slug}")->assertNotFound();
        $this->get("/library/{$ebook->slug}/bab/{$chapter->slug}")->assertNotFound();

        $this->actingAs($member)->get("/library/{$ebook->slug}")->assertNotFound();
        $this->actingAs($member)->get("/library/{$ebook->slug}/bab/{$chapter->slug}")->assertNotFound();
        $this->actingAs($member)->get("/admin/library/{$ebook->id}/preview")->assertForbidden();
    }

    public function test_staff_needs_library_permission_to_manage_and_preview_chapters(): void
    {
        $ebook = $this->createDraftEbook();
        $chapter = $ebook->chapters()->create($this->chapterPayload());

        $staffWithout = User::factory()->create(['role' => 'staff', 'permissions' => ['events']]);
        $this->actingAs($staffWithout)->get("/admin/library/{$ebook->id}/preview")->assertForbidden();
        $this->actingAs($staffWithout)->get("/admin/library/{$ebook->id}/preview/bab/{$chapter->slug}")->assertForbidden();
        $this->actingAs($staffWithout)->post("/admin/library/{$ebook->id}/chapters", $this->chapterPayload(['title' => 'Bab Liar']))
            ->assertForbidden();
        $this->assertSame(1, $ebook->chapters()->count());

        $staffWith = User::factory()->create(['role' => 'staff', 'permissions' => ['library']]);
        $this->actingAs($staffWith)->get("/admin/library/{$ebook->id}/chapters")->assertOk()->assertSee('Bab Pertama');
        $this->actingAs($staffWith)->get("/admin/library/{$ebook->id}/preview/bab/{$chapter->slug}")->assertOk();
    }

    public function test_chapter_slug_is_unique_within_ebook_and_reading_time_is_totalled(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ebook = $this->createDraftEbook();
        $otherEbook = LibraryItem::create([
            'title' => 'Seri JARIB Edisi 2',
            'slug' => 'seri-jarib-edisi-2',
            'type' => 'ebook',
            'summary_preview' => 'Edisi kedua.',
            'status' => 'pending',
        ]);

        $longBody = str_repeat('kata ', 450);
        $this->actingAs($admin)->post("/admin/library/{$ebook->id}/chapters", $this->chapterPayload(['body' => $longBody]));
        $this->actingAs($admin)->post("/admin/library/{$ebook->id}/chapters", $this->chapterPayload());
        $this->actingAs($admin)->post("/admin/library/{$otherEbook->id}/chapters", $this->chapterPayload());

        $chapters = $ebook->chapters()->get();
        $this->assertSame(['bab-pertama', 'bab-pertama-2'], $chapters->pluck('slug')->all());
        $this->assertSame([1, 2], $chapters->pluck('position')->all());
        $this->assertSame(3, $chapters->first()->reading_minutes);
        $this->assertSame('4 menit', $ebook->fresh()->reading_time);
        $this->assertSame('bab-pertama', $otherEbook->chapters()->first()->slug);
    }

    public function test_chapter_validation_and_non_ebook_items_are_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ebook = $this->createDraftEbook();
        $book = LibraryItem::create([
            'title' => 'Buku Biasa',
            'slug' => 'buku-biasa',
            'type' => 'book',
            'summary_preview' => 'Buku.',
            'status' => 'approved',
        ]);

        $this->actingAs($admin)->post("/admin/library/{$ebook->id}/chapters", ['title' => ''])
            ->assertSessionHasErrors(['title', 'body']);

        $this->actingAs($admin)->post("/admin/library/{$book->id}/chapters", $this->chapterPayload())
            ->assertNotFound();
        $this->actingAs($admin)->get("/admin/library/{$book->id}/preview")->assertNotFound();
    }

    public function test_preview_strips_raw_html_and_unsafe_links_from_markdown(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ebook = $this->createDraftEbook();
        $chapter = $ebook->chapters()->create($this->chapterPayload([
            'body' => "Paragraf aman.\n\n<script>alert('x')</script>\n\n[klik](javascript:alert(1))",
        ]));

        $this->actingAs($admin)
            ->get("/admin/library/{$ebook->id}/preview/bab/{$chapter->slug}")
            ->assertOk()
            ->assertSee('Paragraf aman.')
            ->assertDontSee("<script>alert('x')</script>", false)
            ->assertDontSee('javascript:alert', false);
    }

    public function test_existing_book_items_still_work_after_ebook_schema_change(): void
    {
        $book = LibraryItem::create([
            'title' => 'Buku Lama',
            'slug' => 'buku-lama',
            'type' => 'book',
            'summary_preview' => 'Sinopsis.',
            'status' => 'approved',
        ]);

        $this->assertFalse($book->fresh()->is_public);
        $this->get('/library/buku-lama')->assertOk()->assertSee('Buku Lama');
    }
}
