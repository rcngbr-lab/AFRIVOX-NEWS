# AFRIVOX NEWS Ultimate implementation audit

**Audit date:** 30 October 2026  
**Audited target:** the supplied PHP/PDO AFRIVOX NEWS codebase in this release  
**Decision:** `CONDITIONAL GO` for an isolated staging pilot; **`NO-GO` for production launch** and **`NO-GO` for physical print** at this time.

This is an evidence-based implementation audit, not a licence, legal opinion, penetration test, financial assurance engagement or production acceptance certificate. The application is a substantial staging candidate, but the complete institution described in the Ultimate Master Prompt is not yet implemented or externally validated.

## 1. Status vocabulary

- `COMPLETE`: the scoped artifact or local behavior exists and has recorded evidence appropriate to this environment.
- `PARTIAL`: useful implementation exists, but required workflow, coverage or verification remains.
- `BLOCKED_EXTERNAL`: completion requires infrastructure, credentials, a provider, qualified professional approval, real operating data or deployment authority not present here.
- `DEFERRED`: deliberately excluded until a documented business or safety gate is met.
- `NOT_STARTED`: no material implementation was found.

`COMPLETE` never means licensed, production deployed or independently certified unless the row explicitly says so.

## 2. Audit method and evidence boundary

The review inspected public routes, administrative routes, application services, schema and migrations, CLI operations, local SQLite setup, tests, configuration templates and operational documentation. Primary evidence includes:

- `app/EditorialWorkflow.php`, `app/bootstrap.php`, `app/Security.php`, `app/MediaService.php` and `app/PasswordResetService.php`;
- `admin/index.php`, `admin/roles.php`, `admin/media-library.php`, `admin/modules.php`, `admin/newsroom-operations.php`, `admin/categories.php` and `admin/homepage-manager.php`;
- `schema.sql`, the ordered `migrations/` files and `tools/setup_local_sqlite.php`;
- public article, category, author, search, media, live, contact, newsletter, RSS, sitemap and read-only API routes;
- `tests/run.php` and the security/migration/audit CLI utilities;
- Phase 0, security, editorial, compliance, deployment and recovery documents under `docs/`.

The current merged increment passes **70/70 local policy and data-integrity tests**, a fresh SQLite setup/upgrade run, public HTTP smoke tests and authenticated render smoke tests for newsroom, taxonomy, homepage, live, media and management routes. These results supersede the 43/43 V3 baseline but do not substitute for MySQL/MariaDB, full browser/role E2E, malware scanning, mail delivery, accessibility/device testing, monitoring or restore evidence.

## 3. Existing functionality preserved

- Responsive Kinyarwanda-first public homepage, article, category, author, search, media, live, policy and contact experiences.
- Canonical article URLs, `NewsArticle` structured data, RSS and XML sitemap foundation.
- Prepared PDO access, output escaping, CSRF protection, hardened sessions and login throttling.
- Article CMS with retained revisions and audited workflow transitions.
- Public-versus-internal source-field separation and encrypted classified source material.
- Multiple staff roles, suspension, session revocation, MFA and recovery-code controls.
- Media quarantine, rights metadata and independent approval design.
- Audience, advertiser, campaign, invoice, payment and first-party analytics persistence foundations.
- Audit-chain, scheduled-publication, cleanup, migration-preflight and legacy-source-encryption CLI operations.

No destructive schema reset or replacement of existing editorial records is required by the new work. Existing deployments still need a verified backup and rehearsed additive migration procedure.

## 4. Phase readiness summary

| Prompt phase | Current status | Evidence | Decision / principal gap |
|---|---|---|---|
| Phase 0 — feasibility and discovery | `COMPLETE` as planning artifacts; validation is `BLOCKED_EXTERNAL` | `PHASE0_MEDIA_BUSINESS_PLAN.md`, `MARKET_AND_AUDIENCE_ASSESSMENT.md`, `ORGANIZATION_STAFFING_AND_RACI.md`, `EQUIPMENT_AND_PROCUREMENT_PLAN.md`, `FINANCIAL_MODEL_AND_RISK.md`, compliance checklist | `CONDITIONAL GO`; assumptions need interviews, supplier quotes, appointments, counsel and board approval |
| Phase 1 — secure publishing foundation | `PARTIAL` | Public site, authentication, RBAC, CMS, workflow gates, category/homepage management, media quarantine, SEO foundation, audit log | Strong local foundation; MySQL/browser/media/security staging evidence remains |
| Phase 2 — professional newsroom | `PARTIAL` | Pitches, desk decisions, assignments, calendar, specialist queues, revisions, corrections/retractions, source protection, taxonomy and homepage slots; merged local schema/tests pass | Full reporter-to-publication browser E2E, author administration and workload/handover reporting need depth |
| Phase 3 — audience and multimedia | `PARTIAL` | Typed/throttled contact, hashed-token double opt-in/unsubscribe lifecycle, search, sitemaps, analytics, media/live foundations | Actual mail delivery, reader accounts, comments/moderation, video/audio pipeline and advanced audience reporting remain incomplete or externally blocked |
| Phase 4 — commercial system | `PARTIAL` | Advertisers, campaign submit/independent approval/status flow, invoices, payment recording, identity separation, idempotency and dashboard foundations | No full CRM/quote/contract/creative/ad-delivery/external-reconciliation acceptance path; no payment or accounting integration |
| Phase 5 — newspaper/print | `DEFERRED` | Business, equipment and financial gate documents only | `NO-GO` until demand, printer/distributor terms, returns and positive unit economics are verified |
| Phase 6 — international readiness/scale | `PARTIAL` | Language fields, translation relationship, API/SEO foundations, scale triggers documented | Translation operations, institutional subscriptions, object storage/CDN/queue/search and advanced data tools remain |
| Phase 7 — production verification | `PARTIAL` | Local tests, lint/preflight/audit tools and runbooks | Real MySQL, E2E/browser/accessibility/load/restore/monitoring/penetration/regulatory evidence remain |

