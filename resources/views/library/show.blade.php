@extends('layouts.app')

@section('title', $item->title . ' — Pustaka AI IKMAS')

@section('content')
<div class="container" style="padding-top: 2.5rem; padding-bottom: 5rem;">
    <!-- Breadcrumb -->
    <nav style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 2rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <a href="{{ url('/') }}" style="color: var(--text-muted); text-decoration: none;">Beranda</a>
        <span>/</span>
        <a href="{{ route('library.index') }}" style="color: var(--text-muted); text-decoration: none;">Pustaka AI</a>
        <span>/</span>
        <a href="{{ route('library.index', ['type' => $item->type]) }}" style="color: var(--text-muted); text-decoration: none;">{{ $item->type_badge['label'] }}</a>
        <span>/</span>
        <span style="color: var(--text-main); font-weight: 600;">{{ \Illuminate\Support\Str::limit($item->title, 40) }}</span>
    </nav>

    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 2.5rem; align-items: start;">
        <!-- Main Content Area -->
        <main>
            <!-- Article Header -->
            <div style="margin-bottom: 2rem;">
                <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 1rem; flex-wrap: wrap;">
                    <span class="badge {{ $item->type_badge['class'] }}">
                        {{ $item->type_badge['icon'] }} {{ $item->type_badge['label'] }}
                    </span>
                    <span class="badge badge-secondary">
                        {{ $item->category }}
                    </span>
                    @if($item->isAcademic())
                        <span class="badge badge-amber">
                            🎓 {{ $item->degree_label }}
                        </span>
                    @endif
                </div>

                <h1 style="font-size: 2.4rem; font-weight: 800; line-height: 1.3; margin-bottom: 1rem; letter-spacing: -0.01em; color: var(--text-main);">
                    {{ $item->title }}
                </h1>

                <!-- Metadata Row -->
                <div style="display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap; font-size: 0.875rem; color: var(--text-muted); border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem;">
                    @if($item->isBook() && $item->author_name)
                        <div>✍️ Penulis: <strong style="color: var(--text-main);">{{ $item->author_name }}</strong></div>
                    @elseif($item->isPodcast() && $item->podcast_source)
                        <div>🎙️ Sumber: <strong style="color: var(--text-main);">{{ $item->podcast_source }}</strong></div>
                    @elseif($item->isAcademic())
                        <div>🏛️ Institusi: <strong style="color: var(--text-main);">{{ $item->institution ?? 'Alumni Assalaam' }}</strong></div>
                        @if($item->user)
                            <div>Oleh: <strong style="color: var(--text-main);">{{ $item->user->name }}</strong></div>
                        @endif
                    @endif

                    @if($item->reading_time)
                        <div>⏱️ {{ $item->reading_time }}</div>
                    @elseif($item->duration)
                        <div>⏳ Durasi: {{ $item->duration }}</div>
                    @elseif($item->publication_year)
                        <div>🗓️ Tahun: {{ $item->publication_year }}</div>
                    @endif

                    <div>👁️ {{ $item->views_count }} tayangan</div>
                </div>
            </div>

            <!-- Executive Summary / Sinopsis Box -->
            <div class="card" style="background: var(--bg-surface-alt); border-left: 4px solid var(--primary); margin-bottom: 2rem; padding: 1.5rem;">
                <div style="font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: var(--primary); margin-bottom: 0.5rem; letter-spacing: 0.05em;">
                    📋 Intisari & Sinopsis Singkat
                </div>
                <p style="color: var(--text-main); line-height: 1.7; font-size: 1rem; margin: 0;">
                    {{ $item->summary_preview }}
                </p>
            </div>

            <!-- Media Embed Player for Podcast (Jika Podcast) -->
            @if($item->isPodcast() && $item->embed_html)
                <div style="margin-bottom: 2.5rem;">
                    <div style="font-size: 0.9rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
                        🎧 Dengarkan Episode Podcast:
                    </div>
                    {!! $item->embed_html !!}
                </div>
            @endif

            <!-- Academic Specific Meta Box -->
            @if($item->isAcademic())
                <div class="card" style="margin-bottom: 2rem; padding: 1.25rem; background: rgba(245,158,11,0.05); border-color: rgba(245,158,11,0.2);">
                    <div style="font-size: 0.85rem; font-weight: 800; color: #d97706; margin-bottom: 0.75rem;">
                        🎓 Informasi Karya Ilmiah & Sitasi
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.75rem; font-size: 0.85rem; margin-bottom: 1rem;">
                        <div><strong>Jenjang:</strong> {{ $item->degree_label }}</div>
                        <div><strong>Institusi:</strong> {{ $item->institution ?? '-' }}</div>
                        <div><strong>Tahun Terbit:</strong> {{ $item->publication_year ?? '-' }}</div>
                        @if($item->co_authors)
                            <div><strong>Rekan Penulis:</strong> {{ $item->co_authors }}</div>
                        @endif
                    </div>

                    @if($item->external_url)
                        <div style="border-top: 1px dashed var(--border-color); padding-top: 0.75rem;">
                            <a href="{{ $item->external_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-secondary" style="font-size: 0.8rem;">
                                🌐 Buka Repositori Kampus / DOI External ↗
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Full Content / Reader Section -->
            @if($isUnlocked)
                <!-- UNLOCKED: Full Content for Members -->
                <div class="card" style="padding: 2.25rem; line-height: 1.8; font-size: 1.05rem; color: var(--text-main); margin-bottom: 2.5rem;">
                    <div style="font-size: 0.9rem; font-weight: 800; text-transform: uppercase; color: var(--primary); margin-bottom: 1.5rem; letter-spacing: 0.05em; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                        📖 Pembahasan Lengkap & Key Takeaways
                    </div>
                    
                    <div class="article-body" style="white-space: pre-line; word-break: break-word;">
                        {{ $item->content }}
                    </div>

                    <!-- Tombol Unduh Berkas PDF (Khusus Academic jika ada) -->
                    @if($item->isAcademic() && $item->file_path)
                        <div style="margin-top: 2.5rem; padding: 1.5rem; background: var(--bg-surface-alt); border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-main);">📄 Naskah Lengkap PDF Tersedia</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">Unduh naskah riset resmi dari alumni IKMAS untuk kebutuhan studi.</div>
                            </div>
                            <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="btn btn-primary btn-sm">
                                📥 Unduh Naskah PDF
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <!-- LOCKED: Teaser & CTA Box for Guests -->
                <div style="position: relative; margin-bottom: 2.5rem;">
                    <!-- Blurred Teaser Snippet -->
                    <div class="card" style="padding: 2rem; filter: blur(4px); user-select: none; opacity: 0.4; pointer-events: none;">
                        <h3>Bab 1: Fondasi dan Prinsip Utama</h3>
                        <p>Pembahasan inti mengenai transformasi kecerdasan buatan, arsitektur transformator, dan implementasi strategi AI yang efektif dalam bisnis modern...</p>
                        <h3>Bab 2: Panduan Taktis dan Poin Kunci</h3>
                        <p>Langkah-langkah konkret yang diuraikan oleh narasumber untuk mengoptimalkan workflow harian dengan prompting tingkat lanjut...</p>
                    </div>

                    <!-- Floating CTA Card -->
                    <div class="card" style="position: absolute; inset: 0; margin: auto; max-width: 480px; height: fit-content; text-align: center; padding: 2.25rem 2rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.25); border: 1px solid var(--primary); background: var(--bg-surface);">
                        <div style="width: 3.5rem; height: 3.5rem; border-radius: 50%; background: rgba(37,99,235,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 1rem auto;">
                            🔒
                        </div>
                        <h3 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 0.5rem; color: var(--text-main);">
                            Konten Eksklusif Member
                        </h3>
                        <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem;">
                            Naskah rangkuman lengkap, pemutar audio/video tersemat, serta berkas unduhan riset ini dapat diakses penuh oleh seluruh alumni Assalaam dan subscriber terdaftar.
                        </p>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <a href="{{ route('login') }}" class="btn btn-primary" style="width: 100%;">
                                Masuk ke Akun Anda
                            </a>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin: 0.25rem 0;">Belum punya akun?</div>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('register.alumni') }}" class="btn btn-secondary btn-sm" style="flex: 1; font-size: 0.75rem;">
                                    🎓 Daftar Alumni
                                </a>
                                <a href="{{ route('register.subscriber') }}" class="btn btn-secondary btn-sm" style="flex: 1; font-size: 0.75rem;">
                                    👥 Daftar Subscriber
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </main>

        <!-- Sidebar Actions & Related -->
        <aside>
            <!-- Action Card (Bookmark & Share) -->
            <div class="card" style="padding: 1.5rem; margin-bottom: 1.5rem;">
                <div style="font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-bottom: 1rem;">
                    Aksi & Navigasi
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <!-- Tombol Bookmark -->
                    @auth
                        <form method="POST" action="{{ route('member.bookmarks.toggle') }}">
                            @csrf
                            <input type="hidden" name="type" value="library">
                            <input type="hidden" name="id" value="{{ $item->id }}">
                            <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">
                                {{ $isBookmarked ? '★ Hapus dari Bookmark' : '☆ Simpan ke Bookmark' }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;" title="Masuk untuk menyimpan">
                            ☆ Simpan ke Bookmark
                        </a>
                    @endauth

                    <!-- Tombol Share WhatsApp -->
                    @php
                        $shareText = rawurlencode("📖 Intisari AI Menarik: \"{$item->title}\"\n\nBaca selengkapnya di IKMAS AI Learning Center:\n" . url()->current());
                    @endphp
                    <a href="https://wa.me/?text={{ $shareText }}" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-sm" style="width: 100%; justify-content: center;">
                        💬 Bagikan ke WhatsApp
                    </a>
                </div>
            </div>

            <!-- Related Items Card -->
            @if($relatedItems->count() > 0)
            <div class="card" style="padding: 1.5rem;">
                <div style="font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-bottom: 1rem;">
                    Koleksi Terkait
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($relatedItems as $rel)
                    <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.85rem;">
                        <span class="badge {{ $rel->type_badge['class'] }}" style="font-size: 0.65rem; padding: 0.15rem 0.4rem; margin-bottom: 0.35rem;">
                            {{ $rel->type_badge['icon'] }} {{ $rel->type_badge['label'] }}
                        </span>
                        <h4 style="font-size: 0.9rem; font-weight: 700; line-height: 1.3; margin-bottom: 0.35rem;">
                            <a href="{{ route('library.show', $rel->slug) }}" style="color: inherit; text-decoration: none;">
                                {{ $rel->title }}
                            </a>
                        </h4>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                            {{ $rel->category }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </aside>
    </div>
</div>
@endsection
