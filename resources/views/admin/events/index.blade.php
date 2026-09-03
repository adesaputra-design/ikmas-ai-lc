@extends('layouts.admin')

@section('title', 'Kelola Agenda Kegiatan — IKMAS AI')
@section('page-title', 'Agenda Kegiatan')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 800; margin: 0;">Jadwal Study Group & Sharing Session</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0.25rem 0 0 0;">
            Kelola jadwal sesi belajar, ruang virtual Zoom/Meet, dan arsip rekaman video.
        </p>
    </div>
    <a href="{{ route('admin.agenda.create') }}" class="btn btn-primary">
        + Jadwalkan Event Baru
    </a>
</div>

<!-- Filters Bar -->
<div class="card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
    <form method="GET" action="{{ route('admin.agenda.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul, topik, atau pemateri..."
               style="flex: 1; min-width: 200px; padding: 0.5rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">

        <select name="status" style="padding: 0.5rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
            <option value="">Semua Status</option>
            <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Akan Datang (Upcoming)</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
        </select>

        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        @if(request()->anyFilled(['q', 'status']))
            <a href="{{ route('admin.agenda.index') }}" class="btn btn-secondary btn-sm">Reset</a>
        @endif
    </form>
</div>

<!-- Table Card -->
<div class="card" style="padding: 0; overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
        <thead>
            <tr style="background: var(--bg-surface-alt); border-bottom: 1px solid var(--border-color);">
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Acara & Topik</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Waktu & Durasi</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Pemateri</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Status</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right;">Aksi & Broadcast</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $item)
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 1rem 1.25rem;">
                        <div style="font-weight: 700; color: var(--text-main); margin-bottom: 0.2rem;">
                            {{ $item->title }}
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">
                            Topik: {{ $item->topic }}
                        </div>
                    </td>
                    <td style="padding: 1rem 1.25rem;">
                        <div style="font-weight: 600; font-size: 0.85rem;">
                            🗓 {{ $item->formatted_date }}
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">
                            ⏰ {{ $item->formatted_time }} WIB (~{{ $item->duration_minutes }} mnt)
                        </div>
                    </td>
                    <td style="padding: 1rem 1.25rem;">
                        <div style="font-weight: 600; font-size: 0.85rem;">{{ $item->speaker_name }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $item->speaker_title ?? '-' }}</div>
                    </td>
                    <td style="padding: 1rem 1.25rem;">
                        <span class="badge badge-{{ $item->status_color }}" style="font-size: 0.75rem;">
                            {{ $item->status_label }}
                        </span>
                    </td>
                    <td style="padding: 1rem 1.25rem; text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center; flex-wrap: wrap;">
                            <!-- WA Broadcast Copy Button -->
                            <button type="button" class="btn btn-whatsapp btn-sm" onclick="copyWABroadcast({{ $item->id }}, this)" style="padding: 0.35rem 0.65rem; font-size: 0.75rem;" title="Salin template pengumuman WhatsApp">
                                💬 Format Siar WA
                            </button>

                            <a href="{{ route('admin.agenda.edit', $item->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.65rem; font-size: 0.75rem;">
                                Edit
                            </a>

                            <form action="{{ route('admin.agenda.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus agenda ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="padding: 0.35rem 0.65rem; font-size: 0.75rem; color: #ef4444; border: 1px solid rgba(239,68,68,0.2); background: transparent;">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                        Belum ada agenda yang sesuai dengan filter pencarian.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 1.5rem; display: flex; justify-content: center;">
    {{ $events->links() }}
</div>
@endsection

@section('scripts')
<script>
async function copyWABroadcast(eventId, btnElement) {
    const originalText = btnElement.innerHTML;
    btnElement.innerHTML = '⏳ Menyiapkan...';
    btnElement.disabled = true;

    try {
        const response = await fetch(`/admin/agenda/${eventId}/broadcast-text`);
        const data = await response.json();
        
        if (data && data.broadcast_text) {
            await navigator.clipboard.writeText(data.broadcast_text);
            btnElement.innerHTML = '✓ Tersalin!';
            btnElement.classList.remove('btn-whatsapp');
            btnElement.classList.add('btn-primary');

            if (typeof showToast === 'function') {
                showToast('Teks siaran WhatsApp berhasil disalin ke clipboard! Siap di-paste ke grup.', 'success');
            } else {
                alert('Teks siaran WhatsApp berhasil disalin ke clipboard!');
            }

            setTimeout(() => {
                btnElement.innerHTML = originalText;
                btnElement.classList.remove('btn-primary');
                btnElement.classList.add('btn-whatsapp');
                btnElement.disabled = false;
            }, 3000);
        }
    } catch (err) {
        console.error('Error copying broadcast text:', err);
        btnElement.innerHTML = originalText;
        btnElement.disabled = false;
        alert('Gagal mengambil teks siaran. Silakan coba kembali.');
    }
}
</script>
@endsection
