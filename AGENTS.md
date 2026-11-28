# Repository Guide for Claude Code and Codex

This is the shared source of truth for contributors and coding agents. `CLAUDE.md` points here; do not maintain separate conflicting instructions.

## Project

- Laravel 13 application; PHP 8.4 is the supported runtime. Composer dependencies may require PHP 8.4 even where `composer.json` declares a lower compatibility floor.
- Follow nearby code and Laravel conventions. Reuse existing controllers, models, routes, Blade components, and test helpers where suitable.
- Keep authorization, validation, data preservation, and rollback safety at the responsible application or schema boundary.
- Do not change dependencies unless the task requires it. Do not add speculative abstractions.

## Tests and verification

- Add or update PHPUnit and JavaScript tests for behavior changes and important failure modes.
- Run the narrowest relevant tests while iterating, then the requested full checks before reporting completion.
- Use PHP 8.4 for Composer, Artisan, and PHPUnit commands when the lockfile requires it.
- Run `vendor/bin/pint --dirty` after modifying PHP files. Run JavaScript tests with `node --test tests/JavaScript/*.test.js` and `npm run build` when frontend behavior or assets change (`package.json` has no `npm test` script).
- Do not run destructive migration commands against production. Use disposable local databases for migration checks.

## Seri JARIB ebook invariants

- There is one canonical ebook schema and one `Schema::create('library_chapters', ...)` migration.
- Keep phase features integrated in the existing ebook flows: private drafts and editorial publishing, stable chapter URLs and ordered management, approved quotations, reader controls, per-chapter checklists, assessment, reading resume, and print output.
- Chapter checklist, last-read position, and assessment history are user- and ebook-scoped in `ebook_progress`.
- Never discard ebook content or progress during rollback. Migrations must refuse destructive rollback while protected data remains.
- Ebooks are public only when approved and explicitly marked public. Staff management requires the existing `library` permission.

## Documentation

- Update `PROJECT_STATUS.md` when verified project state materially changes. Mark facts as verified or unknown; do not describe source presence as proof of live deployment.
- Keep plans and implementation notes in the existing `plans/` folder.
