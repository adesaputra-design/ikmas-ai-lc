@extends('layouts.admin')

@section('title', 'Editor Asesmen — ' . $item->title)
@section('page-title', 'Editor Asesmen Ebook')

@section('content')
<div class="card" style="max-width: 900px; margin: 0 auto; padding: 1.5rem;">
    <p>Isi definisi asesmen sebagai JSON. Nilai skala, pertanyaan tiap prinsip, semua rentang skor, dan slug bab rekomendasi harus lengkap.</p>
    <form method="POST" action="{{ route('admin.library.ebook.assessment.update', $item) }}">
        @csrf
        @method('PUT')
        <label for="assessment" style="display: block; font-weight: 700; margin-bottom: 0.5rem;">Definisi asesmen</label>
        <textarea id="assessment" name="assessment" rows="26" required spellcheck="false" style="width: 100%; font-family: ui-monospace, monospace; font-size: 0.85rem;">{{ old('assessment', $item->assessment ? json_encode($item->assessment, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
        @error('assessment')<p role="alert" style="color: #b91c1c;">{{ $message }}</p>@enderror
        @if($errors->has('assessment.*'))<p role="alert" style="color: #b91c1c;">{{ $errors->first('assessment.*') }}</p>@endif
        <p style="color: var(--text-muted);">Rentang skor harus berurutan dari jumlah minimum sampai maksimum yang mungkin. Rekomendasi memakai slug bab dari edisi ini.</p>
        <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
            <a class="btn btn-secondary" href="{{ route('admin.library.ebook.preview', $item) }}">Batal</a>
            <button class="btn btn-primary" type="submit">Simpan asesmen</button>
        </div>
    </form>
</div>
@endsection
