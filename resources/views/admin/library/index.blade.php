@extends('layouts.admin')

@section('title', 'Pustaka AI & Kurasi Karya — IKMAS AI')
@section('page-title', 'Pustaka AI & Kurasi Karya')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 800; margin: 0;">Pustaka AI & Kurasi Karya Ilmiah</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0.25rem 0 0 0;">
            Kelola rangkuman buku AI, resume podcast, dan moderasi karya ilmiah alumni Assalaam.
        </p>
    </div>

    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('admin.library.create') }}" class="btn btn-primary btn-sm">
            + Tambah Koleksi Baru
        </a>
    </div>
</div>

<!-- Metrics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="card" style="padding: 1.25rem; border-left: 4px solid var(--accent-amber);">
        <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Antrean Kurasi Ilmiah</div>
        <div style="font-size: 1.85rem; font-weight: 900; color: var(--text-main); margin-top: 0.25rem;">
            {{ $pendingCount }}
        </div>
        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Menunggu review pengurus</div>
    </div>
    <div class="card" style="padding: 1.25rem; border-left: 4px solid var(--primary);">
        <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Rangkuman Buku</div>
        <div style="font-size: 1.85rem; font-weight: 900; color: var(--text-main); margin-top: 0.25rem;">
            {{ $bookCount }}
        </div>
        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Buku terindeks</div>
    </div>
    <div class="card" style="padding: 1.25rem; border-left: 4px solid var(--accent-cyan);">
        <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Resume Podcast</div>
        <div style="font-size: 1.85rem; font-weight: 900; color: var(--text-main); margin-top: 0.25rem;">
            {{ $podcastCount }}
        </div>
        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Episode tersaji</div>
    </div>
    <div class="card" style="padding: 1.25rem; border-left: 4px solid var(--accent-emerald);">
        <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Karya Ilmiah Terbit</div>
        <div style="font-size: 1.85rem; font-weight: 900; color: var(--text-main); margin-top: 0.25rem;">
            {{ $academicCount }}
        </div>
        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Tesis, skripsi, jurnal alumni</div>
    </div>
</div>

<!-- Tabs & Filter Card -->
<div class="card" style="padding: 1.25rem; margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <!-- Tabs -->
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('admin.library.index', ['tab' => 'all']) }}" 
               class="btn btn-sm {{ $tab === 'all' ? 'btn-primary' : 'btn-secondary' }}">
                Semua Koleksi
            </a>
            <a href="{{ route('admin.library.index', ['tab' => 'curation']) }}" 
               class="btn btn-sm {{ $tab === 'curation' ? 'btn-primary' : 'btn-secondary' }}"
               style="{{ $pendingCount > 0 ? 'border: 1px solid var(--accent-amber); font-weight: 700;' : '' }}">
                ⏳ Antrean Kurasi @if($pendingCount > 0)<span class="badge badge-amber" style="margin-left: 0.25rem;">{{ $pendingCount }}</span>@endif
            </a>
            <a href="{{ route('admin.library.index', ['tab' => 'book']) }}" 
               class="btn btn-sm {{ $tab === 'book' ? 'btn-primary' : 'btn-secondary' }}">
                📚 Buku ({{ $bookCount }})
            </a>
            <a href="{{ route('admin.library.index', ['tab' => 'podcast']) }}" 
               class="btn btn-sm {{ $tab === 'podcast' ? 'btn-primary' : 'btn-secondary' }}">
                🎙️ Podcast ({{ $podcastCount }})
            </a>
            <a href="{{ route('admin.library.index', ['tab' => 'academic']) }}" 
               class="btn btn-sm {{ $tab === 'academic' ? 'btn-primary' : 'btn-secondary' }}">
                🎓 Karya Ilmiah ({{ $academicCount }})
            </a>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('admin.library.index') }}" style="display: flex; gap: 0.5rem;">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul, penulis, instansi..."
                   style="padding: 0.45rem 0.8rem; font-size: 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main);">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            @if(request('q'))
                <a href="{{ route('admin.library.index', ['tab' => $tab]) }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>
    </div>
</div>

