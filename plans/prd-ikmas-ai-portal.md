# Product Requirements Document (PRD) — Portal Web IKMAS AI Learning Center

## Problem Statement

Banyak alumni Assalaam yang menyadari pentingnya Artificial Intelligence (AI) untuk pekerjaan, bisnis, dan masa depan mereka, namun menghadapi sejumlah hambatan nyata:
1. **Intimidasi & Rasa Minder**: Merasa AI terlalu teknis, rumit, atau hanya untuk lulusan IT/programmer.
2. **Ketiadaan Repositori Terpusat**: Materi pembelajaran *Study Group*, tips, dan *prompt* berkualitas yang telah dibagikan di grup WhatsApp cepat tenggelam dan sulit dicari kembali oleh anggota baru maupun lama.
3. **Minimnya Panggung Apresiasi (*Showcase*)**: Anggota yang berhasil mempraktikkan AI dan menciptakan karya (tulisan, bot, visual, automasi) belum memiliki wadah resmi untuk memamerkan hasilnya dan menginspirasi sesama alumni.
4. **Hambatan Partisipasi**: Tanpa adanya panduan bertahap dari level pemula (*Beginner*) hingga praktisi (*Practitioner*), banyak alumni memilih pasif dan berisiko tertinggal oleh perkembangan zaman.

Sebagaimana tertuang dalam *Why Statement* IKMAS AI:
> *"Saya percaya bahwa setiap alumni Assalaam seharusnya bisa beradaptasi dengan terus belajar, membuka diri, dan meningkatkan skill — karena tanpa itu, akhirnya mereka tertinggal, tidak adaptif, serta menutup peluang untuk hidup lebih baik."*

---

## Solution

Membangun **Portal Web IKMAS AI Learning Center** berbasis PHP (Laravel) sebagai pusat ekosistem pembelajaran, repositori aset praktis, dan etalase karya alumni yang:
- **Terbuka & Ramah Pemula**: Dapat diakses langsung oleh publik dan alumni tanpa kewajiban login untuk membaca materi, kalender kegiatan, dan menyalin prompt.
- **Repositori Materi Terstruktur**: Menyediakan arsip materi *Study Group* yang diklasifikasikan berdasarkan tingkat kemahiran (*Beginner*, *Explorer*, *Practitioner*) dan pilar topik (*Basics*, *Tools*, *Productivity*, *Workflow*, *Opportunity*).
- **Interactive Prompt Library**: Bank instruksi/prompt produktivitas teruji yang dilengkapi parameter siap pakai dan tombol *1-Click Copy*.
- **Showcase Karya & Dampak Alumni**: Galeri karya nyata alumni Assalaam dengan alur kurasi admin yang memotivasi perubahan dari *consumer* menjadi *creator*.
- **Jembatan Onboarding Komunitas**: Menghubungkan pengunjung web dengan komunitas WhatsApp utama melalui sambutan hangat *Persona Garuda* (Agen Informasi IKMAS AI) dan panduan *"Mulai dari Sini"*.

---

## User Stories

