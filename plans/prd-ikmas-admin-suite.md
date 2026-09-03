# PRD: IKMAS AI Admin Suite & Management Dashboard

## Problem Statement

Pengurus komunitas IKMAS AI Learning Center saat ini menghadapi keterbatasan dalam mengelola konten dan operasional komunitas secara mandiri:
1. **Ketergantungan Teknis Konten**: Penambahan materi pembelajaran dan prompt baru saat ini masih mengandalkan database seeder/file konfigurasi kode, sehingga pengurus non-teknis kesulitan memperbarui materi Study Group mingguan.
2. **Koordinasi Siar Acara**: Pengurus harus merangkai pesan undangan sesi belajar WhatsApp secara manual dari nol setiap kali menjadwalkan agenda baru, memakan waktu dan berisiko terjadi inkonsistensi tautan/jadwal.
3. **Pengawasan Moderasi & Komunitas**: Belum ada antarmuka khusus untuk meninjau detail karya showcase sebelum disetujui, belum ada fitur penanda karya unggulan (*featured*), dan belum ada direktori lengkap untuk melihat daftar seluruh alumni terdaftar beserta kemudahan menghubungi nomor WhatsApp atau mengekspor data ke format CSV/Excel.
4. **Navigasi Terpisah Pengurus**: Pengurus membutuhkan lingkungan kerja administrasi (*Dedicated Admin Sidebar Layout*) yang efisien, fokus, dan terpisah dari tata letak pengunjung publik.

## Solution

Membangun **IKMAS AI Admin Suite & Management Dashboard** yang terintegrasi penuh ke dalam portal web komunitas, terdiri dari:
1. **Dedicated Admin Sidebar Layout**: Panel kerja khusus pengurus dengan navigasi samping (Sidebar) yang responsif, terintegrasi mode gelap/terang, dan aman dengan proteksi otorisasi role admin.
2. **Executive Summary Dashboard**: Metrik ringkasan komunitas real-time, aktivitas terkini, daftar prompt paling populer (berdasarkan jumlah salin), dan tombol aksi cepat (*Quick Actions*).
3. **Manajemen Konten Mandiri (CRUD Materi & Prompt)**: Formulir web untuk membuat, mengedit, mempublikasikan (draft/publish), dan menghapus Materi Belajar & koleksi Prompt Library dengan dukungan tautan slide Canva/Google Drive dan video YouTube.
4. **Manajemen Agenda & Generator Broadcast WhatsApp**: Penjadwalan agenda sesi belajar/sharing dengan tombol 1-klik untuk otomatis membuat dan menyalin draf pesan undangan resmi WhatsApp yang siap di-blast ke grup komunitas alumni.
5. **Moderasi Karya Lanjutan**: Peninjauan detail submisi karya, aksi 1-klik setujui/tolak dengan catatan revisi, serta penetapan status Karya Unggulan (*Featured*).
6. **Direktori Member Alumni & Export Data**: Daftar seluruh alumni terdaftar dengan filter angkatan, tombol langsung chat WhatsApp, dan fitur unduh data ke format CSV/Excel.

## User Stories

### A. Layout, Keamanan, dan Navigasi Admin
1. **US 1**: Sebagai Admin, saya ingin mengakses panel admin melalui tata letak sidebar khusus yang modern dan intuitif, sehingga saya dapat bernavigasi antar modul pengelolaan dengan cepat.
2. **US 2**: Sebagai Pengunjung atau Member biasa, saya tidak boleh dapat mengakses rute-rute admin (`/admin/*`) dan harus menerima respons `403 Forbidden` jika mencoba membukanya.
3. **US 3**: Sebagai Admin, saya ingin dapat berpindah antara Light Mode dan Dark Mode di panel admin sesuai kenyamanan mata saat bekerja.
4. **US 4**: Sebagai Admin, saya ingin memiliki tombol pintasan untuk membuka web publik di tab baru guna melihat langsung dampak perubahan konten.

### B. Beranda Dasbor Eksekutif & Metrik
5. **US 5**: Sebagai Admin, saya ingin melihat statistik ringkasan (Total Member, Submisi Menunggu Kurasi, Karya Tayang, Total Materi, dan Total Prompt) saat pertama kali membuka dasbor, sehingga saya mengetahui status operasional komunitas secara instan.
6. **US 6**: Sebagai Admin, saya ingin melihat daftar 5 prompt yang paling banyak disalin oleh alumni, sehingga pengurus dapat mengetahui topik atau keahlian AI yang paling diminati komunitas.
7. **US 7**: Sebagai Admin, saya ingin memiliki barisan tombol Aksi Cepat (*Quick Actions*) di bagian atas dasbor untuk langsung menambah materi, membuat event, atau menambah prompt tanpa harus mencari di menu lain.

### C. Manajemen Materi Belajar (Learning Materials CRUD)
8. **US 8**: Sebagai Admin, saya ingin melihat tabel daftar seluruh materi belajar dengan informasi pilar, level kemahiran, durasi baca, status publikasi, dan aksi kelola.
9. **US 9**: Sebagai Admin, saya ingin membuat modul materi belajar baru dengan mengisi judul, pilar topik, level kemahiran, isi materi, tautan slide Canva/Drive, dan tautan rekaman YouTube.
10. **US 10**: Sebagai Admin, saya ingin mengedit materi belajar yang sudah ada untuk memperbarui konten atau tautan berkas pendukung.
11. **US 11**: Sebagai Admin, saya ingin menghapus materi belajar yang sudah usang atau tidak relevan dengan konfirmasi penghapusan untuk mencegah salah klik.
12. **US 12**: Sebagai Admin, saya ingin mengatur status materi apakah berstatus `published` (langsung tayang di web publik) atau `draft` (hanya terlihat oleh pengurus).

