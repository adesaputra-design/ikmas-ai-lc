<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\LearningMaterial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningMaterialTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create([
            'name' => 'Produktivitas',
            'slug' => 'produktivitas',
            'type' => 'learning',
            'color' => '#2563eb',
        ]);

        LearningMaterial::create([
            'category_id' => $category->id,
            'title' => 'AI untuk Produktivitas Sehari-hari',
            'slug' => 'ai-untuk-produktivitas-sehari-hari',
            'level' => 'beginner',
            'pillar' => 'productivity',
            'summary' => 'Panduan pengenalan AI praktis untuk mempercepat tugas harian tanpa istilah rumit.',
            'content' => '<h2>Apa itu AI untuk Produktivitas?</h2><p>AI adalah asisten pribadi yang siap membantu kita merangkum dokumen dan menulis draf.</p>',
            'reading_minutes' => 7,
            'video_url' => 'https://youtube.com',
            'is_published' => true,
        ]);
    }

    public function test_learning_materials_catalog_can_be_viewed(): void
    {
        $response = $this->get('/materi');

        $response->assertStatus(200);
        $response->assertSee('Repositori Materi Belajar');
        $response->assertSee('AI untuk Produktivitas Sehari-hari');
        $response->assertSee('Beginner');
    }

    public function test_learning_materials_can_be_filtered_by_level(): void
    {
        $response = $this->get('/materi?level=beginner');

        $response->assertStatus(200);
        $response->assertSee('AI untuk Produktivitas Sehari-hari');

        $responseExplorer = $this->get('/materi?level=practitioner');
        $responseExplorer->assertStatus(200);
        $responseExplorer->assertDontSee('AI untuk Produktivitas Sehari-hari');
    }

    public function test_single_learning_material_detail_page_can_be_rendered(): void
    {
        $response = $this->get('/materi/ai-untuk-produktivitas-sehari-hari');

        $response->assertStatus(200);
        $response->assertSee('AI untuk Produktivitas Sehari-hari');
        $response->assertSee('Apa itu AI untuk Produktivitas?');
        $response->assertSee('7 Menit Baca');
    }
}
