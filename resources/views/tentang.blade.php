@extends('layouts.app')

@section('title', 'Tentang Kami — IKMAS AI Learning Center')

@section('content')

{{-- ── HERO ──────────────────────────────────────────────────────────────── --}}
<section class="tentang-hero">
    <div class="container">
        <div class="tentang-hero-inner">
            <div class="tentang-hero-badge">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                Komunitas Alumni Assalaam
            </div>
            <h1 class="tentang-hero-title">Tentang <span class="text-gradient">IKMAS AI</span><br>Learning Center</h1>
            <p class="tentang-hero-desc">{{ $content['intro_desc'] ?? 'IKMAS AI Learning Center adalah ruang belajar dan kolaborasi AI bagi alumni Assalaam.' }}</p>
        </div>
    </div>
</section>

{{-- ── STRUKTUR ORGANISASI ───────────────────────────────────────────────── --}}
<section class="tentang-section">
    <div class="container">

        <div class="tentang-section-header">
            <h2 class="tentang-section-title">Struktur Organisasi</h2>
            @auth
                <span class="badge badge-emerald tentang-member-badge">✓ Member</span>
            @endauth
        </div>
        <p class="tentang-section-sub">Peta kepengurusan komunitas — diisi satu per satu sesuai kebutuhan nyata, bukan preventif.</p>

        {{-- Community Lead — full width --}}
        <div class="tentang-lead-card">
            <div class="tentang-lead-header">
                <div class="tentang-role-icon tentang-role-icon--lead">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <div>
                    <h3 class="tentang-role-name">Community Lead</h3>
                    <p class="tentang-role-tagline">{{ $content['community_lead_tagline'] ?? '' }}</p>
                </div>
            </div>
            <p class="tentang-role-desc">{{ $content['community_lead_description'] ?? '' }}</p>

            @guest
                <div class="content-locked">
                    <div class="tentang-responsibilities-placeholder">
                        <p class="tentang-resp-preview">Tanggung jawab utama: menjaga Why tetap hidup di setiap keputusan, mengambil keputusan akhir saat ada perbedaan prioritas, menjadi penghubung ke PP IKMAS dan alumni network...</p>
                    </div>
                    <div class="content-locked-cta">
                        <a href="{{ url('/login') }}" class="btn btn-primary btn-sm">Masuk untuk baca selengkapnya</a>
                        <span>atau</span>
                        <a href="{{ url('/register') }}" class="btn-ghost-nav">Daftar Alumni</a>
                    </div>
                </div>
            @else
                <div class="tentang-detail">
                    <h4 class="tentang-detail-label">Tanggung Jawab Utama</h4>
                    <div class="tentang-resp-list">{!! nl2br(e($content['community_lead_responsibilities'] ?? '')) !!}</div>
                    @if(!empty($content['community_lead_note']))
                        <div class="tentang-note">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            <span>{{ $content['community_lead_note'] }}</span>
                        </div>
                    @endif
                </div>
            @endguest
        </div>

        {{-- 4 Koordinator — grid 2×2 --}}
        @php
            $koordinator = [
                ['prefix' => 'program_coordinator',  'label' => 'Program Coordinator',  'icon' => '<path d="M8 6h13"></path><path d="M8 12h13"></path><path d="M8 18h13"></path><path d="M3 6h.01"></path><path d="M3 12h.01"></path><path d="M3 18h.01"></path>'],
                ['prefix' => 'content_coordinator',  'label' => 'Content Coordinator',  'icon' => '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>'],
                ['prefix' => 'community_moderator',  'label' => 'Community Moderator',  'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>'],
                ['prefix' => 'technical_support',    'label' => 'Technical / AI Support','icon' => '<polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline>'],
            ];
        @endphp

        <div class="tentang-org-grid">
            @foreach($koordinator as $role)
                <div class="tentang-role-card">
                    <div class="tentang-role-card-header">
                        <div class="tentang-role-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $role['icon'] !!}</svg>
                        </div>
                        <div>
                            <h3 class="tentang-role-name">{{ $role['label'] }}</h3>
                            <p class="tentang-role-tagline">{{ $content[$role['prefix'].'_tagline'] ?? '' }}</p>
                        </div>
                    </div>
                    <p class="tentang-role-desc">{{ $content[$role['prefix'].'_description'] ?? '' }}</p>

                    @guest
                        <div class="content-locked content-locked--sm">
                            <p class="tentang-resp-preview-sm">Tanggung jawab detail + kapan peran ini mulai didelegasikan...</p>
                            <div class="content-locked-cta content-locked-cta--sm">
                                <a href="{{ url('/login') }}" class="btn btn-primary btn-sm">Masuk untuk baca selengkapnya</a>
                            </div>
                        </div>
                    @else
                        <div class="tentang-detail">
                            <h4 class="tentang-detail-label">Tanggung Jawab Utama</h4>
                            <div class="tentang-resp-list">{!! nl2br(e($content[$role['prefix'].'_responsibilities'] ?? '')) !!}</div>
                            @if(!empty($content[$role['prefix'].'_delegate']))
                                <div class="tentang-note">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                    <span>{{ $content[$role['prefix'].'_delegate'] }}</span>
                                </div>
                            @endif
                        </div>
                    @endguest
                </div>
            @endforeach
        </div>

        {{-- Volunteer Roles — tabel, visible untuk semua --}}
        <div class="tentang-volunteer-section">
            <h3 class="tentang-subsection-title">Volunteer Roles <span class="tentang-subsection-sub">(Lapisan Kedua — muncul organik dari anggota paling aktif)</span></h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Peran</th>
                            <th>Deskripsi</th>
                            <th>Kapan Mulai Relevan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><strong>Facilitator</strong></td><td>Memimpin Study Group</td><td>Bulan 2 — dari member paling aktif di Study Group #1</td></tr>
                        <tr><td><strong>Mentor</strong></td><td>Membantu anggota lain</td><td>Bulan 3+ — setelah ada member cukup mahir</td></tr>
                        <tr><td><strong>Content Contributor</strong></td><td>Membuat konten</td><td>Bulan 2–3 — dokumentasi & cerita AI for Opportunity</td></tr>
                        <tr><td><strong>Project Lead</strong></td><td>Memimpin project</td><td>Setelah loop komunitas stabil (>3 bulan)</td></tr>
                        <tr><td><strong>Community Ambassador</strong></td><td>Memperkenalkan komunitas ke alumni lain</td><td>Kapan saja — tidak butuh skill AI tinggi</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

