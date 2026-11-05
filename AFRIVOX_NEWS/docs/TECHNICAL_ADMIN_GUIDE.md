# AFRIVOX NEWS technical administrator guide

**Applies to:** Ultimate phased staging candidate, 30 October 2026  
**Release state:** controlled staging only; production is `NO-GO`.  
**Audience:** authorized system administrators, release engineers and incident owners.  
**Security boundary:** a technical administrator manages infrastructure but has no editorial publishing authority.

## 1. System and authority boundary

AFRIVOX NEWS is a server-rendered PHP/PDO application with public pages, an authenticated admin area, a normalized relational schema, private/quarantined media, CLI maintenance jobs and append-only signed audit events. Local preview uses SQLite; production targets reviewed MySQL/MariaDB infrastructure.

Possession of database, hosting or operating-system access is emergency technical authority, not permission to edit editorial decisions, decrypt sources for curiosity, approve media, reconcile finance or publish content. All privileged access must be named, least-privilege, logged and reviewed.

## 2. Required production decisions before installation

Record named approval for all of the following before a production build is exposed:

- hosting region/provider, domain/DNS, TLS termination, network segmentation and WAF/rate limits;
- supported PHP/web-server/MySQL versions and patch policy;
- secret manager, key custodians, rotation/recovery process and emergency access;
- private media/object storage, malware scanner and image-processing service;
- transactional mail provider, authenticated sending domain and bounce/complaint handling;
- centralized redacted logs, metrics, alert destinations, on-call roster and escalation times;
- encrypted backup/immutable offsite destination, approved RPO/RTO and completed restore drill;
- privacy/data-controller decisions, retention, incident notification contacts and legal review;
- release owner, rollback owner, editorial acceptance owner and maintenance window.

This repository does not supply those credentials, contracts or approvals.

## 3. Platform requirements

- PHP 8.1 or newer (the local verified run used PHP 8.5.5).
- PDO MySQL for staging/production; SQLite is local-preview evidence only.
- PHP extensions: `mbstring`, `openssl`, `fileinfo`, plus an approved image processor and malware-scanning integration for real uploads.
- Apache/Nginx or equivalent configured so only intended public PHP/static paths are reachable.
- MySQL/MariaDB with `utf8mb4`, transactional InnoDB tables, strict SQL mode, backups and restricted application/migration accounts.
- A job scheduler with observable execution and failure alerts.

Never place the web document root above the project in a way that exposes configuration, migrations, tests, CLI tools, documentation or private media.

## 4. Local preview only

From the project directory:

```powershell
Copy-Item config.example.php config.local.php
C:\php85\php.exe tools\setup_local_sqlite.php
C:\php85\php.exe tests\run.php
C:\php85\php.exe -S 127.0.0.1:8765 -t .
```

Set local-only configuration as documented by the setup tool. Use generated test data only. Do not enter confidential sources, real audience data, production credentials or live finance information. The PHP development server is not a production server.

`config.local.php` and `storage/` are runtime/private material and must not enter a release archive or source control.

## 5. Production configuration and secrets

Create the private configuration from `config.example.php` outside the release build process and verify:

- `app_url` is the exact canonical HTTPS origin; `base_path` matches any reviewed subdirectory deployment.
- `timezone` is the approved operating timezone (the example uses `Africa/Kigali`).
- `db_dsn` includes `charset=utf8mb4`; the application database user is not a server administrator.
- `environment` is `production`; session name is deployment-specific and does not collide with another application.
- `AFRIVOX_APP_KEY` supplies the active application encryption key and `AFRIVOX_AUDIT_KEY` supplies a separate audit HMAC key.
- `active_app_key_id` identifies a key in `app_keys`; older required keys remain available during an approved rotation.
- `private_media_path` resolves outside the public document root with restricted permissions.
- `malware_scanner_required` remains `true` in production and unscanned assets fail closed.
- password-reset and newsletter dispatchers remain `null` until an approved provider/domain and reviewed callable are configured.

Generate independent high-entropy secrets in the approved secret manager. Never email them, log them, bake them into an image/archive, use `CHANGE_ME`, reuse one key for encryption and audit, or delete an old encryption key while retained data still depends on it. See `KEY_MANAGEMENT.md`.

## 6. Database installation and migration

### New empty database

