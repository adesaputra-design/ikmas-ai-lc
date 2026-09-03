@extends('layouts.admin')

@section('title', 'Kelola Tim & Staf — IKMAS AI')
@section('page-title', 'Kelola Tim & Pengurus')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 800; margin: 0;">Kelola Tim & Staf Pengurus</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0.25rem 0 0 0;">
            Atur hierarki wewenang (Admin, Staf, Member) serta pembagian checklist tugas operasional tim pengurus.
        </p>
    </div>
</div>

<!-- Quick Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 2.75rem; height: 2.75rem; border-radius: var(--radius-md); background: rgba(37,99,235,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            👑
        </div>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Administrator</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);">{{ $stats['total_admin'] }}</div>
        </div>
    </div>

    <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 2.75rem; height: 2.75rem; border-radius: var(--radius-md); background: rgba(14,165,233,0.1); color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            🛡️
        </div>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Staf Pengurus</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);">{{ $stats['total_staff'] }}</div>
        </div>
    </div>

    <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 2.75rem; height: 2.75rem; border-radius: var(--radius-md); background: rgba(16,185,129,0.1); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            👥
        </div>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Member Aktif</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);">{{ $stats['total_member'] }}</div>
        </div>
    </div>

    <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 2.75rem; height: 2.75rem; border-radius: var(--radius-md); background: rgba(239,68,68,0.1); color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            🚫
        </div>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Akun Dinonaktifkan</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);">{{ $stats['total_trashed'] }}</div>
        </div>
    </div>
</div>

<!-- Tabs Status -->
<div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
    <a href="{{ route('admin.team.index', ['status' => 'active']) }}" 
       class="btn {{ $status !== 'trashed' ? 'btn-primary' : 'btn-secondary' }} btn-sm"
       style="border-radius: 999px; padding: 0.4rem 1rem;">
        Akun Aktif ({{ $stats['total_admin'] + $stats['total_staff'] + $stats['total_member'] }})
    </a>
    <a href="{{ route('admin.team.index', ['status' => 'trashed']) }}" 
       class="btn {{ $status === 'trashed' ? 'btn-primary' : 'btn-secondary' }} btn-sm"
       style="border-radius: 999px; padding: 0.4rem 1rem;">
        Dinonaktifkan / Terhapus ({{ $stats['total_trashed'] }})
    </a>
</div>

<!-- Filter & Search Bar -->
<div class="card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
    <form method="GET" action="{{ route('admin.team.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
        <input type="hidden" name="status" value="{{ $status }}">
        
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, atau no. WA..."
               style="flex: 1; min-width: 220px; padding: 0.5rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">

        <select name="role" style="padding: 0.5rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main); font-family: inherit; font-size: 0.875rem;">
            <option value="">Semua Peran</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
            <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staf Pengurus</option>
            <option value="member" {{ request('role') === 'member' ? 'selected' : '' }}>Member Alumni</option>
        </select>

        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        @if(request()->anyFilled(['q', 'role']))
            <a href="{{ route('admin.team.index', ['status' => $status]) }}" class="btn btn-secondary btn-sm">Reset</a>
        @endif
    </form>
</div>

