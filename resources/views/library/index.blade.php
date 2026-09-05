@extends('layouts.app')

@section('title', 'Pustaka AI — Rangkuman Buku, Podcast & Karya Ilmiah Alumni IKMAS')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    <!-- Page Header -->
    <div style="text-align: center; max-width: 800px; margin: 0 auto 2.5rem auto;">
        <span class="badge badge-primary" style="margin-bottom: 0.75rem;">📖 Pustaka Pengetahuan & Riset</span>
        <h1 style="font-size: 2.75rem; font-weight: 800; margin-bottom: 1rem; letter-spacing: -0.02em;">
            Pustaka <span class="text-gradient">AI</span> IKMAS
        </h1>
        <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.6;">
            Koleksi kurasi intisari buku-buku AI & teknologi terbaik, resume podcast masa depan, serta arsip karya ilmiah (jurnal, tesis, disertasi) karya alumni Assalaam.
        </p>
    </div>

    <!-- Quick Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2.5rem;">
        <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 2.75rem; height: 2.75rem; border-radius: var(--radius-md); background: rgba(37,99,235,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                📚
            </div>
            <div>
                <div style="font-size: 1.4rem; font-weight: 800; color: var(--text-main);">{{ $stats['books'] }}</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Rangkuman Buku</div>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 2.75rem; height: 2.75rem; border-radius: var(--radius-md); background: rgba(6,182,212,0.1); color: #06b6d4; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                🎙️
            </div>
            <div>
                <div style="font-size: 1.4rem; font-weight: 800; color: var(--text-main);">{{ $stats['podcasts'] }}</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Resume Podcast</div>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 2.75rem; height: 2.75rem; border-radius: var(--radius-md); background: rgba(245,158,11,0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                🎓
            </div>
            <div>
                <div style="font-size: 1.4rem; font-weight: 800; color: var(--text-main);">{{ $stats['academics'] }}</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Karya Ilmiah Alumni</div>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 2.75rem; height: 2.75rem; border-radius: var(--radius-md); background: rgba(16,185,129,0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                ✨
            </div>
            <div>
                <div style="font-size: 1.4rem; font-weight: 800; color: var(--text-main);">{{ $stats['total'] }}</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Total Koleksi</div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card" style="margin-bottom: 2.5rem; padding: 1.5rem;">
        <form method="GET" action="{{ route('library.index') }}" style="display: flex; flex-direction: column; gap: 1.25rem;">
            <!-- Type Filter Tabs & Search -->
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <a href="{{ route('library.index', array_merge(request()->except('type', 'page'))) }}" 
                       class="btn btn-sm {{ !request('type') ? 'btn-primary' : 'btn-secondary' }}">
                        Semua Media
                    </a>
                    <a href="{{ route('library.index', array_merge(request()->except('page'), ['type' => 'book'])) }}" 
                       class="btn btn-sm {{ request('type') === 'book' ? 'btn-primary' : 'btn-secondary' }}">
                        📚 Buku
                    </a>
                    <a href="{{ route('library.index', array_merge(request()->except('page'), ['type' => 'podcast'])) }}" 
                       class="btn btn-sm {{ request('type') === 'podcast' ? 'btn-primary' : 'btn-secondary' }}">
                        🎙️ Podcast
                    </a>
                    <a href="{{ route('library.index', array_merge(request()->except('page'), ['type' => 'academic'])) }}" 
                       class="btn btn-sm {{ request('type') === 'academic' ? 'btn-primary' : 'btn-secondary' }}">
                        🎓 Karya Ilmiah
                    </a>
                </div>

                <!-- Search Input -->
                <div style="display: flex; gap: 0.5rem; width: 100%; max-width: 320px;">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul, penulis, topik..." 
                           style="flex: 1; padding: 0.5rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
                    @if(request('type'))
                        <input type="hidden" name="type" value="{{ request('type') }}">
                    @endif
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                    @if(request()->anyFilled(['q', 'type', 'category']))
                        <a href="{{ route('library.index') }}" class="btn btn-secondary btn-sm" title="Reset">✕</a>
                    @endif
                </div>
            </div>

            <!-- Categories Chips -->
            @if($categories->count() > 0)
            <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-right: 0.25rem;">
                    Topik:
                </span>
                <a href="{{ route('library.index', array_merge(request()->except('category', 'page'))) }}" 
                   style="font-size: 0.775rem; padding: 0.25rem 0.65rem; border-radius: 999px; border: 1px solid var(--border-color); text-decoration: none; {{ !request('category') ? 'background: var(--primary); color: white; border-color: var(--primary);' : 'background: var(--bg-surface); color: var(--text-muted);' }}">
                    Semua
                </a>
                @foreach($categories as $cat)
                <a href="{{ route('library.index', array_merge(request()->except('page'), ['category' => $cat])) }}" 
                   style="font-size: 0.775rem; padding: 0.25rem 0.65rem; border-radius: 999px; border: 1px solid var(--border-color); text-decoration: none; {{ request('category') === $cat ? 'background: var(--primary); color: white; border-color: var(--primary);' : 'background: var(--bg-surface); color: var(--text-muted);' }}">
                    {{ $cat }}
                </a>
                @endforeach
            </div>
            @endif
        </form>
    </div>

    <!-- Items Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
        @forelse($items as $item)
        <article class="card" style="display: flex; flex-direction: column; justify-content: space-between; overflow: hidden; padding: 0; transition: transform 0.2s, box-shadow 0.2s;">
            <!-- Card Thumbnail / Header Cover -->
            @if($item->cover_image)
                <div style="height: 180px; width: 100%; overflow: hidden; background: var(--bg-surface-alt); position: relative;">
                    <img src="{{ asset($item->cover_image) }}" alt="{{ $item->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                    <span class="badge {{ $item->type_badge['class'] }}" style="position: absolute; top: 0.75rem; left: 0.75rem; font-size: 0.75rem; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                        {{ $item->type_badge['icon'] }} {{ $item->type_badge['label'] }}
                    </span>
                </div>
            @else
                <div style="height: 130px; width: 100%; background: linear-gradient(135deg, rgba(37,99,235,0.1), rgba(6,182,212,0.1)); position: relative; display: flex; align-items: center; justify-content: center;">
                    <div style="font-size: 3rem; opacity: 0.3;">
                        {{ $item->type_badge['icon'] }}
                    </div>
                    <span class="badge {{ $item->type_badge['class'] }}" style="position: absolute; top: 0.75rem; left: 0.75rem; font-size: 0.75rem;">
                        {{ $item->type_badge['icon'] }} {{ $item->type_badge['label'] }}
                    </span>
                    @if($item->isAcademic())
                        <span class="badge badge-secondary" style="position: absolute; top: 0.75rem; right: 0.75rem; font-size: 0.7rem;">
                            {{ $item->degree_label }}
                        </span>
                    @endif
                </div>
            @endif

            <!-- Content Body -->
            <div style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--primary); text-transform: uppercase;">
                        {{ $item->category }}
                    </span>
                    @if($item->reading_time)
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.25rem;">
                            ⏱️ {{ $item->reading_time }}
                        </span>
                    @elseif($item->duration)
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.25rem;">
                            ⏳ {{ $item->duration }}
                        </span>
                    @elseif($item->publication_year)
                        <span style="font-size: 0.75rem; color: var(--text-muted);">
                            🗓️ {{ $item->publication_year }}
                        </span>
                    @endif
                </div>

                <h3 style="font-size: 1.2rem; font-weight: 800; line-height: 1.4; margin-bottom: 0.75rem; color: var(--text-main);">
                    <a href="{{ route('library.show', $item->slug) }}" style="color: inherit; text-decoration: none;">
                        {{ $item->title }}
                    </a>
                </h3>

                <!-- Sub-info: Author / Host / University -->
                @if($item->isBook() && $item->author_name)
                    <div style="font-size: 0.825rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                        ✍️ Penulis: <strong style="color: var(--text-main);">{{ $item->author_name }}</strong>
                    </div>
                @elseif($item->isPodcast() && $item->podcast_source)
                    <div style="font-size: 0.825rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                        🎙️ Sumber: <strong style="color: var(--text-main);">{{ $item->podcast_source }}</strong>
                    </div>
                @elseif($item->isAcademic())
                    <div style="font-size: 0.825rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                        🏛️ {{ $item->institution ?? 'Alumni Assalaam' }}
                        @if($item->user)
                            • Oleh <strong style="color: var(--text-main);">{{ $item->user->name }}</strong>
                        @endif
                    </div>
                @endif

                <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex: 1;">
                    {{ \Illuminate\Support\Str::limit($item->summary_preview, 130) }}
                </p>

                <!-- Footer Card Link -->
                <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.775rem; color: var(--text-muted);">
                        👁️ {{ $item->views_count }} tayangan
                    </span>
                    <a href="{{ route('library.show', $item->slug) }}" class="btn btn-sm btn-primary" style="padding: 0.35rem 0.85rem; font-size: 0.8rem;">
                        @guest
                            🔒 Buka Teaser
                        @else
                            Buka Materi →
                        @endguest
                    </a>
                </div>
            </div>
        </article>
        @empty
        <div style="grid-column: 1 / -1; padding: 4rem 2rem; text-align: center;" class="card">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;">Tidak Ditemukan Materi Pustaka</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem; max-width: 450px; margin: 0 auto 1.5rem auto;">
                Belum ada koleksi pustaka yang sesuai dengan filter atau kata kunci pencarian Anda.
            </p>
            <a href="{{ route('library.index') }}" class="btn btn-secondary btn-sm">Reset Filter Pencarian</a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($items->hasPages())
        <div style="margin-top: 3rem; display: flex; justify-content: center;">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection
