# Subscriber Non-Alumni — Design Spec

**Tanggal:** 2026-09-03  
**Status:** Approved  
**Konteks:** IKMAS AI Learning Center — Laravel App

## Latar Belakang

Platform IKMAS AI saat ini hanya mengizinkan alumni IKMAS untuk mendaftar (`alumni_year` wajib diisi). Pengunjung dari luar kalangan alumni tidak memiliki jalur resmi untuk bergabung dan mengakses konten pembelajaran. Fitur ini membuka jalur baru: **subscriber** — akun terbatas bagi non-alumni yang ingin belajar bersama komunitas IKMAS AI.

## Tujuan

- Membuka akses konten bagi publik non-alumni secara terkontrol (tidak langsung aktif)
- Menjaga kualitas komunitas dengan approval admin sebelum akun aktif
- Tidak merusak sistem alumni & role yang sudah berjalan

---

## Scope

### Yang Termasuk
- Role baru `subscriber` di sistem user
- Status akun `pending` / `active` / `rejected`
- Halaman register terpisah `/register/subscriber`
- Halaman register alumni dipindah ke `/register/alumni`
- Blokir login jika status `pending` atau `rejected`
- Modul admin baru untuk approval subscriber
- Akses terbatas untuk subscriber yang sudah `active`

### Yang Tidak Termasuk
- Notifikasi email (tidak ada email/SMTP di scope ini)
- Ekspor data subscriber ke CSV (bukan prioritas)
- Batas kuota subscriber

---

## Data Model

### Migration Baru: `add_status_to_users_table`

Tambahkan kolom `status` ke tabel `users`:

```php
$table->enum('status', ['active', 'pending', 'rejected'])->default('active')->after('role');
```

**Catatan penting:**
- Default `active` menjaga semua user lama (alumni, admin, staff) tetap aktif tanpa perlu migrasi data.
- Subscriber baru dibuat dengan `status = 'pending'`.

### Schema `users` setelah perubahan

| Kolom | Tipe | Keterangan |
|---|---|---|
| `role` | enum | `admin`, `staff`, `member`, `subscriber` |
| `status` | enum | `active`, `pending`, `rejected` (default: `active`) |
| `alumni_year` | string nullable | Hanya diisi untuk alumni |
| `whatsapp_number` | string nullable | Wajib untuk alumni & subscriber |

---

## Model: `User`

### Method baru

```php
public function isSubscriber(): bool
{
    return $this->role === 'subscriber';
}

public function isPending(): bool
{
    return $this->status === 'pending';
}

public function isRejected(): bool
{
    return $this->status === 'rejected';
}

public function isActive(): bool
{
    return $this->status === 'active';
}
```

### `getRoleBadgeAttribute` — tambah case subscriber

```php
'subscriber' => ['label' => 'Subscriber', 'class' => 'badge-amber'],
```

### `$fillable` — tambah `status`

```php
'status',
```

---

## Alur Registrasi

### Sebelum (satu halaman)
`/register` → form alumni

### Sesudah (dua jalur)

```
/register/alumni     → form alumni (nama, email, password, WA, tahun alumni)
                       role=member, status=active → langsung login

/register/subscriber → form subscriber (nama, email, password, WA)
                       role=subscriber, status=pending → halaman "menunggu approval"
```

Halaman `/register` lama diarahkan ke `/register/alumni` (redirect 301) agar link lama tidak rusak.

### Halaman Setelah Register Subscriber

Setelah submit berhasil, redirect ke halaman dedicated:
- **Route:** `GET /register/subscriber/pending`
- **View:** `auth.subscriber-pending`
- Pesan: "Pendaftaran berhasil! Akun Anda sedang ditinjau oleh pengurus IKMAS AI. Kami akan menghubungi Anda melalui WhatsApp setelah akun diaktifkan."
- **Tidak ada otomatis login** — user perlu menunggu approval.

---

## Blokir Login — `AuthController`

Di method `login()`, tambahkan cek sebelum `Auth::attempt()`:

```php
// Cek apakah akun pending atau rejected
$existingUser = User::where('email', $credentials['email'])->first();
if ($existingUser) {
    if ($existingUser->isPending()) {
        return back()->withErrors([
            'email' => 'Akun Anda masih dalam proses peninjauan oleh Pengurus IKMAS AI. Harap tunggu konfirmasi melalui WhatsApp.',
        ])->onlyInput('email');
    }
    if ($existingUser->isRejected()) {
        return back()->withErrors([
            'email' => 'Pendaftaran Anda tidak dapat disetujui. Silakan hubungi kami untuk informasi lebih lanjut.',
        ])->onlyInput('email');
    }
}
```

---

## Hak Akses Subscriber (Role-Based)

Setelah akun `active`, subscriber bisa mengakses:

| Fitur | Alumni (member) | Subscriber |
|---|---|---|
| Materi pembelajaran | ✅ | ✅ |
| Prompt Library | ✅ | ✅ |
| Agenda/Event | ✅ | ✅ |
| Showcase (view) | ✅ | ✅ |
| Bookmark | ✅ | ✅ |
| Dashboard pribadi | ✅ | ✅ |
| Submit showcase | ✅ | ❌ |
| Direktori alumni | ✅ | ❌ |

