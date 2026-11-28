document.addEventListener('DOMContentLoaded', () => {
    const reader = document.querySelector('[data-ebook-reader]');
    if (!reader) return;

    const output = document.getElementById('ebook-font-scale');
    let scale = 1;

    try {
        const savedScale = Number.parseFloat(localStorage.getItem('ikmas_ebook_font_scale'));
        if (Number.isFinite(savedScale)) scale = Math.min(1.4, Math.max(0.85, savedScale));
    } catch {
        // Reading controls remain available when browser storage is disabled.
    }

    const renderScale = () => {
        reader.style.fontSize = `${Number((1.15 * scale).toFixed(3))}rem`;
        if (output) output.textContent = `${Math.round(scale * 100)}%`;
        try {
            localStorage.setItem('ikmas_ebook_font_scale', String(scale));
        } catch {
            // The current page still uses the selected size until it is closed.
        }
    };

    document.querySelectorAll('[data-ebook-font-change]').forEach((button) => {
        button.addEventListener('click', () => {
            const change = Number.parseFloat(button.dataset.ebookFontChange);
            scale = Math.min(1.4, Math.max(0.85, Math.round((scale + change) * 10) / 10));
            renderScale();
        });
    });

    document.querySelectorAll('[data-ebook-font-reset]').forEach((button) => {
        button.addEventListener('click', () => {
            scale = 1;
            renderScale();
        });
    });

    renderScale();
});
