# PRD: Halaman Tentang Kami (/tentang)

## Problem Statement

IKMAS AI Learning Center belum memiliki halaman yang menjelaskan **siapa kami** dan **mau ke mana kami** secara terstruktur dan terbuka. Informasi Struktur Organisasi dan Rencana Aksi saat ini hanya ada sebagai file Markdown internal, tidak dapat diakses alumni maupun pengurus melalui platform. Akibatnya, alumni yang berkunjung ke platform tidak mendapat gambaran cukup tentang keprofesionalan dan keseriusan komunitas, dan tidak tahu bagaimana cara berkontribusi lebih dalam.

## Solution

Buat halaman `/tentang` yang menampilkan informasi komunitas dalam dua lapisan akses:
- **Guest** — dapat melihat hero intro, diagram struktur organisasi (card grid nama peran), Why statement, dan judul milestone Rencana Aksi. Detail operasional di-truncate dengan fade gradient + CTA login.
- **Member (login)** — dapat melihat semua konten penuh termasuk deskripsi detail tiap peran, tanggung jawab, dan rincian taktis Rencana Aksi. Ada badge kecil "✓ Member" sebagai penanda akses eksklusif.

Konten teks bisa diedit admin via panel admin (hybrid: struktur hardcoded Blade, teks editable di database). Link halaman ini masuk ke dropdown "Komunitas" di navbar.

## User Stories

### Halaman Publik (Guest)
1. Sebagai **guest**, saya ingin membuka halaman `/tentang` dan langsung memahami apa itu IKMAS AI LC dari hero intro singkat, sehingga saya tidak perlu mencari informasi di tempat lain.
2. Sebagai **guest**, saya ingin melihat diagram struktur organisasi dalam bentuk card grid (Community Lead + 4 koordinator), sehingga saya tahu komunitas ini punya kepengurusan yang terstruktur.
3. Sebagai **guest**, saya ingin melihat nama dan judul tiap peran organisasi tanpa detail deskripsi, sehingga saya mendapat gambaran tim tanpa harus login dulu.
4. Sebagai **guest**, saya ingin melihat Why statement komunitas (kutipan Ade Machnun S), sehingga saya memahami motivasi dan nilai di balik komunitas ini.
5. Sebagai **guest**, saya ingin melihat judul milestone Rencana Aksi (Minggu Pertama, Bulan 1, Bulan 2, Bulan 3) dalam vertical timeline, sehingga saya tahu komunitas punya roadmap yang jelas.
6. Sebagai **guest**, saya ingin melihat konten detail yang ter-truncate dengan fade gradient dan CTA "Baca selengkapnya — khusus member", sehingga saya terdorong untuk mendaftar guna membaca lebih lanjut.
7. Sebagai **guest**, saya ingin melihat dua CTA di akhir halaman: "Daftar Alumni" (akun baru) dan "Gabung via Form" (Google Form), sehingga saya punya dua jalur masuk sesuai kesiapan saya.
8. Sebagai **guest**, saya ingin menemukan link "Tentang Kami" di dalam dropdown "Komunitas" di navbar, sehingga halaman ini mudah ditemukan.

### Halaman untuk Member (Login)
9. Sebagai **member yang sudah login**, saya ingin melihat semua konten detail Struktur Organisasi penuh tanpa truncate — termasuk deskripsi peran, tanggung jawab, dan prinsip delegasi.
10. Sebagai **member**, saya ingin melihat semua rincian Rencana Aksi penuh — termasuk langkah H-7 sampai H+1 dan detail per minggu di setiap bulan.
11. Sebagai **member**, saya ingin melihat badge kecil "✓ Member" di header tiap section yang terbuka eksklusif untukku, sehingga saya merasa dihargai atas keanggotaan saya.
12. Sebagai **member**, saya ingin melihat Volunteer Roles (Facilitator, Mentor, Content Contributor, dll) dalam card grid terpisah dengan deskripsi kapan relevannya, sehingga saya tahu ada jalur kontribusi yang bisa saya ambil.

