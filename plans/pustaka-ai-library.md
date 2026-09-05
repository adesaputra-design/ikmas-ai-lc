# Plan: Pustaka AI (Library Rangkuman Buku, Resume Podcast & Karya Ilmiah)

> Source PRD: [prd-pustaka-ai-library.md](file:///d:/Ade%20Zone/3.%20AK%20Studio%20%232/IKMAS%20AILC/plans/prd-pustaka-ai-library.md)

## Architectural decisions

Durable decisions that apply across all phases:

- **Routes**:
  - Public: `GET /library` (catalog index with filter by type & category), `GET /library/{slug}` (detail reader with guest teaser vs full member view).
  - Member: `GET /member/library/create` (academic submission form), `POST /member/library` (store paper), `GET /member/library` (my academic submissions).
  - Admin: `GET /admin/library` (catalog & curation queue), `GET /admin/library/create`, `POST /admin/library`, `GET /admin/library/{item}/edit`, `PUT /admin/library/{item}`, `POST /admin/library/{item}/approve`, `POST /admin/library/{item}/reject`, `DELETE /admin/library/{item}`.
- **Schema**:
  - Table `library_items`: `id`, `user_id`, `title`, `slug`, `type` (`book`, `podcast`, `academic`), `category`, `summary_preview`, `content`, `author_name`, `reading_time`, `podcast_source`, `media_embed_url`, `duration`, `academic_degree`, `institution`, `publication_year`, `co_authors`, `external_url`, `file_path`, `cover_image`, `status` (`pending`, `approved`, `rejected`), `rejection_note`, `is_featured`, `views_count`, `timestamps`, `softDeletes`.
  - Table `bookmarks`: column `library_item_id` (nullable foreignId to `library_items`).
- **Key models**:
  - `LibraryItem` (casts, scopes: `approved()`, `byType()`, `featured()`, relations: `user()`, `bookmarks()`).
  - `Bookmark` (belongsTo `libraryItem`).
  - `User` (hasMany `libraryItems`, hasMany `bookmarks`).
- **Authorization & RBAC**:
  - Guest: view catalog & summary teaser with lock badge 🔒.
  - Member & Active Subscriber: full reading, listening (embedded player), PDF download, and bookmarking.
  - Alumni Member only: submit academic research (subscriber is blocked from submission).
  - Staff RBAC: new modular permission `library` in `AdminTeamController::AVAILABLE_PERMISSIONS` and `CheckStaffPermission`.

---

## Phase 1: Fondasi Pustaka & Katalog Publik dengan Teaser Preview (Buku & Podcast)

**User stories**: 1, 2, 3, 4, 5

### What to build

Membangun fondasi model data `library_items`, routing publik `/library` dan `/library/{slug}`, antarmuka katalog dengan filter tab (Semua, Buku, Podcast) serta filter kategori topik AI. Menambahkan menu "Pustaka AI" di dropdown "Belajar" pada navbar. Mengimplementasikan pembatasan akses: tamu melihat teaser preview dengan penanda gembok dan banner CTA registrasi/login, sementara member dan subscriber aktif dapat membaca naskah penuh serta menikmati embed player Spotify/YouTube untuk podcast. Menyiapkan database seeder contoh buku dan podcast.

### Acceptance criteria

- [ ] Migrasi `library_items` berhasil dieksekusi tanpa error.
- [ ] Model `LibraryItem` memiliki relasi, scopes, dan accessor yang lengkap.
- [ ] Menu "Pustaka AI" tampil pada navbar desktop dropdown "Belajar" dan mobile drawer.
- [ ] Halaman `/library` menampilkan daftar buku & podcast dengan filter tipe dan kategori.
- [ ] Tamu (guest) yang membuka detail `/library/{slug}` melihat sinopsis cuplikan, naskah dikaburkan (*locked*), dan banner ajakan masuk/daftar.
- [ ] Member alumni dan subscriber yang login dapat membaca rangkuman penuh dan melihat embed player media.
- [ ] Seeder memuat minimal 2 rangkuman buku AI dan 2 resume podcast AI.
- [ ] Automated tests untuk katalog publik dan hak akses guest/member lolos dengan hijau.

---

## Phase 2: Submisi Mandiri Karya Ilmiah Alumni & Pelacak Dasbor

**User stories**: 8, 9, 10

### What to build

Menyediakan formulir pengajuan karya ilmiah bagi alumni Assalaam di `/member/library/create` untuk mengunggah naskah Skripsi, Tesis, Disertasi, atau Jurnal dengan unggah PDF aman (maksimal 10MB) atau mencantumkan tautan DOI/repositori eksternal. Menambahkan proteksi agar hanya alumni member yang dapat mengajukan (subscriber non-alumni ditolak 403 jika mencoba submit). Menambahkan tab "Karya Ilmiah Saya" di Dasbor Member (`/member/dashboard`) untuk memantau status kurasi naskah (*Pending, Approved, Rejected*).

### Acceptance criteria

- [ ] Form `/member/library/create` dapat diakses oleh member alumni dengan input metadata akademik yang lengkap.
- [ ] File PDF berhasil diunggah ke storage `public/academic_papers/` dan tersimpan jalurnya di database.
- [ ] Akun subscriber (non-alumni) diblokir dari formulir pengajuan dengan pesan penolakan yang ramah.
- [ ] Naskah yang baru diajukan otomatis berstatus `pending` dan belum tampil di katalog publik sebelum disetujui.
- [ ] Dasbor Member menampilkan tab "Karya Ilmiah Saya" dengan riwayat pengajuan dan badge status kurasi.
- [ ] Automated tests untuk alur pengajuan member dan proteksi subscriber lolos dengan hijau.

---

## Phase 3: Admin Suite & Antrean Kurasi Karya Ilmiah Pengurus

**User stories**: 11, 12, 13

### What to build

Mengembangkan panel administrasi `/admin/library` yang terintegrasi dengan sistem perizinan modular `permission:library`. Admin dan Staf kurasi dapat mengelola (CRUD) rangkuman buku dan podcast. Menyediakan tab antrean kurasi naskah karya ilmiah alumni masuk: admin dapat meninjau detail, mengunduh draf PDF, lalu menekan tombol "Setujui" (naskah langsung berstatus `approved` dan tayang di publik) atau "Tolak" (disertai catatan revisi yang tampil di dasbor alumni). Menambahkan opsi izin `library` di modul manajemen tim.

### Acceptance criteria

- [ ] Izin modular `library` terdaftar di `AdminTeamController::AVAILABLE_PERMISSIONS` dan diproteksi `CheckStaffPermission`.
- [ ] Halaman `/admin/library` menyediakan CRUD penuh untuk rangkuman buku dan podcast.
- [ ] Antrean kurasi menampilkan seluruh karya ilmiah berstatus `pending`.
- [ ] Aksi "Setujui" mengubah status menjadi `approved` dan naskah otomatis muncul di `/library`.
- [ ] Aksi "Tolak" menyimpan catatan revisi `rejection_note` dan mengubah status menjadi `rejected`.
- [ ] Automated tests untuk operasi admin dan perizinan staf kurasi lolos dengan hijau.

---

## Phase 4: Halaman Riset Lengkap, Unduh PDF Naskah & Integrasi Bookmark

**User stories**: 6, 7

### What to build

Menyempurnakan halaman detail khusus karya ilmiah di `/library/{slug}` dengan tampilan sitasi standar (APA/IEEE preview), abstrak dwibahasa, tombol "Unduh Naskah PDF" aman (hanya bisa diunduh oleh member/subscriber aktif), dan tautan eksternal DOI. Mengintegrasikan kolom `library_item_id` pada tabel `bookmarks` dan tombol bookmark interaktif di halaman katalog dan detail. Mengimplementasikan penghitung penayangan (*views count*) saat konten dibuka.

### Acceptance criteria

- [ ] Halaman karya ilmiah menampilkan metadata lengkap: jenjang akademik, institusi, tahun, dan abstrak.
- [ ] Tombol unduh PDF hanya dapat diakses oleh member yang sedang login; pengunjung tamu dialihkan ke login.
- [ ] Member dapat melakukan bookmark pada buku, podcast, maupun karya ilmiah.
- [ ] Konten yang di-bookmark muncul di daftar "Bookmark Saya" pada dasbor akun member.
- [ ] Jumlah penayangan (`views_count`) bertambah secara akurat setiap kali halaman detail diakses.
- [ ] Seluruh test suite sistem (Unit & Feature tests) lolos tanpa kegagalan.
