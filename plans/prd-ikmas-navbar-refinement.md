# PRD: Modernisasi & Perapian Estetika Navigasi (Desktop & Layar HP)

## Problem Statement

Pada implementasi bilah navigasi (*navbar*) sebelumnya:
1. **Redundansi Visual pada Brand**: Logo resmi IKMAS sudah memuat teks kata *"IKMAS"* berukuran besar, namun di sebelahnya terdapat teks berulang *"IKMAS AI Learning Center"*, sehingga terlihat menumpuk dan memakan lebar horizontal secara berlebihan.
2. **Teks Menu Melipat (2-Line Wrapping) di Desktop**: Di resolusi laptop/desktop standar (1280px), ruang navigasi tengah terdesak sehingga label menu seperti *"Materi Belajar"*, *"Prompt Library"*, *"Showcase Karya"*, *"Agenda Event"*, dan *"Hub Komunitas"* terlipat menjadi 2 baris. Hal ini membuat navbar tampak tebal, tidak rata, dan kurang profesional.
3. **Kepadatan Ekstrem di Layar Ponsel (Mobile)**: Pada layar HP (< 768px), semua tombol aksi kanan (*Theme Toggle*, *Portal IKMAS*, *Masuk*, *Daftar Alumni*, dan tombol *Hamburger*) mencoba tampil bersamaan di bilah atas, mengakibatkan tombol saling tumpang-tindih atau terpotong keluar dari layar.

## Solution

Melakukan restrukturisasi menyeluruh pada sistem antarmuka bilah navigasi dengan pendekatan **Responsive & Thumb-Friendly UX**:

1. **Brand Header Elegan (Kiri)**:
   - Menghilangkan duplikasi teks *"IKMAS"*.
   - Menyandingkan logo resmi IKMAS dengan garis pemisah (*divider*) tipis vertikal dan lencana teks modern:
     ```
     [ Logo IKMAS ]  |  AI Learning Center
     ```
2. **Menu Navigasi Desktop 1 Baris Rapih (Tengah)**:
   - Memastikan seluruh menu navigasi berformat 1 baris mutlak (`white-space: nowrap`) tanpa melipat.
   - Menggunakan penamaan menu yang ringkas, modern, dan padat:
     **Beranda** &bull; **Materi** &bull; **Prompts** &bull; **Showcase** &bull; **Agenda** &bull; **Komunitas**
   - Menerapkan efek hover kapsul lembut transparan dengan status aktif kontras yang nyaman dilihat.
3. **Cluster Tombol Aksi Desktop Proporsional (Kanan)**:
   - Tombol **`Portal IKMAS ↗`** bergaya *ghost pill* berbatas tipis warna primer dengan ikon panah keluar minimalis.
   - Tombol **`Masuk`** berbentuk tautan teks minimalis.
   - Tombol **`Daftar Alumni`** berupa tombol solid warna hijau zamrud dengan sudut membulat modern.
   - Memperluas kontainer bilah navigasi menjadi `max-width: 1320px` agar ruang nafas di desktop lapang.
4. **Mobile Navigation Drawer (Layar HP < 768px)**:
   - **Bilah Atas HP**: Ramping dan bersih, hanya memuat Logo IKMAS (tinggi ~32px) + teks ringkas di kiri, serta tombol *Theme Toggle* dan tombol *Hamburger (☰)* di kanan.
   - **Laci Navigasi Meluncur (Slide-down Sheet)**:
     - Daftar menu navigasi vertikal lengkap dengan ikon yang mudah di-tap jempol.
     - Kartu tautan khusus menuju portal pusat: `🌐 Kunjungi Portal Pusat IKMAS (m.ikmas.com) ↗`.
     - Tombol aksi akun lebar penuh (*full-width CTA*) untuk *Masuk* dan *Daftar Alumni* di bagian bawah laci.

## User Stories

### A. Pengguna Laptop / Desktop
1. **US 1**: Sebagai Pengguna Desktop, saya ingin melihat logo IKMAS dan identitas AI Learning Center tersusun proporsional tanpa ada pengulangan kata "IKMAS" dua kali.
2. **US 2**: Sebagai Pengguna Desktop, saya ingin seluruh 6 item menu navigasi tampil rapi dalam 1 baris horizontal tanpa ada teks yang terlipat menjadi 2 baris.
3. **US 3**: Sebagai Pengguna Desktop, saya ingin tombol tautan ke `https://m.ikmas.com/` terlihat jelas dan elegan di sebelah tombol akun tanpa membuat navbar sesak.

### B. Pengguna Smartphone / Mobile
4. **US 4**: Sebagai Pengguna HP, saya ingin bilah atas navbar hanya menampilkan logo, tombol ganti tema, dan tombol menu hamburger sehingga tidak ada tombol yang saling bertumpuk atau terpotong.
5. **US 5**: Sebagai Pengguna HP, saat saya menekan tombol menu hamburger, saya ingin melihat menu navigasi vertikal dengan tombol tap yang besar, tautan ke portal pusat IKMAS, dan tombol Masuk/Daftar yang ramah jempol (*thumb-friendly*).

## Implementation Decisions

### 1. Perubahan CSS (`public/css/app.css`)
- Tambahkan styling `.brand-divider` (garis batas vertikal tipis `border-left: 1px solid var(--border-color)` dengan tinggi 1.5rem).
- Atur `.nav-container` dengan `max-width: 1320px` dan `height: 4.25rem`.
- Pastikan `.nav-link` memiliki `white-space: nowrap`, padding seimbang (`0.5rem 0.75rem`), dan font-size `0.875rem`.
- Buat kelas utilitas `.desktop-only` (tampil di `min-width: 769px`, disembunyikan di `max-width: 768px`).
- Buat kelas utilitas `.mobile-only` (disembunyikan di desktop, tampil di mobile).
- Sempurnakan tampilan `.nav-links.open` di mobile dengan padding lega, item vertikal, pemisah visual, kartu tautan portal pusat, dan tombol aksi full-width.

### 2. Perubahan Markup Blade (`resources/views/layouts/app.blade.php`)
- Perbarui struktur brand: Logo + divider + teks `AI Learning Center`.
- Sesuaikan label menu: `Beranda`, `Materi`, `Prompts`, `Showcase`, `Agenda`, `Komunitas`.
- Pada mobile drawer, tambahkan:
  - Tombol tautan eksternal ke `https://m.ikmas.com/`.
  - Tombol autentikasi (*Masuk* dan *Daftar Alumni* / *Panel Akun*).

### 3. Pengujian Otomatis
- Tambahkan atau perbarui unit/feature tests untuk memvalidasi:
  - Semua label menu baru (`Beranda`, `Materi`, `Prompts`, `Showcase`, `Agenda`, `Komunitas`) ter-render dengan benar.
  - Tautan portal pusat IKMAS tetap ada di header dan drawer mobile.
  - Responsive toggle menu berfungsi saat dibuka di browser subagent.

## Out of Scope

- Merombak isi halaman dalam atau routing backend (hanya perapian antarmuka navbar layout).

## Further Notes

- Tautan ke `https://m.ikmas.com/` tetap mempertahankan `target="_blank"` dan `rel="noopener"`.
- Efek *glassmorphism* di latar belakang navbar menggunakan `backdrop-filter: blur(16px)` untuk estetika modern yang premium.
