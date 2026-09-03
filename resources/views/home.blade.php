@extends('layouts.app')

@section('title', 'IKMAS AI Learning Center — Belajar AI. Berbagi. Bertumbuh Bersama.')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-glow"></div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-logo-emblem">
                <div class="hero-logo-frame">
                    <img src="{{ asset('images/ikmas-ai-logo.jpg') }}" alt="Logo Resmi IKMAS AI Learning Center" class="hero-logo-img">
                </div>
            </div>
            
            <h1 class="hero-title">
                Belajar AI. Berbagi. <br>
                <span class="text-gradient">Bertumbuh Bersama.</span>
            </h1>
            
            <p class="hero-description">
                Ruang belajar dan kolaborasi Artificial Intelligence bagi seluruh alumni Assalaam — dari pemula yang belum pernah menyentuh AI hingga praktisi. Belajar tanpa takut tertinggal, berbagi tanpa takut dianggap tidak ahli.
            </p>
            
            <div class="hero-actions">
                <a href="{{ url('/materi') }}" class="btn btn-primary btn-lg">
                    <span>Mulai Belajar Sekarang</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
                
                <a href="{{ url('/prompts') }}" class="btn btn-secondary btn-lg">
                    <span>Jelajahi Prompt Library</span>
                </a>
                
                <a href="https://chat.whatsapp.com/sample-ikmas-ai" target="_blank" rel="noopener" class="btn btn-whatsapp btn-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                    </svg>
                    <span>Gabung Grup WA</span>
                </a>
            </div>
            
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-value">100%</div>
                    <div class="stat-label">Terbuka & Ramah Pemula</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">5 Pilar</div>
                    <div class="stat-label">Kurikulum Pembelajaran</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">70 : 30</div>
                    <div class="stat-label">Rasio Praktik : Konsep</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">1 Wadah</div>
                    <div class="stat-label">Sinergi Alumni Assalaam</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5 Aktivitas Member Terbaru Section -->
<section class="activity-feed-section">
    <div class="container">
        <div class="activity-feed-header">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <span class="activity-pulse-dot"></span>
                <h3 class="activity-feed-title">Denyut Komunitas Terkini</h3>
            </div>
            <span class="activity-feed-sub">Aktivitas & karya nyata alumni</span>
        </div>

        @if(isset($recentActivity) && $recentActivity->count() > 0)
            <div class="activity-grid">
                @foreach($recentActivity as $act)
                    <a href="{{ $act['url'] }}" class="activity-card">
                        <div class="activity-card-top">
                            <span class="badge {{ $act['badge_class'] }}">{{ $act['badge_label'] }}</span>
                            <span class="activity-time">
                                {{ $act['created_at'] ? \Carbon\Carbon::parse($act['created_at'])->diffForHumans() : 'Baru saja' }}
                            </span>
                        </div>
                        <div class="activity-card-body">
                            <div class="activity-card-title">{{ $act['title'] }}</div>
                            <div class="activity-card-subtitle">{{ $act['subtitle'] }}</div>
                        </div>
                        <div class="activity-card-arrow">
                            <span>Lihat</span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="activity-empty-state">
                <div class="activity-empty-icon">🌱</div>
                <div class="activity-empty-text">
                    <strong>Komunitas baru mulai bertumbuh!</strong> Jadilah salah satu yang pertama membagikan karya AI atau bergabung di Study Group.
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Next Upcoming Event Announcement -->
@if(isset($nextEvent))
<div class="container" style="margin-top: -1.5rem; margin-bottom: 2rem;">
    <div class="card card-elevated" style="border-left: 4px solid var(--accent-emerald); padding: 1.25rem 1.75rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.25rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <span class="badge badge-emerald">Jadwal Terdekat</span>
            <div>
                <a href="{{ url('/agenda/' . $nextEvent->slug) }}" style="font-weight: 700; font-size: 1.05rem; color: var(--text-main); display: block;">
                    {{ $nextEvent->title }}
                </a>
                <span style="font-size: 0.85rem; color: var(--text-muted);">
                    📅 {{ $nextEvent->formatted_date }} &bull; 🎙 {{ $nextEvent->speaker_name }}
                </span>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <a href="{{ url('/agenda/' . $nextEvent->slug) }}" class="btn btn-secondary btn-sm">
                Lihat Detail
            </a>
            @if($nextEvent->location_url)
                <a href="{{ $nextEvent->location_url }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm">
                    Ikuti Sesi ↗
                </a>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Garuda Onboarding Section -->
