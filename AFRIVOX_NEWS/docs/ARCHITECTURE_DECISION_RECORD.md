# AFRIVOX NEWS architecture decision record

**Record date:** 30 October 2026  
**Architecture status:** accepted for the current staging candidate; production deployment remains subject to the release gates.

This document records consequential decisions and their trade-offs. Changes require a new superseding decision rather than silently rewriting history.

## ADR-001 — Use a secure modular monolith first

**Status:** Accepted  
**Decision:** Keep one PHP application and relational database, separated into public routes, administrative routes, application/security services, CLI jobs and operational documentation. Do not introduce microservices until measured scale or isolation requirements justify them.

**Rationale:** A lean newsroom needs reliable operations, low hosting complexity and understandable failure recovery more than distributed infrastructure. Service boundaries remain explicit enough to extract media, search, delivery or analytics later.

**Consequences:** Deployment is simpler and transactions are easier to reason about. The team must preserve module boundaries and avoid putting all policy into page controllers.

**Scale triggers:** sustained write contention, long-running media jobs, search quality limits, object-storage need, independent API clients or availability targets the monolith cannot meet.

## ADR-002 — MySQL/MariaDB is authoritative; SQLite is a local verification adapter

**Status:** Accepted with production gate  
**Decision:** Target MySQL 8.0.29+ or MariaDB 10.6+ in production, with the exact chosen release and SQL mode rehearsed in staging. Use SQLite only for local preview and deterministic policy/data-integrity tests.

**Rationale:** The target hosting environment supports PDO MySQL. SQLite makes local verification possible without pretending it proves MySQL DDL, locking or query behavior.

**Consequences:** Cross-engine queries must be explicit, and every release needs representative MySQL migration/concurrency testing. SQLite success alone cannot authorize production.

## ADR-003 — Keep one version-controlled permission policy

**Status:** Accepted  
**Decision:** Define role capabilities in `app/EditorialWorkflow.php`; persist user-to-role assignments, scopes and expiries in the database. Do not maintain a second editable database permission definition that can contradict code.

**Rationale:** Reviewable policy and tests reduce hidden privilege drift. Multiple roles remain possible without giving scoped or expired assignments global authority.

**Consequences:** Permission changes require reviewed code deployment. Every protected backend action must call policy checks; sidebar visibility is never authorization.

## ADR-004 — Enforce editorial work as a server-side state machine

**Status:** Accepted  
**Decision:** Persist article status, immutable snapshots, specialist checks and transition actors. Enforce independent approval, required fact/legal/source/media gates and optimistic locks in backend policy.

**Rationale:** Editorial integrity cannot depend on UI order. A state machine makes invalid transitions testable and auditable.

**Consequences:** Emergency publishing still requires recorded authority and reason. Scheduled jobs must revalidate current actor permissions and publication gates.

## ADR-005 — Separate public source references from protected source material

**Status:** Accepted  
**Decision:** Public projections may include only explicitly public references. Classified source notes/evidence use authenticated AES-256-GCM envelopes with key identifiers, least-privilege access and access logging.

**Rationale:** Confidential sources are a highest-value asset and must not leak through public article, search, feed, sitemap or API queries.

**Consequences:** Keys need independent custody, rotation and backup. Encryption does not replace secure devices, communication practices, retention policy or legal-hold decisions.

## ADR-006 — Quarantine media and require independent rights approval

**Status:** Accepted; operational pipeline is externally blocked  
**Decision:** Store originals outside public delivery, validate type/content, capture creator/licence/holder/expiry/alt metadata, produce safe derivatives and prevent an uploader from approving their own asset.

**Rationale:** Upload security and copyright risk are inseparable from newsroom workflow.

**Consequences:** Production must fail closed when required scanning or safe decoding is unavailable. A scanner, image processor and approved licence taxonomy are mandatory launch dependencies.

## ADR-007 — Use append-only evidence and optimistic concurrency

**Status:** Accepted  
**Decision:** Retain revisions, workflow transitions, source-access events and HMAC-linked audit records. Use `lock_version` or equivalent conditional updates for competing editorial decisions.

**Rationale:** Newsrooms need attributable history and protection from silent last-write-wins changes.

**Consequences:** Local chain verification detects modification but is not remote immutable storage. Production needs append-only export, retention approval and alerting.

## ADR-008 — Store money as integer minor units with explicit currency

**Status:** Accepted  
**Decision:** Persist invoice/payment amounts as integers plus ISO-style currency code; never use binary floating point for ledgers.

