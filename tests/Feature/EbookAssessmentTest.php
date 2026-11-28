<?php

namespace Tests\Feature;

use App\Models\LibraryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EbookAssessmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_complete_an_ebook_assessment_from_its_active_definition(): void
    {
        $ebook = $this->createPublicEbook();
        $ebook->chapters()->create([
            'position' => 1,
            'slug' => 'bab-refleksi',
            'title' => 'Bab Refleksi',
            'summary_points' => ['Intisari'],
            'body' => 'Isi bab.',
        ]);
        $ebook->update(['assessment' => $this->assessment('bab-refleksi')]);

        $this->get("/library/{$ebook->slug}/skor")
            ->assertOk()
            ->assertSee('Cek Skor JARIB-mu')
            ->assertSee('Seberapa konsisten Anda melakukan refleksi?')
            ->assertSee('Masuk untuk menyimpan hasil dan progres');
    }

    public function test_admin_can_update_a_valid_assessment_and_invalid_chapter_references_are_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ebook = $this->createPublicEbook();
        $ebook->chapters()->create([
            'position' => 1,
            'slug' => 'bab-refleksi',
            'title' => 'Bab Refleksi',
            'summary_points' => ['Intisari'],
            'body' => 'Isi bab.',
        ]);

        $updatedAssessment = $this->assessment('bab-refleksi');
        $updatedAssessment['principles'][0]['questions'][0]['text'] = 'Seberapa teratur Anda melakukan refleksi?';

        $this->actingAs($admin)
            ->put("/admin/library/{$ebook->id}/assessment", ['assessment' => json_encode($updatedAssessment)])
            ->assertRedirect(route('admin.library.ebook.preview', $ebook));
        $this->assertSame($updatedAssessment, $ebook->fresh()->assessment);
        $this->get("/library/{$ebook->slug}/skor")->assertOk()->assertSee('Seberapa teratur Anda melakukan refleksi?');

        $this->actingAs($admin)
            ->put("/admin/library/{$ebook->id}/assessment", ['assessment' => json_encode($this->assessment('bab-tidak-ada'))])
            ->assertSessionHasErrors('assessment');

        $incompleteAssessment = $this->assessment('bab-refleksi');
        unset($incompleteAssessment['ranges']);
        $this->actingAs($admin)
            ->put("/admin/library/{$ebook->id}/assessment", ['assessment' => json_encode($incompleteAssessment)])
            ->assertSessionHasErrors('assessment');
    }

    public function test_assessment_editor_requires_library_permission(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'permissions' => []]);
        $ebook = $this->createPublicEbook();

        $this->actingAs($staff)->get("/admin/library/{$ebook->id}/assessment/edit")->assertForbidden();
    }

    private function createPublicEbook(): LibraryItem
    {
        return LibraryItem::create([
            'title' => 'Ebook Skor Uji',
            'slug' => 'ebook-skor-uji',
            'type' => 'ebook',
            'summary_preview' => 'Pengantar.',
            'status' => 'approved',
            'is_public' => true,
        ]);
    }

    private function assessment(string $chapterSlug): array
    {
        return [
            'scale' => ['min' => 1, 'max' => 5, 'labels' => ['1' => 'Belum', '2' => 'Jarang', '3' => 'Kadang', '4' => 'Sering', '5' => 'Konsisten']],
            'principles' => [[
                'id' => 'refleksi',
                'name' => 'Refleksi',
                'questions' => [
                    ['id' => 'refleksi_1', 'text' => 'Seberapa konsisten Anda melakukan refleksi?'],
                    ['id' => 'refleksi_2', 'text' => 'Seberapa sering Anda mengevaluasi keputusan?'],
                ],
            ]],
            'ranges' => [[
                'min' => 2,
                'max' => 10,
                'label' => 'Terus bertumbuh',
                'description' => 'Lanjutkan kebiasaan baik.',
                'recommendation_chapters' => [$chapterSlug],
            ]],
        ];
    }
}
