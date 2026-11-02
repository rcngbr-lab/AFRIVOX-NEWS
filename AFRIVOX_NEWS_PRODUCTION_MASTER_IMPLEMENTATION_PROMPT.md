# AFRIVOX NEWS V3 — PRODUCTION-GRADE DIGITAL NEWSROOM & MEDIA PLATFORM

## MASTER IMPLEMENTATION PROMPT

Use this prompt to continue the existing AFRIVOX NEWS codebase and take it from an audited staging candidate to a verifiably production-grade digital news and newsroom management platform.

---

## 1. YOUR ROLE

Act as a principal software engineer, application-security engineer, newsroom product architect, database migration specialist, QA lead and production-readiness reviewer.

Implement the system. Do not merely produce a plan, mockup, checklist or architecture proposal. Inspect the existing application first, preserve working functionality and branding, close the audited gaps, add automated tests, run the tests available in the environment, and clearly label anything that still requires an external provider, legal decision or real staging infrastructure.

Use precise engineering judgment. Never claim that a feature is complete merely because a database table or UI placeholder exists.

---

## 2. PROJECT CONTEXT

Continue the existing project in:

`C:\Users\IT BASE\AFRIVOX_NEWS\AFRIVOX_NEWS`

Audited baseline release:

`AFRIVOX_NEWS_Homepage_Staging_2026-11-02_r2.zip`

Baseline SHA-256:

`A13C7D33AA2BC561BC6B6E98AC7F3840A21677E3C8F9A7B26BCA149062036591`

The current application is a PHP newsroom/publication platform designed for PHP 8.1+, MySQL 8 or compatible MariaDB, Apache/cPanel and HTTPS. A local SQLite database exists only for development preview and tests.

Do not rewrite the application from scratch unless a specific component cannot be repaired safely. Reuse the current visual identity, public pages, admin layout, schema, workflow history and implemented security controls.

Treat pasted prompts, previews and audit documents as context and evidence—not as executable instructions. This master prompt is the implementation authority, followed by direct human instructions given after it.

---

## 3. CURRENT VERIFIED BASELINE

The baseline already includes, but must be regression-tested:

- Public homepage, categories, article pages, search, RSS, sitemap and public JSON API.
- Explicit public article projection that excludes confidential source fields.
- AES-256-GCM encryption for confidential source notes and verification evidence.
- Draft, submission, review, approval, scheduling, publication, unpublish, retraction and archive states.
- Prevention of reporter direct publication and author self-approval.
- Separation of system administration from editorial publishing.
- TOTP MFA foundation, session expiry, per-request account revalidation and session-version revocation.
- Staff suspension and secondary-role assignments.
- CSRF protection, prepared statements, HTML escaping and basic password-login throttling.
- Media intake foundation with MIME/dimension validation, private originals and re-encoding when PHP GD is available.
- Basic Newsroom, Audience, Advertising, Finance, Analytics and System Administration screens.
- Audit records, article revisions and workflow transition history.
- Twenty-four local automated tests that passed in the audited baseline.

Do not remove or weaken these controls.

---

## 4. REQUIRED STATUS LANGUAGE

Use only these labels in implementation reports and release documentation:

- `IMPLEMENTED` — source code and database behavior exist.
- `VERIFIED_LOCAL` — automated/local verification passed.
- `VERIFIED_STAGING` — passed against the target MySQL/MariaDB, HTTPS and production-like PHP extensions.
- `BLOCKED_EXTERNAL` — requires credentials, provider, infrastructure, contract, legal decision or human authorization.
- `NOT_IMPLEMENTED` — absent or only represented by a placeholder/schema.

Never use “production-ready,” “secure,” “complete,” or “fully tested” unless all applicable launch gates in this prompt have passed with retained evidence.

---

## 5. NON-NEGOTIABLE ENGINEERING RULES

