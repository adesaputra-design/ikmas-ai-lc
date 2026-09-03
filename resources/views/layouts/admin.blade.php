<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Pengurus — IKMAS AI Learning Center')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Master CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
            background: var(--bg-body);
        }

        .admin-sidebar {
            width: 270px;
            background: var(--bg-surface);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            transition: transform 0.25s ease;
        }

        .admin-sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .admin-nav {
            padding: 1.25rem 1rem;
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .admin-nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .admin-nav-item:hover {
            background: var(--bg-surface-alt);
            color: var(--primary);
        }

        .admin-nav-item.active {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            font-weight: 700;
        }

        .admin-nav-badge {
            margin-left: auto;
            background: var(--accent-amber);
            color: #1e293b;
            font-size: 0.75rem;
            padding: 0.15rem 0.5rem;
            border-radius: 999px;
            font-weight: 700;
        }

        .admin-sidebar-footer {
            padding: 1.25rem 1rem;
            border-top: 1px solid var(--border-color);
            background: var(--bg-surface-alt);
        }

        .admin-main {
            margin-left: 270px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .admin-topbar {
            height: 68px;
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .admin-content {
            padding: 2rem;
            flex: 1;
        }

        .admin-mobile-toggle {
            display: none;
            background: transparent;
            border: none;
            color: var(--text-main);
            cursor: pointer;
        }

        @media (max-width: 1024px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            .admin-sidebar.open {
                transform: translateX(0);
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            }
            .admin-main {
                margin-left: 0;
            }
            .admin-mobile-toggle {
                display: block;
            }
            .admin-content {
                padding: 1.25rem;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="admin-sidebar">
            <div>
                <!-- Brand Header -->
                <div class="admin-sidebar-header">
                    <a href="{{ url('/admin/dashboard') }}" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none;">
                        <div class="brand-icon" style="width: 2.25rem; height: 2.25rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                <path d="M2 17l10 5 10-5"></path>
                                <path d="M2 12l10 5 10-5"></path>
                            </svg>
                        </div>
                        <div>
                            <div style="font-weight: 800; font-size: 1.15rem; color: var(--text-main); line-height: 1.1;">
                                IKMAS <span class="text-gradient">AI</span>
                            </div>
                            <span class="badge badge-primary" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">Panel Pengurus</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="admin-nav">
                    <a href="{{ url('/admin/dashboard') }}" class="admin-nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span>Dasbor Utama</span>
                    </a>

                    <a href="{{ url('/admin/materi') }}" class="admin-nav-item {{ request()->is('admin/materi*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        <span>Kelola Materi</span>
                    </a>

                    <a href="{{ url('/admin/prompts') }}" class="admin-nav-item {{ request()->is('admin/prompts*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                        </svg>
                        <span>Prompt Library</span>
                    </a>

                    <a href="{{ url('/admin/agenda') }}" class="admin-nav-item {{ request()->is('admin/agenda*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Agenda Kegiatan</span>
                    </a>

                    <a href="{{ url('/admin/curation') }}" class="admin-nav-item {{ request()->is('admin/curation*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 11 12 14 22 4"></polyline>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                        <span>Kurasi Showcase</span>
                    </a>

                    <a href="{{ url('/admin/alumni') }}" class="admin-nav-item {{ request()->is('admin/alumni*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span>Member Alumni</span>
                    </a>
                </nav>
            </div>

            <!-- Footer Profile & Switcher -->
            <div class="admin-sidebar-footer">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.85rem;">
                    <div style="width: 2.25rem; height: 2.25rem; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.85rem;">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div style="overflow: hidden; flex: 1;">
                        <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main); white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                            {{ auth()->user()->name ?? 'Pengurus' }}
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                            {{ auth()->user()->email ?? 'admin@ikmas.ai' }}
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ url('/') }}" target="_blank" class="btn btn-secondary btn-sm" style="flex: 1; font-size: 0.75rem; padding: 0.4rem;">
                        Lihat Web Publik ↗
                    </a>
                    <form action="{{ url('/logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm" title="Keluar" style="padding: 0.4rem 0.6rem; border: 1px solid var(--border-color); background: var(--bg-surface);">
                            🚪
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="admin-main">
            <!-- Topbar -->
            <header class="admin-topbar">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button type="button" class="admin-mobile-toggle" id="admin-mobile-toggle" aria-label="Toggle Sidebar">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <div>
                        <h2 style="font-size: 1.15rem; font-weight: 800; margin: 0;">@yield('page-title', 'Dasbor Pengurus')</h2>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 1rem;">
                    <!-- Dual Theme Switcher -->
                    <button type="button" class="theme-toggle-btn" id="theme-toggle-btn" title="Ganti Tema (Light/Dark)">
                        <span id="theme-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </header>

            <!-- Dynamic Content -->
            <main class="admin-content">
                @if(session('success'))
                    <div style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: var(--radius-lg); padding: 1rem 1.25rem; margin-bottom: 1.5rem; color: #10b981; display: flex; align-items: center; gap: 0.75rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span style="font-weight: 600;">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('info'))
                    <div style="background: rgba(14,165,233,0.1); border: 1px solid rgba(14,165,233,0.3); border-radius: var(--radius-lg); padding: 1rem 1.25rem; margin-bottom: 1.5rem; color: #0ea5e9; display: flex; align-items: center; gap: 0.75rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <span style="font-weight: 600;">{{ session('info') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="toast-container"></div>

    <!-- Master Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('admin-sidebar');
            const toggle = document.getElementById('admin-mobile-toggle');
            if (toggle && sidebar) {
                toggle.addEventListener('click', () => {
                    sidebar.classList.toggle('open');
                });
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