<!-- Table Card -->
<div class="card" style="padding: 0; overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
        <thead>
            <tr style="background: var(--bg-surface-alt); border-bottom: 1px solid var(--border-color);">
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Pengguna</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Peran</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Tugas / Wewenang</th>
                <th style="padding: 1rem 1.25rem; font-weight: 700;">Karya</th>
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
                            <div>
                                <div style="font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
                                    {{ $u->name }}
                                    @if($u->id === auth()->id())
                                        <span class="badge badge-amber" style="font-size: 0.65rem; padding: 0.1rem 0.35rem;">Anda</span>
                                    @endif
                                </div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">
                                    {{ $u->email }} • {{ $u->whatsapp_number ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1rem 1.25rem;">
                        @php $badge = $u->role_badge; @endphp
                        <span class="badge {{ $badge['class'] }}" style="font-size: 0.75rem;">
                            {{ $badge['label'] }}
                        </span>
                    </td>
                    <td style="padding: 1rem 1.25rem; font-size: 0.825rem;">
                        @if($u->isAdmin())
                            <span style="color: var(--primary); font-weight: 700;">⚡ Akses Penuh (Super Admin)</span>
                        @elseif($u->isStaff())
                            @if(!empty($u->permissions))
                                <div style="display: flex; flex-wrap: wrap; gap: 0.3rem;">
                                    @foreach($u->permissions as $p)
                                        <span style="background: rgba(14,165,233,0.1); color: #0284c7; border: 1px solid rgba(14,165,233,0.25); border-radius: 4px; padding: 0.15rem 0.4rem; font-size: 0.7rem; font-weight: 600;">
                                            {{ $availablePermissions[$p]['label'] ?? $p }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span style="color: var(--text-muted); font-style: italic;">Belum ada tugas dipilih</span>
                            @endif
                        @else
                            <span style="color: var(--text-muted);">-</span>
                        @endif
                    </td>
                    <td style="padding: 1rem 1.25rem; font-size: 0.85rem;">
                        @if($u->showcases_count > 0)
                            <span class="badge badge-emerald" style="font-size: 0.75rem;">{{ $u->showcases_count }} karya</span>
                        @else
                            <span style="color: var(--text-muted);">0</span>
                        @endif
                    </td>
                    <td style="padding: 1rem 1.25rem; text-align: right;">
                        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 0.5rem;">
                            @if($status === 'trashed')
                                <form action="{{ route('admin.team.restore', $u->id) }}" method="POST" onsubmit="return confirm('Pulihkan akun {{ $u->name }}?')">
                                    @csrf
                                    <button type="submit" class="btn btn-emerald btn-sm" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">
                                        ♻️ Pulihkan
                                    </button>
                                </form>
                            @else
                                <!-- Tombol Atur Peran & Tugas -->
                                <button type="button" 
                                        class="btn btn-secondary btn-sm" 
                                        style="padding: 0.35rem 0.75rem; font-size: 0.75rem;"
                                        onclick="openRoleModal({{ json_encode([
                                            'id' => $u->id,
                                            'name' => $u->name,
                                            'role' => $u->role,
                                            'permissions' => $u->permissions ?? [],
                                        ]) }})">
                                    ⚙️ Atur Tugas
                                </button>

                                <!-- Tombol Nonaktifkan Akun (Hanya jika bukan diri sendiri dan bukan admin) -->
                                @if($u->id !== auth()->id())
                                    @if(!$u->isAdmin())
                                        <button type="button" 
                                                class="btn btn-sm" 
                                                style="padding: 0.35rem 0.6rem; font-size: 0.75rem; border: 1px solid rgba(239,68,68,0.3); color: #ef4444; background: rgba(239,68,68,0.05);"
                                                onclick="openDeleteModal('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ $u->showcases_count }}', '{{ route('admin.team.destroy', $u) }}')">
                                            🗑️ Nonaktifkan
                                        </button>
                                    @else
                                        <span title="Turunkan peran admin terlebih dahulu untuk menonaktifkan" style="font-size: 0.75rem; color: var(--text-muted); cursor: not-allowed;">
                                            🔒 Admin
                                        </span>
                                    @endif
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                        Belum ada data akun yang sesuai dengan filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($users->hasPages())
    <div style="margin-top: 1.5rem;">
        {{ $users->links() }}
    </div>
@endif

