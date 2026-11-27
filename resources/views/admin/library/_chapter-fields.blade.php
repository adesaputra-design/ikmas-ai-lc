@php
    $inputStyle = 'width: 100%; padding: 0.6rem 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;';
    $labelStyle = 'display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.35rem;';
    $summaryLines = old('summary_points', $chapter->summary_points ?: ['']);
    $checklistLines = old('action_checklist', $chapter->action_checklist ?: ['']);
@endphp

@if ($errors->any() && ! $errors->hasAny(['chapters', 'editorial_approved', 'direction']))
    <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: var(--radius-md); padding: 0.75rem 1rem; margin-bottom: 1rem; color: #ef4444; font-size: 0.875rem;">
        @foreach ($errors->all() as $error)
            <div>• {{ $error }}</div>
        @endforeach
    </div>
@endif

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
    <div>
        <label for="title" style="{{ $labelStyle }}">Judul Bab <span style="color: #ef4444;">*</span></label>
        <input type="text" id="title" name="title" value="{{ old('title', $chapter->title) }}" required style="{{ $inputStyle }}">
    </div>
    <div>
        <label for="subtitle" style="{{ $labelStyle }}">Subjudul</label>
        <input type="text" id="subtitle" name="subtitle" value="{{ old('subtitle', $chapter->subtitle) }}" style="{{ $inputStyle }}">
    </div>
</div>

<fieldset style="border: none; padding: 0; margin: 0;">
    <legend style="{{ $labelStyle }}">Intisari 30 Detik (satu poin per baris)</legend>
    <div data-lines="summary_points" style="display: flex; flex-direction: column; gap: 0.5rem;">
        @foreach($summaryLines as $point)
            <input type="text" name="summary_points[]" value="{{ $point }}" aria-label="Poin intisari" style="{{ $inputStyle }}">
        @endforeach
    </div>
    <button type="button" class="btn btn-secondary btn-sm" style="margin-top: 0.5rem;" onclick="addLine('summary_points', 'Poin intisari')">+ Tambah poin</button>
</fieldset>

<div>
    <label for="body" style="{{ $labelStyle }}">Isi Bab (Markdown) <span style="color: #ef4444;">*</span></label>
    <textarea id="body" name="body" rows="14" required style="{{ $inputStyle }} font-family: ui-monospace, monospace;">{{ old('body', $chapter->body) }}</textarea>
    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">HTML mentah akan dibuang. Waktu baca dihitung otomatis (200 kata/menit).</div>
</div>

<div>
    <label for="research_box" style="{{ $labelStyle }}">📊 Kata Riset (Markdown)</label>
    <textarea id="research_box" name="research_box" rows="4" style="{{ $inputStyle }}">{{ old('research_box', $chapter->research_box) }}</textarea>
</div>

<div>
    <label for="action_title" style="{{ $labelStyle }}">✅ Amalkan Pekan Ini</label>
    <input type="text" id="action_title" name="action_title" value="{{ old('action_title', $chapter->action_title) }}" style="{{ $inputStyle }}">
</div>

<fieldset style="border: none; padding: 0; margin: 0;">
    <legend style="{{ $labelStyle }}">Checklist aksi (satu item per baris)</legend>
    <div data-lines="action_checklist" style="display: flex; flex-direction: column; gap: 0.5rem;">
        @foreach($checklistLines as $task)
            <input type="text" name="action_checklist[]" value="{{ $task }}" aria-label="Item checklist" style="{{ $inputStyle }}">
        @endforeach
    </div>
    <button type="button" class="btn btn-secondary btn-sm" style="margin-top: 0.5rem;" onclick="addLine('action_checklist', 'Item checklist')">+ Tambah item</button>
</fieldset>

<script>
function addLine(name, label) {
    const container = document.querySelector('[data-lines="' + name + '"]');
    const input = container.querySelector('input').cloneNode();
    input.value = '';
    input.setAttribute('aria-label', label);
    container.appendChild(input);
    input.focus();
}
</script>
