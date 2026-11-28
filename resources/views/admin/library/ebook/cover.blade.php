@extends('layouts.admin')

@section('title', 'Pratinjau — ' . $item->title)
@section('page-title', 'Pratinjau Draf Ebook')

@section('content')
<div style="max-width: 850px; margin: 0 auto;">
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem;">
        <a class="btn btn-secondary btn-sm" href="{{ route('admin.library.edit', $item) }}">← Edit Ebook</a>
        <a class="btn btn-secondary btn-sm" href="{{ route('admin.library.ebook.assessment.edit', $item) }}">Atur Asesmen</a>
        <a class="btn btn-primary btn-sm" href="{{ route('admin.library.ebook.chapters.create', $item) }}">Tambah Bab</a>
    </div>
    <article class="card" style="padding: 2rem;">
        <p class="badge {{ $item->is_public ? 'badge-emerald' : 'badge-amber' }}">{{ $item->is_public ? 'Terbit' : 'Draf Privat' }}</p>
        <p style="color: var(--text-muted);">{{ $item->series_name }} · Edisi #{{ $item->edition_number }}</p>
        <h1>{{ $item->title }}</h1>
        @if($item->author_name)<p>Oleh {{ $item->author_name }}</p>@endif
        <p>{{ $item->summary_preview }}</p>
        <p>Total waktu baca: {{ $chapters->sum('reading_minutes') }} menit</p>
        <h2>Daftar Isi</h2>
        @forelse($chapters as $index => $chapter)
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid var(--border-color);">
                <div>
                    <a href="{{ route('admin.library.ebook.preview.chapter', [$item, $chapter->slug]) }}">{{ $chapter->position }}. {{ $chapter->title }}</a>
                    · {{ $chapter->reading_minutes }} menit
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 0.4rem;">
                    <a class="btn btn-secondary btn-sm" href="{{ route('admin.library.ebook.chapters.edit', [$item, $chapter]) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.library.ebook.chapters.move-up', [$item, $chapter]) }}">
                        @csrf
                        <button class="btn btn-secondary btn-sm" type="submit" aria-label="Pindahkan {{ $chapter->title }} ke atas" @disabled($index === 0)>Naik</button>
                    </form>
                    <form method="POST" action="{{ route('admin.library.ebook.chapters.move-down', [$item, $chapter]) }}">
                        @csrf
                        <button class="btn btn-secondary btn-sm" type="submit" aria-label="Pindahkan {{ $chapter->title }} ke bawah" @disabled($index === $chapters->count() - 1)>Turun</button>
                    </form>
                    <form method="POST" action="{{ route('admin.library.ebook.chapters.destroy', [$item, $chapter]) }}" onsubmit="return confirm('Hapus bab ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-secondary btn-sm" type="submit">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <p>Belum ada bab. Tambahkan bab pertama untuk melihat pratinjau.</p>
        @endforelse
        @if($item->is_public)
            <p style="margin-top: 1.5rem;"><a class="btn btn-primary" href="{{ route('library.show', $item->slug) }}">Lihat Halaman Publik</a></p>
        @else
            <form method="POST" action="{{ route('admin.library.ebook.publish', $item) }}" style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                @csrf
                <label style="display: flex; gap: 0.65rem; align-items: flex-start; margin-bottom: 1rem;">
                    <input type="checkbox" name="editorial_approved" value="1" required>
                    <span>Saya memastikan naskah final dan seluruh kutipan narasumber telah disetujui untuk diterbitkan.</span>
                </label>
                @error('editorial_approved')<p role="alert" style="color: #b91c1c;">{{ $message }}</p>@enderror
                @error('chapters')<p role="alert" style="color: #b91c1c;">{{ $message }}</p>@enderror
                <button class="btn btn-primary" type="submit" @disabled($chapters->isEmpty())>Terbitkan Ebook</button>
            </form>
        @endif
        @if($item->assessment)
            <p style="margin-top: 0.75rem;"><a class="btn btn-secondary" href="{{ route('library.ebook.assessment', $item->slug) }}">Pratinjau Cek Skor</a></p>
        @endif
    </article>
</div>
@endsection
