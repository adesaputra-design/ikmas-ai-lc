@extends('layouts.app')

@section('title', 'Repositori Materi Belajar — IKMAS AI Learning Center')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    <!-- Page Header -->
    <div style="text-align: center; max-width: 750px; margin: 0 auto 3rem auto;">
        <span class="badge badge-primary" style="margin-bottom: 0.75rem;">Arsip & Kurikulum</span>
        <h1 style="font-size: 2.75rem; font-weight: 800; margin-bottom: 1rem; letter-spacing: -0.02em;">
            Repositori Materi Belajar
        </h1>
        <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.6;">
            Kumpulan materi praktis dari sesi AI Study Group dan panduan mandiri. Dirancang khusus untuk memandu alumni Assalaam langkah demi langkah.
        </p>
    </div>

    <!-- Filters & Search Bar -->
    <div class="card" style="margin-bottom: 3rem; padding: 1.5rem;">
        <form method="GET" action="{{ url('/materi') }}" style="display: flex; flex-direction: column; gap: 1.25rem;">
            <!-- Level Filter Pills -->
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <a href="{{ url('/materi' . (request('pillar') ? '?pillar=' . request('pillar') : '')) }}" 
                       class="btn btn-sm {{ !request('level') ? 'btn-primary' : 'btn-secondary' }}">
                        Semua Level
                    </a>
                    <a href="{{ url('/materi?level=beginner' . (request('pillar') ? '&pillar=' . request('pillar') : '')) }}" 
                       class="btn btn-sm {{ request('level') === 'beginner' ? 'btn-primary' : 'btn-secondary' }}">
                        🌱 Beginner
                    </a>
                    <a href="{{ url('/materi?level=explorer' . (request('pillar') ? '&pillar=' . request('pillar') : '')) }}" 
                       class="btn btn-sm {{ request('level') === 'explorer' ? 'btn-primary' : 'btn-secondary' }}">
                        🔍 Explorer
                    </a>
                    <a href="{{ url('/materi?level=practitioner' . (request('pillar') ? '&pillar=' . request('pillar') : '')) }}" 
                       class="btn btn-sm {{ request('level') === 'practitioner' ? 'btn-primary' : 'btn-secondary' }}">
                        ⚡ Practitioner
                    </a>
                </div>

                <!-- Search Input -->
                <div style="display: flex; gap: 0.5rem; width: 100%; max-width: 320px;">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari topik materi..." 
                           style="flex: 1; padding: 0.5rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
                    @if(request('level'))
                        <input type="hidden" name="level" value="{{ request('level') }}">
                    @endif
                    @if(request('pillar'))
                        <input type="hidden" name="pillar" value="{{ request('pillar') }}">
                    @endif
                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                </div>
            </div>

            <!-- Pillar Filter Chips -->
            <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <span style="font-size: 0.825rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-right: 0.5rem;">
                    Pilar:
                </span>
                @php
                    $pillars = [
                        'basics' => 'AI Basics',
                        'tools' => 'AI Tools',
                        'productivity' => 'AI Productivity',
                        'workflow' => 'AI Workflow',
                        'opportunity' => 'AI for Opportunity',
                    ];
                @endphp
                <a href="{{ url('/materi' . (request('level') ? '?level=' . request('level') : '')) }}" 
                   class="badge {{ !request('pillar') ? 'badge-primary' : 'badge-cyan' }}" style="text-decoration: none;">
                    Semua Pilar
                </a>
                @foreach($pillars as $key => $label)
                    <a href="{{ url('/materi?pillar=' . $key . (request('level') ? '&level=' . request('level') : '')) }}" 
                       class="badge {{ request('pillar') === $key ? 'badge-primary' : 'badge-cyan' }}" style="text-decoration: none;">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </form>
    </div>

    <!-- Materials Grid -->
    @if($materials->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.75rem; margin-bottom: 3.5rem;">
            @foreach($materials as $material)
                <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <span class="badge badge-{{ $material->level_color }}">
                                {{ $material->level_label }}
                            </span>
                            <span style="font-size: 0.8rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.25rem;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                {{ $material->reading_minutes }} Menit Baca
                            </span>
                        </div>

                        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.75rem; line-height: 1.35;">
                            <a href="{{ url('/materi/' . $material->slug) }}" style="color: var(--text-main);">
                                {{ $material->title }}
                            </a>
                        </h3>

                        <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.25rem;">
                            {{ Str::limit($material->summary, 120) }}
                        </p>
                    </div>

                    <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <span class="badge badge-cyan" style="font-size: 0.7rem;">
                            {{ $material->pillar_label }}
                        </span>
                        
                        <a href="{{ url('/materi/' . $material->slug) }}" class="btn btn-primary btn-sm">
                            Baca Materi →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div style="display: flex; justify-content: center;">
            {{ $materials->links() }}
        </div>
    @else
        <div class="card" style="text-align: center; padding: 4rem 2rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">📚</div>
            <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Materi Belum Ditemukan</h3>
            <p style="color: var(--text-muted); max-width: 450px; margin: 0 auto 1.5rem auto;">
                Tidak ada materi yang cocok dengan kriteria filter yang kamu pilih saat ini. Coba pilih level atau pilar lain.
            </p>
            <a href="{{ url('/materi') }}" class="btn btn-secondary btn-sm">Reset Filter</a>
        </div>
    @endif
</div>
@endsection