1. Create restricted application and migration identities; validate server version, strict mode, timezone, collation, disk/backup health and connection encryption.
2. Point a non-public staging release at an empty database.
3. Run `install.php` once as documented, then delete or disable it immediately.
4. Verify constraints, indexes, seed/administrative identity handling and recorded migration state.

### Existing database

1. Take an encrypted database/media/configuration backup and complete a restore rehearsal for the change set.
2. Quiesce writes/schedulers as required by the reviewed migration plan.
3. Run `php cli/migration_preflight.php`; a failure is a stop condition.
4. Apply only reviewed additive migrations in this order:
   1. `20261028_phase2.sql`
   2. `20261028_p0_security_mms.sql`
   3. `20261029_v3_security_gates.sql`
   4. `20261029_v4_newsroom_operations.sql`
   5. `20261029_v5_taxonomy.sql`
   6. `20261029_v5_audience.sql`
   7. `20261105_v6_ad_delivery.sql`
5. Record start/end time, operator, release checksum, exact SQL artifact, row counts, warnings and constraints.
6. Run preflight again and execute the validation in section 9 before admitting traffic.

Never apply fresh-install `schema.sql` over a live database and never invent a destructive “down” migration for editorial/audit history. MySQL/MariaDB execution is still an external release gate because this local environment has only the SQLite PDO driver.

## 7. Web-server and filesystem hardening

- Enforce HTTPS and HSTS only after the final domain/certificate path is correct.
- Preserve and test application security headers; do not weaken CSP or related controls merely to silence a third-party embed error.
- Deny web access to `app/`, `cli/`, `docs/`, `migrations/`, `storage/`, `tests/`, `tools/`, configuration, schema, README/validation and hidden/runtime files.
- Deny PHP/script execution in `uploads/`; keep originals/private assets outside the document root.
- Run PHP/web workers as a restricted service identity. Grant write access only to exact runtime paths.
- Disable directory listing, verbose production errors and public stack traces. Send redacted errors to the approved log destination.
- Apply request/body/time limits, edge rate limits and trusted-proxy configuration appropriate to the actual topology.

Test these denials from outside the server, not only by reading `.htaccess`.

## 8. Scheduled and background work

Configure observed, single-instance jobs with locked execution, redacted logs, exit-code monitoring and alerts:

- `php cli/publish_scheduled.php` at the approved publication cadence;
- `php cli/cleanup_security_attempts.php` under the approved retention schedule;
- `php cli/verify_audit_chain.php` on a regular schedule and before/after releases or incidents.

Run jobs with the application service identity and explicit working directory/environment. A scheduler failure must alert a named person. Never run scheduled publishing twice concurrently or “catch up” blindly after a long outage; review overdue content with editorial ownership first.

## 9. Release validation and acceptance

Run from the exact immutable release candidate against the target staging configuration:

```powershell
php cli\migration_preflight.php
php tests\run.php
php cli\verify_audit_chain.php
$files = rg --files -g '*.php'
foreach ($file in $files) { php -l $file }
```

Then record an HTTPS/browser/role acceptance matrix covering at least:

- public homepage, article, category, section, author, search, contact, newsletter verify/unsubscribe, RSS and both sitemaps;
- login, password reset, MFA, forced password change, suspension and session revocation;
- pitch → assignment → draft → submission → specialist gates → independent approval → publish;
- scheduled publication, homepage fallback/curation, live update independent approval/correction and audit history;
- media quarantine, failed scan, rights approval and public projection excluding source data;
- campaign/creative creator-approver separation, licensed 2:3 creative validation, placement activation/expiry, privacy-preserving ad-event deduplication and invoice/payment identity separation;
- unauthorized-role redirects/denials and direct requests to protected directories;
- backup restore, source decryption by an authorized test account, audit verification and rollback rehearsal.

The current local baseline is 74 automated tests passing, all PHP files linting, and a successful multi-role advertising delivery acceptance on SQLite/PHP 8.5. This is evidence for the source candidate, not evidence that a production host, MySQL, scanner, mail provider, monitoring or restore process works.

## 10. Identity, access and operational administration

- Provision an individual identity only after the hiring/authorization record is approved. No shared newsroom or vendor administrator accounts.
- Grant the minimum role set. A system administrator has infrastructure/user-management powers but no editorial publication authority.
- Require MFA for privileged users; deliver enrollment/recovery through approved channels.
- Suspension, password reset, role change and incident containment must revoke existing sessions as designed.
- Review privileged, inactive, vendor and multiple-role accounts on a fixed schedule. Remove access immediately on departure or lost-device notice.
- Use the audited reset/admin workflows; do not update password hashes, MFA state or roles directly in the database except under a declared, documented emergency procedure.

