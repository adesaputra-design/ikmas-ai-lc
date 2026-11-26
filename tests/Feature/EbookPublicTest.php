<?php

namespace Tests\Feature;

use App\Models\Bookmark;
use App\Models\LibraryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EbookPublicTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createEbook(array $overrides = []): LibraryItem
    {
        $ebook = LibraryItem::create(array_merge([
            'title' => 'Ebook Uji Terbit',
            'slug' => 'ebook-uji-terbit',
            'type' => 'ebook',
            'category' => 'Seri JARIB',
            'summary_preview' => 'Pengantar ebook untuk pembaca.',
            'author_name' => 'Narasumber Uji',
            'series_name' => 'Seri JARIB',
            'edition_number' => 1,
            'status' => 'approved',
            'is_public' => true,
        ], $overrides));

        $ebook->chapters()->create([
            'title' => 'Bab Satu',
            'summary_points' => ['Intisari pertama'],
            'body' => 'Isi **bab pertama** yang lengkap.',
        ]);
        $ebook->chapters()->create([
            'title' => 'Bab Dua',
            'summary_points' => ['Intisari kedua'],
            'body' => 'Isi bab kedua yang lengkap.',
        ]);

        return $ebook;
    }

    public function test_guest_can_find_public_ebook_and_read_its_chapters(): void
    {
        $ebook = $this->createEbook();
        LibraryItem::create([
            'title' => 'Buku Lama',
            'slug' => 'buku-lama',
            'type' => 'book',
            'summary_preview' => 'Cuplikan buku.',
            'content' => 'Isi buku terkunci.',
            'status' => 'approved',
        ]);

        $this->get('/library?type=ebook')
            ->assertOk()
            ->assertSee('Ebook Uji Terbit')
            ->assertSee('Seri JARIB · Edisi #1')
            ->assertDontSee('Buku Lama');

        $this->get("/library/{$ebook->slug}")
            ->assertOk()
            ->assertSee('Daftar Isi')
            ->assertSee('2 menit baca')
            ->assertSee('Bab Satu')
            ->assertSee('Bab Dua')
            ->assertSee(route('library.ebook.chapter', [$ebook->slug, 'bab-satu']), false)
            ->assertDontSee('Isi bab pertama')
            ->assertDontSee('Mode pratinjau');

        $this->get("/library/{$ebook->slug}/bab/bab-satu")
            ->assertOk()
            ->assertSee('Intisari pertama')
            ->assertSee('Isi <strong>bab pertama</strong>', false)
            ->assertSee(route('library.ebook.chapter', [$ebook->slug, 'bab-dua']), false);

        $this->get("/library/{$ebook->slug}/bab/bab-dua")
            ->assertOk()
            ->assertSee('Isi bab kedua yang lengkap.')
            ->assertSee(route('library.ebook.chapter', [$ebook->slug, 'bab-satu']), false);

        $this->get("/library/{$ebook->slug}/bab/tidak-ada")->assertNotFound();
    }

    public function test_approved_but_unpublished_ebook_stays_hidden_from_everyone_public(): void
    {
        $ebook = $this->createEbook(['is_public' => false]);
        $member = User::factory()->create(['role' => 'member']);

        $this->get('/library?type=ebook')->assertOk()->assertDontSee('Ebook Uji Terbit');
        $this->get("/library/{$ebook->slug}")->assertNotFound();
        $this->get("/library/{$ebook->slug}/bab/bab-satu")->assertNotFound();
        $this->actingAs($member)->get("/library/{$ebook->slug}")->assertNotFound();
        $this->actingAs($member)->get("/library/{$ebook->slug}/bab/bab-satu")->assertNotFound();
    }

    public function test_chapter_route_does_not_serve_chapters_of_non_ebook_items(): void
    {
        $book = LibraryItem::create([
            'title' => 'Buku Terkunci',
            'slug' => 'buku-terkunci',
            'type' => 'book',
            'summary_preview' => 'Cuplikan.',
            'content' => 'Isi rangkuman rahasia untuk member.',
            'status' => 'approved',
        ]);

        $this->get("/library/{$book->slug}")->assertOk()->assertDontSee('Isi rangkuman rahasia untuk member.');
        $this->get("/library/{$book->slug}/bab/apa-saja")->assertNotFound();
    }

    public function test_member_bookmarks_public_ebook_and_sees_it_on_cover_and_dashboard(): void
    {
        $ebook = $this->createEbook();
        $member = User::factory()->create(['role' => 'member']);

        $this->actingAs($member)
            ->post(route('member.bookmarks.toggle'), ['type' => 'library', 'id' => $ebook->id])
            ->assertRedirect();

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $member->id,
            'bookmarkable_id' => $ebook->id,
            'bookmarkable_type' => 'library',
        ]);

        $this->actingAs($member)->get("/library/{$ebook->slug}")->assertOk()->assertSee('Hapus dari Bookmark');
        $this->actingAs($member)->get('/member/dashboard')->assertOk()->assertSee('Ebook Uji Terbit');
    }

    public function test_prompt_bookmark_with_same_id_does_not_mark_ebook_as_bookmarked(): void
    {
        $ebook = $this->createEbook();
        $member = User::factory()->create(['role' => 'member']);
        Bookmark::create([
            'user_id' => $member->id,
            'bookmarkable_id' => $ebook->id,
            'bookmarkable_type' => 'prompt',
        ]);

        $this->actingAs($member)->get("/library/{$ebook->slug}")->assertOk()->assertSee('Simpan ke Bookmark');
    }

    public function test_admin_publishes_ebook_only_after_editorial_approval_and_with_chapters(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ebook = $this->createEbook(['status' => 'pending', 'is_public' => false]);
        $empty = LibraryItem::create([
            'title' => 'Draf Kosong',
            'slug' => 'draf-kosong',
            'type' => 'ebook',
            'summary_preview' => 'Belum ada bab.',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post("/admin/library/{$ebook->id}/publish")
            ->assertSessionHasErrors('editorial_approved');
        $this->get("/library/{$ebook->slug}")->assertNotFound();

        $this->actingAs($admin)
            ->post("/admin/library/{$empty->id}/publish", ['editorial_approved' => '1'])
            ->assertSessionHasErrors('chapters');
        $this->assertSame('pending', $empty->fresh()->status);

        $this->actingAs($admin)
            ->post("/admin/library/{$ebook->id}/publish", ['editorial_approved' => '1'])
            ->assertRedirect(route('admin.library.chapters.index', $ebook));

        $ebook->refresh();
        $this->assertSame('approved', $ebook->status);
        $this->assertTrue($ebook->is_public);
        $this->get("/library/{$ebook->slug}")->assertOk();
    }

    public function test_ebook_cannot_be_published_through_generic_approve_or_edit_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ebook = $this->createEbook(['status' => 'pending', 'is_public' => false]);

        $this->actingAs($admin)->post("/admin/library/{$ebook->id}/approve")->assertNotFound();

        $this->actingAs($admin)->put("/admin/library/{$ebook->id}", [
            'title' => 'Ebook Uji Terbit',
            'category' => 'Seri JARIB',
            'summary_preview' => 'Pengantar ebook untuk pembaca.',
            'status' => 'approved',
            'is_public' => '1',
        ])->assertRedirect();

        $ebook->refresh();
        $this->assertSame('pending', $ebook->status);
        $this->assertFalse($ebook->is_public);
    }

    public function test_staff_without_library_permission_cannot_publish(): void
    {
        $ebook = $this->createEbook(['status' => 'pending', 'is_public' => false]);
        $staff = User::factory()->create(['role' => 'staff', 'permissions' => ['events']]);

        $this->actingAs($staff)
            ->post("/admin/library/{$ebook->id}/publish", ['editorial_approved' => '1'])
            ->assertForbidden();
        $this->assertSame('pending', $ebook->fresh()->status);
    }
}
