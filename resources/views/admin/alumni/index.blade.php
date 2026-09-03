@extends('layouts.admin')

@section('title', 'Direktori Member Alumni — IKMAS AI')
@section('page-title', 'Direktori Member Alumni')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 800; margin: 0;">Direktori Member Alumni</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0.25rem 0 0 0;">
            Daftar seluruh alumni Assalaam yang terdaftar di portal IKMAS AI Learning Center.
        </p>
    </div>
    <div>
        <a href="{{ route('admin.alumni.export', request()->query()) }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Unduh CSV / Excel</span>
        </a>
    </div>
</div>

<!-- Filters Bar -->
<div class="card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
    <form method="GET" action="{{ route('admin.alumni.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, atau no. WA..."
               style="flex: 1; min-width: 220px; padding: 0.5rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">

        <select name="alumni_year" style="padding: 0.5rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
            <option value="">Semua Angkatan</option>
            @foreach($alumniYears as $year)
                <option value="{{ $year }}" {{ request('alumni_year') == $year ? 'selected' : '' }}>
                    Angkatan {{ $year }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        @if(request()->anyFilled(['q', 'alumni_year']))
            <a href="{{ route('admin.alumni.index') }}" class="btn btn-secondary btn-sm">Reset</a>
        @endif
    </form>
</div>

<!-- Table Card -->
<div class="card" style="padding: 0; overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
        <thead>
            <tr style="background: var(--bg-surface-alt); border-bottom: 1px solid var(--border-color);">
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Nama Alumni</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Alamat Email</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">WhatsApp</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Angkatan</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Karya</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Role</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Bergabung</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $u)
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 1rem 1.25rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 2.25rem; height: 2.25rem; border-radius: 50%; background: linear-gradient(135deg, #1e40af, #0284c7); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.85rem; flex-shrink: 0;">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div style="font-weight: 700; color: var(--text-main);">
                                {{ $u->name }}
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1rem 1.25rem; color: var(--text-muted); font-size: 0.85rem;">
                        {{ $u->email }}
                    </td>
                    <td style="padding: 1rem 1.25rem;">
                        @if($u->whatsapp_number)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $u->whatsapp_number) }}" target="_blank" rel="noopener"
                               class="btn btn-whatsapp btn-sm" style="padding: 0.25rem 0.55rem; font-size: 0.75rem;" title="Buka WhatsApp">
                                💬 {{ $u->whatsapp_number }}
                            </a>
                        @else
                            <span style="color: var(--text-muted); font-size: 0.85rem;">-</span>
                        @endif
                    </td>
                    <td style="padding: 1rem 1.25rem;">
                        @if($u->alumni_year)
                            <span class="badge badge-primary" style="font-size: 0.75rem;">{{ $u->alumni_year }}</span>
                        @else
                            <span style="color: var(--text-muted); font-size: 0.85rem;">-</span>
                        @endif
                    </td>
                    <td style="padding: 1rem 1.25rem; font-size: 0.85rem;">
                        @if($u->showcases_count > 0)
                            <span class="badge badge-emerald" style="font-size: 0.75rem;">{{ $u->showcases_count }} karya</span>
                        @else
                            <span style="color: var(--text-muted);">0</span>
                        @endif
                    </td>
                    <td style="padding: 1rem 1.25rem;">
                        @if($u->role === 'admin')
                            <span class="badge badge-cyan" style="font-size: 0.75rem;">Pengurus</span>
                        @else
                            <span class="badge" style="background: var(--bg-surface-alt); color: var(--text-muted); font-size: 0.75rem;">Member</span>
                        @endif
                    </td>
                    <td style="padding: 1rem 1.25rem; color: var(--text-muted); font-size: 0.85rem;">
                        {{ $u->created_at ? $u->created_at->format('d/m/Y') : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                        Belum ada member alumni yang terdaftar sesuai kriteria pencarian.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 1.5rem; display: flex; justify-content: center;">
    {{ $users->links() }}
</div>
@endsection
