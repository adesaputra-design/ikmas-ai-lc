@extends('layouts.app')

@section('title', 'Showcase Karya Alumni — IKMAS AI Learning Center')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    <!-- Page Header -->
    <div style="text-align: center; max-width: 750px; margin: 0 auto 3rem auto;">
        <span class="badge badge-emerald" style="margin-bottom: 0.75rem;">Etalase Kreasi Nyata</span>
        <h1 style="font-size: 2.75rem; font-weight: 800; margin-bottom: 1rem; letter-spacing: -0.02em;">
            Showcase Karya Alumni
        </h1>
        <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.6;">
            Bukti nyata transformasi alumni Assalaam: dari pemula yang penasaran menjadi kreator yang menghasilkan karya, proyek, dan solusi bernilai.
        </p>
    </div>

    <!-- Actions & Filter Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1rem;">
        <form method="GET" action="{{ url('/showcase') }}" style="display: flex; gap: 0.5rem; width: 100%; max-width: 350px;">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari karya atau tools..."
                   style="flex: 1; padding: 0.5rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            @if(request('q'))
                <a href="{{ url('/showcase') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>

        <a href="{{ auth()->check() ? url('/member/showcase/create') : url('/login') }}" class="btn btn-whatsapp">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Ajukan Karya Saya +</span>
        </a>
    </div>

    <!-- Showcase Grid -->
    @if($showcases->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem; margin-bottom: 3.5rem;">
            @foreach($showcases as $item)
                <div class="card card-elevated" style="display: flex; flex-direction: column; justify-content: space-between; padding: 0; overflow: hidden;">
                    <!-- Image or Visual Header -->
                    <div style="height: 180px; background: linear-gradient(135deg, #1e3a8a 0%, #0284c7 100%); display: flex; align-items: center; justify-content: center; position: relative; color: white;">
                        @if($item->image_url)
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="text-align: center; padding: 1.5rem;">
                                <div style="font-size: 3rem; margin-bottom: 0.25rem;">🚀</div>
                                <span style="font-size: 0.85rem; font-weight: 600; opacity: 0.9;">Project Showcase Alumni</span>
                            </div>
                        @endif

                        <div style="position: absolute; bottom: 0.75rem; left: 1rem; right: 1rem; display: flex; justify-content: space-between; align-items: center;">
                            <span class="badge" style="background: rgba(15, 23, 42, 0.75); color: white; backdrop-filter: blur(4px); font-size: 0.7rem;">
                                🛠 {{ $item->tools_used }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <!-- Author info -->
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <div style="width: 1.75rem; height: 1.75rem; border-radius: 50%; background: var(--bg-surface-alt); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; color: var(--primary);">
                                    {{ strtoupper(substr($item->user->name ?? 'A', 0, 1)) }}
                                </div>
                                <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-main);">
                                    {{ $item->user->name ?? 'Alumni Assalaam' }}
                                </span>
                                @if($item->user->alumni_year)
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">
                                        &bull; Angkatan {{ $item->user->alumni_year }}
                                    </span>
                                @endif
                            </div>

                            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.75rem; line-height: 1.35;">
                                <a href="{{ url('/showcase/' . $item->slug) }}" style="color: var(--text-main);">
                                    {{ $item->title }}
                                </a>
                            </h3>

                            <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.25rem;">
                                {{ Str::limit($item->description, 130) }}
                            </p>

                            <!-- Impact Story Highlight if available -->
                            @if($item->impact_story)
                                <div style="background: rgba(16, 185, 129, 0.08); border-left: 3px solid var(--accent-emerald); padding: 0.75rem 1rem; border-radius: 0.375rem; font-size: 0.825rem; color: var(--text-main); margin-bottom: 1.25rem;">
                                    <strong>Dampak Nyata:</strong> {{ Str::limit($item->impact_story, 100) }}
                                </div>
                            @endif
                        </div>

                        <!-- Footer -->
                        <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
                            @if($item->project_url)
                                <a href="{{ $item->project_url }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm" style="font-size: 0.8rem;">
                                    Tautan Proyek ↗
                                </a>
                            @else
                                <span></span>
                            @endif

                            <a href="{{ url('/showcase/' . $item->slug) }}" class="btn btn-primary btn-sm">
                                Detail Karya →
                            </a>
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
        <div class="card" style="text-align: center; padding: 4rem 2rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🎨</div>
            <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Belum Ada Karya yang Ditampilkan</h3>
            <p style="color: var(--text-muted); max-width: 450px; margin: 0 auto 1.5rem auto;">
                Jadilah alumni pertama yang memamerkan hasil kreasi atau automasi AI karyamu di panggung IKMAS AI!
            </p>
            <a href="{{ auth()->check() ? url('/member/showcase/create') : url('/login') }}" class="btn btn-primary">
                Ajukan Karyamu Sekarang
            </a>
        </div>
    @endif
</div>
@endsection
