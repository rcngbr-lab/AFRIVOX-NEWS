# AFRIVOX NEWS Ultimate release checklist

**Current release decision:** `NO-GO` for production; `CONDITIONAL GO` for isolated staging.  
**Print/e-paper decision:** `NO-GO` / `DEFERRED`.  
**Date:** 30 October 2026.

Do not turn this checklist into a claim of completion by checking design-only items. Each checked gate needs an evidence location, reviewer, date and—where applicable—rollback result.

## 1. Release record

| Field | Required value |
|---|---|
| Release identifier and immutable artifact hash | Pending final package |
| Commit/source snapshot | Pending release operator record |
| Staging URL/environment inventory | `BLOCKED_EXTERNAL` |
| Database engine/version and PHP extensions | `BLOCKED_EXTERNAL` |
| Migration start/end versions | Pending rehearsal |
| Release owner | `BLOCKED_EXTERNAL` — appoint named person |
| Editorial acceptance owner | `BLOCKED_EXTERNAL` — appoint Editor-in-Chief/delegate |
| Security/operations acceptance owner | `BLOCKED_EXTERNAL` |
| Legal/DPO/finance applicability evidence | `BLOCKED_EXTERNAL` |
| Rollback owner and decision deadline | `BLOCKED_EXTERNAL` |

## 2. Artifact and secret hygiene

- [ ] Final archive contains only intended source, schema, migrations, assets and approved documentation.
- [ ] `config.local.php`, live database files, environment secrets, private originals, logs, backups and credentials are excluded.
- [ ] Secret scan records no active credential/private key/session/token.
- [ ] Demonstration content is clearly marked or removed before public launch.
- [ ] Artifact SHA-256 and file inventory are recorded and independently verified after transfer.
- [ ] `install.php` removal/disable procedure is included in the deployment window.

**Current status:** `PARTIAL`; final package verification must run after all code merges.

## 3. Database and migration gate

- [ ] Representative MySQL 8.0.29+ or MariaDB 10.6+ staging exists with the exact production release, charset, SQL mode and privileges.
- [ ] Encrypted pre-migration backup is created and restore-tested.
- [ ] `php cli/migration_preflight.php` reports `READY` before and after ordered migrations.
- [ ] All additive migrations apply once and rerun behavior/guards are documented.
- [ ] Table/row counts, foreign keys, indexes, enums and nullability match the release data dictionary.
- [ ] Existing articles, users, role assignments, revisions, source records, media, audit events and commercial ledgers remain intact.
- [ ] Encrypted source records decrypt with approved keys; no plaintext classified residue is detected.
- [ ] Rollback is rehearsed inside the allowed recovery window.

**Current status:** `BLOCKED_EXTERNAL`; SQLite setup does not satisfy this gate.

## 4. Authentication, authorization and security gate

- [ ] Password, session, CSRF, login throttling and security headers are verified over real HTTPS.
- [ ] MFA enrollment, challenge, throttling, recovery and audited reset work for privileged roles.
- [ ] Suspension and role/session-version changes invalidate all subsequent protected requests.
- [ ] Backend tests prove every role's allowed and denied operations; sidebar visibility is not used as evidence.
- [ ] Reporter cannot approve/publish; author cannot self-approve; system/commercial/finance staff cannot publish independent news.
- [ ] Fact, legal, copy and media-rights outcomes require their exact specialist permissions.
- [ ] Confidential source fields are absent from public article, search, feed, sitemap, analytics and API responses.
- [ ] Encryption keys are backed up, access-controlled and separated from release artifacts.
- [ ] Audit chain verifies; remote append-only export and alerting are operational.
- [ ] Independent vulnerability/dependency/secret review and penetration test have no unresolved critical/high findings.

**Current status:** `PARTIAL`; application controls are strong locally, infrastructure/independent evidence is missing.

## 5. Editorial and newsroom gate

