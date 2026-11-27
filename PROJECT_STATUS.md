# Project Overview
IKMAS AI Learning Center (IKMAS AILC) is a Laravel portal for public visitors, alumni members, subscribers, staff, and administrators. Repository routes and controllers cover learning materials, prompts, events, showcases, and Pustaka AI.

# Current State
- [VERIFIED] The repository uses Laravel 13, PHP `^8.3`, Blade, Vite 8, Tailwind CSS 4, Composer, and npm (`composer.json`, `package.json`, `vite.config.js`).
- [VERIFIED] The default database configuration and `.env.example` specify SQLite. The active database and deployed environment were not inspected.
- [VERIFIED] Web routes include public content, member submissions/bookmarks, and staff/admin management with permission middleware (`routes/web.php`). Hermes endpoints exist in `routes/api.php`.
- [VERIFIED] PHPUnit feature and unit test files exist. On 2026-11-26 (after Seri JARIB Phase 2), `php artisan test` passed: 130 tests, 490 assertions, using Herd PHP 8.4.
- [VERIFIED] At audit time, `main` tracked `origin/main`; last observed commit was `4a5f21f` (2026-09-05). Four pre-existing files were untracked: `CODEX_PROJECT_SETUP.md`, `Master template gflow.txt`, `plan-pustaka-ebook-seri-jarib.md`, and `prd-pustaka-ebook-seri-jarib.md`.
- [REPORTED] User states the production site is `https://ai.ikmas.com/` and is deployed on Hostinger (2026-11-26). Hostinger account configuration and source-to-deployment relationship were not inspected. Public URL verification was attempted but the web tool could not access it.
- [VERIFIED] The default `php` on PATH is Laragon PHP 8.3.30, but `composer.lock` needs PHP >=8.4 (Symfony 8 packages use 8.4-only syntax; with 8.3 `artisan` fails with a parse error). Herd PHP 8.4.21 at `C:\Users\Razaq\.config\herd\bin\php84\php.exe` installs and runs the project without platform overrides. Production PHP version is still unknown and must be >=8.4.
- [UNKNOWN] Production runtime health, active database engine, and deployed revision.

# Completed Features
These are verified as implemented in source, not as live or test-passing behavior.
- [VERIFIED] Public routes for materials, prompts, agenda, showcases, about page, and Pustaka AI catalog/detail.
- [VERIFIED] Alumni/subscriber registration and login routes, member dashboard, showcase and academic library submissions.
- [VERIFIED] Admin routes for content, member and subscriber management, curation, and staff permissions.
- [VERIFIED] Pustaka AI models and migration support `book`, `podcast`, and `academic` items; feature tests cover catalog, filtering, and access behavior.

# In Progress
- [VERIFIED] Seri JARIB plan `plans/seri-jarib-ebook.md`: **Phases 1–3 are done** (draft + private preview; publish with editorial approval, public cover/chapters, catalog filter, bookmark; edit/delete/reorder chapters with stable slugs and gap-free positions). Phases 4–9 not started; next is Phase 4 (mobile reading, font size, WhatsApp quote sharing).
- [VERIFIED] Codex built a parallel Phase 1–2 in the main checkout (uncommitted, on `main`): duplicate migrations `2026_11_26_082536_create_library_chapters_table` and `2026_11_26_082538_add_ebook_fields_to_library_items_table`, `AdminEbookController`, `EbookController`, `public/css/ebook.css`, separate admin ebook views. Its Phase 2 behavior was ported onto the PR branch; those files were not. The main checkout must drop them before pulling the merged branch, or the duplicate migrations will create `library_chapters` twice.
- [VERIFIED] Raw source for edition #1 is the Google Doc "Ghomsony - Closing Maksimal dengan 5 PRINSIP JARIB" (id `1YBHPUs5LSli0zmAfLkJhQ9TWLQlCKOCXabx0-toMr8k`): interview transcript + draft v1. Draft v1 contains many quotes/anecdotes not in the transcript (most Bab 4–5 quotes, Bab 6 Q&A answers, the Rp16 juta story is reversed) and unsourced statistics. Naskah v2 has not been written; speaker approval of quotes is still required before publishing.

