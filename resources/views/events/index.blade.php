@extends('layouts.app')

@section('title', 'Kalender Agenda & Study Group — IKMAS AI Learning Center')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    <!-- Page Header -->
    <div style="text-align: center; max-width: 750px; margin: 0 auto 3rem auto;">
        <span class="badge badge-primary" style="margin-bottom: 0.75rem;">Jadwal & Pertemuan Daring</span>
        <h1 style="font-size: 2.75rem; font-weight: 800; margin-bottom: 1rem; letter-spacing: -0.02em;">
            Kalender Agenda & Study Group
        </h1>
        <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.6;">
            Ikuti sesi belajar bersama daring mingguan, workshop praktis, dan sharing session bersama sesama alumni Assalaam. Terbuka gratis untuk semua angkatan.
        </p>
    </div>

    <!-- Upcoming Events Section -->
    <div style="margin-bottom: 4rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.75rem;">
            <span class="badge badge-emerald" style="font-size: 0.85rem; padding: 0.35rem 0.75rem;">
                ● Sesi Mendatang
            </span>
            <h2 style="font-size: 1.75rem; font-weight: 800;">Agenda Terdekat</h2>
        </div>

        @if($upcomingEvents->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.75rem;">
                @foreach($upcomingEvents as $event)
                    <div class="card card-elevated" style="border-left: 4px solid var(--accent-emerald); display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                <span class="badge badge-emerald">
                                    {{ $event->status_label }}
                                </span>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">
                                    ⏱ {{ $event->duration_minutes }} Menit
                                </span>
                            </div>

                            <h3 style="font-size: 1.3rem; font-weight: 800; margin-bottom: 0.75rem; line-height: 1.35;">
                                <a href="{{ url('/agenda/' . $event->slug) }}" style="color: var(--text-main);">
                                    {{ $event->title }}
                                </a>
                            </h3>

                            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--primary); font-size: 0.95rem; font-weight: 600; margin-bottom: 1rem;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <span>{{ $event->formatted_date }}</span>
                            </div>

                            <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.25rem;">
                                {{ Str::limit($event->description, 130) }}
                            </p>

                            <!-- Speaker Info -->
                            <div style="background: var(--bg-surface-alt); padding: 0.75rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem;">
                                <div style="font-size: 0.8rem; color: var(--text-muted);">Fasilitator / Pemateri:</div>
                                <div style="font-weight: 700; font-size: 0.95rem;">{{ $event->speaker_name }}</div>
                                @if($event->speaker_title)
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $event->speaker_title }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; display: flex; gap: 0.75rem; justify-content: space-between; align-items: center;">
                            <a href="{{ url('/agenda/' . $event->slug) }}" class="btn btn-secondary btn-sm">
                                Detail Sesi
                            </a>

                            @if($event->location_url)
                                <a href="{{ $event->location_url }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polygon points="23 7 16 12 23 17 23 7"></polygon>
                                        <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                                    </svg>
                                    <span>Gabung Zoom / Meet</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card" style="text-align: center; padding: 3rem 1.5rem;">
                <p style="color: var(--text-muted); margin-bottom: 0;">Belum ada agenda terdekat yang dijadwalkan. Pantau terus grup WhatsApp IKMAS AI untuk pengumuman jadwal Study Group berikutnya!</p>
            </div>
        @endif
    </div>

    <!-- Past Events Archive Section -->
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.75rem;">
            <span class="badge" style="background: var(--bg-surface-alt); color: var(--text-muted); font-size: 0.85rem; padding: 0.35rem 0.75rem;">
                Arsip Kegiatan
            </span>
            <h2 style="font-size: 1.5rem; font-weight: 800;">Sesi yang Telah Selesai</h2>
        </div>

        @if($pastEvents->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
                @foreach($pastEvents as $past)
                    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                <span class="badge" style="background: var(--bg-surface-alt); color: var(--text-muted);">
                                    Selesai
                                </span>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">
                                    {{ $past->event_date->translatedFormat('d M Y') }}
                                </span>
                            </div>

                            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem; line-height: 1.35;">
                                <a href="{{ url('/agenda/' . $past->slug) }}" style="color: var(--text-main);">
                                    {{ $past->title }}
                                </a>
                            </h3>

                            <p style="color: var(--text-muted); font-size: 0.875rem; line-height: 1.6; margin-bottom: 1rem;">
                                {{ Str::limit($past->description, 110) }}
                            </p>
                        </div>

                        <div style="border-top: 1px solid var(--border-color); padding-top: 0.875rem; display: flex; gap: 0.5rem; justify-content: flex-end;">
                            @if($past->recording_url)
                                <a href="{{ $past->recording_url }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm" title="Tonton Rekaman">
                                    🎥 Rekaman
                                </a>
                            @endif
                            <a href="{{ url('/agenda/' . $past->slug) }}" class="btn btn-secondary btn-sm">
                                Detail →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="display: flex; justify-content: center; margin-top: 2.5rem;">
                {{ $pastEvents->links() }}
            </div>
        @else
            <p style="color: var(--text-muted); font-size: 0.95rem;">Belum ada arsip sesi yang selesai.</p>
        @endif
    </div>
</div>
@endsection
