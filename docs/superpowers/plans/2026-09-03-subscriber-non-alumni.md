# Subscriber Non-Alumni Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Membuka jalur registrasi bagi pengunjung non-alumni sebagai "subscriber" dengan akun terbatas, proses approval admin, dan akses read-only ke konten platform IKMAS AI.

**Architecture:** Menambahkan kolom `status` (enum: active/pending/rejected) ke tabel `users` dan role baru `subscriber`. Registrasi dipecah menjadi dua jalur terpisah (`/register/alumni` dan `/register/subscriber`). Login diblokir untuk akun pending/rejected. Admin panel mendapat modul baru untuk approve/reject subscriber.

**Tech Stack:** Laravel 11, PHP 8.3+, Blade views, SQLite (test) / MySQL (prod), PHPUnit Feature Tests

**Spec:** `docs/superpowers/specs/2026-09-03-subscriber-non-alumni-design.md`

## Global Constraints

- Seluruh teks UI menggunakan Bahasa Indonesia
- Ikuti pola Blade yang ada: `@extends('layouts.admin')` untuk admin, `@extends('layouts.app')` untuk publik/member
- Style inline CSS sesuai CSS variable yang sudah ada (`var(--primary)`, `var(--border-color)`, `var(--bg-surface)`, dll.) — jangan tambahkan Tailwind atau framework CSS baru
- Selalu gunakan `TDD`: tulis test dulu, jalankan untuk verifikasi gagal, baru implementasi
- Commit setelah setiap task selesai dan semua test pass
- Run test dengan: `php artisan test` dari root project
- Default value `status = 'active'` wajib dipertahankan agar data user lama tidak terdampak

---

## Task 1: Migration — Tambah Kolom `status` ke Tabel `users`

**Files:**
- Create: `database/migrations/2026_09_03_220000_add_status_to_users_table.php`
- Modify: `app/Models/User.php`
- Test: `tests/Feature/SubscriberTest.php` (buat file baru)

**Interfaces:**
- Produces:
  - `User::$fillable` mengandung `'status'`
  - `User::isSubscriber(): bool` — `return $this->role === 'subscriber'`
  - `User::isPending(): bool` — `return $this->status === 'pending'`
  - `User::isRejected(): bool` — `return $this->status === 'rejected'`
  - `User::isActive(): bool` — `return $this->status === 'active'`
  - `User::getRoleBadgeAttribute()` mengembalikan entry untuk `'subscriber'`

---

- [ ] **Step 1: Buat file migration**

```bash
php artisan make:migration add_status_to_users_table --table=users
```

Kemudian edit file migration yang baru dibuat (namanya akan seperti `2026_09_03_XXXXXX_add_status_to_users_table.php`) menjadi:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'pending', 'rejected'])
                  ->default('active')
                  ->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
```

- [ ] **Step 2: Tulis test untuk memverifikasi model User punya method baru**

Buat file `tests/Feature/SubscriberTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriberTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_model_has_subscriber_methods(): void
    {
        $subscriber = User::factory()->create([
            'role' => 'subscriber',
            'status' => 'pending',
        ]);

        $this->assertTrue($subscriber->isSubscriber());
        $this->assertTrue($subscriber->isPending());
        $this->assertFalse($subscriber->isActive());
        $this->assertFalse($subscriber->isRejected());
        $this->assertFalse($subscriber->isMember());
    }

    public function test_existing_users_default_to_active_status(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        $this->assertEquals('active', $member->status);
        $this->assertTrue($member->isActive());
    }

    public function test_subscriber_role_badge_attribute(): void
    {
        $subscriber = User::factory()->create([
            'role' => 'subscriber',
            'status' => 'active',
        ]);

        $badge = $subscriber->role_badge;
        $this->assertEquals('Subscriber', $badge['label']);
        $this->assertEquals('badge-amber', $badge['class']);
    }
}
```

- [ ] **Step 3: Jalankan test untuk verifikasi gagal**

```bash
php artisan test --filter=SubscriberTest
```

Expected: FAIL — `isSubscriber()`, `isPending()`, `isActive()`, `isRejected()` belum ada, dan kolom `status` belum ada.

- [ ] **Step 4: Update `app/Models/User.php`**

Tambahkan `'status'` ke `$fillable`, tambahkan 4 method baru, dan update `getRoleBadgeAttribute()`:

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'whatsapp_number',
    'alumni_year',
    'bio',
    'role',
    'status',        // <-- tambahkan ini
    'permissions',
    'avatar',
];
```

Tambahkan method-method berikut setelah method `isMember()`:

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

Update `getRoleBadgeAttribute()` — tambahkan case `subscriber` sebelum `default`:

