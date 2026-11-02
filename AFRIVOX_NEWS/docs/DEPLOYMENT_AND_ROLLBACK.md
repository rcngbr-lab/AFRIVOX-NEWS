# Deployment and rollback

1. Back up the database and uploaded media; restore both into staging and verify the restore.
2. Deploy the source outside production traffic. Keep `config.local.php` private and ensure PHP 8.1+, PDO MySQL, mbstring, OpenSSL, fileinfo and the approved image processor/scanner are enabled.
3. Run `php cli/migration_preflight.php`. Existing installations apply only the reviewed additive migrations, in order: `20261028_phase2.sql`, `20261028_p0_security_mms.sql`, `20261029_v3_security_gates.sql`, `20261029_v4_newsroom_operations.sql`, `20261029_v5_taxonomy.sql`, then `20261029_v5_audience.sql`. Do not apply the fresh-install `schema.sql` over a live legacy database. Record each migration, row counts, constraints and elapsed time.
4. New installations use `install.php` once against an empty restricted database and delete/disable it immediately afterward.
5. Re-run preflight, the automated suite and the complete role/HTTP acceptance matrix. Verify reporter pitch → assignment → draft → submit, specialist checks, independent editor approval → publish; public article/search/category/section/API; homepage fallback; double opt-in/unsubscribe; commercial identity separation; corrections and audit history.
6. Set observed scheduler entries for `publish_scheduled.php`, security-attempt cleanup and audit verification. Never expose `cli/`, `migrations/`, `tests/`, `app/` or `docs/` through the web server.
7. Require HTTPS, backups with a recorded restore drill, centralized redacted logs, WAF/rate limits, alerting, reviewed mail delivery and named on-call owners before production launch.

Rollback: stop traffic writes and schedulers under the named rollback owner, preserve logs/evidence, restore the rehearsed pre-migration database/media/configuration set, deploy the last verified immutable release, then re-run decryption/count/public/admin smoke checks. The migrations widen enums and add columns/tables; do not attempt an ad-hoc down migration on production editorial history.
