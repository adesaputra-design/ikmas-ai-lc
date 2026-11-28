@extends('layouts.app')

@section('title', $chapter->title . ' — ' . $item->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ebook.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/ebook-reader.js') }}"></script>
@endpush

@section('content')
<main class="ebook-shell ebook-reader">
    <nav class="ebook-breadcrumb" aria-label="Jejak halaman">
        <a href="{{ route('library.index') }}">Pustaka AI</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('library.show', $item->slug) }}">{{ $item->title }}</a>
        <span aria-hidden="true">/</span>
        <span>{{ $chapter->title }}</span>
    </nav>

    <article class="ebook-chapter">
        <header>
            <p class="ebook-kicker">Bab {{ $chapter->position }} · {{ $chapter->reading_minutes }} menit baca</p>
            <h1>{{ $chapter->title }}</h1>
            @if($chapter->subtitle)<p class="ebook-byline">{{ $chapter->subtitle }}</p>@endif
        </header>

        <section class="ebook-reader-controls" aria-label="Pengaturan bacaan">
            <span class="ebook-reader-controls-label">Ukuran teks bacaan</span>
            <div class="ebook-reader-controls-actions">
                <button class="btn btn-secondary btn-sm" type="button" data-ebook-font-change="-0.1" aria-label="Perkecil ukuran teks">A−</button>
                <output id="ebook-font-scale" aria-live="polite">100%</output>
                <button class="btn btn-secondary btn-sm" type="button" data-ebook-font-change="0.1" aria-label="Perbesar ukuran teks">A+</button>
                <button class="btn btn-secondary btn-sm" type="button" data-ebook-font-reset="1">Atur ulang</button>
            </div>
        </section>

        <section class="ebook-summary" aria-labelledby="ebook-summary-title">
            <h2 id="ebook-summary-title">Intisari 30 Detik</h2>
            <ul>@foreach($chapter->summary_points ?? [] as $point)<li>{{ $point }}</li>@endforeach</ul>
        </section>

        <div class="ebook-prose" data-ebook-reader>{!! $chapter->rendered_body !!}</div>

        @if($chapter->approved_quotes)
            <section class="ebook-approved-quotes" aria-labelledby="approved-quotes-heading">
                <h2 id="approved-quotes-heading">Kutipan untuk dibagikan</h2>
                @foreach($chapter->approved_quotes as $quote)
                    @php
                        $chapterUrl = route('library.ebook.chapter', [$item->slug, $chapter->slug]);
                        $shareText = rawurlencode("“{$quote['text']}”\n— {$quote['attribution']}\n{$chapterUrl}");
                    @endphp
                    <figure class="ebook-approved-quote">
                        <blockquote>{{ $quote['text'] }}</blockquote>
                        <figcaption>{{ $quote['attribution'] }}</figcaption>
                        <a class="btn btn-secondary btn-sm ebook-quote-share" href="https://wa.me/?text={{ $shareText }}" target="_blank" rel="noopener noreferrer">Bagikan kutipan ke WhatsApp</a>
                    </figure>
                @endforeach
            </section>
        @endif

        @if($chapter->research_box)
            <aside class="ebook-note">
                <h2>Kata Riset</h2>
                <p>{{ $chapter->research_box }}</p>
            </aside>
        @endif

        @if($chapter->action_title || $chapter->action_checklist)
            <section class="ebook-note" id="amalkan-pekan-ini" aria-labelledby="ebook-action-heading">
                <h2 id="ebook-action-heading">Amalkan Pekan Ini</h2>
                @if($chapter->action_title)<p>{{ $chapter->action_title }}</p>@endif
                @if($chapter->action_checklist)
                    @if($canSaveChecklist)
                        <form method="POST" action="{{ route('library.ebook.progress', $item->slug) }}">
                            @csrf
                            <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                            <input type="hidden" name="checklist_version" value="{{ $checklistVersion }}">
                    @endif
                    <fieldset class="ebook-checklist-items">
                        <legend>Checklist</legend>
                        @foreach($chapter->action_checklist as $index => $action)
                            <label class="ebook-checklist-item">
                                <input type="checkbox" name="checked[]" value="{{ $index }}" @checked(in_array($index, $checked, true))>
                                <span>{{ $action }}</span>
                            </label>
                        @endforeach
                    </fieldset>
                    @if($canSaveChecklist)
                            @if($errors->any())<p role="alert">{{ $errors->first() }}</p>@endif
                            <button class="btn btn-secondary btn-sm ebook-checklist-save" type="submit">Simpan checklist</button>
                            @if(session('ebook_checklist_saved'))<p class="ebook-checklist-feedback" role="status">Checklist tersimpan.</p>@endif
                        </form>
                    @else
                        @guest
                            <a class="ebook-checklist-login" href="{{ route('login') }}">Masuk untuk menyimpan checklist</a>
                        @else
                            <p>Penyimpanan checklist tersedia untuk akun aktif.</p>
                        @endguest
                    @endif
                @endif
            </section>
        @endif
    </article>

    <nav class="ebook-chapter-nav" aria-label="Navigasi bab">
        @if($previous)
            <a href="{{ route('library.ebook.chapter', [$item->slug, $previous->slug]) }}"><small>← Bab sebelumnya</small><strong>{{ $previous->title }}</strong></a>
        @else
            <a href="{{ route('library.show', $item->slug) }}"><small>← Kembali</small><strong>Daftar Isi</strong></a>
        @endif
        @if($next)
            <a href="{{ route('library.ebook.chapter', [$item->slug, $next->slug]) }}"><small>Bab berikutnya →</small><strong>{{ $next->title }}</strong></a>
        @else
            <a href="{{ route('library.show', $item->slug) }}"><small>Selesai membaca</small><strong>Daftar Isi →</strong></a>
        @endif
    </nav>
</main>
@endsection
