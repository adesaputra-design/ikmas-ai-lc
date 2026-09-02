@extends('layouts.app')

@section('title', 'Panel Pengurus & Kurasi — IKMAS AI Learning Center')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    <!-- Flash Messages -->
    @if(session('success'))
        <div style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: var(--radius-lg); padding: 1rem 1.25rem; margin-bottom: 2rem; color: #10b981; display: flex; align-items: center; gap: 0.75rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span style="font-weight: 600;">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div style="background: rgba(14,165,233,0.1); border: 1px solid rgba(14,165,233,0.3); border-radius: var(--radius-lg); padding: 1rem 1.25rem; margin-bottom: 2rem; color: #0ea5e9; display: flex; align-items: center; gap: 0.75rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span style="font-weight: 600;">{{ session('info') }}</span>
        </div>
    @endif

    <!-- Admin Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <span class="badge badge-primary" style="margin-bottom: 0.5rem;">Ruang Kerja Administrator</span>
            <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.02em;">Panel Pengurus & Kurasi IKMAS AI</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">
                Kelola konten, moderasi submisi karya alumni, dan pantau pertumbuhan komunitas.
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ url('/showcase') }}" class="btn btn-secondary btn-sm" target="_blank">Lihat Web Publik ↗</a>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 3.5rem;">
        <div class="card" style="padding: 1.25rem;">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">
                Total Member
            </div>
            <div style="font-size: 2rem; font-weight: 800; color: var(--primary);">
                {{ $metrics['total_members'] }}
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Alumni terdaftar</div>
        </div>

        <div class="card" style="padding: 1.25rem; border-left: 4px solid var(--accent-amber);">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--accent-amber); text-transform: uppercase; margin-bottom: 0.5rem;">
                Menunggu Kurasi
            </div>
            <div style="font-size: 2rem; font-weight: 800; color: var(--accent-amber);">
                {{ $metrics['pending_curation'] }}
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Submisi karya baru</div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">
                Karya Tayang
            </div>
            <div style="font-size: 2rem; font-weight: 800; color: var(--accent-emerald);">
                {{ $metrics['approved_showcases'] }}
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Di etalase publik</div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">
                Materi Belajar
            </div>
            <div style="font-size: 2rem; font-weight: 800; color: var(--text-main);">
                {{ $metrics['total_materials'] }}
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Modul aktif</div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">
                Prompt Library
            </div>
            <div style="font-size: 2rem; font-weight: 800; color: var(--text-main);">
                {{ $metrics['total_prompts'] }}
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Koleksi siap pakai</div>
        </div>
    </div>

    <!-- Pending Curation Queue Section -->
    <div style="margin-bottom: 4rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
            <span class="badge badge-amber">Antrean Moderasi</span>
            <h2 style="font-size: 1.5rem; font-weight: 800;">Submisi Karya Alumni yang Menunggu Kurasi</h2>
        </div>

        @if($pendingShowcases->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                @foreach($pendingShowcases as $p)
                    <div class="card card-elevated" style="border-left: 4px solid var(--accent-amber); padding: 1.75rem;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <span class="badge badge-amber" style="margin-bottom: 0.5rem;">Menunggu Review</span>
                                <h3 style="font-size: 1.35rem; font-weight: 800; line-height: 1.3;">{{ $p->title }}</h3>
                                <div style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.25rem;">
                                    Diajukan oleh: <strong>{{ $p->user->name ?? 'Member' }}</strong> (Alumni Angkatan {{ $p->user->alumni_year ?? '-' }}) &bull; WA: {{ $p->user->whatsapp_number ?? '-' }}
                                </div>
                            </div>
                            <span class="badge badge-cyan">🛠 {{ $p->tools_used }}</span>
                        </div>

                        <div style="font-size: 0.95rem; color: var(--text-main); line-height: 1.7; margin-bottom: 1rem; background: var(--bg-surface-alt); padding: 1rem; border-radius: var(--radius-md);">
                            <strong>Deskripsi Proyek:</strong>
                            <p style="margin-top: 0.25rem; margin-bottom: 0; white-space: pre-line;">{{ $p->description }}</p>

                            @if($p->impact_story)
                                <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--border-color); color: var(--accent-emerald);">
                                    <strong>Cerita Dampak:</strong> {{ $p->impact_story }}
                                </div>
                            @endif

                            @if($p->project_url)
                                <div style="margin-top: 0.5rem;">
                                    <a href="{{ $p->project_url }}" target="_blank" rel="noopener" style="color: var(--primary); font-size: 0.85rem; font-weight: 600;">
                                        Kunjungi Link Proyek ↗
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Curation Actions -->
                        <div style="display: flex; gap: 1rem; justify-content: flex-end; align-items: center; border-top: 1px solid var(--border-color); padding-top: 1rem; flex-wrap: wrap;">
                            <!-- Reject Form -->
                            <form method="POST" action="{{ url('/admin/curation/' . $p->id . '/reject') }}" style="display: flex; gap: 0.5rem; align-items: center;">
                                @csrf
                                <input type="text" name="admin_notes" placeholder="Catatan revisi untuk member..." 
                                       style="padding: 0.4rem 0.75rem; font-size: 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); width: 250px;">
                                <button type="submit" class="btn btn-secondary btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3);">
                                    Tolak / Revisi
                                </button>
                            </form>

                            <!-- Approve Form -->
                            <form method="POST" action="{{ url('/admin/curation/' . $p->id . '/approve') }}">
                                @csrf
                                <button type="submit" class="btn btn-whatsapp btn-sm">
                                    ✓ Setujui & Publikasikan
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card" style="text-align: center; padding: 3rem 1.5rem;">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🎉</div>
                <h4 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.25rem;">Semua Bersih!</h4>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0;">Tidak ada submisi karya alumni yang menunggu kurasi saat ini.</p>
            </div>
        @endif
    </div>

    <!-- Recent Approved Showcases Section -->
    <div>
        <h2 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 1.25rem;">Riwayat Kurasi Terbaru</h2>
        @if($recentShowcases->count() > 0)
            <div class="card" style="padding: 0; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem; text-align: left;">
                    <thead>
                        <tr style="background: var(--bg-surface-alt); border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 0.875rem 1.25rem; font-weight: 700;">Judul Karya</th>
                            <th style="padding: 0.875rem 1.25rem; font-weight: 700;">Kreator</th>
                            <th style="padding: 0.875rem 1.25rem; font-weight: 700;">Status</th>
                            <th style="padding: 0.875rem 1.25rem; font-weight: 700;">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentShowcases as $rc)
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.875rem 1.25rem; font-weight: 600;">
                                    {{ $rc->title }}
                                </td>
                                <td style="padding: 0.875rem 1.25rem; color: var(--text-muted);">
                                    {{ $rc->user->name ?? '-' }} ({{ $rc->user->alumni_year ?? '-' }})
                                </td>
                                <td style="padding: 0.875rem 1.25rem;">
                                    <span class="badge badge-{{ $rc->status_color }}">
                                        {{ $rc->status_label }}
                                    </span>
                                </td>
                                <td style="padding: 0.875rem 1.25rem; color: var(--text-muted); font-size: 0.85rem;">
                                    {{ $rc->updated_at->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="color: var(--text-muted); font-size: 0.9rem;">Belum ada riwayat kurasi.</p>
        @endif
    </div>
</div>
@endsection