```php
public function getRoleBadgeAttribute(): array
{
    return match ($this->role) {
        'admin'      => ['label' => 'Administrator',  'class' => 'badge-primary'],
        'staff'      => ['label' => 'Staf Pengurus',  'class' => 'badge-cyan'],
        'subscriber' => ['label' => 'Subscriber',     'class' => 'badge-amber'],
        default      => ['label' => 'Member Alumni',  'class' => 'badge-emerald'],
    };
}
```

- [ ] **Step 5: Jalankan migration**

```bash
php artisan migrate
```

- [ ] **Step 6: Jalankan test untuk verifikasi pass**

```bash
php artisan test --filter=SubscriberTest
```

Expected: 3 tests PASS.

- [ ] **Step 7: Pastikan test lama tidak rusak**

```bash
php artisan test
```

Expected: semua test pass.

- [ ] **Step 8: Commit**

```bash
git add database/migrations/ app/Models/User.php tests/Feature/SubscriberTest.php
git commit -m "feat: add status column to users and subscriber role methods"
```

---

## Task 2: Login Guard — Blokir Akun Pending & Rejected

**Files:**
- Modify: `app/Http/Controllers/AuthController.php`
- Test: `tests/Feature/SubscriberTest.php` (tambah test cases)

**Interfaces:**
- Consumes: `User::isPending(): bool`, `User::isRejected(): bool` (dari Task 1)
- Produces: Method `login()` memblokir user dengan status `pending` atau `rejected` sebelum `Auth::attempt()`

---

- [ ] **Step 1: Tambahkan test cases untuk login blocking**

Di `tests/Feature/SubscriberTest.php`, tambahkan:

```php
public function test_pending_subscriber_cannot_login(): void
{
    $subscriber = User::factory()->create([
        'email'    => 'pending@test.com',
        'password' => bcrypt('password123'),
        'role'     => 'subscriber',
        'status'   => 'pending',
    ]);

    $response = $this->post('/login', [
        'email'    => 'pending@test.com',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();

    $errors = session('errors');
    $this->assertStringContainsString('peninjauan', $errors->first('email'));
}

public function test_rejected_subscriber_cannot_login(): void
{
    $subscriber = User::factory()->create([
        'email'    => 'rejected@test.com',
        'password' => bcrypt('password123'),
        'role'     => 'subscriber',
        'status'   => 'rejected',
    ]);

    $response = $this->post('/login', [
        'email'    => 'rejected@test.com',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
}

public function test_active_subscriber_can_login(): void
{
    $subscriber = User::factory()->create([
        'email'    => 'active@test.com',
        'password' => bcrypt('password123'),
        'role'     => 'subscriber',
        'status'   => 'active',
    ]);

    $response = $this->post('/login', [
        'email'    => 'active@test.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('member.dashboard'));
    $this->assertAuthenticatedAs($subscriber);
}

public function test_active_alumni_member_can_still_login(): void
{
    $member = User::factory()->create([
        'email'    => 'alumni@test.com',
        'password' => bcrypt('password123'),
        'role'     => 'member',
        'status'   => 'active',
    ]);

    $response = $this->post('/login', [
        'email'    => 'alumni@test.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('member.dashboard'));
    $this->assertAuthenticatedAs($member);
}
```

- [ ] **Step 2: Jalankan test untuk verifikasi gagal**

```bash
php artisan test --filter=SubscriberTest
```

Expected: test `pending`, `rejected`, dan `active subscriber` FAIL karena guard belum ada.

- [ ] **Step 3: Update method `login()` di `AuthController`**

Di `app/Http/Controllers/AuthController.php`, pada method `login()`, tambahkan blok cek status **setelah** validasi credentials dan **sebelum** cek soft-deleted, di baris setelah `$credentials = $request->validate(...)`:

```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required'],
    ]);

    // Cek apakah akun subscriber masih pending atau ditolak
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

    // Cek apakah akun dinonaktifkan (soft-deleted)
    if (User::onlyTrashed()->where('email', $credentials['email'])->exists()) {
        return back()->withErrors([
            'email' => 'Akun ini telah dinonaktifkan oleh Pengurus IKMAS AI. Silakan hubungi Administrator untuk informasi lebih lanjut.',
        ])->onlyInput('email');
    }

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();

        if (Auth::user()->isAdmin() || Auth::user()->isStaff()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('member.dashboard'));
    }

    return back()->withErrors([
        'email' => 'Email atau kata sandi yang kamu masukkan tidak sesuai.',
    ])->onlyInput('email');
}
```

- [ ] **Step 4: Jalankan test untuk verifikasi pass**

```bash
php artisan test --filter=SubscriberTest
```

