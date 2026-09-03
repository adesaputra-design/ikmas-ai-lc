# PRD: Penyelarasan Identitas Brand Logo IKMAS (Tone Warna & Integrasi Ekosistem)

## Problem Statement

Portal komunitas IKMAS AI Learning Center sebelumnya menggunakan skema warna standar bernuansa Deep Navy (`#1e40af`) dan Electric Blue (`#2563eb`). Warna ini belum mencerminkan identitas visual resmi **IKMAS (Ikatan Alumni Ma'had Assalaam)** yang memiliki lambang resmi berciri khas warna **Hijau Hutan Zamrud (*Islamic Forest Green*)** dan **Kuning Emas Kubah (*Warm Islamic Gold*)**.

Selain itu, portal saat ini belum menautkan dan menyematkan logo resmi IKMAS secara terpadu di halaman depan, sehingga pengunjung atau alumni baru belum melihat secara langsung keterhubungan resmi (*official affiliation*) antara portal pembelajaran AI ini dengan ekosistem pusat Ikatan Alumni Ma'had Assalaam di [https://m.ikmas.com/](https://m.ikmas.com/).

## Solution

Melakukan pembaruan menyeluruh pada sistem desain dan antarmuka web:
1. **Transformasi Desain Sistem Warna**:
   - Menetapkan warna **Hijau Assalaam** (`#006837` / `#007a3d`) sebagai warna primer (`--primary`) untuk tombol, link aktif, border fokus, dan aksen navigasi.
   - Menetapkan warna **Kuning Emas** (`#fdb813` / `#f59e0b`) sebagai warna aksen sekunder untuk highlight bintang featured, glowing card effects, dan gradasi visual.
   - Menyesuaikan tema gelap (*Dark Mode*) dengan varian hijau zamrud modern (`#10b981`) dan emas menyala (`#fbbf24`).
2. **Integrasi Logo Resmi IKMAS & Tautan Portal Pusat**:
   - Memasang berkas logo resmi IKMAS di seluruh portal (`public/images/ikmas-logo.png`).
   - **Bilah Navigasi (Navbar)**: Logo resmi IKMAS disematkan di sisi brand header beserta tombol tautan langsung **"Portal Pusat IKMAS ↗"** ke [https://m.ikmas.com/](https://m.ikmas.com/).
   - **Hero Section Beranda**: Lencana resmi di atas judul utama *"Inisiatif Resmi Ikatan Alumni Ma'had Assalaam"* dengan logo IKMAS yang dapat diklik ke [https://m.ikmas.com/](https://m.ikmas.com/).
   - **Footer Web**: Logo resmi IKMAS dengan keterangan organisasi dan tautan eksternal ke [m.ikmas.com](https://m.ikmas.com/).
   - **Panel Admin Sidebar**: Logo resmi IKMAS disematkan pada header sidebar pengurus.

## User Stories

### A. Pengalaman Visual & Identitas Brand
1. **US 1**: Sebagai Pengunjung & Alumni, saya ingin melihat warna hijau tua dan emas khas Assalaam di seluruh elemen website (tombol, lencana, banner, header), sehingga saya langsung merasakan kebanggaan dan keaslian identitas almamater.
2. **US 2**: Sebagai Pengguna Dark Mode, saya ingin tampilan tema gelap berpadu harmonis dengan hijau zamrud dan emas menyala yang nyaman di mata dan tetap modern.

### B. Penautan ke Ekosistem Pusat IKMAS
3. **US 3**: Sebagai Pengunjung di Beranda, saya ingin melihat lencana resmi organisasi IKMAS di Hero Section yang dapat saya klik untuk mengunjungi situs pusat [https://m.ikmas.com/](https://m.ikmas.com/).
4. **US 4**: Sebagai Pengguna di Navbar, saya ingin melihat logo resmi IKMAS dan memiliki akses tombol cepat "Portal Pusat IKMAS ↗" untuk berpindah ke web utama alumni.
5. **US 5**: Sebagai Pengunjung di Footer, saya ingin melihat logo resmi IKMAS dan link portal resmi di bagian footer website.
6. **US 6**: Sebagai Pengurus di Panel Admin, saya ingin melihat logo resmi IKMAS pada sidebar admin dengan nuansa hijau-emas yang konsisten.

## Implementation Decisions

### Berkas Aset & CSS Tokens
- **Logo Asset**: `public/images/ikmas-logo.png`.
- **CSS Tokens (`public/css/app.css`)**:
  - Light mode:
    - `--primary`: `#006837`
    - `--primary-hover`: `#00502a`
    - `--primary-light`: `#f0fdf4`
    - `--primary-border`: `#bbf7d0`
    - `--accent-gold`: `#fdb813`
    - `--accent-gold-hover`: `#d97706`
    - `--shadow-glow`: `0 0 20px rgba(0, 104, 55, 0.2)`
  - Dark mode:
    - `--primary`: `#10b981`
    - `--primary-hover`: `#34d399`
    - `--primary-light`: `rgba(16, 185, 129, 0.15)`
    - `--primary-border`: `rgba(16, 185, 129, 0.3)`
    - `--accent-gold`: `#fbbf24`

### Modifikasi Tampilan Blade
- `resources/views/layouts/app.blade.php`: Navbar brand dengan logo IKMAS dan link "Portal Pusat IKMAS ↗" ke `https://m.ikmas.com/`, footer dengan logo IKMAS.
- `resources/views/home.blade.php`: Hero section badge "Inisiatif Resmi Ikatan Alumni Ma'had Assalaam" dengan link ke `https://m.ikmas.com/`.
- `resources/views/layouts/admin.blade.php`: Sidebar header dengan logo IKMAS.

## Out of Scope

- Mengubah sistem single sign-on (SSO) langsung dengan database akun `m.ikmas.com` (cukup penautan eksternal ke domain).
- Mengubah struktur basis data modul atau konten materi.

## Further Notes

- Seluruh tautan ke `https://m.ikmas.com/` harus menggunakan atribut `target="_blank"` dan `rel="noopener"` untuk keamanan dan kenyamanan browsing pengguna.
