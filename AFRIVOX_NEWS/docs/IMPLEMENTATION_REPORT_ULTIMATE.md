# AFRIVOX NEWS Ultimate implementation report

**Report date:** 30 October 2026  
**Overall classification:** staging candidate; production `NO-GO`.  
**Status labels:** `COMPLETE`, `PARTIAL`, `BLOCKED_EXTERNAL`, `DEFERRED`, `NOT_STARTED`.

This report follows the required A–K format for every implementation phase. Costs in the planning documents are scenarios, not market quotations or actual financial results.

## Phase 0 — feasibility and discovery

### A. Scope

Audit the supplied project and produce the market/audience assessment, business model, legal readiness checklist, editorial operating model, staffing/RACI plan, equipment/procurement plan, financial model, technical audit and architecture decisions.

### B. Existing functionality

The existing product already contained a coherent public news MVP, basic newsroom, PHP/MySQL schema and strategic/roadmap notes. Those were preserved and reassessed rather than replaced.

### C. Implementation

Created/expanded `PHASE0_MEDIA_BUSINESS_PLAN.md`, `MARKET_AND_AUDIENCE_ASSESSMENT.md`, `ORGANIZATION_STAFFING_AND_RACI.md`, `EQUIPMENT_AND_PROCUREMENT_PLAN.md`, `FINANCIAL_MODEL_AND_RISK.md`, `RWANDA_COMPLIANCE_READINESS_CHECKLIST.md`, `ULTIMATE_IMPLEMENTATION_AUDIT.md` and `ARCHITECTURE_DECISION_RECORD.md`.

The recommendation is a narrow digital-first pilot with three to five supportable desks, measured reader/advertiser discovery, adequate runway and strict editorial/commercial separation. Owned printing is rejected.

### D. Database changes

None required for planning artifacts.

### E. Permissions

Governance responsibilities and separation are documented. Named appointments and delegations must be approved externally.

### F. Tests

Internal consistency/code-evidence review completed. Market demand, price quotes, staffing credentials and regulatory applicability are not software tests and remain unverified.

### G. Security findings

Confidential sources, staff identity, unpublished investigations, media rights, audience PII, finance ledgers and audit evidence are the highest-value assets. Production infrastructure evidence is absent.

### H. Operational dependencies

Board/publisher decisions, Editor-in-Chief, legal/DPO/accounting advice, business/media/tax evidence, staff, supplier quotes, hosting and service contracts.

### I. Financial implications

Startup, monthly cost, runway, forecast and break-even scenarios are documented in RWF with explicit assumptions. They are not quotes or commitments; the base scenario does not assume early break-even.

### J. Completion status

`COMPLETE` as planning/audit artifacts; real-world validation is `BLOCKED_EXTERNAL`.

### K. Release decision

`CONDITIONAL GO` for a narrow, funded digital pilot discovery/staging program. `NO-GO` for production launch, owned studio expansion and physical print.

## Phase 1 — secure publishing foundation

### A. Scope

Public website, authentication, backend-enforced RBAC, article CMS, category administration, editorial approvals, basic media library, SEO and core audit logging.

### B. Existing functionality

Responsive public pages, article/category/search/media routes, prepared PDO queries, escaping, CSRF, password hashing, basic throttling, RSS/sitemap and a simpler newsroom were present and retained.

### C. Implementation

- Hardened session/account revalidation, suspension, revocation, password reset foundation, privileged MFA/recovery and role assignment.
- Version-controlled permission source with multiple database role assignments.
- Server-side editorial state machine, independent approval, specialist gates, optimistic locks, revisions and scheduling.
- Encrypted classified source fields, access logging, key identifiers and local audit hash chain.
- Quarantined, rights-aware media with independent approval and fail-closed processing requirements.
- Persistent category and homepage administration increments.
- Canonical/structured metadata, RSS and sitemap foundation.

### D. Database changes

Users/roles/user roles, article workflow fields, revisions, transitions, specialist checks, source access, MFA/reset/attempt records, media assets/linking, homepage slots, settings and audit tables are defined in `schema.sql` and ordered migrations. Current category hierarchy/language/activity changes require the latest additive migration and regression.

### E. Permissions

