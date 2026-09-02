<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Prompt;
use App\Models\Showcase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberAndShowcaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_alumni_can_register_with_whatsapp_and_alumni_year(): void
    {
        $response = $this->post('/register', [
            'name' => 'Ahmad Alumni',
            'email' => 'ahmad@alumni.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'whatsapp_number' => '081234567890',
            'alumni_year' => '2015',
        ]);

        $response->assertRedirect('/member/dashboard');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'ahmad@alumni.test',
            'whatsapp_number' => '081234567890',
            'alumni_year' => '2015',
            'role' => 'member',
        ]);
    }

    public function test_member_can_submit_showcase_which_defaults_to_pending(): void
    {
        $user = User::factory()->create([
            'role' => 'member',
            'alumni_year' => '2012',
            'whatsapp_number' => '081987654321',
        ]);

        $response = $this->actingAs($user)->post('/member/showcase', [
            'title' => 'Bot WA Layanan Pelanggan Toko Alumni',
            'description' => 'Bot automasi menggunakan OpenAI API dan Node.js untuk menjawab pertanyaan katalog produk.',
            'tools_used' => 'ChatGPT, WhatsApp Business API',
            'project_url' => 'https://tokoalumni.test',
            'impact_story' => 'Meningkatkan kecepatan respons chat dari 30 menit menjadi instan 5 detik.',
        ]);

        $response->assertRedirect('/member/dashboard');

        $this->assertDatabaseHas('showcases', [
            'user_id' => $user->id,
            'title' => 'Bot WA Layanan Pelanggan Toko Alumni',
            'status' => 'pending',
        ]);
    }

    public function test_public_showcase_only_displays_approved_works(): void
    {
        $user = User::factory()->create([
            'name' => 'Rahmat Assalaam',
            'alumni_year' => '2010',
        ]);

        $approvedShowcase = Showcase::create([
            'user_id' => $user->id,
            'title' => 'Aplikasi Ringkasan Kitab Berbasis LLM',
            'slug' => 'aplikasi-ringkasan-kitab-berbasis-llm',
            'description' => 'Aplikasi web mini untuk mencari faedah dan ringkasan bab kitab.',
            'tools_used' => 'Claude, Python',
            'status' => 'approved',
        ]);

        $pendingShowcase = Showcase::create([
            'user_id' => $user->id,
            'title' => 'Karya Baru yang Belum Disetujui',
            'slug' => 'karya-baru-yang-belum-disetujui',
            'description' => 'Masih dalam proses kurasi admin.',
            'tools_used' => 'ChatGPT',
            'status' => 'pending',
        ]);

        $response = $this->get('/showcase');

        $response->assertStatus(200);
        $response->assertSee('Aplikasi Ringkasan Kitab Berbasis LLM');
        $response->assertSee('Rahmat Assalaam');
        $response->assertDontSee('Karya Baru yang Belum Disetujui');
    }

    public function test_member_can_bookmark_and_toggle_prompt(): void
    {
        $user = User::factory()->create(['role' => 'member']);
        $prompt = Prompt::create([
            'title' => 'Prompt Copywriting Hebat',
            'slug' => 'prompt-copywriting-hebat',
            'target_role' => 'Marketer',
            'target_tool' => 'ChatGPT',
            'prompt_text' => 'Tulis copy menarik...',
        ]);

        $response = $this->actingAs($user)->postJson('/member/bookmarks/toggle', [
            'id' => $prompt->id,
            'type' => 'prompt',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['bookmarked' => true]);

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            'bookmarkable_id' => $prompt->id,
            'bookmarkable_type' => 'prompt',
        ]);
    }
}