<section class="container" id="komunitas-garuda">
    <div class="garuda-box">
        <div class="garuda-header">
            <div class="garuda-avatar">
                🦅
            </div>
            <div>
                <h3 class="garuda-title">Garuda</h3>
                <div class="garuda-role">Agen Informasi IKMAS AI Learning Center</div>
            </div>
        </div>
        
        <p class="garuda-quote">
            "Halo kawan alumni Assalaam! 👋 Saya <strong>Garuda</strong>, teman informasi dan fasilitator belajarmu di sini. Kamu tidak harus menjadi programmer atau ahli teknologi untuk mulai memanfaatkan AI. AI diciptakan untuk meringankan pekerjaan kita, bukan membuat kita pusing. Mulai dari langkah kecil, coba langsung, dan kita bertumbuh bareng-bareng!"
        </p>
        
        <div class="garuda-steps">
            <div class="step-card">
                <div>
                    <div class="step-number">Langkah 01</div>
                    <h4 class="step-title">Pahami Dasarnya</h4>
                    <p class="step-desc">Pelajari modul perdana Study Group: cara memberi instruksi yang tepat tanpa istilah yang rumit.</p>
                </div>
                <a href="{{ url('/materi') }}" class="btn btn-secondary btn-sm" style="width: 100%;">Baca Panduan Pemula →</a>
            </div>
            
            <div class="step-card">
                <div>
                    <div class="step-number">Langkah 02</div>
                    <h4 class="step-title">Gunakan Prompt Siap Pakai</h4>
                    <p class="step-desc">Temukan ratusan prompt produktivitas siap salin untuk draf email, riset, ringkasan, atau analisis dokumen.</p>
                </div>
                <a href="{{ url('/prompts') }}" class="btn btn-secondary btn-sm" style="width: 100%;">Buka Prompt Library →</a>
            </div>
            
            <div class="step-card">
                <div>
                    <div class="step-number">Langkah 03</div>
                    <h4 class="step-title">Ngobrol di WhatsApp</h4>
                    <p class="step-desc">Bergabung ke WhatsApp Community untuk tanya jawab santai, jadwal Study Group mingguan, dan berbagi tips.</p>
                </div>
                <a href="https://chat.whatsapp.com/sample-ikmas-ai" target="_blank" rel="noopener" class="btn btn-whatsapp btn-sm" style="width: 100%;">Masuk Grup WA →</a>
            </div>
        </div>
    </div>
<!-- Latest Learning Materials Section -->
@if(isset($latestMaterials) && $latestMaterials->count() > 0)
<section class="section" style="padding-top: 2rem;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span class="badge badge-primary" style="margin-bottom: 0.5rem;">Study Group & Modul</span>
                <h2 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.02em;">Materi Belajar Terbaru</h2>
            </div>
            <a href="{{ url('/materi') }}" class="btn btn-secondary btn-sm">
                Lihat Semua Materi ({{ \App\Models\LearningMaterial::where('is_published', true)->count() }}) →
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
            @foreach($latestMaterials as $mat)
                <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                            <span class="badge badge-{{ $mat->level_color }}">
                                {{ $mat->level_label }}
                            </span>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">
                                {{ $mat->reading_minutes }} Menit Baca
                            </span>
                        </div>
                        <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem; line-height: 1.35;">
                            <a href="{{ url('/materi/' . $mat->slug) }}" style="color: var(--text-main);">
                                {{ $mat->title }}
                            </a>
                        </h3>
                        <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1rem;">
                            {{ Str::limit($mat->summary, 110) }}
                        </p>
                    </div>
                    <div style="border-top: 1px solid var(--border-color); padding-top: 0.875rem; display: flex; justify-content: space-between; align-items: center;">
                        <span class="badge badge-cyan" style="font-size: 0.7rem;">
                            {{ $mat->pillar_label }}
                        </span>
                        <a href="{{ url('/materi/' . $mat->slug) }}" class="btn btn-primary btn-sm">
                            Baca Modul →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Featured Prompts Section -->
