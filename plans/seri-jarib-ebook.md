# Plan: Ebook Interaktif Pustaka AI — Seri JARIB

> Source PRD: `prd-pustaka-ebook-seri-jarib.md` (root repository). This plan supersedes the untracked root draft `plan-pustaka-ebook-seri-jarib.md` for implementation sequencing; neither source document is changed here.

## Tujuan dan batasan

Pembaca dapat membuka edisi JARIB di ponsel tanpa login, berpindah bab, mencoba latihan, dan mencetaknya. Anggota yang berhak dapat menyimpan aktivitas. Admin dan staf berizin `library` dapat menyiapkan edisi berikutnya tanpa bantuan developer. Penerbitan isi edisi pertama menunggu naskah final dan persetujuan narasumber atas kutipan; naskah tersebut belum ada di repository.

## Architectural decisions

- **Routes:** pertahankan katalog `GET /library` dan detail `GET /library/{slug}`. Tambahkan `GET /library/{slug}/bab/{chapterSlug}`, `GET /library/{slug}/skor`, dan `GET /library/{slug}/cetak`. Penyimpanan memakai `POST /library/{slug}/progress` dan `POST /library/{slug}/skor` di balik autentikasi. Pengelolaan dan pratinjau draf berada di bawah `/admin/library/{item}` dengan izin `library`. Route detail yang sudah ada tidak menangkap segmen tambahan; urutan pendaftaran route tidak dijadikan mekanisme keamanan.
- **Schema:** perluas `library_items.type` dengan `ebook` tanpa mengubah data `book`, `podcast`, dan `academic`. Metadata ebook mencakup `series_name`, `edition_number`, `is_public` (default `false`), dan `assessment` terstruktur. `library_chapters` menyimpan bab berurutan, slug, intisari, body Markdown, riset, aksi, checklist, dan estimasi waktu baca. `ebook_progress` menyimpan pasangan unik user–ebook, bab terakhir, checklist, dan riwayat skor. Migrasi dan rollback harus aman bagi data lama serta diuji pada SQLite dan mesin database produksi setelah lingkungan produksi diketahui.
- **Key models:** `LibraryItem`, `LibraryChapter`, dan `EbookProgress`. Bab terkait satu ebook; urutan dan slug unik dalam ebook. `Bookmark` tetap memakai skema `bookmarkable_id` + `bookmarkable_type = library`; jangan menambah kolom `library_item_id` yang tidak ada.
- **Access:** hanya item `approved` yang tampil di katalog publik. Bab lengkap dapat dibaca tamu jika `is_public = true`; tipe lama tetap memakai aturan akses yang sekarang. Draf hanya dapat dipratinjau admin atau staf berizin `library`. Penyimpanan checklist, skor, dan posisi baca mengikuti hak baca anggota yang ada, termasuk subscriber aktif; setiap operasi selalu dibatasi ke user yang sedang login.
- **Content and scoring:** render Markdown dengan HTML mentah dan tautan berbahaya diblokir. Kutipan yang dibagikan ke WhatsApp harus berasal dari konten yang telah disetujui narasumber. Definisi pertanyaan, kelompok prinsip, skala, rentang hasil, dan rekomendasi bab tersimpan sebagai data ebook; server menghitung ulang skor dari jawaban tervalidasi, bukan menerima total skor dari browser.
- **Presentation:** gunakan Blade dan aset Vite portal, tampilan baca responsif, dukungan tema terang/gelap, pengaturan ukuran huruf dengan fallback bila penyimpanan browser gagal, serta print stylesheet browser. Tidak ada layanan PDF atau dependensi frontend baru yang diperlukan dalam rencana ini.

## Prasyarat implementasi

1. Selaraskan PHP CLI dan dependensi terkunci, lalu selesaikan setup Laravel Boost sesuai `AGENTS.md`. Pengujian sebelumnya hanya berjalan dengan pengabaian pemeriksaan versi PHP.
2. Buat tes regresi bookmark pustaka yang sudah tersimpan dan perbaiki query/relasi bookmark sebelum alur bookmark ebook dipakai. Suite saat ini lulus tetapi belum menguji keadaan tersebut.
3. Pastikan naskah v2 final tersedia, penanda `[CEK: ...]` telah diselesaikan, dan kutipan telah disetujui narasumber sebelum menerbitkan isi edisi pertama. Data contoh untuk pengembangan dan tes tidak dianggap sebagai persetujuan penerbitan.