### Admin
13. Sebagai **admin**, saya ingin bisa mengedit teks konten halaman `/tentang` (deskripsi peran, teks rencana aksi) dari panel admin, sehingga saya tidak perlu minta developer untuk update konten.
14. Sebagai **admin**, saya ingin update konten langsung tersimpan dan tampil di halaman `/tentang` tanpa perlu deploy ulang.

## Implementation Decisions

- **Route**: `GET /tentang` → `TentangController@index`, nama route `tentang`.
- **Controller**: `TentangController` baru — ambil konten dari tabel `page_contents` (hybrid data), pass ke view `tentang.blade.php`.
- **Schema baru** — tabel `page_contents` dengan kolom: `id`, `page` (string, misal `tentang`), `key` (string, misal `community_lead_description`), `value` (text), `timestamps`. Tidak ada relasi ke model lain.
- **Model baru**: `PageContent` — simple model dengan scope `forPage($page)` untuk filter per halaman.
- **View**: `resources/views/tentang.blade.php` menggunakan layout `app.blade.php`. Struktur hardcoded (card grid, vertical timeline), teks konten dari variabel yang di-pass controller.
- **Akses berlapis di Blade**: Gunakan `@auth` / `@guest` Blade directive untuk menentukan tampilan truncate vs full. Tidak ada middleware baru — halaman `/tentang` tetap public.
- **Truncate mechanism**: CSS class `.content-locked` dengan `max-height` + `overflow: hidden` + `::after` pseudo-element gradient fade untuk guest. Member tidak dapat class ini.
- **Badge Member**: Badge `<span class="badge badge-emerald">✓ Member</span>` ditampilkan `@auth` di header setiap section restricted.
- **Org chart layout**: Community Lead sebagai card full-width di atas, 4 koordinator (Program, Content, Moderator, Technical) dalam CSS grid 2×2 di bawahnya. Volunteer Roles dalam grid terpisah di bawah.
- **Vertical timeline Rencana Aksi**: CSS timeline dengan pseudo-element garis vertikal. Setiap node: label waktu (H-7, Bulan 1, dst) + judul + konten (ter-truncate untuk guest).
- **Admin panel**: Route baru di grup admin `GET/POST /admin/tentang` → `AdminTentangController`. View form sederhana dengan `<textarea>` per konten key. Simpan ke tabel `page_contents`.
- **Navbar update**: Tambahkan "Tentang Kami" ke dropdown Komunitas di `app.blade.php` (setelah Agenda, sebelum Komunitas Garuda).
- **Google Form URL**: `https://forms.gle/UhyTLF7DyPNAZuir6` — hardcoded di Blade view, bukan dari database.
- **Seeder**: Buat `PageContentSeeder` untuk mengisi konten awal dari dokumen Markdown yang ada, sehingga halaman tidak kosong saat pertama deploy.

## Out of Scope

- Halaman `/tentang` yang fully CMS-driven (seluruh struktur/layout editable dari admin) — struktur hardcoded, hanya teks yang editable.
- Versi mobile yang berbeda secara fundamental dari desktop.
- Animasi masuk/keluar konten saat member login/logout di halaman yang sama.
- Fitur komentar atau diskusi di halaman `/tentang`.
- Multi-bahasa (Bahasa Indonesia saja).
- Gambar/foto pengurus — hanya nama dan peran saja untuk saat ini.

## Further Notes

- Konten awal di-seed dari dua file Markdown existing: `IKMAS_AI_LC_Struktur_Organisasi.md` dan `IKMAS_AI_LC_Rencana_Aksi.md`.
- Google Form URL (`https://forms.gle/UhyTLF7DyPNAZuir6`) adalah link real yang sudah ada di dokumen Rencana Aksi — bisa langsung dipakai.
- Tabel `page_contents` dirancang generik sehingga bisa dipakai untuk halaman lain di masa depan (FAQ, Kebijakan Privasi, dll) tanpa migrasi tambahan.
- Badge "✓ Member" menggunakan class `.badge .badge-emerald` yang sudah ada di design system — tidak perlu style baru.
- Dropdown Komunitas di navbar perlu diupdate bersamaan: Showcase · Agenda · **Tentang Kami** · Komunitas Garuda · WhatsApp Community.
