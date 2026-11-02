# Confidential-data and audit-key management

## Key separation

- `AFRIVOX_APP_KEY` encrypts source notes, evidence and MFA secrets using AES-256-GCM.
- `AFRIVOX_AUDIT_KEY` signs the audit hash chain using HMAC-SHA-256.
- These must be independent 32-byte random values. Never store production values in the repository, release ZIP, database dump, logs, screenshots or support tickets.

`config.example.php` reads environment variables and demonstrates a key ring. Production should inject secrets from the hosting secret manager. `config.local.php` contains development-only material and must be excluded from packages.

## Rotation procedure

1. Take and verify an encrypted backup.
2. Add a new key ID to `app_keys` without removing old IDs.
3. Set `active_app_key_id` to the new ID; new writes now use `v2.<new-id>`.
4. Run a bounded, audited re-encryption job in staging, then production. Verify record counts and decryptability.
5. Keep the previous key available through the rollback/backup-retention window.
6. Remove an old key only after every live record and retained backup governed by policy no longer needs it.

Changing `audit_key` starts a new verifiable chain segment and must be accompanied by a signed operational record containing the last old-chain hash and first new-chain hash. Do not silently rotate it.

## Failure and recovery

If an encryption key is lost, ciphertext cannot be recovered by design. Stop publication and source-data edits, preserve evidence, notify the incident owner and restore only from an approved backup containing the correct key reference. If compromise is suspected, isolate the application, rotate credentials/keys, revoke sessions, verify the audit chain and follow the incident-response process.
