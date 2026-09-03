@extends('layouts.admin')

@section('title', 'Kurasi Showcase Karya — IKMAS AI')
@section('page-title', 'Kurasi Showcase Karya')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 800; margin: 0;">Moderasi & Kurasi Karya Alumni</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0.25rem 0 0 0;">
            Validasi inovasi, proyek automasi, dan kreasi AI yang diajukan oleh alumni Assalaam.
        </p>
    </div>
</div>

<!-- Tabs & Search Header -->
<div class="card" style="padding: 1.25rem; margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <!-- Status Tabs -->
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('admin.curation.index', ['status' => 'pending']) }}"
               class="btn btn-sm {{ $status === 'pending' ? 'btn-primary' : 'btn-secondary' }}">
                ⏳ Menunggu Kurasi ({{ $counts['pending'] }})
            </a>
            <a href="{{ route('admin.curation.index', ['status' => 'approved']) }}"
               class="btn btn-sm {{ $status === 'approved' ? 'btn-primary' : 'btn-secondary' }}">
                ✓ Disetujui ({{ $counts['approved'] }})
            </a>
            <a href="{{ route('admin.curation.index', ['status' => 'rejected']) }}"
               class="btn btn-sm {{ $status === 'rejected' ? 'btn-primary' : 'btn-secondary' }}">
                ✕ Perlu Revisi ({{ $counts['rejected'] }})
            </a>
            <a href="{{ route('admin.curation.index', ['status' => 'all']) }}"
               class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-secondary' }}">
                Semua Karya ({{ $counts['all'] }})
            </a>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('admin.curation.index') }}" style="display: flex; gap: 0.5rem;">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari karya / kreator..."
                   style="padding: 0.45rem 0.8rem; font-size: 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main);">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            @if(request('q'))
                <a href="{{ route('admin.curation.index', ['status' => $status]) }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>
    </div>
</div>

<!-- Showcases List / Grid -->
@if($showcases->count() > 0)
    <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-bottom: 2rem;">
        @foreach($showcases as $item)
            <div class="card card-elevated" style="border-left: 4px solid {{ $item->status === 'approved' ? 'var(--accent-emerald)' : ($item->status === 'pending' ? 'var(--accent-amber)' : '#ef4444') }}; padding: 1.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem; flex-wrap: wrap;">
                            <span class="badge badge-{{ $item->status_color }}">
                                {{ $item->status_label }}
                            </span>
                            @if($item->is_featured)
                                <span class="badge badge-amber">⭐ Karya Unggulan (Featured)</span>
                            @endif
                            <span class="badge badge-cyan">🛠 {{ $item->tools_used }}</span>
                        </div>
                        <h3 style="font-size: 1.35rem; font-weight: 800; margin: 0; line-height: 1.3;">
                            {{ $item->title }}
                        </h3>
                    </div>

                    <!-- Creator Details & WA Direct Contact -->
                    <div style="text-align: right; font-size: 0.85rem; color: var(--text-muted);">
                        <div>Diajukan oleh: <strong style="color: var(--text-main);">{{ $item->user->name ?? 'Member' }}</strong></div>
                        <div>Alumni Angkatan: <strong>{{ $item->user->alumni_year ?? '-' }}</strong></div>
                        @if($item->user->whatsapp_number)
                            <div style="margin-top: 0.25rem;">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->user->whatsapp_number) }}" target="_blank" rel="noopener"
                                   style="color: #10b981; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
                                    💬 Hubungi: {{ $item->user->whatsapp_number }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Description & Impact Story -->
                <div style="background: var(--bg-surface-alt); padding: 1rem 1.25rem; border-radius: var(--radius-md); font-size: 0.95rem; line-height: 1.6; margin-bottom: 1rem; border: 1px solid var(--border-color);">
                    <div style="font-weight: 700; margin-bottom: 0.25rem; color: var(--text-main);">Deskripsi Proyek:</div>
                    <p style="margin: 0 0 0.75rem 0; color: var(--text-main); white-space: pre-line;">{{ $item->description }}</p>

                    @if($item->impact_story)
                        <div style="border-top: 1px solid var(--border-color); padding-top: 0.75rem; color: var(--accent-emerald);">
                            <strong>Cerita Dampak & Manfaat:</strong> {{ $item->impact_story }}
                        </div>
                    @endif

                    @if($item->project_url)
                        <div style="margin-top: 0.75rem;">
                            <a href="{{ $item->project_url }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
                                Kunjungi Link Proyek ↗
                            </a>
                        </div>
                    @endif

                    @if($item->admin_notes && $item->status === 'rejected')
                        <div style="margin-top: 0.75rem; background: rgba(239,68,68,0.08); padding: 0.5rem 0.75rem; border-radius: var(--radius-md); border-left: 3px solid #ef4444; font-size: 0.85rem; color: #ef4444;">
                            <strong>Catatan Revisi untuk Member:</strong> {{ $item->admin_notes }}
                        </div>
                    @endif
                </div>

                <!-- Curation Actions Footer -->
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                    <div>
                        @if($item->status === 'approved')
                            <a href="{{ url('/showcase/' . $item->slug) }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 0.8rem;">
                                Lihat di Etalase Publik ↗
                            </a>
                        @endif
                    </div>

                    <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                        <!-- Toggle Featured Button (only for approved) -->
                        @if($item->status === 'approved')
                            <form method="POST" action="{{ route('admin.curation.toggle-featured', $item->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.8rem;">
                                    {{ $item->is_featured ? '✕ Cabut Unggulan' : '⭐ Jadikan Unggulan' }}
                                </button>
                            </form>
                        @endif

                        <!-- Reject Form (for pending or approved) -->
                        @if($item->status !== 'rejected')
                            <form method="POST" action="{{ route('admin.curation.reject', $item->id) }}" style="display: flex; gap: 0.35rem; align-items: center;">
                                @csrf
                                <input type="text" name="admin_notes" placeholder="Catatan perbaikan..." 
                                       style="padding: 0.35rem 0.65rem; font-size: 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); width: 180px;">
                                <button type="submit" class="btn btn-secondary btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3); font-size: 0.8rem;">
                                    Tolak / Revisi
                                </button>
                            </form>
                        @endif

                        <!-- Approve Button (for pending or rejected) -->
                        @if($item->status !== 'approved')
                            <form method="POST" action="{{ route('admin.curation.approve', $item->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-whatsapp btn-sm" style="font-size: 0.8rem;">
                                    ✓ Setujui & Publikasikan
                                </button>
                            </form>
                        @endif

                        <!-- Delete Button -->
                        <form method="POST" action="{{ route('admin.curation.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karya ini secara permanen?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm" style="padding: 0.35rem 0.55rem; color: var(--text-muted); background: transparent; border: none;" title="Hapus Permanen">
                                🗑
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div style="display: flex; justify-content: center;">
        {{ $showcases->links() }}
    </div>
@else
    <div class="card" style="text-align: center; padding: 4rem 1.5rem;">
        <div style="font-size: 3rem; margin-bottom: 0.5rem;">🎉</div>
        <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.25rem;">Tidak Ada Karya di Kategori Ini</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">
            Semua pengajuan telah tertinjau atau belum ada submisi yang sesuai dengan tab ini.
        </p>
    </div>
@endif
@endsection
