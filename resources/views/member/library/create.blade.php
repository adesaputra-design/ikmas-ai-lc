@extends('layouts.app')

@section('title', 'Ajukan Karya Ilmiah Alumni — Pustaka AI IKMAS')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    <!-- Breadcrumbs -->
    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 2rem;">
        <a href="{{ url('/') }}" style="color: inherit; text-decoration: none;">Beranda</a>
        <span>/</span>
        <a href="{{ route('member.dashboard') }}" style="color: inherit; text-decoration: none;">Area Member</a>
        <span>/</span>
        <a href="{{ route('library.index') }}" style="color: inherit; text-decoration: none;">Pustaka AI</a>
        <span>/</span>
        <span style="color: var(--primary); font-weight: 600;">Ajukan Karya Ilmiah</span>
    </div>

    <div style="max-width: 740px; margin: 0 auto;">
        <div class="card card-elevated" style="padding: 2.5rem;">
            <div style="margin-bottom: 2rem;">
                <span class="badge badge-amber" style="margin-bottom: 0.5rem;">🎓 Repositori Riset Alumni</span>
                <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; color: var(--text-main);">
                    Ajukan Karya Ilmiah Alumni
                </h1>
                <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">
                    Dokumentasikan karya riset akademik Anda (Skripsi, Tesis, Disertasi, atau Jurnal Ilmiah) di Pustaka AI IKMAS. Karya Anda akan dikurasi oleh tim pengurus sebelum tayang di pustaka komunitas.
                </p>
            </div>

            @if ($errors->any())
                <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: var(--radius-md); padding: 0.875rem 1rem; margin-bottom: 1.5rem; color: #ef4444; font-size: 0.875rem;">
                    @foreach ($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('member.library.store') }}" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1.5rem;">
                @csrf

                <!-- Judul -->
                <div>
                    <label for="title" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Judul Karya Ilmiah / Riset <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                           placeholder="Contoh: Rancang Bangun Sistem Temu Kembali Informasi Fikih Berbasis RAG dan LLM"
                           style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem;">
                </div>

                <!-- Grid Jenjang & Institusi -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label for="academic_degree" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                            Jenjang / Jenis Karya <span style="color: #ef4444;">*</span>
                        </label>
                        <select id="academic_degree" name="academic_degree" required
                                style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                            <option value="">Pilih Jenis...</option>
                            <option value="skripsi" {{ old('academic_degree') === 'skripsi' ? 'selected' : '' }}>Skripsi (S1)</option>
                            <option value="tesis" {{ old('academic_degree') === 'tesis' ? 'selected' : '' }}>Tesis (S2)</option>
                            <option value="disertasi" {{ old('academic_degree') === 'disertasi' ? 'selected' : '' }}>Disertasi (S3)</option>
                            <option value="jurnal" {{ old('academic_degree') === 'jurnal' ? 'selected' : '' }}>Jurnal Ilmiah / Paper</option>
                        </select>
                    </div>

                    <div>
                        <label for="institution" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                            Institusi / Universitas <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" id="institution" name="institution" value="{{ old('institution') }}" required
                               placeholder="Contoh: ITB, UGM, Al-Azhar, UI"
                               style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                    </div>
                </div>

                <!-- Grid Kategori & Tahun -->
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                    <div>
                        <label for="category" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                            Topik / Bidang AI <span style="color: #ef4444;">*</span>
                        </label>
                        <select id="category" name="category" required
                                style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                            <option value="">Pilih Topik...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="publication_year" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                            Tahun Terbit / Sidang
                        </label>
                        <input type="number" id="publication_year" name="publication_year" value="{{ old('publication_year', date('Y')) }}"
                               min="1980" max="{{ date('Y') + 1 }}"
                               style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                    </div>
                </div>

                <!-- Co-authors -->
                <div>
                    <label for="co_authors" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Rekan Penulis / Dosen Pembimbing (Opsional)
                    </label>
                    <input type="text" id="co_authors" name="co_authors" value="{{ old('co_authors') }}"
                           placeholder="Contoh: Prof. Dr. Budi, M.Kom., Ahmad Fauzi"
                           style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                </div>

                <!-- Abstrak / Sinopsis Singkat -->
                <div>
                    <label for="summary_preview" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Abstrak / Sinopsis Singkat <span style="color: #ef4444;">*</span>
                    </label>
                    <textarea id="summary_preview" name="summary_preview" rows="4" required
                              placeholder="Tuliskan intisari masalah yang diteliti, metode yang digunakan, dan kesimpulan utama penelitian..."
                              style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem; line-height: 1.5;">{{ old('summary_preview') }}</textarea>
                    <span style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem; display: block;">Bagian ini akan ditampilkan sebagai cuplikan awal untuk pembaca.</span>
                </div>

                <!-- Isi Lengkap / Uraian Tambahan -->
                <div>
                    <label for="content" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Uraian Detail / Key Takeaways Riset (Opsional)
                    </label>
                    <textarea id="content" name="content" rows="6"
                              placeholder="Rincian bab, metodologi langkah demi langkah, dataset yang digunakan, atau saran penerapan praktis..."
                              style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem; line-height: 1.5;">{{ old('content') }}</textarea>
                </div>

                <!-- Upload PDF Naskah -->
                <div style="padding: 1.25rem; background: var(--bg-surface-alt); border-radius: var(--radius-md); border: 1px dashed var(--border-color);">
                    <label for="pdf_file" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Unggah Berkas Naskah PDF (Maks. 10MB)
                    </label>
                    <input type="file" id="pdf_file" name="pdf_file" accept="application/pdf"
                           style="width: 100%; font-size: 0.85rem; color: var(--text-muted);">
                    <span style="font-size: 0.775rem; color: var(--text-muted); margin-top: 0.35rem; display: block;">
                        Berkas naskah lengkap hanya akan dapat diunduh oleh member dan subscriber aktif.
                    </span>
                </div>

                <!-- Link Repositori / DOI -->
                <div>
                    <label for="external_url" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Tautan Repositori Kampus / DOI / Google Scholar (Opsional)
                    </label>
                    <input type="url" id="external_url" name="external_url" value="{{ old('external_url') }}"
                           placeholder="Contoh: https://doi.org/10.1016/... atau https://repository.itb.ac.id/..."
                           style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                </div>

                <!-- Cover Image -->
                <div>
                    <label for="cover_image" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Gambar Ilustrasi / Diagram Riset (Opsional, Maks. 2MB)
                    </label>
                    <input type="file" id="cover_image" name="cover_image" accept="image/*"
                           style="width: 100%; font-size: 0.85rem; color: var(--text-muted);">
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1rem;">
                    <a href="{{ route('member.dashboard') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.75rem;">
                        🚀 Ajukan Karya Ilmiah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
