@extends('layouts.admin')

@section('title', (isset($chapter) ? 'Edit Bab' : 'Tambah Bab') . ' — ' . $item->title)
@section('page-title', isset($chapter) ? 'Edit Bab Ebook' : 'Tambah Bab Ebook')

@section('content')
<div style="max-width: 850px; margin: 0 auto;">
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem;">
        <a class="btn btn-secondary btn-sm" href="{{ route('admin.library.edit', $item) }}">← Kembali ke Ebook</a>
        <a class="btn btn-secondary btn-sm" href="{{ route('admin.library.ebook.preview', $item) }}">Pratinjau Draf</a>
    </div>

    <div class="card" style="padding: 2rem;">
        <h2 style="margin-top: 0;">{{ $item->title }}</h2>
        <p style="color: var(--text-muted);">Bab tersimpan sebagai draf privat. Isi Markdown akan dirender di pratinjau.</p>

        @if($errors->any())
            <div role="alert" style="color: #b91c1c; margin-bottom: 1rem;">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ isset($chapter) ? route('admin.library.ebook.chapters.update', [$item, $chapter]) : route('admin.library.ebook.chapters.store', $item) }}" style="display: grid; gap: 1rem;">
            @csrf
            @if(isset($chapter)) @method('PUT') @endif
            <label for="chapter-title">Judul Bab *
                <input id="chapter-title" name="title" type="text" required value="{{ old('title', $chapter->title ?? '') }}" style="display: block; width: 100%; padding: 0.65rem;">
            </label>
            <label for="chapter-subtitle">Subjudul
                <input id="chapter-subtitle" name="subtitle" type="text" value="{{ old('subtitle', $chapter->subtitle ?? '') }}" style="display: block; width: 100%; padding: 0.65rem;">
            </label>
            <div>
                <div id="chapter-summaries" style="display: grid; gap: 0.5rem;">
                    @foreach(old('summary_points', $chapter->summary_points ?? ['']) as $point)
                        <label>Intisari 30 Detik *
                            <input name="summary_points[]" type="text" required value="{{ $point }}" style="display: block; width: 100%; padding: 0.65rem;">
                        </label>
                    @endforeach
                </div>
                <button class="btn btn-secondary btn-sm" type="button" onclick="addChapterField('chapter-summaries', 'summary_points[]', 'Intisari 30 Detik', true)" style="margin-top: 0.5rem;">Tambah Poin</button>
            </div>
            <label for="chapter-body">Isi Bab (Markdown) *
                <textarea id="chapter-body" name="body" rows="12" required style="display: block; width: 100%; padding: 0.65rem;">{{ old('body', $chapter->body ?? '') }}</textarea>
            </label>
            <section aria-labelledby="approved-quotes-title">
                <h3 id="approved-quotes-title">Kutipan yang disetujui narasumber</h3>
                <p>Hanya kutipan dengan atribusi dan persetujuan yang dicentang di sini yang akan mendapat tombol berbagi.</p>
                @php($nextQuoteIndex = max(array_keys(old('approved_quotes', $quotes) ?: [-1])) + 1)
                <div id="approved-quotes" data-next-index="{{ $nextQuoteIndex }}" style="display: grid; gap: 1rem;">
                    @foreach(old('approved_quotes', $quotes) as $index => $quote)
                        <fieldset class="ebook-quote-editor" style="display: grid; gap: 0.75rem; border: 1px solid var(--border-color); padding: 1rem;">
                            <legend>Kutipan {{ $index + 1 }}</legend>
                            <label for="quote-text-{{ $index }}">Teks kutipan
                                <textarea id="quote-text-{{ $index }}" name="approved_quotes[{{ $index }}][text]" rows="3" required maxlength="1000" style="display: block; width: 100%; padding: 0.65rem;">{{ $quote['text'] ?? '' }}</textarea>
                            </label>
                            <label for="quote-attribution-{{ $index }}">Atribusi / narasumber
                                <input id="quote-attribution-{{ $index }}" name="approved_quotes[{{ $index }}][attribution]" type="text" required maxlength="255" value="{{ $quote['attribution'] ?? '' }}" style="display: block; width: 100%; padding: 0.65rem;">
                            </label>
                            <label>
                                <input name="approved_quotes[{{ $index }}][approved]" type="checkbox" value="1" required @checked(($quote['approved'] ?? null) === '1' || ($quote['approved'] ?? null) === 1)>
                                Saya memastikan narasumber menyetujui kutipan ini untuk dibagikan.
                            </label>
                            <button class="btn btn-secondary btn-sm" type="button" data-remove-quote>Hapus kutipan</button>
                        </fieldset>
                    @endforeach
                </div>
                <button class="btn btn-secondary btn-sm" type="button" id="add-approved-quote">Tambah kutipan yang disetujui</button>
            </section>
            <label for="chapter-research">Kata Riset
                <textarea id="chapter-research" name="research_box" rows="4" style="display: block; width: 100%; padding: 0.65rem;">{{ old('research_box', $chapter->research_box ?? '') }}</textarea>
            </label>
            <label for="chapter-action">Amalkan Pekan Ini
                <input id="chapter-action" name="action_title" type="text" value="{{ old('action_title', $chapter->action_title ?? '') }}" style="display: block; width: 100%; padding: 0.65rem;">
            </label>
            <div>
                <div id="chapter-checklist" style="display: grid; gap: 0.5rem;">
                    @foreach(old('action_checklist', $chapter->action_checklist ?? ['']) as $action)
                        <label>Butir Checklist
                            <input name="action_checklist[]" type="text" value="{{ $action }}" style="display: block; width: 100%; padding: 0.65rem;">
                        </label>
                    @endforeach
                </div>
                <button class="btn btn-secondary btn-sm" type="button" onclick="addChapterField('chapter-checklist', 'action_checklist[]', 'Butir Checklist', false)" style="margin-top: 0.5rem;">Tambah Butir</button>
            </div>
            <div><button class="btn btn-primary" type="submit">{{ isset($chapter) ? 'Simpan Perubahan' : 'Simpan Bab' }}</button></div>
        </form>
    </div>
