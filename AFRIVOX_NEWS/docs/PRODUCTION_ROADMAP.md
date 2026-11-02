# IMPLEMENTATION ROADMAP — production scope vs delivered prototype

## Phase 0 — Entity, compliance, operational feasibility

**Gate:** Confirm company/entity and publication type, RMC/RURA requirements, tax and invoicing setup, brand/domain rights, editorial responsibility, copyright agreement templates, data-protection registration/roles, retention and safeguarding procedures. Select 3-5 desks to start. Budget reporting + people rather than just code.

## Phase 1 — Hardening current PHP MVP

- Test on real cPanel PHP + MySQL, document server versions, enable required extensions.
- Configure production DNS/TLS; security headers; private secrets outside web root.
- Remove install file and demo stories; replace site/legal identity and placeholder privacy page.
- Implement media processing via GD/Imagick and automatic responsive thumbnail generation, metadata stripping, alt text and rights ledger.
- Add account password reset, MFA, roles/permissions tests, disable user, revoke sessions, per-user audit.
- Implement mandatory editor signoff with immutable revisions/approval decisions; support editor rejection with reason.
- Add permanent URLs, redirect management and page-not-found recovery, complete pagination for sitemap and archives.
- Add double opt-in mailing, confirmed consent evidence, unsubscribe endpoints, suppression lists and deletion requests; turn off signup until compliant.
- Add backups (DB + images) and restore drill; test availability, load, recoverability, GDPR-style privacy review appropriate to Rwanda law, and accessibility.
- Configure real analytics and dashboards; quality test 10 device/browser combinations including low-bandwidth Android.

## Phase 2 — Commercial/media capabilities

- Advertiser campaigns, placement booking, creatives, sponsor labeling, impression/click metrics with privacy controls, invoice and cash collection reporting.
- Photo essay album editor, caption and credit workflow, reusable media library, multi-image upload.
- Podcasts/video: licensed ingest, YouTube or professional video platform, captions, manifests, audio transcription and playback.
- Dynamic home-layout editor, top stories and breaking alerts with review gate.
- Topic tags, multi-author stories, related article curation, multiple languages and translations with review.
- Editorial calendar, assignments, evidence and source document handling.
- Moderated reader comments only if staffed and legally assessed; otherwise omit.
- Data journalism charts from audited source datasets and reproducible transformations.

## Phase 3 — Growth / enterprise-grade

- CDN caching, image pipeline/object storage, Redis and queue workers, observability and alerting.
- REST/GraphQL API only if needed for mobile or partner syndication, with versioning and API authentication.
- Subscription portal with billing, MoMo/card payment gateway as lawfully integrated, subscription cancellations/refunds, receivables and tax invoices.
- Growth suite with newsletters, cohort retention, A/B headline tests with no misleading clickbait, personalization with privacy safeguards.
- Secure tip intake architecture independent from the general contact form, newsroom source-protection training.
- Formal BCP/DR, vendor management, code security assessment, penetration test and ongoing vulnerability scans.

## Acceptance criteria before any public news launch

- Publishing pipeline: reporter cannot publish, editor can, correction appears publicly, timestamps and bylines accurate.
- Login: no weak credentials; rate limits, sessions, CSRF and reset protected; MFA for editorial approval.
- Image: cannot upload executable files; strips EXIF; resizes; permissions documented; credit/caption visible.
- Accuracy: every factual article has documented evidence and editor sign-off, review templates by desk.
- Privacy: finalized notices, lawful data collection, processor records, unsubscribe/deletion and access requests tested.
- Security: tested backups and restores, monitoring and error handling, HTTPS, server restrictions.
- SEO: URL canonical, Article JSON-LD, robots, XML sitemap, OG image, RSS, indexing and redirects validated.
- Performance: measure real page metrics on low-bandwidth mobile; validate accessibility.
- Legal: proper authorizations/registration confirmed by competent agencies and counsel.
- Business: confirmed editorial staffing and at least initial commercial demand validation.

## Roles and rough operating model

- Editor-in-chief / legal accountability, section editor(s), reporters, photo/video producer, growth/audience editor, digital ad salesperson, system developer/IT security, bookkeeping and counsel (contracted at pilot stage where appropriate).
- Management reports: article throughput, review delay, corrections, referrals, repeat readership, signed contracts, money collected, costs and cash runway; do not chase pageviews at the expense of trust.
