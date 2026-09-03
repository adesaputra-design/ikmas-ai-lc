# Plan: Halaman Tentang Kami (/tentang)

> Source PRD: `plans/prd-halaman-tentang.md`

## Architectural Decisions

Keputusan durable yang berlaku lintas semua phase:

- **Route**: `GET /tentang` → `TentangController@index`, nama `tentang`. Ditambah ke grup admin: `GET /admin/tentang` (form edit) + `POST /admin/tentang` (simpan).
- **Schema baru**: Tabel `page_contents` — kolom `id`, `page` (varchar), `key` (varchar), `value` (longtext), `timestamps`. Index composite pada `(page, key)` untuk query efisien.
- **Key models**: `PageContent` (baru) — model simple dengan scope `forPage()`.
- **Akses**: Halaman `/tentang` public (tidak perlu middleware auth). Blade directive `@auth`/`@guest` mengontrol tampilan penuh vs truncate. Admin route menggunakan middleware existing `['auth', 'admin']`.
- **CSS pattern**: Class `.content-locked` (guest-only) dengan `max-height` + gradient fade via `::after` pseudo-element. Tidak ada JavaScript untuk show/hide — murni CSS + Blade conditional.
- **Navbar**: Dropdown Komunitas di `app.blade.php` diupdate menambahkan "Tentang Kami" (urutan: Showcase → Agenda → Tentang Kami → Komunitas Garuda → WhatsApp).

---

## Phase 1: Fondasi Data & Route

**User stories**: 13, 14

### What to build

Buat seluruh lapisan data dan routing agar halaman `/tentang` punya backbone sebelum UI dibangun:

1. **Migrasi** tabel `page_contents` dengan kolom `page`, `key`, `value`.
2. **Model** `PageContent` dengan scope `forPage($page)` dan helper static `getValue($page, $key, $default)`.
3. **Seeder** `PageContentSeeder` — isi konten awal dari dokumen Markdown (semua deskripsi peran, tanggung jawab, teks rencana aksi) sebagai key-value pairs untuk page `tentang`.
4. **`TentangController`** — query semua konten untuk page `tentang`, susun ke array asosiatif, pass ke view.
5. **`AdminTentangController`** — `index()` tampilkan form edit semua key, `update()` simpan perubahan ke `page_contents`.
6. **Routes**: tambahkan `GET /tentang` di public routes dan `GET/POST /admin/tentang` di grup admin.
7. **Admin view** `admin/tentang/edit.blade.php` — form sederhana dengan `<textarea>` per key konten, menggunakan layout admin yang ada.

### Acceptance criteria

- [ ] Migrasi `page_contents` berhasil dijalankan (`php artisan migrate`)
- [ ] Seeder berhasil mengisi konten awal semua key untuk page `tentang` (`php artisan db:seed --class=PageContentSeeder`)
- [ ] `GET /tentang` dapat diakses tanpa login (tidak redirect ke login)
- [ ] `GET /admin/tentang` hanya bisa diakses admin (redirect ke login jika guest)
- [ ] Admin bisa submit form edit dan perubahan tersimpan ke database
- [ ] `PageContent::getValue('tentang', 'community_lead_description', '')` mengembalikan nilai yang benar
- [ ] Tidak ada error N+1 — satu query untuk semua konten satu halaman

---

## Phase 2: Halaman Publik — Hero, Struktur Organisasi (Guest View)

**User stories**: 1, 2, 3, 8

### What to build

Bangun tampilan halaman `/tentang` untuk guest — bagian Hero intro dan section Struktur Organisasi:

**Hero section**: Judul "Tentang IKMAS AI Learning Center", paragraf intro singkat (konten dari `page_contents`), visual/icon komunitas.

**Struktur Organisasi — card grid**:
- Community Lead: card full-width dengan nama peran + tagline singkat (visible untuk semua)
- 4 Koordinator (Program, Content, Moderator, Technical): CSS grid 2×2, setiap card tampilkan nama peran + 1 kalimat deskripsi (visible untuk semua)
- Detail deskripsi panjang + tanggung jawab: `.content-locked` untuk guest (truncate + fade gradient + CTA inline "Masuk untuk baca selengkapnya")
- Member: konten penuh + badge `✓ Member` di header card

**Volunteer Roles**: Tabel/grid sederhana tampilkan 5 peran + kolom "Kapan relevan" — visible untuk semua (info ini cukup umum untuk publik).

**Navbar update**: Tambahkan "Tentang Kami" ke dropdown Komunitas di `app.blade.php`.

Tambahkan CSS baru: `.content-locked`, `.tentang-org-grid`, `.tentang-role-card` ke `app.css`.

### Acceptance criteria

