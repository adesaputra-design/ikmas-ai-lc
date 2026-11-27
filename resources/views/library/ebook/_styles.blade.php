@push('styles')
<style>
    .ebook-shell { --ebook-paper: #fbf7ee; --ebook-ink: #2b2a26; --ebook-muted: #6b675d; --ebook-gold-soft: #fdf3d6; background: var(--ebook-paper); color: var(--ebook-ink); min-height: 70vh; padding: 2rem 1rem 4rem; }
    [data-theme="dark"] .ebook-shell { --ebook-paper: var(--bg-surface); --ebook-ink: var(--text-main); --ebook-muted: var(--text-muted); --ebook-gold-soft: rgba(253, 184, 19, 0.12); }
    .ebook-column { max-width: 680px; margin: 0 auto; }
    .ebook-preview-bar { max-width: 680px; margin: 0 auto 1.5rem; padding: 0.65rem 1rem; border-radius: var(--radius-md); background: rgba(253, 184, 19, 0.18); border: 1px solid #fdb813; font-size: 0.85rem; display: flex; justify-content: space-between; gap: 0.75rem; flex-wrap: wrap; }
    .ebook-eyebrow { font-size: 0.8rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: #006837; }
    [data-theme="dark"] .ebook-eyebrow { color: #fdb813; }
    .ebook-title { font-family: 'Source Serif 4', 'Lora', Georgia, serif; font-size: clamp(1.75rem, 5vw, 2.5rem); line-height: 1.2; margin: 0.5rem 0; }
    .ebook-meta { color: var(--ebook-muted); font-size: 0.9rem; }
    .ebook-chip { display: inline-block; padding: 0.15rem 0.6rem; border-radius: 999px; background: var(--ebook-gold-soft); font-size: 0.8rem; font-weight: 600; }
    .ebook-toc { list-style: none; padding: 0; margin: 1.5rem 0; border-top: 1px solid rgba(0, 0, 0, 0.08); }
    .ebook-toc li { border-bottom: 1px solid rgba(0, 0, 0, 0.08); }
    .ebook-toc a { display: flex; justify-content: space-between; gap: 1rem; padding: 0.9rem 0.25rem; color: inherit; text-decoration: none; }
    .ebook-toc a:hover, .ebook-toc a:focus-visible { color: #006837; }
    .ebook-body { font-family: 'Source Serif 4', 'Lora', Georgia, serif; font-size: 1.125rem; line-height: 1.75; }
    .ebook-body h2, .ebook-body h3 { font-family: inherit; margin-top: 2rem; }
    .ebook-body blockquote { margin: 1.5rem 0; padding-left: 1rem; border-left: 3px solid #fdb813; color: var(--ebook-muted); }
    .ebook-box { margin: 1.75rem 0; padding: 1.1rem 1.25rem; border-radius: var(--radius-md); }
    .ebook-box h2 { font-size: 1rem; margin: 0 0 0.5rem; }
    .ebook-box--summary { background: var(--ebook-gold-soft); }
    .ebook-box--research { background: rgba(0, 104, 55, 0.07); border-left: 4px solid #006837; }
    .ebook-box--action { border: 2px solid #006837; }
    .ebook-box ul { margin: 0; padding-left: 1.25rem; }
    .ebook-nav { display: flex; justify-content: space-between; gap: 1rem; margin-top: 2.5rem; padding-top: 1.25rem; border-top: 1px solid rgba(0, 0, 0, 0.1); }
    .ebook-nav a { color: #006837; font-weight: 700; text-decoration: none; }
    [data-theme="dark"] .ebook-nav a, [data-theme="dark"] .ebook-toc a:hover { color: #fdb813; }
</style>
@endpush
