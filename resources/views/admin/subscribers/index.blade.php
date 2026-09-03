@extends('layouts.admin')

@section('title', 'Kelola Subscriber — IKMAS AI')
@section('page-title', 'Kelola Subscriber')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 800; margin: 0;">Kelola Subscriber</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0.25rem 0 0 0;">
            Tinjau dan kelola pendaftaran subscriber non-alumni.
        </p>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.25); border-radius: var(--radius-md); padding: 0.875rem 1rem; margin-bottom: 1.25rem; color: #10b981; font-size: 0.875rem;">
        ✓ {{ session('success') }}
    </div>
@endif
@if(session('info'))
    <div style="background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.25); border-radius: var(--radius-md); padding: 0.875rem 1rem; margin-bottom: 1.25rem; color: #3b82f6; font-size: 0.875rem;">
        ℹ {{ session('info') }}
    </div>
@endif
@if(session('error'))
    <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: var(--radius-md); padding: 0.875rem 1rem; margin-bottom: 1.25rem; color: #ef4444; font-size: 0.875rem;">
        ✗ {{ session('error') }}
    </div>
@endif

{{-- Filter Tabs --}}
<div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; flex-wrap: wrap;">
    @foreach(['pending' => 'Menunggu Tinjauan', 'active' => 'Aktif', 'rejected' => 'Ditolak', 'all' => 'Semua'] as $key => $label)
        <a href="{{ route('admin.subscribers.index', ['filter' => $key]) }}"
           class="btn {{ $filter === $key ? 'btn-primary' : 'btn-secondary' }} btn-sm"
           style="border-radius: 999px; padding: 0.4rem 1rem;">
            {{ $label }} ({{ $counts[$key] ?? 0 }})
        </a>
    @endforeach
</div>

{{-- Table --}}
<div class="card" style="padding: 0; overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
        <thead>
            <tr style="background: var(--bg-surface-alt); border-bottom: 1px solid var(--border-color);">
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Nama</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Email</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">WhatsApp</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Tanggal Daftar</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Status</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subscribers as $subscriber)
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 1rem 1.25rem; font-weight: 600;">{{ $subscriber->name }}</td>
                <td style="padding: 1rem 1.25rem; color: var(--text-muted);">{{ $subscriber->email }}</td>
                <td style="padding: 1rem 1.25rem;">
                    @if($subscriber->whatsapp_number)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $subscriber->whatsapp_number) }}"
                           target="_blank" style="color: var(--primary);">
                            {{ $subscriber->whatsapp_number }}
                        </a>
                    @else
                        -
                    @endif
                </td>
                <td style="padding: 1rem 1.25rem; color: var(--text-muted); font-size: 0.85rem;">
                    {{ $subscriber->created_at ? $subscriber->created_at->format('d M Y, H:i') : '-' }}
                </td>
                <td style="padding: 1rem 1.25rem;">
                    @if($subscriber->status === 'pending')
                        <span style="background: rgba(245,158,11,0.15); color: #f59e0b; border-radius: 999px; padding: 0.2rem 0.65rem; font-size: 0.78rem; font-weight: 700;">Menunggu</span>
                    @elseif($subscriber->status === 'active')
                        <span style="background: rgba(16,185,129,0.15); color: #10b981; border-radius: 999px; padding: 0.2rem 0.65rem; font-size: 0.78rem; font-weight: 700;">Aktif</span>
                    @else
                        <span style="background: rgba(239,68,68,0.15); color: #ef4444; border-radius: 999px; padding: 0.2rem 0.65rem; font-size: 0.78rem; font-weight: 700;">Ditolak</span>
                    @endif
                </td>
                <td style="padding: 1rem 1.25rem; text-align: right;">
                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end; flex-wrap: wrap;">
                        @if($subscriber->status === 'pending')
                            <form method="POST" action="{{ route('admin.subscribers.approve', $subscriber) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3);">
                                    ✓ Setujui
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.subscribers.reject', $subscriber) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.25);"
                                        onclick="return confirm('Yakin ingin menolak pendaftaran {{ $subscriber->name }}?')">
                                    ✗ Tolak
                                </button>
                            </form>
                        @elseif($subscriber->status === 'rejected')
                            <form method="POST" action="{{ route('admin.subscribers.approve', $subscriber) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3);">
                                    ↩ Aktifkan
                                </button>
                            </form>
                        @endif

                        @if(auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('admin.subscribers.destroy', $subscriber) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-secondary"
                                    onclick="return confirm('Hapus akun {{ $subscriber->name }} secara permanen?')">
                                Hapus
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                    Tidak ada subscriber dengan filter ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($subscribers->hasPages())
<div style="margin-top: 1.5rem;">
    {{ $subscribers->links() }}
</div>
@endif

@endsection
