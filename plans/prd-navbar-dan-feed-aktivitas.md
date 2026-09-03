# PRD: Navbar Dropdown & Feed Aktivitas Terbaru

## Problem Statement

Navbar IKMAS AI Learning Center saat ini menampilkan 6 item secara flat (Beranda, Materi, Prompts, Showcase, Agenda, Komunitas), tanpa hierarki visual dan tanpa adaptasi berdasarkan status login user. Ini tidak mencerminkan alur perjalanan alumni yang menjadi inti konsep produk — dari "belum tahu" sampai "aktif di komunitas". Selain itu, halaman Beranda belum memunculkan sinyal kehidupan komunitas sejak user pertama kali landing, sehingga terasa seperti direktori konten statis.

## Solution

1. **Navbar dikelompokkan menjadi dropdown** — 6 item flat diorganisir menjadi: Beranda (standalone) + dropdown "Belajar" (Materi, Prompts) + dropdown "Komunitas" (Showcase, Agenda, Komunitas Garuda, WhatsApp Community). CTA di kanan navbar bersifat adaptif: guest melihat "Masuk" + "Daftar Alumni", member melihat "Area Member" + "Keluar", admin melihat "Panel Admin" + "Keluar".

2. **Feed 5 Aktivitas Terbaru di Beranda** — section baru ditempatkan tepat setelah Hero, menampilkan gabungan 5 aktivitas terbaru dari 3 jenis: karya baru di-approve (Showcase), member baru bergabung (User), dan event baru ditambahkan (Event). Setiap item ditampilkan sebagai medium card dengan badge berwarna, nama, preview konten, dan waktu relatif.

## User Stories

### Navbar
1. Sebagai **pengunjung (guest)**, saya ingin melihat navbar yang rapi dan terstruktur, sehingga saya bisa menemukan konten yang relevan dengan mudah tanpa harus membaca semua item satu per satu.
2. Sebagai **guest**, saya ingin bisa hover ke "Belajar" di navbar dan melihat sub-menu Materi dan Prompts, sehingga saya tahu apa saja konten belajar yang tersedia.
3. Sebagai **guest**, saya ingin bisa hover ke "Komunitas" di navbar dan melihat sub-menu Showcase, Agenda, Komunitas Garuda, dan link WhatsApp Community, sehingga saya bisa langsung bergabung ke komunitas aktif.
4. Sebagai **guest**, saya ingin tombol "Daftar Alumni" tampil menonjol di navbar, sehingga saya tahu langkah pertama untuk bergabung.
5. Sebagai **member yang sudah login**, saya ingin melihat tombol "Area Member" menggantikan "Daftar Alumni" di navbar, sehingga saya bisa langsung akses dashboard tanpa harus mencari-cari.
6. Sebagai **admin yang sudah login**, saya ingin melihat tombol "Panel Admin" di navbar, sehingga saya bisa langsung akses panel admin.
7. Sebagai **pengguna mobile**, saya ingin membuka menu hamburger dan langsung melihat semua item navigation secara flat dengan label grup "BELAJAR" dan "KOMUNITAS" sebagai section header, sehingga saya tidak perlu tap berkali-kali untuk membuka accordion.
8. Sebagai **pengguna desktop**, saya ingin dropdown terbuka saat hover dengan delay 150ms, sehingga dropdown tidak terbuka tidak sengaja saat mouse sekadar lewat.
9. Sebagai **pengguna mobile**, saya ingin dropdown menutup otomatis saat saya tap di luar area dropdown, sehingga saya bisa navigasi dengan nyaman.