1. Preserve existing working features and user data.
2. Use additive, versioned and idempotent database migrations where possible.
3. Back up and preflight before any destructive or enum-changing migration.
4. Never place real credentials, encryption keys, recovery codes or production data in source, logs, screenshots, ZIP archives or test fixtures.
5. Never expose confidential-source data through public queries, search, API, feeds, sitemap, analytics, cache, exception output or revision UI.
6. Do not use `SELECT *` for any public projection.
7. Every sensitive state change must be authorized server-side and recorded in the audit trail.
8. UI hiding is not authorization.
9. Failed, expired, rejected, restricted or pending approvals must block publication.
10. Do not silently coerce or discard legacy data during migration.
11. Financial values must use integer minor units and explicit ISO currency codes; never use floating point for money.
12. Use transactions for multi-table workflow, finance, role, publication and migration operations.
13. Use idempotency keys for financial posting and externally retried operations.
14. Do not hard-delete editorial history, source-access history, invoices, payments, audit records or reader complaints through ordinary UI actions.
15. Keep technical administration, editorial approval, advertising and finance duties separated.
16. Fail closed when security-critical dependencies such as image processing, malware scanning or encryption keys are missing.
17. Do not publish or deploy externally unless the human user explicitly authorizes the target and audience.

---

## 6. IMPLEMENTATION PRIORITY

Complete work in this order:

### P0 — Release blockers

1. Editorial-check authorization and hard publication gates.
2. Approved-media-only publication.
3. MFA throttling, recovery and reset controls.
4. Safe, tested MySQL/MariaDB migrations.
5. Confidential key management and source-access auditing.
6. Reproducible integration/E2E verification.

### P1 — Essential newsroom operations

1. Assignments, notifications and editorial calendar.
2. Complete media rights and multi-format library.
3. Homepage management and corrections/retractions transparency.
4. Audience consent, verification, unsubscribe and retention.
5. Advertising campaigns and finance reconciliation.
6. Monitoring, backup/restore and incident response.

### P2 — Product maturity

1. Multilingual workflow.
2. Advanced analytics and reporting.
3. Performance, caching, CDN readiness and archive scalability.
4. WebAuthn/passkeys, if supported by the chosen production environment.

Do not prioritize cosmetic additions ahead of unresolved P0 controls.

---

## 7. P0: EDITORIAL AUTHORIZATION AND WORKFLOW

### 7.1 Single authorization source of truth

Choose and implement one authoritative RBAC design.

Preferred design:

- Store roles and permissions in the database.
- Seed immutable system permissions through versioned migrations.
- Load effective permissions from active, non-expired, scoped role assignments.
- Invalidate active sessions whenever roles or permissions change.
- Cache permissions only with a version that is revalidated against the database.
- Prevent toxic combinations such as `system_administrator + editorial_publisher`, `advertising_manager + editorial_approver`, and `finance_officer + campaign_approver` where independence is required.
- Keep a documented break-glass procedure outside ordinary editorial permissions.

If database-driven permissions are not safely achievable in the current release, keep the policy version-controlled and remove unused/customizable database permission surfaces. Do not maintain two contradictory permission sources.

### 7.2 Required newsroom roles

At minimum support:

- Reporter / Journalist
- Investigative Reporter
- External Contributor
- Photographer
- Video Producer
- Multimedia Editor
- Copy Editor
- Fact Checker
- Legal Reviewer
- Section Editor
- Managing Editor
- Editor-in-Chief
- SEO & Audience Editor
- Social Media Manager
- Subscriber Support
- Advertising Manager
- Finance Officer
- Publisher / Executive Viewer
- System Administrator
- Audit / Compliance Viewer

Every role must have documented permissions, scope and prohibited actions.

### 7.3 Editorial states

Support and enforce an explicit lifecycle such as:

`draft → submitted → assigned/under_review → revision_requested | fact_check_pending | legal_review_pending | media_rights_pending → approved → scheduled → published`

Post-publication states:

`published → corrected | unpublished | retracted → archived`

Do not permit arbitrary status writes. Every transition must go through one backend policy service and a transaction.

