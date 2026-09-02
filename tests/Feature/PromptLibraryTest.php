<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Prompt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromptLibraryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create([
            'name' => 'Copywriting',
            'slug' => 'copywriting',
            'type' => 'prompt',
            'color' => '#2563eb',
        ]);

        Prompt::create([
            'category_id' => $category->id,
            'title' => 'Formula Copywriting Headline Penjualan',
            'slug' => 'formula-copywriting-headline-penjualan',
            'target_role' => 'Pebisnis / Marketer',
            'target_tool' => 'ChatGPT & Claude',
            'prompt_text' => 'Bertindaklah sebagai copywriter direct response kelas dunia. Tuliskan 5 opsi headline menarik untuk produk [Nama Produk] dengan target pasar [Target Audiens].',
            'instruction' => 'Ganti [Nama Produk] dan [Target Audiens] dengan spesifikasi bisnismu.',
            'tags' => 'headline, copywriting, bisnis',
            'is_featured' => true,
        ]);
    }

    public function test_prompt_library_catalog_can_be_viewed(): void
    {
        $response = $this->get('/prompts');

        $response->assertStatus(200);
        $response->assertSee('Prompt Library Interaktif');
        $response->assertSee('Formula Copywriting Headline Penjualan');
        $response->assertSee('Salin Prompt');
    }

    public function test_prompts_can_be_filtered_by_role(): void
    {
        $response = $this->get('/prompts?role=Pebisnis');

        $response->assertStatus(200);
        $response->assertSee('Formula Copywriting Headline Penjualan');

        $responseOther = $this->get('/prompts?role=Programmer');
        $responseOther->assertStatus(200);
        $responseOther->assertDontSee('Formula Copywriting Headline Penjualan');
    }

    public function test_prompts_can_be_filtered_by_tool(): void
    {
        $response = $this->get('/prompts?tool=ChatGPT');

        $response->assertStatus(200);
        $response->assertSee('Formula Copywriting Headline Penjualan');

        $responseOther = $this->get('/prompts?tool=Midjourney');
        $responseOther->assertStatus(200);
        $responseOther->assertDontSee('Formula Copywriting Headline Penjualan');
    }
}
