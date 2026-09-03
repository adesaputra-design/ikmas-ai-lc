@extends('layouts.admin')

@section('title', 'Tambah Prompt Baru — IKMAS AI')
@section('page-title', 'Tambah Prompt Baru')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <a href="{{ route('admin.prompts.index') }}" class="btn btn-secondary btn-sm">
            ← Kembali ke Katalog Prompt
        </a>
    </div>

    <div class="card" style="padding: 2rem;">
        <h2 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 1.5rem;">Formulir Template Prompt AI</h2>

        @if ($errors->any())
            <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: var(--radius-md); padding: 0.875rem 1rem; margin-bottom: 1.5rem; color: #ef4444; font-size: 0.875rem;">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.prompts.store') }}" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf

            <div>
                <label for="title" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Judul Prompt <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       placeholder="Contoh: Prompt Penyusun Rencana Konten 30 Hari"
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem;">
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
                <div>
                    <label for="target_role" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Target Peran / Profesi <span style="color: #ef4444;">*</span>
                    </label>
                    <select id="target_role" name="target_role" required
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                        <option value="Umum & Produktivitas" {{ old('target_role') === 'Umum & Produktivitas' ? 'selected' : '' }}>Umum & Produktivitas</option>
                        <option value="Pebisnis & Marketer" {{ old('target_role') === 'Pebisnis & Marketer' ? 'selected' : '' }}>Pebisnis & Marketer</option>
                        <option value="Penulis & Content Creator" {{ old('target_role') === 'Penulis & Content Creator' ? 'selected' : '' }}>Penulis & Content Creator</option>
                        <option value="Pendidik & Guru" {{ old('target_role') === 'Pendidik & Guru' ? 'selected' : '' }}>Pendidik & Guru</option>
                        <option value="Developer & IT" {{ old('target_role') === 'Developer & IT' ? 'selected' : '' }}>Developer & IT</option>
                    </select>
                </div>

                <div>
                    <label for="target_tool" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Target Alat / Model AI <span style="color: #ef4444;">*</span>
                    </label>
                    <select id="target_tool" name="target_tool" required
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                        <option value="ChatGPT" {{ old('target_tool') === 'ChatGPT' ? 'selected' : '' }}>ChatGPT</option>
                        <option value="Claude" {{ old('target_tool') === 'Claude' ? 'selected' : '' }}>Claude</option>
                        <option value="Gemini" {{ old('target_tool') === 'Gemini' ? 'selected' : '' }}>Gemini</option>
                        <option value="Canva / Midjourney" {{ old('target_tool') === 'Canva / Midjourney' ? 'selected' : '' }}>Canva / Midjourney</option>
                        <option value="Cursor / v0" {{ old('target_tool') === 'Cursor / v0' ? 'selected' : '' }}>Cursor / v0</option>
                    </select>
                </div>
            </div>

            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                    <label for="prompt_text" style="font-size: 0.875rem; font-weight: 700;">
                        Teks Prompt Berparameter <span style="color: #ef4444;">*</span>
                    </label>
                    <span style="font-size: 0.75rem; color: var(--primary);">Tips: Gunakan kurung siku seperti [Topik] atau [Target Audiens]</span>
                </div>
                <textarea id="prompt_text" name="prompt_text" rows="6" required
                          placeholder="Bertindaklah sebagai ahli strategi pemasaran. Buatkan rencana kalender konten 30 hari untuk bisnis di bidang [Industri/Niche] yang menargetkan audiens [Target Audiens]..."
                          style="width: 100%; padding: 0.75rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: monospace; font-size: 0.95rem; line-height: 1.6;">{{ old('prompt_text') }}</textarea>
            </div>

            <div>
                <label for="instruction" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Petunjuk Penggunaan bagi Alumni (Opsional)
                </label>
                <textarea id="instruction" name="instruction" rows="3"
                          placeholder="Contoh: Jalankan di model GPT-4o untuk hasil maksimal, lalu ganti bagian di dalam kurung siku dengan data tokomu."
                          style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem; line-height: 1.5;">{{ old('instruction') }}</textarea>
            </div>

            <div>
                <label for="tags" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Tags / Kata Kunci (Pisahkan dengan koma)
                </label>
                <input type="text" id="tags" name="tags" value="{{ old('tags') }}"
                       placeholder="marketing, instagram, content calendar"
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
            </div>

            <div style="padding: 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface-alt); display: flex; align-items: center; gap: 0.75rem;">
                <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} style="width: 1.25rem; height: 1.25rem; accent-color: var(--primary);">
                <label for="is_featured" style="font-size: 0.9rem; font-weight: 600; cursor: pointer;">
                    ⭐ Tandai sebagai Prompt Unggulan (Tampil prioritas di halaman depan)
                </label>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1rem;">
                <a href="{{ route('admin.prompts.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Prompt Baru</button>
            </div>
        </form>
    </div>
</div>
@endsection
