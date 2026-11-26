# Plan: Ebook Interaktif di Pustaka AI — Seri JARIB

> Source PRD: `plans/prd-pustaka-ebook-seri-jarib.md`

## Architectural Decisions

Keputusan yang berlaku untuk semua phase:

- **Routes** (urutan penting: route bab didaftarkan **sebelum** `/library/{slug}` agar tidak tertangkap wildcard):
  - `GET /library/{slug}/bab/{chapter}` → `EbookController@chapter`, nama `library.ebook.chapter`
  - `GET /library/{slug}/skor` → `EbookController@assessment`, nama `library.ebook.assessment`
  - `GET /library/{slug}/cetak` → `EbookController@print`, nama `library.ebook.print`
  - `POST /library/{slug}/progress` dan `POST /library/{slug}/skor` di grup `auth`
  - `GET /library/{slug}` tetap memakai `LibraryController@show`, lalu mendelegasikan ke `EbookController@cover` jika `type === 'ebook'`
  - Admin: `Route::resource('library.chapters', AdminLibraryChapterController::class)` + `POST .../chapters/{chapter}/move` di grup `permission:library`
- **Schema:** `library_items` mendapat nilai enum `ebook` dan kolom `series_name`, `edition_number`, `is_public`, `assessment` (json). Tabel baru `library_chapters` dan `ebook_progress` (detail kolom di PRD).
- **Enum migration:** pakai `$table->string('type', 20)->change()` (didukung Laravel 11+ di SQLite dan MySQL), lalu validasi nilai di level aplikasi (`in:book,podcast,academic,ebook`) dan konstanta `LibraryItem::TYPES`. Cara ini lebih aman daripada mempertahankan enum yang harus diubah lagi setiap ada tipe baru.
- **Key models:** `LibraryItem` (`chapters()` hasMany terurut `position`, `isEbook()`, `isPublic()`), `LibraryChapter` (`renderedBody()`, `renderedResearch()`, hook `saving` untuk `reading_minutes`), `EbookProgress`.
- **Rendering:** service `App\Support\EbookMarkdown` membungkus `Str::markdown()` (`html_input: strip`, `allow_unsafe_links: false`) dan mengubah blockquote yang seluruh isinya `<strong>` menjadi komponen pull quote.
- **Akses:** helper `LibraryItem::isReadableBy(?User)` mengembalikan `true` jika `is_public`, atau memakai aturan `isUnlocked` yang ada. Akses ini juga dipakai `LibraryController@show` agar perilaku tipe lama tidak berubah.
- **Frontend:** Blade + CSS di `resources/css/ebook.css` (di-import lewat Vite) + JS kecil tanpa framework (`resources/js/ebook.js`) untuk ukuran huruf, progress bar, checklist, dan skor. State tamu disimpan di memori/`localStorage` (try/catch). State member disimpan ke server lewat `fetch` + CSRF.

---

## Phase 1: Fondasi Data & Ebook Minimal yang Bisa Dibaca

**User stories:** 1, 2, 12 (sebagian)

### What to build
Migrasi (tipe `ebook`, kolom item baru, `library_chapters`), model, dan seeder edisi #1 dari Naskah v2. Halaman sampul berisi daftar isi dan halaman bab yang merender Markdown polos dengan navigasi prev/next. Belum ada styling khusus dan belum ada interaktivitas.

**Prasyarat (bug yang sudah ada):** `LibraryController@show` dan `LibraryItem::bookmarks()` memakai kolom `bookmarks.library_item_id`, padahal tabel `bookmarks` bersifat polimorfik (`bookmarkable_id`/`bookmarkable_type`) dan tidak punya kolom itu. Akibatnya, member yang login kemungkinan mendapat error SQL saat membuka item pustaka mana pun. Perbaiki dulu dengan relasi `morphMany` dan query `bookmarkable_*`, beserta test-nya, sebelum menambah fitur ebook.