Expected: semua test pass.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/AuthController.php tests/Feature/SubscriberTest.php
git commit -m "feat: block login for pending and rejected subscribers"
```

---

## Task 3: Registrasi — Dua Jalur (Alumni & Subscriber)

**Files:**
- Modify: `app/Http/Controllers/AuthController.php` (tambah method baru)
- Modify: `routes/web.php`
- Create: `resources/views/auth/register-alumni.blade.php`
- Create: `resources/views/auth/register-subscriber.blade.php`
- Create: `resources/views/auth/subscriber-pending.blade.php`
- Test: `tests/Feature/SubscriberTest.php` (tambah test cases)

**Interfaces:**
- Consumes: `User::$fillable` mengandung `'status'` (dari Task 1)
- Produces:
  - `GET /register` → redirect 301 ke `/register/alumni`
  - `GET /register/alumni` → view `auth.register-alumni`
  - `POST /register/alumni` → `AuthController::registerAlumni()` — buat user role=member, status=active, login, redirect ke dashboard
  - `GET /register/subscriber` → view `auth.register-subscriber`
  - `POST /register/subscriber` → `AuthController::registerSubscriber()` — buat user role=subscriber, status=pending, **tanpa login**, redirect ke `/register/subscriber/pending`
  - `GET /register/subscriber/pending` → view `auth.subscriber-pending`

---

- [ ] **Step 1: Tambah test cases untuk registrasi**

Di `tests/Feature/SubscriberTest.php`, tambahkan:

```php
public function test_alumni_can_register_at_new_route(): void
{
    $response = $this->post('/register/alumni', [
        'name'                  => 'Ahmad Rizki',
        'email'                 => 'rizki@alumni.test',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'whatsapp_number'       => '081234567890',
        'alumni_year'           => '2016',
    ]);

    $response->assertRedirect(route('member.dashboard'));
    $this->assertDatabaseHas('users', [
        'email'  => 'rizki@alumni.test',
        'role'   => 'member',
        'status' => 'active',
    ]);
}

public function test_subscriber_can_register(): void
{
    $response = $this->post('/register/subscriber', [
        'name'                  => 'Budi Santoso',
        'email'                 => 'budi@publik.test',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'whatsapp_number'       => '082345678901',
    ]);

    $response->assertRedirect('/register/subscriber/pending');
    $this->assertDatabaseHas('users', [
        'email'  => 'budi@publik.test',
        'role'   => 'subscriber',
        'status' => 'pending',
    ]);
    $this->assertGuest(); // tidak otomatis login
}

public function test_register_old_route_redirects_to_alumni(): void
{
    $response = $this->get('/register');

    $response->assertRedirect('/register/alumni');
}

public function test_subscriber_pending_page_is_accessible(): void
{
    $response = $this->get('/register/subscriber/pending');

    $response->assertStatus(200);
    $response->assertSee('Pendaftaran berhasil');
}
```

- [ ] **Step 2: Jalankan test untuk verifikasi gagal**

```bash
php artisan test --filter=SubscriberTest
```

Expected: FAIL — route-route baru belum ada.

- [ ] **Step 3: Update `routes/web.php`**

Ganti blok auth routes yang ada dengan:

```php
// Auth Routes
Route::redirect('/register', '/register/alumni', 301);

Route::get('/register/alumni', [AuthController::class, 'showAlumniRegisterForm'])->name('register.alumni');
Route::post('/register/alumni', [AuthController::class, 'registerAlumni'])->name('register.alumni.submit');

Route::get('/register/subscriber', [AuthController::class, 'showSubscriberRegisterForm'])->name('register.subscriber');
Route::post('/register/subscriber', [AuthController::class, 'registerSubscriber'])->name('register.subscriber.submit');
Route::get('/register/subscriber/pending', [AuthController::class, 'subscriberPending'])->name('register.subscriber.pending');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
```

**Catatan:** Hapus atau comment out baris lama:
```php
// Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
// Route::post('/register', [AuthController::class, 'register']);
```

- [ ] **Step 4: Tambahkan method-method baru di `AuthController`**

Hapus atau rename method lama `showRegisterForm()` dan `register()`. Ganti dengan:

```php
public function showAlumniRegisterForm()
{
    if (Auth::check()) {
        return redirect()->route('member.dashboard');
    }

    return view('auth.register-alumni');
}

