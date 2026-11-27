<?php

namespace Tests\Feature;

use App\Models\LibraryChapter;
use App\Models\LibraryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EbookChecklistTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_save_and_clear_checklist_for_a_chapter(): void
    {
        $member = User::factory()->create(['role' => 'member', 'status' => 'active']);
        $chapter = $this->createChapter('ebook-checklist');
        $reader = "/library/ebook-checklist/bab/{$chapter->slug}";

        $this->actingAs($member)->post('/library/ebook-checklist/progress', [
            ...$this->formFor($chapter),
            'checked' => ['1'],
        ])->assertRedirect($reader.'#amalkan-pekan-ini');

        $this->get($reader)->assertOk()
            ->assertSee('name="checked[]" value="1" checked', false)
            ->assertDontSee('name="checked[]" value="0" checked', false);

        $this->post('/library/ebook-checklist/progress', $this->formFor($chapter))
            ->assertRedirect($reader.'#amalkan-pekan-ini');
        $this->get($reader)->assertOk()->assertDontSee('name="checked[]" value="1" checked', false);
    }

    public function test_guest_can_try_checkboxes_without_a_save_form_and_cannot_post_progress(): void
    {
        $chapter = $this->createChapter('ebook-tamu');

        $this->get('/library/ebook-tamu/bab/bab-aksi')
            ->assertOk()
            ->assertSee('type="checkbox"', false)
            ->assertSee('Masuk untuk menyimpan checklist')
            ->assertDontSee('/library/ebook-tamu/progress', false);

        $this->post('/library/ebook-tamu/progress', $this->formFor($chapter))->assertRedirect(route('login'));
        $this->assertDatabaseCount('ebook_progress', 0);
    }

    public function test_saving_a_second_chapter_preserves_the_first_and_other_users_are_isolated(): void
    {
        $member = User::factory()->create(['role' => 'member', 'status' => 'active']);
        $other = User::factory()->create(['role' => 'member', 'status' => 'active']);
        $first = $this->createChapter('ebook-terpisah');
        $second = $first->libraryItem->chapters()->create([
            'title' => 'Bab Kedua',
            'slug' => 'bab-kedua',
            'position' => 2,
            'body' => 'Isi bab kedua.',
            'action_checklist' => ['Ulangi satu langkah.'],
        ]);

        $progress = '/library/ebook-terpisah/progress';
        $reader = '/library/ebook-terpisah/bab/bab-aksi';
        $form = [...$this->formFor($first), 'checked' => ['1']];
        $this->actingAs($member)->post($progress, $form)->assertRedirect();
        $this->post($progress, [...$this->formFor($second), 'checked' => ['0']])->assertRedirect();
        $this->get($reader)->assertSee('name="checked[]" value="1" checked', false);
        $this->get('/library/ebook-terpisah/bab/bab-kedua')
            ->assertSee('name="checked[]" value="0" checked', false);

        $this->actingAs($other)->get($reader)->assertDontSee('name="checked[]" value="1" checked', false);
        $this->post($progress, [
            ...$this->formFor($first),
            'checked' => ['0'],
            'user_id' => $member->id,
        ])->assertRedirect();
        $this->get($reader)->assertSee('name="checked[]" value="0" checked', false);
        $this->actingAs($member)->get($reader)
            ->assertSee('name="checked[]" value="1" checked', false)
            ->assertDontSee('name="checked[]" value="0" checked', false);
        $this->assertDatabaseCount('ebook_progress', 2);
    }

    public function test_wrong_ebook_private_ebook_and_invalid_checklist_values_are_rejected(): void
    {
        $member = User::factory()->create(['role' => 'member', 'status' => 'active']);
        $chapter = $this->createChapter('ebook-valid');
        $other = $this->createChapter('ebook-lain');
        $other->libraryItem->update(['is_public' => false]);
        $reader = '/library/ebook-valid/bab/bab-aksi';
        $progress = '/library/ebook-valid/progress';

        $this->actingAs($member)->post($progress, $this->formFor($other))->assertNotFound();
        $this->post('/library/ebook-lain/progress', $this->formFor($other))->assertNotFound();
        $this->from($reader)->post($progress, [
            ...$this->formFor($chapter),
            'checked' => ['4'],
        ])->assertRedirect($reader)->assertSessionHasErrors('checked.0');
        $this->assertDatabaseCount('ebook_progress', 0);
    }

    public function test_old_checkmarks_are_not_reused_after_staff_changes_the_list(): void
    {
        $member = User::factory()->create(['role' => 'member', 'status' => 'active']);
        $chapter = $this->createChapter('ebook-direvisi');
        $reader = '/library/ebook-direvisi/bab/bab-aksi';
        $progress = '/library/ebook-direvisi/progress';
        $oldForm = [...$this->formFor($chapter), 'checked' => ['0']];

        $this->actingAs($member)->post($progress, $oldForm)->assertRedirect();
        $chapter->update(['action_checklist' => ['Aksi baru.', 'Catat hasilnya.']]);
        $this->get($reader)->assertDontSee('name="checked[]" value="0" checked', false);
        $this->from($reader)->post($progress, $oldForm)->assertRedirect($reader)
            ->assertSessionHasErrors('checklist_version');
    }

    public function test_only_active_subscriber_can_save_progress(): void
    {
        $chapter = $this->createChapter('ebook-subscriber');
        $subscriber = User::factory()->create(['role' => 'subscriber', 'status' => 'pending']);
        $progress = '/library/ebook-subscriber/progress';

        $this->actingAs($subscriber)->post($progress, $this->formFor($chapter))->assertForbidden();
        $this->assertDatabaseCount('ebook_progress', 0);

        $subscriber->update(['status' => 'active']);
        $this->actingAs($subscriber)->post($progress, $this->formFor($chapter))->assertRedirect();
        $this->assertDatabaseCount('ebook_progress', 1);
    }

    private function createChapter(string $ebookSlug): LibraryChapter
    {
        $ebook = LibraryItem::create([
            'title' => 'Ebook Checklist Uji',
            'slug' => $ebookSlug,
            'type' => 'ebook',
            'summary_preview' => 'Ebook publik untuk tes.',
            'status' => 'approved',
            'is_public' => true,
        ]);

        return $ebook->chapters()->create([
            'title' => 'Bab Aksi',
            'slug' => 'bab-aksi',
            'position' => 1,
            'body' => 'Isi bab untuk tes.',
            'action_title' => 'Aksi pekan ini.',
            'action_checklist' => ['Tentukan satu langkah.', 'Catat hasilnya.'],
        ]);
    }

    private function formFor(LibraryChapter $chapter): array
    {
        return [
            'chapter_id' => $chapter->id,
            'checklist_version' => hash('sha256', json_encode($chapter->action_checklist, JSON_THROW_ON_ERROR)),
        ];
    }
}
