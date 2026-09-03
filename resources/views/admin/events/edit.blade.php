@extends('layouts.admin')

@section('title', 'Edit Agenda Kegiatan — IKMAS AI')
@section('page-title', 'Edit Agenda Kegiatan')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <a href="{{ route('admin.agenda.index') }}" class="btn btn-secondary btn-sm">
            ← Kembali ke Jadwal Agenda
        </a>
        <a href="{{ url('/agenda/' . $agenda->slug) }}" target="_blank" class="btn btn-secondary btn-sm">
            Lihat di Web Publik ↗
        </a>
    </div>

    <div class="card" style="padding: 2rem;">
        <h2 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 1.5rem;">Perbarui Rincian Agenda</h2>

        @if ($errors->any())
            <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: var(--radius-md); padding: 0.875rem 1rem; margin-bottom: 1.5rem; color: #ef4444; font-size: 0.875rem;">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.agenda.update', $agenda->id) }}" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            @method('PUT')

            <div>
                <label for="title" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Judul Acara <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title', $agenda->title) }}" required
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem;">
            </div>

            <div>
                <label for="topic" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Topik / Sub-Tema <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" id="topic" name="topic" value="{{ old('topic', $agenda->topic) }}" required
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                <div>
                    <label for="event_date" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Waktu Pelaksanaan (Tanggal & Jam) <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="datetime-local" id="event_date" name="event_date" 
                           value="{{ old('event_date', $agenda->event_date ? \Carbon\Carbon::parse($agenda->event_date)->format('Y-m-d\TH:i') : '') }}" required
                           style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                </div>

                <div>
                    <label for="duration_minutes" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Perkiraan Durasi (Menit) <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="number" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $agenda->duration_minutes) }}" min="15" required
                           style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                </div>

                <div>
                    <label for="status" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Status Sesi <span style="color: #ef4444;">*</span>
                    </label>
                    <select id="status" name="status" required
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                        <option value="upcoming" {{ old('status', $agenda->status) === 'upcoming' ? 'selected' : '' }}>Akan Datang (Upcoming)</option>
                        <option value="completed" {{ old('status', $agenda->status) === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                <div>
                    <label for="speaker_name" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Nama Pemateri / Fasilitator <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" id="speaker_name" name="speaker_name" value="{{ old('speaker_name', $agenda->speaker_name) }}" required
                           style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                </div>

                <div>
                    <label for="speaker_title" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Gelar / Keterangan Pemateri
                    </label>
                    <input type="text" id="speaker_title" name="speaker_title" value="{{ old('speaker_title', $agenda->speaker_title) }}"
                           style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                </div>
            </div>

            <div>
                <label for="location_url" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Tautan Ruang Virtual (Zoom / Google Meet)
                </label>
                <input type="url" id="location_url" name="location_url" value="{{ old('location_url', $agenda->location_url) }}"
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
            </div>

            <div>
                <label for="description" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                    Deskripsi & Garis Besar Sesi <span style="color: #ef4444;">*</span>
                </label>
                <textarea id="description" name="description" rows="5" required
                          style="width: 100%; padding: 0.75rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.95rem; line-height: 1.6;">{{ old('description', $agenda->description) }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                <div>
                    <label for="recording_url" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Tautan Rekaman Video (Jika Sesi Sudah Lewat)
                    </label>
                    <input type="url" id="recording_url" name="recording_url" value="{{ old('recording_url', $agenda->recording_url) }}"
                           placeholder="https://youtube.com/watch?v=..."
                           style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
                </div>

                <div>
                    <label for="materials_url" style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">
                        Tautan Slide / Materi
                    </label>
                    <input type="text" id="materials_url" name="materials_url" value="{{ old('materials_url', $agenda->materials_url) }}"
                           placeholder="https://drive.google.com/..."
                           style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1rem;">
                <a href="{{ route('admin.agenda.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Agenda</button>
            </div>
        </form>
    </div>
</div>
@endsection
