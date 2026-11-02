# Incident-response runbook

1. **Detect and triage:** record time, reporter, affected environment/data and severity. Source exposure, key compromise, editorial bypass or public malicious media is critical.
2. **Contain:** disable affected account/session, stop scheduler/uploads or isolate the service. Preserve logs, database snapshot, audit-chain head and relevant files.
3. **Protect people:** confidential-source safety takes priority. Limit incident membership and do not place source plaintext in tickets/chat.
4. **Investigate:** verify the audit chain, review source-access logs, role/session changes, media approvals and deployment checksums. Work from copies.
5. **Eradicate/recover:** patch, rotate credentials/keys where appropriate, restore verified data, rerun gates/tests and monitor staged reopening.
6. **Notify:** legal/editorial leadership decides regulator, affected-person, provider and public notification under applicable law. This is `BLOCKED_EXTERNAL` until contacts and thresholds are approved.
7. **Review:** produce a blameless timeline, root cause, control failures, owners/deadlines and evidence-retention decision.

Production must configure named on-call contacts, secure out-of-band communications, severity/notification thresholds and monitoring alerts before launch.
