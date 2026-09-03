## Problem Statement

Saat ini, platform IKMAS AI Learning Center hanya memiliki dua tingkatan akses biner: `admin` dan `member` (non-admin). Dalam operasional komunitas yang bertumbuh, Lead Admin / Ketua Komunitas membutuhkan bantuan dari tim pengurus (staf operasional/volunteer) untuk mengelola tugas-tugas harian seperti kurasi karya anggota (showcase), pengelolaan materi belajar, jadwal agenda kegiatan, hingga pustaka prompt. 

Namun, jika semua staf diberikan akses `admin` penuh, timbul risiko keamanan operasional: staf dapat secara tidak sengaja mengubah data pengguna lain, mengubah pengaturan konfigurasi web, atau bahkan menurunkan akun Admin utama. Sebaliknya, jika staf hanya berstatus `member`, mereka tidak memiliki akses ke panel belakang sama sekali sehingga seluruh beban kerja operasional menumpuk di pundak satu Admin utama.

Selain itu, sistem belum memiliki mekanisme bagi Admin untuk mengelola status keanggotaan secara aman—termasuk menonaktifkan atau menghapus akun anggota bermasalah/tidak aktif tanpa menghilangkan data historis atau merusak integritas relasi karya alumni yang pernah dibuat.

## Solution

1. Mengimplementasikan sistem hak akses 3 lapisan (Admin, Staf, dan Member) dengan sistem pendelegasian tugas modular berbasis checklist.
2. Mengimplementasikan manajemen status anggota & penonaktifan akun aman (*Soft Delete*) eksklusif untuk Admin utama:
   - Admin dapat menonaktifkan akun anggota dengan dialog konfirmasi aman.
   - Karya showcase milik akun yang dinonaktifkan otomatis disembunyikan dari galeri publik.
   - Admin dapat melihat daftar akun yang dinonaktifkan dan memulihkannya kembali (*Restore*) kapan saja.
   - Dilengkapi proteksi keamanan: staf tidak memiliki akses hapus, dan Admin tidak dapat menghapus akunnya sendiri.

## User Stories

### Admin (Super Admin / Community Lead)
1. **As an Admin**, I want to view a centralized team and user management list, so that I can monitor all members and their current operational roles.
2. **As an Admin**, I want to promote a member to become a Staff member, so that they can assist in running day-to-day community operations.
3. **As an Admin**, I want to select specific task modules for each Staff member via a clear checklist, so that I can delegate responsibilities cleanly without granting unnecessary access.
4. **As an Admin**, I want to edit or revoke a Staff member's assigned tasks or demote them back to regular member at any time, so that access remains up-to-date with current volunteer commitments.
5. **As an Admin**, I want to deactivate/soft-delete a member account with a clear confirmation dialog, so that problematic or inactive accounts can be removed safely without permanently wiping historical data.
6. **As an Admin**, I want to view a dedicated filter/tab for deactivated members and restore them if needed, so that accidental deletions can be reversed instantly.
7. **As an Admin**, I want the system to prevent me from deleting or demoting my own active admin account (anti-lockout), so that the platform never loses administrative control.
8. **As an Admin**, I want to retain exclusive access to sensitive actions (managing users/staff, member deletion, editing landing page content, and core configurations), so that system governance remains safe.

### Staf (Tim Operasional / Pengurus)
9. **As a Staff member**, I want to log in through the standard dashboard and see only the navigation items relevant to my assigned duties, so that my workspace is clean, focused, and distraction-free.
10. **As a Staff member with Materials duty**, I want to create, update, and organize learning materials and categories, so that community learning resources are regularly maintained.
11. **As a Staff member with Showcase Curation duty**, I want to review submitted member projects, approve, reject, or mark them as featured, so that high-quality alumni achievements are showcased promptly.
12. **As a Staff member with Events duty**, I want to manage community events and copy broadcast announcement messages, so that study groups and workshops run smoothly.
13. **As a Staff member with Prompts duty**, I want to add and refine prompt library items, so that community members have practical AI prompts to practice with.
14. **As a Staff member with Alumni Directory duty**, I want to browse and export alumni directories, so that I can coordinate outreach or networking initiatives.
15. **As a Staff member**, I want the system to prohibit me from deleting accounts or modifying roles, and give a polite notice if I try to access restricted modules.

