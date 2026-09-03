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

<!-- Tabs Status: Aktif vs Dinonaktifkan -->
<div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
    <a href="{{ route('admin.alumni.index', array_merge(request()->except(['page', 'status']), ['status' => 'active'])) }}" 
       class="btn {{ ($status ?? 'active') !== 'trashed' ? 'btn-primary' : 'btn-secondary' }} btn-sm"
       style="border-radius: 999px; padding: 0.4rem 1rem;">
        Member Aktif ({{ $totalMembers ?? 0 }})
    </a>
    <a href="{{ route('admin.alumni.index', array_merge(request()->except(['page', 'status']), ['status' => 'trashed'])) }}" 
       class="btn {{ ($status ?? '') === 'trashed' ? 'btn-primary' : 'btn-secondary' }} btn-sm"
       style="border-radius: 999px; padding: 0.4rem 1rem;">
        Dinonaktifkan ({{ $totalTrashed ?? 0 }})
    </a>
</div>

<!-- Filters Bar -->
<div class="card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
    <form method="GET" action="{{ route('admin.alumni.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
        <input type="hidden" name="status" value="{{ $status ?? 'active' }}">
        
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

        <select name="role" style="padding: 0.5rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
            <option value="">Semua Peran</option>
            <option value="member" {{ request('role') == 'member' ? 'selected' : '' }}>Member Alumni</option>
            <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staf Pengurus</option>
            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
        </select>

        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        @if(request()->anyFilled(['q', 'alumni_year', 'role']))
            <a href="{{ route('admin.alumni.index', ['status' => $status ?? 'active']) }}" class="btn btn-secondary btn-sm">Reset</a>
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
                <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right;">Aksi</th>
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
                                @if($u->id === auth()->id())
                                    <span class="badge badge-amber" style="font-size: 0.65rem; padding: 0.1rem 0.35rem;">Anda</span>
                                @endif
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
                        @php $badge = $u->role_badge; @endphp
                        <span class="badge {{ $badge['class'] }}" style="font-size: 0.75rem;">
                            {{ $badge['label'] }}
                        </span>
                    </td>
                    <td style="padding: 1rem 1.25rem; color: var(--text-muted); font-size: 0.85rem;">
                        {{ $u->created_at ? $u->created_at->format('d/m/Y') : '-' }}
                    </td>
                    <td style="padding: 1rem 1.25rem; text-align: right;">
                        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 0.4rem;">
                            @if(($status ?? 'active') === 'trashed')
                                @if(auth()->user()->isAdmin())
                                    <form action="{{ route('admin.alumni.restore', $u->id) }}" method="POST" onsubmit="return confirm('Pulihkan akun {{ $u->name }}?')">
                                        @csrf
                                        <button type="submit" class="btn btn-emerald btn-sm" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">
                                            ♻️ Pulihkan
                                        </button>
                                    </form>
                                @endif
                            @else
                                {{-- Tombol Reset Password (Admin bisa reset siapa saja, Staf hanya akun Member) --}}
                                @if(auth()->user()->isAdmin() || $u->role === 'member')
                                    <button type="button" 
                                            class="btn btn-secondary btn-sm" 
                                            style="padding: 0.35rem 0.65rem; font-size: 0.75rem;"
                                            title="Reset Kata Sandi"
                                            onclick="openResetPasswordModal('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ route('admin.alumni.reset-password', $u) }}')">
                                        🔑 Reset
                                    </button>
                                @endif

                                @if(auth()->user()->isAdmin() && $u->id !== auth()->id() && !$u->isAdmin())
                                    <button type="button" 
                                            class="btn btn-sm" 
                                            style="padding: 0.35rem 0.6rem; font-size: 0.75rem; border: 1px solid rgba(239,68,68,0.3); color: #ef4444; background: rgba(239,68,68,0.05);"
                                            onclick="openAlumniDeleteModal('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ $u->showcases_count }}', '{{ route('admin.alumni.destroy', $u) }}')">
                                        🗑️
                                    </button>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="padding: 3rem; text-align: center; color: var(--text-muted);">
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

