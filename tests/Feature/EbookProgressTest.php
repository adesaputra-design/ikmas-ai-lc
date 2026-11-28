<?php

namespace Tests\Feature;

use App\Models\LibraryChapter;
use App\Models\LibraryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EbookProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_reading_a_chapter_saves_position_and_the_cover_offers_to_continue(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $ebook = $this->createEbook('ebook-lanjut-1');
        $firstChapter = $this->createChapter($ebook, 'bab-satu', 1);
        $secondChapter = $this->createChapter($ebook, 'bab-dua', 2);

        $this->actingAs($member)->get("/library/{$ebook->slug}/bab/{$firstChapter->slug}")->assertOk();
        $progress = $this->progress($member, $ebook);
        $this->assertSame($firstChapter->id, (int) $progress->last_chapter_id);

        $this->actingAs($member)->get("/library/{$ebook->slug}/bab/{$secondChapter->slug}")->assertOk();
        $this->assertSame($secondChapter->id, (int) $this->progress($member, $ebook)->last_chapter_id);

        $this->actingAs($member)->get("/library/{$ebook->slug}")
            ->assertOk()
            ->assertSee('Lanjutkan membaca')
            ->assertSee(route('library.ebook.chapter', [$ebook->slug, $secondChapter->slug]));
    }

    public function test_progress_is_isolated_by_member_and_ebook(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $otherMember = User::factory()->create(['role' => 'member']);
        $firstEbook = $this->createEbook('ebook-progres-satu');
        $secondEbook = $this->createEbook('ebook-progres-dua');
        $firstChapter = $this->createChapter($firstEbook, 'bab-pertama', 1);
        $secondChapter = $this->createChapter($secondEbook, 'bab-kedua', 1);

        $this->actingAs($member)->get("/library/{$firstEbook->slug}/bab/{$firstChapter->slug}")->assertOk();
        $this->actingAs($member)->get("/library/{$secondEbook->slug}/bab/{$secondChapter->slug}")->assertOk();
        $this->actingAs($otherMember)->get("/library/{$firstEbook->slug}/bab/{$firstChapter->slug}")->assertOk();

        $this->assertSame($firstChapter->id, (int) $this->progress($member, $firstEbook)->last_chapter_id);
        $this->assertSame($secondChapter->id, (int) $this->progress($member, $secondEbook)->last_chapter_id);
        $this->assertSame($firstChapter->id, (int) $this->progress($otherMember, $firstEbook)->last_chapter_id);
        $this->assertDatabaseCount('ebook_progress', 3);
    }

    public function test_guest_and_inactive_subscriber_do_not_create_or_see_saved_progress(): void
    {
        $ebook = $this->createEbook('ebook-progres-publik');
        $chapter = $this->createChapter($ebook, 'bab-publik', 1);

        $this->get("/library/{$ebook->slug}/bab/{$chapter->slug}")->assertOk();
        $this->get("/library/{$ebook->slug}")->assertOk()->assertDontSee('Lanjutkan membaca');

        $inactiveSubscriber = User::factory()->create(['role' => 'subscriber', 'status' => 'pending']);
        $this->actingAs($inactiveSubscriber)->get("/library/{$ebook->slug}/bab/{$chapter->slug}")->assertOk();
        $this->assertDatabaseCount('ebook_progress', 0);
    }

    public function test_deleted_last_chapter_removes_the_continue_link(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $ebook = $this->createEbook('ebook-bab-dihapus');
        $chapter = $this->createChapter($ebook, 'bab-terakhir', 1);

        $this->actingAs($member)->get("/library/{$ebook->slug}/bab/{$chapter->slug}")->assertOk();
        $chapter->delete();

        $this->assertNull($this->progress($member, $ebook)->last_chapter_id);
        $this->actingAs($member)->get("/library/{$ebook->slug}")
            ->assertOk()
            ->assertDontSee('Lanjutkan membaca');
    }

    public function test_cross_ebook_last_chapter_reference_is_not_used_for_resume(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $ebook = $this->createEbook('ebook-pemilik-progres');
        $otherEbook = $this->createEbook('ebook-bab-asing');
        $ownChapter = $this->createChapter($ebook, 'bab-sendiri', 1);
        $foreignChapter = $this->createChapter($otherEbook, 'bab-asing', 1);
        DB::table('ebook_progress')->insert([
            'user_id' => $member->id,
            'library_item_id' => $ebook->id,
            'last_chapter_id' => $foreignChapter->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($member)->get("/library/{$ebook->slug}")
            ->assertOk()
            ->assertDontSee('Lanjutkan membaca')
            ->assertSee($ownChapter->title);
    }

    private function createEbook(string $slug): LibraryItem
    {
        return LibraryItem::create([
            'title' => "Ebook {$slug}",
            'slug' => $slug,
            'type' => 'ebook',
            'summary_preview' => 'Pengantar.',
            'status' => 'approved',
            'is_public' => true,
        ]);
    }

    private function createChapter(LibraryItem $ebook, string $slug, int $position): LibraryChapter
    {
        return $ebook->chapters()->create([
            'position' => $position,
            'slug' => $slug,
            'title' => "Judul {$slug}",
            'summary_points' => ['Poin'],
            'body' => 'Isi bab.',
        ]);
    }

    private function progress(User $user, LibraryItem $ebook): object
    {
        return (object) DB::table('ebook_progress')
            ->where('user_id', $user->id)
            ->where('library_item_id', $ebook->id)
            ->first();
    }
}
