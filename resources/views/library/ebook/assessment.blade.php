@extends('layouts.app')

@section('title', 'Cek Skor — ' . $item->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ebook.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/ebook-assessment.js') }}" defer></script>
@endpush

@section('content')
<main class="ebook-shell ebook-reader">
    <nav class="ebook-breadcrumb" aria-label="Jejak halaman">
        <a href="{{ route('library.index') }}">Pustaka AI</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('library.show', $item->slug) }}">{{ $item->title }}</a>
        <span aria-hidden="true">/</span>
        <span>Cek Skor</span>
    </nav>

    <article class="ebook-chapter">
        <header>
            <p class="ebook-kicker">Refleksi diri · {{ $item->title }}</p>
            <h1>Cek Skor JARIB-mu</h1>
            <p>Jawab semua pertanyaan. Kamu bisa melihat hasil terlebih dahulu; akun yang berhak dapat memilih untuk menyimpannya.</p>
        </header>

        @if(session('success'))
            <p role="status" style="padding: 0.75rem; background: var(--bg-surface-alt); border-radius: var(--radius-md);">{{ session('success') }}</p>
        @endif

        <form id="ebook-assessment-form" novalidate>
            @foreach($item->assessment['principles'] as $principle)
                <fieldset style="margin: 1.5rem 0; padding: 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <legend style="font-weight: 700; padding: 0 0.4rem;">{{ $principle['name'] }}</legend>
                    @foreach($principle['questions'] as $question)
                        <fieldset style="margin: 1rem 0; border: 0; padding: 0;">
                            <legend>{{ $question['text'] }}</legend>
                            <div style="display: flex; flex-wrap: wrap; gap: 0.6rem; margin-top: 0.5rem;">
                                @for($value = $item->assessment['scale']['min']; $value <= $item->assessment['scale']['max']; $value++)
                                    <label style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.45rem 0.65rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
                                        <input type="radio" name="{{ $question['id'] }}" value="{{ $value }}" required>
                                        <span>{{ $value }} · {{ $item->assessment['scale']['labels'][(string) $value] }}</span>
                                    </label>
                                @endfor
                            </div>
                        </fieldset>
                    @endforeach
                </fieldset>
            @endforeach

            <p id="ebook-assessment-error" role="alert" aria-live="polite" style="color: #b91c1c;"></p>
            <button class="btn btn-primary" type="submit">Lihat hasil</button>
        </form>

        <section id="ebook-assessment-result" aria-live="polite" hidden style="margin-top: 2rem;">
            <h2>Hasil refleksimu</h2>
            <p id="ebook-assessment-range"></p>
            <div id="ebook-assessment-principles"></div>
            <div id="ebook-assessment-recommendations"></div>
            @if($canSave)
                <form id="ebook-assessment-save-form" method="POST" action="{{ route('library.ebook.assessment.store', $item->slug) }}" hidden>
                    @csrf
                    <div id="ebook-assessment-save-answers"></div>
                    <button class="btn btn-primary" type="submit">Simpan hasil ini</button>
                </form>
            @elseif(auth()->check())
                <p>Hasil ini tidak dapat disimpan oleh akun yang belum aktif.</p>
            @else
                <p><a href="{{ route('login') }}">Masuk untuk menyimpan hasil dan progres</a></p>
            @endif
        </section>

        @if($canSave)
            <section aria-labelledby="ebook-assessment-history-title" style="margin-top: 2rem;">
                <h2 id="ebook-assessment-history-title">Riwayat percobaan</h2>
                @forelse($assessmentAttempts as $attempt)
                    <article style="padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                        <h3>{{ \Illuminate\Support\Carbon::parse($attempt['date'])->format('d-m-Y H:i') }}</h3>
                        <p>Skor total: {{ $attempt['total'] }} · {{ $attempt['range']['label'] }}</p>
                        <p>{{ $attempt['range']['description'] }}</p>
                        <ul>
                            @foreach($attempt['scores'] as $score)
                                <li>{{ $score['name'] }}: {{ $score['score'] }}</li>
                            @endforeach
                        </ul>
                        <p>Rekomendasi bab:</p>
                        <ul>
                            @foreach($attempt['range']['recommendation_chapters'] as $chapterSlug)
                                @if(isset($chaptersBySlug[$chapterSlug]))
                                    <li><a href="{{ route('library.ebook.chapter', [$item->slug, $chapterSlug]) }}">{{ $chaptersBySlug[$chapterSlug]->title }}</a></li>
                                @endif
                            @endforeach
                        </ul>
                    </article>
                @empty
                    <p>Belum ada hasil asesmen yang disimpan.</p>
                @endforelse
            </section>
        @endif
    </article>
</main>
<script type="application/json" id="ebook-assessment-definition">{!! Illuminate\Support\Js::encode($item->assessment) !!}</script>
<div id="ebook-assessment-chapter-url" data-template="{{ route('library.ebook.chapter', [$item->slug, '__chapter__']) }}" hidden></div>
@endsection
