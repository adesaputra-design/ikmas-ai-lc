# Plan: Modernisasi & Perapian Estetika Navigasi (Desktop & Mobile)

> Source PRD: [plans/prd-ikmas-navbar-refinement.md](file:///d:/Ade%20Zone/3.%20AK%20Studio%20%232/IKMAS%20AILC/plans/prd-ikmas-navbar-refinement.md)

## Architectural decisions

- **Container Width**: `.nav-container` diperluas dari 1200px menjadi `1320px` untuk memberikan ruang nafas lapang di layar desktop 1280px+.
- **Breakpoint Responsif**: `768px` sebagai batas pemisah antara Desktop View (horizontal bar) dan Mobile View (topbar ramping + slide-down sheet).
- **Kelas Utilitas Tampilan**:
  - `.desktop-only`: Ditampilkan pada layar `>= 769px`, disembunyikan pada `< 768px`.
  - `.mobile-only`: Disembunyikan pada layar `>= 769px`, ditampilkan pada `< 768px`.
- **Integrasi Tautan Portal**: Tautan `https://m.ikmas.com/` tetap memakai atribut `target="_blank"` dan `rel="noopener"`.
- **Palet Warna**: Konsisten dengan tema **Assalaam Forest Green (`#006837`)** dan **Warm Gold (`#FDB813`)**.

---

## Phase 1: Desktop Brand Header & Navigation Streamlining

**User stories**: US 1, US 2

### What to build
Memperbarui area brand sisi kiri dan menu navigasi tengah di layar desktop:
1. Menghilangkan duplikasi teks kata "IKMAS" pada brand header. Menyandingkan logo resmi IKMAS dengan divider vertikal elegan dan lencana tipografi modern `AI Learning Center`.
2. Menghilangkan masalah teks melipat (2-line wrapping) pada 6 menu navigasi dengan menetapkan `white-space: nowrap`, label ringkas (`Beranda`, `Materi`, `Prompts`, `Showcase`, `Agenda`, `Komunitas`), serta efek hover soft-pill yang halus.

### Acceptance criteria
- [ ] Brand header menampilkan logo IKMAS dan teks `AI Learning Center` tanpa pengulangan kata "IKMAS" ganda.
- [ ] Seluruh 6 item menu navigasi tampil dalam 1 baris horizontal rapi tanpa ada teks yang terlipat di resolusi 1280px.
- [ ] Status menu aktif ditandai dengan latar belakang pill hijau lembut (`--primary-light`) dan teks primer yang kontras.
- [ ] Pengujian otomatis memvalidasi elemen brand dan link menu navigasi.

---

## Phase 2: Desktop Action Cluster & External Portal Pill

**User stories**: US 3

### What to build
Menata ulang cluster tombol aksi di sisi kanan navbar desktop agar seimbang, proporsional, dan tidak mendesak menu tengah:
1. Tombol `Portal IKMAS ↗` berformat ghost pill berbatas tipis warna primer dengan ikon panah minimalis.
2. Tombol `Masuk` berbentuk ghost button minimalis.
3. Tombol `Daftar Alumni` berupa solid button warna hijau zamrud dengan micro-shadow.
4. Tombol switch tema (Light/Dark) tetap berada di samping tombol portal.

### Acceptance criteria
- [ ] Tombol `Portal IKMAS ↗` tampil proporsional dan membuka `https://m.ikmas.com/` di tab baru.
- [ ] Tombol `Masuk` dan `Daftar Alumni` tersusun seimbang tanpa berebut ruang dengan menu navigasi.
- [ ] Tampilan bilah navigasi desktop memiliki estetika modern dan harmonis di Light dan Dark mode.

---

## Phase 3: Mobile-First Topbar & Slide-Down Drawer

**User stories**: US 4, US 5

### What to build
Membangun pengalaman navigasi mobile (< 768px) yang bersih dan ramah jempol (*thumb-friendly*):
1. **Bilah Atas HP**: Ramping, hanya menampilkan Logo IKMAS + teks ringkas di kiri, serta tombol *Theme Toggle* dan tombol *Hamburger (☰)* di kanan. Seluruh tombol yang menjejali bar disembunyikan ke dalam drawer.
2. **Laci Navigasi Meluncur (Slide-Down Drawer)**:
   - Daftar 6 menu navigasi vertikal berjarak tap jempol yang nyaman (44px+ touch target).
   - Kartu tautan khusus portal pusat: `🌐 Kunjungi Portal Pusat IKMAS (m.ikmas.com) ↗`.
   - Tombol aksi akun lebar penuh (*full-width button*) untuk *Masuk* dan *Daftar Alumni*.
3. Skrip interaksi buka/tutup laci menu yang mulus (*smooth toggle*).

### Acceptance criteria
- [ ] Di layar HP (< 768px), navbar atas bersih tanpa tombol bertumpuk atau terpotong.
- [ ] Menekan tombol hamburger membuka laci navigasi vertikal dengan transisi halus.
- [ ] Di dalam laci navigasi terdapat tautan `https://m.ikmas.com/` dan tombol akun yang mudah di-tap jempol.
- [ ] Seluruh suite pengujian otomatis lulus 100% dan terverifikasi di browser subagent.
