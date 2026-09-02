@extends('layouts.app')

@section('title', $showcase->title . ' — Showcase Karya IKMAS AI')

@section('content')
<div class="container" style="padding-top: 2.5rem; padding-bottom: 5rem;">
    <!-- Breadcrumbs -->
    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 2rem;">
        <a href="{{ url('/') }}">Beranda</a>
        <span>/</span>
        <a href="{{ url('/showcase') }}">Showcase Karya</a>
        <span>/</span>
        <span style="color: var(--primary); font-weight: 600;">{{ Str::limit($showcase->title, 35) }}</span>
    </div>

    <article class="card" style="padding: 2.5rem; max-width: 850px; margin: 0 auto;">
        <!-- Header Tags -->
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
            <span class="badge badge-emerald">Karya Terverifikasi</span>
            <span class="badge badge-cyan">🛠 {{ $showcase->tools_used }}</span>
        </div>

        <h1 style="font-size: 2.25rem; font-weight: 800; line-height: 1.25; margin-bottom: 1.25rem;">
            {{ $showcase->title }}
        </h1>

        <!-- Creator info card -->
        <div style="background: var(--bg-surface-alt); border-radius: var(--radius-xl); padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 3rem; height: 3rem; border-radius: 50%; background: linear-gradient(135deg, #1e40af, #0284c7); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.25rem; font-weight: 700;">
                    {{ strtoupper(substr($showcase->user->name ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Kreator / Pembuat</div>
                    <div style="font-weight: 800; font-size: 1.1rem;">{{ $showcase->user->name ?? 'Alumni Assalaam' }}</div>
                    @if($showcase->user->alumni_year)
                        <div style="font-size: 0.85rem; color: var(--primary); font-weight: 600;">Alumni Angkatan {{ $showcase->user->alumni_year }}</div>
                    @endif
                </div>
            </div>

            @if($showcase->project_url)
                <a href="{{ $showcase->project_url }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm">
                    Kunjungi Tautan Proyek ↗
                </a>
            @endif
        </div>

        <!-- Screenshot if any -->
        @if($showcase->image_url)
            <div style="margin-bottom: 2rem; border-radius: var(--radius-xl); overflow: hidden; border: 1px solid var(--border-color);">
                <img src="{{ $showcase->image_url }}" alt="{{ $showcase->title }}" style="width: 100%; max-height: 450px; object-fit: cover;">
            </div>
        @endif

        <!-- Description -->
        <div style="font-size: 1.05rem; line-height: 1.8; color: var(--text-main); margin-bottom: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.75rem;">Deskripsi & Cara Kerja</h3>
            <p style="white-space: pre-line;">{{ $showcase->description }}</p>
        </div>

        <!-- Impact Story Box -->
        @if($showcase->impact_story)
            <div style="background: linear-gradient(135deg, rgba(16,185,129,0.08) 0%, rgba(2,132,199,0.08) 100%); border-left: 4px solid var(--accent-emerald); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 2rem;">
                <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--accent-emerald); margin-bottom: 0.5rem;">Cerita Dampak & Manfaat Nyata</h4>
                <p style="font-size: 0.95rem; line-height: 1.7; color: var(--text-main); margin-bottom: 0;">
                    {{ $showcase->impact_story }}
                </p>
            </div>
        @endif
    </article>
</div>
@endsection
