@extends('layouts.admin')

@section('title', 'Edit Materi Belajar — IKMAS AI')
@section('page-title', 'Edit Materi Belajar')

@section('content')
<div style="max-width: 850px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <a href="{{ route('admin.materi.index') }}" class="btn btn-secondary btn-sm">
            ← Kembali ke Daftar Materi
        </a>
        @if($materi->is_published)
            <a href="{{ url('/materi/' . $materi->slug) }}" target="_blank" class="btn btn-secondary btn-sm">
                Lihat di Web Publik ↗
            </a>
        @endif
    </div>

    <div class="card" style="padding: 2rem;">
        <h2 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 1.5rem;">Perbarui Modul Pembelajaran</h2>

        @if ($errors->any())
            <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: var(--radius-md); padding: 0.875rem 1rem; margin-bottom: 1.5rem; color: #ef4444; font-size: 0.875rem;">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.materi.update', $materi->id) }}" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            @method('PUT')

            <div>
                <label for="title" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Judul Materi <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title', $materi->title) }}" required
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem;">
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                <div>
                    <label for="pillar" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Pilar Kurikulum <span style="color: #ef4444;">*</span>
                    </label>
                    <select id="pillar" name="pillar" required
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                        <option value="basics" {{ old('pillar', $materi->pillar) === 'basics' ? 'selected' : '' }}>AI Basics</option>
                        <option value="tools" {{ old('pillar', $materi->pillar) === 'tools' ? 'selected' : '' }}>AI Tools</option>
                        <option value="productivity" {{ old('pillar', $materi->pillar) === 'productivity' ? 'selected' : '' }}>AI Productivity</option>
                        <option value="workflow" {{ old('pillar', $materi->pillar) === 'workflow' ? 'selected' : '' }}>AI Workflow</option>
                        <option value="opportunity" {{ old('pillar', $materi->pillar) === 'opportunity' ? 'selected' : '' }}>AI for Opportunity</option>
                    </select>
                </div>

                <div>
                    <label for="level" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Tingkat Kemahiran <span style="color: #ef4444;">*</span>
                    </label>
                    <select id="level" name="level" required
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                        <option value="beginner" {{ old('level', $materi->level) === 'beginner' ? 'selected' : '' }}>Beginner (Pemula)</option>
                        <option value="explorer" {{ old('level', $materi->level) === 'explorer' ? 'selected' : '' }}>Explorer (Menengah)</option>
                        <option value="practitioner" {{ old('level', $materi->level) === 'practitioner' ? 'selected' : '' }}>Practitioner (Lanjutan)</option>
                    </select>
                </div>

                <div>
                    <label for="reading_minutes" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Estimasi Waktu Baca (Menit)
                    </label>
                    <input type="number" id="reading_minutes" name="reading_minutes" value="{{ old('reading_minutes', $materi->reading_minutes) }}" min="1"
                           style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                </div>
            </div>

            <div>
                <label for="summary" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Ringkasan Singkat (Muncul di Kartu Depan)
                </label>
                <textarea id="summary" name="summary" rows="2"
                          style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">{{ old('summary', $materi->summary) }}</textarea>
            </div>

            <div>
                <label for="content" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Isi Lengkap Materi Pembelajaran <span style="color: #ef4444;">*</span>
                </label>
                <textarea id="content" name="content" rows="10" required
                          style="width: 100%; padding: 0.75rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem; line-height: 1.6;">{{ old('content', $materi->content) }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                <div>
                    <label for="slide_url" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Tautan Slide Presentasi (Canva / Google Drive)
                    </label>
                    <input type="url" id="slide_url" name="slide_url" value="{{ old('slide_url', $materi->slide_url) }}"
                           placeholder="https://canva.com/design/..."
                           style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
                </div>

                <div>
                    <label for="video_url" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Tautan Video Rekaman Sesi (YouTube)
                    </label>
                    <input type="url" id="video_url" name="video_url" value="{{ old('video_url', $materi->video_url) }}"
                           placeholder="https://youtube.com/watch?v=..."
                           style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
                </div>
            </div>

            <div style="padding: 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface-alt); display: flex; align-items: center; gap: 0.75rem;">
                <input type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published', $materi->is_published) ? 'checked' : '' }} style="width: 1.25rem; height: 1.25rem; accent-color: var(--primary);">
                <label for="is_published" style="font-size: 0.9rem; font-weight: 600; cursor: pointer;">
                    Publikasikan Langsung ke Web Publik (Hilangkan centang jika ingin disimpan sebagai Draf)
                </label>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1rem;">
                <a href="{{ route('admin.materi.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Materi Belajar</button>
            </div>
        </form>
    </div>
</div>
@endsection