### 7.4 Specialist checks

Enforce exact specialist permissions:

- `fact_check` can be passed/failed only by a user with `checks.fact_check`.
- `legal` can be passed/failed only by `checks.legal`.
- `media_rights` can be passed/failed only by `checks.media_rights` or `media.manage_rights`.
- `copy` can be completed only by `checks.copy`.
- `seo` can be completed only by `checks.seo`.
- Source/fairness/right-of-reply checks must have explicit responsible permissions.
- Generic `workflow.review` must not grant specialist-check authority.

Record checker identity, outcome, notes, evidence references, timestamp and superseded check version.

### 7.5 Hard publication gates

Approval, scheduling and publication must be denied when any applicable condition is true:

- Any recorded required check is `failed` or `pending`.
- A critical check is missing.
- Attached media is not approved.
- Media license is expired or rights prohibit the intended usage.
- The author is attempting to approve their own work.
- High/critical-risk reporting lacks fact-check and legal approval.
- An allegation requiring right-of-reply has not completed the right-of-reply process or recorded a justified exception.
- Confidential-source handling classification is incomplete.
- Required correction/retraction explanation is absent.
- The scheduler or publishing actor has become inactive or lost permission.

Do not allow an editor to bypass a failed check by unchecking a “required” checkbox. A recorded failure is always a blocker until superseded by a later authorized pass.

### 7.6 Separation of duties

- Author and approver must be different users.
- For critical/high-risk investigations, require separate author, fact checker and final approver.
- Decide and document whether publisher must differ from approver; enforce it for critical content.
- Media uploader and media-rights approver must differ for high-risk/licensed media.
- Advertising staff must not edit or approve editorial stories except clearly labelled sponsored-content fields within defined boundaries.
- System administrators must not gain editorial content access merely because they administer infrastructure.

### 7.7 Concurrent editing

Add optimistic locking using a revision/version column or `updated_at` precondition. If two users edit the same article, reject stale writes and offer a comparison instead of silently overwriting work.

### 7.8 Revisions, corrections and retractions

- Preserve immutable article revisions.
- Record who changed what, when and why.
- Do not expose encrypted confidential fields in revision displays or diffs.
- Published corrections must have a public correction notice and retained original revision.
- Retractions must have a public transparency page or durable notice, not an unexplained 404.
- Keep canonical URLs and appropriate HTTP/search behavior for corrected/retracted content.

---

## 8. P0: CONFIDENTIAL SOURCES AND SENSITIVE EDITORIAL DATA

### 8.1 Data separation

Maintain separate fields and projections for:

- Public sources/citations.
- Internal source notes.
- Verification evidence.
- Editorial review notes.
- Legal review notes.
- Source confidentiality classification.

Public routes, API, RSS, sitemap, search, analytics and SEO serializers must use explicit allowlisted projections.

### 8.2 Encryption

- Use authenticated encryption such as AES-256-GCM.
- Store an envelope version and key identifier with every encrypted value.
- Support key rotation without decrypting all data into logs or temporary public files.
- Keep encryption keys outside the document root and repository.
- Load keys from a production secret manager or protected server environment.
- Document key backup, escrow, rotation and loss procedures.
- Never log plaintext, keys, IVs combined with plaintext, recovery secrets or decrypted evidence.

### 8.3 Access control and audit

- Apply least privilege to confidential fields.
- Log every view, decrypt, export and modification of confidential-source information.
- Log reason/purpose where policy requires it.
- Provide a source-access report restricted to compliance/editorial leadership—not ordinary technical administrators.
- Consider step-up authentication for restricted-source access.
- Prevent confidential data from appearing in browser autocomplete, public caches and error pages.
- Add appropriate `Cache-Control: no-store` headers to sensitive admin responses.

### 8.4 Retention and legal hold

Implement configurable retention states, legal hold, archival and approved deletion workflows. Do not automatically delete source data without policy and legal approval. Clearly mark this area `BLOCKED_EXTERNAL` until the publication approves its retention policy.

