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
                <div class="brand-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5"></path>
                        <path d="M2 12l10 5 10-5"></path>
                    </svg>
                </div>
                <div class="brand-text">
                    <span class="brand-title">IKMAS <span class="text-gradient">AI</span></span>
                    <span class="brand-subtitle">Learning Center</span>
                </div>
            </a>
            
            <nav>
                <ul class="nav-links" id="nav-links">
                    <li><a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Beranda</a></li>
                    <li><a href="{{ url('/materi') }}" class="nav-link {{ request()->is('materi*') ? 'active' : '' }}">Materi Belajar</a></li>
                    <li><a href="{{ url('/prompts') }}" class="nav-link {{ request()->is('prompts*') ? 'active' : '' }}">Prompt Library</a></li>
                    <li><a href="{{ url('/showcase') }}" class="nav-link {{ request()->is('showcase*') ? 'active' : '' }}">Showcase Karya</a></li>
                    <li><a href="{{ url('/agenda') }}" class="nav-link {{ request()->is('agenda*') ? 'active' : '' }}">Agenda Event</a></li>
                    <li><a href="{{ url('/#komunitas-garuda') }}" class="nav-link">Hub Komunitas</a></li>
                </ul>
            </nav>
            
            <div class="nav-actions">
                <!-- Dual Theme Switcher -->
                <button type="button" class="theme-toggle-btn" id="theme-toggle-btn" title="Ganti Tema (Light/Dark)">
                    <span id="theme-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                        </svg>
                    </span>
                </button>
                
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ url('/admin/dashboard') }}" class="btn btn-secondary btn-sm">Panel Admin</a>
                    @else
                        <a href="{{ url('/member/dashboard') }}" class="btn btn-secondary btn-sm">Area Member</a>
                    @endif
                    <form action="{{ url('/logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm" style="background: transparent; color: var(--text-muted); border: none;">Keluar</button>
                    </form>
                @else
                    <a href="{{ url('/login') }}" class="btn btn-secondary btn-sm">Masuk</a>
                    <a href="{{ url('/register') }}" class="btn btn-primary btn-sm">Daftar Alumni</a>
                @endauth
                
                <!-- Mobile Menu Button -->
                <button type="button" class="mobile-menu-btn" id="mobile-menu-btn" aria-label="Buka Menu">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                    <div class="nav-brand" style="margin-bottom: 0.5rem;">
                        <div class="brand-icon" style="width: 2rem; height: 2rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                <path d="M2 17l10 5 10-5"></path>
                                <path d="M2 12l10 5 10-5"></path>
                            </svg>
                        </div>
                        <span class="brand-title">IKMAS <span class="text-gradient">AI</span></span>
                    </div>
                    <p class="footer-brand-desc">
                        Ruang belajar dan kolaborasi Artificial Intelligence bagi alumni Assalaam. Dari pemula hingga praktisi.
                    </p>
                    <p style="font-size: 0.85rem; font-style: italic; color: var(--primary); margin-top: 0.75rem; font-weight: 600;">
                        "Belajar AI. Berbagi. Bertumbuh Bersama."
                    </p>
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