## 5. Material findings

### P0 — must block production

| Finding | Risk | Current control | Required closure |
|---|---|---|---|
| No real MySQL 8/MariaDB migration rehearsal | Schema differences or migration failure can corrupt/interrupt deployment | Additive migrations and preflight exist; SQLite preview is exercised | Rehearse backup, ordered migration, post-check and rollback on representative MySQL staging |
| Media scanning and positive image pipeline not available locally | Malicious or malformed files; inability to publish approved media safely | Quarantine, MIME/dimension checks, independent approval and production fail-closed flag | Provision GD/Imagick as selected, malware scanner, safe public derivatives and malicious-file tests |
| No production secrets/append-only audit/monitoring infrastructure | Undetected compromise or unverifiable audit history | AES-GCM envelopes, key IDs, HMAC audit chain and local verifier | Approved secret custody/KMS approach, remote append-only export, alerts and on-call owner |
| Backup/restore and disaster recovery are unproven | Irrecoverable loss or excessive downtime | Runbooks exist | Encrypted backup, offsite copy, representative restore drill and approved RPO/RTO |
| Legal, data-protection, tax and media obligations unconfirmed | Unlawful operation or invalid claims of compliance | Official-source readiness checklist exists | Qualified applicability decisions, registrations/licences where required and retained evidence |
| Full role/browser E2E not run | Policy/UI mismatch may permit or prevent critical work | Server-side policy and unit/integration tests exist | Exercise every role, suspension, MFA, self-approval denial and critical workflow in staging |

### P1 — required before an operational public pilot

| Finding | Status | Required next evidence |
|---|---|---|
| Newsroom pitch/assignment/calendar increment | `PARTIAL` | Fresh-schema and migration tests, role E2E, concurrency/reassignment/handover acceptance |
| Contact/newsletter abuse and consent lifecycle | `PARTIAL` | DB throttling/honeypot, verified double opt-in, unsubscribe/suppression, approved mail provider and privacy retention |
| Homepage curation and taxonomy | `PARTIAL` | Migration integration, public rendering, fallback behavior and editorial-role acceptance |
| Correction/complaints operations | `PARTIAL` | End-to-end correction, public notice, complaint assignment, SLA/escalation and audit evidence |
| Accessibility/mobile/performance | `PARTIAL` | Keyboard and screen-reader audit, representative low-end Android/iOS checks and measured Web Vitals under constrained bandwidth |
| Commercial control path | `PARTIAL` | Quote/contract/creative approval, delivery evidence, invoice/payment separation, reconciliation and report totals |

### P2 — growth only

- Reader accounts, saved stories, memberships and institutional subscriptions.
- Staffed comment moderation; comments should remain off until coverage and legal procedure exist.
- Video/audio ingest, transcoding, captions/transcripts and streaming/storage integration.
- Object storage/CDN, queue workers, dedicated search and advanced analytics after measured triggers.
- Data-journalism provenance tooling and AI-assistance register with mandatory human review.

## 6. Security assessment

### Effective local controls

- Backend state-machine authorization; reporters cannot publish and authors cannot approve their own work.
- Exact specialist permissions for fact, legal, copy, rights and other editorial checks.
- Active-account and session-version revalidation, suspension and role-change revocation.
- Privileged MFA, throttling and single-use recovery codes.
- Explicit public article projection excludes internal source fields.
- Authenticated AES-256-GCM envelopes with key identifiers for classified source data.
- Optimistic locking for articles and newsroom decisions.
- Private media originals, independent approval and rights/expiry checks.
- HMAC-linked audit events and verifier utility.
- CSRF protection, prepared statements, output encoding and password hashing.

### Residual security risks

