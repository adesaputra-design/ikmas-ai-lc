# Project Overview
IKMAS AI Learning Center (IKMAS AILC) is a Laravel portal for public visitors, alumni members, subscribers, staff, and administrators. Repository routes and controllers cover learning materials, prompts, events, showcases, and Pustaka AI.

# Current State
- [VERIFIED] The repository uses Laravel 13, PHP `^8.3`, Blade, Vite 8, Tailwind CSS 4, Composer, and npm (`composer.json`, `package.json`, `vite.config.js`).
- [VERIFIED] The default database configuration and `.env.example` specify SQLite. The active database and deployed environment were not inspected.
- [VERIFIED] Web routes include public content, member submissions/bookmarks, and staff/admin management with permission middleware (`routes/web.php`). Hermes endpoints exist in `routes/api.php`.
- [VERIFIED] PHPUnit feature and unit test files exist. On 2026-11-26, the full suite passed under temporary PHP 8.4.28: 128 tests, 497 assertions, after Seri JARIB Fase 3 work.
- [VERIFIED] At audit time, `main` tracked `origin/main`; last observed commit was `4a5f21f` (2026-09-05). Four pre-existing files were untracked: `CODEX_PROJECT_SETUP.md`, `Master template gflow.txt`, `plan-pustaka-ebook-seri-jarib.md`, and `prd-pustaka-ebook-seri-jarib.md`.
- [REPORTED] User states the production site is `https://ai.ikmas.com/` and is deployed on Hostinger (2026-11-26). Hostinger account configuration and source-to-deployment relationship were not inspected. Public URL verification was attempted but the web tool could not access it.
- [VERIFIED] The default local CLI remains PHP 8.3.30. The lockfile requires PHP >=8.4.1. Hostinger reports PHP 8.4.28 for ai.ikmas.com, with PDO MySQL enabled and a MySQL database assigned; the deployed Laravel database connection remains unverified.
- [VERIFIED] Hostinger reports ai.ikmas.com enabled on PHP 8.4.28, with active SSL and a MySQL database assigned to the domain (2026-11-28). [UNKNOWN] The deployed Laravel revision and database connection currently used by the application remain unverified.

# Completed Features
These are verified as implemented in source, not as live or test-passing behavior.
- [VERIFIED] Public routes for materials, prompts, agenda, showcases, about page, and Pustaka AI catalog/detail.
- [VERIFIED] Alumni/subscriber registration and login routes, member dashboard, showcase and academic library submissions.
- [VERIFIED] Admin routes for content, member and subscriber management, curation, and staff permissions.
- [VERIFIED] Pustaka AI models and migrations support `book`, `podcast`, `academic`, and private `ebook` drafts; feature tests cover catalog, filtering, access, admin preview, and bookmark behavior.
- [VERIFIED] Seri JARIB Fase 1–4 are implemented locally: authorized staff can create and privately preview drafts, publish after editorial confirmation, edit and reorder chapters, and add approved shareable quotations. Public ebooks appear in the catalog, can be read chapter by chapter, and bookmarked by members. Fase 4 adds reader text-size controls, theme-aware styles, mobile layout rules, and safe Markdown rendering. Automated tests and local visual smoke checks at 360px/1280px in light/dark themes pass. No live deployment was checked.

