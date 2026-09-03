<?php

namespace Tests\Feature;

use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPromptCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_prompts_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Prompt::create([
            'title' => 'Prompt Riset Pasar',
            'slug' => 'prompt-riset-pasar',
            'target_role' => 'Pebisnis & Marketer',
            'target_tool' => 'ChatGPT',
            'prompt_text' => 'Analisis pasar untuk produk [Nama Produk]...',
            'instruction' => 'Gunakan di model GPT-4o',
            'copy_count' => 15,
            'is_featured' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/prompts');

        $response->assertStatus(200);
        $response->assertSee('Prompt Library');
        $response->assertSee('Prompt Riset Pasar');
        $response->assertSee('15x disalin');
    }

    public function test_admin_can_create_prompt_with_variables(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/prompts', [
            'title' => 'Prompt Penyusun RPP Guru',
            'target_role' => 'Pendidik & Guru',
            'target_tool' => 'Claude',
            'prompt_text' => 'Buatkan modul ajar untuk mata pelajaran [Mata Pelajaran] kelas [Kelas]...',
            'instruction' => 'Sertakan rubrik penilaian di prompt lanjutan.',
            'tags' => 'guru, edukasi, rpp',
            'is_featured' => 1,
        ]);

        $response->assertRedirect('/admin/prompts');

        $this->assertDatabaseHas('prompts', [
            'title' => 'Prompt Penyusun RPP Guru',
            'slug' => 'prompt-penyusun-rpp-guru',
            'target_role' => 'Pendidik & Guru',
            'target_tool' => 'Claude',
            'is_featured' => true,
        ]);
    }

    public function test_admin_can_edit_prompt(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $prompt = Prompt::create([
            'title' => 'Prompt Draf Email',
            'slug' => 'prompt-draf-email',
            'target_role' => 'Umum & Produktivitas',
            'target_tool' => 'Gemini',
            'prompt_text' => 'Tulis email formal...',
        ]);

        $response = $this->actingAs($admin)->put("/admin/prompts/{$prompt->id}", [
            'title' => 'Prompt Draf Email Profesional',
            'target_role' => 'Umum & Produktivitas',
            'target_tool' => 'ChatGPT',
            'prompt_text' => 'Tulis email bisnis kepada [Nama Penerima]...',
            'instruction' => 'Gunakan gaya bahasa santun.',
            'is_featured' => 0,
        ]);

        $response->assertRedirect('/admin/prompts');

        $this->assertDatabaseHas('prompts', [
            'id' => $prompt->id,
            'title' => 'Prompt Draf Email Profesional',
            'target_tool' => 'ChatGPT',
        ]);
    }

    public function test_admin_can_delete_prompt(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $prompt = Prompt::create([
            'title' => 'Prompt Usang',
            'slug' => 'prompt-usang',
            'target_role' => 'Developer & IT',
            'target_tool' => 'ChatGPT',
            'prompt_text' => 'Prompt lama...',
        ]);

        $response = $this->actingAs($admin)->delete("/admin/prompts/{$prompt->id}");

        $response->assertRedirect('/admin/prompts');

        $this->assertDatabaseMissing('prompts', [
            'id' => $prompt->id,
        ]);
    }
}