- Shared-host/web-root layout needs target-server denial verification; `.htaccess` is not portable to Nginx.
- No independent penetration test, dependency/SAST scan record or WAF/bot-control validation.
- Local hash chaining is not equivalent to remote immutable evidence.
- Source safety still depends on human device, communication, retention and legal-hold procedures.
- No phishing-resistant MFA or managed-device assurance.
- Media malware and polyglot testing is blocked until scanner/processor infrastructure exists.
- Public PII workflows need approved retention, subject-request and breach-response operations.

## 7. Mandatory acceptance-scenario record

| # | Scenario | Status | Evidence / exact remaining work |
|---:|---|---|---|
| 1 | Reporter receives assignment and submits draft | `PARTIAL` | Assignment and article submission components exist; integrated browser E2E pending |
| 2 | Editor requests corrections and approves corrected version | `COMPLETE` locally for article state policy | Browser role E2E and new assignment linkage pending |
| 3 | Approved article appears with correct metadata/byline/date | `PARTIAL` | Route and publication logic exist; staging scheduler/browser/structured-data validation pending |
| 4 | Unauthorized user cannot retrieve internal sources | `COMPLETE` locally | Public-projection tests and encrypted storage evidence; penetration/production configuration pending |
| 5 | Licensed media upload → independent approval → display | `BLOCKED_EXTERNAL` | Fail-closed controls exist; no GD/scanner positive E2E in this environment |
| 6 | Approved live event and timestamped updates | `PARTIAL` | Backend live event lifecycle, reporter draft, independent approval, pin/correction/withdraw controls and public route exist; complete multi-user browser action E2E remains |
| 7 | Newspaper edition plan → proof → export | `DEFERRED` | Print/e-paper is `NO-GO` pending commercial validation |
| 8 | Advertiser → quote/contract/campaign/report | `PARTIAL` | Advertiser/campaign foundation exists; quote/contract/creative/delivery path incomplete |
| 9 | Invoice → payment → reconciliation/report | `PARTIAL` | Integer-money ledger exists; reconciliation/authorization/report acceptance incomplete |
| 10 | Feedback/comment moderation | `PARTIAL` | Contact inbox exists; comments and complete moderation are not implemented |
| 11 | Suspension immediately blocks protected access | `COMPLETE` locally | Session-version/account revalidation covered; browser concurrency E2E pending |
| 12 | Correction shows publicly with revision history | `PARTIAL` | Correction/revision mechanisms exist; full public browser acceptance pending |
| 13 | Backup restores into test environment | `BLOCKED_EXTERNAL` | Runbook only; backup destination and representative drill absent |
| 14 | Regression of existing pages/workflows | `COMPLETE` locally | Current merged suite passes 70/70; fresh SQLite setup, public HTTP matrix, XML parsing and authenticated admin render matrix pass. Target MySQL/browser matrix remains a production gate |
| 15 | Mobile and assistive-technology completion | `BLOCKED_EXTERNAL` | Responsive/focus/reduced-motion foundations exist; human/device/AT audit absent |

## 8. Hosting and operational constraints

- Production target is PHP 8.1+ with PDO MySQL and HTTPS; local validation uses PHP/SQLite and cannot prove target-database behavior.
- Local PHP lacks `pdo_mysql`, GD and Imagick; no scanner is connected.
- Mail, monitoring, remote audit, object storage/CDN, payment and identity integrations have not been selected or authorized.
- Demonstration stories and placeholder identity/policy material must not be represented as real journalism or final legal text.
- `install.php` must be removed after controlled installation; secrets must remain outside public/release artifacts.
- Scheduled publication, cleanup, verification, backup and alert jobs require a named operator and scheduler.

## 9. Prioritized next actions

1. Provision representative HTTPS/MySQL or MariaDB staging with required PHP extensions and no production data.
2. Re-run the 70-test baseline plus the complete multi-role browser acceptance matrix on that target stack.
3. Rehearse migration and rollback from a verified backup; record counts, constraints and decryption checks.
4. Configure scanner/image processing, mail delivery, monitoring and append-only audit destinations.
5. Run all 15 acceptance scenarios with evidence; retain failures as launch blockers.
6. Complete accessibility, low-bandwidth performance, security and secret/dependency reviews.
7. Obtain legal/DPO/tax/media applicability decisions and approve retention, complaints, corrections, licensing and sponsored-content procedures.
8. Validate reader demand, advertiser contracts, costs and cash runway with real evidence.
9. Approve a signed release checklist and rollback owner before any production change.
10. Keep print/e-paper inactive until a separate board-approved business gate passes.

## 10. Audit conclusion

AFRIVOX NEWS has a credible secure-editorial foundation and useful operational modules, not a finished world-class media institution. The correct current classification is **staging candidate**. The next release decision is `CONDITIONAL GO` for isolated staging work, while public production remains `NO-GO` until the P0 evidence above is complete and explicitly approved.