- [VERIFIED] Seri JARIB Fase 5 is implemented in commit 02724f7 on codex/seri-jarib-fase-5. Guests can try chapter checklists on the page. Active accounts can save their own selections per chapter. Six checklist smoke tests passed (43 assertions), and the full PHPUnit suite passed (137 tests, 559 assertions). Fase 1–4 were pushed as commit 59dfea6 and are open for review in draft PR #2 (https://github.com/adesaputra-design/ikmas-ai-lc/pull/2). PR #1 overlaps the ebook schema. Fase 5 was pushed to codex/seri-jarib-fase-5 as commit 02724f7.
- [VERIFIED] Seri JARIB Fase 6 is implemented locally. Staff with `library` permission can edit a structured assessment definition; validation requires unique questions, complete scale labels, contiguous score ranges covering all possible totals, and recommendations that point to chapters in the same ebook. Guests calculate per-principle averages and a result range in the browser, with chapter recommendations; answers are not submitted to the server. Focused assessment tests passed (3 tests, 14 assertions), JavaScript calculation tests passed (2 tests), and the full PHPUnit suite passed (134 tests, 530 assertions). No deployment or commit was performed.
- [VERIFIED] Seri JARIB Fase 7 is implemented locally. Eligible signed-in users can save attempts to their own `ebook_progress` row. The server checks all answer IDs and scale values, recalculates the total, principle scores, and result range from the active assessment, and appends timestamped history; browser totals and user IDs are ignored. Guests are redirected to login, inactive subscribers are denied, and history displays only for the signed-in user. Focused tests and the full PHPUnit suite passed (138 tests, 562 assertions); JavaScript calculation tests passed (2 tests). No production migration, deployment, or commit was performed.
- [VERIFIED] Seri JARIB Fase 8 is implemented locally. Reading a chapter saves `last_chapter_id` only for admins, staff, members, and active subscribers. The ebook cover shows a continue link only when that chapter belongs to the same ebook and still exists. Guest and inactive-subscriber reads create no progress row. Feature coverage includes user/ebook isolation, deleted chapters, and invalid cross-ebook references. The full PHPUnit suite passed (143 tests, 588 assertions); JavaScript calculation tests passed (2 tests). No production migration, deployment, or commit was performed.
- [VERIFIED] Seri JARIB Fase 9 print route and document are implemented locally. The document contains the cover and ordered chapters, renders approved chapter content, and hides screen controls in print media. The route only serves approved public ebooks. Print route/access tests and the full PHPUnit suite passed (146 tests, 603 assertions); Composer platform checks on PHP 8.4.28 and `npm run build` passed (the build noted an optional font optimization package). Local visual review on a temporary SQLite copy showed the styled cover and all 11 chapters on desktop; at the browser's available 582px viewport, content reflowed without horizontal overflow. The requested 390px viewport override did not take effect. [UNKNOWN] Android print output and output for an approved edition remain unverified; the review copy was temporary, and the staged source edition is still private/pending. No deployment or commit was performed.

# In Progress
- [VERIFIED] The approved nine-phase implementation breakdown is in `plans/seri-jarib-ebook.md`; Fase 1–8 criteria are checked. Fase 9 automated checks and desktop visual review are complete; Android print and approved-edition output remain pending. The PRD and earlier root draft remain untracked and unchanged.
- [VERIFIED] The checkout is still on main with combined uncommitted Seri JARIB changes. Existing remote branches already contain Fase 1–4 and Fase 5. Before committing or pushing, reconcile the local delta onto the right feature branch so existing PRs are not duplicated.
- [VERIFIED] Naskah v2 in Google Docs was edited and checked against the timestamped interview transcript. The editorial note records approval for seven Mas Ghomsony quotes in the ebook and on WhatsApp, plus approval to include Kak Ade Machnun's separate quote in the ebook introduction. Original audio has not been checked.
- [VERIFIED] Seri JARIB Edisi 1 was staged on 2026-11-27 as a private draft in a separate local SQLite database. The draft package and database are under the Git ignored directory `storage/app/private/seri-jarib/`. The local smoke test passed with 135 assertions, covering all 11 sections, 18 checklist items, seven shareable quotes, admin previews, and anonymous access restrictions. The interactive score check is now available in source; the staged draft still needs its assessment definition reviewed and saved.

# Known Issues
## Local PHP runtime mismatch
- **Status:** Local CLI mismatch verified; Hostinger runtime checked through the account API.
- **Problem:** Default CLI PHP 8.3.30 cannot run the locked dependencies, which require PHP >=8.4.1. Hostinger reports PHP 8.4.28 with PDO MySQL enabled and a database assigned to the domain. The app's production database connection and MySQL migration compatibility are not verified.
- **Next action:** Align local CLI and test migrations using a disposable MySQL database before any production migration.

# Next Priorities
1. Align the normal local PHP CLI with the lockfile and verify Laravel's production database connection.
2. Verify print output at a real phone viewport and on Android using an approved JARIB edition.
3. Review the private edition's assessment and finish release review before any production migration or deployment.

# Technical Notes
- Routes are in `routes/web.php` and `routes/api.php`; middleware aliases `admin` and `permission` are registered in `bootstrap/app.php`.
- `User` roles include `admin`, `staff`, `member`, and `subscriber`; staff permissions are stored on the user model.
- The library type schema has a local migration supporting `ebook`; public reading and editorial-gated publishing are implemented locally.
- `.env` exists locally and should not be copied into documentation. Reported production URL: `https://ai.ikmas.com/` on Hostinger. Hostinger account settings were inspected on 2026-11-28; application database connectivity and deployed revision remain unverified.

