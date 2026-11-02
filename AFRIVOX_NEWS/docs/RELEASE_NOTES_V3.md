# AFRIVOX NEWS V3 P0 release notes — 2026-10-29

## Added

- Exact specialist-check authorization and hard high/critical publication gates.
- Independent article approval/publishing and optimistic concurrency locks.
- Private media quarantine, independent rights approval, expiry/restriction gates and production scan fail-closed configuration.
- AES-256-GCM v2 key-ID envelopes, key ring rotation, source-access reporting and HMAC-chained audit verification.
- Separate MFA throttle, single-use recovery codes and reauthenticated second-admin MFA reset.
- Hashed, single-use, expiring password-reset tokens with non-enumerating request UI and session revocation; provider delivery remains external.
- Safe V3 migration, migration preflight, security-record cleanup and expanded policy/data-integrity tests.
- Threat model, RBAC/gate matrices, retention draft, key/media/backup/incident runbooks and release traceability.

## Compatibility and upgrade

PHP 8.1+ is required. Existing deployments must back up, run preflight, apply migrations in documented order and rehearse on MySQL/MariaDB staging. Production needs `pdo_mysql`, OpenSSL, mbstring, fileinfo and GD plus a malware scanner.

## Classification

`STAGING_CANDIDATE`. Not production-ready until external launch gates in `V3_RELEASE_STATUS.md` pass.
