@extends('layouts.app')

@section('title', 'Masuk ke Portal — IKMAS AI Learning Center')

@section('content')
<div class="container" style="padding-top: 4rem; padding-bottom: 5rem;">
    <div style="max-width: 450px; margin: 0 auto;">
        <div class="card card-elevated" style="padding: 2.5rem;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div class="brand-icon" style="margin: 0 auto 1rem auto;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5"></path>
                        <path d="M2 12l10 5 10-5"></path>
                    </svg>
                </div>
                <h1 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 0.5rem;">Masuk ke Portal</h1>
                <p style="color: var(--text-muted); font-size: 0.9rem;">
                    Gunakan akun alumni terdaftarmu untuk mengakses area member atau panel admin.
                </p>
            </div>

            @if ($errors->any())
                <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: var(--radius-md); padding: 0.875rem 1rem; margin-bottom: 1.5rem; color: #ef4444; font-size: 0.875rem;">
                    @foreach ($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ url('/login') }}" style="display: flex; flex-direction: column; gap: 1.25rem;">
                @csrf

                <div>
                    <label for="email" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">
                        Alamat Email
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="nama@alumni.test"
                           style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                        <label for="password" style="font-size: 0.875rem; font-weight: 600;">Kata Sandi</label>
                    </div>
                    <input type="password" id="password" name="password" required
                           placeholder="••••••••"
                           style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                </div>

                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" id="remember" name="remember" style="accent-color: var(--primary);">
                    <label for="remember" style="font-size: 0.85rem; color: var(--text-muted); cursor: pointer;">
                        Ingat saya di perangkat ini
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem;">
                    Masuk Sekarang →
                </button>
            </form>

            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); text-align: center; font-size: 0.875rem; color: var(--text-muted);">
                Belum punya akun alumni? <a href="{{ url('/register') }}" style="color: var(--primary); font-weight: 700;">Daftar di sini</a>
            </div>
        </div>
    </div>
</div>
@endsection
