# Implementation assessment — 28 October 2026

## Baseline audit

The supplied ZIP contained a coherent PHP 8.1/MySQL MVP: responsive homepage, category and article templates, prepared statements, output escaping, CSRF tokens, password hashing, basic throttling, an upload MIME check, a three-role newsroom, search, RSS and sitemap. The standalone previews were visual references and did not contain a backend.

Material gaps in the baseline were: public exposure risk for `source_note`; direct publication without independent approval; stale sessions after deactivation; only three roles; no retained revisions/fact/legal gates; no hardened media pipeline; no operational modules; no MFA or automated security tests; and untested MySQL behavior.

## Implemented in this release

- Server-enforced editorial workflow and granular role policy.
- Retained revisions, workflow transitions, editorial checks, risk, right-of-reply and scheduling metadata.
- Expanded normalized schema for roles, authors, media rights, live reporting, newsletter verification state, advertisers and campaigns.
- Additive migration for the v1 database; original ZIP remains the rollback source.
- Public author, live reporting, governance-policy and read-only API routes.
- Accessibility improvements: skip link, focus state, reduced motion and structured live timeline.
- CLI scheduled-publication job, OpenAPI file and executable policy/data-integrity tests.
- Explicit public vs encrypted internal source records, authenticated AES-256-GCM envelopes and a legacy-encryption CLI.
- TOTP MFA, per-request account/session revalidation, privileged-role MFA requirement, suspension and session revocation.
- Self-approval prevention and explicit system/editorial/commercial separation of duties.
- Rights-aware media library with private originals and mandatory GD re-encoding/fail-closed behavior.
- Database-backed audience, advertising, invoice/payment, first-party analytics and system-administration modules.

## Known external dependencies and unimplemented boundaries

The release does not claim completed password-reset email delivery, malware scanning, payment-gateway processing, ad measurement, video transcoding, push notifications, streaming or regulator approval. Those require chosen providers, credentials, contracts, operational owners and production infrastructure. Legal text is a framework, not legal approval. MySQL end-to-end execution still requires a real MySQL 8/MariaDB staging environment; local GD was unavailable, so the media pipeline was verified to fail closed but not to produce a derivative here.
