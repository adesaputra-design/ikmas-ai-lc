@extends('layouts.app')

@section('title', 'Ajukan Karya ke Showcase — IKMAS AI')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    <!-- Breadcrumbs -->
    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 2rem;">
        <a href="{{ url('/') }}">Beranda</a>
        <span>/</span>
        <a href="{{ url('/member/dashboard') }}">Area Member</a>
        <span>/</span>
        <span style="color: var(--primary); font-weight: 600;">Ajukan Karya Baru</span>
    </div>

    <div style="max-width: 700px; margin: 0 auto;">
        <div class="card card-elevated" style="padding: 2.5rem;">
            <div style="margin-bottom: 2rem;">
                <span class="badge badge-primary" style="margin-bottom: 0.5rem;">Etalase Komunitas</span>
                <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem;">Ajukan Karya ke Showcase</h1>
                <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">
                    Bagikan proyek, automasi, atau konten yang kamu hasilkan berkat AI. Pengurus IKMAS AI akan meninjau dan mempublikasikannya ke etalase utama agar menginspirasi alumni lain.
                </p>
            </div>

            @if ($errors->any())
                <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: var(--radius-md); padding: 0.875rem 1rem; margin-bottom: 1.5rem; color: #ef4444; font-size: 0.875rem;">
                    @foreach ($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ url('/member/showcase') }}" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1.5rem;">
                @csrf

                <div>
                    <label for="title" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Judul Karya / Proyek <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                           placeholder="Contoh: Bot WA Layanan Pelanggan Toko Alumni"
                           style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem;">
                </div>

                <div>
                    <label for="tools_used" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Tools AI yang Digunakan <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" id="tools_used" name="tools_used" value="{{ old('tools_used') }}" required
                           placeholder="Contoh: ChatGPT, Claude, Midjourney, Python"
                           style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem;">
                    <span style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem; display: block;">Pisahkan dengan tanda koma jika menggunakan lebih dari satu tool.</span>
                </div>

                <div>
                    <label for="project_url" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Tautan Proyek / Demo (Opsional)
                    </label>
                    <input type="url" id="project_url" name="project_url" value="{{ old('project_url') }}"
                           placeholder="https://tokoalumni.com atau link Google Drive / YouTube"
                           style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem;">
                </div>

                <div>
                    <label for="image" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Tangkapan Layar / Gambar Pratinjau (Opsional, Maks 2MB)
                    </label>
                    <input type="file" id="image" name="image" accept="image/*"
                           style="width: 100%; padding: 0.5rem; border: 1px dashed var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface-alt); font-size: 0.85rem;">
                </div>

                <div>
                    <label for="description" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Deskripsi Karya & Cara Kerja <span style="color: #ef4444;">*</span>
                    </label>
                    <textarea id="description" name="description" rows="4" required
                              placeholder="Jelaskan apa yang dibuat, bagaimana proses pembuatannya, dan bagaimana karyamu bekerja..."
                              style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem; line-height: 1.6;">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="impact_story" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Cerita Manfaat & Dampak Nyata (Opsional)
                    </label>
                    <textarea id="impact_story" name="impact_story" rows="3"
                              placeholder="Contoh: Menghemat waktu pembuatan katalog dari 3 hari jadi 2 jam, atau membantu usaha sampingan mendapatkan 10 klien pertama..."
                              style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem; line-height: 1.6;">{{ old('impact_story') }}</textarea>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1rem;">
                    <a href="{{ url('/member/dashboard') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Kirim Karya untuk Ditinjau →</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
