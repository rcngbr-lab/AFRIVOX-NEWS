# Phase 2 file-by-file change summary

- `schema.sql` — expanded normalized MySQL schema for editorial governance, media, live and commercial boundaries.
- `migrations/20261028_phase2.sql` — non-destructive upgrade path from the supplied v1 schema.
- `app/EditorialWorkflow.php` — central role/permission and article-state policy.
- `app/bootstrap.php` — workflow helpers, revision/transition persistence, hardened session timeout, SQLite preview compatibility, accessibility navigation.
- `admin/index.php` — structured metadata editor, staged workflow actions, checks, revision/history panels and expanded staff roles.
- `admin/login.php` — last-login recording and cross-driver local QA support.
- `article.php` — subheadline, key points, updated timestamp, author link and explicit image alt text.
- `author.php`, `live.php`, `policies.php` — new public templates.
- `api/v1/index.php`, `docs/API.openapi.yaml` — safe read-only public API and contract.
- `cli/publish_scheduled.php` — bounded scheduled-publication worker.
- `tests/run.php` — executable authorization, workflow and data-integrity tests.
- `assets/style.css`, `assets/admin.css` — accessibility, live reporting and workflow components.
- `.htaccess` — blocks internal code, migrations, tests, tools and local storage; adds a permissions policy.
- `README.md`, `VALIDATION.txt`, `docs/*.md` — audit, deployment/rollback, compliance, validation and known-limitations records.
- `tools/setup_local_sqlite.php` — isolated local preview database; not a production database path.

The original supplied ZIP remains untouched and is the source rollback artifact.