# Known Issues
- [VERIFIED] Library bookmark schema mismatch is fixed: `LibraryItem::bookmarks()` uses `bookmarkable_id` + `bookmarkable_type = 'library'`, covered by `LibraryPublicTest::test_member_sees_saved_library_bookmark_on_detail_page`.
- [VERIFIED] Pint reports pre-existing style deviations in `LibraryItem.php` and `AdminLibraryController.php`; new files pass. Not reformatted to keep diffs focused.

# Next Priorities
1. Merge PR adesaputra-design/ikmas-ai-lc#1, clean the duplicate Codex files from the main checkout, then Seri JARIB Phase 4 (mobile reading, font size, WhatsApp quote sharing).
2. Draft Naskah v2 from the transcript only, mark unverified claims `[CEK: …]`, and get speaker approval before seeding edition #1.
3. Confirm production PHP >=8.4 and Hostinger deployment configuration before deploying.

# Technical Notes
- Routes are in `routes/web.php` and `routes/api.php`; middleware aliases `admin` and `permission` are registered in `bootstrap/app.php`.
- `User` roles include `admin`, `staff`, `member`, and `subscriber`; staff permissions are stored on the user model.
- `library_items.type` now includes `ebook`; ebook columns `series_name`, `edition_number`, `is_public` (default false). Chapters live in `library_chapters` (`LibraryChapter` model): slug unique per ebook, position auto-assigned, `reading_minutes` = words ÷ 200 (min 1), item `reading_time` synced as "N menit". Markdown rendered via `LibraryChapter::renderMarkdown()` with raw HTML stripped and unsafe links blocked.
- Migration `2026_11_26_100000_add_ebook_support_to_library_items_table` refuses rollback while any ebook row exists (checked before dropping anything). Verified on SQLite; MySQL not yet tested.
- Ebooks are created as `pending` drafts. Admin routes (permission `library`): `admin/library/{item}/chapters` (GET list+form, POST add), `admin/library/{item}/preview`, `admin/library/{item}/preview/bab/{chapterSlug}`, `POST admin/library/{item}/publish` (requires `editorial_approved` and ≥1 chapter; sets `approved` + `is_public`). The generic `approve` route and the edit form's status field cannot publish an ebook.
- Public access: `LibraryItem::visibleToPublic()` = approved and (not ebook or `is_public`). `GET /library/{slug}` renders `library.ebook.cover` for ebooks; `GET /library/{slug}/bab/{chapterSlug}` (`library.ebook.chapter`) serves chapters. Unpublished ebooks are 404 for everyone outside admin preview, members included. Views `library/ebook/cover`, `library/ebook/chapter` take an `$isPreview` flag.
- `.env` exists locally and should not be copied into documentation. Reported production URL: `https://ai.ikmas.com/` on Hostinger. Hosting configuration and production settings remain unverified.

# Last Session Summary
- **Task:** Seri JARIB Phase 3: edit, delete, and reorder chapters (Phases 1–2 are in `935e5e9` and `3d0d58d`).
- **Changes:** `LibraryChapter::move()` swaps a chapter with its neighbour inside a transaction (position 0 as a temporary slot); the `deleted` event closes the position gap. `AdminLibraryChapterController` gains `edit`, `update`, `move`, `destroy` with shared validation; four admin routes under `scopeBindings()` so a chapter of another ebook returns 404. Chapter slug stays fixed when the title changes. Form fields extracted to `admin/library/_chapter-fields.blade.php`; new `admin/library/chapter-edit.blade.php`; Kelola Bab rows get ↑ ↓, Edit, and Hapus (with confirmation); `tests/Feature/EbookChapterManagementTest.php` (7 tests).
- **Verification:** Full suite 137/137 passed; Pint clean. Browser on localhost: moved a chapter up, edited title and body (URL unchanged), deleted the middle chapter; public cover showed the new order and total reading time.
- **Result:** Phase 3 acceptance criteria met on branch `claude/cek-project-folder-ec3dd5` (PR #1).
- **Remaining Issues:** Main checkout still holds Codex's uncommitted duplicate implementation; MySQL migration untested; Naskah v2 not written; production PHP version unknown.
- **Recommended Next Step:** Phase 4 (mobile reading, font size, WhatsApp quote sharing), or merge PR #1 first.

# Last Updated
2026-11-27 (Asia/Jakarta)
