<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLandingTest extends TestCase
{
    use RefreshDatabase;
    public function test_landing_page_can_be_rendered(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('IKMAS AI Learning Center');
        $response->assertSee('Belajar AI. Berbagi. Bertumbuh Bersama.');
        $response->assertSee('Garuda');
    }

    public function test_custom_404_page_is_rendered(): void
    {
        $response = $this->get('/halaman-yang-tidak-ada-12345');

        $response->assertStatus(404);
        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('Kembali ke Beranda');
    }
}
