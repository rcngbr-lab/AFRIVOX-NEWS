# Backup, restore and disaster-recovery runbook

## Before production

The publisher must approve RPO/RTO, backup frequency, regions/providers, encryption/key custody and restore owners. Current drill status is `BLOCKED_EXTERNAL`.

## Backup

1. Quiesce scheduler for a consistent release/migration backup when required.
2. Create encrypted database and private-media backups with least-privilege credentials.
3. Store application release checksum, schema migration state, key IDs and audit-chain head beside the backup—never the raw keys.
4. Copy to an access-controlled offsite destination with immutability/versioning.
5. Verify size, checksum, encryption and automated restore eligibility; alert on any failure.

## Restore drill

1. Restore into an isolated non-production network using separate credentials.
2. Apply only documented migrations and start workers disabled.
3. Run migration preflight, all tests and audit-chain verification.
4. Confirm authorized source records decrypt, public endpoints exclude them, media rights remain intact, invoices/payments reconcile and no scheduled story publishes automatically.
5. Record achieved RPO/RTO, row/file counts, failures, corrective actions and named approval. Destroy the drill environment securely under policy.

## Disaster recovery

Declare an incident, freeze destructive changes, preserve audit evidence, identify the last verified backup, rotate compromised credentials, restore, verify, then reopen traffic gradually. Never “repair” confidential ciphertext by deleting unreadable records.
