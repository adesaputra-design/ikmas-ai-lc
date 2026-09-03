<?php

namespace Tests\Feature;

use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLayoutAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sidebar_layout_renders_navigation_links(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dasbor Utama');
        $response->assertSee('Kelola Materi');
        $response->assertSee('Prompt Library');
        $response->assertSee('Agenda Kegiatan');
        $response->assertSee('Kurasi Showcase');
        $response->assertSee('Member Alumni');
        $response->assertSee('Lihat Web Publik');
    }

    public function test_dashboard_displays_quick_actions_and_top_copied_prompts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $topPrompt = Prompt::create([
            'title' => 'Prompt Viral Copywriting',
            'slug' => 'prompt-viral-copywriting',
            'target_role' => 'Marketer',
            'target_tool' => 'ChatGPT',
            'prompt_text' => 'Tulis copy viral...',
            'copy_count' => 88,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Aksi Cepat Pengurus');
        $response->assertSee('Prompt Paling Populer');
        $response->assertSee('Prompt Viral Copywriting');
        $response->assertSee('88x disalin');
    }
}
