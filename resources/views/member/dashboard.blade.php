@extends('layouts.app')

@section('title', 'Dashboard Member — IKMAS AI Learning Center')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    @if(session('success'))
        <div style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: var(--radius-lg); padding: 1rem 1.25rem; margin-bottom: 2rem; color: #10b981; display: flex; align-items: center; gap: 0.75rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span style="font-weight: 600;">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Profile Header Card -->
    <div class="card card-elevated" style="margin-bottom: 3rem; padding: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div style="width: 4.5rem; height: 4.5rem; border-radius: 50%; background: linear-gradient(135deg, #1e40af, #0284c7); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.75rem; font-weight: 800;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <h1 style="font-size: 1.75rem; font-weight: 800;">{{ $user->name }}</h1>
                        <span class="badge {{ $user->role_badge['class'] }}">{{ $user->role_badge['label'] }}</span>
                    </div>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">
                        @if($user->isSubscriber())
                            📱 {{ $user->whatsapp_number ?? '-' }}
                        @else
                            🎓 Alumni Assalaam Angkatan <strong>{{ $user->alumni_year ?? '-' }}</strong> &bull; 📱 {{ $user->whatsapp_number ?? '-' }}
                        @endif
                    </div>
                </div>
            </div>

            @if(!$user->isSubscriber())
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="{{ route('member.library.create') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #f59e0b, #d97706); border-color: #d97706;">
                    <span>🎓 Ajukan Karya Ilmiah</span>
                </a>
                <a href="{{ url('/member/showcase/create') }}" class="btn btn-secondary">
                    <span>+ Showcase Baru</span>
                </a>
            </div>
            @endif
        </div>
    </div>

    @if(!$user->isSubscriber())
    <!-- My Showcases Section -->
    <div style="margin-bottom: 4rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.5rem; font-weight: 800;">Karya Saya di Showcase</h2>
                <p style="font-size: 0.875rem; color: var(--text-muted);">Pantau status kurasi dan tayangan karyamu di komunitas.</p>
            </div>
            <a href="{{ url('/member/showcase/create') }}" class="btn btn-secondary btn-sm">
                + Tambah Karya
            </a>
        </div>

        @if($myShowcases->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
                @foreach($myShowcases as $showcase)
                    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                <span class="badge badge-{{ $showcase->status_color }}">
                                    {{ $showcase->status_label }}
                                </span>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">
                                    🛠 {{ $showcase->tools_used }}
                                </span>
                            </div>

                            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem;">
                                {{ $showcase->title }}
                            </h3>

                            <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1rem;">
                                {{ Str::limit($showcase->description, 110) }}
                            </p>

                            @if($showcase->status === 'pending')
                                <div style="background: rgba(245,158,11,0.08); border-radius: var(--radius-md); padding: 0.625rem 0.875rem; font-size: 0.8rem; color: var(--accent-amber);">
                                    ⏳ Sedang dalam antrean kurasi pengurus IKMAS AI.
                                </div>
                            @elseif($showcase->status === 'approved')
                                <div style="background: rgba(16,185,129,0.08); border-radius: var(--radius-md); padding: 0.625rem 0.875rem; font-size: 0.8rem; color: var(--accent-emerald);">
                                    ✨ Karya telah tayang di etalase publik!
                                </div>
                            @endif
                        </div>

                        @if($showcase->status === 'approved')
                            <div style="border-top: 1px solid var(--border-color); padding-top: 0.75rem; margin-top: 1rem; text-align: right;">
                                <a href="{{ url('/showcase/' . $showcase->slug) }}" class="btn btn-secondary btn-sm">
                                    Lihat di Galeri Publik →
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="card" style="text-align: center; padding: 3rem 1.5rem;">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">💡</div>
                <h4 style="font-size: 1.15rem; margin-bottom: 0.5rem;">Belum Ada Karya yang Diajukan</h4>
                <p style="color: var(--text-muted); font-size: 0.9rem; max-width: 420px; margin: 0 auto 1.25rem auto;">
                    Punya bot automasi, prompt khusus, draf buku, atau visual yang kamu hasilkan dengan AI? Bagikan karyamu sekarang!
                </p>
                <a href="{{ url('/member/showcase/create') }}" class="btn btn-primary btn-sm">
                    Mulai Ajukan Karya
                </a>
            </div>
        @endif
    </div>
    @endif

    @if(!$user->isSubscriber())
    <!-- My Academic Papers Section -->
    <div style="margin-bottom: 4rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);">Karya Ilmiah Saya di Pustaka AI</h2>
                <p style="font-size: 0.875rem; color: var(--text-muted);">Pantau status kurasi skripsi, tesis, disertasi, atau paper riset yang kamu ajukan.</p>
            </div>
            <a href="{{ route('member.library.create') }}" class="btn btn-secondary btn-sm">
                + Ajukan Karya Ilmiah
            </a>
        </div>

        @if($myAcademicPapers->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
                @foreach($myAcademicPapers as $paper)
                    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                @if($paper->status === 'approved')
                                    <span class="badge badge-emerald">✨ Terbit di Pustaka</span>
                                @elseif($paper->status === 'rejected')
                                    <span class="badge" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.2);">⚠️ Perlu Revisi</span>
                                @else
                                    <span class="badge badge-amber">⏳ Menunggu Kurasi</span>
                                @endif
                                <span class="badge badge-secondary" style="font-size: 0.75rem;">
                                    {{ $paper->degree_label }}
                                </span>
                            </div>

                            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-main);">
                                {{ $paper->title }}
                            </h3>

                            <div style="font-size: 0.825rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                                🏛️ {{ $paper->institution }} &bull; 🗓️ {{ $paper->publication_year }}
                            </div>

                            <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1rem;">
                                {{ Str::limit($paper->summary_preview, 110) }}
                            </p>

                            @if($paper->status === 'rejected' && $paper->rejection_note)
                                <div style="background: rgba(239,68,68,0.08); border-radius: var(--radius-md); padding: 0.625rem 0.875rem; font-size: 0.8rem; color: #ef4444; margin-bottom: 0.75rem;">
                                    <strong>Catatan Pengurus:</strong> {{ $paper->rejection_note }}
                                </div>
                            @endif
                        </div>

                        @if($paper->status === 'approved')
                            <div style="border-top: 1px solid var(--border-color); padding-top: 0.75rem; margin-top: 1rem; text-align: right;">
                                <a href="{{ route('library.show', $paper->slug) }}" class="btn btn-secondary btn-sm">
                                    Lihat di Pustaka Publik →
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="card" style="text-align: center; padding: 3rem 1.5rem;">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🎓</div>
                <h4 style="font-size: 1.15rem; margin-bottom: 0.5rem; color: var(--text-main);">Belum Ada Karya Ilmiah yang Diajukan</h4>
                <p style="color: var(--text-muted); font-size: 0.9rem; max-width: 440px; margin: 0 auto 1.25rem auto;">
                    Punya skripsi, tesis, disertasi, atau publikasi jurnal terkait AI & teknologi? Arsipkan karya intelektualmu bersama alumni Assalaam!
                </p>
                <a href="{{ route('member.library.create') }}" class="btn btn-primary btn-sm">
                    Mulai Ajukan Karya Ilmiah
                </a>
            </div>
        @endif
    </div>
    @endif

    <!-- Bookmarked Prompts & Library Section -->
    <div style="margin-bottom: 4rem;">
        <div style="margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 800;">Pustaka AI Tersimpan (Bookmarks)</h2>
            <p style="font-size: 0.875rem; color: var(--text-muted);">Buku, resume podcast, dan karya ilmiah yang kamu simpan untuk dibaca nanti.</p>
        </div>

        @if($bookmarkedLibraries->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
                @foreach($bookmarkedLibraries as $bLib)
                    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <span class="badge {{ $bLib->type_badge['class'] }}" style="font-size: 0.7rem;">
                                    {{ $bLib->type_badge['icon'] }} {{ $bLib->type_badge['label'] }}
                                </span>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">
                                    {{ $bLib->category }}
                                </span>
                            </div>
                            <h4 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-main);">
                                <a href="{{ route('library.show', $bLib->slug) }}" style="color: inherit; text-decoration: none;">
                                    {{ $bLib->title }}
                                </a>
                            </h4>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">
                                {{ Str::limit($bLib->summary_preview, 100) }}
                            </p>
                        </div>
                        <div style="border-top: 1px solid var(--border-color); padding-top: 0.75rem; margin-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
                            <a href="{{ route('library.show', $bLib->slug) }}" class="btn btn-secondary btn-sm" style="font-size: 0.8rem;">
                                Buka Materi →
                            </a>
                            <form method="POST" action="{{ route('member.bookmarks.toggle') }}">
                                @csrf
                                <input type="hidden" name="type" value="library">
                                <input type="hidden" name="id" value="{{ $bLib->id }}">
                                <button type="submit" class="btn btn-sm" style="color: #ef4444; background: none; border: none; font-size: 0.8rem; cursor: pointer;">
                                    ✕ Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card" style="text-align: center; padding: 2rem 1.5rem; margin-bottom: 2.5rem;">
                <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 0.75rem;">Kamu belum menyimpan buku, podcast, atau paper favorit.</p>
                <a href="{{ route('library.index') }}" class="btn btn-secondary btn-sm">Jelajahi Pustaka AI</a>
            </div>
        @endif

        <div style="margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 800;">Prompt Tersimpan (Bookmarks)</h2>
            <p style="font-size: 0.875rem; color: var(--text-muted);">Daftar instruksi prompt yang kamu simpan untuk akses kilat.</p>
        </div>

        @if($bookmarkedPrompts->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.5rem;">
                @foreach($bookmarkedPrompts as $bPrompt)
                    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <span class="badge badge-primary" style="margin-bottom: 0.5rem;">{{ $bPrompt->target_role }}</span>
                            <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem;">{{ $bPrompt->title }}</h4>
                            <div style="background: var(--bg-surface-alt); padding: 0.75rem; border-radius: var(--radius-md); font-family: monospace; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                                {{ Str::limit($bPrompt->prompt_text, 120) }}
                            </div>
                        </div>
                        <div style="border-top: 1px solid var(--border-color); padding-top: 0.75rem; text-align: right;">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="copyPrompt(this, `{{ addslashes($bPrompt->prompt_text) }}`)">
                                Salin Prompt
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card" style="text-align: center; padding: 2.5rem 1.5rem;">
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem;">Kamu belum menandai prompt favorit.</p>
                <a href="{{ url('/prompts') }}" class="btn btn-secondary btn-sm">Jelajahi Prompt Library</a>
            </div>
        @endif
    </div>

    <!-- Security & Change Password Section -->
    <div style="margin-top: 3.5rem;">
        <div style="margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 800;">Keamanan Akun & Kata Sandi</h2>
            <p style="font-size: 0.875rem; color: var(--text-muted);">Perbarui kata sandi akun portal Anda secara berkala demi keamanan.</p>
        </div>

        <div class="card" style="max-width: 540px; padding: 1.75rem;">
            @if(session('success'))
                <div style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: var(--radius-md); padding: 0.75rem 1rem; margin-bottom: 1.25rem; color: #10b981; font-size: 0.875rem; font-weight: 600;">
                    ✓ {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: var(--radius-md); padding: 0.75rem 1rem; margin-bottom: 1.25rem; color: #ef4444; font-size: 0.875rem;">
                    @foreach($errors->all() as $err)
                        <div>• {{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('member.password.update') }}" style="display: flex; flex-direction: column; gap: 1rem;">
                @csrf
                <div>
                    <label for="current_password" style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.35rem;">Kata Sandi Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" required
                           style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
                </div>

                <div>
                    <label for="password" style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.35rem;">Kata Sandi Baru (Min. 8 karakter)</label>
                    <input type="password" id="password" name="password" required minlength="8"
                           style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
                </div>

                <div>
                    <label for="password_confirmation" style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.35rem;">Ulangi Kata Sandi Baru</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                           style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
                </div>

                <div style="margin-top: 0.5rem;">
                    <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.5rem 1.25rem;">
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
