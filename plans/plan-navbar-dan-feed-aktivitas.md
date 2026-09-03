# Plan: Navbar Dropdown & Feed Aktivitas Terbaru

> Source PRD: `plans/prd-navbar-dan-feed-aktivitas.md`

## Architectural Decisions

Keputusan yang berlaku lintas semua phase:

- **Routes**: Tidak ada route baru — semua fitur menggunakan route existing (`/`, `/materi`, `/prompts`, `/showcase`, `/agenda`, `/#komunitas-garuda`, WhatsApp external URL).
- **Schema**: Tidak ada perubahan schema/migrasi baru — semua data dari tabel `users`, `showcases`, dan `events` yang sudah ada.
- **Key models**: `User`, `Showcase` (status=approved), `Event` — semuanya sudah ada di `app/Models/`.
- **Layout**: Perubahan navbar ada di `resources/views/layouts/app.blade.php` saja. `admin.blade.php` tidak disentuh.
- **CSS**: Semua style baru ditambahkan ke `public/css/app.css` menggunakan konvensi class existing (`.nav-`, `.badge-`, `.mobile-`). Tidak ada framework CSS baru.
- **JS**: Logika dropdown ditambahkan ke `public/js/app.js` menggunakan vanilla JS, melanjutkan pola yang sudah ada (DOMContentLoaded, event listeners). Tidak ada library baru.
- **Data feed**: Query gabungan dibangun manual di `HomeController` dengan `array_merge` + `usort` pada collection dari 3 model, diambil 5 item terbaru. Menggunakan `Carbon::diffForHumans()` untuk waktu relatif.
- **Badge warna feed**: `.badge-emerald` (Karya Baru), `.badge-cyan` (Member Baru), `.badge-amber` (Event Baru) — memanfaatkan class `.badge` existing.

---

## Phase 1: Navbar Dropdown Desktop & CTA Adaptif

**User stories**: 1, 2, 3, 4, 5, 6, 8

### What to build

Restruktur HTML navbar di `app.blade.php` dari 6 `<li>` flat menjadi:
- `Beranda` — link standalone seperti sekarang
- `Belajar ▾` — dropdown berisi Materi dan Prompts
- `Komunitas ▾` — dropdown berisi Showcase, Agenda, Komunitas Garuda (anchor `/#komunitas-garuda`), dan WhatsApp Community (external, tab baru)

Dropdown terbuka saat hover dengan delay 150ms di desktop menggunakan kombinasi CSS (`opacity`, `visibility`, `pointer-events`) dan JavaScript timer di `app.js`. Setiap dropdown-parent di `<li>` diberi class `.nav-dropdown` sebagai hook CSS/JS.

CTA di `nav-actions` diperbarui:
- **Guest**: "Masuk" (ghost) + "Daftar Alumni" (primary pill)
- **Member**: "Area Member" (secondary pill) + "Keluar" (ghost)
- **Admin**: "Panel Admin" (secondary pill) + "Keluar" (ghost)

Tambahkan CSS baru untuk `.nav-dropdown`, `.nav-dropdown-menu`, `.nav-dropdown-item`, dan state hover/active di `app.css`.

### Acceptance criteria

- [ ] Navbar desktop menampilkan: Beranda | Belajar ▾ | Komunitas ▾ — dan tidak ada item lain di nav-links
- [ ] Hover ke "Belajar" menampilkan dropdown setelah delay ~150ms berisi Materi dan Prompts
- [ ] Hover ke "Komunitas" menampilkan dropdown setelah delay ~150ms berisi Showcase, Agenda, Komunitas Garuda, dan WhatsApp Community
- [ ] WhatsApp Community link membuka tab baru
- [ ] Meninggalkan area dropdown menutup dropdown (dengan delay yang sama)
- [ ] Guest melihat CTA "Masuk" + "Daftar Alumni" di kanan navbar
- [ ] Member yang login melihat "Area Member" + "Keluar" (tombol Daftar Alumni tidak muncul)
- [ ] Admin yang login melihat "Panel Admin" + "Keluar"
- [ ] Active state (highlight) muncul di parent dropdown "Belajar" jika berada di `/materi` atau `/prompts`
- [ ] Active state muncul di parent "Komunitas" jika berada di `/showcase` atau `/agenda`
- [ ] Dropdown tidak muncul di mobile (dihandle di Phase 2)

---

## Phase 2: Mobile Drawer — Flat dengan Section Header

**User stories**: 7, 9

### What to build

Update struktur mobile drawer di `app.blade.php`. Hapus item yang sebelumnya di-duplikasi di `mobile-drawer-footer` untuk autentikasi, ganti dengan struktur flat langsung di `nav-links`:

