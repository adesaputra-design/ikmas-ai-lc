## Problem Statement

Ketika anggota (member) atau alumni lupa kata sandi akun portal IKMAS AI Learning Center, sistem belum memiliki alur pemulihan kata sandi. Karena platform belum mengonfigurasi layanan SMTP email transaksional di server produksi, pengiriman email reset mandiri belum dapat diandalkan secara langsung. 

Di sisi lain, anggota komunitas alumni sangat terbiasa berkomunikasi via WhatsApp. Pengurus (Admin dan Staf) membutuhkan cara yang cepat, aman, dan terintegrasi untuk membantu mereset kata sandi member yang meminta bantuan, serta memberikan kemudahan bagi member untuk mengubah kata sandi mereka sendiri setelah berhasil masuk kembali ke akun.

## Solution

Mengimplementasikan solusi pemulihan kata sandi yang praktis dan terintegrasi:
1. **Akses Bantuan Cepat di Halaman Login**: Tautan *"Lupa kata sandi?"* yang langsung mengarahkan member ke WhatsApp resmi pengurus dengan format pesan bantuan yang siap kirim.
2. **Fitur Reset Password oleh Pengurus**: Admin (dan Staf yang bertugas di modul Alumni) dapat mereset kata sandi member dari halaman panel pengurus, baik dengan mengetik password baru secara manual maupun membuat password acak otomatis (generate), lengkap dengan tombol salin pesan WhatsApp ke member.
3. **Keamanan Bertingkat**: Staf hanya diizinkan mereset akun berstatus `member` biasa; Staf tidak dapat mereset akun sesama Staf atau Admin.
4. **Form Ganti Kata Sandi Mandiri**: Anggota yang sudah login dapat memperbarui kata sandi mereka secara mandiri di Dasbor Member dengan memverifikasi kata sandi saat ini.

## User Stories

### Member (Anggota / Alumni)
1. **As a Member**, I want to click a "Lupa kata sandi?" link on the login page, so that I can immediately reach the community helpdesk on WhatsApp with a pre-filled message requesting assistance.
2. **As a Member**, I want to receive my temporary/new password from the admin, so that I can regain access to my account without delay.
3. **As a Member**, I want to change my password from my Member Dashboard after logging in, so that I can set my own secure, private credentials.

### Staf Pengurus (dengan tugas Direktori Alumni)
4. **As a Staff member with Alumni permissions**, I want to click a "Reset Password" button for any regular member, so that I can help alumni who forget their login credentials.
5. **As a Staff member**, I want the system to prohibit me from resetting passwords of fellow Staff members or Administrators, so that security boundaries cannot be bypassed.

### Administrator (Super Admin)
6. **As an Admin**, I want to reset passwords for any user (Member, Staff, or Admin) via a clean modal interface, so that I have complete oversight over account recovery.
7. **As an Admin/Staff**, I want the reset modal to offer a "Generate Random Password" button and a "Copy WhatsApp Message" button, so that the workflow is seamless and takes less than 10 seconds.

## Implementation Decisions

### Login Page Interface
- Add a "Lupa kata sandi?" link aligned next to the "Kata Sandi" label on `/login`.
- Target: WhatsApp link (`https://wa.me/6285713257939?text=...`) with polite, pre-filled Indonesian text.

### Admin & Staff Management Panel
- Add a "🔑 Reset Password" button on user rows in both `/admin/alumni` and `/admin/team`.
- Modal "Reset Kata Sandi Anggota":
  - Input field for new password.
  - "🎲 Buat Password Acak" button (generates e.g. `ikmas-8392`).
  - "📋 Salin Pesan WA" button with template message ready for member communication.
  - Backend route: `POST /admin/users/{user}/reset-password`.
- Security checks in backend:
  - If user is `staff`, check if target user is `member`. If target is `staff` or `admin`, reject with 403 Forbidden.
  - If user is `admin`, allow reset for any target.

### Member Dashboard Self-Service
- Add a "Ganti Kata Sandi" card in `/member/dashboard`.
- Fields: Kata Sandi Saat Ini (`current_password`), Kata Sandi Baru (`password`), Konfirmasi Kata Sandi (`password_confirmation`).
- Backend route: `POST /member/password/update`.
- Validation: `current_password` must match using `Hash::check`, new password minimum 8 characters and confirmed.

## Out of Scope

- Automated email delivery via SMTP (deferred until dedicated transactional email service is configured).
- SMS OTP verification.
- Two-Factor Authentication (2FA).

## Further Notes

- This WhatsApp-assisted recovery matches user habits in Indonesian alumni communities, eliminating friction and bounce rates while keeping accounts secure.
