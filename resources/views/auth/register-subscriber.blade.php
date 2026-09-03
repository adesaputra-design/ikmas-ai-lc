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
