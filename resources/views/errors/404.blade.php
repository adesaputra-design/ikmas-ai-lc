@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan (404) — IKMAS AI Learning Center')

@section('content')
<section class="container" style="padding: 6rem 1.5rem; text-align: center;">
    <div style="max-width: 550px; margin: 0 auto;">
        <div style="font-size: 5rem; font-weight: 800; line-height: 1; margin-bottom: 1rem;" class="text-gradient">
            404
        </div>
        
        <h1 style="font-size: 2rem; margin-bottom: 1rem;">Halaman Tidak Ditemukan</h1>
        
        <p style="color: var(--text-muted); font-size: 1.05rem; line-height: 1.6; margin-bottom: 2rem;">
            Maaf, halaman atau tautan yang kamu tuju sepertinya telah dipindahkan atau belum tersedia. Jangan khawatir, kamu bisa kembali ke beranda atau menjelajahi materi belajar kami.
        </p>
        
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="{{ url('/') }}" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>Kembali ke Beranda</span>
            </a>
            
            <a href="{{ url('/materi') }}" class="btn btn-secondary">
                <span>Jelajahi Materi Belajar</span>
            </a>
        </div>
    </div>
</section>
@endsection
