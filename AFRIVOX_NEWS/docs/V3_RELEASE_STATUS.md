# V3 release status and launch gates

## Current classification

`STAGING_CANDIDATE` — not production-ready.

The local implementation closes core P0 application controls and passes 43 automated policy/data-integrity tests. Production remains blocked because this machine has no PHP GD, malware scanner or `pdo_mysql`, no real MySQL staging database, no mail/monitoring/append-only audit providers, and no approved legal retention/license/privacy policies.

## Mandatory next sequence

1. Provision isolated staging with the production PHP version, `pdo_mysql`, GD, HTTPS and a non-production MySQL 8/MariaDB database.
2. Back up the staging database, run `php cli/migration_preflight.php`, apply migrations in order and run the preflight again.
3. Provision a scanner and set `malware_scanner_required=true`; demonstrate malicious/pending files never become public.
4. Run HTTP/E2E tests for every role, specialist checks, self-approval denial, critical separation, session revocation, MFA throttling/recovery, media expiry and concurrent edits.
5. Configure an approved transactional mail provider/domain and verify the implemented non-enumerating, single-use password reset end to end.
6. Export audit events to an append-only destination; schedule `verify_audit_chain.php` and `cleanup_security_attempts.php` with alerts.
7. Run encrypted backup/restore and disaster-recovery drills; record approved RPO/RTO.
8. Obtain publisher/legal/DPO approval for source retention/legal hold, privacy requests, complaints, corrections, media licenses and sponsored content.
9. Complete security review, dependency/secret scan, performance tests and production configuration review.
10. Deploy only after explicit human authorization, a signed launch checklist and tested rollback.

## Rollback stop conditions

Stop scheduler and deployment if migration counts differ, encrypted source records fail to decrypt, audit verification fails, any publication gate can be bypassed, media reaches public storage before approval, or monitoring/backup checks fail. Restore the verified pre-migration backup and prior application release; preserve incident evidence.