public function registerAlumni(Request $request)
{
    $validated = $request->validate([
        'name'             => ['required', 'string', 'max:255'],
        'email'            => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password'         => ['required', 'string', 'min:8', 'confirmed'],
        'whatsapp_number'  => ['required', 'string', 'max:25'],
        'alumni_year'      => ['required', 'string', 'max:10'],
    ]);

    $user = User::create([
        'name'             => $validated['name'],
        'email'            => $validated['email'],
        'password'         => Hash::make($validated['password']),
        'whatsapp_number'  => $validated['whatsapp_number'],
        'alumni_year'      => $validated['alumni_year'],
        'role'             => 'member',
        'status'           => 'active',
    ]);

    Auth::login($user);

    return redirect()->route('member.dashboard')->with('success', 'Selamat datang di IKMAS AI! Akun alumni berhasil dibuat.');
}

public function showSubscriberRegisterForm()
{
    if (Auth::check()) {
        return redirect()->route('member.dashboard');
    }

    return view('auth.register-subscriber');
}

public function registerSubscriber(Request $request)
{
    $validated = $request->validate([
        'name'            => ['required', 'string', 'max:255'],
        'email'           => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password'        => ['required', 'string', 'min:8', 'confirmed'],
        'whatsapp_number' => ['required', 'string', 'max:25'],
    ]);

    User::create([
        'name'            => $validated['name'],
        'email'           => $validated['email'],
        'password'        => Hash::make($validated['password']),
        'whatsapp_number' => $validated['whatsapp_number'],
        'role'            => 'subscriber',
        'status'          => 'pending',
    ]);

    // Tidak login otomatis — tunggu approval admin
    return redirect()->route('register.subscriber.pending');
}

public function subscriberPending()
{
    return view('auth.subscriber-pending');
}
```

- [ ] **Step 5: Buat view `resources/views/auth/register-alumni.blade.php`**

Copy konten dari `resources/views/auth/register.blade.php` yang sudah ada. Hanya ubah dua hal:
1. `action="{{ url('/register') }}"` → `action="{{ route('register.alumni.submit') }}"`
2. Tambahkan link ke halaman subscriber di bagian bawah card (setelah "Sudah memiliki akun?"):

```html
<div style="margin-top: 0.75rem; text-align: center; font-size: 0.875rem; color: var(--text-muted);">
    Bukan alumni Assalaam? 
    <a href="{{ route('register.subscriber') }}" style="color: var(--primary); font-weight: 700;">Daftar sebagai Subscriber</a>
</div>
```

- [ ] **Step 6: Buat view `resources/views/auth/register-subscriber.blade.php`**

Buat file baru dengan konten berikut (adaptasi dari register-alumni.blade.php, tanpa field `alumni_year`):

```blade
@extends('layouts.app')

@section('title', 'Daftar Subscriber — IKMAS AI Learning Center')

@section('content')
<div class="container" style="padding-top: 3.5rem; padding-bottom: 5rem;">
    <div style="max-width: 500px; margin: 0 auto;">
        <div class="card card-elevated" style="padding: 2.5rem;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div class="brand-icon" style="margin: 0 auto 1rem auto;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5"></path>
                        <path d="M2 12l10 5 10-5"></path>
                    </svg>
                </div>
                <h1 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 0.5rem;">Daftar Subscriber</h1>
                <p style="color: var(--text-muted); font-size: 0.9rem;">
                    Akses konten pembelajaran AI IKMAS. Pendaftaran akan ditinjau oleh pengurus sebelum aktif.
                </p>
            </div>

            @if ($errors->any())
                <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: var(--radius-md); padding: 0.875rem 1rem; margin-bottom: 1.5rem; color: #ef4444; font-size: 0.875rem;">
                    @foreach ($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register.subscriber.submit') }}" style="display: flex; flex-direction: column; gap: 1.15rem;">
                @csrf

                <div>
                    <label for="name" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">
                        Nama Lengkap
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           placeholder="Contoh: Budi Santoso"
                           style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                </div>

                <div>
                    <label for="email" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">
                        Alamat Email
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           placeholder="budi@email.com"
                           style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                </div>

                <div>
                    <label for="whatsapp_number" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">
                        No. WhatsApp
                    </label>
                    <input type="text" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required
                           placeholder="08123456789"
                           style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                </div>

                <div>
                    <label for="password" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">
                        Kata Sandi (Minimal 8 Karakter)
                    </label>
                    <input type="password" id="password" name="password" required
                           placeholder="••••••••"
                           style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                </div>

                <div>
                    <label for="password_confirmation" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">
                        Konfirmasi Kata Sandi
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           placeholder="••••••••"
                           style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                </div>

                <div style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.3); border-radius: var(--radius-md); padding: 0.875rem 1rem; font-size: 0.85rem; color: var(--text-muted);">
                    ⏳ Setelah mendaftar, akun Anda akan ditinjau oleh Pengurus IKMAS AI sebelum dapat diaktifkan.
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem;">
                    Daftar Sebagai Subscriber →
                </button>
            </form>

            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); text-align: center; font-size: 0.875rem; color: var(--text-muted);">
                Sudah memiliki akun? <a href="{{ url('/login') }}" style="color: var(--primary); font-weight: 700;">Masuk di sini</a>
            </div>
            <div style="margin-top: 0.75rem; text-align: center; font-size: 0.875rem; color: var(--text-muted);">
                Alumni Assalaam? <a href="{{ route('register.alumni') }}" style="color: var(--primary); font-weight: 700;">Daftar sebagai Alumni</a>
            </div>
        </div>
    </div>
