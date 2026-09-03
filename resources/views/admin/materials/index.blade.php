@extends('layouts.admin')

@section('title', 'Kelola Materi Belajar — IKMAS AI')
@section('page-title', 'Kelola Materi Belajar')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 800; margin: 0;">Daftar Modul Materi</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0.25rem 0 0 0;">
            Kelola modul materi pembelajaran Study Group AI untuk alumni.
        </p>
    </div>
    <a href="{{ route('admin.materi.create') }}" class="btn btn-primary">
        + Tambah Materi Baru
    </a>
</div>

<!-- Filters Bar -->
<div class="card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
    <form method="GET" action="{{ route('admin.materi.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul materi..."
               style="flex: 1; min-width: 200px; padding: 0.5rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">

        <select name="pillar" style="padding: 0.5rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
            <option value="">Semua Pilar</option>
            <option value="basics" {{ request('pillar') === 'basics' ? 'selected' : '' }}>AI Basics</option>
            <option value="tools" {{ request('pillar') === 'tools' ? 'selected' : '' }}>AI Tools</option>
            <option value="productivity" {{ request('pillar') === 'productivity' ? 'selected' : '' }}>AI Productivity</option>
            <option value="workflow" {{ request('pillar') === 'workflow' ? 'selected' : '' }}>AI Workflow</option>
            <option value="opportunity" {{ request('pillar') === 'opportunity' ? 'selected' : '' }}>AI for Opportunity</option>
        </select>

        <select name="level" style="padding: 0.5rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
            <option value="">Semua Level</option>
            <option value="beginner" {{ request('level') === 'beginner' ? 'selected' : '' }}>Beginner</option>
            <option value="explorer" {{ request('level') === 'explorer' ? 'selected' : '' }}>Explorer</option>
            <option value="practitioner" {{ request('level') === 'practitioner' ? 'selected' : '' }}>Practitioner</option>
        </select>

        <select name="status" style="padding: 0.5rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
            <option value="">Semua Status</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Tayang (Published)</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draf (Draft)</option>
        </select>

        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        @if(request()->anyFilled(['q', 'pillar', 'level', 'status']))
            <a href="{{ route('admin.materi.index') }}" class="btn btn-secondary btn-sm">Reset</a>
        @endif
    </form>
</div>

<!-- Table Card -->
<div class="card" style="padding: 0; overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
        <thead>
            <tr style="background: var(--bg-surface-alt); border-bottom: 1px solid var(--border-color);">
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Judul Materi</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Pilar & Level</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Durasi</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Media</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Status</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($materials as $item)
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 1rem 1.25rem;">
                        <div style="font-weight: 700; color: var(--text-main); margin-bottom: 0.2rem;">
                            {{ $item->title }}
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;">
                            /materi/{{ $item->slug }}
                        </div>
                    </td>
                    <td style="padding: 1rem 1.25rem;">
                        <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                            <span class="badge badge-primary" style="font-size: 0.7rem;">{{ $item->pillar_label }}</span>
                            <span class="badge badge-{{ $item->level_color }}" style="font-size: 0.7rem;">{{ $item->level_label }}</span>
                        </div>
                    </td>
                    <td style="padding: 1rem 1.25rem; color: var(--text-muted); font-size: 0.85rem;">
                        ⏱ {{ $item->reading_minutes }} mnt
                    </td>
                    <td style="padding: 1rem 1.25rem; font-size: 0.85rem;">
                        @if($item->video_url)
                            <span title="Ada Video Rekaman" style="cursor: pointer;">🎥</span>
                        @endif
                        @if($item->slide_url)
                            <span title="Ada Slide Presentasi" style="cursor: pointer;">📑</span>
                        @endif
                        @if(!$item->video_url && !$item->slide_url)
                            <span style="color: var(--text-muted);">-</span>
                        @endif
                    </td>
                    <td style="padding: 1rem 1.25rem;">
                        @if($item->is_published)
                            <span class="badge badge-emerald" style="font-size: 0.75rem;">Tayang</span>
                        @else
                            <span class="badge" style="background: rgba(148,163,184,0.2); color: var(--text-muted); font-size: 0.75rem;">Draf</span>
                        @endif
                    </td>
                    <td style="padding: 1rem 1.25rem; text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                            @if($item->is_published)
                                <a href="{{ url('/materi/' . $item->slug) }}" target="_blank" class="btn btn-secondary btn-sm" title="Lihat Halaman Publik" style="padding: 0.35rem 0.6rem; font-size: 0.75rem;">
                                    ↗
                                </a>
                            @endif
                            <a href="{{ route('admin.materi.edit', $item->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.65rem; font-size: 0.75rem;">
                                Edit
                            </a>
                            <form action="{{ route('admin.materi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="padding: 0.35rem 0.65rem; font-size: 0.75rem; color: #ef4444; border: 1px solid rgba(239,68,68,0.2); background: transparent;">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                        Belum ada materi yang sesuai dengan pencarian.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 1.5rem; display: flex; justify-content: center;">
    {{ $materials->links() }}
</div>
@endsection