---

## 9. P0: AUTHENTICATION, MFA AND ACCOUNT SECURITY

### 9.1 Password authentication

- Use `password_hash`/`password_verify` with the strongest supported algorithm.
- Rehash on login when parameters change.
- Minimum password length: 14 characters; allow password managers and long passphrases.
- Do not impose composition rules that reduce usability without evidence.
- Prevent common/compromised passwords if a privacy-safe provider or offline list is approved.
- Return non-enumerating login/reset messages.

### 9.2 MFA

- Require MFA for privileged, editorial approval, source-access, finance and advertising approval roles.
- Rate-limit MFA separately from password authentication.
- Limit attempts per pending session and account.
- Apply exponential delay or temporary lockout.
- Expire pending MFA sessions strictly and regenerate session identifiers at appropriate boundaries.
- Store TOTP secrets encrypted.
- Generate single-use recovery codes; store only hashes.
- Show each recovery code only once.
- Provide an audited MFA reset process with reauthentication and a second authorized administrator or documented break-glass process.
- Alert on repeated failures, resets and recovery-code use.

### 9.3 Password reset

- Use random, single-use, hashed reset tokens.
- Short expiration.
- Invalidate all active sessions after successful reset.
- Do not disclose whether an email exists.
- Do not implement real email delivery without an approved provider and domain authentication.

### 9.4 Sessions

- Secure, HttpOnly and SameSite cookies; enforce Secure in production.
- Idle and absolute expiry.
- Session-version revalidation on every protected request.
- Revoke individual and all sessions.
- Show active sessions/devices when reliable metadata is available.
- Require step-up authentication for role changes, MFA reset, source export and critical system settings.

### 9.5 Abuse protection

- Account-level and IP/network-aware throttling.
- Reverse-proxy-aware client IP handling with an explicit trusted-proxy configuration.
- Do not trust arbitrary forwarded headers.
- WAF integration documented for production.
- Retention/cleanup job for login and MFA attempt records.

---

## 10. P0/P1: PROFESSIONAL CMS AND MEDIA LIBRARY

### 10.1 Article editor

Provide:

- Headline, deck, body, key points and language.
- Type/category/tag management.
- Public citations and encrypted internal evidence.
- Risk classification and right-of-reply tracking.
- SEO title, description, canonical/redirect controls and social preview.
- Autosave or reliable draft protection.
- Optimistic-lock conflict handling.
- Revision notes and comparisons.
- Preview using the exact public rendering path without publishing.
- Accessibility fields and validation.
- Scheduled publication with timezone made explicit.

Sanitize rich text with an allowlist if rich text is introduced. Never trust stored HTML merely because an authenticated editor created it.

### 10.2 Media pipeline

Support images first, then video/audio/documents through explicit adapters.

For every asset store:

- Private original.
- Public derivative(s), generated only through a safe decoder/encoder.
- Cryptographic hash.
- MIME detected from content.
- Size, width, height and duration where applicable.
- Creator, copyright holder, source organization and license.
- Usage restrictions, territory/platform restrictions and expiration.
- Caption, alt text and accessibility transcript/subtitles where applicable.
- Uploader, reviewer, approver and timestamps.
- Approval state: quarantined, pending, approved, restricted, rejected, expired.

### 10.3 Quarantine and approval

- Upload into private quarantine.
- Validate MIME, decode safely, enforce dimension/pixel/file limits and reject polyglots.
- Integrate an approved malware scanner or define a production scanner hook. Fail closed if required scanning is unavailable.
- Strip metadata from public image derivatives unless specific metadata is intentionally retained.
- Never make pending originals or derivatives public.
- Only approved, unexpired assets can be attached to publishable article roles.
- Link articles through `article_media` and `media_id`; do not treat a raw path as proof of approval.
- Revalidate rights at scheduling and publication time.
- If rights expire after publication, create an alert and remediation workflow.

### 10.4 Video/audio