</div>
@endsection
```

- [ ] **Step 7: Buat view `resources/views/auth/subscriber-pending.blade.php`**

```blade
@extends('layouts.app')

@section('title', 'Pendaftaran Diterima — IKMAS AI Learning Center')

@section('content')
<div class="container" style="padding-top: 5rem; padding-bottom: 5rem;">
    <div style="max-width: 520px; margin: 0 auto; text-align: center;">
        <div class="card card-elevated" style="padding: 3rem 2.5rem;">
            <div style="width: 72px; height: 72px; border-radius: 50%; background: rgba(245,158,11,0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>

            <h1 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 1rem;">Pendaftaran Berhasil!</h1>
            <p style="color: var(--text-muted); line-height: 1.7; margin-bottom: 1.5rem;">
                Terima kasih telah mendaftar di <strong>IKMAS AI Learning Center</strong>.<br>
                Akun Anda sedang ditinjau oleh pengurus. Kami akan menghubungi Anda melalui <strong>WhatsApp</strong> setelah akun diaktifkan.
            </p>
            <div style="background: var(--bg-surface-alt); border-radius: var(--radius-md); padding: 1rem 1.25rem; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 2rem;">
                ⏳ Proses peninjauan biasanya memakan waktu <strong>1–2 hari kerja</strong>.
            </div>

            <a href="{{ url('/') }}" class="btn btn-primary" style="display: inline-block;">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
```

- [ ] **Step 8: Jalankan test untuk verifikasi pass**

```bash
php artisan test --filter=SubscriberTest
```

Expected: semua test pass.

- [ ] **Step 9: Pastikan semua test pass**

```bash
php artisan test
```

- [ ] **Step 10: Commit**

```bash
git add app/Http/Controllers/AuthController.php routes/web.php resources/views/auth/
git commit -m "feat: split registration into alumni and subscriber flows"
```

---

## Task 4: Batasi Akses Subscriber — Blokir Submit Showcase

**Files:**
- Modify: `app/Http/Controllers/Member/MemberDashboardController.php`
- Test: `tests/Feature/SubscriberTest.php` (tambah test cases)

**Interfaces:**
- Consumes: `User::isSubscriber(): bool` (dari Task 1), route `member.showcase.create` dan `member.showcase.store`
- Produces: `createShowcase()` dan `storeShowcase()` mengembalikan abort(403) jika user adalah subscriber

---

- [ ] **Step 1: Tambah test cases untuk akses subscriber**

Di `tests/Feature/SubscriberTest.php`, tambahkan:

```php
public function test_active_subscriber_cannot_access_create_showcase_page(): void
{
    $subscriber = User::factory()->create([
        'role'   => 'subscriber',
        'status' => 'active',
    ]);

    $response = $this->actingAs($subscriber)->get('/member/showcase/create');

    $response->assertStatus(403);
}

public function test_active_subscriber_cannot_submit_showcase(): void
{
    $subscriber = User::factory()->create([
        'role'   => 'subscriber',
        'status' => 'active',
    ]);

    $response = $this->actingAs($subscriber)->post('/member/showcase', [
        'title'       => 'Karya Subscriber',
        'description' => 'Test showcase dari subscriber',
        'tools_used'  => 'ChatGPT',
    ]);

    $response->assertStatus(403);
}

public function test_active_alumni_member_can_access_create_showcase_page(): void
{
    $member = User::factory()->create([
        'role'   => 'member',
        'status' => 'active',
    ]);

    $response = $this->actingAs($member)->get('/member/showcase/create');

    $response->assertStatus(200);
}
```

- [ ] **Step 2: Jalankan test untuk verifikasi gagal**

```bash
php artisan test --filter=SubscriberTest
```

Expected: test subscriber showcase access FAIL — saat ini semua user yang login bisa akses.

- [ ] **Step 3: Update `MemberDashboardController`**

Di `app/Http/Controllers/Member/MemberDashboardController.php`, update dua method:

```php
public function createShowcase()
{
    if (auth()->user()->isSubscriber()) {
        abort(403, 'Fitur ini hanya tersedia untuk anggota alumni. Subscriber tidak dapat mengajukan showcase.');
    }

    return view('member.showcase-create');
}