Editorial creation, editing, review, approval, publication, source access, media approval, staff management, taxonomy and homepage operations use backend capabilities. Reporters cannot publish; system/commercial/finance roles have no editorial publish path.

### F. Tests

Current merged baseline: **70/70 local policy/data-integrity tests**, fresh SQLite setup/upgrade, public HTTP/XML smoke tests and authenticated admin render smoke tests pass. MySQL/MariaDB migration, full browser role matrix and positive media E2E are not complete.

### G. Security findings

Strong application controls exist. Remaining P0 risks are target-server configuration, production key custody, scanner/image processor, remote immutable audit, monitoring, MySQL rehearsal and independent security testing.

### H. Operational dependencies

HTTPS/MySQL staging, required PHP extensions, scanner, secret custody, mail/monitoring/audit providers, scheduler and named security/editorial operators.

### I. Financial implications

Ongoing hosting, backups, monitoring, mail, scanner, security review and operator time are unavoidable. Cheapest shared hosting is not automatically suitable for protected-source or media operations.

### J. Completion status

`PARTIAL`: secure local foundation is implemented; target/integration/acceptance evidence is incomplete.

### K. Release decision

`CONDITIONAL GO` for isolated staging. Production remains `NO-GO`.

## Phase 2 — professional newsroom

### A. Scope

Pitches, desk decisions, assignments, review/specialist queues, corrections, revision history, calendar, source protection, homepage curation and author operations.

### B. Existing functionality

Article ownership, drafts/submission, specialist states, review decisions, approval/scheduling/publication, revisions, transitions, notifications foundation and author records already existed.

### C. Implementation

Added a persisted newsroom operations surface for pitch submission/resubmission, accept/reject/clarification/hold/merge decisions, reporting assignment, due date/priority/briefing, assignee start/complete/cancel, notifications and calendar views. Added taxonomy and curated homepage administration. Existing correction/retraction, source and workflow controls were preserved.

### D. Database changes

`story_pitches`; pitch/article/assignment linkage; priority, briefing, status and timing fields; category hierarchy/language/activity; existing homepage slots. The additive migration must be rehearsed against real legacy data.

### E. Permissions

Pitch creators and assignees operate only their work; newsroom managers make desk/assignment decisions; article review/approval/publish remains separately enforced. Permission integration and role E2E must be confirmed in the final merged build.

### F. Tests

The merged suite covers pitch state, optimistic-lock conflict, assignment lifecycle/target integrity and taxonomy hierarchy; the relevant authenticated admin routes render successfully in local HTTP smoke testing. Full reporter → desk → assignment → article → specialist → publication browser E2E plus merge/reassignment/handover remain pending on staging.

### G. Security findings

Optimistic locking and scope filtering reduce race/overreach risk. Ensure pitches never become an alternate plaintext confidential-source store; sensitive evidence belongs in protected source/evidence fields.

### H. Operational dependencies

Named desk owners, assignment/handover procedure, staffing/capacity limits, escalation coverage, source-safety training and high-risk legal access.

### I. Financial implications

Assignments may record expected reporting cost, but approval/budget controls and actual expense reconciliation are not complete. Management must fund reporting time, travel and specialist review.

### J. Completion status

`PARTIAL`.

### K. Release decision

`CONDITIONAL GO` for staged newsroom pilot after migration and role E2E; not yet production accepted.

## Phase 3 — audience and multimedia

### A. Scope

Advanced photo operations, video/podcasts, newsletters, search, reader feedback, analytics, moderated comments and live news.

### B. Existing functionality

Search, media archive, contact form/inbox, pending subscriber registry, first-party analytics table, live-event public view and article media fields existed.

### C. Implementation

Rights-aware media and public media foundations were strengthened. Audience work implements database-backed public-form abuse controls, pending/verified newsletter lifecycle, hashed verification and unsubscribe tokens, suppression behavior and separate general/news sitemap duties. Live operations now provide a backend event lifecycle, reporter draft updates, independent editor approval, pinning, corrections that preserve original text, withdrawal, notifications and audit events.

### D. Database changes

Subscriber consent/verification/unsubscribe lifecycle and public-form attempts are integrated into `schema.sql`, the SQLite setup/upgrade tool and the additive audience migration, alongside contact, analytics, live event/update, notification and media tables. MySQL/MariaDB execution remains a staging gate.