@if(isset($featuredPrompts) && $featuredPrompts->count() > 0)
<section class="section" style="padding-top: 1rem; padding-bottom: 2rem;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span class="badge badge-cyan" style="margin-bottom: 0.5rem;">Bank Instruksi 1-Click</span>
                <h2 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.02em;">Prompt Produktivitas Populer</h2>
            </div>
            <a href="{{ url('/prompts') }}" class="btn btn-secondary btn-sm">
                Lihat Semua Prompt ({{ \App\Models\Prompt::count() }}) →
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.5rem;">
            @foreach($featuredPrompts as $prompt)
                <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                            <span class="badge badge-primary">{{ $prompt->target_role }}</span>
                            <span class="badge badge-cyan" style="font-size: 0.7rem;">🛠 {{ $prompt->target_tool }}</span>
                        </div>
                        <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.75rem; line-height: 1.35;">
                            {{ $prompt->title }}
                        </h3>
                        <div style="background: var(--bg-surface-alt); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.875rem; margin-bottom: 0.875rem; font-family: monospace; font-size: 0.825rem; line-height: 1.5; color: var(--text-muted); max-height: 110px; overflow: hidden; position: relative;">
                            {{ Str::limit($prompt->prompt_text, 140) }}
                        </div>
                    </div>
                    <div style="border-top: 1px solid var(--border-color); padding-top: 0.875rem; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.75rem; color: var(--text-muted);">Disalin {{ $prompt->copy_count }}x</span>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="copyPrompt(this, `{{ addslashes($prompt->prompt_text) }}`)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                            <span>Salin Prompt</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- 5 Learning Pillars Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">
                <span class="badge badge-cyan">Kurikulum Terarah</span>
            </div>
            <h2 class="section-title">5 Pilar Pembelajaran IKMAS AI</h2>
            <p class="section-desc">
                Struktur belajar yang dirancang bertahap untuk membawa alumni dari tingkat <em>Beginner</em>, <em>Explorer</em>, hingga <em>Practitioner</em> mandiri.
            </p>
        </div>
        
        <div class="pillars-grid">
            <div class="card pillar-card">
                <div class="pillar-icon" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                    💡
                </div>
                <h3 class="pillar-title">AI Basics</h3>
                <p class="pillar-desc">
                    Memahami logika dan cara kerja Artificial Intelligence tanpa jargon matematis. Menghilangkan rasa takut dan membangun rasa percaya diri dalam berinteraksi dengan AI.
                </p>
            </div>
            
            <div class="card pillar-card">
                <div class="pillar-icon" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                    🛠
                </div>
                <h3 class="pillar-title">AI Tools</h3>
                <p class="pillar-desc">
                    Panduan praktis menggunakan berbagai alat AI terkini: ChatGPT, Claude, Midjourney, Canva Magic, Cursor, v0, dan platform produktivitas lainnya.
                </p>
            </div>
            
            <div class="card pillar-card">
                <div class="pillar-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    ⚡
                </div>
                <h3 class="pillar-title">AI Productivity</h3>
                <p class="pillar-desc">
                    Menerapkan AI untuk mempercepat pekerjaan harian: penulisan laporan, riset pasar, draf komunikasi bisnis, hingga analisis lembar kerja dan presentasi.
                </p>
            </div>
            
            <div class="card pillar-card">
                <div class="pillar-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    🔄
                </div>
                <h3 class="pillar-title">AI Workflow</h3>
                <p class="pillar-desc">
                    Mengintegrasikan AI ke dalam alur kerja yang otomatis dan berulang. Dari sekadar mengetik prompt biasa menjadi sistem kerja terstruktur dan efisien.
                </p>
            </div>
            
            <div class="card pillar-card">
                <div class="pillar-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                    🚀
                </div>
                <h3 class="pillar-title">AI for Opportunity</h3>
                <p class="pillar-desc">
                    Menghubungkan keahlian AI dengan dampak nyata: membuka peluang usaha baru, efisiensi bisnis alumni, hingga monetisasi karya dan layanan digital.
                </p>
            </div>
            
            <div class="card pillar-card" style="background: linear-gradient(135deg, rgba(37,99,235,0.05) 0%, rgba(2,132,199,0.08) 100%); border-style: dashed;">
                <div class="pillar-icon" style="background: rgba(30, 58, 138, 0.1); color: #1e3a8a;">
                    🌱
                </div>
                <h3 class="pillar-title">Filosofi Bertumbuh</h3>
                <p class="pillar-desc">
                    <strong>Learn → Share → Practice → Collaborate → Grow</strong>.<br>
                    Tujuan kami bukan mencetak ahli teori, melainkan membantu alumni berdaya dan adaptif terhadap masa depan.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Why Section (Simon Sinek) -->
<section class="container" style="margin-bottom: 4rem;">
    <div class="why-card">
        <p class="why-quote">
            "Saya percaya bahwa setiap alumni Assalaam seharusnya bisa beradaptasi dengan terus belajar, membuka diri, dan meningkatkan skill — karena tanpa itu, akhirnya mereka tertinggal, tidak adaptif, serta menutup peluang untuk hidup lebih baik."
        </p>
        <div class="why-author">
            Why Statement — IKMAS AI Learning Center
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="container" style="margin-bottom: 5rem;">
    <div class="cta-banner">
        <span class="badge badge-primary" style="margin-bottom: 1rem;">Mulai Sekarang</span>
        <h2 class="cta-title">Siap Bertumbuh Bersama Komunitas?</h2>
        <p class="cta-desc">
            Pintu selalu terbuka untuk seluruh alumni Assalaam dari semua angkatan. Mari belajar bareng, saling dukung, dan menghasilkan karya nyata.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="https://chat.whatsapp.com/sample-ikmas-ai" target="_blank" rel="noopener" class="btn btn-whatsapp btn-lg">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                </svg>
                <span>Gabung Komunitas WhatsApp</span>
            </a>
            <a href="{{ url('/register') }}" class="btn btn-primary btn-lg">
                <span>Daftar Akun Alumni</span>
            </a>
        </div>
    </div>
</section>
@endsection
