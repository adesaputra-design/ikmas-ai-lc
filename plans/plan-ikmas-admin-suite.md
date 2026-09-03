# Plan: IKMAS AI Admin Suite & Management Dashboard

> Source PRD: [plans/prd-ikmas-admin-suite.md](file:///d:/Ade%20Zone/3.%20AK%20Studio%20%232/IKMAS%20AILC/plans/prd-ikmas-admin-suite.md)

## Architectural decisions

Durable decisions that apply across all phases:

- **Layout Structure**: Dedicated Admin Sidebar Layout (`resources/views/layouts/admin.blade.php`), terpisah dari layout publik, dilengkapi bilah navigasi samping kiri, top bar dengan switch tema & pintasan ke web publik, serta area konten utama.
- **Route Prefixes & Naming**:
  - `/admin/dashboard` (`admin.dashboard`)
  - `/admin/materi` (`admin.materi.*`)
  - `/admin/prompts` (`admin.prompts.*`)
  - `/admin/agenda` (`admin.agenda.*`)
  - `/admin/curation` (`admin.curation.*`)
  - `/admin/alumni` (`admin.alumni.*`)
- **Authorization**: Dilindungi middleware ganda `['auth', 'admin']` (`EnsureUserIsAdmin`).
- **Database Migrations**:
  - Kolom `is_published` (boolean, default: true) pada tabel `learning_materials`.
  - Kolom `is_featured` (boolean, default: false) pada tabel `showcases`.
- **Export Mechanism**: Response stream native PHP Laravel dengan MIME `text/csv` dan UTF-8 BOM untuk kompatibilitas langsung di Microsoft Excel.

---

## Phase 1: Dedicated Admin Sidebar Shell & Executive Overview

**User stories**: US 1, US 2, US 3, US 4, US 5, US 6, US 7

### What to build
Membangun fondasi tata letak Dedicated Admin Sidebar (`layouts/admin.blade.php`) lengkap dengan navigasi samping aktif, status profil admin, tombol beralih tema (Dark/Light Mode), dan tautan cepat ke portal publik. Memperbarui halaman `/admin/dashboard` dengan Executive Overview yang menyajikan kartu ringkasan metrik komunitas real-time, deretan tombol Aksi Cepat (*Quick Actions*), dan papan peringkat (*Leaderboard*) 5 prompt yang paling sering disalin oleh alumni.

### Acceptance criteria
- [ ] Pengguna dengan role `admin` dapat mengakses layout sidebar admin yang elegan dan responsif.
- [ ] Pengunjung umum atau member biasa diblokir dengan pesan `403 Forbidden` jika mencoba mengakses `/admin/*`.
- [ ] Tombol toggle tema di panel admin berfungsi mulus beralih antara Light dan Dark Mode.
- [ ] Dasbor menampilkan metrik akurat: Total Member, Submisi Menunggu Kurasi, Karya Tayang, Total Materi, dan Total Prompt.
- [ ] Dasbor menampilkan barisan tombol Quick Actions untuk navigasi kilat penambahan konten.
- [ ] Dasbor menampilkan daftar 5 prompt teratas berdasarkan jumlah salin (`copy_count`).
- [ ] Tes otomatis `AdminLayoutAndDashboardTest` lulus 100%.

---

## Phase 2: Manajemen Materi Belajar (Learning Materials CRUD & Publish Status)

**User stories**: US 8, US 9, US 10, US 11, US 12

### What to build
Menyediakan kemampuan pengelolaan penuh modul pembelajaran Study Group. Menambahkan migrasi kolom `is_published` pada tabel `learning_materials`. Membangun pengontrol `AdminLearningMaterialController` beserta halaman indeks materi (tabel dengan filter pilar & level), formulir pembuatan materi baru, formulir pengeditan, aksi hapus dengan konfirmasi dialog, serta toggle status draft/publish.

### Acceptance criteria
- [ ] Kolom `is_published` termigrasi dan terintegrasi pada model `LearningMaterial`.
- [ ] Halaman `/admin/materi` menampilkan tabel seluruh materi belajar beserta status publikasinya.
- [ ] Admin dapat membuat materi baru dengan mengisi judul, pilar, level, konten, tautan slide Canva/Drive, dan tautan YouTube.
- [ ] Admin dapat mengedit materi yang telah ada.
- [ ] Admin dapat menghapus materi dengan pesan konfirmasi.
- [ ] Materi berstatus draft tidak ditampilkan di halaman katalog publik `/materi`.
- [ ] Tes otomatis `AdminLearningMaterialTest` lulus 100%.

---

## Phase 3: Manajemen Prompt Library (Prompts CRUD & Featured Toggle)

**User stories**: US 13, US 14, US 15

### What to build
Membangun modul manajemen perpustakaan prompt di panel admin (`/admin/prompts`). Membangun `AdminPromptController` dengan fitur indeks tabel, formulir penambahan prompt baru (dengan input parameter variabel, target peran, target tools, dan status featured), formulir pengeditan prompt, serta aksi penghapusan.

### Acceptance criteria
- [ ] Halaman `/admin/prompts` menampilkan daftar prompt lengkap dengan info peran target, tools, jumlah disalin, dan status featured.
- [ ] Admin dapat menambah prompt baru dengan validasi data yang tepat.
- [ ] Admin dapat mengedit teks prompt dan instruksi panduan.
- [ ] Admin dapat menghapus prompt dari perpustakaan.
- [ ] Tes otomatis `AdminPromptCrudTest` lulus 100%.

---

## Phase 4: Manajemen Agenda Kegiatan & Generator Broadcast WhatsApp

**User stories**: US 16, US 17, US 18

### What to build
Membangun modul manajemen jadwal agenda komunitas (`/admin/agenda`). Membangun `AdminEventController` untuk membuat agenda sesi baru (tanggal, jam WIB, nama pemateri, link Zoom/Meet, dan deskripsi), mengedit agenda, serta menandai sesi selesai (*completed*) dengan memasukkan link rekaman video dan berkas slide. Mengimplementasikan fitur **Generator Broadcast WhatsApp** yang menghasilkan draf teks pengumuman resmi berformat tebal/emotikon rapi dengan tombol 1-klik salin ke clipboard.

### Acceptance criteria
- [ ] Halaman `/admin/agenda` menampilkan daftar event mendatang dan arsip sesi selesai.
- [ ] Admin dapat membuat jadwal event baru dan memperbarui sesi yang telah selesai dengan link rekaman.
- [ ] Tombol "Salin Format Siar WA" pada setiap event otomatis menyalin teks template undangan resmi ke clipboard.
- [ ] Teks broadcast WA memuat judul acara, hari/tanggal, jam WIB, pembicara, dan tautan virtual meet secara presisi.
- [ ] Tes otomatis `AdminEventAndBroadcastTest` lulus 100%.

---

## Phase 5: Moderasi Showcase Karya Lanjutan & Status Karya Unggulan

**User stories**: US 19, US 20, US 21, US 22

### What to build
Memperluas panel kurasi karya alumni (`/admin/curation`). Menambahkan migrasi kolom `is_featured` pada tabel `showcases`. Menyediakan antarmuka kurasi yang mengelompokkan submisi berdasarkan status (*Menunggu Kurasi*, *Disetujui*, *Perlu Revisi*), fitur 1-klik persetujuan, form penolakan dengan catatan revisi untuk member, serta tombol penanda Karya Unggulan (*Toggle Featured*) agar tampil di barisan depan showcase.

### Acceptance criteria
- [ ] Kolom `is_featured` termigrasi pada tabel `showcases`.
- [ ] Halaman kurasi menampilkan detail submisi karya (nama member, angkatan, no WA, tools, dampak).
- [ ] Aksi persetujuan (Approve) langsung mempublikasikan karya ke etalase publik.
- [ ] Aksi penolakan (Reject) menyimpan catatan revisi yang dapat dibaca alumni di dashboard mereka.
- [ ] Admin dapat menandai/mencabut status Karya Unggulan (*Featured*).
- [ ] Tes otomatis `AdminShowcaseCurationTest` lulus 100%.

---

## Phase 6: Direktori Member Alumni, 1-Click WhatsApp Chat & Export CSV

**User stories**: US 23, US 24, US 25, US 26

### What to build
Membangun modul direktori anggota alumni terdaftar (`/admin/alumni`). Membangun `AdminAlumniController` yang menyajikan tabel seluruh alumni dengan pencarian nama/email dan filter tahun angkatan kelulusan Assalaam. Menyediakan tombol 1-klik "Chat WhatsApp" untuk membuka percakapan personal ke alumni, serta tombol **"Export CSV"** yang menghasilkan unduhan berkas data spreadsheet siap buka di Excel.

### Acceptance criteria
- [ ] Halaman `/admin/alumni` menampilkan tabel direktori member dengan nama, email, no WA, tahun angkatan, dan tanggal daftar.
- [ ] Pencarian dan filter angkatan berfungsi akurat.
- [ ] Tombol "Chat WA" memicu tautan `https://wa.me/62...` yang valid.
- [ ] Tombol "Export CSV" mengunduh berkas `.csv` dengan header kolom rapi dan UTF-8 BOM yang dapat dibuka di Excel tanpa error encoding.
- [ ] Tes otomatis `AdminAlumniDirectoryTest` lulus 100%.