public function storeShowcase(Request $request)
{
    if (auth()->user()->isSubscriber()) {
        abort(403, 'Fitur ini hanya tersedia untuk anggota alumni. Subscriber tidak dapat mengajukan showcase.');
    }

    // ... sisa kode yang sudah ada tidak berubah
    $validated = $request->validate([
        'title'       => ['required', 'string', 'max:255'],
        'description' => ['required', 'string'],
        'tools_used'  => ['required', 'string', 'max:255'],
        'project_url' => ['nullable', 'url', 'max:255'],
        'impact_story'=> ['nullable', 'string'],
        'image'       => ['nullable', 'image', 'max:2048'],
    ]);

    $imageUrl = null;
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('showcase', 'public');
        $imageUrl = '/storage/' . $path;
    }

    $slugBase = \Illuminate\Support\Str::slug($validated['title']);
    $slug = $slugBase;
    $counter = 1;
    while (\App\Models\Showcase::where('slug', $slug)->exists()) {
        $slug = $slugBase . '-' . $counter++;
    }

    \App\Models\Showcase::create([
        'user_id'      => Auth::id(),
        'title'        => $validated['title'],
        'slug'         => $slug,
        'description'  => $validated['description'],
        'tools_used'   => $validated['tools_used'],
        'project_url'  => $validated['project_url'] ?? null,
        'impact_story' => $validated['impact_story'] ?? null,
        'image_url'    => $imageUrl,
        'status'       => 'pending',
    ]);

    return redirect()->route('member.dashboard')->with('success', 'Karya kamu berhasil diajukan! Pengurus akan meninjau dan mengurasi karyamu sebelum ditampilkan di galeri publik.');
}
```

- [ ] **Step 4: Jalankan test untuk verifikasi pass**

```bash
php artisan test --filter=SubscriberTest
```

Expected: semua test pass.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Member/MemberDashboardController.php tests/Feature/SubscriberTest.php
git commit -m "feat: restrict subscriber from submitting showcase"
```

---

## Task 5: Admin Panel — Modul Kelola Subscriber

**Files:**
- Create: `app/Http/Controllers/Admin/AdminSubscriberController.php`
- Create: `resources/views/admin/subscribers/index.blade.php`
- Modify: `routes/web.php`
- Modify: `app/Http/Controllers/Admin/AdminDashboardController.php`
- Test: `tests/Feature/SubscriberTest.php` (tambah test cases)

**Interfaces:**
- Consumes: `User::isSubscriber(): bool`, `User::isPending(): bool`, `User::isActive(): bool` (dari Task 1)
- Produces:
  - `GET /admin/subscribers` → `AdminSubscriberController::index()` — list subscriber dengan filter tab
  - `POST /admin/subscribers/{user}/approve` → `AdminSubscriberController::approve()` — set status=active
  - `POST /admin/subscribers/{user}/reject` → `AdminSubscriberController::reject()` — set status=rejected
  - `DELETE /admin/subscribers/{user}` → `AdminSubscriberController::destroy()` — soft delete
  - Permission key: `'subscribers'`
  - `AdminDashboardController::index()` passes `$metrics['pending_subscribers']` ke view

---

- [ ] **Step 1: Tambah test cases untuk admin subscriber**

Di `tests/Feature/SubscriberTest.php`, tambahkan:

```php
public function test_admin_can_approve_subscriber(): void
{
    $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
    $subscriber = User::factory()->create([
        'role'   => 'subscriber',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)
        ->post("/admin/subscribers/{$subscriber->id}/approve");

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertEquals('active', $subscriber->fresh()->status);
}

public function test_admin_can_reject_subscriber(): void
{
    $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
    $subscriber = User::factory()->create([
        'role'   => 'subscriber',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)
        ->post("/admin/subscribers/{$subscriber->id}/reject");

    $response->assertRedirect();
    $response->assertSessionHas('info');
    $this->assertEquals('rejected', $subscriber->fresh()->status);
}

public function test_admin_can_view_subscriber_list(): void
{
    $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
    User::factory()->create([
        'role'   => 'subscriber',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->get('/admin/subscribers');

    $response->assertStatus(200);
}

public function test_non_admin_cannot_access_subscriber_module(): void
{
    $member = User::factory()->create(['role' => 'member', 'status' => 'active']);

    $response = $this->actingAs($member)->get('/admin/subscribers');

    $response->assertStatus(403);
}
```

- [ ] **Step 2: Jalankan test untuk verifikasi gagal**

```bash
php artisan test --filter=SubscriberTest
```

Expected: FAIL — route dan controller belum ada.

- [ ] **Step 3: Buat `AdminSubscriberController`**

