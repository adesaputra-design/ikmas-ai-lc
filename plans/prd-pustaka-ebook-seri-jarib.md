# PRD: Ebook Interaktif di Pustaka AI — Seri JARIB

> Terkait: `plans/prd-pustaka-ai-library.md` (modul Pustaka AI yang sudah berjalan)
> Edisi pertama: *Closing Maksimal dengan 5 Prinsip JARIB* — Mas Ir. Ghomsony
> Naskah sumber: Google Doc "Naskah v2 — Closing Maksimal dengan 5 Prinsip JARIB (untuk review)"

## Problem Statement

IKMAS punya materi berharga dari wawancara tokoh alumni, misalnya seri JARIB tentang sales dan closing. Saat ini materi itu hanya berupa Google Doc panjang yang:

1. **Tidak nyaman dibaca di HP.** Anggota membuka link dari grup WhatsApp, lalu mendapati dokumen lebih dari 15 halaman tanpa navigasi.
2. **Tidak meninggalkan jejak aksi.** Pembaca selesai membaca tanpa langkah konkret dan tanpa cara mengukur diri.
3. **Tidak menyatu dengan portal IKMAS AI.** Padahal Pustaka AI sudah punya katalog, bookmark, dan share WA, tetapi hanya mendukung satu blok konten per item (`content`) dan tidak punya struktur bab.
4. **Tidak bisa diterbitkan ulang tanpa developer.** Edisi JARIB berikutnya, dengan narasumber lain, harus bisa diinput pengurus sendiri.

## Solution

Tambahkan tipe item baru **`ebook`** ke Pustaka AI, dengan struktur **bab (chapter)** yang bisa diedit di Admin Panel, serta **mode baca** yang dirancang untuk HP:

- Halaman sampul ebook berisi daftar isi, total waktu baca, dan tombol "Mulai Membaca".
- Satu halaman per bab, dengan elemen tetap: waktu baca, **Intisari 30 Detik**, isi Markdown, **kutipan besar yang bisa dibagikan ke WhatsApp**, kotak **Kata Riset**, dan **Amalkan Pekan Ini** (satu aksi dan checklist).
- **Cek Skor JARIB-mu** di akhir ebook berupa self-assessment 10 butir. Hasilnya berupa skor per prinsip dan rekomendasi bab yang perlu dibaca ulang.
- **Akses:** isi bacaan terbuka untuk publik, karena ebook ini juga menjadi etalase IKMAS. Checklist, skor, progres baca, dan bookmark hanya tersimpan untuk member. Di akhir setiap bab, tamu melihat ajakan mendaftar.
- **PDF:** tombol "Simpan sebagai PDF" memakai print stylesheet browser, tanpa library server.

## User Stories

### Pembaca Tamu (belum login)
1. As a Guest, I want to open an ebook link shared in WhatsApp and read the whole book on my phone without logging in, so that I can benefit from the content immediately.
2. As a Guest, I want to see a table of contents with reading time per chapter, so that I can pick a chapter that fits my spare 5 minutes.
3. As a Guest, I want a 30-second summary at the top of each chapter, so that I can grasp the key idea even if I don't read it all.
4. As a Guest, I want to tap a highlighted quote and share it to WhatsApp, so that I can spread an inspiring line to my groups.
5. As a Guest, I want to try the checklist and the JARIB score quiz, and be told that saving requires an account, so that I understand the value of joining.
6. As a Guest, I want to increase/decrease font size and switch light/dark mode, so that reading is comfortable.
7. As a Guest, I want to save the ebook as a PDF via my browser's print dialog, so that I can keep or print it.

### Member & Subscriber Aktif
8. As a Member, I want my checklist ticks per chapter to be saved, so that I can track what I've practiced.
9. As a Member, I want my JARIB score saved with the date, so that I can retake it later and see progress.
10. As a Member, I want the ebook to remember the last chapter I read, so that I can continue where I left off.
11. As a Member, I want to bookmark the ebook like any other library item.

### Admin & Staf (izin `library`)
12. As an Admin, I want to create an item of type "Ebook" with cover, series name ("Seri JARIB"), edition number, and author/narasumber.
13. As an Admin, I want to add, edit, reorder, and delete chapters, each with: title, subtitle, 30-second summary (bullet list), body (Markdown), research box (Markdown), weekly action, and checklist items.
14. As an Admin, I want to define the end-of-book assessment (questions grouped by principle, scale labels, score bands) as structured fields, so that the next edition can use different principles.
15. As an Admin, I want to preview an ebook before publishing (status `pending` stays hidden from public).

## Implementation Decisions

### 1. Data Model
- **`library_items.type`**: tambah nilai `ebook`. Kolom ini berupa enum. Dev memakai SQLite (`.env.example`), sedangkan produksi kemungkinan MySQL, jadi migrasi harus mengubah enum dengan cara yang aman di keduanya (lihat plan).
- **Kolom baru di `library_items`** (nullable, hanya dipakai untuk ebook): `series_name` (string, mis. "Seri JARIB"), `edition_number` (unsigned int), `assessment` (json: daftar pertanyaan, prinsip, label skala, rentang skor).
- **Tabel baru `library_chapters`**: `id`, `library_item_id` (FK, cascade), `position` (unsigned int), `slug`, `title`, `subtitle` (nullable), `summary_points` (json array of string), `body` (longText, Markdown), `research_box` (text Markdown, nullable), `action_title` (text, nullable), `action_checklist` (json array of string), `reading_minutes` (unsigned smallint, dihitung otomatis dari jumlah kata ÷ 200 saat disimpan), timestamps. Unique `(library_item_id, slug)` dan `(library_item_id, position)`.
- **Tabel baru `ebook_progress`**: `id`, `user_id` (FK), `library_item_id` (FK), `last_chapter_id` (nullable FK), `checklist_state` (json: `{chapter_id: [bool,...]}`), `assessment_results` (json array: `[{date, answers, scores}]`), timestamps. Unique `(user_id, library_item_id)`.
- **`reading_time` pada item** diisi otomatis dari total `reading_minutes` bab.

