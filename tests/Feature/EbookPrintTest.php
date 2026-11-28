<?php

namespace Tests\Feature;

use App\Models\LibraryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EbookPrintTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_a_print_document_with_cover_and_all_chapters_in_order(): void
    {
        $ebook = $this->createPublicEbook('ebook-cetak-uji');
        $ebook->chapters()->create([
            'position' => 1,
            'slug' => 'bab-satu',
            'title' => 'Bab Satu Cetak',
            'summary_points' => ['Intisari cetak satu'],
            'body' => 'Isi bab pertama untuk dicetak.',
        ]);
        $ebook->chapters()->create([
            'position' => 2,
            'slug' => 'bab-dua',
            'title' => 'Bab Dua Cetak',
            'summary_points' => ['Intisari cetak dua'],
            'body' => 'Isi bab kedua untuk dicetak.',
        ]);

        $this->get("/library/{$ebook->slug}/cetak")
            ->assertOk()
            ->assertSeeInOrder([
                'Ebook Cetak Uji',
                'Bab Satu Cetak',
                'Isi bab pertama untuk dicetak.',
                'Bab Dua Cetak',
                'Isi bab kedua untuk dicetak.',
            ])
            ->assertSee('Cetak / Simpan PDF')
            ->assertSee('window.print()')
            ->assertDontSee('ebook-chapter-nav')
            ->assertDontSee('Mulai Membaca');

        $this->get("/library/{$ebook->slug}")
            ->assertOk()
            ->assertSee(route('library.ebook.print', $ebook->slug));
    }

    public function test_private_or_unapproved_ebooks_cannot_be_printed(): void
    {
        $privateEbook = $this->createPublicEbook('ebook-cetak-privat');
        $privateEbook->update(['is_public' => false]);
        $pendingEbook = $this->createPublicEbook('ebook-cetak-pending');
        $pendingEbook->update(['status' => 'pending']);

        $this->get("/library/{$privateEbook->slug}/cetak")->assertNotFound();
        $this->get("/library/{$pendingEbook->slug}/cetak")->assertNotFound();
    }

    public function test_print_stylesheet_sets_page_breaks_and_hides_screen_controls(): void
    {
        $stylesheet = file_get_contents(public_path('css/ebook.css'));

        $this->assertIsString($stylesheet);
        $this->assertStringContainsString('@media print', $stylesheet);
        $this->assertStringContainsString('.ebook-print-toolbar', $stylesheet);
        $this->assertStringContainsString('display: none !important', $stylesheet);
        $this->assertStringContainsString('break-before: page', $stylesheet);
    }

    private function createPublicEbook(string $slug): LibraryItem
    {
        return LibraryItem::create([
            'title' => ucwords(str_replace('-', ' ', $slug)),
            'slug' => $slug,
            'type' => 'ebook',
            'summary_preview' => 'Sinopsis singkat untuk cetak.',
            'author_name' => 'Penulis Uji',
            'status' => 'approved',
            'is_public' => true,
        ]);
    }
}