{{-- ── RENCANA AKSI ─────────────────────────────────────────────────────── --}}
<section class="tentang-section tentang-section--alt">
    <div class="container">

        <div class="tentang-section-header">
            <h2 class="tentang-section-title">Rencana Aksi</h2>
            @auth
                <span class="badge badge-emerald tentang-member-badge">✓ Member</span>
            @endauth
        </div>

        {{-- Why Statement --}}
        <blockquote class="tentang-why">
            <p class="tentang-why-text">{{ $content['why_statement'] ?? '' }}</p>
            <footer class="tentang-why-attr">{{ $content['why_attribution'] ?? '' }}</footer>
        </blockquote>

        <p class="tentang-section-sub" style="margin-bottom: 2.5rem;">{{ $content['month_intro'] ?? '' }}</p>

        {{-- Vertical Timeline --}}
        <div class="tentang-timeline">

            {{-- Minggu Pertama --}}
            <div class="timeline-group">
                <div class="timeline-group-label">
                    <span class="timeline-group-dot"></span>
                    <h3 class="timeline-group-title">{{ $content['week1_title'] ?? 'Minggu Pertama' }}</h3>
                </div>
                <p class="timeline-group-intro">{{ $content['week1_intro'] ?? '' }}</p>

                @php
                    $weekNodes = [
                        ['key' => 'week1_h7',           'label' => 'H-7'],
                        ['key' => 'week1_h6h5_materi',  'label' => 'H-6 s.d. H-5'],
                        ['key' => 'week1_h5_umumkan',   'label' => 'H-5'],
                        ['key' => 'week1_h4h1_momentum','label' => 'H-4 s.d. H-1'],
                        ['key' => 'week1_h1_reminder',  'label' => 'H-1 & Pagi Hari-H'],
                        ['key' => 'week1_hari_h',       'label' => 'Hari-H'],
                        ['key' => 'week1_followup',     'label' => '24 Jam Setelah Sesi'],
                    ];
                @endphp

                <div class="timeline-nodes">
                    @foreach($weekNodes as $node)
                        <div class="timeline-node">
                            <div class="timeline-node-dot"></div>
                            <div class="timeline-node-content">
                                <span class="timeline-label">{{ $node['label'] }}</span>
                                @guest
                                    <p class="timeline-node-text timeline-node-text--truncate">{{ Str::limit($content[$node['key']] ?? '', 80) }}...</p>
                                @else
                                    <p class="timeline-node-text">{{ $content[$node['key']] ?? '' }}</p>
                                @endguest
                            </div>
                        </div>
                    @endforeach
                </div>

                @guest
                    <div class="timeline-lock-cta">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        <span>Detail lengkap tiap langkah hanya untuk member —</span>
                        <a href="{{ url('/login') }}" style="color: var(--primary); font-weight: 600;">Masuk</a>
                        <span>atau</span>
                        <a href="{{ url('/register') }}" style="color: var(--primary); font-weight: 600;">Daftar</a>
                    </div>
                @endguest
            </div>

            {{-- 3 Bulan --}}
            @php
                $months = [
                    ['title_key' => 'month1_title', 'detail_key' => 'month1_detail', 'color' => 'emerald'],
                    ['title_key' => 'month2_title', 'detail_key' => 'month2_detail', 'color' => 'amber'],
                    ['title_key' => 'month3_title', 'detail_key' => 'month3_detail', 'color' => 'primary'],
                ];
            @endphp

            @foreach($months as $i => $month)
                <div class="timeline-group">
                    <div class="timeline-group-label">
                        <span class="timeline-group-dot timeline-group-dot--{{ $month['color'] }}"></span>
                        <h3 class="timeline-group-title">{{ $content[$month['title_key']] ?? 'Bulan '.($i+1) }}</h3>
                    </div>

                    @guest
                        <div class="timeline-month-locked">
                            <p class="timeline-month-locked-text">Detail rencana per minggu untuk fase ini hanya tersedia untuk member.</p>
                            <a href="{{ url('/login') }}" class="btn btn-primary btn-sm">Masuk untuk baca selengkapnya</a>
                        </div>
                    @else
                        <div class="timeline-month-detail">
                            {!! nl2br(e($content[$month['detail_key']] ?? '')) !!}
                        </div>
                    @endguest
                </div>
            @endforeach

        </div>{{-- end .tentang-timeline --}}

        {{-- Kutipan Penutup --}}
        <div class="tentang-closing-quote">
            <p>{{ $content['closing_quote'] ?? '' }}</p>
        </div>

    </div>
</section>

{{-- ── CTA ───────────────────────────────────────────────────────────────── --}}
<section class="tentang-cta-section">
    <div class="container">
        <div class="tentang-cta-inner">
            <h2 class="tentang-cta-title">Siap bergabung?</h2>
            <p class="tentang-cta-sub">Belajar AI bersama alumni Assalaam — dari pemula hingga praktisi.</p>
            <div class="tentang-cta-actions">
                @auth
                    <a href="{{ url('/member/dashboard') }}" class="btn btn-primary">Buka Area Member</a>
                @else
                    <a href="{{ url('/register') }}" class="btn btn-primary">Daftar Alumni</a>
                    <a href="https://forms.gle/UhyTLF7DyPNAZuir6" target="_blank" rel="noopener" class="btn btn-secondary">
                        Gabung via Form
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>

@endsection
