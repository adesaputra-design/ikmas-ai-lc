# PRD: Navbar Modern (Desktop Dropdown & Mobile Offcanvas m.ikmas.com Style)

## Problem Statement

Navbar IKMAS AI Learning Center saat ini menggunakan pola navigasi datar (flat 6-7 link) yang kurang ringkas di desktop dan drawer mobile yang kurang selaras dengan pola portal induk IKMAS (https://m.ikmas.com). Pengguna mobile memerlukan pengalaman navigasi yang lebih ergonomis, familiar, dan visual (offcanvas slide dari kanan dengan ikon pendukung per item), sementara navigasi desktop perlu dikelompokkan ke dalam dropdown berbasis prioritas belajar dan komunitas dengan menyederhanakan link Beranda (karena sudah diwakili oleh logo).

## Solution

1. **Desktop Navbar**:
   - Menghapus link teks "Beranda" karena logo brand di kiri sudah mengarah ke homepage (`/`).
   - Mengelompokkan navigasi utama menjadi 2 dropdown bersih:
     - **Belajar ▾**: Materi (`/materi`), Prompts (`/prompts`).
     - **Komunitas ▾**: Showcase (`/showcase`), Agenda (`/agenda`), Tentang Kami (`/tentang`), Komunitas Garuda (`/#komunitas-garuda`), WhatsApp Community (`https://chat.whatsapp.com/...` eksternal).
   - Dropdown menggunakan hover dengan delay 150ms di desktop (halus, tidak sengaja terbuka saat kursor lewat).
   - CTA adaptif sesuai autentikasi:
     - Guest: Tombol "Masuk" + "Daftar Alumni".
     - Member: Tombol "Area Member" + "Keluar".
     - Admin: Tombol "Panel Admin" + "Keluar".

2. **Mobile Nav (m.ikmas.com Style)**:
   - Pola **Offcanvas dari sisi kanan (`offcanvas-end`)** dengan pure CSS + vanilla JS (zero dependency, tanpa mengimpor bootstrap penuh).
   - Animasi smooth slide-in `transform: translateX(100%) -> translateX(0)` didukung backdrop semi-transparan dan tap-outside/swipe-to-close.
   - Menggunakan markup HTML terpisah dari desktop untuk menjaga keterbacaan kode dan kemudahan maintenance.
   - Menampilkan ikon SVG inline di setiap item menu:
     - Beranda (🏠 house)
     - — BELAJAR — (section header teks kapital, muted, tanpa ikon)
     - Materi (📖 book-open)
     - Prompts (✨ sparkle/zap)
     - — KOMUNITAS — (section header teks kapital, muted, tanpa ikon)
     - Showcase (🏆 trophy/award)
     - Agenda (📅 calendar)
     - Tentang Kami (ℹ️ info circle)
     - Komunitas Garuda (👥 users)
     - WhatsApp Community (💬 message-circle, label eksternal)
   - Bagian bawah drawer offcanvas: Akses Portal Pusat IKMAS (m.ikmas.com) + Tombol Auth adaptif (Login/Register atau Area Member/Logout).

3. **Behavior Navbar (Sticky on Scroll)**:
   - Navbar sticky dengan transisi cerdas: auto-hide saat scroll ke bawah dan auto-show saat scroll ke atas agar tidak memakan ruang layar HP.

## User Stories

1. Sebagai **pengguna mobile**, saya ingin membuka menu hamburger dan melihat panel offcanvas muncul dari kanan mirip aplikasi modern / m.ikmas.com, sehingga terasa konsisten dengan ekosistem IKMAS.
2. Sebagai **pengguna mobile**, saya ingin setiap menu memiliki ikon visual yang jelas serta pemisah grup (BELAJAR dan KOMUNITAS), sehingga saya bisa menemukan menu yang dicari dengan cepat.
3. Sebagai **pengguna mobile**, saya ingin menutup offcanvas dengan menekan tombol silang (X) atau mengetuk area luar backdrop.
4. Sebagai **pengunjung desktop**, saya ingin navbar terlihat ringkas dan fokus (hanya Belajar dan Komunitas), karena klik pada logo sudah cukup membawa saya kembali ke Beranda.
5. Sebagai **pengunjung desktop**, saya ingin menu dropdown terbuka saat mouse diarahkan (hover) dengan jeda halus (150ms) dan tidak mendadak hilang saat mouse digerakkan sedikit.
6. Sebagai **member/admin**, saya ingin melihat tombol aksi di navbar dan di mobile drawer yang relevan dengan status saya (misalnya langsung ke Area Member, bukan ajakan Daftar Alumni).

## Implementation Decisions

- **Markup Layout**: Memisahkan container desktop menu dan container mobile offcanvas di `resources/views/layouts/app.blade.php`.
- **Styling**: Menambahkan styling CSS offcanvas, dropdown hover-delay, dan nav-item icons di `public/css/app.css`.
- **Script**: Memperbarui logika di `public/js/app.js` untuk mengontrol buka-tutup offcanvas, backdrop, scroll-lock pada body saat menu terbuka, dan sticky smart-hide/show saat scroll.
- **Tanpa Dependency Luar**: Menggunakan SVG inline standar dan vanilla JS modern.

## Out of Scope

- Instalasi library CSS pihak ketiga seperti Bootstrap 5 penuh.
- Perubahan routing URL atau backend database.