# Last Session Summary
- **Task:** Implement Seri JARIB Fase 1 with `do-work` and a smoke test.
- **Changes:** Installed Laravel Boost dev setup; added ebook metadata/chapter schema, model, admin creation and private preview flow, forms/views, routes, and tests. Fixed the library bookmark lookup with a regression test. Updated the plan and status. No commit, deployment, or active-database migration was performed.
- **Verification:** PHP 8.4.28 temporary CLI: Pint passed; Blade cache compiled; full PHPUnit suite passed with 121 tests and 434 assertions after the catalog exclusion fix. The end-to-end admin draft smoke test passed with 21 assertions.
- **Result:** Fase 1 works in source and SQLite in-memory tests. The ebook remains private until Fase 2.
- **Remaining Issues:** Default PHP CLI is still 8.3.30; production runtime/database and final approved manuscript are unknown.
- **Recommended Next Step:** Verify hosting compatibility before any deployment; continue with Fase 2 only when requested and after editorial approval of the manuscript.

## Last Session Summary — Seri JARIB Fase 2
- **Task:** Continue Seri JARIB with a public reader using test data and a smoke test.
- **Changes:** Added ebook catalog filters for series and edition; restricted ebook reads to explicitly public ebooks even for signed-in members; retained an editorial confirmation and chapter requirement on publication; added coverage for guest chapter navigation, publication, private access, filtering, old library behavior, and member bookmarks on detail/dashboard. Updated the phase plan and project status. No real manuscript was published, and no deployment or database migration was performed.
- **Verification:** PHP 8.4.28 temporary CLI: relevant smoke tests passed (16 tests, 99 assertions); full suite passed (125 tests, 470 assertions); Vite production build passed. Pint formatted the changed model after its initial check found existing formatting differences.
- **Remaining Issues:** Local default PHP is 8.3.30 while lockfile requires >=8.4.1; production environment and editorial approvals remain unknown.
- **Recommended Next Step:** Implement Fase 3 chapter editing and ordering, then verify hosting compatibility before deployment.

## Last Session Summary — Seri JARIB Fase 3
- **Task:** Add chapter editing, deletion, and ordering to the ebook admin workflow.
- **Changes:** Added edit/update, delete, move-up, and move-down routes and controls. Operations require the existing `library` permission and verify that each chapter belongs to the selected ebook. Reordering swaps through a temporary free position inside a transaction; deletion compacts positions. Updated the phase plan and status. No migration, deployment, or commit was performed.
- **Verification:** PHP 8.4.28 temporary CLI: full suite passed (128 tests, 499 assertions); Blade view cache succeeded; Pint check passed after sorting existing route imports.
- **Remaining Issues:** Default PHP CLI is 8.3.30 while the lockfile requires >=8.4.1; production environment and editorial approvals remain unknown.
- **Recommended Next Step:** Implement Fase 4 responsive reader controls and approved quotation sharing.

## Last Session Summary — Seri JARIB Fase 4
- **Task:** Continue JARIB with responsive reading controls and approved quotation sharing, using test data and smoke tests.
- **Changes:** Added approved quote storage and staff editor confirmation, public WhatsApp sharing with attribution and chapter URL, separate styling from ordinary blockquotes, reader text-size controls with storage fallback, and responsive/theme-token styles. Added Markdown safety regression coverage and updated the phase plan. No production database migration, deployment, or commit was performed.
- **Verification:** PHP 8.4.28: full PHPUnit suite passed (131 tests, 516 assertions), including the Markdown safety regression; JavaScript reader tests passed (2 tests); Vite build and Blade cache passed; targeted Pint and `git diff --check` passed. Browser smoke against the disposable SQLite DB verified cover and chapter at 360px and 1280px, theme switching, keyboard focus, WhatsApp URL content, and text-size persistence after reload. Full-repository Pint still reports formatting differences in unrelated existing files.
- **Remaining Issues:** Default PHP CLI is still 8.3.30; production environment, final manuscript, and quotation permissions remain unknown.
- **Recommended Next Step:** Implement Fase 5 using test data; obtain editorial approval before publishing the real JARIB edition.

# Last Updated
2026-11-28 (Asia/Jakarta)