### A. Pengunjung Publik / Alumni Baru (Unauthenticated Guest)
1. **As a** alumni yang baru mengenal AI, **I want** membaca landing page dengan penjelasan yang bersahabat dan tidak intimidatif, **so that** saya merasa percaya diri untuk mulai belajar.
2. **As a** pengunjung, **I want** menyaring materi pembelajaran berdasarkan level (Beginner/Explorer/Practitioner) dan kategori tools (ChatGPT, Claude, Canva AI, Midjourney, dll.), **so that** saya dapat belajar sesuai tahap pemahaman saya saat ini.
3. **As a** pengunjung yang sedang bekerja, **I want** mencari prompt di Prompt Library dan menyalinnya dengan 1 kali klik ke *clipboard*, **so that** saya bisa langsung menggunakannya di tools AI saya tanpa repot memblok teks manual.
4. **As a** alumni yang mencari inspirasi, **I want** melihat direktori Showcase Karya Alumni beserta deskripsi tools dan cerita dampaknya, **so that** saya terdorong bahwa alumni lain dengan latar belakang serupa pun bisa berkarya.
5. **As a** alumni yang ingin hadir di sesi belajar bersama, **I want** melihat jadwal agenda event mendatang lengkap dengan tanggal, jam, narasumber, dan tautan pertemuan (Zoom/Google Meet), **so that** saya dapat mengatur waktu untuk bergabung.
6. **As a** pengunjung yang ingin bergabung ke grup diskusi harian, **I want** menemukan tombol ajakan gabung ke Komunitas WhatsApp resmi IKMAS AI, **so that** saya dapat terhubung langsung dengan sesama alumni.
7. **As a** pengguna yang membuka web di malam hari atau sensitif terhadap cahaya, **I want** mengganti tampilan antara Light Mode dan Dark Mode dengan cepat, **so that** mata saya tetap nyaman saat membaca materi panjang.

### B. Member Alumni Terdaftar (Authenticated Member)
8. **As a** alumni Assalaam, **I want** mendaftarkan akun menggunakan nama, email, nomor WhatsApp aktif, dan tahun angkatan/kelulusan, **so that** identitas saya sebagai bagian dari keluarga alumni terdata dengan jelas.
9. **As a** alumni yang telah membuat karya dengan AI, **I want** mengisi formulir submisi karya (judul, ringkasan, tools AI yang digunakan, tangkapan layar/preview, URL proyek, dan cerita manfaat), **so that** karya saya dapat dikurasi oleh admin untuk dipajang di Showcase.
10. **As a** member yang telah mengirim karya, **I want** melihat status kurasi karya saya (Pending / Approved / Rejected) di dashboard profil, **so that** saya mengetahui progres peninjauan karya saya secara transparan.
11. **As a** member, **I want** menandai (*bookmark*) prompt atau modul materi favorit saya, **so that** saya dapat membukanya kembali dengan cepat dari menu dashboard saya.
12. **As a** member yang mengalami perubahan data, **I want** memperbarui nomor WhatsApp, tautan portofolio/LinkedIn, dan foto profil saya, **so that** profil kontributor saya selalu akurat.
13. **As a** member yang lupa kata sandi, **I want** memiliki mekanisme pemulihan/reset kata sandi yang aman, **so that** saya tidak kehilangan akses ke akun saya.

### C. Pengurus / Administrator Komunitas (Admin)
14. **As a** admin komunitas, **I want** masuk ke dashboard khusus admin yang dilindungi autentikasi, **so that** saya dapat mengelola seluruh konten website dengan aman.
15. **As a** admin konten, **I want** menambah, mengubah, menerbitkan (*publish*), atau mengarsipkan materi *Study Group* lengkap dengan durasi baca, tingkat level, dan tautan rekaman video/slide, **so that** repositori materi selalu relevan dan rapi.
16. **As a** pengurus, **I want** mengelola Prompt Library (menambah kategori peran profesi, teks prompt dengan placeholder variabel, dan instruksi penggunaan), **so that** anggota mendapatkan prompt yang terstandarisasi.
17. **As a** pengurus event, **I want** menjadwalkan agenda Study Group atau Workshop baru, mencantumkan profil pemateri, dan memperbarui status event dari *Upcoming* menjadi *Completed*, **so that** informasi kegiatan selalu mutakhir.
18. **As a** kurator komunitas, **I want** meninjau (*review*) daftar submisi karya alumni yang masuk, menyetujui (*Approve*) karya yang layak tayang, atau memberikan catatan revisi/alasan penolakan (*Reject*), **so that** etalase showcase tetap berkualitas dan bebas dari spam.
19. **As a** admin, **I want** melihat ringkasan metrik (total member terdaftar, total materi, total prompt, total karya aktif, dan antrean kurasi), **so that** saya bisa memantau pertumbuhan dan keaktifan portal secara berkala.

