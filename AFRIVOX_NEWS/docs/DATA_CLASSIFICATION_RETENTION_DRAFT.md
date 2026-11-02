# Data classification and retention draft

This is a governance draft, not approved legal advice. Automatic deletion of source, editorial, audit, complaint or finance history is disabled until policy approval.

| Class | Examples | Default handling | Retention decision |
|---|---|---|---|
| Public | Published stories, public citations, approved public media | Public projection only; cache/search/API allowed | Editorial archive policy required |
| Internal | Drafts, assignments, ordinary review notes | Authenticated role access; audit sensitive changes | Publication policy required |
| Confidential | Source notes, unpublished evidence, contracts, reader privacy requests | AES-256-GCM; least privilege; no public projection; access log | `BLOCKED_EXTERNAL`: legal/editorial approval |
| Restricted | High-risk source identities, legal-hold evidence, MFA secrets/keys | Encryption, named leadership/specialist access, step-up for administrative actions, separate key custody | `BLOCKED_EXTERNAL`: dual-control deletion/legal hold |
| Financial | Invoices, payments, advertiser contracts | Finance permissions; immutable adjustments/audit | `BLOCKED_EXTERNAL`: tax/accounting periods |
| Security telemetry | Login/MFA attempts, audit hashes, incident evidence | Restricted operations access; no secret plaintext | Attempt cleanup defaults to 30 days; audit/incident periods require approval |

Legal hold overrides deletion. An approved deletion must identify authority, scope, reviewer, evidence preservation and audit event. Backups must follow the same expiry/hold policy; deleting live data alone is not complete erasure.