**Implementasi akses:** Tidak perlu middleware baru. Subscriber menggunakan middleware `auth` yang sudah ada. Batasan dilakukan di:
1. **View** — tombol "Submit Showcase" disembunyikan jika `isSubscriber()`
2. **Controller** — `MemberDashboardController::createShowcase()` dan `storeShowcase()` menambah cek `isSubscriber()` → abort 403

---

## Admin Panel — Modul Subscriber

### Permission baru: `subscribers`

Ditambahkan ke daftar permission yang tersedia (tidak perlu perubahan schema, permission sudah JSON di kolom `permissions`).

### Controller Baru: `AdminSubscriberController`

**File:** `app/Http/Controllers/Admin/AdminSubscriberController.php`

```
index()   → list semua subscriber (filter: pending / active / rejected)
approve() → set status = 'active'
reject()  → set status = 'rejected'
destroy() → soft delete (reuse pola yang sama dengan AdminAlumniController)
```

### Routes baru di admin group

```php
Route::middleware('permission:subscribers')->group(function () {
    Route::get('/subscribers', [AdminSubscriberController::class, 'index'])->name('subscribers.index');
    Route::post('/subscribers/{user}/approve', [AdminSubscriberController::class, 'approve'])->name('subscribers.approve');
    Route::post('/subscribers/{user}/reject', [AdminSubscriberController::class, 'reject'])->name('subscribers.reject');
    Route::delete('/subscribers/{user}', [AdminSubscriberController::class, 'destroy'])->name('subscribers.destroy');
});
```

### View Admin: `resources/views/admin/subscribers/index.blade.php`

Layout mengikuti pola `admin/alumni/index.blade.php`. Tabel berisi:
- Nama, email, WA, tanggal daftar, status badge
- Tombol Approve (hijau) dan Reject (merah) untuk status `pending`
- Filter tab: Semua / Pending / Aktif / Ditolak

### Badge status di `AdminDashboardController`

Tambahkan counter `pendingSubscribers` ke data dashboard agar terlihat di sidebar/notifikasi.

---

## File-File yang Terdampak

### [NEW] Files

| File | Keterangan |
|---|---|
| `database/migrations/2026_09_03_XXXXXX_add_status_to_users_table.php` | Kolom `status` di tabel users |
| `app/Http/Controllers/Admin/AdminSubscriberController.php` | CRUD + approve/reject subscriber |
| `resources/views/auth/register-alumni.blade.php` | Form register alumni (clone dari register.blade.php) |
| `resources/views/auth/register-subscriber.blade.php` | Form register subscriber (tanpa alumni_year) |
| `resources/views/auth/subscriber-pending.blade.php` | Halaman konfirmasi setelah daftar subscriber |
| `resources/views/admin/subscribers/index.blade.php` | List + approve/reject subscriber di admin panel |

### [MODIFY] Files

| File | Perubahan |
|---|---|
| `app/Models/User.php` | Tambah `status` ke fillable, method `isSubscriber()`, `isPending()`, `isRejected()`, `isActive()`, update `getRoleBadgeAttribute()` |
| `app/Http/Controllers/AuthController.php` | Pecah `register()` menjadi `registerAlumni()` dan `registerSubscriber()`. Login tambah blokir pending/rejected. `showRegisterForm()` dijadikan redirect. |
| `routes/web.php` | `/register` → redirect ke `/register/alumni`. Tambah route alumni, subscriber, dan admin subscribers. |
| `app/Http/Controllers/Member/MemberDashboardController.php` | Guard `isSubscriber()` di `createShowcase()` & `storeShowcase()`. |
| `app/Http/Controllers/Admin/AdminDashboardController.php` | Tambah count `pendingSubscribers`. |
| Views member dashboard | Sembunyikan tombol "Submit Showcase" jika subscriber. |
| Views admin sidebar/nav | Tambah link ke modul Subscribers. |

---

## Testing

### Feature Tests

Buat file baru: `tests/Feature/SubscriberTest.php`

Test cases:
1. `test_subscriber_can_register` — POST berhasil, akun dibuat dengan role=subscriber, status=pending
2. `test_subscriber_register_redirects_to_pending_page` — setelah daftar redirect ke halaman pending
3. `test_pending_subscriber_cannot_login` — login gagal dengan pesan pending
4. `test_rejected_subscriber_cannot_login` — login gagal dengan pesan rejected
5. `test_active_subscriber_can_login` — login berhasil
6. `test_subscriber_cannot_access_create_showcase` — 403 jika subscriber coba submit showcase
7. `test_admin_can_approve_subscriber` — POST approve → status=active
8. `test_admin_can_reject_subscriber` — POST reject → status=rejected
9. `test_alumni_register_still_works` — POST `/register/alumni` tetap berfungsi seperti sebelumnya

---

## Verification Plan

### Automated Tests

```bash
php artisan test --filter=SubscriberTest
php artisan test  # pastikan test lama tidak rusak
```

### Manual Verification

1. Buka `/register` → redirect ke `/register/alumni`
2. Register sebagai subscriber → redirect ke halaman pending
3. Coba login dengan akun pending → pesan error tepat
4. Admin approve subscriber → akun aktif
5. Login subscriber aktif → masuk ke member dashboard
6. Subscriber aktif coba akses `/member/showcase/create` → 403
7. Register alumni tetap berjalan normal