## 11. Media and mail integrations

### Media

Verify the private path, permissions, storage encryption, scanner availability, allowed MIME/signature checks, image processing, metadata policy, quotas and retention. A scanner timeout/unavailable result is not “clean.” Exercise a safe scanner test fixture and confirm it cannot become publicly executable.

### Mail

Authenticate the sending domain and configure provider credentials in the secret manager. Review password-reset and newsletter dispatcher code, TLS, templates, expiry, bounce/complaint handling, suppression and delivery webhooks. Newsletter remains double-opt-in; do not bulk-confirm pending subscribers. Redact confirmation/reset tokens and addresses from logs.

## 12. Monitoring and daily technical checks

Production monitoring must cover:

- HTTPS/DNS/certificate health and public/admin availability;
- 5xx/error rate, latency, resource/disk/inode pressure and database connection/replication/backup health;
- failed login/MFA/reset/rate-limit anomalies without logging secrets or raw unnecessary identifiers;
- scheduler last-success, overdue scheduled stories, mail failures, scan failures and queue age;
- audit-chain verification, unexpected role/session changes and privileged access;
- backup completion/checksum/age and periodic measured restore results.

Each alert needs severity, threshold, owner, destination, runbook and acknowledgement/escalation timer. Current monitoring configuration is `BLOCKED_EXTERNAL`; log files alone do not meet this gate.

## 13. Backup and restore

Follow `BACKUP_RESTORE_DR_RUNBOOK.md`:

1. Back up the database and private/public media consistently, encrypted and offsite/immutable.
2. Store release checksum, schema state, key IDs and audit-chain head beside the backup—never raw keys.
3. Verify checksums and restore eligibility automatically.
4. Restore regularly into an isolated environment with outbound/schedulers disabled.
5. Run preflight, tests, audit verification, counts, authorized decryption, media-rights and finance reconciliation checks.
6. Record achieved RPO/RTO, failures, owners and management acceptance; securely destroy the drill environment under policy.

A backup that has not been restored is not accepted recovery evidence.

## 14. Incident response

For suspected key/source exposure, editorial bypass, malicious upload, unauthorized publication or destructive data change:

1. Open a restricted incident record and preserve time, reporter and affected environment.
2. Contain narrowly—revoke affected sessions, disable the exact job/integration or isolate traffic under the incident commander.
3. Preserve logs, database snapshot, release checksum, audit-chain head and relevant files before repair.
4. Keep source plaintext, tokens and keys out of tickets/chat. Protect affected people first.
5. Investigate on copies; verify audit/role/media/deployment history.
6. Patch/rotate/restore as appropriate, then rerun the complete gates and reopen gradually with monitoring.
7. Authorized legal/editorial leadership decides regulatory, affected-person, provider and public notification.

Use `INCIDENT_RESPONSE_RUNBOOK.md`. Do not delete unreadable ciphertext or rotate away its only available key as a “fix.”

## 15. Rollback

The named rollback owner stops traffic writes and schedulers, preserves evidence, and restores the rehearsed pre-migration database/media/configuration set plus the last verified immutable release. Re-run decryption, row/file counts, preflight, tests, audit-chain and public/admin smoke checks before staged reopening.

Do not combine old code with an unreviewed newer schema, perform ad-hoc destructive SQL or silently discard content created during a deployment window. Reconcile any accepted writes through an approved recovery plan.

## 16. Go-live gate and change record

Production approval requires evidence—not a verbal “works on my machine”—for:

- target MySQL migration and concurrency rehearsal;
- external HTTPS/server hardening and protected-path tests;
- scanner/image pipeline, private media and mail provider tests;
- centralized monitoring/alerts with named on-call response;
- encrypted backup plus successful measured restore;
- role/browser/editorial/commercial acceptance matrix;
- accessibility/device/load/security review and independent penetration test as approved;
- legal/privacy/registration decisions, retention and incident contacts;
- immutable artifact checksum, dependency inventory, rollback rehearsal and signatures from technical, security, editorial and business owners.

For every change retain request/risk, reviewer, test evidence, migration/rollback plan, artifact checksum, deployment times, operator, observed result and final acceptance. Until all release-checklist blockers are closed, deploy only to controlled staging and use synthetic/non-sensitive data.