### E. Permissions

Audience/support roles can manage inbox/subscribers; audience/SEO roles view relevant analytics; multimedia specialists manage/approve assets under separation. Reporters may draft live updates but cannot approve them; authorized editors manage events and independently approve/moderate updates. Comments remain unavailable until a moderator policy exists.

### F. Tests

Local tests cover hashed newsletter tokens, confirmation/unsubscribe single use, suppression, public-form throttling, live permission separation, independent approval and correction preservation; the public signup/contact/verification/unsubscribe routes and both XML sitemaps pass HTTP/XML smoke checks. Positive media processing, actual mail delivery/webhooks, video/audio pipeline, full live multi-user browser actions and comments E2E are not complete.

### G. Security findings

Verification secrets must be hashed; responses must avoid account enumeration; unsubscribe must be safe and auditable; public forms need layered abuse controls. General contact is not secure whistleblower intake.

### H. Operational dependencies

Approved mail domain/provider, DPO/retention policy, audience owner, moderator coverage, scanner/storage/transcoding, captions/transcription and live-news shifts.

### I. Financial implications

Mail, moderation, storage/egress, media production and accessibility have recurring cost. Video/audio should expand only after cadence and audience value are measured.

### J. Completion status

`PARTIAL`; mail/media providers are `BLOCKED_EXTERNAL`; comments are `DEFERRED` until staffed.

### K. Release decision

Search/basic feedback may enter staging. Sending, comments and advanced media are `NO-GO` until their controls pass.

## Phase 4 — commercial system

### A. Scope

Advertisers, CRM, campaigns, inventory/creative approval, invoices, payment reconciliation and commercial reporting.

### B. Existing functionality

Advertiser, campaign, invoice, payment and analytics tables plus a basic multi-module admin page existed.

### C. Implementation

Persistent advertiser/campaign and integer-money invoice/payment foundations now include campaign submit/independent approval/status transitions, idempotent invoice/payment identifiers, invoice balance updates, audit events and an invoice-creator/payment-recorder identity check. Editorial/commercial permission separation remains enforced. Phase 0 documents add pricing, contracts, concentration, receivable and editorial-firewall requirements.

### D. Database changes

Advertisers, campaigns, invoices and payments exist. Complete CRM leads/opportunities/activities, quotes/contracts, creative/placement/delivery, reconciliation sessions, credits/refunds and chart-of-account integration are not complete.

### E. Permissions

Advertising manages commercial records; finance manages financial records/reports; neither receives editorial publication authority. Separation between sales, settlement recording and reconciliation needs stronger workflow enforcement.

### F. Tests

Persistence foundations have local coverage, but the mandatory advertiser → quote/contract → creative → campaign → delivery and invoice → payment → reconciliation/report scenarios are not accepted end to end.

### G. Security findings

Commercial contacts/payment references are personal/financial data. Access, exports, changes, idempotency, reconciliation and audit must be restricted; no card data should be collected by this application without a separately approved scope.

### H. Operational dependencies

Commercial owner, finance owner, contract/rate-card templates, accounting/tax review, bank/payment evidence, ad serving/measurement decision and real signed customers.

### I. Financial implications

Revenue is recognized only from approved policy and actual evidence; unsigned pipeline is not cash. Payment/accounting integrations, sales effort and receivable risk add cost.

### J. Completion status

`PARTIAL`.

### K. Release decision

`CONDITIONAL GO` for internal staging/record design; `NO-GO` as a complete production CRM/accounting/ad-delivery system.

## Phase 5 — newspaper and print operations

### A. Scope

Edition planning, e-paper, pages, proof/approval, print-ready export, circulation, distribution and returns—only after independent commercial validation.

### B. Existing functionality

No operational print module. Strategy, procurement, financial formulas and go/no-go gates exist.

### C. Implementation

No software module was implemented by design. The documents specify outsourced-pilot evidence, printer technical terms, distribution custody, returns and unit economics.

### D. Database changes

None.

### E. Permissions

No print permissions are active. Future roles must separate editorial approval, prepress release, circulation custody and finance reconciliation.