### Acceptance criteria
- [ ] Member yang login bisa membuka detail item pustaka dan mem-bookmark-nya tanpa error (test regresi bug bookmark).
- [ ] `php artisan migrate` berjalan di SQLite. Data `book`/`podcast`/`academic` yang ada tetap utuh.
- [ ] `JaribEbookSeeder` membuat 1 item `ebook` (`is_public = true`, kategori "Seri JARIB") dengan 9 bab berurutan.
- [ ] `/library/closing-maksimal-5-prinsip-jarib` menampilkan sampul dan daftar isi tanpa login.
- [ ] `/library/{slug}/bab/{chapter}` menampilkan isi bab dan link bab sebelum/sesudah.
- [ ] Item `book` tetap terkunci untuk tamu (test regresi lulus).
- [ ] `reading_minutes` terisi otomatis dan total muncul di sampul.

---

## Phase 2: Mode Baca Mobile-First

**User stories:** 3, 4, 6

### What to build
Desain area baca (krem/serif, emerald/gold, ornamen pembuka bab), kotak Intisari, pull quote dengan tombol "Bagikan ke WA", kotak Kata Riset, progress bar baca, pengatur ukuran huruf, dan dukungan mode gelap portal. CTA daftar untuk tamu di akhir bab. Tab "📖 Ebook" di katalog.

### Acceptance criteria
- [ ] Tampilan rapi di layar 360px dan 1280px (cek screenshot keduanya).
- [ ] Blockquote bold dirender sebagai pull quote. Tombol WA membuka `wa.me` berisi kutipan, nama narasumber, dan URL bab.
- [ ] Ukuran huruf bertahan setelah reload. Halaman tetap berfungsi bila `localStorage` diblokir.
- [ ] Kontras teks memenuhi WCAG AA di mode terang dan gelap.
- [ ] Katalog memfilter tipe `ebook` dan menampilkan badge "Seri JARIB · Edisi #1".

---

## Phase 3: Checklist, Progres, dan Cek Skor JARIB

**User stories:** 5, 8, 9, 10, 11

### What to build
Tabel `ebook_progress`. Kotak "Amalkan Pekan Ini" dengan checklist interaktif. Halaman Cek Skor (pertanyaan dari `assessment` json, skala 1–4, skor per prinsip, rentang skor, dan link ke bab yang direkomendasikan). Tamu bisa mencoba tetapi hasilnya tidak disimpan, dengan pesan ajakan login. Member menyimpan dan melihat riwayat skor. Sampul menampilkan "Lanjutkan dari Bab X" untuk member.

### Acceptance criteria
- [ ] Tamu bisa mencentang checklist dan menghitung skor. Muncul pesan "Masuk untuk menyimpan" dan tidak ada request POST.
- [ ] Member: centang tersimpan, reload mempertahankan status, dan `last_chapter_id` diperbarui saat membuka bab.
- [ ] Skor per prinsip dihitung benar (unit test) dan riwayat menampilkan tanggal.
- [ ] POST tanpa login diarahkan ke login. POST ke ebook lain tidak bisa mengubah progres user lain.

---

## Phase 4: Admin Panel Bab & Assessment

**User stories:** 12, 13, 14, 15

### What to build
Field ebook di form item admin. CRUD bab dengan pengurutan naik/turun, input baris dinamis untuk intisari dan checklist, dan editor assessment (pertanyaan per prinsip dan rentang skor). Tombol preview untuk item berstatus `pending`.

### Acceptance criteria
- [ ] Staf berizin `library` bisa menambah, mengedit, mengurutkan, dan menghapus bab. Staf tanpa izin mendapat 403.
- [ ] Ebook baru berstatus `pending` 404 untuk publik, tetapi terbuka lewat preview admin.
- [ ] Mengubah assessment langsung tercermin di halaman skor.

---

## Phase 5: Cetak/PDF & Penyelesaian

**User stories:** 7

### What to build
Halaman `/cetak` berisi semua bab dalam satu dokumen dengan stylesheet `@media print` (tanpa navbar/tombol, page-break per bab, sampul sederhana). Meta Open Graph per bab (judul, intisari) supaya pratinjau link di WhatsApp menarik. Pemeriksaan akhir di HP.

### Acceptance criteria
- [ ] "Simpan sebagai PDF" di Chrome Android dan desktop menghasilkan PDF rapi: satu bab per halaman baru, tanpa elemen UI.
- [ ] Link bab yang dibagikan ke WA menampilkan judul dan intisari bab.
- [ ] Seluruh test suite lulus (`php artisan test`).