---

## Phase 1: Draf ebook dan pratinjau privat

**User stories:** 12, 13 (tambah bab), 15.

### What to build

Admin atau staf berizin membuat item ebook berstatus draf, mengisi metadata dan satu bab, lalu melihat sampul dan bab melalui pratinjau privat. Jalur ini melewati form, validasi, penyimpanan, akses, dan tampilan tanpa membuka draf ke publik.

### Acceptance criteria

- [ ] Item `ebook` dan babnya tersimpan melalui panel admin, dengan slug bab unik dalam ebook dan estimasi baca yang konsisten.
- [ ] Admin/staf berizin dapat membuka pratinjau sampul serta bab; staf tanpa izin ditolak.
- [ ] Tamu dan member biasa mendapat 404 untuk draf, termasuk bila mengetahui URL bab.
- [ ] Migrasi SQLite dan tes fitur alur draf/pratinjau lulus; data item tipe lama tetap terbaca.

---

## Phase 2: Terbitkan ebook untuk pembaca publik

**User stories:** 1, 2, 3, 11.

### What to build

Pengurus menerbitkan ebook yang sudah disetujui. Tamu menemukan ebook lewat katalog, membuka sampul dan daftar isi dengan waktu baca, lalu membaca bab serta berpindah ke bab lain. Member dapat menyimpan dan mengenali kembali bookmark ebook.

### Acceptance criteria

- [ ] Ebook `approved` dan `is_public = true` tampil di `/library` dengan filter `ebook`, seri, dan edisi; draf tetap tersembunyi.
- [ ] Sampul menampilkan daftar isi, total waktu baca, dan tautan mulai; halaman bab menampilkan intisari, isi, serta navigasi bab sebelum/sesudah.
- [ ] Tamu dapat membaca bab publik tanpa login; item `book`/`podcast` tetap mengikuti pembatasan lama.
- [ ] Bookmark ebook yang dibuat member terbaca kembali pada detail dan dasbor, tanpa tercampur dengan bookmark prompt atau item lain.
- [ ] Tes fitur untuk katalog, akses publik, navigasi, regresi akses tipe lama, dan bookmark lulus. Edisi pertama diterbitkan hanya dari naskah yang lolos prasyarat editorial.

---

## Phase 3: Sunting dan urutkan bab yang sudah dibuat

**User stories:** 13 (sunting, hapus, urutkan).

### What to build

Pengurus memperbaiki isi bab dan urutannya melalui panel admin. Perubahan terlihat di pratinjau atau halaman publik sesuai status ebook, sehingga edisi berikutnya dapat dikelola tanpa perubahan kode.

### Acceptance criteria

- [ ] Admin/staf berizin dapat menyunting, menghapus, dan memindahkan urutan bab; staf tanpa izin ditolak.
- [ ] Urutan pada daftar isi, navigasi, dan total waktu baca mengikuti perubahan yang tersimpan.
- [ ] Tindakan terhadap bab dari ebook lain ditolak; konflik posisi tidak menghasilkan urutan ganda atau kehilangan bab.
- [ ] Tes fitur mencakup perubahan yang terlihat oleh pembaca dan batas izin.

---

## Phase 4: Bacaan ponsel dan berbagi kutipan

**User stories:** 4, 6.

### What to build

Halaman bab mendapat tata baca ponsel, dukungan tema portal, ukuran huruf yang dapat diubah, kotak riset, dan kutipan terpilih yang dapat dibagikan sebagai teks ke WhatsApp. Alur baca tetap berfungsi ketika penyimpanan browser diblokir.

### Acceptance criteria

- [ ] Sampul dan bab dapat dibaca pada lebar 360px dan 1280px; fokus keyboard, label kontrol, dan kontras teks memenuhi kebutuhan aksesibilitas.
- [ ] Ukuran huruf bertahan setelah muat ulang bila penyimpanan browser tersedia, dan kontrol tetap berfungsi bila tidak tersedia.
- [ ] Kutipan khusus dan blockquote biasa tampil berbeda; tautan WhatsApp memuat kutipan, narasumber, dan URL bab yang benar.
- [ ] Rendering Markdown menolak HTML mentah dan tautan tidak aman; tes renderer dan pemeriksaan tampilan terang/gelap lulus.

