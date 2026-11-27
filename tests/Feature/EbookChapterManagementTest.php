<?php

namespace Tests\Feature;

use App\Models\LibraryChapter;
use App\Models\LibraryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EbookChapterManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /**
     * @param  list<string>  $titles
     */
    private function createPublishedEbook(array $titles = ['Bab Satu', 'Bab Dua', 'Bab Tiga'], string $slug = 'ebook-uji'): LibraryItem
    {
        $ebook = LibraryItem::create([
            'title' => 'Ebook Uji',
            'slug' => $slug,
            'type' => 'ebook',
            'summary_preview' => 'Pengantar.',
            'status' => 'approved',
            'is_public' => true,
        ]);

        foreach ($titles as $title) {
            $ebook->chapters()->create([
                'title' => $title,
                'summary_points' => ["Intisari {$title}"],
                'body' => "Isi {$title}.",
            ]);
        }

        return $ebook;
    }

    /**
     * @return list<string>
     */
    private function orderedTitles(LibraryItem $ebook): array
    {
        return $ebook->chapters()->pluck('title')->all();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function chapterPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Bab Satu Revisi',
            'subtitle' => 'Subjudul baru',
            'summary_points' => ['Intisari revisi'],
            'body' => str_repeat('kata ', 450),
            'research_box' => 'Riset revisi.',
            'action_title' => 'Aksi revisi',
            'action_checklist' => ['Langkah revisi'],
        ], $overrides);
    }

    public function test_admin_edits_chapter_and_readers_see_the_change_with_a_stable_url(): void
    {
        $ebook = $this->createPublishedEbook();
        $chapter = $ebook->chapters()->first();

        $this->actingAs($this->admin)
            ->get(route('admin.library.chapters.edit', [$ebook, $chapter]))
            ->assertOk()
            ->assertSee('Bab Satu');

        $this->actingAs($this->admin)
            ->put(route('admin.library.chapters.update', [$ebook, $chapter]), $this->chapterPayload())
            ->assertRedirect(route('admin.library.chapters.index', $ebook));

        $chapter->refresh();
        $this->assertSame('bab-satu', $chapter->slug);
        $this->assertSame(3, $chapter->reading_minutes);
        $this->assertSame(['Langkah revisi'], $chapter->action_checklist);
        $this->assertSame('5 menit', $ebook->fresh()->reading_time);

        $this->get(route('library.ebook.chapter', [$ebook->slug, 'bab-satu']))
            ->assertOk()
            ->assertSee('Bab Satu Revisi')
            ->assertSee('Intisari revisi')
            ->assertDontSee('Intisari Bab Satu');
    }

    public function test_chapter_update_is_validated(): void
    {
        $ebook = $this->createPublishedEbook();
        $chapter = $ebook->chapters()->first();

        $this->actingAs($this->admin)
            ->put(route('admin.library.chapters.update', [$ebook, $chapter]), ['title' => '', 'body' => ''])
            ->assertSessionHasErrors(['title', 'body']);

        $this->assertSame('Bab Satu', $chapter->fresh()->title);
    }

    public function test_moving_chapters_updates_table_of_contents_and_navigation(): void
    {
        $ebook = $this->createPublishedEbook();
        $third = $ebook->chapters()->where('title', 'Bab Tiga')->first();

        $this->actingAs($this->admin)
            ->post(route('admin.library.chapters.move', [$ebook, $third]), ['direction' => 'up'])
            ->assertRedirect(route('admin.library.chapters.index', $ebook));

        $this->assertSame(['Bab Satu', 'Bab Tiga', 'Bab Dua'], $this->orderedTitles($ebook));
        $this->assertSame([1, 2, 3], $ebook->chapters()->pluck('position')->all());

        $this->get(route('library.show', $ebook->slug))
            ->assertOk()
            ->assertSeeInOrder(['Bab Satu', 'Bab Tiga', 'Bab Dua']);

        $this->get(route('library.ebook.chapter', [$ebook->slug, 'bab-tiga']))
            ->assertOk()
            ->assertSee('Bab 2')
            ->assertSeeInOrder(['← Bab Satu', 'Bab Dua →']);
    }

    public function test_moving_beyond_the_ends_keeps_the_order_unchanged(): void
    {
        $ebook = $this->createPublishedEbook();
        $first = $ebook->chapters()->where('title', 'Bab Satu')->first();
        $last = $ebook->chapters()->where('title', 'Bab Tiga')->first();

        $this->actingAs($this->admin)->post(route('admin.library.chapters.move', [$ebook, $first]), ['direction' => 'up'])->assertRedirect();
        $this->actingAs($this->admin)->post(route('admin.library.chapters.move', [$ebook, $last]), ['direction' => 'down'])->assertRedirect();
        $this->actingAs($this->admin)->post(route('admin.library.chapters.move', [$ebook, $first]), ['direction' => 'sideways'])
            ->assertSessionHasErrors('direction');

        $this->assertSame(['Bab Satu', 'Bab Dua', 'Bab Tiga'], $this->orderedTitles($ebook));
        $this->assertSame([1, 2, 3], $ebook->chapters()->pluck('position')->all());
    }

    public function test_deleting_a_chapter_closes_the_gap_and_updates_reading_time(): void
    {
        $ebook = $this->createPublishedEbook();
        $middle = $ebook->chapters()->where('title', 'Bab Dua')->first();
        $this->assertSame('3 menit', $ebook->fresh()->reading_time);

        $this->actingAs($this->admin)
            ->delete(route('admin.library.chapters.destroy', [$ebook, $middle]))
            ->assertRedirect(route('admin.library.chapters.index', $ebook));

        $this->assertModelMissing($middle);
        $this->assertSame(['Bab Satu', 'Bab Tiga'], $this->orderedTitles($ebook));
        $this->assertSame([1, 2], $ebook->chapters()->pluck('position')->all());
        $this->assertSame('2 menit', $ebook->fresh()->reading_time);

        $this->get(route('library.ebook.chapter', [$ebook->slug, 'bab-dua']))->assertNotFound();

        $ebook->chapters()->create(['title' => 'Bab Empat', 'body' => 'Isi.']);
        $this->assertSame([1, 2, 3], $ebook->chapters()->pluck('position')->all());
    }

    public function test_chapter_of_another_ebook_cannot_be_managed_through_this_ebook(): void
    {
        $ebook = $this->createPublishedEbook();
        $other = $this->createPublishedEbook(['Bab Asing'], 'ebook-lain');
        $foreign = $other->chapters()->first();

        $this->actingAs($this->admin)->get(route('admin.library.chapters.edit', [$ebook, $foreign]))->assertNotFound();
        $this->actingAs($this->admin)->put(route('admin.library.chapters.update', [$ebook, $foreign]), $this->chapterPayload())->assertNotFound();
        $this->actingAs($this->admin)->post(route('admin.library.chapters.move', [$ebook, $foreign]), ['direction' => 'up'])->assertNotFound();
        $this->actingAs($this->admin)->delete(route('admin.library.chapters.destroy', [$ebook, $foreign]))->assertNotFound();

        $this->assertModelExists($foreign);
        $this->assertSame('Bab Asing', $foreign->fresh()->title);
    }

    public function test_staff_needs_library_permission_to_edit_move_or_delete_chapters(): void
    {
        $ebook = $this->createPublishedEbook();
        $chapter = $ebook->chapters()->where('title', 'Bab Dua')->first();
        $staffWithout = User::factory()->create(['role' => 'staff', 'permissions' => ['events']]);
        $staffWith = User::factory()->create(['role' => 'staff', 'permissions' => ['library']]);

        $this->actingAs($staffWithout)->get(route('admin.library.chapters.edit', [$ebook, $chapter]))->assertForbidden();
        $this->actingAs($staffWithout)->put(route('admin.library.chapters.update', [$ebook, $chapter]), $this->chapterPayload())->assertForbidden();
        $this->actingAs($staffWithout)->post(route('admin.library.chapters.move', [$ebook, $chapter]), ['direction' => 'up'])->assertForbidden();
        $this->actingAs($staffWithout)->delete(route('admin.library.chapters.destroy', [$ebook, $chapter]))->assertForbidden();
        $this->assertSame(['Bab Satu', 'Bab Dua', 'Bab Tiga'], $this->orderedTitles($ebook));

        $this->actingAs($staffWith)->post(route('admin.library.chapters.move', [$ebook, $chapter]), ['direction' => 'up'])->assertRedirect();
        $this->assertSame(['Bab Dua', 'Bab Satu', 'Bab Tiga'], $this->orderedTitles($ebook));

        $this->actingAs($staffWith)->delete(route('admin.library.chapters.destroy', [$ebook, $chapter]))->assertRedirect();
        $this->assertSame(0, LibraryChapter::whereKey($chapter->id)->count());
    }
}