Buat file `app/Http/Controllers/Admin/AdminSubscriberController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminSubscriberController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'pending');

        $query = User::where('role', 'subscriber')->latest();

        if ($filter === 'pending') {
            $query->where('status', 'pending');
        } elseif ($filter === 'active') {
            $query->where('status', 'active');
        } elseif ($filter === 'rejected') {
            $query->where('status', 'rejected');
        }
        // 'all' = tidak ada filter tambahan

        $subscribers = $query->paginate(20)->withQueryString();

        $counts = [
            'all'      => User::where('role', 'subscriber')->count(),
            'pending'  => User::where('role', 'subscriber')->where('status', 'pending')->count(),
            'active'   => User::where('role', 'subscriber')->where('status', 'active')->count(),
            'rejected' => User::where('role', 'subscriber')->where('status', 'rejected')->count(),
        ];

        return view('admin.subscribers.index', compact('subscribers', 'counts', 'filter'));
    }

    public function approve(User $user)
    {
        if (! $user->isSubscriber()) {
            return back()->with('error', 'User ini bukan subscriber.');
        }

        $user->update(['status' => 'active']);

        return back()->with('success', "Akun {$user->name} berhasil diaktifkan.");
    }

    public function reject(User $user)
    {
        if (! $user->isSubscriber()) {
            return back()->with('error', 'User ini bukan subscriber.');
        }

        $user->update(['status' => 'rejected']);

        return back()->with('info', "Pendaftaran {$user->name} telah ditolak.");
    }

    public function destroy(User $user)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Hanya Administrator yang berwenang menghapus akun subscriber.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "Akun subscriber {$name} berhasil dihapus.");
    }
}
```

- [ ] **Step 4: Tambahkan routes subscriber di `routes/web.php`**

Di dalam blok admin routes (setelah blok `manage_team`), tambahkan:

```php
// Modul Subscriber
Route::middleware('permission:subscribers')->group(function () {
    Route::get('/subscribers', [\App\Http\Controllers\Admin\AdminSubscriberController::class, 'index'])->name('subscribers.index');
    Route::post('/subscribers/{user}/approve', [\App\Http\Controllers\Admin\AdminSubscriberController::class, 'approve'])->name('subscribers.approve');
    Route::post('/subscribers/{user}/reject', [\App\Http\Controllers\Admin\AdminSubscriberController::class, 'reject'])->name('subscribers.reject');
    Route::delete('/subscribers/{user}', [\App\Http\Controllers\Admin\AdminSubscriberController::class, 'destroy'])->name('subscribers.destroy');
});
```

- [ ] **Step 5: Update `AdminDashboardController` — tambah metric `pending_subscribers`**

Di `app/Http/Controllers/Admin/AdminDashboardController.php`, update array `$metrics`:

```php
$metrics = [
    'total_members'       => User::where('role', 'member')->count(),
    'total_materials'     => LearningMaterial::count(),
    'total_prompts'       => Prompt::count(),
    'total_events'        => Event::count(),
    'pending_curation'    => Showcase::where('status', 'pending')->count(),
    'approved_showcases'  => Showcase::where('status', 'approved')->count(),
    'pending_subscribers' => User::where('role', 'subscriber')->where('status', 'pending')->count(),
];
```

- [ ] **Step 6: Buat view `resources/views/admin/subscribers/index.blade.php`**

