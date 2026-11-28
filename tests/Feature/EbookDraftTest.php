<?php

namespace Tests\Feature;

use App\Models\LibraryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EbookDraftTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_creates_draft_chapter_and_previews_it_privately(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin/library/create')
            ->assertOk()
            ->assertSee('Ebook');

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

        $this->actingAs($admin)
            ->get('/admin/library?tab=ebook')
            ->assertOk()
            ->assertSee('Seri JARIB Uji');

        $this->actingAs($admin)
            ->get("/admin/library/{$ebook->id}/chapters/create")
            ->assertOk()
            ->assertSee('Simpan Bab');

        $this->actingAs($admin)->post("/admin/library/{$ebook->id}/chapters", [
            'title' => 'Bab Pertama',
            'summary_points' => ['Poin penting pertama'],
            'body' => 'Isi **bab pertama** untuk pratinjau.',
            'research_box' => 'Catatan riset.',
            'action_title' => 'Coba pekan ini',
            'action_checklist' => ['Latihan pertama'],
        ])->assertRedirect();

        $chapter = $ebook->chapters()->firstOrFail();
        $this->assertSame('bab-pertama', $chapter->slug);
        $this->assertSame(1, $chapter->reading_minutes);

        $this->actingAs($admin)
            ->get("/admin/library/{$ebook->id}/preview")
            ->assertOk()
            ->assertSee('Bab Pertama')
            ->assertSee('Total waktu baca: 1 menit');

        $this->actingAs($admin)
            ->get("/admin/library/{$ebook->id}/preview/bab/{$chapter->slug}")
            ->assertOk()
            ->assertSee('Isi <strong>bab pertama</strong>', false);

        auth()->logout();
        $this->get("/library/{$ebook->slug}")->assertNotFound();
        $this->get("/library/{$ebook->slug}/bab/{$chapter->slug}")->assertNotFound();

        $member = User::factory()->create(['role' => 'member']);
        $this->actingAs($member)
            ->get("/library/{$ebook->slug}")
            ->assertNotFound();
        $this->actingAs($member)
            ->get("/admin/library/{$ebook->id}/preview")
            ->assertForbidden();
    }

    public function test_ebook_cannot_be_opened_through_public_detail_before_public_reader_exists(): void
    {
        $ebook = LibraryItem::create([
            'title' => 'Ebook Privat',
            'slug' => 'ebook-privat',
            'type' => 'ebook',
            'summary_preview' => 'Isi privat.',
            'status' => 'approved',
            'is_public' => false,
        ]);

        $this->get("/library/{$ebook->slug}")->assertNotFound();
        $this->get('/library')->assertOk()->assertDontSee('Ebook Privat');
    }

    public function test_admin_can_leave_optional_chapter_checklist_empty(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ebook = LibraryItem::create([
            'title' => 'Draf Tanpa Checklist',
            'slug' => 'draf-tanpa-checklist',
            'type' => 'ebook',
            'summary_preview' => 'Draf uji.',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post("/admin/library/{$ebook->id}/chapters", [
            'title' => 'Bab Tanpa Checklist',
            'summary_points' => ['Intisari'],
            'body' => 'Isi bab.',
            'action_checklist' => [''],
        ])->assertRedirect(route('admin.library.ebook.preview', $ebook));

        $this->assertDatabaseHas('library_chapters', [
            'library_item_id' => $ebook->id,
            'slug' => 'bab-tanpa-checklist',
        ]);
    }

    public function test_ebook_draft_routes_require_library_staff_permission(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'permissions' => []]);
        $ebook = LibraryItem::create([
            'title' => 'Draf Staf',
            'slug' => 'draf-staf',
            'type' => 'ebook',
            'summary_preview' => 'Pratinjau staf.',
            'status' => 'pending',
        ]);
        $chapter = $ebook->chapters()->create([
            'position' => 1,
            'slug' => 'bab-staf',
            'title' => 'Bab Staf',
            'summary_points' => ['Poin'],
            'body' => 'Isi.',
        ]);

        $this->actingAs($staff)
            ->get("/admin/library/{$ebook->id}/preview")
            ->assertForbidden();

        $this->actingAs($staff)
            ->post("/admin/library/{$ebook->id}/chapters", [
                'title' => 'Bab Rahasia',
                'summary_points' => ['Poin'],
                'body' => 'Isi.',
            ])
            ->assertForbidden();

        $this->actingAs($staff)->get("/admin/library/{$ebook->id}/chapters/{$chapter->id}/edit")->assertForbidden();
        $this->actingAs($staff)->post("/admin/library/{$ebook->id}/chapters/{$chapter->id}/move-down")->assertForbidden();
        $this->actingAs($staff)->delete("/admin/library/{$ebook->id}/chapters/{$chapter->id}")->assertForbidden();

        $this->assertDatabaseMissing('library_chapters', ['title' => 'Bab Rahasia']);

        $staff->update(['permissions' => ['library']]);
        $this->actingAs($staff)
            ->get("/admin/library/{$ebook->id}/preview")
            ->assertOk();
    }

    public function test_chapter_preview_sanitizes_markdown(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ebook = LibraryItem::create([
            'title' => 'Draf Markdown',
            'slug' => 'draf-markdown',
            'type' => 'ebook',
            'summary_preview' => 'Pratinjau aman.',
            'status' => 'pending',
        ]);
        $chapter = $ebook->chapters()->create([
            'position' => 1,
            'slug' => 'bab-aman',
            'title' => 'Bab Aman',
            'summary_points' => ['Poin'],
            'body' => "Teks **tebal**.\n\n<script>alert('x')</script>\n\n[tautan](javascript:alert(1))",
        ]);

        $this->actingAs($admin)
            ->get("/admin/library/{$ebook->id}/preview/bab/{$chapter->slug}")
            ->assertOk()
            ->assertSee('<strong>tebal</strong>', false)
            ->assertDontSee("alert('x')", false)
            ->assertDontSee('href="javascript:', false);
    }

    public function test_chapters_with_same_title_get_distinct_slugs_and_positions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ebook = LibraryItem::create([
            'title' => 'Draf Bab Ganda',
            'slug' => 'draf-bab-ganda',
            'type' => 'ebook',
            'summary_preview' => 'Draf uji.',
            'status' => 'pending',
        ]);

        foreach ([1, 2] as $number) {
            $this->actingAs($admin)->post("/admin/library/{$ebook->id}/chapters", [
                'title' => 'Bab Serupa',
                'summary_points' => ["Intisari {$number}"],
                'body' => "Isi bab {$number}.",
            ])->assertRedirect(route('admin.library.ebook.preview', $ebook));
        }

        $this->assertSame(['bab-serupa', 'bab-serupa-2'], $ebook->chapters()->pluck('slug')->all());
        $this->assertSame([1, 2], $ebook->chapters()->pluck('position')->all());
        $this->assertSame('2 menit', $ebook->fresh()->reading_time);
    }

    public function test_admin_can_move_chapter_down_without_duplicate_positions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ebook = LibraryItem::create([
            'title' => 'Draf Urutan Bab',
            'slug' => 'draf-urutan-bab',
            'type' => 'ebook',
            'summary_preview' => 'Draf uji.',
            'status' => 'pending',
        ]);
        $first = $ebook->chapters()->create([
            'position' => 1, 'slug' => 'bab-satu', 'title' => 'Bab Satu',
            'summary_points' => ['Poin'], 'body' => 'Isi.',
        ]);
        $second = $ebook->chapters()->create([
            'position' => 2, 'slug' => 'bab-dua', 'title' => 'Bab Dua',
            'summary_points' => ['Poin'], 'body' => 'Isi.',
        ]);

        $this->actingAs($admin)
            ->post("/admin/library/{$ebook->id}/chapters/{$first->id}/move-down")
            ->assertRedirect(route('admin.library.ebook.preview', $ebook));

        $this->assertSame(['bab-dua', 'bab-satu'], $ebook->chapters()->pluck('slug')->all());
        $this->assertSame([1, 2], $ebook->chapters()->pluck('position')->all());
        $this->assertSame(2, $ebook->chapters()->where('slug', 'bab-satu')->value('position'));
        $this->assertSame(1, $ebook->chapters()->where('slug', 'bab-dua')->value('position'));
        $this->actingAs($admin)->get("/admin/library/{$ebook->id}/preview")
            ->assertOk()->assertSeeInOrder(['1. Bab Dua', '2. Bab Satu']);
    }

    public function test_chapter_update_requires_valid_title_and_body(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ebook = LibraryItem::create([
            'title' => 'Draf Validasi Bab', 'slug' => 'draf-validasi-bab', 'type' => 'ebook',
            'summary_preview' => 'Draf uji.', 'status' => 'pending',
        ]);
        $chapter = $ebook->chapters()->create([
            'position' => 1, 'slug' => 'bab-validasi', 'title' => 'Bab Awal',
            'summary_points' => ['Poin awal'], 'body' => 'Isi awal.',
        ]);

        $this->actingAs($admin)->put("/admin/library/{$ebook->id}/chapters/{$chapter->id}", [
            'title' => '', 'body' => '',
        ])->assertSessionHasErrors(['title', 'summary_points', 'body']);

        $this->assertSame('Bab Awal', $chapter->fresh()->title);
        $this->assertSame('Isi awal.', $chapter->fresh()->body);
    }

    public function test_chapter_reordering_at_both_ends_keeps_order_and_positions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ebook = LibraryItem::create([
            'title' => 'Draf Batas Bab', 'slug' => 'draf-batas-bab', 'type' => 'ebook',
            'summary_preview' => 'Draf uji.', 'status' => 'pending',
        ]);
        $first = $ebook->chapters()->create([
            'position' => 1, 'slug' => 'bab-pertama', 'title' => 'Bab Pertama',
            'summary_points' => ['Poin'], 'body' => 'Isi pertama.',
        ]);
        $last = $ebook->chapters()->create([
            'position' => 2, 'slug' => 'bab-terakhir', 'title' => 'Bab Terakhir',
            'summary_points' => ['Poin'], 'body' => 'Isi terakhir.',
        ]);

        $this->actingAs($admin)->post("/admin/library/{$ebook->id}/chapters/{$first->id}/move-up")
            ->assertRedirect(route('admin.library.ebook.preview', $ebook));
        $this->actingAs($admin)->post("/admin/library/{$ebook->id}/chapters/{$last->id}/move-down")
            ->assertRedirect(route('admin.library.ebook.preview', $ebook));

        $this->assertSame(['Bab Pertama', 'Bab Terakhir'], $ebook->chapters()->pluck('title')->all());
        $this->assertSame([1, 2], $ebook->chapters()->pluck('position')->all());
    }

    public function test_admin_can_edit_and_delete_chapter_and_positions_are_compacted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ebook = LibraryItem::create([
            'title' => 'Draf Edit Bab', 'slug' => 'draf-edit-bab', 'type' => 'ebook',
            'summary_preview' => 'Draf uji.', 'status' => 'pending',
        ]);
        $first = $ebook->chapters()->create([
            'position' => 1, 'slug' => 'bab-satu', 'title' => 'Bab Satu',
            'summary_points' => ['Poin awal'], 'body' => 'Isi awal.',
        ]);
        $middle = $ebook->chapters()->create([
            'position' => 2, 'slug' => 'bab-tengah', 'title' => 'Bab Tengah',
            'summary_points' => ['Poin'], 'body' => 'Isi.',
        ]);
        $last = $ebook->chapters()->create([
            'position' => 3, 'slug' => 'bab-terakhir', 'title' => 'Bab Terakhir',
            'summary_points' => ['Poin'], 'body' => 'Isi.',
        ]);

        $this->actingAs($admin)->get("/admin/library/{$ebook->id}/chapters/{$first->id}/edit")
            ->assertOk()->assertSee('Isi awal.');
        $this->actingAs($admin)->put("/admin/library/{$ebook->id}/chapters/{$first->id}", [
            'title' => 'Bab Pertama Diperbarui',
            'subtitle' => 'Versi baru',
            'summary_points' => ['Poin baru'],
            'body' => 'Konten yang diperbarui.',
        ])->assertRedirect(route('admin.library.ebook.preview', $ebook));

        $this->assertDatabaseHas('library_chapters', [
            'id' => $first->id,
            'title' => 'Bab Pertama Diperbarui',
            'body' => 'Konten yang diperbarui.',
        ]);
        $this->assertSame('bab-satu', $first->fresh()->slug);
        $this->assertSame('3 menit', $ebook->fresh()->reading_time);
        $this->actingAs($admin)->get("/admin/library/{$ebook->id}/preview/bab/{$first->slug}")
            ->assertOk()->assertSee('Konten yang diperbarui.');

        $this->actingAs($admin)->delete("/admin/library/{$ebook->id}/chapters/{$middle->id}")
            ->assertRedirect(route('admin.library.ebook.preview', $ebook));

        $this->assertDatabaseMissing('library_chapters', ['id' => $middle->id]);
        $this->assertSame(['bab-satu', 'bab-terakhir'], $ebook->chapters()->pluck('slug')->all());
        $this->assertSame([1, 2], $ebook->chapters()->pluck('position')->all());
        $this->assertDatabaseHas('library_chapters', ['id' => $last->id, 'position' => 2]);
        $this->assertSame('2 menit', $ebook->fresh()->reading_time);
    }

    public function test_chapter_routes_cannot_edit_or_move_another_ebooks_chapter(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ebook = LibraryItem::create([
            'title' => 'Ebook Pemilik', 'slug' => 'ebook-pemilik', 'type' => 'ebook',
            'summary_preview' => 'Uji.', 'status' => 'pending',
        ]);
        $otherEbook = LibraryItem::create([
            'title' => 'Ebook Lain', 'slug' => 'ebook-lain', 'type' => 'ebook',
            'summary_preview' => 'Uji.', 'status' => 'pending',
        ]);
        $chapter = $otherEbook->chapters()->create([
            'position' => 1, 'slug' => 'bab-rahasia', 'title' => 'Bab Rahasia',
            'summary_points' => ['Poin'], 'body' => 'Isi rahasia.',
        ]);

        $this->actingAs($admin)->get("/admin/library/{$ebook->id}/chapters/{$chapter->id}/edit")->assertNotFound();
        $this->actingAs($admin)->put("/admin/library/{$ebook->id}/chapters/{$chapter->id}", [
            'title' => 'Bab Diubah', 'summary_points' => ['Poin'], 'body' => 'Perubahan.',
        ])->assertNotFound();
        $this->actingAs($admin)->post("/admin/library/{$ebook->id}/chapters/{$chapter->id}/move-up")->assertNotFound();
        $this->actingAs($admin)->delete("/admin/library/{$ebook->id}/chapters/{$chapter->id}")->assertNotFound();
        $this->assertDatabaseHas('library_chapters', ['id' => $chapter->id, 'position' => 1]);
    }

    public function test_admin_must_confirm_quote_approval_before_it_can_be_shared(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ebook = LibraryItem::create([
            'title' => 'Draf Kutipan', 'slug' => 'draf-kutipan', 'type' => 'ebook',
            'summary_preview' => 'Draf uji.', 'status' => 'pending',
        ]);
        $payload = [
            'title' => 'Bab Kutipan',
            'summary_points' => ['Intisari'],
            'body' => 'Isi bab.',
            'approved_quotes' => [[
                'text' => 'Kutipan yang perlu persetujuan.',
                'attribution' => 'Narasumber Uji',
            ]],
        ];

        $this->actingAs($admin)->post("/admin/library/{$ebook->id}/chapters", $payload)
            ->assertSessionHasErrors('approved_quotes.0.approved');
        $this->assertDatabaseMissing('library_chapters', ['title' => 'Bab Kutipan']);

        $payload['approved_quotes'][0]['approved'] = '1';
        $this->actingAs($admin)->post("/admin/library/{$ebook->id}/chapters", $payload)
            ->assertRedirect(route('admin.library.ebook.preview', $ebook));

        $chapter = $ebook->chapters()->firstOrFail();
        $this->assertSame([[
            'text' => 'Kutipan yang perlu persetujuan.',
            'attribution' => 'Narasumber Uji',
        ]], $chapter->approved_quotes);
    }
}