---

## Phase 5: Checklist yang bisa dicoba dan disimpan

**User stories:** 5 (checklist), 8.

### What to build

Tamu dapat mencentang aksi pekanan dalam sesi baca dan melihat ajakan masuk untuk menyimpan. Member yang berhak menyimpan checklist pada ebook miliknya dan melihat centang yang sama setelah membuka ulang bab.

### Acceptance criteria

- [ ] Tamu dapat memakai checklist tanpa POST atau membuat data server.
- [ ] Member menyimpan dan memuat ulang checklist per bab; perubahan pada satu bab tidak menimpa bab lain.
- [ ] POST tanpa login diarahkan ke login; user lain atau ebook/bab yang tidak cocok tidak dapat diubah.
- [ ] Tes fitur mencakup penyimpanan, pemuatan ulang, validasi input, dan isolasi user.

---

## Phase 6: Editor asesmen dan cek skor tamu

**User stories:** 5 (cek skor), 14.

### What to build

Pengurus mendefinisikan pertanyaan per prinsip, skala, rentang hasil, dan rekomendasi bab. Tamu membuka `/library/{slug}/skor`, menjawab, melihat hasil per prinsip dan saran baca, lalu mendapat ajakan masuk untuk menyimpan.

### Acceptance criteria

- [ ] Admin/staf berizin dapat menyimpan dan mengubah asesmen terstruktur; data yang tidak lengkap atau merujuk bab lain ditolak.
- [ ] Tamu dapat menyelesaikan asesmen tanpa data server; hasil dan rekomendasi berasal dari definisi ebook yang aktif.
- [ ] Jawaban kosong, di luar skala, atau jumlahnya tidak cocok ditangani jelas tanpa skor menyesatkan.
- [ ] Tes perhitungan serta tes fitur editor dan alur tamu lulus; perubahan definisi tercermin pada halaman skor.

---

## Phase 7: Simpan skor dan lihat riwayat

**User stories:** 9.

### What to build

Member yang berhak menyimpan hasil asesmen dan melihat tanggal serta skor tiap percobaan. Server memvalidasi jawaban terhadap definisi ebook saat penyimpanan dan menghitung hasilnya kembali.

### Acceptance criteria

- [ ] Hasil tersimpan dengan tanggal dan dapat dibaca ulang pada ebook yang sama; percobaan berikutnya menambah riwayat.
- [ ] Total skor yang dikirim browser diabaikan; jawaban tidak valid ditolak.
- [ ] User tidak dapat membaca atau mengubah riwayat user lain; POST tanpa login diarahkan ke login.
- [ ] Tes fitur mencakup perhitungan server, riwayat, dan isolasi user.

---

## Phase 8: Lanjutkan dari bab terakhir

**User stories:** 10.

### What to build

Saat member yang berhak membaca bab, posisi baca terakhirnya disimpan. Sampul menawarkan tautan untuk melanjutkan dari bab tersebut, tanpa mengubah pengalaman tamu.

### Acceptance criteria

- [ ] Membuka bab memperbarui posisi pada progres user dan ebook yang tepat.
- [ ] Sampul menampilkan tautan lanjutkan hanya bila ada bab terakhir yang masih valid.
- [ ] Membaca ebook lain tidak mengubah posisi ebook sebelumnya; tamu tidak membuat progres server.
- [ ] Tes fitur mencakup pemuatan ulang, isolasi ebook/user, dan bab yang telah dihapus.

---

## Phase 9: Cetak dan simpan PDF dari browser

**User stories:** 7.

### What to build

Pembaca membuka `/library/{slug}/cetak` untuk melihat semua bab dalam satu dokumen lalu memakai dialog cetak browser untuk menyimpan PDF. Halaman cetak mengikuti akses ebook yang sama dengan halaman baca.

### Acceptance criteria

- [ ] Dokumen cetak memuat sampul dan semua bab berurutan, dengan pergantian halaman per bab dan tanpa navigasi, tombol, atau CTA.
- [ ] Ebook privat atau draf tetap terlindungi pada route cetak.
- [ ] Hasil cetak diperiksa di browser desktop dan Android pada edisi JARIB yang telah disetujui.
- [ ] Tes akses route cetak dan suite penuh lulus.
