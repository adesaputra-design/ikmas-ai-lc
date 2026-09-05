## Problem Statement

Komunitas alumni Assalaam di **IKMAS AI Learning Center** saat ini telah memiliki modul *Materi Belajar* (kurikulum tutorial) dan *Prompt Library* (koleksi instruksi AI). Namun, komunitas belum memiliki wadah terstruktur untuk:
1. **Rangkuman Buku AI & Bisnis**: Mengonsumsi intisari buku-buku AI, teknologi, dan kewirausahaan terbaik dunia secara cepat dalam format ringkas (*executive summary & key takeaways*).
2. **Resume Podcast AI**: Mengakses rangkuman insight penting dari episode podcast AI terkemuka tanpa harus mendengarkan rekaman audio berjam-jam, namun tetap dapat memutar media aslinya melalui pemutar tersemat.
3. **Dokumentasi Karya Ilmiah Alumni**: Belum tersedianya etalase dan repositori terpusat untuk mendokumentasikan kontribusi riset akademik alumni IKMAS (Skripsi, Tesis S2, Disertasi S3, dan Jurnal Ilmiah bereputasi). Banyak alumni yang telah menghasilkan karya riset AI berkualitas di berbagai universitas dalam dan luar negeri, namun karyanya tersebar dan tidak terarsip dalam komunitas.
4. **Insentif & Eksklusivitas Keanggotaan**: Dibutuhkan konten bernilai tinggi (*high-value asset*) yang eksklusif bagi member dan subscriber terdaftar guna mendorong alumni baru untuk segera mendaftar akun dan aktif di portal.

## Solution

Mengembangkan modul **Pustaka AI (Knowledge Hub / Library)** yang komprehensif dan terpadu:
1. **Unified Multi-Type Library**: Satu katalog pustaka terpadu dengan 3 jenis media:
   - **Rangkuman Buku (`book`)**: Sinopsis, bab-bab inti, estimasi waktu baca, dan poin-poin kunci (*key takeaways*).
   - **Resume Podcast (`podcast`)**: Rangkuman dialog episode, poin insight, dan pemutar media audio/video tersemat (*Spotify / YouTube embed*).
   - **Karya Ilmiah Alumni (`academic`)**: Arsip riset alumni Assalaam (Jurnal, Tesis, Disertasi, Skripsi) dengan unggah berkas PDF naskah dan tautan eksternal (DOI/Google Scholar).
2. **Kategorisasi Topik AI**: Seluruh konten dikelompokkan ke dalam kategori topik terstruktur (misal: *Fundamental AI, Prompting & LLM, AI for Business, Computer Vision, Etika & Masa Depan AI*).
3. **Akses Freemium / Teaser Preview**: Pengunjung tamu (publik belum login) dapat menjelajahi katalog dan membaca cuplikan singkat dengan penanda gembok 🔒 (*Khusus Member*). Untuk membaca naskah lengkap, mendengarkan player, atau mengunduh PDF, tamu diarahkan untuk Masuk atau Daftar Akun. Member alumni dan subscriber aktif menikmati akses penuh tanpa batas.
4. **Submisi & Kurasi Karya Ilmiah Alumni**: Member alumni dapat mengajukan naskah riset karya mereka sendiri melalui dasbor member. Pengajuan masuk ke antrean kurasi Admin Panel berstatus *Pending*, dan baru dipublikasikan setelah ditinjau serta disetujui (*Approved*) oleh Admin atau Staf Pengurus yang berwenang.
5. **Bookmark System**: Member dapat menyimpan item pustaka favorit ke daftar bookmark pribadi untuk dibaca kembali kapan saja.

---

## User Stories

### 1. Pengunjung Tamu (Guest / Belum Login)
1. **As a Guest**, I want to browse the Pustaka AI catalog and filter by media type (Buku, Podcast, Karya Ilmiah) or AI topic, so that I can discover the wealth of knowledge available in IKMAS.
2. **As a Guest**, I want to see cover images, titles, authors, and a short teaser synopsis of each item, so that I can gauge the relevance of the content.
3. **As a Guest**, I want to see clear member-exclusive lock badges (🔒) and an inviting Call-To-Action to login or register when trying to read the full content, so that I understand the value of joining the IKMAS AI community.

### 2. Member Alumni & Subscriber Aktif
4. **As an active Member/Subscriber**, I want to read complete book summaries in a distraction-free reader view with key takeaways highlighted, so that I can absorb core AI insights quickly.
5. **As an active Member/Subscriber**, I want to read podcast resumes while playing the embedded audio/video from Spotify or YouTube, so that I have a multi-sensory learning experience.
6. **As an active Member/Subscriber**, I want to read abstracts, academic degree info, and download the full PDF manuscript (or open the official DOI link) of alumni research papers, so that I can cite and learn from fellow alumni's research.
7. **As an active Member**, I want to bookmark any book summary, podcast, or academic paper with a single click, so that I can easily revisit it from my dashboard.

### 3. Kontributor Karya Ilmiah (Alumni Member)
8. **As an Alumni Member**, I want to access an "Ajukan Karya Ilmiah" form from my Member Dashboard, so that I can submit my thesis, dissertation, or journal paper to the community library.
9. **As an Alumni Member**, I want to upload a PDF file of my paper (up to 10MB) or provide an external DOI/university repository URL, along with metadata (degree level, university, graduation year, abstract, and co-authors).
10. **As an Alumni Member**, I want to track the curation status of my submitted papers (Pending, Approved, Rejected with admin notes) from my dashboard, so that I know when my work is published or needs revision.

