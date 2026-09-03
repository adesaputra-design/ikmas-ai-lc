# Plan: Navbar Modern (Desktop Dropdown & Mobile Offcanvas m.ikmas.com Style)

> Source PRD: `plans/prd-navbar-dropdown-offcanvas.md`

## Architectural Decisions

- **Layout file**: `resources/views/layouts/app.blade.php` memuat 2 blok navigasi utama yang terpisah:
  1. Desktop nav: `<div class="desktop-nav-menu desktop-only">`
  2. Mobile offcanvas: `<div class="ikmas-offcanvas" id="mobileOffcanvas">` + backdrop `<div class="offcanvas-backdrop" id="offcanvasBackdrop">`
- **Zero Framework Dependency**: Murni CSS vanilla modern + vanilla JS.
- **Scroll behavior**: Navbar sticky dengan utility class `.navbar-scrolled-hidden` saat scroll down dan remove class saat scroll up.
- **Routing & Icon Mapping**: Menjaga semua rute tetap sama dan menggunakan icon SVG inline.

---

## Phase 1: Struktur HTML Navbar Desktop Dropdown & Mobile Offcanvas

**User stories**: 1, 2, 4, 6

### What to build
Restrukturisasi markup header di `resources/views/layouts/app.blade.php`:
1. Navigasi desktop:
   - Hapus link "Beranda" teks (logo tetap link ke home).
   - Buat dropdown "Belajar ▾" (Materi, Prompts).
   - Buat dropdown "Komunitas ▾" (Showcase, Agenda, Tentang Kami, Komunitas Garuda, WhatsApp Community).
   - Action cluster desktop (Tema, Portal Pusat, Auth button adaptif).
2. Mobile Hamburger button & Offcanvas:
   - Hamburger button memicu `openOffcanvas()`.
   - Wadah Offcanvas di sisi kanan (`ikmas-offcanvas`):
     - Header offcanvas: Logo/Title "IKMAS AI" + tombol Close (X).
     - Body offcanvas: Daftar menu vertikal dengan icon SVG (Home, BELAJAR header, Materi, Prompts, KOMUNITAS header, Showcase, Agenda, Tentang Kami, Komunitas Garuda, WhatsApp).
     - Footer offcanvas: Link Portal Pusat + Tombol Auth adaptif.
   - Backdrop overlay di luar drawer.

### Acceptance criteria
- [ ] Terdapat markup desktop terpisah dan mobile offcanvas terpisah di `app.blade.php`.
- [ ] Dropdown desktop memuat sub-menu Belajar dan Komunitas yang lengkap.
- [ ] Mobile offcanvas memuat seluruh menu dengan icon SVG dan pemisah section header.
- [ ] Link eksternal (WhatsApp & Portal Pusat) dilengkapi atribut `target="_blank"` dan `rel="noopener"`.

---

## Phase 2: Styling CSS (Dropdown Hover, Offcanvas Slide-in, Sticky Header)

**User stories**: 1, 3, 5

### What to build
Penambahan CSS di `public/css/app.css`:
1. Dropdown desktop:
   - Styling `.nav-dropdown`, `.nav-dropdown-menu`, panah indikator, item hover, dan active state.
   - Delay transisi 150ms agar mouse keluar-masuk terasa mulus.
2. Mobile Offcanvas (Right-side slide):
   - `.ikmas-offcanvas`: `position: fixed; top: 0; right: 0; width: 300px; height: 100vh; transform: translateX(100%); transition: transform 0.3s ease; z-index: 1050;`
   - `.ikmas-offcanvas.active`: `transform: translateX(0);`
   - `.offcanvas-backdrop`: overlay gelap dengan transisi opacity.
   - Styling icon SVG pendukung item offcanvas agar rata dan proporsional.
3. Sticky auto-hide/show:
   - `.navbar`: `position: sticky; top: 0; transition: transform 0.3s ease;`
   - `.navbar.navbar-hidden`: `transform: translateY(-100%);`

### Acceptance criteria
- [ ] Desktop dropdown tampil rapi, melayang di bawah menu saat hover, dengan shadow dan border radius konsisten.
- [ ] Di layar mobile (< 992px), nav desktop tersembunyi dan hamburger muncul.
- [ ] Offcanvas berada di sisi kanan dan tersembunyi secara default.
- [ ] Saat dibuka, offcanvas bergeser mulus dari kanan ke kiri bersama backdrop.

---

## Phase 3: JavaScript Behavior & Interaksi

**User stories**: 1, 3, 5

### What to build
Pembaruan interaktivitas di `public/js/app.js`:
1. Offcanvas toggle:
   - Buka saat klik hamburger button.
   - Tutup saat klik tombol close (X), backdrop overlay, atau tombol ESC.
   - Kunci scroll body (`document.body.style.overflow = 'hidden'`) saat offcanvas terbuka.
2. Desktop Dropdown hover timer:
   - Implementasi timeout 150ms pada event `mouseenter` dan `mouseleave`.
3. Sticky Scroll Handler:
   - Deteksi arah scroll (`window.scrollY` vs `lastScrollY`). Jika scroll down > 80px, tambahkan class `.navbar-hidden`. Jika scroll up, hapus class.

### Acceptance criteria
- [ ] Klik hamburger membuka offcanvas dari kanan dengan backdrop.
- [ ] Klik close button atau klik area luar menutup drawer dengan lancar.
- [ ] Scroll body terkunci saat drawer terbuka.
- [ ] Scroll ke bawah menyembunyikan navbar, scroll ke atas menampilkannya kembali.
- [ ] Tidak ada error di console JavaScript browser.