```blade
@extends('layouts.admin')

@section('title', 'Kelola Subscriber — IKMAS AI')
@section('page-title', 'Kelola Subscriber')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 800; margin: 0;">Kelola Subscriber</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0.25rem 0 0 0;">
            Tinjau dan kelola pendaftaran subscriber non-alumni.
        </p>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.25); border-radius: var(--radius-md); padding: 0.875rem 1rem; margin-bottom: 1.25rem; color: #10b981; font-size: 0.875rem;">
        ✓ {{ session('success') }}
    </div>
@endif
@if(session('info'))
    <div style="background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.25); border-radius: var(--radius-md); padding: 0.875rem 1rem; margin-bottom: 1.25rem; color: #3b82f6; font-size: 0.875rem;">
        ℹ {{ session('info') }}
    </div>
@endif
@if(session('error'))
    <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: var(--radius-md); padding: 0.875rem 1rem; margin-bottom: 1.25rem; color: #ef4444; font-size: 0.875rem;">
        ✗ {{ session('error') }}
    </div>
@endif

{{-- Filter Tabs --}}
<div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; flex-wrap: wrap;">
    @foreach(['pending' => 'Menunggu Tinjauan', 'active' => 'Aktif', 'rejected' => 'Ditolak', 'all' => 'Semua'] as $key => $label)
        <a href="{{ route('admin.subscribers.index', ['filter' => $key]) }}"
           class="btn {{ $filter === $key ? 'btn-primary' : 'btn-secondary' }} btn-sm"
           style="border-radius: 999px; padding: 0.4rem 1rem;">
            {{ $label }} ({{ $counts[$key] ?? 0 }})
        </a>
    @endforeach
</div>

{{-- Table --}}
<div class="card" style="padding: 0; overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
        <thead>
            <tr style="background: var(--bg-surface-alt); border-bottom: 1px solid var(--border-color);">
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Nama</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Email</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">WhatsApp</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Tanggal Daftar</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Status</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subscribers as $subscriber)
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 1rem 1.25rem; font-weight: 600;">{{ $subscriber->name }}</td>
                <td style="padding: 1rem 1.25rem; color: var(--text-muted);">{{ $subscriber->email }}</td>
                <td style="padding: 1rem 1.25rem;">
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $subscriber->whatsapp_number) }}"
                       target="_blank" style="color: var(--primary);">
                        {{ $subscriber->whatsapp_number ?? '-' }}
                    </a>
                </td>
                <td style="padding: 1rem 1.25rem; color: var(--text-muted); font-size: 0.85rem;">
                    {{ $subscriber->created_at->format('d M Y, H:i') }}
                </td>
                <td style="padding: 1rem 1.25rem;">
                    @if($subscriber->status === 'pending')
                        <span style="background: rgba(245,158,11,0.15); color: #f59e0b; border-radius: 999px; padding: 0.2rem 0.65rem; font-size: 0.78rem; font-weight: 700;">Menunggu</span>
                    @elseif($subscriber->status === 'active')
                        <span style="background: rgba(16,185,129,0.15); color: #10b981; border-radius: 999px; padding: 0.2rem 0.65rem; font-size: 0.78rem; font-weight: 700;">Aktif</span>
                    @else
                        <span style="background: rgba(239,68,68,0.15); color: #ef4444; border-radius: 999px; padding: 0.2rem 0.65rem; font-size: 0.78rem; font-weight: 700;">Ditolak</span>
                    @endif
                </td>
                <td style="padding: 1rem 1.25rem; text-align: right;">
                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end; flex-wrap: wrap;">
                        @if($subscriber->status === 'pending')
                            <form method="POST" action="{{ route('admin.subscribers.approve', $subscriber) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3);">
                                    ✓ Setujui
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.subscribers.reject', $subscriber) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.25);"
                                        onclick="return confirm('Yakin ingin menolak pendaftaran {{ $subscriber->name }}?')">
                                    ✗ Tolak
                                </button>
                            </form>
                        @elseif($subscriber->status === 'rejected')
                            <form method="POST" action="{{ route('admin.subscribers.approve', $subscriber) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3);">
                                    ↩ Aktifkan
                                </button>
                            </form>
                        @endif

                        @if(auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('admin.subscribers.destroy', $subscriber) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-secondary"
                                    onclick="return confirm('Hapus akun {{ $subscriber->name }} secara permanen?')">
                                Hapus
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                    Tidak ada subscriber dengan filter ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($subscribers->hasPages())
<div style="margin-top: 1.5rem;">
    {{ $subscribers->links() }}
</div>
@endif

@endsection
```

- [ ] **Step 7: Jalankan test untuk verifikasi pass**

```bash
php artisan test --filter=SubscriberTest
```

Expected: semua test pass.

- [ ] **Step 8: Jalankan seluruh test suite**

```bash
php artisan test
```

Expected: semua test pass.

- [ ] **Step 9: Commit**

```bash
git add app/Http/Controllers/Admin/AdminSubscriberController.php \
        app/Http/Controllers/Admin/AdminDashboardController.php \
        resources/views/admin/subscribers/ \
        routes/web.php \
        tests/Feature/SubscriberTest.php
git commit -m "feat: add admin subscriber management module with approve/reject"
```

---

## Verification Checklist (Manual)

Setelah semua task selesai, lakukan verifikasi manual berikut:

- [ ] Buka `GET /register` → harus redirect ke `/register/alumni`
- [ ] Register alumni di `/register/alumni` → login otomatis, masuk dashboard
- [ ] Register subscriber di `/register/subscriber` → redirect ke halaman pending, tidak login
- [ ] Coba login dengan akun pending → pesan error "masih dalam proses peninjauan"
- [ ] Login sebagai admin → buka `/admin/subscribers` → lihat subscriber pending
- [ ] Klik "Setujui" → subscriber menjadi status aktif
- [ ] Login sebagai subscriber aktif → bisa akses materi, prompt, agenda
- [ ] Subscriber aktif coba buka `/member/showcase/create` → error 403
- [ ] Register alumni tetap berfungsi normal