### 4. Administrator & Staf Pengurus (Modul Library)
11. **As an Admin or authorized Staff**, I want to create, edit, and delete book summaries and podcast resumes via the Admin Panel, including setting categories, embed URLs, and reading times.
12. **As an Admin or authorized Staff**, I want to view a dedicated curation queue for alumni academic papers, so that I can verify their authenticity, review the abstract, and approve or reject with constructive feedback.
13. **As an Admin**, I want to grant the modular permission `library` to specific staff members, so that designated editorial team members can manage the library without accessing sensitive team settings.

---

## Implementation Decisions

### 1. Model Arsitektur & Database
- **Tabel `library_items`**:
  - Kolom esensial: `user_id` (nullable, untuk penulis alumni), `title`, `slug` (unique), `type` (enum: `book`, `podcast`, `academic`), `category` (string, e.g. *Prompting, LLM, Computer Vision, Business AI*), `summary_preview` (text teaser), `content` (longText naskah lengkap).
  - Kolom metadata buku: `author_name`, `reading_time` (e.g. *8 mnt baca*).
  - Kolom metadata podcast: `podcast_source`, `media_embed_url` (Spotify/YouTube), `duration`.
  - Kolom metadata karya ilmiah: `academic_degree` (enum: `skripsi`, `tesis`, `disertasi`, `jurnal`), `institution` (nama kampus), `publication_year`, `co_authors`, `external_url` (DOI / repositori), `file_path` (PDF upload).
  - Kolom kontrol & status: `cover_image`, `status` (enum: `pending`, `approved`, `rejected`), `rejection_note`, `is_featured` (boolean), `views_count`, soft deletes.
- **Relasi Bookmark**: Integrasi dengan tabel `bookmarks` menggunakan `library_item_id` (nullable foreign key) untuk mendukung penyimpanan lintas tipe materi, showcase, dan library.

### 2. Antarmuka Pengguna (User Interface)
- **Navigasi Navbar (`layouts.app`)**:
  - Tambahkan menu **"Pustaka AI"** di dalam dropdown *"Belajar"* di samping *Materi Belajar* dan *Prompt Library*.
  - Menampilkan subtitle ringkas: *"Rangkuman buku, podcast & karya ilmiah alumni"*.
- **Halaman Katalog (`/library`)**:
  - Header inspiratif dengan statistik pustaka (total buku, podcast, naskah ilmiah).
  - Tab filter interaktif: *Semua*, *📚 Rangkuman Buku*, *🎙️ Resume Podcast*, *🎓 Karya Ilmiah Alumni*.
  - Filter kategori topik & form pencarian instan.
  - Kartu item dengan badge tipe, estimasi durasi/waktu baca, dan ikon gembok 🔒 bagi pengunjung non-login.
- **Halaman Detail Baca (`/library/{slug}`)**:
  - Mode pembaca bersih (*clean reading layout*).
  - Panel metadata buku/podcast/paper (penulis, institusi, tahun, kategori).
  - Player tersemat otomatis jika URL Spotify / YouTube valid.
  - Tombol aksi: Bookmark, Bagikan (Share WA), Unduh PDF / Buka DOI.
  - Jika Guest: Menampilkan sinopsis preview, mengaburkan naskah inti, dan menyajikan card ajakan: *"Materi ini eksklusif untuk member IKMAS AI. Masuk akun atau daftar sekarang."*
- **Halaman Dasbor Member (`/member/dashboard` & `/member/library/create`)**:
  - Tab *"Karya Ilmiah Saya"* menampilkan daftar riset yang pernah diajukan alumni dan badge statusnya.
  - Formulir pengajuan karya ilmiah yang intuitif dengan validasi berkas PDF maksimal 10MB.
- **Admin Panel (`/admin/library`)**:
  - Tabel kelola buku & podcast (tambah/edit/hapus).
  - Tab antrean kurasi karya ilmiah alumni dengan aksi *Setujui (Approve)* dan *Tolak (Reject)* disertai catatan revisi.

### 3. Hak Akses & Perizinan (RBAC)
- Guest: Hanya diizinkan melihat daftar dan preview sinopsis.
- Member (Alumni) & Subscriber Aktif: Akses penuh baca, dengar, unduh, dan bookmark.
- Hanya `member` (alumni terverifikasi) yang memiliki hak mengajukan naskah karya ilmiah (karena aturan: *penulis wajib anggota IKMAS*). `subscriber` (non-alumni) tidak diizinkan submit karya ilmiah.
- Izin staf modular baru: `library` (*"Pustaka & Karya Ilmiah"*), dikonfigurasi di `AdminTeamController::AVAILABLE_PERMISSIONS` dan `CheckStaffPermission`.

---

## Out of Scope

1. Fitur audio reader text-to-speech (TTS) otomatis untuk membacakan rangkuman buku (dapat dievaluasi pada fase lanjutan).
2. Sistem peer-review akademik ganda (blind review) — modul kurasi difokuskan pada kurasi editorial dan verifikasi berkas oleh pengurus IKMAS, bukan jurnal penerbitan formal.
3. Fitur penjualan buku fisik atau e-commerce berbayar per judul.

---

## Further Notes

- Konfigurasi penyimpanan PDF menggunakan disk `public` (`storage/app/public/academic_papers/`) dengan symlink `public/storage`.
- Format embed media podcast mendukung konversi otomatis URL reguler Spotify / YouTube menjadi format `<iframe>` embed yang responsif.