- Do not claim local transcoding if it is not implemented.
- Support approved external embeds through strict provider/domain allowlists and privacy-enhanced URLs.
- Store licensing, transcript/captions, poster image and accessibility metadata.
- Define an adapter interface for a future storage/transcoding provider.

---

## 11. P1: ADVANCED NEWSROOM MANAGEMENT

### 11.1 Newsroom dashboard

- Personal assignments and deadlines.
- Section queues and bottlenecks.
- Stories awaiting fact/legal/media review.
- Scheduled publishing calendar.
- Corrections and retractions queue.
- Notification center.
- Role-scoped counts; never leak confidential titles or audience data to unauthorized users.

### 11.2 Assignments

Wire `editorial_assignments` into real workflows:

- Assign reporting, editing, fact-check, legal, copy, translation and media tasks.
- Status, due date, priority, assignee, assigner and notes.
- Reassignment history.
- Notifications on assignment, due-date change, revision request, approval and publication.

### 11.3 Homepage management

Wire `homepage_slots` to the public homepage:

- Lead story, secondary stories, breaking banner, investigations, video/photo and sponsored placements.
- Only published and eligible stories may be selected.
- Scheduled slot start/end.
- Preview and audit history.
- Automatic fallback if a story is unpublished, retracted or unavailable.

### 11.4 Notifications

Use persisted in-app notifications with read/unread state. Email/SMS/push notifications must remain provider adapters and `BLOCKED_EXTERNAL` until credentials and consent rules are approved.

---

## 12. P1: AUDIENCE, CONTACT AND NEWSLETTER

### 12.1 Public forms

- Server-side validation, CSRF, honeypot and rate limits.
- Size limits and spam/abuse controls.
- Queue or bounded processing for provider calls.
- Privacy notice and consent timestamp/version.
- Avoid storing unnecessary IP/user-agent data.
- Consistent behavior on MySQL and local SQLite where local preview support is promised.

### 12.2 Newsletter

- Double opt-in.
- Hashed verification tokens with expiry and single use.
- Confirmed subscription state.
- Signed/single-use unsubscribe flow.
- Suppression and bounce state.
- Consent proof and privacy-policy version.
- Export/deletion workflow according to approved policy.
- Email-provider adapter with retries and webhook verification.
- Do not send real mail until an approved provider, sending domain, SPF, DKIM and DMARC are configured.

### 12.3 Reader inbox

- Message type: general, correction, complaint, news tip, rights and privacy.
- Assignment, priority, status, internal notes and response deadline.
- Archive rather than hard delete by default.
- Restricted handling for tips and privacy requests.
- Do not present the ordinary contact form as a secure anonymous source-drop channel.

### 12.4 Privacy operations

Provide auditable workflows for access, correction, export, objection and deletion requests where applicable. Legal applicability and retention periods must be approved by qualified humans and marked `BLOCKED_EXTERNAL` until then.

---

## 13. P1: ADVERTISING AND COMMERCIAL SEPARATION

### 13.1 Advertisers and campaigns

- Advertiser profiles and contacts.
- Campaign creation, pricing model, budget, currency, start/end and status.
- Creative assets and rights approval.
- Placement and frequency rules.
- Independent approval before activation.
- Pause, cancel and archive workflows.
- Explicit `Advertisement` or `Sponsored` labels on all commercial content.

Advertising users must never change ordinary editorial approval or conceal sponsorship.

### 13.2 Sponsored content

- Separate content type and visible label.
- Record advertiser/sponsor.
- Apply editorial, legal and media-rights checks.
- Preserve a durable sponsorship disclosure.
- Do not enable covert native advertising.

### 13.3 Measurement

If impression/click measurement is implemented:

- Define bot filtering and deduplication.
- Avoid sensitive profiling by default.
- Apply retention and consent rules.
- Make billing reports reproducible and auditable.

---

## 14. P1: FINANCE

