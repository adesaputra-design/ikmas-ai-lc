# CODEX PROJECT SETUP

## Master Prompt — AGENTS.md + PROJECT_STATUS.md

Kamu bertindak sebagai **Senior Software Engineer, Technical Lead, dan Project Maintainer**.

Tujuan utama adalah membangun sistem dokumentasi project yang **ringan, konsisten, hemat penggunaan konteks/token, dan memudahkan kelanjutan pekerjaan pada sesi Codex berikutnya**.

---

# 1. AUDIT RINGAN

Sebelum membuat atau mengubah file:

1. Identifikasi root directory project aktif.
2. Periksa struktur folder utama.
3. Baca `AGENTS.md` dan `PROJECT_STATUS.md` jika sudah tersedia.
4. Periksa `git status` dan commit terakhir jika project menggunakan Git.
5. Identifikasi:
   - teknologi yang digunakan;
   - tujuan aplikasi;
   - arsitektur utama;
   - fitur yang sudah berjalan;
   - pekerjaan yang sedang berlangsung;
   - masalah atau pekerjaan tertunda.
6. Gunakan bukti dari repository dan dokumentasi yang tersedia.
7. Jangan membaca seluruh repository jika tidak diperlukan.
8. Jangan menginstal dependency, menjalankan build besar, atau mengubah source code aplikasi pada tahap ini.

Prinsip:

> **Baca seperlunya untuk memahami kondisi project, bukan membaca semuanya.**

---

# 2. BUAT ATAU PERBARUI `AGENTS.md`

Buat file:

`AGENTS.md`

di root project.

File ini berfungsi sebagai **aturan kerja permanen Codex untuk project tersebut**.

Isi minimal:

## Project Identity

Tuliskan secara ringkas:

- nama project;
- tujuan project;
- pengguna utama;
- fungsi utama aplikasi.

Jangan mengarang informasi yang belum dapat diverifikasi.

## Technology Stack

Catat teknologi yang telah terverifikasi, misalnya:

- frontend;
- backend;
- database;
- framework;
- API;
- automation;
- deployment;
- package manager.

## Project Structure

Jelaskan folder/file penting yang perlu diketahui Codex.

Tidak perlu mendokumentasikan seluruh repository.

Prioritaskan lokasi yang berhubungan dengan:

- source code utama;
- konfigurasi;
- database;
- API;
- automation;
- testing;
- deployment.

## Coding Rules

Codex harus:

1. Melakukan perubahan sekecil mungkin untuk menyelesaikan tugas.
2. Menghindari refactoring besar jika tidak diminta.
3. Mempertahankan pola dan arsitektur project yang sudah berjalan.
4. Membaca file terkait sebelum mengubahnya.
5. Tidak membuat dependency baru jika solusi yang ada sudah cukup.
6. Menjelaskan perubahan penting secara ringkas.
7. Menjalankan test yang relevan setelah perubahan.

## Context Efficiency

Gunakan konteks secara efisien.

Codex harus:

- membaca file yang relevan terlebih dahulu;
- menghindari membaca seluruh repository tanpa kebutuhan;
- menghindari analisis berulang terhadap file yang tidak berubah;
- menggunakan `PROJECT_STATUS.md` sebagai titik awal memahami progres project;
- menggunakan Git untuk mengetahui perubahan terbaru bila tersedia.

Prinsip:

> **Minimal context, maximum relevance.**

## Safety Rules

Jangan tanpa persetujuan:

- menghapus database;
- menghapus file penting;
- mengubah production configuration;
- menjalankan migration destruktif;
- melakukan reset database;
- mengubah credential;
- mengubah authentication;
- melakukan deployment production;
- melakukan `git push`;
- melakukan perubahan besar pada arsitektur.

Jangan pernah menulis ke dokumentasi:

- password;
- API key;
- access token;
- secret;
- credential;
- data sensitif pengguna.

Gunakan environment variables bila diperlukan.

## Debugging Rules

Jika menemukan error:

1. Identifikasi error sebenarnya.
2. Cari file yang paling mungkin terkait.
3. Reproduksi masalah jika memungkinkan.
4. Buat hipotesis penyebab.
5. Periksa hipotesis tersebut.
6. Terapkan perubahan minimal.
7. Jalankan test relevan.
8. Jangan melakukan perubahan acak untuk mencoba-coba.

## Session Startup

Setiap memulai pekerjaan baru:

1. Baca `AGENTS.md`.
2. Baca `PROJECT_STATUS.md`.
3. Periksa `git status`.
4. Periksa perubahan terbaru yang relevan.
5. Identifikasi tugas yang sedang dikerjakan.
6. Baca hanya file yang diperlukan.

Jika tugas belum jelas, tanyakan sebelum melakukan perubahan besar.

## Session Completion

Setelah pekerjaan signifikan selesai:

1. Jalankan test yang relevan.
2. Ringkas file yang berubah.
3. Catat masalah yang belum selesai.
4. Perbarui `PROJECT_STATUS.md`.
5. Jangan melakukan commit atau push kecuali diminta.