<!-- Modal Reset Password -->
<div id="resetPasswordModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1060; align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="card" style="width: 100%; max-width: 480px; padding: 1.75rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 1.25rem;">🔑</span>
                <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0;">Reset Kata Sandi Member</h3>
            </div>
            <button type="button" onclick="closeResetPasswordModal()" style="background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer;">✕</button>
        </div>

        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
            Member: <strong id="resetTargetName" style="color: var(--text-main);"></strong>
        </p>

        <form id="resetPasswordForm" method="POST" action="">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label for="new_password" style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.4rem;">
                    Kata Sandi Baru:
                </label>
                <div style="display: flex; gap: 0.5rem;">
                    <input type="text" id="new_password" name="password" required minlength="8" placeholder="Minimal 8 karakter..."
                           style="flex: 1; padding: 0.55rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: monospace; font-size: 0.9rem;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="generateRandomPassword()" title="Buat password acak">
                        🎲 Acak
                    </button>
                </div>
            </div>

            <div style="background: var(--bg-surface-alt); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.85rem; margin-bottom: 1.25rem;">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.35rem;">
                    KIRIM INFO KE MEMBER (WHATSAPP):
                </div>
                <p id="waPreviewText" style="font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 0.5rem; font-family: monospace; white-space: pre-line;">
                    Halo [Nama], kata sandi akun IKMAS AI kamu telah direset. Kata sandi baru: ...
                </p>
                <button type="button" class="btn btn-whatsapp btn-sm" style="padding: 0.3rem 0.65rem; font-size: 0.75rem;" onclick="copyWaText()">
                    📋 Salin Pesan WhatsApp
                </button>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="closeResetPasswordModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Password Baru</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Nonaktifkan / Hapus -->
@if(auth()->user()->isAdmin())
<div id="alumniDeleteModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1060; align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="card" style="width: 100%; max-width: 480px; padding: 1.75rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.3);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; color: #ef4444;">
            <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: rgba(239,68,68,0.1); display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                ⚠️
            </div>
            <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0;">Nonaktifkan Akun Anggota?</h3>
        </div>

        <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1rem;">
            Apakah Anda yakin ingin menonaktifkan akun <strong id="alumniDeleteName" style="color: var(--text-main);"></strong>?
        </p>

        <div style="background: rgba(239,68,68,0.05); border: 1px solid rgba(239,68,68,0.2); border-radius: var(--radius-md); padding: 0.85rem; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;">
            <div>• Akun tidak akan dapat login lagi ke portal.</div>
            <div>• <strong id="alumniShowcaseCount"></strong> karya showcase miliknya otomatis disembunyikan dari web publik.</div>
            <div>• Data tetap aman dan dapat Anda <strong>pulihkan (restore)</strong> kapan saja.</div>
        </div>

        <form id="alumniDeleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="closeAlumniDeleteModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary" style="background: #ef4444; border-color: #ef4444;">
                    Ya, Nonaktifkan Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@section('scripts')
<script>
    let currentResetName = '';

    function openResetPasswordModal(id, name, actionUrl) {
        currentResetName = name;
        document.getElementById('resetTargetName').innerText = name;
        document.getElementById('resetPasswordForm').action = actionUrl;
        generateRandomPassword();
        document.getElementById('resetPasswordModal').style.display = 'flex';
    }

    function closeResetPasswordModal() {
        document.getElementById('resetPasswordModal').style.display = 'none';
    }

    function generateRandomPassword() {
        const chars = 'abcdefghjkmnpqrstuvwxyz23456789';
        let pass = 'ikmas-';
        for (let i = 0; i < 5; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        const input = document.getElementById('new_password');
        input.value = pass;
        updateWaPreview(pass);
    }

    function updateWaPreview(pass) {
        const text = `Assalamu'alaikum ${currentResetName}, kata sandi akun portal IKMAS AI kamu telah direset oleh pengurus.\n\nKata sandi baru: ${pass}\n\nSilakan segera login di https://ai.ikmas.com/login dan kamu dapat mengganti kata sandi di Dasbor Akun kamu. Terima kasih!`;
        document.getElementById('waPreviewText').innerText = text;
    }

    document.getElementById('new_password')?.addEventListener('input', function(e) {
        updateWaPreview(e.target.value);
    });

    function copyWaText() {
        const pass = document.getElementById('new_password').value;
        const text = `Assalamu'alaikum ${currentResetName}, kata sandi akun portal IKMAS AI kamu telah direset oleh pengurus.\n\nKata sandi baru: ${pass}\n\nSilakan segera login di https://ai.ikmas.com/login dan kamu dapat mengganti kata sandi di Dasbor Akun kamu. Terima kasih!`;
        navigator.clipboard.writeText(text).then(() => {
            alert('Teks WhatsApp berhasil disalin ke clipboard!');
        });
    }

    function openAlumniDeleteModal(id, name, showcaseCount, actionUrl) {
        document.getElementById('alumniDeleteName').innerText = name;
        document.getElementById('alumniShowcaseCount').innerText = showcaseCount;
        document.getElementById('alumniDeleteForm').action = actionUrl;
        document.getElementById('alumniDeleteModal').style.display = 'flex';
    }

    function closeAlumniDeleteModal() {
        document.getElementById('alumniDeleteModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const delModal = document.getElementById('alumniDeleteModal');
        const resetModal = document.getElementById('resetPasswordModal');
        if (event.target === delModal) closeAlumniDeleteModal();
        if (event.target === resetModal) closeResetPasswordModal();
    }
</script>
@endsection

@endsection