### Feed Aktivitas Terbaru
10. Sebagai **pengunjung baru (guest)**, saya ingin langsung melihat bukti komunitas aktif di bagian atas halaman Beranda, sehingga saya yakin ini bukan platform statis yang sudah tidak diurus.
11. Sebagai **guest**, saya ingin melihat 5 aktivitas terbaru berupa campuran karya baru, member baru, dan event baru, sehingga saya mendapat gambaran lengkap tentang kehidupan komunitas.
12. Sebagai **guest**, saya ingin setiap item feed memiliki badge berwarna (hijau "Karya Baru", biru "Member Baru", kuning "Event Baru") sehingga saya bisa langsung membedakan jenis aktivitas tanpa membaca detail.
13. Sebagai **guest**, saya ingin melihat preview singkat dari setiap aktivitas (judul karya, nama member, nama event) beserta waktu relatif (misalnya "2 jam lalu"), sehingga saya tahu seberapa baru aktivitas tersebut.
14. Sebagai **guest**, saya ingin bisa mengklik seluruh kartu aktivitas untuk diarahkan ke halaman detail yang relevan (halaman showcase atau halaman event), sehingga saya bisa explore lebih jauh.
15. Sebagai **guest**, saya ingin melihat empty state yang informatif jika belum ada aktivitas sama sekali, sehingga tampilan tidak terlihat rusak atau kosong.
16. Sebagai **member**, saya ingin melihat nama saya muncul di feed jika saya baru bergabung, sehingga ada rasa diterima dan diakui oleh komunitas.

## Implementation Decisions

- **Navbar Grouping**: 2 dropdown grup — "Belajar" (Materi + Prompts) dan "Komunitas" (Showcase + Agenda + Komunitas Garuda anchor + WhatsApp Community external link). Beranda tetap standalone.
- **Dropdown behavior**: Hover dengan delay 150ms di desktop menggunakan CSS + JavaScript sederhana (bukan library). Tap-to-toggle di mobile.
- **Mobile drawer**: Semua item ditampilkan flat tanpa accordion. Label "BELAJAR" dan "KOMUNITAS" sebagai section header non-clickable di dalam drawer.
- **CTA adaptif**: Logika `@auth` / `@else` di Blade sudah ada — perlu disesuaikan dengan tampilan baru (guest: Masuk + Daftar Alumni; member: Area Member + Keluar; admin: Panel Admin + Keluar).
- **WhatsApp link**: Buka di tab baru (`target="_blank" rel="noopener"`). URL saat ini masih placeholder — perlu diganti URL real saat komunitas terbentuk.
- **Feed aktivitas**: Query gabungan dari 3 model (Showcase `status=approved`, User (member baru), Event (event baru)). Diambil 5 item terbaru berdasarkan `created_at` descending. Query dilakukan langsung di HomeController tanpa cache.
- **Feed card format**: Badge berwarna (pill) + nama/judul + deskripsi singkat (truncate 1 baris) + waktu relatif menggunakan Carbon `diffForHumans()`. Seluruh kartu adalah link.
- **Feed positioning**: Section baru disisipkan setelah Hero section dan sebelum Next Upcoming Event di `home.blade.php`.
- **Badge warna**: Hijau `#006633` untuk "Karya Baru", biru untuk "Member Baru", emas/amber untuk "Event Baru" — konsisten dengan palet design system existing.
- **Empty state feed**: Jika tidak ada aktivitas, tampilkan pesan placeholder friendly.
- **Tidak ada perubahan schema/database** — semua data sudah tersedia dari model yang ada.

## Out of Scope

- Real-time feed via WebSocket atau polling AJAX.
- Cache layer untuk feed.
- Notifikasi push atau badge counter di navbar.
- Halaman dedicated "Aktivitas Komunitas".
- Perubahan pada halaman selain Beranda dan layout navbar utama.
- Perubahan pada `admin.blade.php`.
- Member activity tracking (baca materi, copy prompt, dst).

## Further Notes

- Referensi visual: `dokumentasi-ikmas-ai-prototype.md`, khususnya konsep feed aktivitas di Beranda dan alur 6 langkah perjalanan alumni.
- WhatsApp URL saat ini masih placeholder — perlu diupdate ke URL real.
- Waktu relatif menggunakan `Carbon::diffForHumans()` — sudah tersedia di Laravel tanpa package tambahan.
- Badge baru harus mengikuti konvensi CSS design system existing agar tidak tabrakan style.
