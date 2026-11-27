@extends('layouts.admin')

@section('title', $chapter->title . ' — ' . $item->title)
@section('page-title', 'Pratinjau Bab Ebook')

@section('content')
<div style="max-width: 850px; margin: 0 auto;">
    <a class="btn btn-secondary btn-sm" href="{{ route('admin.library.ebook.preview', $item) }}">← Daftar Isi</a>
    <article class="card" style="padding: 2rem; margin-top: 1.5rem;">
        <p class="badge badge-amber">Draf Privat</p>
        <p>Bab {{ $chapter->position }} · {{ $chapter->reading_minutes }} menit baca</p>
        <h1>{{ $chapter->title }}</h1>
        @if($chapter->subtitle)<p>{{ $chapter->subtitle }}</p>@endif
        <h2>Intisari 30 Detik</h2>
        <ul>@foreach($chapter->summary_points ?? [] as $point)<li>{{ $point }}</li>@endforeach</ul>
        <div>{!! $chapter->rendered_body !!}</div>
        @if($chapter->research_box)
            <h2>Kata Riset</h2>
            <p>{{ $chapter->research_box }}</p>
        @endif
        @if($chapter->action_title)
            <h2>Amalkan Pekan Ini</h2>
            <p>{{ $chapter->action_title }}</p>
            <ul>@foreach($chapter->action_checklist ?? [] as $action)<li>{{ $action }}</li>@endforeach</ul>
        @endif
    </article>
</div>
@endsection
