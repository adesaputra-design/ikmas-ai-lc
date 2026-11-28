<?php

namespace Tests\Feature;

use App\Models\LibraryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EbookAssessmentPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_save_server_calculated_score_and_read_own_attempt_history(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $ebook = $this->createPublicEbook();

        $this->actingAs($member)->post("/library/{$ebook->slug}/skor", [
            'answers' => ['q1' => '4', 'q2' => '3'],
            'total_score' => 999,
        ])->assertRedirect(route('library.ebook.assessment', $ebook->slug));
        $this->actingAs($member)->post("/library/{$ebook->slug}/skor", [
            'answers' => ['q1' => '5', 'q2' => '5'],
        ])->assertRedirect(route('library.ebook.assessment', $ebook->slug));

        $progress = $this->progressFor($member, $ebook);
        $attempts = json_decode($progress->assessment_results, true);
        $this->assertCount(2, $attempts);
        $this->assertSame(7, $attempts[0]['total']);
        $this->assertSame(['q1' => 4, 'q2' => 3], $attempts[0]['answers']);
        $this->assertSame('Berkembang', $attempts[0]['range']['label']);
        $this->assertSame(10, $attempts[1]['total']);

        $this->actingAs($member)->get("/library/{$ebook->slug}/skor")
            ->assertOk()
            ->assertSee('Riwayat percobaan')
            ->assertSee('Skor total: 7')
            ->assertSee('Skor total: 10')
            ->assertSee('Refleksi: 3.5')
            ->assertSee('Bab Refleksi');
    }

    public function test_guest_is_redirected_and_invalid_answers_are_not_saved(): void
    {
        $ebook = $this->createPublicEbook();
        $this->post("/library/{$ebook->slug}/skor", ['answers' => ['q1' => '4', 'q2' => '3']])
            ->assertRedirect(route('login'));

        $member = User::factory()->create(['role' => 'member']);
        $this->actingAs($member)->post("/library/{$ebook->slug}/skor", ['answers' => ['q1' => '4']])
            ->assertSessionHasErrors('answers');
        $this->actingAs($member)->post("/library/{$ebook->slug}/skor", ['answers' => ['q1' => '0', 'q2' => '3']])
            ->assertSessionHasErrors('answers');

        $this->assertDatabaseCount('ebook_progress', 0);
    }

    public function test_assessment_history_is_scoped_to_the_signed_in_user(): void
    {
        $firstMember = User::factory()->create(['role' => 'member']);
        $secondMember = User::factory()->create(['role' => 'member']);
        $ebook = $this->createPublicEbook();

        $this->actingAs($firstMember)->post("/library/{$ebook->slug}/skor", [
            'answers' => ['q1' => '5', 'q2' => '5'],
        ])->assertRedirect();
        $this->actingAs($secondMember)->get("/library/{$ebook->slug}/skor")
            ->assertOk()
            ->assertDontSee('Skor total: 10');
        $this->actingAs($secondMember)->post("/library/{$ebook->slug}/skor", [
            'answers' => ['q1' => '1', 'q2' => '1'],
            'user_id' => $firstMember->id,
            'total_score' => 999,
        ])->assertRedirect();

        $this->assertSame(10, $this->storedAttempts($firstMember, $ebook)[0]['total']);
        $this->assertSame(2, $this->storedAttempts($secondMember, $ebook)[0]['total']);
    }

    public function test_inactive_subscriber_cannot_save_an_assessment(): void
    {
        $subscriber = User::factory()->create(['role' => 'subscriber', 'status' => 'pending']);
        $ebook = $this->createPublicEbook();

        $this->actingAs($subscriber)->post("/library/{$ebook->slug}/skor", [
            'answers' => ['q1' => '4', 'q2' => '3'],
        ])->assertForbidden();

        $this->assertDatabaseCount('ebook_progress', 0);

        $activeSubscriber = User::factory()->create(['role' => 'subscriber', 'status' => 'active']);
        $this->actingAs($activeSubscriber)->post("/library/{$ebook->slug}/skor", [
            'answers' => ['q1' => '4', 'q2' => '3'],
        ])->assertRedirect();
        $this->assertDatabaseHas('ebook_progress', [
            'user_id' => $activeSubscriber->id,
            'library_item_id' => $ebook->id,
        ]);
    }

    private function createPublicEbook(): LibraryItem
    {
        $ebook = LibraryItem::create([
            'title' => 'Ebook Riwayat Uji',
            'slug' => 'ebook-riwayat-uji',
            'type' => 'ebook',
            'summary_preview' => 'Pengantar uji.',
            'status' => 'approved',
            'is_public' => true,
        ]);
        $ebook->chapters()->create([
            'position' => 1,
            'slug' => 'bab-refleksi',
            'title' => 'Bab Refleksi',
            'summary_points' => ['Intisari'],
            'body' => 'Isi bab.',
        ]);
        $ebook->update(['assessment' => [
            'scale' => ['min' => 1, 'max' => 5, 'labels' => ['1' => 'Belum', '2' => 'Jarang', '3' => 'Kadang', '4' => 'Sering', '5' => 'Konsisten']],
            'principles' => [[
                'id' => 'refleksi',
                'name' => 'Refleksi',
                'questions' => [
                    ['id' => 'q1', 'text' => 'Pertanyaan satu?'],
                    ['id' => 'q2', 'text' => 'Pertanyaan dua?'],
                ],
            ]],
            'ranges' => [
                ['min' => 2, 'max' => 6, 'label' => 'Awal', 'description' => 'Mulai bertumbuh.', 'recommendation_chapters' => ['bab-refleksi']],
                ['min' => 7, 'max' => 10, 'label' => 'Berkembang', 'description' => 'Terus bertumbuh.', 'recommendation_chapters' => ['bab-refleksi']],
            ],
        ]]);

        return $ebook;
    }

    private function progressFor(User $user, LibraryItem $ebook): object
    {
        return (object) DB::table('ebook_progress')
            ->where('user_id', $user->id)
            ->where('library_item_id', $ebook->id)
            ->first();
    }

    private function storedAttempts(User $user, LibraryItem $ebook): array
    {
        return json_decode($this->progressFor($user, $ebook)->assessment_results, true);
    }
}