**Rationale:** Exact arithmetic and idempotency are prerequisites for reconciliation.

**Consequences:** Currency exponent and rounding rules must be defined by qualified finance review. An application ledger does not replace compliant accounting, tax invoicing or bank/provider reconciliation.

## ADR-009 — Use additive, reversible migrations

**Status:** Accepted  
**Decision:** Preserve existing records, use ordered migrations and preflight checks, take a verified backup and rehearse rollback before target deployment.

**Rationale:** Editorial history and source evidence must not be destroyed by an upgrade.

**Consequences:** Some MySQL alterations require a reviewed maintenance window. A migration is not approved merely because its file exists.

## ADR-010 — Make audience data consent-based and purpose-limited

**Status:** Accepted; lifecycle is partial  
**Decision:** Minimize contact/newsletter data, distinguish pending from verified subscription, store verification secrets as hashes, provide unsubscribe/suppression and throttle anonymous forms.

**Rationale:** Direct audience relationships are valuable but create privacy and abuse obligations.

**Consequences:** Real sending remains disabled until an approved provider, authenticated domain, privacy notice, retention schedule and subject-request process exist.

## ADR-011 — Keep reader comments disabled until moderation is staffed

**Status:** Accepted  
**Decision:** Do not expose a public comment feature as operational until moderation coverage, escalation rules, abuse handling, privacy/legal review and audit evidence are ready.

**Rationale:** Unstaffed comments create safety, defamation, harassment and operational risk.

**Consequences:** General feedback/correction intake may operate after its own controls, but it is not a secure anonymous tip channel.

## ADR-012 — Use human-reviewed multilingual relationships

**Status:** Accepted; implementation partial  
**Decision:** Model Kinyarwanda, English and French variants as distinct editorial records related to an original, each requiring human review and independent metadata. Emit alternate-language links only when a real published counterpart exists.

**Rationale:** Machine output must not be represented as verified translation, and false `hreflang` harms users/search engines.

**Consequences:** Translation assignment, terminology, reviewer and correction synchronization workflows remain to be completed.

## ADR-013 — Separate general sitemap and news sitemap responsibilities

**Status:** Accepted  
**Decision:** Keep a general sitemap for canonical archive discovery and a dedicated news sitemap limited to eligible recent publication records. RSS remains a separate distribution surface.

**Rationale:** News discovery has different recency and metadata requirements from long-lived archive discovery.

**Consequences:** Search Console ownership, real-domain submission and live structured-data validation remain external. Metadata never guarantees indexing.

## ADR-014 — Defer print/e-paper until independent commercial validation

**Status:** Accepted (`NO-GO`)  
**Decision:** Do not implement or procure owned print production now. Treat e-paper and outsourced print as a separate phase requiring measured demand, printer/distributor specifications, return controls, positive stress-tested unit economics and working capital.

**Rationale:** Print adds capital, deadlines, inventory, distribution and cash risks without demonstrated demand.

**Consequences:** Edition/page-planning/export/circulation modules remain `DEFERRED`, not missing launch blockers for the digital pilot.

## ADR-015 — Integrate external services through explicit adapters and fail-safe states

**Status:** Accepted  
**Decision:** Mail, malware scanning, payment, monitoring, object storage, transcoding and future AI services require named adapters, signed events/idempotency where relevant, timeouts/retries, audit and safe unavailable states.

**Rationale:** Provider credentials or a dashboard label do not prove operational integration.

**Consequences:** No service is reported as working until configured and acceptance-tested. Editorial truth, permissions and source confidentiality never delegate to an unreviewed provider.

## ADR-016 — Responsible AI is assistive, disclosed and human-accountable

**Status:** Accepted policy; tooling not started  
**Decision:** AI may assist transcription, translation drafts, metadata or research organization only under approved data handling, provenance and human editorial review. AI cannot autonomously approve, publish, fabricate sources or replace specialist checks.

**Rationale:** Speed cannot displace accuracy, rights, confidentiality or accountable judgment.

**Consequences:** Any future AI integration needs a risk assessment, vendor/data-flow review, usage register, labeling rule and measurable error/override process.

## Supersession process

An ADR may be superseded only by a new numbered record that states the changed context, alternatives, migration/rollback impact, security/privacy/editorial consequences, cost owner and approval evidence. Production-affecting decisions require technical, editorial and operational sign-off; legal/finance approval is added when applicable.
