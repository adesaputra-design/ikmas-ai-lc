@extends('layouts.app')

@section('title', $item->title . ' — Pustaka AI IKMAS')

@include('library.ebook._styles')

@php
    $chapterUrl = fn ($chapter) => $isPreview
        ? route('admin.library.preview.chapter', [$item, $chapter->slug])
        : url("/library/{$item->slug}/bab/{$chapter->slug}");
@endphp

@section('content')
<div class="ebook-shell">
    @if($isPreview)
        <div class="ebook-preview-bar" role="status">
            <span><strong>Mode pratinjau.</strong> Status: {{ $item->status }} · {{ $item->is_public ? 'akses publik' : 'akses terbatas' }}. Halaman ini tidak terlihat oleh tamu.</span>
            <a href="{{ route('admin.library.chapters.index', $item) }}">← Kelola bab</a>
        </div>
    @endif

    <div class="ebook-column">
        <div class="ebook-eyebrow">
            {{ $item->series_name ?? 'Ebook Pustaka AI' }}@if($item->edition_number) · Edisi #{{ $item->edition_number }}@endif
        </div>
        <h1 class="ebook-title">{{ $item->title }}</h1>
        <p class="ebook-meta">
            @if($item->author_name)Bersama <strong>{{ $item->author_name }}</strong> · @endif
            {{ $chapters->count() }} bab · <span class="ebook-chip">{{ $item->reading_time ?? '0 menit' }} baca</span>
        </p>

        @if($item->cover_image)
            <img src="{{ asset('storage/' . $item->cover_image) }}" alt="Sampul {{ $item->title }}" style="width: 100%; max-height: 360px; object-fit: cover; border-radius: var(--radius-md); margin: 1rem 0;">
        @endif

        <p style="font-size: 1.05rem; line-height: 1.7;">{{ $item->summary_preview }}</p>

        <h2 style="font-size: 1.1rem; margin-top: 2rem;">Daftar Isi</h2>
        @if($chapters->isEmpty())
            <p class="ebook-meta">Belum ada bab.</p>
        @else
            <ol class="ebook-toc">
                @foreach($chapters as $chapter)
                    <li>
                        <a href="{{ $chapterUrl($chapter) }}">
                            <span><strong>{{ $chapter->position }}.</strong> {{ $chapter->title }}</span>
                            <span class="ebook-meta" style="white-space: nowrap;">{{ $chapter->reading_minutes }} mnt</span>
                        </a>
                    </li>
                @endforeach
            </ol>
            <a href="{{ $chapterUrl($chapters->first()) }}" class="btn btn-primary">Mulai Membaca →</a>
        @endif
    </div>
</div>
@endsection