- [ ] Pitch → desk decision → assignment → draft → submission → review → specialist checks → approval → publish passes end to end.
- [ ] Rejection, clarification, hold, merge, reassignment, cancellation and overdue/handover behavior are acceptance-tested.
- [ ] Stale/concurrent article and pitch decisions are rejected safely.
- [ ] Required high/critical-risk fact/legal/source/fairness/right-of-reply gates cannot be bypassed.
- [ ] Scheduled publication revalidates actor, approval and linked media at execution time.
- [ ] Correction and retraction preserve history and show an appropriate public notice.
- [ ] Homepage placement expires/falls back without hiding or duplicating unrelated content.
- [ ] Categories/subcategories cannot be retired in a way that breaks active content.
- [ ] Author/byline, timestamps, location, language, public sources and media captions/credits are correct.
- [ ] Editorial charter, complaints/corrections, source, AI and sponsored-content procedures are approved and staff trained.

**Current status:** `PARTIAL`; integrated browser acceptance remains.

## 6. Media gate

- [ ] Original uploads remain private and cannot execute.
- [ ] MIME/content/dimension/size/polyglot/malformed tests reject unsafe files.
- [ ] Required malware scanning fails closed and scanner health is monitored.
- [ ] Public derivative is safely decoded/re-encoded; sensitive metadata is removed.
- [ ] Uploader cannot approve own asset.
- [ ] Creator/source, rights holder, licence, permitted channels, geography, expiry, caption, alt text and consent are present as applicable.
- [ ] Expired/prohibited/pending media blocks publication.
- [ ] Video/audio storage, captions/transcripts, player accessibility and rights are accepted before those formats are enabled.

**Current status:** `BLOCKED_EXTERNAL` for positive production pipeline; GD/Imagick/scanner are unavailable locally.

## 7. Audience, privacy and public-form gate

- [ ] Contact/newsletter forms use server-side validation, CSRF, honeypot and database/infrastructure throttling.
- [ ] Newsletter requires verified double opt-in; raw verification tokens are not stored.
- [ ] Unsubscribe/suppression works and prevents later sends.
- [ ] Approved mail provider/domain authentication, bounce/complaint handling and delivery audit pass.
- [ ] Privacy notice, purpose/legal basis, retention, processors and transfer/storage decisions are approved.
- [ ] Access/correction/objection/restriction/portability/erasure request procedure is tested with identity verification and audit.
- [ ] General contact is not advertised as an anonymous secure-drop channel.
- [ ] Comments remain disabled unless moderation staffing, statuses, appeals and abuse/safeguarding escalation are accepted.

**Current status:** `PARTIAL`; provider, privacy operations and comments are not production-ready.

## 8. Commercial and finance gate

- [ ] Advertiser due diligence and contact/consent data are purpose-limited.
- [ ] Lead/opportunity → quote → contract/order → approved creative → campaign/placement → delivery report passes.
- [ ] Sponsored content is unmistakably labelled and commercial staff cannot control editorial conclusions.
- [ ] Invoice numbering/idempotency/currency/totals and immutable history are verified.
- [ ] Sales cannot mark payment reconciled; finance records settlement evidence and an independent reviewer reconciles it.
- [ ] Credits/refunds/write-offs require authorization and remain auditable.
- [ ] Report totals reconcile to source transactions; pipeline is not reported as cash/revenue.
- [ ] Qualified finance/tax review approves actual invoice, VAT/EBM, recognition and filing processes.

**Current status:** `PARTIAL`; do not use the application as a compliant accounting system yet.

## 9. Public quality, SEO and accessibility gate

- [ ] Homepage, article, category, author, search, media, live, policies, contact, newsletter, feed and both sitemap surfaces return expected status/content.
- [ ] Canonical, title/description, Open Graph, article JSON-LD and byline/date values validate against live pages.
- [ ] General sitemap covers canonical archive pages; news sitemap contains only eligible recent stories.
- [ ] `hreflang` is emitted only for real published translation counterparts.
- [ ] Robots and sitemap URLs use the real canonical domain; Search Console ownership/submission is recorded.
- [ ] Keyboard-only, screen-reader, zoom, focus, reduced-motion, contrast and form-error tests pass.
- [ ] Representative low-end Android, modern Android, iOS Safari and desktop browsers pass essential tasks.
- [ ] Measured LCP/CLS/INP, server response and page weight meet approved budgets over constrained bandwidth.
- [ ] Load/soak behavior, error pages and cache invalidation are accepted.

