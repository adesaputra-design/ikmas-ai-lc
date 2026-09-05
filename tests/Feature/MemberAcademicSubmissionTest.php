<?php

namespace Tests\Feature;

use App\Models\LibraryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MemberAcademicSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_academic_submission_form(): void
    {
        $response = $this->get('/member/library/create');
        $response->assertRedirect('/login');
    }

    public function test_subscriber_cannot_access_or_submit_academic_paper(): void
    {
        $subscriber = User::factory()->create([
            'role' => 'subscriber',
            'status' => 'active',
        ]);

        $responseForm = $this->actingAs($subscriber)->get('/member/library/create');
        $responseForm->assertStatus(403);

        $responseSubmit = $this->actingAs($subscriber)->post('/member/library', [
            'title' => 'Riset AI Non-Alumni',
            'academic_degree' => 'skripsi',
            'institution' => 'Kampus Luar',
            'summary_preview' => 'Abstrak riset...',
        ]);
        $responseSubmit->assertStatus(403);
    }

    public function test_alumni_member_can_view_submission_form(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($member)->get('/member/library/create');
        $response->assertStatus(200);
        $response->assertSee('Ajukan Karya Ilmiah Alumni');
    }

    public function test_alumni_member_can_submit_academic_paper_with_pdf(): void
    {
        Storage::fake('public');

        $member = User::factory()->create(['role' => 'member']);
        $fakePdf = UploadedFile::fake()->create('riset_alumni.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($member)->post('/member/library', [
            'title' => 'Optimasi Agen Otonom untuk Penjadwalan Santri',
            'academic_degree' => 'tesis',
            'institution' => 'Institut Teknologi Bandung',
            'publication_year' => 2024,
            'co_authors' => 'Dr. Pembimbing, M.Kom.',
            'category' => 'LLM & Prompting',
            'summary_preview' => 'Penelitian penerapan sistem agen AI otonom dalam alur kurikulum.',
            'content' => 'Laporan lengkap bab 1 sampai 5...',
            'external_url' => 'https://doi.org/10.1234/sample.ai',
            'pdf_file' => $fakePdf,
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect(route('member.dashboard'));

        $this->assertDatabaseHas('library_items', [
            'title' => 'Optimasi Agen Otonom untuk Penjadwalan Santri',
            'user_id' => $member->id,
            'type' => 'academic',
            'academic_degree' => 'tesis',
            'institution' => 'Institut Teknologi Bandung',
            'status' => 'pending',
        ]);

        $item = LibraryItem::where('title', 'Optimasi Agen Otonom untuk Penjadwalan Santri')->first();
        $this->assertNotNull($item->file_path);
        Storage::disk('public')->assertExists($item->file_path);
    }

    public function test_alumni_member_can_see_submitted_paper_in_dashboard(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        $paper = LibraryItem::create([
            'user_id' => $member->id,
            'title' => 'Studi Kasus NLP Pesantren',
            'slug' => 'studi-kasus-nlp-pesantren',
            'type' => 'academic',
            'academic_degree' => 'skripsi',
            'institution' => 'Universitas Indonesia',
            'summary_preview' => 'Riset NLP...',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($member)->get('/member/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Studi Kasus NLP Pesantren');
        $response->assertSee('Menunggu Kurasi');
    }
}
