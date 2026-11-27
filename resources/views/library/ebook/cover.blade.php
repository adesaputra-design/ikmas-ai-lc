@extends('layouts.app')

@section('title', $item->title . ' — Pustaka AI IKMAS')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ebook.css') }}">
@endpush

@section('content')
<main class="ebook-shell">
    <nav class="ebook-breadcrumb" aria-label="Jejak halaman">
        <a href="{{ route('library.index') }}">Pustaka AI</a>
        <span aria-hidden="true">/</span>
        <span>{{ $item->title }}</span>
    </nav>

    <article class="ebook-cover">
        <p class="ebook-kicker">{{ $item->series_name }} · Edisi #{{ $item->edition_number }}</p>
        <h1>{{ $item->title }}</h1>
        @if($item->author_name)<p class="ebook-byline">Oleh {{ $item->author_name }}</p>@endif
        <p class="ebook-intro">{{ $item->summary_preview }}</p>
        <p class="ebook-reading-time">{{ $chapters->count() }} bab · Total waktu baca: {{ $chapters->sum('reading_minutes') }} menit</p>

        <div class="ebook-cover-actions">
            @if($chapters->isNotEmpty())
                <a class="btn btn-primary" href="{{ route('library.ebook.chapter', [$item->slug, $chapters->first()->slug]) }}">Mulai Membaca</a>
            @endif
            @auth
                <form method="POST" action="{{ route('member.bookmarks.toggle') }}">
                    @csrf
                    <input type="hidden" name="type" value="library">
                    <input type="hidden" name="id" value="{{ $item->id }}">
                    <button class="btn btn-secondary" type="submit">{{ $isBookmarked ? '★ Hapus dari Bookmark' : '☆ Simpan ke Bookmark' }}</button>
                </form>
            @else
                <a class="btn btn-secondary" href="{{ route('login') }}">Masuk untuk menyimpan</a>
            @endauth
        </div>
    </article>

    <section class="ebook-toc" aria-labelledby="ebook-toc-title">
        <h2 id="ebook-toc-title">Daftar Isi</h2>
        <ol>
            @forelse($chapters as $chapter)
                <li>
                    <a href="{{ route('library.ebook.chapter', [$item->slug, $chapter->slug]) }}">
                        <span><small>Bab {{ $chapter->position }}</small><strong>{{ $chapter->title }}</strong></span>
                        <span class="ebook-toc-time">{{ $chapter->reading_minutes }} menit <span aria-hidden="true">→</span></span>
                    </a>
                </li>
            @empty
                <li>Bab belum tersedia.</li>
            @endforelse
        </ol>
    </section>
</main>
@endsection