**Current status:** `PARTIAL`; live-domain, device/AT and measured performance evidence is missing.

## 10. Operations, recovery and observability gate

- [ ] Production logs redact secrets/source/PII and have approved retention/access.
- [ ] Uptime, application errors, auth abuse, scanner health, scheduled-job failures, audit verification and backup failures alert a named on-call owner.
- [ ] Scheduled publishing, security cleanup and audit verification run under least privilege and are observed.
- [ ] Encrypted database, media and configuration backups meet approved RPO/RTO.
- [ ] A representative restore into an isolated environment passes data, decryption, file and application checks.
- [ ] Incident response tabletop and account/source/media/data-breach escalation are recorded.
- [ ] DNS/TLS, hosting, mail, storage, scanner and monitoring provider recovery/exit procedures are documented.

**Current status:** `BLOCKED_EXTERNAL`.

## 11. Regulatory and organizational gate

- [ ] Legal entity, ownership, brand/domain and contracting authority are verified.
- [ ] RMC/RURA applicability, media registration/licensing and journalist accreditation are confirmed with current official evidence.
- [ ] RRA/TIN/VAT/EBM and finance obligations are approved for actual activities.
- [ ] Data-controller/processor registration and storage/transfer safeguards are confirmed as applicable.
- [ ] Employment/freelancer, copyright/licence, vulnerable-person, defamation/privacy and complaints duties have approved procedures.
- [ ] Named editorial, DPO/privacy, security, finance, complaints and incident owners accept their duties.

**Current status:** `BLOCKED_EXTERNAL`; software never substitutes for these decisions.

## 12. Fifteen-scenario acceptance gate

- [ ] 1 — news reporting
- [ ] 2 — editorial correction request and approval
- [ ] 3 — publication metadata/byline/date
- [ ] 4 — source confidentiality
- [ ] 5 — licensed media publication
- [ ] 6 — breaking/live news
- [ ] 7 — newspaper edition (`DEFERRED`; cannot pass while print is `NO-GO`)
- [ ] 8 — advertising lifecycle
- [ ] 9 — finance/reconciliation
- [ ] 10 — reader feedback/moderation
- [ ] 11 — immediate suspension
- [ ] 12 — public correction/revision history
- [ ] 13 — data recovery
- [ ] 14 — regression
- [ ] 15 — mobile/accessibility

Record actor, role, environment, test data, timestamp, expected result, actual result and evidence for each. A blocked/not-tested scenario must stay visibly blocked; it cannot be waived by changing the label.

## 13. Print/e-paper hold gate

Do not activate edition planning, print export, circulation, distribution, returns or press procurement until all are approved:

- [ ] Measured reader/institutional demand and paid pilot criteria.
- [ ] Printer technical specification, proof/reprint/confidentiality terms and quotes.
- [ ] Distributor route, custody, sales/returns and reconciliation contract.
- [ ] Positive stress-tested edition contribution and adequate working capital.
- [ ] Applicable legal/licensing determination and accountable print manager.
- [ ] Accessible digital alternative and approved archival strategy.

**Current status:** `DEFERRED` / `NO-GO`.

## 14. Rollback stop conditions

Stop deployment or scheduler immediately if any of these occurs:

- migration/count/constraint mismatch or failed source decryption;
- audit-chain failure or missing release hash;
- editorial approval, self-approval, specialist or suspension gate bypass;
- pending/expired/unscanned media reaches public delivery;
- confidential fields appear in any public response;
- contact/newsletter sends without verified consent;
- backup, monitoring, scheduled jobs or rollback checks fail;
- unexpected error rate, content corruption or severe accessibility/security regression.

Rollback procedure: freeze writes/schedulers as authorized; preserve logs/evidence; route traffic to the verified prior release; restore the rehearsed backup only under the approved recovery decision; validate decryption, counts and public/admin smoke tests; record incident and corrective action. Never use an unverified destructive reset.

## 15. Final approval

Production may change from `NO-GO` only when the checklist has evidence and signatures from the release owner, editorial authority, security/operations owner and relevant legal/DPO/finance owners. The board/publisher authorizes business launch; that authorization cannot waive a critical security, editorial-integrity or legal gate.
