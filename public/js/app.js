// IKMAS AI Learning Center - Master Client Script

// 1. Theme Management (Dual Theme: Light / Dark)
(function initTheme() {
    const savedTheme = localStorage.getItem('ikmas_theme');
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const initialTheme = savedTheme ? savedTheme : (prefersDark ? 'dark' : 'light');
    
    document.documentElement.setAttribute('data-theme', initialTheme);
    updateThemeIcon(initialTheme);
})();

function updateThemeIcon(theme) {
    const themeIcon = document.getElementById('theme-icon');
    if (!themeIcon) return;
    
    if (theme === 'dark') {
        // Moon to Sun icon
        themeIcon.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="5"></circle>
                <line x1="12" y1="1" x2="12" y2="3"></line>
                <line x1="12" y1="21" x2="12" y2="23"></line>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                <line x1="1" y1="12" x2="3" y2="12"></line>
                <line x1="21" y1="12" x2="23" y2="12"></line>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
            </svg>
        `;
    } else {
        // Sun to Moon icon
        themeIcon.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
            </svg>
        `;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const themeBtn = document.getElementById('theme-toggle-btn');
    if (themeBtn) {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        updateThemeIcon(currentTheme);
        
        themeBtn.addEventListener('click', () => {
            const current = document.documentElement.getAttribute('data-theme');
            const nextTheme = current === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', nextTheme);
            localStorage.setItem('ikmas_theme', nextTheme);
            updateThemeIcon(nextTheme);
        });
    }

    // 2. Desktop Dropdowns with 150ms Hover Delay & Click Toggle
    const dropdowns = document.querySelectorAll('.nav-dropdown');
    dropdowns.forEach(dropdown => {
        let timer = null;
        const btn = dropdown.querySelector('.nav-dropdown-btn');

        const openDropdown = () => {
            clearTimeout(timer);
            dropdowns.forEach(d => {
                if (d !== dropdown) {
                    d.classList.remove('is-open');
                    d.querySelector('.nav-dropdown-btn')?.setAttribute('aria-expanded', 'false');
                }
            });
            dropdown.classList.add('is-open');
            btn?.setAttribute('aria-expanded', 'true');
        };

        const closeDropdown = () => {
            timer = setTimeout(() => {
                dropdown.classList.remove('is-open');
                btn?.setAttribute('aria-expanded', 'false');
            }, 150);
        };

        dropdown.addEventListener('mouseenter', openDropdown);
        dropdown.addEventListener('mouseleave', closeDropdown);

        if (btn) {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = dropdown.classList.contains('is-open');
                if (isOpen) {
                    dropdown.classList.remove('is-open');
                    btn.setAttribute('aria-expanded', 'false');
                } else {
                    openDropdown();
                }
            });
        }
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.nav-dropdown')) {
            dropdowns.forEach(d => {
                d.classList.remove('is-open');
                d.querySelector('.nav-dropdown-btn')?.setAttribute('aria-expanded', 'false');
            });
        }
    });

    // 3. Mobile Offcanvas (m.ikmas.com Style - Slide from Right)
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const offcanvas = document.getElementById('mobileOffcanvas');
    const backdrop = document.getElementById('offcanvasBackdrop');
    const closeBtn = document.getElementById('offcanvasCloseBtn');

    function openOffcanvas() {
        if (!offcanvas) return;
        offcanvas.classList.add('active');
        if (backdrop) backdrop.classList.add('active');
        mobileBtn?.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeOffcanvas() {
        if (!offcanvas) return;
        offcanvas.classList.remove('active');
        if (backdrop) backdrop.classList.remove('active');
        mobileBtn?.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    if (mobileBtn) {
        mobileBtn.addEventListener('click', openOffcanvas);
    }
    if (closeBtn) {
        closeBtn.addEventListener('click', closeOffcanvas);
    }
    if (backdrop) {
        backdrop.addEventListener('click', closeOffcanvas);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && offcanvas && offcanvas.classList.contains('active')) {
            closeOffcanvas();
        }
    });

    // Auto-close offcanvas on navigation click
    if (offcanvas) {
        offcanvas.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                closeOffcanvas();
            });
        });
    }

    // 4. Sticky Header with Scroll Detection (Hide on scroll down, show on scroll up)
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        let lastScrollY = window.scrollY;
        let ticking = false;

        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    const currentScrollY = window.scrollY;
                    // Hanya sembunyikan jika offcanvas sedang tidak aktif dan sudah scroll lebih dari 80px
                    const isOffcanvasOpen = offcanvas && offcanvas.classList.contains('active');

                    if (!isOffcanvasOpen) {
                        if (currentScrollY > lastScrollY && currentScrollY > 90) {
                            navbar.classList.add('navbar-hidden');
                        } else if (currentScrollY < lastScrollY) {
                            navbar.classList.remove('navbar-hidden');
                        }
                    }

                    lastScrollY = Math.max(0, currentScrollY);
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    }
});

// 3. Toast Notification Helper
window.showToast = function(message, type = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    const icon = type === 'success' ? `
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
    ` : `
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
    `;
    
    toast.innerHTML = `
        ${icon}
        <span style="font-size: 0.9rem; font-weight: 500;">${message}</span>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
};

// 4. Interactive 1-Click Clipboard Copy with Feedback
window.copyPrompt = function(button, promptText) {
    if (!navigator.clipboard) {
        // Fallback
        const textarea = document.createElement('textarea');
        textarea.value = promptText;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        handleCopySuccess(button);
        return;
    }
    
    navigator.clipboard.writeText(promptText).then(() => {
        handleCopySuccess(button);
    }).catch(err => {
        console.error('Copy failed: ', err);
        window.showToast('Gagal menyalin prompt.', 'error');
    });
};

function handleCopySuccess(button) {
    const originalContent = button.innerHTML;
    button.innerHTML = `
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        Tersalin!
    `;
    button.classList.add('btn-whatsapp');
    button.classList.remove('btn-secondary', 'btn-primary');
    
    window.showToast('Prompt berhasil disalin ke clipboard!');
    
    setTimeout(() => {
        button.innerHTML = originalContent;
        button.classList.remove('btn-whatsapp');
        button.classList.add('btn-secondary');
    }, 2500);
}