### 2. Rendering Konten
- Isi bab ditulis dalam Markdown dan dirender dengan `Str::markdown()` (`league/commonmark` sudah terpasang), memakai `html_input => 'strip'` dan `allow_unsafe_links => false`.
- **Kutipan besar**: blockquote Markdown yang seluruh isinya **bold** (`> **"…"**`) dirender sebagai *pull quote* dengan tombol "Bagikan ke WA". Blockquote biasa tetap dirender sebagai kutipan biasa. Aturan ini sesuai dengan format Naskah v2, jadi naskah tidak perlu diubah.
- Checklist `- [ ]` di Markdown tidak dipakai di body. Checklist disimpan di kolom `action_checklist` supaya statusnya bisa disimpan.

### 3. Antarmuka
- **Route publik:** `GET /library/{slug}` untuk item bertipe `ebook` menampilkan halaman sampul ebook (view `library.ebook.cover`). `GET /library/{slug}/bab/{chapterSlug}` menampilkan halaman bab (view `library.ebook.chapter`). `GET /library/{slug}/skor` menampilkan Cek Skor.
- **Route member (auth):** `POST /library/{slug}/progress` untuk menyimpan checklist dan bab terakhir, serta `POST /library/{slug}/skor` untuk menyimpan hasil skor.
- **Mode baca:** navbar dan warna portal tetap dipakai (emerald `#006837`, gold `#fdb813`). Area baca memakai latar krem (`#fbf7ee`) atau `--bg-surface` saat mode gelap, font serif untuk isi (mis. *Source Serif 4* / *Lora* via Google Fonts, dengan fallback Georgia), lebar maks ±680px, dan ukuran huruf 3 tingkat (disimpan di `localStorage`, dibungkus `try/catch`). Pembuka bab diberi ornamen geometri SVG halus.
- **Elemen bab:** progress bar baca di atas, chip waktu baca, kotak Intisari (latar emas muda), pull quote, kotak "📊 Kata Riset", kotak "✅ Amalkan Pekan Ini", navigasi Bab sebelumnya/berikutnya, dan CTA daftar untuk tamu.
- **Share kutipan:** tautan `https://wa.me/?text=` berisi kutipan, nama narasumber, dan URL bab.
- **Print:** stylesheet `@media print` menyembunyikan navbar, tombol, dan CTA, memakai satu kolom serif, dan memberi page-break per bab. Tombol "Simpan PDF" di sampul membuka `/library/{slug}/cetak`, yaitu semua bab dalam satu halaman, lalu memanggil `window.print()`.
- **Katalog `/library`:** tambah tab filter "📖 Ebook" dan badge seri/edisi pada kartu.

### 4. Hak Akses
- Untuk tipe `ebook`, **seluruh isi bab terbuka untuk publik**, berbeda dengan `book`/`podcast` yang dikunci. Ini dikendalikan dengan kolom baru `is_public` (boolean, default `false`) agar bisa dipakai tipe lain juga. Ebook JARIB diset `true`.
- Menyimpan progres, checklist, dan skor membutuhkan login (member, staf, admin, atau subscriber aktif, sama seperti aturan `isUnlocked` yang sekarang).
- Admin memakai izin staf `library` yang sudah ada.

### 5. Admin Panel
- Form item: saat tipe `ebook` dipilih, tampilkan field `series_name`, `edition_number`, `is_public`, dan editor assessment.
- Halaman `admin/library/{item}/chapters` berisi tabel bab dengan tombol naik/turun untuk urutan, serta form tambah/edit bab (textarea Markdown dan input baris dinamis untuk intisari dan checklist).
- Tombol "Preview" membuka halaman ebook meski status masih `pending` (khusus admin/staf).

### 6. Konten Awal
- Seeder `JaribEbookSeeder` mengisi edisi #1 dari Naskah v2 setelah disetujui Mas Ghomsony (bagian `[CEK: …]` sudah diselesaikan dan "Catatan untuk Reviewer" dihapus).

## Testing Decisions
- Feature test: tamu bisa membuka sampul, bab, dan skor ebook publik; tamu ditolak (redirect login) saat POST progress/skor; member bisa menyimpan dan membaca ulang progress.
- Feature test: ebook berstatus `pending` 404 untuk publik, tetapi bisa dibuka admin lewat preview.
- Unit test: perhitungan `reading_minutes`; perhitungan skor per prinsip dan pemilihan rentang skor; renderer pull quote (blockquote bold menjadi pull quote, blockquote biasa tetap).
- Test regresi: item `book`/`podcast` tetap terkunci untuk tamu.

## Out of Scope
- Deploy ke Vercel. Keputusan awal ini dibatalkan karena portal berbasis Laravel dan di-hosting di server yang ada.
- Generate PDF di server (dompdf/Browsershot).
- Komentar/diskusi antarpembaca per bab.
- Audio/TTS per bab.
- Gambar kartu kutipan (PNG) otomatis. Fase ini cukup berbagi teks ke WA.

## Further Notes
- Prinsip editorial seri: kutipan narasumber **harus** berasal dari rekaman atau persetujuan narasumber. Draf versi 1 berisi kutipan yang tidak ada di rekaman, dan ini tidak boleh terulang.
- Struktur bab di naskah (Intisari, Kata Riset, Amalkan Pekan Ini) adalah template untuk edisi seri berikutnya.
