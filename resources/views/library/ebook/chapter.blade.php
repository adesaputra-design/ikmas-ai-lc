@extends('layouts.app')

@section('title', $chapter->title . ' — ' . $item->title)

@include('library.ebook._styles')

@php
    $coverUrl = $isPreview ? route('admin.library.preview', $item) : route('library.show', $item->slug);
    $chapterUrl = fn ($target) => $isPreview
        ? route('admin.library.preview.chapter', [$item, $target->slug])
        : route('library.ebook.chapter', [$item->slug, $target->slug]);
@endphp

@section('content')
<div class="ebook-shell">
    @if($isPreview)
        <div class="ebook-preview-bar" role="status">
            <span><strong>Mode pratinjau.</strong> {{ $item->isApproved() && $item->is_public ? 'Ebook ini sudah terbit.' : 'Bab ini belum terlihat oleh pembaca.' }}</span>
            <a href="{{ route('admin.library.chapters.index', $item) }}">← Kelola bab</a>
        </div>
    @endif

    <article class="ebook-column">
        <a href="{{ $coverUrl }}" class="ebook-eyebrow" style="text-decoration: none;">← {{ $item->title }}</a>
        <div class="ebook-meta" style="margin-top: 1rem;">Bab {{ $chapter->position }} · <span class="ebook-chip">{{ $chapter->reading_minutes }} menit baca</span></div>
        <h1 class="ebook-title">{{ $chapter->title }}</h1>
        @if($chapter->subtitle)
            <p class="ebook-meta" style="font-size: 1.05rem;">{{ $chapter->subtitle }}</p>
        @endif

        @if(!empty($chapter->summary_points))
            <section class="ebook-box ebook-box--summary" aria-labelledby="intisari-heading">
                <h2 id="intisari-heading">Intisari 30 Detik</h2>
                <ul>
                    @foreach($chapter->summary_points as $point)
                        <li>{{ $point }}</li>
                    @endforeach
                </ul>
            </section>
        @endif

        <div class="ebook-body">
            {!! $chapter->body_html !!}
        </div>

        @if($chapter->research_html)
            <section class="ebook-box ebook-box--research" aria-labelledby="riset-heading">
                <h2 id="riset-heading">📊 Kata Riset</h2>
                {!! $chapter->research_html !!}
            </section>
        @endif

        @if($chapter->action_title || !empty($chapter->action_checklist))
            <section class="ebook-box ebook-box--action" aria-labelledby="aksi-heading">
                <h2 id="aksi-heading">✅ Amalkan Pekan Ini</h2>
                @if($chapter->action_title)
                    <p style="margin-top: 0;">{{ $chapter->action_title }}</p>
                @endif
                @foreach($chapter->action_checklist ?? [] as $i => $task)
                    <label style="display: flex; gap: 0.5rem; align-items: flex-start; margin: 0.35rem 0;">
                        <input type="checkbox" id="aksi-{{ $chapter->id }}-{{ $i }}">
                        <span>{{ $task }}</span>
                    </label>
                @endforeach
            </section>
        @endif

        <nav class="ebook-nav" aria-label="Navigasi bab">
            <span>@if($previous)<a href="{{ $chapterUrl($previous) }}">← {{ $previous->title }}</a>@endif</span>
            <span>@if($next)<a href="{{ $chapterUrl($next) }}">{{ $next->title }} →</a>@else<a href="{{ $coverUrl }}">Kembali ke daftar isi</a>@endif</span>
        </nav>
    </article>
</div>
@endsection
