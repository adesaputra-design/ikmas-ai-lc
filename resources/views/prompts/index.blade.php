@extends('layouts.app')

@section('title', 'Prompt Library Interaktif — IKMAS AI Learning Center')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    <!-- Page Header -->
    <div style="text-align: center; max-width: 750px; margin: 0 auto 3rem auto;">
        <span class="badge badge-primary" style="margin-bottom: 0.75rem;">1-Click Copy & Paste</span>
        <h1 style="font-size: 2.75rem; font-weight: 800; margin-bottom: 1rem; letter-spacing: -0.02em;">
            Prompt Library Interaktif
        </h1>
        <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.6;">
            Koleksi instruksi teruji untuk berbagai kebutuhan pekerjaan harian, penulisan konten, riset, hingga pengembangan usaha. Salin langsung dengan satu klik dan sesuaikan parameter di dalamnya.
        </p>
    </div>

    <!-- Search & Filter Card -->
    <div class="card" style="margin-bottom: 3rem; padding: 1.5rem;">
        <form method="GET" action="{{ url('/prompts') }}" style="display: flex; flex-direction: column; gap: 1.25rem;">
            <!-- Search Input -->
            <div style="display: flex; gap: 0.75rem; width: 100%;">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari prompt berdasarkan judul, kata kunci, atau kegunaan..." 
                       style="flex: 1; padding: 0.625rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem;">
                @if(request('role'))
                    <input type="hidden" name="role" value="{{ request('role') }}">
                @endif
                @if(request('tool'))
                    <input type="hidden" name="tool" value="{{ request('tool') }}">
                @endif
                <button type="submit" class="btn btn-primary">Cari Prompt</button>
                @if(request()->hasAny(['q', 'role', 'tool']))
                    <a href="{{ url('/prompts') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>

            <!-- Role Filters -->
            <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-right: 0.5rem;">
                    Peran:
                </span>
                <a href="{{ url('/prompts' . (request('tool') ? '?tool=' . request('tool') : '')) }}" 
                   class="badge {{ !request('role') ? 'badge-primary' : 'badge-cyan' }}" style="text-decoration: none;">
                    Semua Peran
                </a>
                @foreach($roles as $r)
                    <a href="{{ url('/prompts?role=' . urlencode($r) . (request('tool') ? '&tool=' . urlencode(request('tool')) : '')) }}" 
                       class="badge {{ request('role') === $r ? 'badge-primary' : 'badge-cyan' }}" style="text-decoration: none;">
                        {{ $r }}
                    </a>
                @endforeach
            </div>

            <!-- Tool Filters -->
            <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; border-top: 1px solid var(--border-color); padding-top: 0.75rem;">
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-right: 0.5rem;">
                    Alat AI:
                </span>
                <a href="{{ url('/prompts' . (request('role') ? '?role=' . request('role') : '')) }}" 
                   class="badge {{ !request('tool') ? 'badge-primary' : 'badge-cyan' }}" style="text-decoration: none;">
                    Semua Alat
                </a>
                @foreach($tools as $t)
                    <a href="{{ url('/prompts?tool=' . urlencode($t) . (request('role') ? '&role=' . urlencode(request('role')) : '')) }}" 
                       class="badge {{ request('tool') === $t ? 'badge-primary' : 'badge-cyan' }}" style="text-decoration: none;">
                        {{ $t }}
                    </a>
                @endforeach
            </div>
        </form>
    </div>

    <!-- Prompts Grid -->
    @if($prompts->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 1.75rem; margin-bottom: 3.5rem;">
            @foreach($prompts as $prompt)
                <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <!-- Header Badges -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.875rem; gap: 0.5rem; flex-wrap: wrap;">
                            <span class="badge badge-primary">
                                {{ $prompt->target_role }}
                            </span>
                            <span class="badge badge-cyan">
                                🛠 {{ $prompt->target_tool }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 0.875rem; line-height: 1.35;">
                            {{ $prompt->title }}
                        </h3>

                        <!-- Prompt Content Box -->
                        <div style="position: relative; background: var(--bg-surface-alt); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.1rem; margin-bottom: 1rem; font-family: monospace, sans-serif; font-size: 0.875rem; line-height: 1.6; color: var(--text-main); white-space: pre-line; max-height: 200px; overflow-y: auto;">
                            {{ $prompt->prompt_text }}
                        </div>

                        <!-- Instruction Note -->
                        @if($prompt->instruction)
                            <div style="font-size: 0.8rem; color: var(--text-muted); background: rgba(37,99,235,0.05); padding: 0.5rem 0.75rem; border-radius: var(--radius-md); margin-bottom: 1rem;">
                                💡 <strong>Tips Pakai:</strong> {{ $prompt->instruction }}
                            </div>
                        @endif
                    </div>

                    <!-- Footer Action -->
                    <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.75rem; color: var(--text-muted);">
                            📋 Disalin {{ $prompt->copy_count }}x
                        </span>

                        <button type="button" class="btn btn-secondary btn-sm" 
                                onclick="copyPrompt(this, `{{ addslashes($prompt->prompt_text) }}`)">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                            <span>Salin Prompt</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div style="display: flex; justify-content: center;">
            {{ $prompts->links() }}
        </div>
    @else
        <div class="card" style="text-align: center; padding: 4rem 2rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
            <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Prompt Belum Ditemukan</h3>
            <p style="color: var(--text-muted); max-width: 450px; margin: 0 auto 1.5rem auto;">
                Tidak ada prompt yang cocok dengan kata kunci atau filter yang kamu pilih. Coba gunakan kata kunci umum atau reset filter.
            </p>
            <a href="{{ url('/prompts') }}" class="btn btn-secondary btn-sm">Reset Filter</a>
        </div>
    @endif
</div>
@endsection
