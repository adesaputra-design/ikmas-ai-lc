@extends('layouts.admin')

@section('title', ($isEdit ? 'Edit Konten Pustaka' : 'Tambah Konten Pustaka') . ' — IKMAS AI')
@section('page-title', $isEdit ? 'Edit Konten Pustaka' : 'Tambah Konten Pustaka')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <a href="{{ route('admin.library.index') }}" class="btn btn-secondary btn-sm">
            ← Kembali ke Pustaka
        </a>
    </div>

    <div class="card" style="padding: 2rem;">
        <h2 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 0.5rem;">
            {{ $isEdit ? 'Edit Informasi Pustaka: ' . $item->title : 'Tambah Koleksi Buku / Podcast / Jurnal' }}
        </h2>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1.5rem;">
            Rangkuman buku dan resume podcast dapat dibuat langsung oleh pengurus/admin untuk memperkaya khazanah AI alumni.
        </p>

        @if ($errors->any())
            <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: var(--radius-md); padding: 0.875rem 1rem; margin-bottom: 1.5rem; color: #ef4444; font-size: 0.875rem;">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" 
              action="{{ $isEdit ? route('admin.library.update', $item->id) : route('admin.library.store') }}" 
              enctype="multipart/form-data" 
              style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <!-- Format Type Selection -->
            <div>
                <label for="type" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Format Koleksi <span style="color: #ef4444;">*</span>
                </label>
                @if($isEdit)
                    <input type="hidden" name="type" value="{{ $item->type }}">
                    <div style="padding: 0.65rem 0.85rem; background: var(--bg-surface-alt); border: 1px solid var(--border-color); border-radius: var(--radius-md); font-weight: 700; display: inline-flex; align-items: center; gap: 0.5rem;">
                        <span class="badge {{ $item->type_badge['class'] }}">{{ $item->type_badge['icon'] }} {{ $item->type_badge['label'] }}</span>
                        <span style="font-size: 0.8rem; color: var(--text-muted);">(Format tidak dapat diubah pada mode edit)</span>
                    </div>
                @else
                    <select id="type" name="type" required onchange="toggleTypeFields(this.value)"
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem;">
                        <option value="book" {{ old('type', 'book') === 'book' ? 'selected' : '' }}>📚 Rangkuman Buku (Book Summary)</option>
                        <option value="podcast" {{ old('type') === 'podcast' ? 'selected' : '' }}>🎙️ Resume Podcast AI</option>
                        <option value="academic" {{ old('type') === 'academic' ? 'selected' : '' }}>🎓 Karya Ilmiah / Jurnal / Tesis Alumni</option>
                    </select>
                @endif
            </div>

            <!-- Title -->
            <div>
                <label for="title" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Judul Koleksi <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title', $item->title) }}" required
                       placeholder="Contoh: Life 3.0: Being Human in the Age of Artificial Intelligence"
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem;">
            </div>

            <!-- Category -->
            <div>
                <label for="category" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Kategori Topik <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" id="category" name="category" value="{{ old('category', $item->category ?? 'Fundamental AI') }}" required
                       placeholder="Contoh: Prompt Engineering, AI Ethics, Computer Vision, MLOps"
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
            </div>

            <!-- Dynamic Section: BOOK FIELDS -->
            <div id="section-book" style="display: {{ old('type', $item->type ?? 'book') === 'book' ? 'block' : 'none' }}; background: var(--bg-surface-alt); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <div style="font-weight: 800; font-size: 0.95rem; margin-bottom: 1rem; color: var(--primary);">
                    📚 Metadata Buku
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div>
                        <label for="author_name" style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.35rem;">Penulis Buku</label>
                        <input type="text" id="author_name" name="author_name" value="{{ old('author_name', $item->author_name) }}" placeholder="Max Tegmark / Stuart Russell"
                               style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main);">
                    </div>
                    <div>
                        <label for="reading_time" style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.35rem;">Estimasi Waktu Baca</label>
                        <input type="text" id="reading_time" name="reading_time" value="{{ old('reading_time', $item->reading_time) }}" placeholder="10 mnt baca"
                               style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main);">
                    </div>
                </div>
            </div>

            <!-- Dynamic Section: PODCAST FIELDS -->
            <div id="section-podcast" style="display: {{ old('type', $item->type ?? 'book') === 'podcast' ? 'block' : 'none' }}; background: var(--bg-surface-alt); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <div style="font-weight: 800; font-size: 0.95rem; margin-bottom: 1rem; color: var(--accent-cyan);">
                    🎙️ Metadata Podcast
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label for="podcast_source" style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.35rem;">Nama Saluran / Host</label>
                        <input type="text" id="podcast_source" name="podcast_source" value="{{ old('podcast_source', $item->podcast_source) }}" placeholder="Lex Fridman Podcast #410"
                               style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main);">
                    </div>
                    <div>
                        <label for="duration" style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.35rem;">Durasi Audio</label>
                        <input type="text" id="duration" name="duration" value="{{ old('duration', $item->duration) }}" placeholder="2 jam 15 mnt"
                               style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main);">
                    </div>
                </div>
                <div>
                    <label for="media_embed_url" style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Tautan Embed Player (Spotify URL / YouTube Link)
                    </label>
                    <input type="url" id="media_embed_url" name="media_embed_url" value="{{ old('media_embed_url', $item->media_embed_url) }}"
                           placeholder="https://open.spotify.com/episode/... atau https://www.youtube.com/watch?v=..."
                           style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main);">
                </div>
            </div>

            <!-- Dynamic Section: ACADEMIC FIELDS -->
            <div id="section-academic" style="display: {{ old('type', $item->type ?? 'book') === 'academic' ? 'block' : 'none' }}; background: var(--bg-surface-alt); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <div style="font-weight: 800; font-size: 0.95rem; margin-bottom: 1rem; color: var(--accent-emerald);">
                    🎓 Metadata Karya Ilmiah Alumni
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label for="academic_degree" style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.35rem;">Jenjang Karya</label>
                        <select id="academic_degree" name="academic_degree" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main);">
                            <option value="skripsi" {{ old('academic_degree', $item->academic_degree) === 'skripsi' ? 'selected' : '' }}>Skripsi (S1)</option>
                            <option value="tesis" {{ old('academic_degree', $item->academic_degree) === 'tesis' ? 'selected' : '' }}>Tesis (S2)</option>
                            <option value="disertasi" {{ old('academic_degree', $item->academic_degree) === 'disertasi' ? 'selected' : '' }}>Disertasi (S3)</option>
                            <option value="jurnal" {{ old('academic_degree', $item->academic_degree) === 'jurnal' ? 'selected' : '' }}>Artikel Jurnal Terpublikasi</option>
                        </select>
                    </div>
                    <div>
                        <label for="institution" style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.35rem;">Universitas / Lembaga</label>
                        <input type="text" id="institution" name="institution" value="{{ old('institution', $item->institution) }}" placeholder="Institut Teknologi Bandung"
                               style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main);">
                    </div>
                    <div>
                        <label for="publication_year" style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.35rem;">Tahun Terbit</label>
                        <input type="number" id="publication_year" name="publication_year" value="{{ old('publication_year', $item->publication_year) }}" placeholder="2025"
                               style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main);">
                    </div>
                    <div>
                        <label for="co_authors" style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.35rem;">Rekan Penulis / Pembimbing</label>
                        <input type="text" id="co_authors" name="co_authors" value="{{ old('co_authors', $item->co_authors) }}" placeholder="Prof. Dr. ..., dkk."
                               style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main);">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div>
                        <label for="external_url" style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.35rem;">Link Repository / DOI</label>
                        <input type="url" id="external_url" name="external_url" value="{{ old('external_url', $item->external_url) }}" placeholder="https://doi.org/... atau https://repository..."
                               style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main);">
                    </div>
                    <div>
                        <label for="pdf_file" style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.35rem;">Upload Berkas PDF (Maks 15MB)</label>
                        <input type="file" id="pdf_file" name="pdf_file" accept=".pdf"
                               style="width: 100%; font-size: 0.85rem;">
                        @if($item->file_path)
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Berkas saat ini: <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank">Unduh PDF</a></div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Synopsis (Public Teaser) -->
            <div>
                <label for="summary_preview" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Sinopsis / Abstrak (Dapat dibaca oleh Tamu/Guest sebagai Teaser) <span style="color: #ef4444;">*</span>
                </label>
                <textarea id="summary_preview" name="summary_preview" rows="4" required
                          placeholder="Tuliskan intisari pembahasan 2-3 paragraf untuk menarik minat baca pembaca..."
                          style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem;">{{ old('summary_preview', $item->summary_preview) }}</textarea>
            </div>

            <!-- Content (Full summary / Key takeaways - Gated) -->
            <div>
                <label for="content" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Ulasan Lengkap / Poin-Poin Kunci / Transkrip (Eksklusif Member Alumni & Subscriber)
                </label>
                <textarea id="content" name="content" rows="10"
                          placeholder="Jabarkan rangkuman bab per bab, key insights, rekomendasi implementasi AI, atau kutipan penting..."
                          style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem;">{{ old('content', $item->content) }}</textarea>
            </div>

            <!-- Cover Image & Flags -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; align-items: start;">
                <div>
                    <label for="cover_image" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Foto Cover / Poster (Opsional)
                    </label>
                    <input type="file" id="cover_image" name="cover_image" accept="image/*"
                           style="width: 100%; font-size: 0.85rem;">
                    @if($item->cover_image)
                        <div style="margin-top: 0.5rem;">
                            <img src="{{ asset('storage/' . $item->cover_image) }}" alt="Preview" style="height: 80px; border-radius: var(--radius-sm);">
                        </div>
                    @endif
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem; padding: 1rem; background: var(--bg-surface-alt); border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; cursor: pointer;">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $item->is_featured) ? 'checked' : '' }}>
                        <span style="font-weight: 700;">⭐ Jadikan Koleksi Unggulan</span>
                    </label>

                    @if($isEdit)
                        <div style="border-top: 1px solid var(--border-color); padding-top: 0.75rem;">
                            <label for="status" style="display: block; font-size: 0.8rem; font-weight: 700; margin-bottom: 0.25rem;">Status Publikasi</label>
                            <select id="status" name="status" style="width: 100%; padding: 0.4rem 0.6rem; font-size: 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main);">
                                <option value="approved" {{ old('status', $item->status) === 'approved' ? 'selected' : '' }}>Disetujui / Terbit</option>
                                <option value="pending" {{ old('status', $item->status) === 'pending' ? 'selected' : '' }}>Menunggu Kurasi</option>
                                <option value="rejected" {{ old('status', $item->status) === 'rejected' ? 'selected' : '' }}>Ditolak / Perlu Revisi</option>
                            </select>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Submit Button -->
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                <a href="{{ route('admin.library.index') }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Terbitkan ke Pustaka AI' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleTypeFields(type) {
    const bookSec = document.getElementById('section-book');
    const podSec = document.getElementById('section-podcast');
    const acadSec = document.getElementById('section-academic');

    if (bookSec) bookSec.style.display = (type === 'book') ? 'block' : 'none';
    if (podSec) podSec.style.display = (type === 'podcast') ? 'block' : 'none';
    if (acadSec) acadSec.style.display = (type === 'academic') ? 'block' : 'none';
}
</script>
@endsection