### Member (Anggota / Alumni)
16. **As a Member**, I want to register and participate in the community (submitting showcases, saving bookmarks, browsing public materials), without seeing administrative clutter or unauthorized controls.
17. **As a Member whose account was deactivated**, I want the login system to inform me gracefully that the account is inactive rather than throwing an unhandled application error.

## Implementation Decisions

### Roles & Access Architecture
- **3-Tier Role Structure:**
  - `admin`: Super Admin / Community Lead with absolute control across all features, user administration, and deletion privileges.
  - `staff`: Operational team members whose permissions are evaluated dynamically based on their individual task checklist. Cannot delete members or manage roles.
  - `member`: Standard registered community members with access limited to public features and member portal.
- **Unified Management Panel:**
  - Both Admin and Staff utilize the `/admin` workspace.
  - No separate panel URLs; instead, the navigation layout adapts dynamically based on the active user's permissions.
  - Attempting to access an unassigned module route produces an HTTP 403 Forbidden response with an informative, user-friendly notification.

### Task Checklist Modules
- `materials`: Learning materials & category management (Full CRUD).
- `prompts`: AI prompt library management (Full CRUD).
- `events`: Events calendar & WhatsApp broadcast text generator (Full CRUD).
- `curation`: Showcase review, approval, rejection, and featured toggling.
- `alumni`: Alumni directory search, filtering, and CSV export.
- `pages`: Public landing page content / About page CMS.

### Member Deletion & Restoration Architecture
- **Soft Deletes Mechanism:**
  - Uses Laravel's native `SoftDeletes` trait on the `User` model (`deleted_at` timestamp).
  - Soft-deleted users are immediately blocked from logging in.
  - Showcases belonging to soft-deleted users are automatically excluded from public queries, keeping public galleries curated.
- **Restoration:**
  - Admin can filter by "Status: Aktif" or "Status: Dinonaktifkan (Terhapus)".
  - On deactivated accounts, a "Pulihkan" (Restore) button restores the user's active status and re-enables their showcase submissions.

### Schema & Data Storage
- Users table receives `permissions` (json, nullable) and `deleted_at` (soft deletes timestamp).
- Existing administrator accounts automatically retain their full administrative rights.
- Default registration role remains standard member with empty staff tasks.

### Interface & User Experience
- **Admin Sidebar:**
  - Role badge under user profile dynamically indicates "Administrator" or "Staf Pengurus".
  - Dedicated "Kelola Tim & Staf" link visible only to Admin users.
  - Menu items for materials, prompts, events, showcase curation, alumni, and page settings render conditionally based on user permissions.
- **Team & Alumni Management Screens:**
  - Displays user directory with search by name/email/phone, role filter, and status filter (Active vs Deactivated).
  - Role management modal providing role selection (Admin, Staff, Member) and staff checklist.
  - "Hapus/Nonaktifkan Anggota" action button visible exclusively to Admin, triggering a clear confirmation modal detailing the member's name and associated showcase count.
  - "Pulihkan Akun" action button on deactivated records.

### Security & Edge Cases
- **Anti-Lockout Protection:** An Admin cannot delete or demote their own account or the sole remaining Admin.
- **Staff Tamper Protection:** Staff members have zero access to account deletion or role modification routes.
- **Authentication Safeguard:** Authentication attempts from soft-deleted users are rejected with an inactive account message.

## Out of Scope

- Multi-tenant organization support (this platform is dedicated to IKMAS AI Learning Center).
- Hard delete / database purge button (soft delete provides full security while safeguarding historical references).
- Complex custom permission rule-builder where admins can define arbitrary permission keys from scratch.

## Further Notes

- The combined 3-tier role delegation and soft-delete member lifecycle gives the Community Lead total peace of mind in building a scalable operational team.
