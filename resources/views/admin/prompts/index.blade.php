@extends('layouts.admin')

@section('title', 'Kelola Prompt Library — IKMAS AI')
@section('page-title', 'Prompt Library')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 800; margin: 0;">Katalog Prompt Siap Pakai</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0.25rem 0 0 0;">
            Kelola template prompt AI yang dapat disalin oleh seluruh member alumni.
        </p>
    </div>
    <a href="{{ route('admin.prompts.create') }}" class="btn btn-primary">
        + Tambah Prompt Baru
    </a>
</div>

<!-- Filters Bar -->
<div class="card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
    <form method="GET" action="{{ route('admin.prompts.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul atau isi prompt..."
               style="flex: 1; min-width: 200px; padding: 0.5rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">

        <select name="target_role" style="padding: 0.5rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
            <option value="">Semua Peran</option>
            <option value="Umum & Produktivitas" {{ request('target_role') === 'Umum & Produktivitas' ? 'selected' : '' }}>Umum & Produktivitas</option>
            <option value="Pebisnis & Marketer" {{ request('target_role') === 'Pebisnis & Marketer' ? 'selected' : '' }}>Pebisnis & Marketer</option>
            <option value="Penulis & Content Creator" {{ request('target_role') === 'Penulis & Content Creator' ? 'selected' : '' }}>Penulis & Content Creator</option>
            <option value="Pendidik & Guru" {{ request('target_role') === 'Pendidik & Guru' ? 'selected' : '' }}>Pendidik & Guru</option>
            <option value="Developer & IT" {{ request('target_role') === 'Developer & IT' ? 'selected' : '' }}>Developer & IT</option>
        </select>

        <select name="target_tool" style="padding: 0.5rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
            <option value="">Semua Tools</option>
            <option value="ChatGPT" {{ request('target_tool') === 'ChatGPT' ? 'selected' : '' }}>ChatGPT</option>
            <option value="Claude" {{ request('target_tool') === 'Claude' ? 'selected' : '' }}>Claude</option>
            <option value="Gemini" {{ request('target_tool') === 'Gemini' ? 'selected' : '' }}>Gemini</option>
            <option value="Canva / Midjourney" {{ request('target_tool') === 'Canva / Midjourney' ? 'selected' : '' }}>Canva / Midjourney</option>
            <option value="Cursor / v0" {{ request('target_tool') === 'Cursor / v0' ? 'selected' : '' }}>Cursor / v0</option>
        </select>

        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        @if(request()->anyFilled(['q', 'target_role', 'target_tool']))
            <a href="{{ route('admin.prompts.index') }}" class="btn btn-secondary btn-sm">Reset</a>
        @endif
    </form>
</div>

<!-- Table Card -->
<div class="card" style="padding: 0; overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
        <thead>
            <tr style="background: var(--bg-surface-alt); border-bottom: 1px solid var(--border-color);">
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Judul Prompt</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Target Peran & Tool</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Popularitas</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Featured</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($prompts as $item)
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 1rem 1.25rem;">
                        <div style="font-weight: 700; color: var(--text-main); margin-bottom: 0.2rem;">
                            {{ $item->title }}
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); font-family: monospace;">
                            {{ Str::limit($item->prompt_text, 80) }}
                        </div>
                    </td>
                    <td style="padding: 1rem 1.25rem;">
                        <span class="badge badge-primary" style="font-size: 0.7rem; margin-bottom: 0.25rem; display: inline-block;">
                            {{ $item->target_role }}
                        </span>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">
                            🛠 {{ $item->target_tool }}
                        </div>
                    </td>
                    <td style="padding: 1rem 1.25rem;">
                        <span class="badge badge-cyan" style="font-size: 0.75rem;">
                            {{ $item->copy_count }}x disalin
                        </span>
                    </td>
                    <td style="padding: 1rem 1.25rem;">
                        @if($item->is_featured)
                            <span class="badge badge-amber" style="font-size: 0.75rem;">⭐ Featured</span>
                        @else
                            <span style="color: var(--text-muted); font-size: 0.85rem;">-</span>
                        @endif
                    </td>
                    <td style="padding: 1rem 1.25rem; text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                            <a href="{{ route('admin.prompts.edit', $item->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.65rem; font-size: 0.75rem;">
                                Edit
                            </a>
                            <form action="{{ route('admin.prompts.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus prompt ini?')">
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
                    <td colspan="5" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                        Belum ada prompt yang sesuai dengan kriteria pencarian.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 1.5rem; display: flex; justify-content: center;">
    {{ $prompts->links() }}
</div>
@endsection