<!-- Library Items List -->
@if($items->count() > 0)
    <div style="display: flex; flex-direction: column; gap: 1.25rem; margin-bottom: 2rem;">
        @foreach($items as $item)
            <div class="card card-elevated" style="padding: 1.5rem; border-left: 4px solid {{ $item->status === 'approved' ? 'var(--accent-emerald)' : ($item->status === 'pending' ? 'var(--accent-amber)' : '#ef4444') }};">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                    
                    <div style="display: flex; gap: 1.25rem; flex: 1; min-width: 300px;">
                        @if($item->cover_image)
                            <img src="{{ asset('storage/' . $item->cover_image) }}" alt="{{ $item->title }}" 
                                 style="width: 80px; height: 110px; object-fit: cover; border-radius: var(--radius-md); flex-shrink: 0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                        @else
                            <div style="width: 80px; height: 110px; border-radius: var(--radius-md); background: var(--bg-surface-alt); border: 1px dashed var(--border-color); display: flex; align-items: center; justify-content: center; font-size: 2rem; flex-shrink: 0;">
                                {{ $item->type_badge['icon'] }}
                            </div>
                        @endif

                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem; flex-wrap: wrap;">
                                <span class="badge {{ $item->type_badge['class'] }}">
                                    {{ $item->type_badge['icon'] }} {{ $item->type_badge['label'] }}
                                </span>
                                <span class="badge badge-secondary">{{ $item->category }}</span>
                                @if($item->academic_degree)
                                    <span class="badge badge-amber">{{ $item->degree_label }}</span>
                                @endif
                                <span class="badge {{ $item->status === 'approved' ? 'badge-emerald' : ($item->status === 'pending' ? 'badge-amber' : 'badge-secondary') }}" style="{{ $item->status === 'rejected' ? 'background: rgba(239,68,68,0.1); color: #ef4444;' : '' }}">
                                    {{ $item->status === 'approved' ? '✓ Terbit' : ($item->status === 'pending' ? '⏳ Menunggu Review' : '✕ Ditolak') }}
                                </span>
                                @if($item->is_featured)
                                    <span class="badge badge-amber">⭐ Unggulan</span>
                                @endif
                            </div>

                            <h3 style="font-size: 1.2rem; font-weight: 800; margin: 0 0 0.25rem 0; line-height: 1.3;">
                                <a href="{{ url('/library/' . $item->slug) }}" target="_blank" style="color: inherit; text-decoration: none;">
                                    {{ $item->title }}
                                </a>
                            </h3>

                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                                @if($item->isBook())
                                    Penulis: <strong style="color: var(--text-main);">{{ $item->author_name ?? '-' }}</strong> @if($item->reading_time)• Estimasi: {{ $item->reading_time }}@endif
                                @elseif($item->isPodcast())
                                    Sumber: <strong style="color: var(--text-main);">{{ $item->podcast_source ?? '-' }}</strong> @if($item->duration)• Durasi: {{ $item->duration }}@endif
                                @elseif($item->isAcademic())
                                    Institusi: <strong style="color: var(--text-main);">{{ $item->institution ?? '-' }}</strong> ({{ $item->publication_year ?? '-' }})
                                    @if($item->user)
                                        <span style="margin-left: 0.5rem; color: var(--primary); font-weight: 600;">(Alumni: {{ $item->user->name }} - {{ $item->user->alumni_year ?? 'Member' }})</span>
                                    @endif
                                @endif
                            </div>

                            <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5;">
                                {{ $item->summary_preview }}
                            </p>

                            @if($item->rejection_note && $item->status === 'rejected')
                                <div style="margin-top: 0.5rem; padding: 0.5rem 0.75rem; background: rgba(239,68,68,0.08); border-left: 3px solid #ef4444; border-radius: var(--radius-sm); font-size: 0.8rem; color: #ef4444;">
                                    <strong>Catatan Evaluasi / Revisi:</strong> {{ $item->rejection_note }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions Buttons -->
                    <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                        <a href="{{ url('/library/' . $item->slug) }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 0.8rem;">
                            Lihat ↗
                        </a>

                        <a href="{{ route('admin.library.edit', $item->id) }}" class="btn btn-secondary btn-sm" style="font-size: 0.8rem;">
                            Edit
                        </a>

                        @if($item->isAcademic())
                            @if($item->status !== 'approved')
                                <form method="POST" action="{{ route('admin.library.approve', $item->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-whatsapp btn-sm" style="font-size: 0.8rem;">
                                        ✓ Setujui
                                    </button>
                                </form>
                            @endif

                            @if($item->status !== 'rejected')
                                <button type="button" class="btn btn-secondary btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3); font-size: 0.8rem;"
                                        onclick="document.getElementById('reject-form-{{ $item->id }}').style.display = document.getElementById('reject-form-{{ $item->id }}').style.display === 'none' ? 'flex' : 'none'">
                                    ✕ Tolak / Catatan
                                </button>
                            @endif
                        @endif

                        <form method="POST" action="{{ route('admin.library.destroy', $item->id) }}" onsubmit="return confirm('Hapus permanen konten ini dari pustaka?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm" style="color: var(--text-muted); background: transparent; border: none;" title="Hapus">
                                🗑
                            </button>
                        </form>
                    </div>

                </div>

                @if($item->isAcademic())
                    <!-- Inline Reject Form (Toggleable) -->
                    <form id="reject-form-{{ $item->id }}" method="POST" action="{{ route('admin.library.reject', $item->id) }}" 
                          style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color); gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                        @csrf
                        <input type="text" name="rejection_note" placeholder="Tuliskan catatan evaluasi atau alasan penolakan..." required
                               style="flex: 1; min-width: 250px; padding: 0.45rem 0.75rem; font-size: 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main);">
                        <button type="submit" class="btn btn-secondary btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3);">
                            Kirim Catatan Penolakan
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div style="display: flex; justify-content: center;">
        {{ $items->links() }}
    </div>
@else
    <div class="card" style="text-align: center; padding: 4rem 1.5rem;">
        <div style="font-size: 3rem; margin-bottom: 0.5rem;">📖</div>
        <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.25rem;">Tidak Ada Konten Pustaka</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0 0 1.25rem 0;">
            Belum ada item pustaka dalam kategori atau filter ini.
        </p>
        <a href="{{ route('admin.library.create') }}" class="btn btn-primary btn-sm">
            + Tambah Konten Sekarang
        </a>
    </div>
@endif
@endsection
