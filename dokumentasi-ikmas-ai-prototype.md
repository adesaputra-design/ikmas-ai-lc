# Dokumentasi Prototype: Komunitas Belajar AI IKMAS

File terkait: `ikmas-ai-prototype.html`

## Apa Ini

Prototype interaktif berbasis HTML — bukan aplikasi produksi, bukan proposal. Ini simulasi tampilan & alur produk yang bisa diklik langsung di browser, dibuat untuk mendemokan konsep "Komunitas Belajar AI IKMAS" ke pengurus (mendampingi slide pitch `ikmas-ai-community-pitch.pptx`).

Semua data di dalamnya (nama alumni, angkatan, jumlah member, karya showcase, statistik chapter) adalah **data contoh/dummy** untuk keperluan demo — bukan data real dari webapp.ikmas.com.

## Cara Membuka

1. Cukup dobel-klik file `ikmas-ai-prototype.html`, akan terbuka di browser default.
2. Tidak perlu instalasi atau server — semua kode (HTML, CSS, JS, logo) ada dalam satu file.
3. Butuh koneksi internet hanya untuk memuat font (Fraunces & Plus Jakarta Sans dari Google Fonts). Kalau offline, prototype tetap jalan tapi pakai font default browser.
4. Bisa dibuka di laptop (untuk presentasi) maupun HP (untuk dicoba sendiri oleh pengurus sebelum rapat).

## Alur Demo (6 Langkah)

Ikuti urutan ini saat presentasi — setiap langkah representasi satu tahap perjalanan alumni dari "belum tahu" sampai "aktif di komunitas":

| # | Layar | Yang Ditunjukkan |
|---|---|---|
| 1 | **Beranda** | Kondisi awal produk: hero komunitas, statistik basis alumni existing (2.961 · 34 provinsi), feed "aktivitas terbaru" agar terasa hidup sejak halaman pertama |
| 2 | **Form Gabung** | Alumni isi nama, angkatan, provinsi, status (alumni/keluarga/guru), minat belajar (dakwah/bisnis) |
| 3 | **Verifikasi** | Demo mekanisme trust: status "menunggu admin" + opsi minta peer vouch dari alumni lain — klik "Minta Konfirmasi" untuk mempercepat |
| 4 | **Dashboard Member** | Otomatis dipersonalisasi dari data form (nama, chapter wilayah+angkatan), dua pilihan jalur belajar, ringkasan direktori & aktivitas komunitas |
| 5 | **Jalur Belajar** | Klik masuk salah satu jalur (Dakwah atau Bisnis), buka modul pertama, lihat contoh materi singkat |
| 6 | **Showcase & Leaderboard** | Momen inti: karya AI dari alumni lain, leaderboard chapter teraktif — bukti visual bahwa ini komunitas hidup, bukan direktori pasif |

Titik-titik di bagian bawah layar menunjukkan sedang di langkah ke berapa. Tombol ↺ di pojok kanan atas (atau "Ulangi simulasi" di layar terakhir) mengembalikan semua ke kondisi awal — berguna kalau ingin demo ulang untuk orang lain di rapat yang sama.

## Interaksi yang Bisa Dicoba

- **Form gabung**: isian nama/angkatan/provinsi akan langsung muncul di sapaan dashboard ("Assalamu'alaikum, [Nama]!" dan "Chapter Anda: [Provinsi] · Angkatan [Tahun]") — menunjukkan personalisasi nyata, bukan sekadar gambar statis.
- **Chip status & minat**: bisa diklik ganti pilihan sebelum submit.
- **Peer vouch**: klik "Minta Konfirmasi" pada salah satu nama alumni untuk mensimulasikan proses verifikasi dipercepat.
- **Pilih jalur belajar**: dua kartu (Dakwah vs Bisnis) di dashboard bisa diklik, dan nama jalur yang dipilih terbawa ke layar modul.

## Batasan yang Perlu Diketahui Sebelum Presentasi

Supaya tidak ada pertanyaan pengurus yang salah paham dan perlu dijawab secara sadar:

- **Bukan koneksi ke webapp.ikmas.com** — semua angka (2.961 alumni, 34 provinsi, dst) adalah statistik existing yang dipakai sebagai konteks, bukan hasil integrasi API real-time.
- **Bukan sistem verifikasi/database sungguhan** — proses "peer vouch" dan "admin approval" di sini murni animasi untuk menunjukkan konsep alurnya, belum ada backend di baliknya.
- **Konten showcase & leaderboard adalah contoh ilustratif** — dibuat untuk menunjukkan *seperti apa* rasanya komunitas yang hidup, bukan cuplikan data asli.
- **Belum ada data survei minat AI di dalamnya** — konsisten dengan slide 7 di deck pitch, prototype ini tidak mengklaim validasi minat, hanya menunjukkan bentuk produknya.

## Struktur Teknis Singkat

- Satu file HTML mandiri: HTML + CSS + JavaScript vanilla (tanpa framework, tanpa build step) + logo IKMAS tertanam sebagai base64 (tidak butuh file gambar terpisah).
- State demo dikendalikan JS sederhana (`goTo(n)`), tidak ada penyimpanan data — refresh browser akan mengembalikan ke layar 1.
- Palet warna diambil langsung dari logo IKMAS: hijau `#006633`, emas `#FFCC00` — konsisten dengan slide pitch.
- Font: **Fraunces** (judul/serif) + **Plus Jakarta Sans** (isi/sans), dimuat dari Google Fonts.
- Responsif — sudah diuji tampil rapi di lebar desktop (1440px) maupun mobile (390px).

## Kalau Perlu Diperbarui

Karena ini file HTML biasa, bisa diedit langsung (buka dengan text editor apa saja) tanpa perlu tool khusus — atau kirim ke saya kalau mau ada perubahan konten, alur, atau data yang ditampilkan (misalnya setelah hasil survei minat AI keluar dan ingin ditambahkan sebagai layar baru).