### D. Edge Cases & Penanganan Kesalahan
20. **As a** pengunjung yang mencoba mengakses halaman yang tidak ada (404), **I want** melihat halaman error yang ramah dengan tautan kembali ke Beranda atau Materi Belajar, **so that** saya tidak tersesat.
21. **As a** member yang gagal mengunggah gambar karya karena ukuran terlalu besar atau format tidak sesuai, **I want** menerima pesan validasi yang jelas mengenai batas ukuran dan format yang didukung.
22. **As a** pengunjung dengan koneksi lambat, **I want** antarmuka tetap memuat teks materi secara cepat (*lightweight layout*), **so that** pengalaman membaca tidak terganggu.

---

## Implementation Decisions

### 1. Architectural & Technology Decisions
- **Framework**: PHP 8.4 dengan **Laravel 11**, memanfaatkan arsitektur MVC bawaan yang kokoh, sistem routing ekspresif, dan Blade templating engine.
- **Database Engine**: **SQLite** untuk fase pengembangan lokal dan prototipe cepat tanpa dependensi instalasi server database eksternal, dengan skema migrasi Eloquent yang 100% *drop-in compatible* jika dipindahkan ke MySQL/MariaDB pada server produksi.
- **Frontend & Styling**: Blade Components dengan kustom CSS modern + Tailwind utility principles. Desain responsif (*mobile-first*) dengan sistem **Dual Theme** (Light Theme sebagai bawaan ramah pemula dan Dark Theme untuk nuansa teknologi canggih).
- **Brand Palette**:
  - *Primary*: Deep Navy (`#0B192C` / `#1E293B`) & Electric Blue (`#2563EB` / `#3B82F6`).
  - *Accent*: Cyan / Tech Blue (`#06B6D4`) untuk highlight interaktif.
  - *Typography*: Google Fonts *Plus Jakarta Sans* untuk keterbacaan modern dan bersih.
- **State Management & Interactivity**: Vanilla JavaScript modular untuk penanganan toggle tema (tersimpan di `localStorage`), interaksi *1-Click Copy Prompt* dengan visual *toast notification*, filter dinamis pada katalog materi/prompt, dan pratinjau unggahan berkas.

### 2. Core Modules & Data Schemas (High-Level)
- **Modul Pengguna & Autentikasi**:
  - Menyimpan kredensial standar (nama, email terverifikasi, kata sandi terenkripsi).
  - Atribut khusus komunitas: Nomor WhatsApp (format internasional), Tahun Angkatan Alumni Assalaam, Biografi singkat, dan Role (`admin` atau `member`).
- **Modul Kategori & Taksonomi**:
  - Relasi fleksibel untuk mengelompokkan materi, prompt, dan showcase (misal: *Produktivitas*, *Copywriting & Konten*, *Riset & Analisis*, *Automasi & Workflow*, *Pengembangan Bisnis*).
- **Modul Materi Belajar (Learning Resources)**:
  - Menyimpan judul, slug URL ramah SEO, tingkat keahlian (*Beginner*, *Explorer*, *Practitioner*), pilar konten (*Basics*, *Tools*, *Productivity*, *Workflow*, *Opportunity*), ringkasan pendek, isi materi lengkap (format Markdown/HTML bersih), estimasi waktu baca, serta tautan video rekaman atau berkas slide jika tersedia.
- **Modul Prompt Library**:
  - Menyimpan judul kegunaan, peran target (misal: *Copywriter*, *Guru*, *Pebisnis*, *Programmer*), tools sasaran (misal: *ChatGPT*, *Claude*, *Cursor*), teks template prompt dengan variabel penanda yang jelas (contoh: `[Masukkan Topik]`), instruksi cara penggunaan, dan tag kata kunci.
