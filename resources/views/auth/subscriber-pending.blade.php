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
