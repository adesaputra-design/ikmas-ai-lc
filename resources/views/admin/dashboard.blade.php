@extends('layouts.admin')

@section('title', 'Dasbor Pengurus — IKMAS AI')
@section('page-title', 'Dasbor Utama Pengurus')

@section('content')
<!-- Welcome & Quick Actions Bar -->
<div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-xl); padding: 1.75rem 2rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
    <div>
        <div style="font-size: 0.85rem; font-weight: 700; color: var(--primary); text-transform: uppercase; margin-bottom: 0.25rem;">
            Pusat Komando Komunitas
        </div>
        <h1 style="font-size: 1.75rem; font-weight: 800; margin: 0; line-height: 1.2;">
            Selamat Datang, {{ auth()->user()->name }}! 👋
        </h1>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0.35rem 0 0 0;">
            Kelola pembelajaran, pantau aktivitas alumni, dan moderasi konten dalam satu tempat.
        </p>
    </div>

    <div>
        <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.5rem;">
            Aksi Cepat Pengurus
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            @if(auth()->user()->hasPermission('materials'))
            <a href="{{ url('/admin/materi/create') }}" class="btn btn-primary btn-sm">
                + Materi Baru
            </a>
            @endif

            @if(auth()->user()->hasPermission('prompts'))
            <a href="{{ url('/admin/prompts/create') }}" class="btn btn-secondary btn-sm">
                + Tambah Prompt
            </a>
            @endif

            @if(auth()->user()->hasPermission('events'))
            <a href="{{ url('/admin/agenda/create') }}" class="btn btn-secondary btn-sm">
                + Jadwalkan Event
            </a>
            @endif

            @if(auth()->user()->hasPermission('curation'))
            <a href="{{ url('/admin/curation') }}" class="btn btn-secondary btn-sm" style="border-color: var(--accent-amber); color: var(--accent-amber);">
                Tinjau Kurasi ({{ $metrics['pending_curation'] }})
            </a>
            @endif

            @if(auth()->user()->isAdmin())
            <a href="{{ url('/admin/team') }}" class="btn btn-secondary btn-sm" style="border-color: var(--primary); color: var(--primary);">
                👥 Kelola Tim & Staf
            </a>
            @endif
        </div>
    </div>
</div>

<!-- Metrics Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem;">
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

<!-- 2-Column Split: Top Prompts Leaderboard & Moderation Queue -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 2rem; margin-bottom: 2.5rem;">
    <!-- Top Copied Prompts Leaderboard -->
    <div class="card" style="padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <div>
                <h2 style="font-size: 1.15rem; font-weight: 800; margin: 0;">🔥 Prompt Paling Populer</h2>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0.2rem 0 0 0;">Instruksi yang paling sering disalin alumni</p>
            </div>
            <a href="{{ url('/admin/prompts') }}" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">Semua Prompt →</a>
        </div>

        @if($topPrompts->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($topPrompts as $idx => $p)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; background: var(--bg-surface-alt); border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                        <div style="display: flex; align-items: center; gap: 0.75rem; overflow: hidden;">
                            <div style="width: 1.75rem; height: 1.75rem; border-radius: 50%; background: {{ $idx === 0 ? '#f59e0b' : ($idx === 1 ? '#94a3b8' : ($idx === 2 ? '#b45309' : 'var(--primary)')) }}; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 800; flex-shrink: 0;">
                                {{ $idx + 1 }}
                            </div>
                            <div style="overflow: hidden;">
                                <div style="font-weight: 700; font-size: 0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                                    {{ $p->title }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">
                                    {{ $p->target_role }} &bull; {{ $p->target_tool }}
                                </div>
                            </div>
                        </div>
                        <span class="badge badge-primary" style="flex-shrink: 0; font-size: 0.75rem;">
                            {{ $p->copy_count }}x disalin
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: var(--text-muted); font-size: 0.85rem; text-align: center; padding: 2rem 0;">Belum ada data salin prompt.</p>
        @endif
    </div>

    <!-- Quick Curation Card -->
    <div class="card" style="padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <div>
                <h2 style="font-size: 1.15rem; font-weight: 800; margin: 0;">⏳ Antrean Kurasi Karya</h2>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0.2rem 0 0 0;">Karya alumni yang menunggu validasi</p>
            </div>
            <a href="{{ url('/admin/curation') }}" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">Lihat Antrean →</a>
        </div>

        @if($pendingShowcases->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                @foreach($pendingShowcases->take(3) as $p)
                    <div style="padding: 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface-alt);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.35rem;">
                            <h4 style="font-size: 0.95rem; font-weight: 700; margin: 0;">{{ $p->title }}</h4>
                            <span class="badge badge-amber" style="font-size: 0.7rem;">Pending</span>
                        </div>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                            Oleh: {{ $p->user->name ?? 'Member' }} (Angkatan {{ $p->user->alumni_year ?? '-' }})
                        </p>
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <form method="POST" action="{{ url('/admin/curation/' . $p->id . '/approve') }}">
                                @csrf
                                <button type="submit" class="btn btn-whatsapp btn-sm" style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                                    ✓ Setujui
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 2.5rem 1rem;">
                <div style="font-size: 2rem; margin-bottom: 0.25rem;">✨</div>
                <div style="font-weight: 700; font-size: 0.95rem;">Semua Karya Bersih</div>
                <p style="color: var(--text-muted); font-size: 0.8rem; margin: 0.25rem 0 0 0;">Tidak ada submisi karya yang tertunda.</p>
            </div>
        @endif
    </div>
</div>

<!-- Recent Curation History -->
<div>
    <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1rem;">Riwayat Kurasi Terbaru</h3>
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
        <p style="color: var(--text-muted); font-size: 0.85rem;">Belum ada riwayat kurasi.</p>
    @endif
</div>
@endsection