### F. Tests

Mandatory newspaper edition scenario: `NOT TESTED` because the phase is `DEFERRED`.

### G. Security findings

Premature print introduces embargo/confidentiality, unreleased-page, file integrity, supplier access and cash/inventory risks.

### H. Operational dependencies

Verified demand, applicable permits, printer/distributor contracts, prepress expertise, proofing standard, working capital and accountable staff.

### I. Financial implications

Potentially material printing, spoilage, delivery, returns and working-capital exposure. No actual supplier economics exist.

### J. Completion status

`DEFERRED`.

### K. Release decision

`NO-GO`.

## Phase 6 — international readiness and scale

### A. Scope

Multilingual operations, advanced analytics, institutional subscriptions, integrations, infrastructure scaling and advanced/data reporting.

### B. Existing functionality

Kinyarwanda-first interface, `rw/en/fr` content fields, translation relationship, SEO/API foundation, analytics events and documented scale triggers.

### C. Implementation

Language-aware article model and human-review policy are present. Architecture decisions define modular scaling, adapter-based integrations, real translation counterparts and responsible AI boundaries.

### D. Database changes

Language/original/translation-status fields exist. Translation assignments, terminology/version tracking, institutional accounts/subscriptions, data-source provenance and scale-service state are incomplete.

### E. Permissions

Translation reviewers and institutional/subscription operators need explicit future permissions. Existing editorial approval must remain mandatory for every language.

### F. Tests

No full multilingual assignment/correction synchronization, institutional subscription, CDN/cache/queue/search or data-journalism provenance E2E has been recorded.

### G. Security findings

Cross-border storage/transfers, translation-provider confidentiality, payment identity, API abuse and AI-provider data use require separate assessment.

### H. Operational dependencies

Qualified translators/editors, demand, provider contracts, DPO transfer decisions, object storage/CDN/queue/search and observability.

### I. Financial implications

Translation, support, storage/egress, delivery, payment and infrastructure costs must be justified by measured audience/revenue needs.

### J. Completion status

`PARTIAL`.

### K. Release decision

`DEFERRED` beyond language-ready foundations until validated triggers exist.

## Phase 7 — production verification

### A. Scope

Security, permission, end-to-end, database-integrity, load, accessibility, SEO, recovery, operational and regulatory acceptance.

### B. Existing functionality

Automated policy/data tests, PHP lint process, SQLite setup, migration preflight, audit verifier, scheduled publishing, cleanup utilities and deployment/recovery/incident runbooks.

### C. Implementation

Created an evidence-based audit, full requirement traceability and release checklist. Existing test tools and stop/rollback conditions are retained.

### D. Database changes

None solely for verification. All current migrations must be included in the final rehearsal.

### E. Permissions

Release, migration, backup, key, incident and production authorization require named operational owners. Application super-admin is not automatic business/editorial release authority.

### F. Tests

Recorded local V3 baseline: 43 passed, 0 failed. The merged Ultimate increment requires a fresh run. MySQL, browser roles, positive media, mail, load, device/assistive technology, restore, monitoring and independent security tests remain incomplete or blocked.

### G. Security findings

Unresolved P0 external evidence prevents production classification: scanner, key/secret custody, remote audit, observability, restore, staging MySQL and independent review.

### H. Operational dependencies

Representative staging, test accounts/devices, providers, monitoring/on-call, backup targets, release window and legal/organizational acceptance.

### I. Financial implications

Staging, test devices, professional review, backup/monitoring and penetration/accessibility work must be budgeted. Verification is a release cost, not optional overhead.

### J. Completion status

`PARTIAL`; multiple mandatory scenarios are `BLOCKED_EXTERNAL` or `DEFERRED`.

### K. Release decision

`CONDITIONAL GO` for further isolated staging and remediation. **Production `NO-GO`.**

## Final release statement

The codebase has progressed well beyond a static mockup and includes meaningful persisted backend controls. It does not yet implement or verify every institution, commercial, audience, multimedia, print and production requirement in the Ultimate Prompt. No section of this report authorizes live confidential-source, customer, financial or production use. Release status may change only through the evidence process in `RELEASE_CHECKLIST_ULTIMATE.md`.