```
[section header] BELAJAR
  Materi
  Prompts
[section header] KOMUNITAS
  Showcase
  Agenda
  Komunitas Garuda
  WhatsApp Community ↗
[divider]
  Area Member / Panel Admin / Masuk + Daftar
  Keluar
```

Section header "BELAJAR" dan "KOMUNITAS" adalah elemen `<li>` non-clickable dengan class `.nav-drawer-section-header` — styling uppercase, ukuran kecil, warna muted, spacing atas.

Drawer menutup otomatis saat tap di luar area `.nav-links.open` (overlay tap handler di `app.js`). Tambahkan CSS untuk `.nav-drawer-section-header` di `app.css`.

Pastikan semua item di mobile drawer bisa diklik dan mengarah ke route yang benar — termasuk WhatsApp Community link eksternal.

### Acceptance criteria

- [ ] Tap hamburger membuka drawer — tampil Beranda, section header "BELAJAR", Materi, Prompts, section header "KOMUNITAS", Showcase, Agenda, Komunitas Garuda, WhatsApp Community
- [ ] Section header "BELAJAR" dan "KOMUNITAS" tidak bisa diklik (bukan `<a>`)
- [ ] WhatsApp Community membuka tab baru dari mobile drawer
- [ ] Autentikasi CTA (Area Member / Daftar / Keluar) tampil di bagian bawah drawer, konsisten dengan kondisi login
- [ ] Tap di luar drawer (overlay) menutup drawer
- [ ] Tidak ada accordion / expand-collapse — semua item langsung terlihat saat drawer terbuka
- [ ] Dropdown desktop (Phase 1) tidak terpengaruh oleh perubahan ini

---

## Phase 3: Feed 5 Aktivitas Terbaru di Beranda

**User stories**: 10, 11, 12, 13, 14, 15, 16

### What to build

**Controller** (`HomeController`): Tambahkan query untuk `$recentActivity` — gabungan 5 item terbaru dari:
1. `Showcase::where('status', 'approved')->with('user')->latest()->take(5)->get()` → type `showcase`
2. `User::where('role', 'member')->latest()->take(5)->get()` → type `member`
3. `Event::latest()->take(5)->get()` → type `event`

Merge ketiga collection, sort descending berdasarkan `created_at`, ambil 5 teratas. Setiap item dibungkus dalam array standar dengan key: `type`, `title`, `subtitle`, `url`, `created_at`. Pass ke view sebagai `$recentActivity`.

**View** (`home.blade.php`): Sisipkan section baru setelah blok `<!-- Hero Section -->` (sebelum `<!-- Next Upcoming Event Announcement -->`):

Section berisi:
- Heading kecil: "Aktivitas Terbaru"
- Grid/list 5 kartu horizontal — masing-masing kartu adalah `<a>` (full-clickable) berisi:
  - Badge berwarna: `.badge .badge-emerald` "Karya Baru" / `.badge .badge-cyan` "Member Baru" / `.badge .badge-amber` "Event Baru"
  - Nama/judul (bold, 1 baris)
  - Subtitle singkat (muted, truncate 1 baris)
  - Waktu relatif (`Carbon::diffForHumans()`, ukuran kecil)
- Empty state: jika `$recentActivity` kosong, tampilkan pesan "Komunitas baru terbentuk — jadilah yang pertama aktif!" dengan ikon sederhana

**CSS** (`app.css`): Tambahkan class `.activity-feed`, `.activity-card`, `.activity-card-meta` untuk layout dan styling kartu feed.

### Acceptance criteria

- [ ] Section "Aktivitas Terbaru" muncul di Beranda, tepat di bawah Hero dan di atas section Event
- [ ] Menampilkan maksimal 5 item, campuran dari showcase/member/event berdasarkan `created_at` terbaru
- [ ] Setiap kartu menampilkan badge berwarna sesuai tipe aktivitas
- [ ] Kartu Showcase menampilkan: judul karya + nama member yang upload + waktu relatif → link ke `/showcase/{slug}`
- [ ] Kartu Member menampilkan: nama member baru + angkatan alumni + waktu relatif → link ke `/member/dashboard` atau showcase mereka jika ada (fallback ke `#`)
- [ ] Kartu Event menampilkan: nama event + tanggal event + waktu relatif → link ke `/agenda/{slug}`
- [ ] Waktu relatif menggunakan `diffForHumans()` (contoh: "3 jam lalu", "2 hari lalu")
- [ ] Jika tidak ada aktivitas apapun (database kosong), empty state muncul — tidak ada error atau tampilan kosong
- [ ] Tampil responsif di mobile (kartu stack vertikal) dan desktop (bisa horizontal scroll atau grid)
- [ ] Tidak ada query N+1 — relasi di-eager load dengan `with()`
