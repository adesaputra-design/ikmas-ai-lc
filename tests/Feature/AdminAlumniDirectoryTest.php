<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAlumniDirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_alumni_directory(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $alumni = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@alumni.test',
            'whatsapp_number' => '08123456789',
            'alumni_year' => '2015',
            'role' => 'member',
        ]);

        $response = $this->actingAs($admin)->get('/admin/alumni');

        $response->assertStatus(200);
        $response->assertSee('Direktori Member Alumni');
        $response->assertSee('Budi Santoso');
        $response->assertSee('08123456789');
        $response->assertSee('2015');
        $response->assertSee('Unduh CSV');
    }

    public function test_admin_can_filter_alumni_by_year(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        User::factory()->create([
            'name' => 'Alumni 2012',
            'alumni_year' => '2012',
        ]);

        User::factory()->create([
            'name' => 'Alumni 2020',
            'alumni_year' => '2020',
        ]);

        $response = $this->actingAs($admin)->get('/admin/alumni?alumni_year=2012');

        $response->assertStatus(200);
        $response->assertSee('Alumni 2012');
        $response->assertDontSee('Alumni 2020');
    }

    public function test_admin_can_export_alumni_csv(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        User::factory()->create([
            'name' => 'Zulham Efendi',
            'email' => 'zulham@alumni.test',
            'whatsapp_number' => '08555444333',
            'alumni_year' => '2018',
        ]);

        $response = $this->actingAs($admin)->get('/admin/alumni/export');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        
        $content = $response->streamedContent();
        $this->assertStringContainsString('Nama Lengkap', $content);
        $this->assertStringContainsString('Zulham Efendi', $content);
        $this->assertStringContainsString('zulham@alumni.test', $content);
        $this->assertStringContainsString('08555444333', $content);
        $this->assertStringContainsString('2018', $content);
    }
}