</div>
<script>
function addChapterField(containerId, name, caption, required) {
    const label = document.createElement('label');
    label.textContent = caption;
    const input = document.createElement('input');
    input.name = name;
    input.type = 'text';
    input.required = required;
    input.style.cssText = 'display: block; width: 100%; padding: 0.65rem;';
    label.appendChild(input);
    document.getElementById(containerId).appendChild(label);
    input.focus();
}

document.getElementById('add-approved-quote').addEventListener('click', () => {
    const container = document.getElementById('approved-quotes');
    const index = Number(container.dataset.nextIndex);
    container.dataset.nextIndex = String(index + 1);
    const fieldset = document.createElement('fieldset');
    fieldset.className = 'ebook-quote-editor';
    fieldset.style.cssText = 'display: grid; gap: 0.75rem; border: 1px solid var(--border-color); padding: 1rem;';
    fieldset.innerHTML = `
        <legend>Kutipan ${index + 1}</legend>
        <label for="quote-text-${index}">Teks kutipan
            <textarea id="quote-text-${index}" name="approved_quotes[${index}][text]" rows="3" required maxlength="1000" style="display: block; width: 100%; padding: 0.65rem;"></textarea>
        </label>
        <label for="quote-attribution-${index}">Atribusi / narasumber
            <input id="quote-attribution-${index}" name="approved_quotes[${index}][attribution]" type="text" required maxlength="255" style="display: block; width: 100%; padding: 0.65rem;">
        </label>
        <label><input name="approved_quotes[${index}][approved]" type="checkbox" value="1" required> Saya memastikan narasumber menyetujui kutipan ini untuk dibagikan.</label>
        <button class="btn btn-secondary btn-sm" type="button" data-remove-quote>Hapus kutipan</button>
    `;
    container.appendChild(fieldset);
    fieldset.querySelector('[data-remove-quote]').addEventListener('click', () => fieldset.remove());
    fieldset.querySelector('textarea').focus();
});

document.querySelectorAll('[data-remove-quote]').forEach((button) => {
    button.addEventListener('click', () => button.closest('fieldset').remove());
});
</script>
@endsection