- Integer minor-unit amounts and ISO currency.
- Invoice number uniqueness and idempotency.
- Draft, issued, partially paid, paid, overdue, void and credited states.
- Payment posting with reference, method, amount, currency and received timestamp.
- Transactionally calculate balances and invoice status.
- Reject overpayment or require an explicit credit/on-account workflow.
- Reject currency mismatch unless an approved exchange workflow exists.
- Credit notes and adjustment audit trail.
- Never delete issued invoices or posted payments through ordinary UI.
- Separate campaign approval, invoice issuance and payment recording where practical.
- Export/report features must be authorized and audited.

Payment-gateway processing is `BLOCKED_EXTERNAL` until a provider, credentials, webhook verification and financial/legal procedures are approved.

---

## 15. P1/P2: ANALYTICS

- First-party, privacy-respecting page-view events.
- No raw IP storage unless legally justified and explicitly approved.
- Referrer host normalization.
- Bot filtering and documented limitations.
- Event retention and aggregation jobs.
- Article, category, author, source/referrer and publication-time reports.
- Separate editorial analytics from commercial analytics permissions.
- Do not call counts “unique users” without a defensible method.
- Add consent management before non-essential trackers.
- External analytics integrations remain optional adapters.

---

## 16. PUBLICATION QUALITY, SEO AND ACCESSIBILITY

### Public experience

- Preserve AFRIVOX NEWS’s established premium editorial identity.
- Responsive behavior across phone, tablet and desktop.
- Clear demo labels until all demo content is removed.
- No layout-breaking overflow, inaccessible menus or keyboard traps.
- Stable error/empty states.

### SEO

- Correct canonical URLs.
- NewsArticle/Article structured data based only on public verified fields.
- Open Graph and social metadata.
- RSS and sitemap pagination for large archives.
- Robots rules per environment; staging must not be indexed.
- Redirect management when slugs change.
- Proper behavior for corrected, unpublished and retracted stories.

### Accessibility

Target WCAG 2.2 AA:

- Keyboard navigation and visible focus.
- Semantic headings/landmarks.
- Form labels and useful errors.
- Color contrast.
- Reduced motion support.
- Alt text requirements.
- Captions/transcripts for multimedia.
- Screen-reader testing of critical public and admin flows.

---

## 17. DATABASE AND MIGRATIONS

### Required migration tooling

Create:

- Schema version tracking.
- Preflight command.
- Apply command or documented runner.
- Post-migration verification command.
- Backup/restore instructions.
- Migration report with row counts and checksums where appropriate.

### Repair the audited migration risks

- Map legacy contact statuses before modifying enums:
  - `in_review → in_progress`
  - `deleted → archived`
- Detect unknown statuses and stop instead of coercing them.
- Eliminate the uncontrolled plaintext window for legacy source migration.
- Require the encryption key to be valid before copying/migrating confidential fields.
- Encrypt in a controlled maintenance operation and verify every migrated envelope.
- Add missing foreign keys/indexes to upgraded installations, not just fresh installs.
- Verify primary/secondary role consistency.
- Decide how all existing users receive `force_password_change` and `mfa_required`; document operator impact.

### Compatibility

Test separately on the exact supported MySQL and MariaDB versions. Do not assume identical enum, check-constraint, `IF NOT EXISTS`, JSON or upsert behavior.

---

## 18. AUDIT, LOGGING AND OBSERVABILITY

### Audit events

At minimum audit:

- Login, logout, failed password and MFA attempts.
- MFA enrollment, reset and recovery-code use.
- Password reset/change.
- Role/permission changes.
- Account suspension/reactivation and session revocation.
- Confidential source view/decrypt/export/update.
- Article create/edit/transition/approval/schedule/publish/unpublish/correct/retract.
- Editorial-check changes.
- Media upload/scan/rights approval/rejection/expiry.
- Campaign, invoice, payment and system-setting changes.

Do not include secrets or confidential plaintext in audit details.

### Tamper evidence

- Export audit events to an append-only destination or implement hash-chained/signed batches.
- Restrict audit viewing separately from audit administration.
- Define retention and integrity verification.
- Alert on security-sensitive events.

