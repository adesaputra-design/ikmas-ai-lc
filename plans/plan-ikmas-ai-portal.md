# Plan: Portal Web IKMAS AI Learning Center

> Source PRD: [plans/prd-ikmas-ai-portal.md](file:///d:/Ade%20Zone/3.%20AK%20Studio%20%232/IKMAS%20AILC/plans/prd-ikmas-ai-portal.md)

## Architectural decisions

Durable decisions that apply across all phases:

- **Framework & Runtime**: PHP 8.4 via Laravel Herd, Laravel 11/12 MVC dengan Blade Templating Engine.
- **Routes & URL Structure**:
  - Public Hub: `/` (Beranda), `/materi`, `/materi/{slug}`, `/prompts`, `/showcase`, `/showcase/{slug}`, `/agenda`, `/agenda/{slug}`, `/tentang`
  - Authentication: `/login`, `/register`, `/logout`
  - Member Area: `/member/dashboard`, `/member/showcase/create`, `/member/bookmarks`, `/member/profile`
  - Admin Area: `/admin/dashboard`, `/admin/curation`, `/admin/materials`, `/admin/prompts`, `/admin/events`
- **Database Schema (SQLite Portable)**:
  - `users`: `name`, `email`, `password`, `whatsapp_number`, `alumni_year`, `bio`, `role` (`'admin'` / `'member'`)
  - `categories`: `name`, `slug`, `type` (`'learning'`, `'prompt'`, `'showcase'`), `color`
  - `learning_materials`: `category_id`, `title`, `slug`, `level` (`'beginner'`, `'explorer'`, `'practitioner'`), `pillar` (`'basics'`, `'tools'`, `'productivity'`, `'workflow'`, `'opportunity'`), `summary`, `content`, `reading_minutes`, `video_url`
  - `prompts`: `category_id`, `title`, `slug`, `target_role`, `target_tool`, `prompt_text`, `instruction`, `tags`
  - `events`: `title`, `slug`, `description`, `event_date`, `duration_minutes`, `location_url`, `speaker_name`, `speaker_title`, `status` (`'upcoming'`, `'completed'`), `materials_url`
  - `showcases`: `user_id`, `title`, `slug`, `description`, `tools_used`, `image_url`, `project_url`, `impact_story`, `status` (`'pending'`, `'approved'`, `'rejected'`), `admin_notes`
  - `bookmarks`: polymorphic (`user_id`, `bookmarkable_id`, `bookmarkable_type`)
- **Key Models**:
  - `User`, `Category`, `LearningMaterial`, `Prompt`, `Event`, `Showcase`, `Bookmark`
- **Design System & Aesthetics**:
  - Antarmuka **Dual Theme** (*Clean Light Mode* secara default + *Dark Mode Toggle* disimpan di `localStorage`).
  - Warna: *Deep Navy* (`#0B192C`), *Electric Blue* (`#2563EB`), *Cyan Accent* (`#06B6D4`).
  - Tipografi: *Plus Jakarta Sans*.
  - Desain *mobile-first*, *glassmorphism* halus, kartu interaktif, dan notifikasi toast.

---

## Phase 1: Fondasi Proyek, Desain Sistem Dual-Theme & Landing Hub Publik

**User stories**: US 1, US 6, US 7, US 20, US 22

### What to build

Inisialisasi fondasi proyek Laravel 11 lengkap dengan konfigurasi database SQLite dan tata letak dasar (*base layout*). Membangun sistem tema ganda (Light/Dark Mode toggle) yang persisten di browser, tipografi Plus Jakarta Sans, dan palet warna resmi IKMAS AI. Membuat halaman beranda (*Landing Page*) publik yang memuat Hero Section dengan *Why Statement*, narasi sambutan ramah dari *Persona Garuda* (Agen Informasi IKMAS AI), navigasi responsif, seksi nilai tambah komunitas, dan tombol ajakan (*Call to Action*) untuk bergabung ke Grup WhatsApp Komunitas.

### Acceptance criteria

- [ ] Aplikasi Laravel terkonfigurasi dan berjalan lancar di lingkungan PHP 8.4 lokal.
- [ ] Database SQLite terbentuk otomatis melalui migrasi awal.
- [ ] Base layout menyediakan navigasi responsif (desktop & mobile) dan footer informatif.
- [ ] Tombol toggle tema berfungsi instan beralih antara Light Mode dan Dark Mode, dengan preferensi tersimpan di `localStorage`.
- [ ] Landing page memuat Hero Section, Persona Garuda Onboarding Hub, dan link WhatsApp aktif.
- [ ] Halaman 404 ramah pengguna tersedia jika mengakses rute yang salah.

---

## Phase 2: Repositori Materi Study Group & Filter Level Belajar

**User stories**: US 2, US 15

### What to build

Membangun modul materi pembelajaran *Study Group* dari hulu ke hilir. Membuat skema dan model `categories` serta `learning_materials`, dilengkapi seeder data materi nyata dari dokumen strategi (misal: "AI untuk Produktivitas Sehari-hari", "Prompt Engineering Dasar", "Automasi Workflow"). Menyediakan halaman katalog `/materi` dengan fitur filter interaktif berdasarkan tingkat kemahiran (*Beginner*, *Explorer*, *Practitioner*) dan pilar topik. Mengembangkan halaman baca materi `/materi/{slug}` dengan tampilan tipografi yang nyaman, estimasi waktu baca, dan penyematan link rekaman video/slide.

### Acceptance criteria

- [ ] Tabel `categories` dan `learning_materials` termigrasi dengan relasi Eloquent yang tepat.
- [ ] Database terisi *seed data* materi Study Group nyata dari dokumen IKMAS AI.
- [ ] Halaman `/materi` dapat memfilter daftar materi berdasarkan level dan pilar topik tanpa memuat ulang seluruh halaman secara lambat.
- [ ] Lencana level (*Beginner*, *Explorer*, *Practitioner*) tampil dengan warna pembeda yang jelas.
- [ ] Halaman detail materi menampilkan isi lengkap (Markdown/HTML), estimasi waktu baca, serta tautan video/slide pendukung.

---

## Phase 3: Interactive Prompt Library dengan 1-Click Copy

**User stories**: US 3, US 16

### What to build

Membangun modul perpustakaan *prompt* praktis siap pakai. Membuat skema dan model `prompts` serta *seed data* kumpulan prompt produktivitas (copywriting, riset, coding/automasi, administrasi bisnis). Mengembangkan antarmuka katalog `/prompts` dengan filter kategori peran (*Copywriter*, *Pebisnis*, *Guru*, *Programmer*) dan tools (*ChatGPT*, *Claude*, *Cursor*). Mengintegrasikan fitur interaktif *1-Click Copy* menggunakan Clipboard API: saat tombol diklik, teks prompt tersalin seketika, ikon tombol berubah menjadi centang aktif, dan muncul notifikasi pop-up (*toast*) yang mengonfirmasi bahwa prompt telah siap ditempel.

### Acceptance criteria

- [ ] Tabel `prompts` termigrasi dan terisi seeder prompt produktivitas teruji.
- [ ] Halaman `/prompts` menampilkan kartu-kartu prompt dengan teks instruksi dan penanda variabel yang jelas (misal: `[Topik]`, `[Target Audiens]`).
- [ ] Filter peran dan tools sasaran menyaring prompt secara responsif.
- [ ] Menekan tombol "Copy Prompt" menyalin seluruh teks prompt ke clipboard pengguna.
- [ ] Muncul notifikasi toast pop-up yang informatif dan tombol memberikan umpan balik visual sukses.

---

## Phase 4: Kalender Agenda Event & Detail Sesi Study Group

**User stories**: US 5, US 17

### What to build

Membangun modul agenda kegiatan komunitas. Membuat skema dan model `events` serta *seed data* jadwal sesi perdana Study Group dan meetup bulanan. Mengembangkan halaman `/agenda` yang memisahkan antara sesi mendatang (*Upcoming Events*) dengan arsip kegiatan yang telah selesai (*Past Events*). Halaman detail agenda `/agenda/{slug}` memuat informasi tanggal, waktu WIB, profil pembicara/fasilitator, link langsung ruang virtual (Zoom/Google Meet), serta tautan materi pasca-sesi jika acara telah rampung.

### Acceptance criteria

- [ ] Tabel `events` termigrasi dengan atribut waktu, lokasi virtual, status, dan materi.
- [ ] Halaman `/agenda` menampilkan kartu agenda dengan lencana status jelas (*Upcoming* / *Completed*).
- [ ] Kartu event mendatang memiliki tombol langsung menuju tautan Zoom/Meet atau registrasi.
- [ ] Sesi yang telah lewat menampilkan tautan rekaman dan berkas slide materi.
- [ ] Widget agenda terdekat di halaman Beranda otomatis sinkron dengan data event aktif.

---

## Phase 5: Keanggotaan Alumni & Galeri Showcase Karya dengan Form Submisi

**User stories**: US 4, US 8, US 9, US 10, US 11, US 12, US 21

### What to build

Membangun modul autentikasi alumni dan etalase karya. Menyiapkan alur registrasi khusus alumni Assalaam (nama, email, nomor WhatsApp, tahun angkatan) dan sistem login. Membangun halaman galeri publik `/showcase` yang menampilkan karya-karya alumni yang telah disetujui. Membangun halaman dashboard member `/member/dashboard` yang memungkinkan alumni mengisi form pengajuan karya baru (judul, deskripsi, upload gambar tangkapan layar, link proyek, cerita dampak). Submisi baru otomatis berstatus *pending*. Member juga dapat memantau status karya miliknya dan menandai (*bookmark*) prompt/materi favorit.

### Acceptance criteria

- [ ] Pengguna dapat mendaftar akun baru dengan mengisi data wajib: Nama, Email, Password, No WhatsApp, dan Tahun Angkatan.
- [ ] Pengguna terdaftar dapat login dan mengakses area member.
- [ ] Form submisi karya berfungsi dengan validasi input (termasuk batas format & ukuran unggahan gambar).
- [ ] Karya yang baru diajukan berstatus `pending` dan belum tampil di halaman publik.
- [ ] Galeri publik `/showcase` hanya menampilkan karya yang telah berstatus `approved`.
- [ ] Member dapat menandai prompt atau materi ke daftar bookmark pribadi.

---

## Phase 6: Panel Kurasi & Dashboard Manajemen Pengurus Admin

**User stories**: US 14, US 18, US 19

### What to build

Membangun area terproteksi khusus pengurus/administrator (`/admin`). Menyediakan dashboard ringkasan metrik komunitas (total anggota terdaftar, total materi, total prompt, dan jumlah karya yang menunggu kurasi). Membangun antarmuka kurasi karya alumni di mana admin dapat meninjau detail pengajuan karya, menyetujui (*Approve*) dengan satu klik sehingga langsung tayang di galeri publik, atau menolak (*Reject*) dengan menyertakan catatan. Admin juga dapat mengelola materi, prompt, dan event secara mandiri.

### Acceptance criteria

- [ ] Rute `/admin/*` diproteksi middleware autentikasi dan otorisasi role `admin`.
- [ ] Dashboard admin menampilkan kartu ringkasan metrik komunitas yang akurat.
- [ ] Halaman kurasi menampilkan daftar karya berstatus `pending` secara terorganisir.
- [ ] Menekan tombol "Approve" mengubah status karya menjadi `approved` dan langsung memunculkannya di galeri publik.
- [ ] Menekan tombol "Reject" memperbarui status karya dan menyimpan catatan penolakan.
- [ ] Akun admin bawaan tersedia via seeder untuk kemudahan peninjauan instan.