<!-- Modal Atur Peran & Tugas -->
<div id="roleModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="card" style="width: 100%; max-width: 540px; max-height: 90vh; overflow-y: auto; padding: 1.75rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.2rem; font-weight: 800; margin: 0;">Atur Peran & Tugas Staf</h3>
            <button type="button" onclick="closeRoleModal()" style="background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer;">✕</button>
        </div>

        <form id="roleForm" method="POST" action="">
            @csrf
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.4rem; color: var(--text-muted);">Nama Anggota:</label>
                <div id="modalUserName" style="font-size: 1.05rem; font-weight: 800; color: var(--text-main);"></div>
            </div>

            <!-- Radio Pilihan Peran -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem;">Pilih Tingkatan Peran:</label>
                
                <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                    <label style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); cursor: pointer;" id="role-admin-label">
                        <input type="radio" name="role" value="admin" style="margin-top: 0.2rem;" onchange="handleRoleChange(this.value)">
                        <div>
                            <div style="font-weight: 700; font-size: 0.9rem; color: var(--text-main);">👑 Administrator (Super Admin)</div>
                            <div style="font-size: 0.775rem; color: var(--text-muted);">Akses penuh ke semua modul, kelola staf, dan pengaturan sistem.</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); cursor: pointer;" id="role-staff-label">
                        <input type="radio" name="role" value="staff" style="margin-top: 0.2rem;" onchange="handleRoleChange(this.value)">
                        <div>
                            <div style="font-weight: 700; font-size: 0.9rem; color: var(--text-main);">🛡️ Staf Pengurus (Tugas Modular)</div>
                            <div style="font-size: 0.775rem; color: var(--text-muted);">Hanya dapat mengakses modul-modul yang dicentang di bawah.</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); cursor: pointer;" id="role-member-label">
                        <input type="radio" name="role" value="member" style="margin-top: 0.2rem;" onchange="handleRoleChange(this.value)">
                        <div>
                            <div style="font-weight: 700; font-size: 0.9rem; color: var(--text-main);">👥 Member Alumni (Umum)</div>
                            <div style="font-size: 0.775rem; color: var(--text-muted);">Anggota reguler tanpa akses ke panel pengurus belakang.</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Panel Checklist Tugas Staf (Muncul saat role 'staff' dipilih) -->
            <div id="staffPermissionsSection" style="display: none; margin-bottom: 1.5rem; background: var(--bg-surface-alt); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem;">
                <div style="font-size: 0.85rem; font-weight: 800; margin-bottom: 0.35rem; color: var(--primary);">
                    📋 Checklist Pembagian Tugas Staf:
                </div>
                <div style="font-size: 0.775rem; color: var(--text-muted); margin-bottom: 1rem;">
                    Centang modul yang menjadi tanggung jawab staf ini. Menu lain akan otomatis disembunyikan.
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($availablePermissions as $key => $meta)
                        <label style="display: flex; align-items: flex-start; gap: 0.65rem; background: var(--bg-surface); padding: 0.65rem 0.85rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); cursor: pointer;">
                            <input type="checkbox" name="permissions[]" value="{{ $key }}" id="perm_{{ $key }}" style="margin-top: 0.25rem;">
                            <div>
                                <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main);">{{ $meta['label'] }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $meta['desc'] }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="closeRoleModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Peran & Tugas</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Nonaktifkan / Hapus -->
<div id="deleteModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1060; align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="card" style="width: 100%; max-width: 480px; padding: 1.75rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.3);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; color: #ef4444;">
            <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: rgba(239,68,68,0.1); display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                ⚠️
            </div>
            <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0;">Nonaktifkan Akun Anggota?</h3>
        </div>

        <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1rem;">
            Apakah Anda yakin ingin menonaktifkan akun <strong id="deleteTargetName" style="color: var(--text-main);"></strong>?
        </p>

        <div style="background: rgba(239,68,68,0.05); border: 1px solid rgba(239,68,68,0.2); border-radius: var(--radius-md); padding: 0.85rem; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;">
            <div>• Akun tidak akan dapat login lagi ke portal.</div>
            <div>• <strong id="deleteShowcaseWarning"></strong> karya showcase miliknya otomatis disembunyikan dari web publik.</div>
            <div>• Data tetap aman dan dapat Anda <strong>pulihkan (restore)</strong> kapan saja.</div>
        </div>

        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary" style="background: #ef4444; border-color: #ef4444;">
                    Ya, Nonaktifkan Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openRoleModal(data) {
        document.getElementById('modalUserName').innerText = data.name;
        document.getElementById('roleForm').action = '/admin/team/' + data.id + '/role';
        
        // Check corresponding radio
        const radios = document.getElementsByName('role');
        radios.forEach(r => {
            r.checked = (r.value === data.role);
        });

        // Uncheck all permissions first
        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);

        // Check if user has permissions
        if (Array.isArray(data.permissions)) {
            data.permissions.forEach(p => {
                const cb = document.getElementById('perm_' + p);
                if (cb) cb.checked = true;
            });
        }

        handleRoleChange(data.role);
        const modal = document.getElementById('roleModal');
        modal.style.display = 'flex';
    }

    function closeRoleModal() {
        document.getElementById('roleModal').style.display = 'none';
    }

    function handleRoleChange(role) {
        const staffSec = document.getElementById('staffPermissionsSection');
        if (role === 'staff') {
            staffSec.style.display = 'block';
        } else {
            staffSec.style.display = 'none';
        }
    }

    function openDeleteModal(id, name, showcaseCount, actionUrl) {
        document.getElementById('deleteTargetName').innerText = name;
        document.getElementById('deleteShowcaseWarning').innerText = showcaseCount;
        document.getElementById('deleteForm').action = actionUrl;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    // Close on backdrop click
    window.onclick = function(event) {
        const roleModal = document.getElementById('roleModal');
        const deleteModal = document.getElementById('deleteModal');
        if (event.target === roleModal) closeRoleModal();
        if (event.target === deleteModal) closeDeleteModal();
    }
</script>
@endsection