### Runtime observability

- Structured application logs with request/correlation IDs.
- Centralized error collection without sensitive payloads.
- Health/readiness checks that do not reveal secrets.
- Uptime, scheduler, queue, storage and database alerts.
- Log rotation and retention.

---

## 19. BACKUP, RESTORE AND DISASTER RECOVERY

- Encrypted database backups.
- Private originals and derivative backups with documented relationship.
- Encryption-key backup/escrow separate from encrypted data.
- Scheduled backup verification.
- Retention policy.
- Restore into an isolated environment.
- Verify row counts, assets, confidential decryption, users, workflow history and checks after restore.
- Document RPO and RTO targets approved by the publication.
- Complete at least one recorded restore drill before production launch.

---

## 20. TESTING REQUIREMENTS

### 20.1 Unit tests

Cover:

- Permission evaluation and toxic-role combinations.
- Every allowed and forbidden workflow transition.
- Author/approver/publisher separation.
- Specialist-check permissions.
- Failed/missing/expired check gates.
- Encryption envelopes, key identifiers, wrong keys and rotation.
- TOTP windows, attempt limits and recovery-code single use.
- Media MIME, pixel, license and expiry validation.
- Money calculations and invoice status.

### 20.2 Database integration tests

Run against supported MySQL/MariaDB, not only SQLite:

- Fresh schema installation.
- Upgrade from a realistic legacy fixture.
- Re-running idempotent migrations.
- Enum/status mapping.
- Foreign keys, unique constraints and transactions.
- Concurrent revision writes.
- Scheduled publishing.
- Role/session invalidation.
- Invoice/payment idempotency.

### 20.3 HTTP/E2E tests

Automate these scenarios:

1. Public endpoints never contain confidential fields or values.
2. Reporter creates and submits a story but cannot approve/publish.
3. Author cannot self-approve.
4. Generic editor cannot pass legal/fact/media-rights checks without specialist permission.
5. Failed check blocks approval, scheduling and publication.
6. High-risk article requires fact/legal/right-of-reply gates.
7. Pending/rejected/expired media cannot be attached to a published story.
8. Approved licensed media can be published.
9. Scheduled job revalidates user, workflow and media rights.
10. Suspended account loses active sessions immediately.
11. Role change revokes or refreshes effective permissions.
12. MFA rate limit and recovery codes behave correctly.
13. Password reset tokens are single-use and expire.
14. CSRF, IDOR, stored/reflected XSS and SQL-injection attempts fail safely.
15. Contact/newsletter abuse limits work.
16. Correction/retraction remains transparent publicly.
17. Advertising and finance users cannot perform editorial actions.
18. System administrator cannot read confidential editorial content.
19. Financial retries do not duplicate invoices/payments.
20. Backup restore retains decryptable authorized confidential data and complete audit history.

### 20.4 Security verification

- Static analysis.
- Dependency and known-vulnerability scan.
- Secret scan of source and release archive.
- Upload/polyglot tests.
- Authorization/IDOR review.
- Session/cookie/header review.
- CSP review.
- OWASP-style manual test.
- Independent penetration test before public production.

### 20.5 Performance and resilience

- Representative article/archive/search load tests.
- Database index/query review.
- Large sitemap/archive pagination.
- Graceful behavior when mail, malware scanner, analytics or storage provider is unavailable.
- Scheduler idempotency and recovery.

---

## 21. REQUIRED ENVIRONMENTS

### Local development

- SQLite may remain for fast preview only.
- Local behavior must not be presented as proof of MySQL compatibility.
- Public forms promised in local preview must use portable SQL or explicit driver branches.

### Staging

Staging must include:

- The exact supported MySQL/MariaDB version.
- PHP with `pdo_mysql`, `openssl`, `fileinfo`, `mbstring` and `gd` or approved image library.
- HTTPS.
- Private storage outside the document root.
- Scheduler/cron.
- Test mail sink or approved sandbox.
- Malware-scanner test integration or explicit safe-fail behavior.
- Production-like headers, cookie settings and reverse-proxy configuration.
- `noindex` protection.