- [ ] `/tentang` dapat diakses dan render tanpa error di browser
- [ ] Hero section tampil dengan intro teks dari `page_contents`
- [ ] Card Community Lead muncul full-width di atas 4 card koordinator
- [ ] 4 card koordinator tampil dalam grid 2×2 di desktop, stack di mobile
- [ ] Guest melihat nama peran + deskripsi singkat, detail ter-truncate dengan fade gradient
- [ ] Teks CTA "Masuk untuk baca selengkapnya" muncul di bawah konten ter-truncate (untuk guest)
- [ ] Member melihat konten penuh + badge "✓ Member" di header section restricted
- [ ] Tabel Volunteer Roles tampil lengkap untuk semua user
- [ ] Link "Tentang Kami" muncul di dropdown Komunitas navbar dan mengarah ke `/tentang`
- [ ] Active state navbar "Komunitas" muncul saat berada di `/tentang`

---

## Phase 3: Rencana Aksi — Vertical Timeline & CTA

**User stories**: 4, 5, 6, 7, 9, 10, 11, 12

### What to build

Lengkapi halaman `/tentang` dengan section Rencana Aksi dan CTA penutup:

**Why Statement**: Kutipan blockquote dari Ade Machnun S (konten dari `page_contents`) — visible untuk semua. Tampil sebelum timeline sebagai konteks motivasi.

**Vertical Timeline Rencana Aksi**:
- Garis vertikal CSS dengan node per milestone
- Node Minggu Pertama: 7 sub-node (H-7 sampai H+1)
  - Label waktu + judul visible untuk semua
  - Detail taktis per node: `.content-locked` untuk guest
- Node Bulan 1, 2, 3: masing-masing tampilkan nama fase + tema
  - Detail per minggu di dalam setiap bulan: `.content-locked` untuk guest
- Member: semua node terbuka penuh + badge `✓ Member` di header section timeline

**CTA Section** (akhir halaman — visible untuk semua):
- Tombol primary "Daftar Alumni" → `/register`
- Link/tombol secondary "Gabung via Form" → `https://forms.gle/UhyTLF7DyPNAZuir6` (tab baru)
- Untuk member yang sudah login: ganti CTA dengan "Buka Area Member" → `/member/dashboard`

**Kutipan penutup**: *"Lebih baik 100 anggota dengan 30 aktif daripada 1.000 tanpa aktivitas."* — tampil di atas CTA.

Tambahkan CSS: `.tentang-timeline`, `.timeline-node`, `.timeline-label` ke `app.css`.

### Acceptance criteria

- [ ] Why statement tampil sebagai blockquote di atas timeline
- [ ] Vertical timeline tampil dengan garis vertikal dan node-node milestone
- [ ] Judul tiap node (H-7, H-6...Bulan 1, 2, 3) visible untuk guest
- [ ] Detail taktis tiap node ter-truncate dengan fade + CTA login untuk guest
- [ ] Member melihat semua detail timeline penuh + badge "✓ Member" di header section
- [ ] CTA "Daftar Alumni" mengarah ke `/register`
- [ ] CTA "Gabung via Form" membuka Google Form di tab baru
- [ ] Member yang sudah login melihat "Buka Area Member" (bukan "Daftar Alumni")
- [ ] Kutipan penutup tampil di atas CTA
- [ ] Timeline responsif di mobile (node stack vertikal dengan garis di kiri)
- [ ] Halaman `/tentang` end-to-end complete: Hero → Struktur Org → Rencana Aksi → CTA

---

## Phase 4: Admin Panel Edit Konten

**User stories**: 13, 14

### What to build

Tambahkan entry point di admin dashboard ke halaman edit konten `/tentang`. Lengkapi admin view yang sudah dibuat di Phase 1 dengan UX yang layak:

- Link "Edit Halaman Tentang" di sidebar/menu admin
- Form edit dikelompokkan per section (Intro, Struktur Org, Rencana Aksi) dengan label human-readable per field
- Textarea dengan `rows` yang sesuai panjang konten
- Tombol "Simpan Perubahan" dengan flash success message setelah berhasil
- Validasi: semua field wajib tidak boleh kosong

### Acceptance criteria

- [ ] Ada link ke `/admin/tentang` dari halaman admin dashboard atau sidebar
- [ ] Form menampilkan semua key konten dikelompokkan per section
- [ ] Label tiap field human-readable (bukan raw key seperti `community_lead_description`)
- [ ] Submit form menyimpan semua perubahan sekaligus ke `page_contents`
- [ ] Flash message "Konten berhasil diperbarui" muncul setelah simpan
- [ ] Perubahan yang disimpan langsung terlihat saat membuka `/tentang` di browser
- [ ] Validasi: field tidak boleh kosong (server-side)
- [ ] Halaman edit tidak bisa diakses non-admin (redirect ke login)
