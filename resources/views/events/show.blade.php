@extends('layouts.app')

@section('title', $event->title . ' — Agenda IKMAS AI')

@section('content')
<div class="container" style="padding-top: 2.5rem; padding-bottom: 5rem;">
    <!-- Breadcrumbs -->
    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 2rem;">
        <a href="{{ url('/') }}">Beranda</a>
        <span>/</span>
        <a href="{{ url('/agenda') }}">Agenda Event</a>
        <span>/</span>
        <span style="color: var(--primary); font-weight: 600;">{{ Str::limit($event->title, 35) }}</span>
    </div>

    <article class="card" style="padding: 2.5rem; max-width: 850px; margin: 0 auto;">
        <!-- Header Badges -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
            <span class="badge badge-{{ $event->status_color }}">
                {{ $event->status_label }}
            </span>
            <span style="font-size: 0.85rem; color: var(--text-muted);">
                ⏱ Durasi: {{ $event->duration_minutes }} Menit
            </span>
        </div>

        <h1 style="font-size: 2.25rem; font-weight: 800; line-height: 1.25; margin-bottom: 1.25rem;">
            {{ $event->title }}
        </h1>

        <!-- Date & Location Box -->
        <div style="background: var(--bg-surface-alt); border: 1px solid var(--border-color); border-radius: var(--radius-xl); padding: 1.5rem; margin-bottom: 2rem; display: flex; flex-direction: column; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; color: var(--primary); font-weight: 700; font-size: 1.1rem;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span>{{ $event->formatted_date }}</span>
            </div>

            @if($event->status === 'upcoming' && $event->location_url)
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                    <div>
                        <span style="font-size: 0.85rem; color: var(--text-muted); display: block;">Platform Pertemuan:</span>
                        <span style="font-weight: 600;">Ruang Pertemuan Daring (Zoom / Meet)</span>
                    </div>
                    <a href="{{ $event->location_url }}" target="_blank" rel="noopener" class="btn btn-primary">
                        Buka Tautan Pertemuan Daring ↗
                    </a>
                </div>
            @endif
        </div>

        <!-- Description -->
        <div style="font-size: 1.05rem; line-height: 1.8; color: var(--text-main); margin-bottom: 2.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.75rem;">Tentang Sesi Ini</h3>
            <p>{{ $event->description }}</p>

            <div style="background: rgba(37,99,235,0.04); border-radius: var(--radius-lg); padding: 1.25rem; margin-top: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 700; color: var(--primary); margin-bottom: 0.5rem;">Format AI Study Group: 70% Praktik, 30% Konsep</h4>
                <ul style="margin-left: 1.25rem; font-size: 0.9rem; color: var(--text-muted);">
                    <li>10 min — Pembukaan & orientasi tema</li>
                    <li>15 min — Konsep kunci & dasar pemikiran</li>
                    <li>20 min — Demonstrasi langsung (hands-on)</li>
                    <li>20 min — Praktik mandiri peserta</li>
                    <li>10 min — Tanya jawab & berbagi pengalaman</li>
                    <li>5 min  — Penutupan & tindak lanjut</li>
                </ul>
            </div>
        </div>

        <!-- Speaker Profile -->
        <div style="background: var(--bg-surface-alt); border-radius: var(--radius-xl); padding: 1.5rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: center;">
            <div style="width: 3.5rem; height: 3.5rem; border-radius: 50%; background: linear-gradient(135deg, #1e40af, #0284c7); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; flex-shrink: 0;">
                🎙
            </div>
            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Narasumber / Fasilitator</span>
                <h4 style="font-size: 1.15rem; font-weight: 700;">{{ $event->speaker_name }}</h4>
                @if($event->speaker_title)
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0;">{{ $event->speaker_title }}</p>
                @endif
            </div>
        </div>

        <!-- Recording & Slides if completed -->
        @if($event->recording_url || $event->materials_url)
            <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                @if($event->recording_url)
                    <a href="{{ $event->recording_url }}" target="_blank" rel="noopener" class="btn btn-primary">
                        🎥 Tonton Rekaman Video
                    </a>
                @endif
                @if($event->materials_url)
                    <a href="{{ $event->materials_url }}" target="_blank" rel="noopener" class="btn btn-secondary">
                        📑 Unduh Berkas Slide / Materi
                    </a>
                @endif
            </div>
        @endif
    </article>
</div>
@endsection
