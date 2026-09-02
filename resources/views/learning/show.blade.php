@extends('layouts.app')

@section('title', $material->title . ' — IKMAS AI Learning Center')

@section('content')
<div class="container" style="padding-top: 2.5rem; padding-bottom: 5rem;">
    <!-- Breadcrumbs -->
    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 2rem;">
        <a href="{{ url('/') }}">Beranda</a>
        <span>/</span>
        <a href="{{ url('/materi') }}">Materi Belajar</a>
        <span>/</span>
        <span style="color: var(--primary); font-weight: 600;">{{ Str::limit($material->title, 35) }}</span>
    </div>

    <div style="display: grid; grid-template-columns: 1fr; gap: 2.5rem;">
        <article class="card" style="padding: 2.5rem; max-width: 900px; margin: 0 auto; width: 100%;">
            <!-- Meta Header -->
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1.25rem;">
                <span class="badge badge-{{ $material->level_color }}">
                    {{ $material->level_label }}
                </span>
                <span class="badge badge-cyan">
                    {{ $material->pillar_label }}
                </span>
                @if($material->category)
                    <span class="badge badge-primary">
                        {{ $material->category->name }}
                    </span>
                @endif
                <span style="font-size: 0.85rem; color: var(--text-muted); margin-left: auto; display: flex; align-items: center; gap: 0.35rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    {{ $material->reading_minutes }} Menit Baca
                </span>
            </div>

            <!-- Title -->
            <h1 style="font-size: 2.5rem; font-weight: 800; line-height: 1.2; margin-bottom: 1.25rem; letter-spacing: -0.02em;">
                {{ $material->title }}
            </h1>

            <!-- Summary Lead -->
            @if($material->summary)
                <p style="font-size: 1.15rem; color: var(--text-muted); line-height: 1.7; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);">
                    {{ $material->summary }}
                </p>
            @endif

            <!-- Video Section if any -->
            @if($material->video_url)
                <div style="margin-bottom: 2.5rem; border-radius: var(--radius-xl); overflow: hidden; background: var(--bg-surface-alt); border: 1px solid var(--border-color); padding: 1rem; text-align: center;">
                    <span class="badge badge-primary" style="margin-bottom: 0.5rem;">Rekaman Sesi</span>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.75rem;">Tonton penjelasan lengkap dari sesi Study Group:</p>
                    <a href="{{ $material->video_url }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm">
                        Buka Rekaman Video ↗
                    </a>
                </div>
            @endif

            <!-- Article Content -->
            <div class="article-content" style="line-height: 1.8; font-size: 1.05rem; color: var(--text-main);">
                {!! $material->content !!}
            </div>

            <!-- Slide Resources if any -->
            @if($material->slide_url)
                <div style="margin-top: 2.5rem; padding: 1.25rem; background: var(--bg-surface-alt); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <strong style="display: block; font-size: 0.95rem;">Berkas Presentasi / Panduan Slide</strong>
                        <span style="font-size: 0.85rem; color: var(--text-muted);">Unduh atau buka slide rangkuman materi ini.</span>
                    </div>
                    <a href="{{ $material->slide_url }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm">
                        Unduh Slide ↗
                    </a>
                </div>
            @endif

            <!-- Garuda Community Box -->
            <div style="margin-top: 3.5rem; padding-top: 2rem; border-top: 1px solid var(--border-color); background: linear-gradient(135deg, rgba(37,99,235,0.04) 0%, rgba(14,165,233,0.04) 100%); border-radius: var(--radius-xl); padding: 1.75rem;">
                <div style="display: flex; gap: 1rem; align-items: flex-start;">
                    <div style="font-size: 2rem;">🦅</div>
                    <div>
                        <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem;">Mau Tanya Jawab atau Diskusi Materi Ini?</h4>
                        <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1rem;">
                            Jika ada bagian yang kurang jelas atau kamu ingin mencoba prompt praktiknya, tanyakan langsung di grup WhatsApp IKMAS AI!
                        </p>
                        <a href="https://chat.whatsapp.com/sample-ikmas-ai" target="_blank" rel="noopener" class="btn btn-whatsapp btn-sm">
                            Tanyakan di WhatsApp Community
                        </a>
                    </div>
                </div>
            </div>
        </article>

        <!-- Related Materials -->
        @if($relatedMaterials->count() > 0)
            <div style="max-width: 900px; margin: 3rem auto 0 auto; width: 100%;">
                <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem;">Materi Terkait Lainnya</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                    @foreach($relatedMaterials as $rel)
                        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <span class="badge badge-{{ $rel->level_color }}" style="margin-bottom: 0.75rem;">
                                    {{ $rel->level_label }}
                                </span>
                                <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem;">
                                    <a href="{{ url('/materi/' . $rel->slug) }}">{{ $rel->title }}</a>
                                </h4>
                                <p style="font-size: 0.85rem; color: var(--text-muted);">
                                    {{ Str::limit($rel->summary, 90) }}
                                </p>
                            </div>
                            <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid var(--border-color);">
                                <a href="{{ url('/materi/' . $rel->slug) }}" class="btn btn-secondary btn-sm" style="width: 100%;">
                                    Baca Materi →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
