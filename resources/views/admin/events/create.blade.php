@extends('layouts.admin')

@section('title', 'Jadwalkan Event Baru — IKMAS AI')
@section('page-title', 'Jadwalkan Event Baru')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <a href="{{ route('admin.agenda.index') }}" class="btn btn-secondary btn-sm">
            ← Kembali ke Jadwal Agenda
        </a>
    </div>

    <div class="card" style="padding: 2rem;">
        <h2 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 1.5rem;">Rincian Agenda Sesi Komunitas</h2>

        @if ($errors->any())
            <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: var(--radius-md); padding: 0.875rem 1rem; margin-bottom: 1.5rem; color: #ef4444; font-size: 0.875rem;">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.agenda.store') }}" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf

            <div>
                <label for="title" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Judul Acara <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       placeholder="Contoh: AI Study Group Sesi #2: Automasi Workflow dengan Make.com"
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem;">
            </div>

            <div>
                <label for="topic" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Topik / Sub-Tema <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" id="topic" name="topic" value="{{ old('topic') }}" required
                       placeholder="Contoh: AI Workflow & Productivity"
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                <div>
                    <label for="event_date" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Waktu Pelaksanaan (Tanggal & Jam) <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="datetime-local" id="event_date" name="event_date" value="{{ old('event_date') }}" required
                           style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                </div>

                <div>
                    <label for="duration_minutes" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Perkiraan Durasi (Menit) <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="number" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" min="15" required
                           style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                </div>

                <div>
                    <label for="status" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Status Sesi <span style="color: #ef4444;">*</span>
                    </label>
                    <select id="status" name="status" required
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                        <option value="upcoming" {{ old('status') === 'upcoming' ? 'selected' : '' }}>Akan Datang (Upcoming)</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                <div>
                    <label for="speaker_name" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Nama Pemateri / Fasilitator <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" id="speaker_name" name="speaker_name" value="{{ old('speaker_name') }}" required
                           placeholder="Contoh: Muhammad Reza, S.Kom."
                           style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                </div>

                <div>
                    <label for="speaker_title" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Gelar / Keterangan Pemateri
                    </label>
                    <input type="text" id="speaker_title" name="speaker_title" value="{{ old('speaker_title') }}"
                           placeholder="Contoh: AI Engineer & Alumni Assalaam 2014"
                           style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                </div>
            </div>

            <div>
                <label for="location_url" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Tautan Ruang Virtual (Zoom / Google Meet)
                </label>
                <input type="url" id="location_url" name="location_url" value="{{ old('location_url') }}"
                       placeholder="https://meet.google.com/abc-defg-hij atau link Zoom"
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
            </div>

            <div>
                <label for="description" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Deskripsi & Garis Besar Sesi <span style="color: #ef4444;">*</span>
                </label>
                <textarea id="description" name="description" rows="5" required
                          placeholder="Jelaskan poin-poin yang akan dipelajari, siapa yang cocok hadir, dan persiapan yang dibutuhkan..."
                          style="width: 100%; padding: 0.75rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem; line-height: 1.6;">{{ old('description') }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                <div>
                    <label for="recording_url" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Tautan Rekaman Video (Jika Sesi Sudah Lewat)
                    </label>
                    <input type="url" id="recording_url" name="recording_url" value="{{ old('recording_url') }}"
                           placeholder="https://youtube.com/watch?v=..."
                           style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
                </div>

                <div>
                    <label for="materials_url" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Tautan Slide / Materi
                    </label>
                    <input type="text" id="materials_url" name="materials_url" value="{{ old('materials_url') }}"
                           placeholder="https://drive.google.com/..."
                           style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1rem;">
                <a href="{{ route('admin.agenda.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Jadwalkan Acara</button>
            </div>
        </form>
    </div>
</div>
@endsection
