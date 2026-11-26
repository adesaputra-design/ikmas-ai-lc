@extends('layouts.admin')

@section('title', 'Bab Ebook: ' . $item->title . ' — IKMAS AI')
@section('page-title', 'Kelola Bab Ebook')

@php
    $inputStyle = 'width: 100%; padding: 0.6rem 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;';
    $labelStyle = 'display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.35rem;';
    $oldSummary = old('summary_points', ['']);
    $oldChecklist = old('action_checklist', ['']);
@endphp

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
        <a href="{{ route('admin.library.index', ['tab' => 'ebook']) }}" class="btn btn-secondary btn-sm">← Kembali ke Pustaka</a>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.library.edit', $item) }}" class="btn btn-secondary btn-sm">Edit Info Ebook</a>
            <a href="{{ route('admin.library.preview', $item) }}" target="_blank" class="btn btn-primary btn-sm">Pratinjau ↗</a>
        </div>
    </div>

    <div class="card" style="padding: 1.5rem; margin-bottom: 1.5rem;">
        <div style="font-size: 0.8rem; color: var(--text-muted);">
            {{ $item->series_name ?? 'Ebook' }}@if($item->edition_number) · Edisi #{{ $item->edition_number }}@endif
            · Status: <strong>{{ $item->status }}</strong> · {{ $item->is_public ? 'Akses publik' : 'Akses terbatas' }}
        </div>
        <h2 style="font-size: 1.3rem; font-weight: 800; margin: 0.25rem 0 1rem;">{{ $item->title }}</h2>

        @if($chapters->isEmpty())
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Belum ada bab. Tambahkan bab pertama melalui form di bawah.</p>
        @else
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                        <th style="padding: 0.5rem;">#</th>
                        <th style="padding: 0.5rem;">Judul</th>
                        <th style="padding: 0.5rem;">Waktu baca</th>
                        <th style="padding: 0.5rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chapters as $chapter)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.5rem;">{{ $chapter->position }}</td>
                            <td style="padding: 0.5rem;">
                                <strong>{{ $chapter->title }}</strong>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">/bab/{{ $chapter->slug }}</div>
                            </td>
                            <td style="padding: 0.5rem;">{{ $chapter->reading_minutes }} mnt</td>
                            <td style="padding: 0.5rem; text-align: right;">
                                <a href="{{ route('admin.library.preview.chapter', [$item, $chapter->slug]) }}" target="_blank" class="btn btn-secondary btn-sm">Pratinjau ↗</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0.75rem 0 0;">Total waktu baca: {{ $item->reading_time }}</p>
        @endif
    </div>

    <div class="card" style="padding: 1.5rem;">
        <h3 style="font-size: 1.1rem; font-weight: 800; margin: 0 0 1rem;">Tambah Bab</h3>

        @if ($errors->any())
            <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: var(--radius-md); padding: 0.75rem 1rem; margin-bottom: 1rem; color: #ef4444; font-size: 0.875rem;">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.library.chapters.store', $item) }}" style="display: flex; flex-direction: column; gap: 1.1rem;">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
                <div>
                    <label for="title" style="{{ $labelStyle }}">Judul Bab <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required style="{{ $inputStyle }}">
                </div>
                <div>
                    <label for="subtitle" style="{{ $labelStyle }}">Subjudul</label>
                    <input type="text" id="subtitle" name="subtitle" value="{{ old('subtitle') }}" style="{{ $inputStyle }}">
                </div>
            </div>

            <fieldset style="border: none; padding: 0; margin: 0;">
                <legend style="{{ $labelStyle }}">Intisari 30 Detik (satu poin per baris)</legend>
                <div data-lines="summary_points" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    @foreach($oldSummary as $point)
                        <input type="text" name="summary_points[]" value="{{ $point }}" aria-label="Poin intisari" style="{{ $inputStyle }}">
                    @endforeach
                </div>
                <button type="button" class="btn btn-secondary btn-sm" style="margin-top: 0.5rem;" onclick="addLine('summary_points', 'Poin intisari')">+ Tambah poin</button>
            </fieldset>

            <div>
                <label for="body" style="{{ $labelStyle }}">Isi Bab (Markdown) <span style="color: #ef4444;">*</span></label>
                <textarea id="body" name="body" rows="14" required style="{{ $inputStyle }} font-family: ui-monospace, monospace;">{{ old('body') }}</textarea>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">HTML mentah akan dibuang. Waktu baca dihitung otomatis (200 kata/menit).</div>
            </div>

            <div>
                <label for="research_box" style="{{ $labelStyle }}">📊 Kata Riset (Markdown)</label>
                <textarea id="research_box" name="research_box" rows="4" style="{{ $inputStyle }}">{{ old('research_box') }}</textarea>
            </div>

            <div>
                <label for="action_title" style="{{ $labelStyle }}">✅ Amalkan Pekan Ini</label>
                <input type="text" id="action_title" name="action_title" value="{{ old('action_title') }}" style="{{ $inputStyle }}">
            </div>

            <fieldset style="border: none; padding: 0; margin: 0;">
                <legend style="{{ $labelStyle }}">Checklist aksi (satu item per baris)</legend>
                <div data-lines="action_checklist" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    @foreach($oldChecklist as $task)
                        <input type="text" name="action_checklist[]" value="{{ $task }}" aria-label="Item checklist" style="{{ $inputStyle }}">
                    @endforeach
                </div>
                <button type="button" class="btn btn-secondary btn-sm" style="margin-top: 0.5rem;" onclick="addLine('action_checklist', 'Item checklist')">+ Tambah item</button>
            </fieldset>

            <div style="display: flex; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <button type="submit" class="btn btn-primary">Simpan Bab</button>
            </div>
        </form>
    </div>
</div>

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
@endsection
