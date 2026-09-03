<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\LearningMaterial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLearningMaterialTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Category::create([
            'name' => 'AI Basics',
            'slug' => 'basics',
            'type' => 'learning',
        ]);
    }

    public function test_admin_can_view_materials_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::where('slug', 'basics')->first();

        LearningMaterial::create([
            'category_id' => $category->id,
            'title' => 'Panduan Prompt Dasar',
            'slug' => 'panduan-prompt-dasar',
            'pillar' => 'basics',
            'level' => 'beginner',
            'content' => 'Isi materi pengenalan AI...',
            'reading_time' => 5,
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/materi');

        $response->assertStatus(200);
        $response->assertSee('Kelola Materi Belajar');
        $response->assertSee('Panduan Prompt Dasar');
    }

    public function test_admin_can_create_learning_material(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::where('slug', 'basics')->first();

        $response = $this->actingAs($admin)->post('/admin/materi', [
            'category_id' => $category->id,
            'title' => 'Materi Baru dari Admin',
            'pillar' => 'basics',
            'level' => 'beginner',
            'content' => 'Langkah-langkah menyusun prompt terstruktur...',
            'reading_time' => 7,
            'slides_url' => 'https://canva.com/design/sample',
            'video_url' => 'https://youtube.com/watch?v=sample',
            'is_published' => 1,
        ]);

        $response->assertRedirect('/admin/materi');

        $this->assertDatabaseHas('learning_materials', [
            'title' => 'Materi Baru dari Admin',
            'slug' => 'materi-baru-dari-admin',
            'pillar' => 'basics',
            'is_published' => true,
        ]);
    }

    public function test_admin_can_edit_learning_material(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::where('slug', 'basics')->first();

        $material = LearningMaterial::create([
            'category_id' => $category->id,
            'title' => 'Materi Lama',
            'slug' => 'materi-lama',
            'pillar' => 'basics',
            'level' => 'beginner',
            'content' => 'Konten lama',
            'reading_time' => 5,
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)->put("/admin/materi/{$material->id}", [
            'category_id' => $category->id,
            'title' => 'Materi Sudah Diupdate',
            'pillar' => 'basics',
            'level' => 'explorer',
            'content' => 'Konten yang telah direvisi',
            'reading_time' => 10,
            'is_published' => 1,
        ]);

        $response->assertRedirect('/admin/materi');

        $this->assertDatabaseHas('learning_materials', [
            'id' => $material->id,
            'title' => 'Materi Sudah Diupdate',
            'level' => 'explorer',
        ]);
    }

    public function test_admin_can_delete_learning_material(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::where('slug', 'basics')->first();

        $material = LearningMaterial::create([
            'category_id' => $category->id,
            'title' => 'Materi Untuk Dihapus',
            'slug' => 'materi-untuk-dihapus',
            'pillar' => 'basics',
            'level' => 'beginner',
            'content' => 'Konten hapus',
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/materi/{$material->id}");

        $response->assertRedirect('/admin/materi');

        $this->assertDatabaseMissing('learning_materials', [
            'id' => $material->id,
        ]);
    }

    public function test_draft_material_is_hidden_from_public_catalog(): void
    {
        $category = Category::where('slug', 'basics')->first();

        $published = LearningMaterial::create([
            'category_id' => $category->id,
            'title' => 'Materi Publik Tayang',
            'slug' => 'materi-publik-tayang',
            'pillar' => 'basics',
            'level' => 'beginner',
            'content' => 'Konten publik',
            'is_published' => true,
        ]);

        $draft = LearningMaterial::create([
            'category_id' => $category->id,
            'title' => 'Materi Draf Rahasia Pengurus',
            'slug' => 'materi-draf-rahasia-pengurus',
            'pillar' => 'basics',
            'level' => 'beginner',
            'content' => 'Konten draf',
            'is_published' => false,
        ]);

        $response = $this->get('/materi');

        $response->assertStatus(200);
        $response->assertSee('Materi Publik Tayang');
        $response->assertDontSee('Materi Draf Rahasia Pengurus');
    }
}
