@extends('layouts.admin')

@section('title', 'Edit Bab: ' . $chapter->title . ' — IKMAS AI')
@section('page-title', 'Edit Bab Ebook')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
        <a href="{{ route('admin.library.chapters.index', $item) }}" class="btn btn-secondary btn-sm">← Kembali ke daftar bab</a>
        <a href="{{ route('admin.library.preview.chapter', [$item, $chapter->slug]) }}" target="_blank" class="btn btn-primary btn-sm">Pratinjau ↗</a>
    </div>

    <div class="card" style="padding: 1.5rem;">
        <div style="font-size: 0.8rem; color: var(--text-muted);">
            {{ $item->title }} · Bab {{ $chapter->position }} · Alamat tetap: /bab/{{ $chapter->slug }}
        </div>
        <h2 style="font-size: 1.2rem; font-weight: 800; margin: 0.25rem 0 1rem;">{{ $chapter->title }}</h2>

        <form method="POST" action="{{ route('admin.library.chapters.update', [$item, $chapter]) }}" style="display: flex; flex-direction: column; gap: 1.1rem;">
            @csrf
            @method('PUT')
            @include('admin.library._chapter-fields', ['chapter' => $chapter])

            <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <span style="font-size: 0.8rem; color: var(--text-muted);">
                    Alamat bab tidak ikut berubah walau judul diganti, agar tautan yang sudah dibagikan tetap berfungsi.
                </span>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
