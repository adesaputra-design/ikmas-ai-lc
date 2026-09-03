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
            
            <nav>
                <ul class="nav-links" id="nav-links">
                    <li><a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Beranda</a></li>
                    <li><a href="{{ url('/materi') }}" class="nav-link {{ request()->is('materi*') ? 'active' : '' }}">Materi</a></li>
                    <li><a href="{{ url('/prompts') }}" class="nav-link {{ request()->is('prompts*') ? 'active' : '' }}">Prompts</a></li>
                    <li><a href="{{ url('/showcase') }}" class="nav-link {{ request()->is('showcase*') ? 'active' : '' }}">Showcase</a></li>
                    <li><a href="{{ url('/agenda') }}" class="nav-link {{ request()->is('agenda*') ? 'active' : '' }}">Agenda</a></li>
                    <li><a href="{{ url('/#komunitas-garuda') }}" class="nav-link">Komunitas</a></li>

                    <!-- Mobile Drawer Footer Actions (Mobile Only) -->
                    <li class="mobile-only mobile-drawer-footer">
                        <a href="https://m.ikmas.com/" target="_blank" rel="noopener" class="mobile-portal-card" title="Kunjungi Portal Pusat Ikatan Alumni Ma'had Assalaam">
                            <span>🌐 Portal Pusat IKMAS (m.ikmas.com)</span>
                            <span>↗</span>
                        </a>
                        
                        <div class="mobile-auth-cluster">
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
                <button type="button" class="mobile-menu-btn mobile-only" id="mobile-menu-btn" aria-label="Buka Menu">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
    </header>

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
