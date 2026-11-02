# AFRIVOX NEWS V3 requirement-to-code/test traceability

Status vocabulary: `IMPLEMENTED` means code and database behavior exist; `VERIFIED_LOCAL` means it was exercised in this local PHP/SQLite environment; `NOT_IMPLEMENTED` means absent; `BLOCKED_EXTERNAL` requires infrastructure, a provider, legal policy or explicit deployment authority.

| Requirement | Status | Implementation evidence | Verification / remaining gate |
|---|---|---|---|
| One authorization source of truth | IMPLEMENTED | `app/EditorialWorkflow.php`; permission definitions are version-controlled; fresh schema no longer creates `role_permissions` | Policy tests pass. Existing deployments must treat any legacy `role_permissions` table as non-authoritative and archive/remove it during reviewed migration. |
| Active, global, unexpired role assignments | IMPLEMENTED | `load_user_roles()` in `app/bootstrap.php` | Runtime revalidates account and session version every request. Scoped roles are not promoted to global. |
| Role changes revoke sessions and require step-up | IMPLEMENTED | `admin/roles.php` | Current password + enforced privileged MFA; target session version increments. |
| Exact specialist checks | VERIFIED_LOCAL | `EditorialWorkflow::canCompleteCheck()` and `admin/index.php` | Automated tests prove generic editors cannot pass fact/legal checks. |
| Draft → review → approval → publish gates | VERIFIED_LOCAL | `EditorialWorkflow.php`, `transition_article()` | High/critical evidence gates, independent approval, active permission revalidation and concurrency tests/policy tests. |
| Concurrent edits / workflow races | IMPLEMENTED | `articles.lock_version`; conditional updates in `admin/index.php` and `app/bootstrap.php` | SQLite behavior exercised; real MySQL concurrency test remains `BLOCKED_EXTERNAL`. |
| Revisions, correction and retraction history | IMPLEMENTED | `article_revisions`, `workflow_transitions`, explanation gates | Local integrity tests pass. Full correction UI/E2E remains a staging task. |
| Confidential source separation | VERIFIED_LOCAL | Explicit public projections; encrypted internal fields | Tests prove public projection excludes internal notes. |
| AES-256-GCM key rotation | VERIFIED_LOCAL | `app/Security.php`, `app_keys`, envelope `v2.<key-id>` | Encryption, wrong-key and rotated-key tests pass. Production keys/HSM/KMS are `BLOCKED_EXTERNAL`. |
| Source access audit | IMPLEMENTED | `source_access_logs`, view/update events | Production retention and legal-hold policy is `BLOCKED_EXTERNAL`. |
| MFA throttling and recovery | IMPLEMENTED | `admin/login.php`, `admin/mfa.php`, `mfa_attempts`, single-use recovery codes | Local syntax/schema verified. Browser E2E with real accounts remains staging work. |
| Audited MFA reset | IMPLEMENTED | `admin/index.php` | Different security administrator + password reauthentication + session revocation. Formal break-glass custody is `BLOCKED_EXTERNAL`. |
| Password reset tokens | VERIFIED_LOCAL | `PasswordResetService`, non-enumerating request/reset pages, hashed 30-minute single-use tokens | Automated tests cover hashing, single use, expiry and session revocation. Delivery remains `BLOCKED_EXTERNAL` pending an approved mail provider/domain. |
| Media quarantine and independent approval | IMPLEMENTED | `app/MediaService.php`, `admin/media-library.php` | No public derivative before approval; uploader cannot approve. PHP GD and scanner are unavailable locally, so positive upload E2E is `BLOCKED_EXTERNAL`. |
| Malware scanning | BLOCKED_EXTERNAL | Fail-closed production flag `malware_scanner_required` | Scanner service/ClamAV integration and clean-result callback must be provisioned. |
| Media rights metadata and expiry | IMPLEMENTED | Media service and publication context | Prohibited-use phrases and expiry block approval/publication; legal license taxonomy requires publisher approval. |
| Tamper-evident audit | VERIFIED_LOCAL | HMAC hash chain in `audit_log()`; `cli/verify_audit_chain.php` | Local verifier passes. Remote append-only/WORM export is `BLOCKED_EXTERNAL`. |
| Attempt-record cleanup | IMPLEMENTED | `cli/cleanup_security_attempts.php` | Must be scheduled by production operator. |
| Migration preflight | VERIFIED_LOCAL | `cli/migration_preflight.php`, `migrations/20261029_v3_security_gates.sql` | Local preflight reports `READY`. MySQL rehearsal/rollback is `BLOCKED_EXTERNAL`. |
| Automated policy/security tests | VERIFIED_LOCAL | `tests/run.php` | 43/43 pass on PHP 8.5. |
| MySQL 8/MariaDB integration | BLOCKED_EXTERNAL | MySQL schema/migrations supplied | Local PHP lacks `pdo_mysql` and no staging database was authorized. |
| Backup/restore and disaster recovery drill | BLOCKED_EXTERNAL | Deployment notes and encrypted-data design exist | Needs encrypted backups, offsite target, RPO/RTO approval and restore rehearsal. |
| Monitoring, alerting and incident response integration | BLOCKED_EXTERNAL | Application audit/error boundaries exist | Requires production observability destination and on-call ownership. |
| Retention, privacy requests and legal hold | BLOCKED_EXTERNAL | No automatic deletion of editorial/source/audit history | Publisher counsel/DPO must approve jurisdiction, periods and dual-control deletion procedure. |
| P1 newsroom/audience/commercial modules | PARTIAL | Existing dashboards, inbox, newsletter, campaigns, invoices, payments and analytics foundations | Assignment/homepage/notification and commercial E2E are not fully acceptance-tested. |
| Production deployment | BLOCKED_EXTERNAL | Staging package can be built | Requires explicit human authorization and every launch gate in `V3_RELEASE_STATUS.md`. |

## Local verification record

- `php tools/setup_local_sqlite.php` — passed.
- `php cli/migration_preflight.php` — `READY`.
- `php tests/run.php` — 43 passed, 0 failed.
- PHP syntax check across changed security/editorial files — passed.
- Local environment has OpenSSL, fileinfo and SQLite; it does not have PHP GD, Imagick or `pdo_mysql`.