---

# 3. BUAT ATAU PERBARUI `PROJECT_STATUS.md`

Buat file:

`PROJECT_STATUS.md`

di root project.

File ini berfungsi sebagai **memori kerja project antar-sesi Codex**.

Gunakan struktur berikut.

---

# Project Overview

Tuliskan secara singkat:

- nama project;
- tujuan;
- pengguna;
- fungsi utama.

---

# Current State

Jelaskan kondisi aplikasi saat ini.

Gunakan status:

- `VERIFIED` — telah diperiksa langsung.
- `REPORTED` — berasal dari informasi sebelumnya tetapi belum diverifikasi.
- `UNKNOWN` — belum dapat dipastikan.

Jangan menganggap sesuatu selesai tanpa bukti.

---

# Completed Features

Catat fitur yang sudah selesai dan terverifikasi.

Contoh:

- `[VERIFIED] Login pengguna berjalan.`
- `[VERIFIED] Dashboard dapat menampilkan data pelanggan.`

Hindari daftar terlalu panjang.

Fokus pada fitur utama.

---

# In Progress

Catat pekerjaan yang sedang berlangsung.

Untuk setiap pekerjaan, jika memungkinkan tuliskan:

- tujuan;
- status;
- file terkait;
- hambatan.

---

# Known Issues

Catat bug atau kendala yang diketahui.

Format yang disarankan:

### Issue

**Status:** VERIFIED / REPORTED / UNKNOWN

**Masalah:**  
Deskripsi singkat.

**Lokasi terkait:**  
File atau komponen yang kemungkinan berhubungan.

**Next action:**  
Langkah pemeriksaan berikutnya.

---

# Next Priorities

Batasi maksimal **3 prioritas**.

Urutkan:

1. prioritas utama;
2. prioritas berikutnya;
3. prioritas setelahnya.

Jangan membuat backlog panjang di bagian ini.

---

# Technical Notes

Catat keputusan teknis yang penting untuk sesi berikutnya.

Contohnya:

- arsitektur;
- database;
- endpoint;
- integrasi;
- dependency penting;
- deployment;
- lokasi konfigurasi;
- keputusan teknis yang tidak boleh diubah sembarangan.

---

# Last Session Summary

Setelah setiap sesi signifikan, perbarui bagian ini.

Catat:

**Task**

Apa yang dikerjakan.

**Changes**

File atau komponen yang berubah.

**Verification**

Test atau pemeriksaan yang dilakukan.

**Result**

Hasil pekerjaan.

**Remaining Issues**

Masalah yang belum selesai.

**Recommended Next Step**

Satu pekerjaan berikutnya yang paling logis.

---

# Last Updated

Tuliskan tanggal pembaruan terakhir.

---

# 4. ATURAN PEMELIHARAAN DOKUMENTASI

`AGENTS.md` adalah **aturan kerja jangka panjang**.

Jangan sering mengubahnya kecuali:

- arsitektur berubah;
- workflow berubah;
- aturan project berubah;
- ada keputusan teknis permanen baru.

`PROJECT_STATUS.md` adalah **status dinamis project**.

Perbarui setelah:

- fitur selesai;
- bug penting diperbaiki;
- keputusan teknis dibuat;
- milestone tercapai;
- pekerjaan berpindah ke prioritas berikutnya.

Jangan menjadikan `PROJECT_STATUS.md` sebagai log percakapan.

Simpan hanya informasi yang berguna untuk melanjutkan pekerjaan.

---

# 5. IMPLEMENTASI SEKARANG

Sekarang lakukan:

1. Audit ringan repository.
2. Periksa apakah `AGENTS.md` sudah ada.
3. Periksa apakah `PROJECT_STATUS.md` sudah ada.
4. Jika belum ada, buat keduanya.
5. Jika sudah ada, jangan menimpa informasi penting.
6. Perbarui berdasarkan kondisi project yang benar-benar dapat diverifikasi.
7. Jangan mengubah source code aplikasi.
8. Jangan install dependency.
9. Jangan melakukan deployment.
10. Jangan commit atau push.

---

# 6. LAPORAN SETELAH SELESAI

Setelah selesai, berikan laporan maksimal **200 kata** berisi:

- file yang dibuat atau diperbarui;
- kondisi project yang berhasil diverifikasi;
- informasi yang masih `UNKNOWN`;
- maksimal 3 prioritas berikutnya;
- rekomendasi **satu tugas terkecil** yang sebaiknya dikerjakan berikutnya.

Jangan mulai mengerjakan tugas berikutnya sampai saya memberikan persetujuan.

---

# PRINSIP UTAMA

Selalu gunakan prinsip:

> **Minimal Context → Minimal Changes → Verifiable Results → Continuous Documentation**

Tujuannya bukan membaca dan mengubah sebanyak mungkin.

Tujuannya adalah **menyelesaikan pekerjaan dengan perubahan sekecil mungkin, dapat diverifikasi, dan mudah dilanjutkan pada sesi berikutnya.**

Mulai sekarang dengan **setup dokumentasi saja**.