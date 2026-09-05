<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="IKMAS AI Learning Center — Ruang belajar dan kolaborasi Artificial Intelligence bagi alumni Assalaam. Belajar AI. Berbagi. Bertumbuh Bersama.">
    <meta name="keywords" content="IKMAS AI, AI Learning Center, Alumni Assalaam, Belajar AI, Prompt Library, Study Group AI">
    <title>@yield('title', 'IKMAS AI Learning Center — Belajar AI. Berbagi. Bertumbuh Bersama.')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232563EB'><path d='M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5'/></svg>">
    
    <!-- Design System CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <div class="container nav-container">
            <a href="{{ url('/') }}" class="nav-brand">
                <img src="{{ asset('images/ikmas-logo.png') }}" alt="Logo Resmi IKMAS" class="brand-logo-img">
                <div class="brand-divider"></div>
                <div class="brand-badge">
                    <span class="brand-badge-title"><span class="text-gradient">AI</span> Learning Center</span>
                    <span class="brand-badge-sub">Ekosistem Alumni Assalaam</span>
                </div>
            </a>
            
            <!-- Desktop Menu (Dropdowns) -->
            <nav class="desktop-nav desktop-only">
                <ul class="nav-links">
                    <!-- Dropdown Belajar -->
                    <li class="nav-dropdown" data-dropdown="belajar">
                        <button type="button" class="nav-link nav-dropdown-btn {{ request()->is('materi*') || request()->is('prompts*') || request()->is('library*') ? 'active' : '' }}" aria-expanded="false">
                            <span>Belajar</span>
                            <svg class="dropdown-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div class="nav-dropdown-menu">
                            <a href="{{ url('/materi') }}" class="dropdown-item {{ request()->is('materi*') ? 'active' : '' }}">
                                <div class="dropdown-item-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                </div>
                                <div class="dropdown-item-text">
                                    <span class="dropdown-item-title">Materi Belajar</span>
                                    <span class="dropdown-item-desc">Kurikulum & modul AI terstruktur</span>
                                </div>
                            </a>
                            <a href="{{ url('/prompts') }}" class="dropdown-item {{ request()->is('prompts*') ? 'active' : '' }}">
                                <div class="dropdown-item-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                </div>
                                <div class="dropdown-item-text">
                                    <span class="dropdown-item-title">Prompt Library</span>
                                    <span class="dropdown-item-desc">Koleksi prompt AI teruji alumni</span>
                                </div>
                            </a>
                            <a href="{{ url('/library') }}" class="dropdown-item {{ request()->is('library*') ? 'active' : '' }}">
                                <div class="dropdown-item-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                                </div>
                                <div class="dropdown-item-text">
                                    <span class="dropdown-item-title">Pustaka AI</span>
                                    <span class="dropdown-item-desc">Buku, podcast & riset alumni</span>
                                </div>
                            </a>
                        </div>
                    </li>

                    <!-- Dropdown Komunitas -->
                    <li class="nav-dropdown" data-dropdown="komunitas">
                        <button type="button" class="nav-link nav-dropdown-btn {{ request()->is('showcase*') || request()->is('agenda*') || request()->is('tentang*') ? 'active' : '' }}" aria-expanded="false">
                            <span>Komunitas</span>
                            <svg class="dropdown-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div class="nav-dropdown-menu">
                            <a href="{{ url('/showcase') }}" class="dropdown-item {{ request()->is('showcase*') ? 'active' : '' }}">
                                <div class="dropdown-item-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                                </div>
                                <div class="dropdown-item-text">
                                    <span class="dropdown-item-title">Showcase Karya</span>
                                    <span class="dropdown-item-desc">Eksperimen & implementasi alumni</span>
                                </div>
                            </a>
                            <a href="{{ url('/agenda') }}" class="dropdown-item {{ request()->is('agenda*') ? 'active' : '' }}">
                                <div class="dropdown-item-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                </div>
                                <div class="dropdown-item-text">
                                    <span class="dropdown-item-title">Agenda & Event</span>
                                    <span class="dropdown-item-desc">Study Group, webinar & sharing</span>
                                </div>
                            </a>
                            <a href="{{ url('/tentang') }}" class="dropdown-item {{ request()->is('tentang*') ? 'active' : '' }}">
                                <div class="dropdown-item-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                </div>
                                <div class="dropdown-item-text">
                                    <span class="dropdown-item-title">Tentang Kami</span>
                                    <span class="dropdown-item-desc">Struktur organisasi & rencana aksi</span>
                                </div>
                            </a>
                            <a href="{{ url('/#komunitas-garuda') }}" class="dropdown-item">
                                <div class="dropdown-item-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                </div>
                                <div class="dropdown-item-text">
                                    <span class="dropdown-item-title">Komunitas Garuda</span>
                                    <span class="dropdown-item-desc">Wadah kolaborasi alumni Assalaam</span>
                                </div>
                            </a>
                            <a href="https://chat.whatsapp.com/sample-ikmas-ai" target="_blank" rel="noopener" class="dropdown-item dropdown-item-external">
                                <div class="dropdown-item-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                </div>
                                <div class="dropdown-item-text">
                                    <span class="dropdown-item-title">Grup WhatsApp ↗</span>
                                    <span class="dropdown-item-desc">Diskusi harian & tanya jawab cepat</span>
                                </div>
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            
            <div class="nav-actions">
                <!-- Dual Theme Switcher (Always accessible) -->
                <button type="button" class="theme-toggle-btn" id="theme-toggle-btn" title="Ganti Tema (Light/Dark)">
                    <span id="theme-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                        </svg>
                    </span>
                </button>

                <!-- Link Portal Pusat IKMAS (Desktop Only) -->
                <a href="https://m.ikmas.com/" target="_blank" rel="noopener" class="btn-portal-pill desktop-only" title="Kunjungi Portal Pusat Ikatan Alumni Ma'had Assalaam">
                    <span>Portal IKMAS</span>
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                </a>
                
                <!-- Authentication Actions (Desktop Only) -->
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ url('/admin/dashboard') }}" class="btn btn-secondary btn-sm desktop-only" style="border-radius: var(--radius-full);">Panel Admin</a>
                    @else
                        <a href="{{ url('/member/dashboard') }}" class="btn btn-secondary btn-sm desktop-only" style="border-radius: var(--radius-full);">Area Member</a>
                    @endif
                    <form action="{{ url('/logout') }}" method="POST" class="desktop-only" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-ghost-nav" style="background: transparent; border: none; cursor: pointer;">Keluar</button>
                    </form>
                @else
                    <a href="{{ url('/login') }}" class="btn-ghost-nav desktop-only">Masuk</a>
                    <a href="{{ url('/register') }}" class="btn btn-primary btn-sm desktop-only" style="border-radius: var(--radius-full); padding: 0.45rem 1.15rem; font-size: 0.85rem;">Daftar Alumni</a>
                @endauth
                
                <!-- Mobile Menu Hamburger Button (Mobile Only) -->
                <button type="button" class="mobile-menu-btn mobile-only" id="mobile-menu-btn" aria-label="Buka Menu" aria-expanded="false">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Offcanvas Drawer (m.ikmas.com Style - Slide from Right) -->
    <div class="offcanvas-backdrop" id="offcanvasBackdrop"></div>
    <aside class="ikmas-offcanvas" id="mobileOffcanvas" aria-label="Menu Navigasi Mobile">
        <div class="offcanvas-header">
            <div class="offcanvas-brand">
                <img src="{{ asset('images/ikmas-logo.png') }}" alt="Logo IKMAS" class="offcanvas-logo">
                <div>
                    <div class="offcanvas-brand-title">IKMAS AI LC</div>
                    <div class="offcanvas-brand-sub">Ekosistem Alumni</div>
                </div>
            </div>
            <button type="button" class="offcanvas-close-btn" id="offcanvasCloseBtn" aria-label="Tutup Menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <div class="offcanvas-body">
            <ul class="offcanvas-nav-list">
                <!-- Beranda -->
                <li>
                    <a href="{{ url('/') }}" class="offcanvas-nav-item {{ request()->is('/') ? 'active' : '' }}">
                        <span class="offcanvas-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        </span>
                        <span>Beranda</span>
                    </a>
                </li>

                <!-- Section Header: BELAJAR -->
                <li class="offcanvas-section-header">BELAJAR</li>
                <li>
                    <a href="{{ url('/materi') }}" class="offcanvas-nav-item {{ request()->is('materi*') ? 'active' : '' }}">
                        <span class="offcanvas-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                        </span>
                        <span>Materi Belajar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('/prompts') }}" class="offcanvas-nav-item {{ request()->is('prompts*') ? 'active' : '' }}">
                        <span class="offcanvas-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                        </span>
                        <span>Prompt Library</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('/library') }}" class="offcanvas-nav-item {{ request()->is('library*') ? 'active' : '' }}">
                        <span class="offcanvas-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                        </span>
                        <span>Pustaka AI</span>
                    </a>
                </li>

                <!-- Section Header: KOMUNITAS -->
                <li class="offcanvas-section-header">KOMUNITAS</li>
                <li>
                    <a href="{{ url('/showcase') }}" class="offcanvas-nav-item {{ request()->is('showcase*') ? 'active' : '' }}">
                        <span class="offcanvas-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                        </span>
                        <span>Showcase Karya</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('/agenda') }}" class="offcanvas-nav-item {{ request()->is('agenda*') ? 'active' : '' }}">
                        <span class="offcanvas-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </span>
                        <span>Agenda & Event</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('/tentang') }}" class="offcanvas-nav-item {{ request()->is('tentang*') ? 'active' : '' }}">
                        <span class="offcanvas-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        </span>
                        <span>Tentang Kami</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('/#komunitas-garuda') }}" class="offcanvas-nav-item">
                        <span class="offcanvas-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </span>
                        <span>Komunitas Garuda</span>
                    </a>
                </li>
                <li>
                    <a href="https://chat.whatsapp.com/sample-ikmas-ai" target="_blank" rel="noopener" class="offcanvas-nav-item offcanvas-nav-item-whatsapp">
                        <span class="offcanvas-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                        </span>
                        <span>WhatsApp Community</span>
                        <span class="offcanvas-badge-ext">↗</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="offcanvas-footer">
            <a href="https://m.ikmas.com/" target="_blank" rel="noopener" class="offcanvas-portal-btn">
                <span>🌐 Portal Pusat m.ikmas.com</span>
                <span>↗</span>
            </a>

            <div class="offcanvas-auth">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ url('/admin/dashboard') }}" class="btn btn-primary btn-sm" style="text-align: center;">Panel Admin</a>
                    @else
                        <a href="{{ url('/member/dashboard') }}" class="btn btn-primary btn-sm" style="text-align: center;">Area Member</a>
                    @endif
                    <form action="{{ url('/logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%; text-align: center;">Keluar</button>
                    </form>
                @else
                    <a href="{{ url('/login') }}" class="btn btn-secondary btn-sm" style="text-align: center;">Masuk</a>
                    <a href="{{ url('/register') }}" class="btn btn-primary btn-sm" style="text-align: center;">Daftar</a>
                @endauth
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <a href="https://m.ikmas.com/" target="_blank" rel="noopener" style="display: inline-block; margin-bottom: 1rem;" title="Kunjungi Portal Pusat IKMAS">
                        <img src="{{ asset('images/ikmas-logo.png') }}" alt="Logo Resmi IKMAS" style="height: 3rem; width: auto; object-fit: contain;">
                    </a>
                    <p class="footer-brand-desc">
                        Ruang belajar dan kolaborasi Artificial Intelligence resmi bagi alumni Assalaam. Wadah inovasi alumni dari pemula hingga praktisi global.
                    </p>
                    <div style="margin-top: 0.85rem;">
                        <a href="https://m.ikmas.com/" target="_blank" rel="noopener" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.85rem; color: var(--primary); font-weight: 700;">
                            Portal Pusat: m.ikmas.com ↗
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="footer-heading">Navigasi</h4>
                    <ul class="footer-links">
                        <li><a href="{{ url('/') }}" class="footer-link">Beranda</a></li>
                        <li><a href="{{ url('/materi') }}" class="footer-link">Materi Belajar</a></li>
                        <li><a href="{{ url('/prompts') }}" class="footer-link">Prompt Library</a></li>
                        <li><a href="{{ url('/showcase') }}" class="footer-link">Showcase Karya</a></li>
                        <li><a href="{{ url('/agenda') }}" class="footer-link">Kalender Event</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="footer-heading">Pilar Belajar</h4>
                    <ul class="footer-links">
                        <li><a href="{{ url('/materi?pillar=basics') }}" class="footer-link">AI Basics</a></li>
                        <li><a href="{{ url('/materi?pillar=tools') }}" class="footer-link">AI Tools</a></li>
                        <li><a href="{{ url('/materi?pillar=productivity') }}" class="footer-link">AI Productivity</a></li>
                        <li><a href="{{ url('/materi?pillar=workflow') }}" class="footer-link">AI Workflow</a></li>
                        <li><a href="{{ url('/materi?pillar=opportunity') }}" class="footer-link">AI for Opportunity</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="footer-heading">Komunitas</h4>
                    <ul class="footer-links">
                        <li><a href="https://chat.whatsapp.com/sample-ikmas-ai" target="_blank" rel="noopener" class="footer-link">WhatsApp Community</a></li>
                        <li><a href="{{ url('/login') }}" class="footer-link">Portal Anggota</a></li>
                        <li><a href="{{ url('/register') }}" class="footer-link">Daftar Alumni</a></li>
                        <li><a href="{{ url('/#komunitas-garuda') }}" class="footer-link">Agen Garuda</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div>
                    &copy; {{ date('Y') }} IKMAS AI Learning Center. Inisiatif Komunitas Alumni Assalaam.
                </div>
                <div>
                    Dibangun dengan semangat <span style="color: #ef4444;">❤</span> untuk kemajuan bersama.
                </div>
            </div>
        </div>
    </footer>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="toast-container"></div>

    <!-- Master Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
