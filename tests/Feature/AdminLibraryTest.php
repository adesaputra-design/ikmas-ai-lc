<?php

namespace Tests\Feature;

use App\Models\LibraryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLibraryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'permissions' => ['library'],
        ]);

        $this->member = User::factory()->create([
            'role' => 'member',
            'alumni_year' => 2018,
        ]);
    }

    public function test_non_admin_cannot_access_admin_library(): void
    {
        $response = $this->actingAs($this->member)->get('/admin/library');
        $response->assertStatus(403);
    }

    public function test_admin_can_view_library_management_index(): void
    {
        LibraryItem::create([
            'user_id' => $this->admin->id,
            'title' => 'Buku Belajar AI',
            'slug' => 'buku-belajar-ai',
            'type' => 'book',
            'category' => 'AI Basics',
            'summary_preview' => 'Buku pengantar belajar AI.',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/library');

        $response->assertStatus(200);
        $response->assertSee('Pustaka AI');
        $response->assertSee('Buku Belajar AI');
    }

    public function test_admin_can_create_book_summary(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/library', [
            'type' => 'book',
            'title' => 'Designing Machine Learning Systems',
            'category' => 'MLOps',
            'summary_preview' => 'Buku pegangan arsitektur machine learning holistik.',
            'content' => 'Rangkuman lengkap bab 1 sampai 10 mengenai data flywheels.',
            'author_name' => 'Chip Huyen',
            'reading_time' => '15 mnt baca',
            'is_featured' => 1,
        ]);

        $response->assertRedirect('/admin/library?tab=book');
        $this->assertDatabaseHas('library_items', [
            'title' => 'Designing Machine Learning Systems',
            'type' => 'book',
            'author_name' => 'Chip Huyen',
            'status' => 'approved',
        ]);
    }

    public function test_admin_can_approve_pending_academic_paper(): void
    {
        $paper = LibraryItem::create([
            'user_id' => $this->member->id,
            'type' => 'academic',
            'academic_degree' => 'tesis',
            'institution' => 'Institut Teknologi Bandung',
            'title' => 'Deteksi Anomali Jaringan dengan Transformer',
            'slug' => 'deteksi-anomali-jaringan',
            'category' => 'Cybersecurity',
            'summary_preview' => 'Abstrak riset anomali jaringan.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/library/{$paper->id}/approve");

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('library_items', [
            'id' => $paper->id,
            'status' => 'approved',
            'rejection_note' => null,
        ]);
    }

    public function test_admin_can_reject_academic_paper_with_evaluation_note(): void
    {
        $paper = LibraryItem::create([
            'user_id' => $this->member->id,
            'type' => 'academic',
            'academic_degree' => 'skripsi',
            'institution' => 'Universitas Indonesia',
            'title' => 'Penerapan OCR pada Manuskrip Kuno',
            'slug' => 'penerapan-ocr-manuskrip',
            'category' => 'Computer Vision',
            'summary_preview' => 'Abstrak riset OCR.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/library/{$paper->id}/reject", [
            'rejection_note' => 'Mohon sertakan link repositori kode dan perjelas metodologi akurasi.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('library_items', [
            'id' => $paper->id,
            'status' => 'rejected',
            'rejection_note' => 'Mohon sertakan link repositori kode dan perjelas metodologi akurasi.',
        ]);
    }

    public function test_admin_can_delete_library_item(): void
    {
        $item = LibraryItem::create([
            'user_id' => $this->admin->id,
            'type' => 'podcast',
            'podcast_source' => 'Dwarkesh Podcast',
            'title' => 'Podcast AI Bersama Andrej Karpathy',
            'slug' => 'podcast-ai-karpathy',
            'category' => 'Deep Learning',
            'summary_preview' => 'Resume obrolan podcast seputar LLM.',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/library/{$item->id}");

        $response->assertSessionHas('success');
        $this->assertSoftDeleted('library_items', [
            'id' => $item->id,
        ]);
    }
}