### D. Manajemen Prompt Library (Prompts CRUD)
13. **US 13**: Sebagai Admin, saya ingin melihat katalog tabel seluruh prompt yang tersimpan dengan filter peran profesi dan alat AI.
14. **US 14**: Sebagai Admin, saya ingin menambahkan prompt baru dengan menentukan judul, peran target, alat AI target, teks prompt berparameter (`[Parameter]`), instruksi panduan, serta tanda centang apakah menjadi *Featured Prompt*.
15. **US 15**: Sebagai Admin, saya ingin memperbarui atau menghapus prompt yang sudah ada di perpustakaan.

### E. Manajemen Agenda Kegiatan & Generator Broadcast WhatsApp
16. **US 16**: Sebagai Admin, saya ingin menjadwalkan agenda event baru dengan menentukan judul, topik bahasan, tanggal & jam WIB, nama pemateri, tautan Zoom/Meet, dan deskripsi acara.
17. **US 17**: Sebagai Admin, saya ingin memperbarui agenda yang telah selesai menjadi status `completed` dan menyematkan tautan rekaman YouTube serta tautan materi tayang.
18. **US 18**: Sebagai Admin, saya ingin menekan tombol **"Salin Format Broadcast WhatsApp"** pada kartu/baris agenda, sehingga sistem langsung menyalin teks undangan rapi berisi salam resmi, tanggal, jam, narasumber, link Zoom, dan call-to-action ke clipboard saya untuk siap di-paste ke grup WhatsApp IKMAS.

### F. Moderasi Portofolio Showcase Karya Alumni
19. **US 19**: Sebagai Admin, saya ingin melihat antrean submisi karya alumni yang berstatus `pending` lengkap dengan nama alumni, nomor WA, tahun angkatan, dan deskripsi karya.
20. **US 20**: Sebagai Admin, saya ingin menyetujui (*Approve*) karya dengan 1-klik sehingga otomatis tayang di etalase publik.
21. **US 21**: Sebagai Admin, saya ingin menolak (*Reject*) karya dengan menyertakan catatan revisi personal yang akan tampil di dashboard member yang bersangkutan.
22. **US 22**: Sebagai Admin, saya ingin dapat menandai (*toggle*) karya alumni yang luar biasa sebagai **Karya Unggulan (*Featured*)** agar mendapatkan tempat sorotan khusus di halaman depan.

### G. Direktori Member Alumni & Export Data
23. **US 23**: Sebagai Admin, saya ingin melihat tabel seluruh alumni yang terdaftar di portal dengan rincian nama, email, nomor WhatsApp, tahun angkatan, dan tanggal bergabung.
24. **US 24**: Sebagai Admin, saya ingin dapat mencari member berdasarkan nama atau menyaring berdasarkan tahun angkatan alumni.
25. **US 25**: Sebagai Admin, saya ingin dapat mengklik tombol **"Chat WhatsApp"** di samping data member untuk langsung membuka percakapan WhatsApp Web/App ke nomor alumni tersebut.
26. **US 26**: Sebagai Admin, saya ingin dapat mengunduh seluruh data anggota alumni ke dalam format berkas **CSV / Excel** untuk dokumentasi sekretariat pengurus IKMAS.

## Implementation Decisions

### Arsitektur Navigasi & Antarmuka
- **Dedicated Admin Layout**: Menggunakan layout Blade terpisah (`resources/views/layouts/admin.blade.php`) dengan elemen sidebar samping kiri persisten, breadcrumb atas, status akun admin login, dan area konten utama yang luas.
- **Responsivitas**: Sidebar dapat dilipat (*collapsible*) pada layar mobile atau tablet menggunakan toggle hamburger.

### Skema & Modifikasi Basis Data
- **Tabel `learning_materials`**:
  - Penambahan kolom `is_published` (boolean, default: true) untuk mendukung mekanisme draft/publish.
- **Tabel `showcases`**:
  - Penambahan kolom `is_featured` (boolean, default: false) untuk mendukung sorotan karya unggulan pengurus.

### Logika Ekspor Data & Broadcast WhatsApp
- **Export CSV Member**: Menggunakan stream response native PHP Laravel (`text/csv`) dengan pemisah koma/titik-koma dan UTF-8 BOM agar langsung kompatibel dibuka di Microsoft Excel tanpa error karakter.
- **WhatsApp Broadcast Generator**: Template teks dinamis yang diformat dengan *bolding* WhatsApp (`*teks*`), emoji yang rapi, tautan agenda, dan penutup ramah khas komunitas IKMAS Assalaam.

## Out of Scope

- **Sistem Pembayaran / Tiket Berbayar**: Seluruh kegiatan dan materi bersifat non-komersial untuk alumni.
- **Email Blast Massal Otomatis (SMTP Bulk Mailer)**: Komunikasi utama komunitas alumni bertumpu pada grup WhatsApp, bukan email marketing.
- **Role Permission Granular Bertingkat**: Hanya ada 2 tingkat akses (`member` dan `admin`). Belum diperlukan role bertingkat (seperti editor, super-admin, auditor).

## Further Notes

- Seluruh aksi manipulasi data sensitif (hapus materi, hapus prompt, tolak karya) harus disertai dialog konfirmasi browser untuk mencegah kehilangan data yang tidak disengaja.
- Flash session message dengan notifikasi toast akan memberikan umpan balik instan untuk setiap operasi CRUD yang berhasil.