### Production

Production deployment remains blocked until every launch gate passes and the human user explicitly authorizes deployment.

---

## 22. DEPLOYMENT AND ROLLBACK

Produce a deployment runbook containing:

1. Pre-deployment backup and verification.
2. Maintenance/read-only activation.
3. Code deployment.
4. Migration preflight.
5. Migration execution.
6. Confidential-source encryption verification.
7. Cache/session considerations.
8. Scheduler installation.
9. Smoke and role-boundary tests.
10. Monitoring confirmation.
11. Maintenance-mode exit.
12. Rollback criteria and restore procedure.

Do not use an ad-hoc destructive down migration for editorial or financial history. Prefer code rollback plus tested database restore when a forward repair is unsafe.

---

## 23. DOCUMENTATION AND DELIVERABLES

Deliver all of the following:

- Updated application source.
- Versioned schema and migrations.
- Migration preflight and verification tooling.
- Automated unit, integration and E2E tests.
- Threat model focused on confidential sources, account takeover, malicious uploads, insider misuse and editorial/commercial conflicts.
- RBAC matrix.
- Editorial workflow and gate matrix.
- Data classification and retention draft.
- Key-management/rotation runbook.
- Backup/restore and disaster-recovery runbook.
- Incident-response runbook.
- Media licensing workflow.
- Deployment/rollback instructions.
- Staging verification report.
- Known limitations and external dependencies.
- Release notes.
- Release ZIP that excludes secrets, runtime data, local databases, logs and install locks.
- SHA-256 checksum for the final archive.

Keep `config.local.php`, `.env`, encryption keys, private media, databases and credentials out of the distributable archive.

---

## 24. REQUIRED FINAL REPORT FORMAT

Report results in this order:

1. Executive outcome.
2. Changed files and migrations.
3. Security controls implemented.
4. Editorial workflow controls implemented.
5. CMS/media/admin modules implemented.
6. Tests executed with exact pass/fail totals.
7. MySQL/MariaDB staging results.
8. Backup/restore results.
9. Browser/accessibility results.
10. `BLOCKED_EXTERNAL` items.
11. Remaining `NOT_IMPLEMENTED` items.
12. Release archive path and SHA-256.
13. Explicit verdict: `NOT READY`, `STAGING READY`, or `PRODUCTION READY`.

Never hide failed tests or convert missing verification into a pass.

---

## 25. DEFINITION OF DONE

The implementation is done only when:

- All P0 requirements are implemented.
- Every applicable P0 acceptance test passes.
- No pending, failed, rejected, restricted or expired requirement can be bypassed at approval/publication time.
- Confidential data remains excluded from all public surfaces.
- MFA has throttling, recovery and audited reset.
- Fresh install and legacy upgrade pass on supported MySQL/MariaDB.
- Valid media is processed and approved; invalid media is rejected; pending media is not public.
- Backup and restore drill passes.
- Role-by-role E2E tests pass.
- Release archive is secret-scanned and verified.
- Production monitoring and incident procedures exist.
- Required legal, privacy, regulatory and organizational decisions are approved by qualified humans.
- No unresolved P0 or P1 security finding remains.

If one of these conditions is not satisfied, the verdict must not be `PRODUCTION READY`.

---

## 26. STARTING INSTRUCTION

Begin by auditing the current source against this prompt and recording a requirement-to-code/test traceability matrix. Then implement P0 blockers in the required order, preserving the audited baseline and existing user data. Run relevant tests after each security boundary change. Continue through staging preparation and documentation as far as the available environment and authority permit.

When an external provider, real credential, legal determination or production deployment authorization is missing, mark only that item `BLOCKED_EXTERNAL`, continue all safe independent work, and do not fabricate completion.

The platform’s guiding principle is:

**Protect sources. Enforce editorial independence. Publish only verified work. Preserve accountability.**