- **Modul Showcase Karya Alumni**:
  - Berelasi dengan member pembuat karya.
  - Menyimpan judul proyek, deskripsi karya, daftar tools AI yang digunakan, gambar tangkapan layar/pratinjau, tautan langsung ke hasil karya/proyek, cerita dampak nyata (*impact story*), serta status publikasi (*pending*, *approved*, *rejected*).
- **Modul Agenda Kegiatan (Events)**:
  - Menyimpan judul acara, tanggal & waktu pelaksanaan, durasi sesi, tautan lokasi/ruang virtual (Zoom/Meet), nama dan profil pemateri/fasilitator, tautan materi, dan status acara (*upcoming* vs *completed*).
- **Modul Bookmark / Favorit**:
  - Menyimpan relasi penanda materi dan prompt favorit bagi member yang sedang login.

### 3. Workflow & Specific Interactions
- **Alur Kurasi Showcase Karya**:
  1. Member login mengisi form pengajuan karya baru.
  2. Status submisi langsung diset ke `pending` dan muncul di antrean verifikasi Admin Dashboard.
  3. Karya belum tampil di halaman publik hingga Admin menekan tombol `Approve`.
  4. Begitu disetujui, karya otomatis muncul di galeri publik dengan lencana nama dan angkatan pembuat.
- **Alur Copy Prompt Interaktif**:
  1. Setiap kartu prompt memiliki cuplikan teks dan tombol salin.
  2. Saat diklik, teks lengkap disalin ke clipboard pengguna menggunakan Clipboard API browser.
  3. Tombol berubah status menjadi tercentang dan notifikasi pop-up (*toast*) muncul: *"Prompt berhasil disalin!"*.
- **Alur Onboarding Persona Garuda**:
  1. Bagian khusus di halaman beranda menampilkan visual dan narasi ramah dari Agen Garuda.
  2. Memberikan 3 opsi langkah awal: (a) Mulai dari Panduan Pemula, (b) Jelajahi Koleksi Prompt, (c) Masuk ke Grup WhatsApp IKMAS AI.

---

## Out of Scope (Eksplisit Dikecualikan untuk Fase Ini)

1. **Mesin Bot WhatsApp Otomatis di Dalam Web**: Pengiriman pesan berkala sequence Garuda ke grup WA tetap berjalan melalui instrumen/bot WhatsApp terpisah sesuai panduan operasional grup; web hanya bertindak sebagai portal landing dan tautan undangan grup.
2. **Sistem Forum Obrolan Real-time (Chat / Live Messaging)**: Diskusi harian dan tanya-jawab tetap dipusatkan di Grup WhatsApp Komunitas agar tidak memecah fokus interaksi anggota.
3. **Sistem Pembayaran / Payment Gateway Terintegrasi**: Seluruh materi, pendaftaran event Study Group, dan akses prompt disediakan secara gratis untuk ekosistem alumni Assalaam.
4. **Platform Hosting Video Internal (Self-hosted Video Streaming)**: Rekaman sesi Study Group diunggah di YouTube Unlisted atau Google Drive, dan hanya disematkan (*embedded*) atau ditautkan melalui web portal.

---

## Further Notes & Future Roadmap

- **Deployment Readiness**: Struktur direktori dan konfigurasi database dirancang agar mudah di-*deploy* ke berbagai lingkungan produksi: cPanel shared hosting standar (dengan migrasi SQLite ke MySQL), VPS (seperti Ubuntu + Nginx/Apache), maupun platform PaaS.
- **Data Seeding**: Sistem akan menyertakan *seeder* data awal yang langsung mengimpor materi nyata dari dokumen strategi IKMAS AI (topik Study Group #1 "AI untuk Produktivitas Sehari-hari", prompt produktivitas esensial, agenda peluncuran, dan sampel proyek alumni).
- **Fase Berikutnya (Post-Launch)**:
  - Pelacakan analitik sederhana untuk materi paling banyak dibaca dan prompt paling sering disalin.
  - Integrasi formulir pendaftaran alumni otomatis dengan verifikasi nomor WhatsApp OTP jika keanggotaan berkembang pesat.
