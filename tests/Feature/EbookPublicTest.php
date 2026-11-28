<?php

namespace Tests\Feature;

use App\Models\LibraryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EbookPublicTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_private_ebook_remains_hidden_from_member(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $ebook = LibraryItem::create([
            'title' => 'Ebook Privat Uji',
            'slug' => 'ebook-privat-uji',
            'type' => 'ebook',
            'summary_preview' => 'Belum boleh dibaca.',
            'status' => 'approved',
            'is_public' => false,
        ]);
        $ebook->chapters()->create([
            'position' => 1,
            'slug' => 'bab-privat',
            'title' => 'Bab Privat',
            'summary_points' => ['Uji'],
            'body' => 'Isi belum terbit.',
        ]);

        $this->actingAs($member)->get("/library/{$ebook->slug}")->assertNotFound();
        $this->actingAs($member)->get("/library/{$ebook->slug}/bab/bab-privat")->assertNotFound();
    }

    public function test_guest_can_find_public_ebook_and_read_its_chapters(): void
    {
        $ebook = $this->createPublicEbook('ebook-uji-terbit', 'Seri JARIB', 1);
        $ebook->chapters()->create([
            'position' => 1,
            'slug' => 'bab-satu',
            'title' => 'Bab Satu',
            'summary_points' => ['Intisari pertama'],
            'body' => 'Isi **bab pertama** yang lengkap.',
        ]);
        $ebook->chapters()->create([
            'position' => 2,
            'slug' => 'bab-dua',
            'title' => 'Bab Dua',
            'summary_points' => ['Intisari kedua'],
            'body' => 'Isi bab kedua yang lengkap.',
        ]);
        LibraryItem::create([
            'title' => 'Buku Lama',
            'slug' => 'buku-lama',
            'type' => 'book',
            'summary_preview' => 'Cuplikan buku.',
            'content' => 'Isi buku terkunci.',
            'status' => 'approved',
        ]);
        $this->createPublicEbook('ebook-uji-terbit-edisi-dua', 'Seri JARIB', 2);
        $this->createPublicEbook('ebook-seri-lain', 'Seri Lain', 1);

        $this->get('/library?type=ebook')
            ->assertOk()
            ->assertSee('Ebook Uji Terbit')
            ->assertSee('Seri JARIB')
            ->assertDontSee('Buku Lama');

        $this->get('/library?type=ebook&series=Seri%20JARIB&edition=2')
            ->assertOk()
            ->assertSee('Ebook Uji Terbit Edisi 2')
            ->assertDontSee('Ebook Uji Terbit Edisi 1')
            ->assertDontSee('Seri Lain Edisi 1');

        $this->get("/library/{$ebook->slug}")
            ->assertOk()
            ->assertSee('Daftar Isi')
            ->assertSee('Total waktu baca: 2 menit')
            ->assertSee('Bab Satu')
            ->assertSee('Bab Dua')
            ->assertDontSee('Isi bab pertama');

        $this->get("/library/{$ebook->slug}/bab/bab-satu")
            ->assertOk()
            ->assertSee('Intisari pertama')
            ->assertSee('Isi <strong>bab pertama</strong>', false)
            ->assertSee('Bab Dua');

        $this->get("/library/{$ebook->slug}/bab/bab-dua")
            ->assertOk()
            ->assertSee('Bab Satu')
            ->assertSee('Isi bab kedua yang lengkap.');
    }

    public function test_admin_publishes_reviewed_ebook_with_a_chapter(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ebook = LibraryItem::create([
            'title' => 'Draf Siap Terbit',
            'slug' => 'draf-siap-terbit',
            'type' => 'ebook',
            'summary_preview' => 'Naskah uji.',
            'status' => 'pending',
            'is_public' => false,
        ]);
        $ebook->chapters()->create([
            'position' => 1,
            'slug' => 'bab-uji',
            'title' => 'Bab Uji',
            'summary_points' => ['Poin uji'],
            'body' => 'Isi naskah uji.',
        ]);

        $this->actingAs($admin)
            ->post("/admin/library/{$ebook->id}/publish")
            ->assertSessionHasErrors('editorial_approved');
        $this->get("/library/{$ebook->slug}")->assertNotFound();

        $this->actingAs($admin)
            ->post("/admin/library/{$ebook->id}/publish", ['editorial_approved' => '1'])
            ->assertRedirect(route('admin.library.ebook.preview', $ebook));

        $ebook->refresh();
        $this->assertSame('approved', $ebook->status);
        $this->assertTrue($ebook->is_public);
        $this->get("/library/{$ebook->slug}")->assertOk();
    }

    public function test_member_can_save_public_ebook_and_find_it_in_dashboard(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $ebook = $this->createPublicEbook('ebook-bookmark-uji', 'Seri JARIB', 3);

        $this->actingAs($member)
            ->post('/member/bookmarks/toggle', ['type' => 'library', 'id' => $ebook->id])
            ->assertRedirect();

        $this->get("/library/{$ebook->slug}")
            ->assertOk()
            ->assertSee('Hapus dari Bookmark');
        $this->get('/member/dashboard')
            ->assertOk()
            ->assertSee($ebook->title);
    }

    public function test_reader_controls_and_approved_quote_share_are_available_to_guest(): void
    {
        $ebook = $this->createPublicEbook('ebook-kutipan-uji', 'Seri Uji', 1);
        $ebook->update(['author_name' => 'Narasumber Uji']);
        $ebook->chapters()->create([
            'position' => 1,
            'slug' => 'bab-kutipan',
            'title' => 'Bab Kutipan',
            'summary_points' => ['Intisari uji'],
            'body' => '> Blockquote biasa tanpa atribusi.',
            'approved_quotes' => [[
                'text' => 'Kutipan yang disetujui untuk dibagikan.',
                'attribution' => 'Narasumber Uji',
            ]],
        ]);

        $this->get("/library/{$ebook->slug}/bab/bab-kutipan")
            ->assertOk()
            ->assertSee('Ukuran teks bacaan')
            ->assertSee('ebook-font-scale')
            ->assertSee('Kutipan yang disetujui untuk dibagikan.')
            ->assertSee('Narasumber Uji')
            ->assertSee('Blockquote biasa tanpa atribusi.')
            ->assertSee('https://wa.me/?text=')
            ->assertSee(rawurlencode(route('library.ebook.chapter', [$ebook->slug, 'bab-kutipan'])));
    }

    public function test_reader_strips_raw_html_and_unsafe_markdown_links(): void
    {
        $ebook = $this->createPublicEbook('ebook-markdown-aman', 'Seri Uji', 1);
        $ebook->chapters()->create([
            'position' => 1,
            'slug' => 'bab-markdown-aman',
            'title' => 'Bab Markdown Aman',
            'body' => '<script>alert("uji")</script> [tautan berbahaya](javascript:alert(1))',
        ]);

        $this->get("/library/{$ebook->slug}/bab/bab-markdown-aman")
            ->assertOk()
            ->assertDontSee('<script>', false)
            ->assertDontSee('href="javascript:', false);
    }

    private function createPublicEbook(string $slug, string $series, int $edition): LibraryItem
    {
        return LibraryItem::create([
            'title' => "Ebook Uji Terbit Edisi {$edition}".($series === 'Seri Lain' ? ' Seri Lain' : ''),
            'slug' => $slug,
            'type' => 'ebook',
            'category' => $series,
            'summary_preview' => 'Pengantar ebook untuk pembaca.',
            'series_name' => $series,
            'edition_number' => $edition,
            'status' => 'approved',
            'is_public' => true,
        ]);
    }
}
