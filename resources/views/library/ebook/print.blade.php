<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Versi cetak ebook {{ $item->title }}">
    <title>{{ $item->title }} — Versi Cetak</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ebook.css') }}">
</head>
<body class="ebook-print-page">
    <nav class="ebook-print-toolbar" aria-label="Kontrol dokumen">
        <a class="btn btn-secondary" href="{{ route('library.show', $item->slug) }}">Kembali ke ebook</a>
        <button class="btn btn-primary" type="button" onclick="window.print()">Cetak / Simpan PDF</button>
    </nav>

    <main class="ebook-print-document">
        <header class="ebook-print-cover">
            @if($item->cover_image)
                <img class="ebook-print-cover-image" src="{{ asset('storage/' . $item->cover_image) }}" alt="Sampul {{ $item->title }}">
            @endif
            <p class="ebook-kicker">{{ $item->series_name }} · Edisi #{{ $item->edition_number }}</p>
            <h1>{{ $item->title }}</h1>
            @if($item->author_name)<p class="ebook-byline">Oleh {{ $item->author_name }}</p>@endif
            <p class="ebook-intro">{{ $item->summary_preview }}</p>
            <p class="ebook-reading-time">{{ $chapters->count() }} bab · Total waktu baca {{ $chapters->sum('reading_minutes') }} menit</p>
        </header>

        @foreach($chapters as $chapter)
            <article class="ebook-print-chapter">
                <header>
                    <p class="ebook-kicker">Bab {{ $chapter->position }} · {{ $chapter->reading_minutes }} menit baca</p>
                    <h2>{{ $chapter->title }}</h2>
                    @if($chapter->subtitle)<p class="ebook-byline">{{ $chapter->subtitle }}</p>@endif
                </header>

                @if($chapter->summary_points)
                    <section class="ebook-print-summary">
                        <h3>Intisari 30 Detik</h3>
                        <ul>@foreach($chapter->summary_points as $point)<li>{{ $point }}</li>@endforeach</ul>
                    </section>
                @endif

                <div class="ebook-prose">{!! $chapter->rendered_body !!}</div>

                @if($chapter->approved_quotes)
                    <section class="ebook-print-quotes">
                        @foreach($chapter->approved_quotes as $quote)
                            <blockquote>
                                <p>“{{ $quote['text'] }}”</p>
                                <footer>— {{ $quote['attribution'] }}</footer>
                            </blockquote>
                        @endforeach
                    </section>
                @endif

                @if($chapter->research_box)
                    <aside class="ebook-print-note">
                        <h3>Kata Riset</h3>
                        <p>{{ $chapter->research_box }}</p>
                    </aside>
                @endif

                @if($chapter->action_title || $chapter->action_checklist)
                    <section class="ebook-print-note">
                        <h3>Amalkan Pekan Ini</h3>
                        @if($chapter->action_title)<p>{{ $chapter->action_title }}</p>@endif
                        @if($chapter->action_checklist)
                            <ul>@foreach($chapter->action_checklist as $action)<li>{{ $action }}</li>@endforeach</ul>
                        @endif
                    </section>
                @endif
            </article>
        @endforeach
    </main>
</body>
</html>
