@extends('layouts.admin')

@section('title', 'Bab Ebook: ' . $item->title . ' — IKMAS AI')
@section('page-title', 'Kelola Bab Ebook')

@php
    $isPublished = $item->isApproved() && $item->is_public;
@endphp

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
        <a href="{{ route('admin.library.index', ['tab' => 'ebook']) }}" class="btn btn-secondary btn-sm">← Kembali ke Pustaka</a>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.library.edit', $item) }}" class="btn btn-secondary btn-sm">Edit Info Ebook</a>
            <a href="{{ route('admin.library.preview', $item) }}" target="_blank" class="btn btn-primary btn-sm">Pratinjau ↗</a>
        </div>
    </div>

    <div class="card" style="padding: 1.5rem; margin-bottom: 1.5rem;">
        <div style="font-size: 0.8rem; color: var(--text-muted);">
            {{ $item->series_name ?? 'Ebook' }}@if($item->edition_number) · Edisi #{{ $item->edition_number }}@endif
            · Status: <strong>{{ $isPublished ? 'Terbit untuk publik' : 'Draf privat' }}</strong>
        </div>
        <h2 style="font-size: 1.3rem; font-weight: 800; margin: 0.25rem 0 1rem;">{{ $item->title }}</h2>

        @if($chapters->isEmpty())
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Belum ada bab. Tambahkan bab pertama melalui form di bawah.</p>
        @else
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                        <th style="padding: 0.5rem;">#</th>
                        <th style="padding: 0.5rem;">Judul</th>
                        <th style="padding: 0.5rem;">Waktu baca</th>
                        <th style="padding: 0.5rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chapters as $chapter)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.5rem;">{{ $chapter->position }}</td>
                            <td style="padding: 0.5rem;">
                                <strong>{{ $chapter->title }}</strong>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">/bab/{{ $chapter->slug }}</div>
                            </td>
                            <td style="padding: 0.5rem;">{{ $chapter->reading_minutes }} mnt</td>
                            <td style="padding: 0.5rem;">
                                <div style="display: flex; gap: 0.35rem; justify-content: flex-end; flex-wrap: wrap;">
                                    <form method="POST" action="{{ route('admin.library.chapters.move', [$item, $chapter]) }}">
                                        @csrf
                                        <input type="hidden" name="direction" value="up">
                                        <button type="submit" class="btn btn-secondary btn-sm" aria-label="Naikkan bab {{ $chapter->title }}" @disabled($loop->first)>↑</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.library.chapters.move', [$item, $chapter]) }}">
                                        @csrf
                                        <input type="hidden" name="direction" value="down">
                                        <button type="submit" class="btn btn-secondary btn-sm" aria-label="Turunkan bab {{ $chapter->title }}" @disabled($loop->last)>↓</button>
                                    </form>
                                    <a href="{{ route('admin.library.chapters.edit', [$item, $chapter]) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    <a href="{{ route('admin.library.preview.chapter', [$item, $chapter->slug]) }}" target="_blank" class="btn btn-secondary btn-sm">Pratinjau ↗</a>
                                    <form method="POST" action="{{ route('admin.library.chapters.destroy', [$item, $chapter]) }}"
                                          onsubmit="return confirm(@js('Hapus bab "'.$chapter->title.'"? Tindakan ini tidak bisa dibatalkan.'))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3);">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0.75rem 0 0;">Total waktu baca: {{ $item->reading_time }}</p>
        @endif
    </div>

    @if($isPublished)
        <div class="card" style="padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; border-left: 4px solid var(--accent-emerald);">
            <strong>✓ Ebook sudah terbit.</strong>
            <span style="color: var(--text-muted); font-size: 0.9rem;">Bab yang ditambahkan sekarang langsung terlihat oleh pembaca.</span>
            <a href="{{ route('library.show', $item->slug) }}" target="_blank" style="margin-left: 0.5rem;">Lihat halaman publik ↗</a>
        </div>
    @else
        <div class="card" style="padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; border-left: 4px solid var(--accent-amber);">
            <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0 0 0.5rem;">Terbitkan Ebook</h3>
            @error('chapters')
                <div style="color: #ef4444; font-size: 0.85rem; margin-bottom: 0.5rem;">{{ $message }}</div>
            @enderror
            @error('editorial_approved')
                <div style="color: #ef4444; font-size: 0.85rem; margin-bottom: 0.5rem;">{{ $message }}</div>
            @enderror
            <form method="POST" action="{{ route('admin.library.publish', $item) }}" style="display: flex; flex-direction: column; gap: 0.75rem;">
                @csrf
                <label style="display: flex; gap: 0.5rem; align-items: flex-start; font-size: 0.875rem; cursor: pointer;">
                    <input type="checkbox" name="editorial_approved" value="1" style="margin-top: 0.2rem;">
                    <span>Naskah sudah final dan setiap kutipan narasumber sudah disetujui atau berasal dari rekaman.</span>
                </label>
                <div>
                    <button type="submit" class="btn btn-primary btn-sm" @disabled($chapters->isEmpty())>Terbitkan untuk Publik</button>
                    @if($chapters->isEmpty())
                        <span style="font-size: 0.8rem; color: var(--text-muted); margin-left: 0.5rem;">Tambahkan minimal satu bab dulu.</span>
                    @endif
                </div>
            </form>
        </div>
    @endif

    <div class="card" style="padding: 1.5rem;">
        <h3 style="font-size: 1.1rem; font-weight: 800; margin: 0 0 1rem;">Tambah Bab</h3>

        <form method="POST" action="{{ route('admin.library.chapters.store', $item) }}" style="display: flex; flex-direction: column; gap: 1.1rem;">
            @csrf
            @include('admin.library._chapter-fields', ['chapter' => new \App\Models\LibraryChapter])

            <div style="display: flex; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <button type="submit" class="btn btn-primary">Simpan Bab</button>
            </div>
        </form>
    </div>
</div>
@endsection
