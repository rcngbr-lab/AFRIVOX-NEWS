# AFRIVOX NEWS — Digital Newsroom & Media Platform (staging candidate)

**Version:** Ultimate phased implementation candidate, 30 October 2026  
**Language:** Kinyarwanda and English public interface; article translations appear when published in the CMS.  
**Deployment target:** PHP 8.1+ with PDO MySQL, MySQL 8.0.29+ or MariaDB 10.6+, Apache/cPanel and HTTPS. The exact database release and SQL mode must pass the staging migration/rollback rehearsal.

## What is included

- Screenshot-led AFRIVOX homepage with bilingual header, exact newspaper navigation, three-column hero, category strip, featured-news grid, newsletter and branding sidebar.
- Responsive category archive, article view, search and photo/video archive, with desktop/tablet/mobile layouts and no static replacement of CMS content.
- Article bylines, timestamps, explicitly public sources, correction notices and clearly flagged demo content.
- SEO: per-article JSON-LD `NewsArticle`, metadata, canonical URLs, RSS feed, XML sitemap.
- Secure newsroom login with idle/absolute expiry, database revalidation, session-version revocation, separately throttled TOTP MFA and single-use recovery codes for privileged roles.
- Confidential source notes and verification evidence use authenticated AES-256-GCM encryption at rest; public article projections never select those fields.
- Newsroom workflow: draft → submitted → review/fact/legal gates → approval → schedule/publish, with revision requests, unpublish/retract/archive and mandatory explanations.
- Separation of duties: reporters cannot publish or self-approve; commercial/finance/system administrators cannot approve or publish editorial content.
- Audited story pitches, desk decisions, assignments, deadlines and editorial calendar.
- Controlled live-news lifecycle: event states, reporter draft updates, independent editor approval, pinning, correction/withdrawal notes, notifications and audit history.
- Hierarchical multilingual categories and scheduled homepage curation slots with chronological fallback.
- Typed contact/corrections intake with DB-backed anonymous throttling and a warning against submitting confidential identities.
- Newsletter double opt-in lifecycle with hashed expiring confirmation tokens, hashed unsubscribe tokens, suppression protection and portable SQLite/MySQL persistence. Actual mail delivery requires an approved dispatcher and authenticated sending domain.
- Server-enforced staged workflow: draft, submitted, editorial review, fact-check/legal review, approval, scheduling, publication, archive and retraction.
- Retained revisions, transition history, editorial checks, risk and right-of-reply metadata.
- Granular newsroom roles, public author/live/policy pages, read-only JSON API and OpenAPI documentation.
- Additive v1 migration, scheduled-publication CLI job and executable workflow/data-integrity tests.
- Staff suspension, multiple-role assignments, forced session revocation, role-aware access and audit history.
- Private media quarantine, mandatory rights metadata, independent approval, MIME/dimension checks and GD re-encoding; nothing becomes public before approval and production scanning can fail closed.
- Key-ID based confidential-data encryption rotation, confidential-source access logging, optimistic article locks and HMAC-chained audit events with a verifier CLI.
- Database-backed Newsroom, Audience, Advertising, Finance, first-party Analytics and System Administration modules. Campaign creation/approval and invoice/payment identity separation are enforced, while full CRM, ad delivery and external reconciliation remain incomplete.
- HTTPS-aware cookies, prepared SQL statements, CSRF protection, HTML escaping and login throttling.
- 12 **fictional DEMO editorial stories** and 12 original SVG concept illustrations for testing; removable from admin.
- `preview.html` and `admin-preview.html` — open offline without PHP or a database; these show visual design only, not functioning backend.

## What is NOT finished — important

**This is a staging candidate, not an independently verified production-ready news enterprise.** The local environment lacks `pdo_mysql`, PHP GD/Imagick, a malware scanner and a live MySQL server. Local SQLite setup and **70 automated policy/data-integrity tests pass**; public and authenticated route smoke tests pass. Real MySQL/MariaDB migration, positive upload, complete multi-role publishing/commercial E2E, accessibility/device testing and production operations still require isolated staging.

Not implemented or not independently verified: payment-provider processing and external statement reconciliation; real double-opt-in email delivery; full legal/privacy approval; malware scanning; video transcoding/storage; comments/moderation; live ad delivery/measurement; full CRM/quotes/contracts; completed multilingual translation assignment UI; anonymous secure-drop portal; push notifications; CDN/high-availability scaling; automated backup infrastructure; penetration testing; production monitoring and disaster recovery.

**Do not enter real confidential tips or live customer data until the privacy, retention, security and operational obligations have been completed.** Contact and newsletter forms store submitted information in the MySQL database.

## Install with cPanel (typical shared hosting)

1. Buy/confirm your domain and set up cPanel with valid HTTPS/TLS certificate.
2. In cPanel > MultiPHP Manager, choose PHP 8.1+; enable/check `pdo_mysql`, `mbstring`, `fileinfo`, `iconv`, `json`, `session`, `openssl` and `gd`. Secure media upload deliberately fails when GD is absent.
3. In cPanel > MySQL Databases, create a database and restricted user; give the user privileges for this database only. Store credentials privately.
4. Upload project files to `public_html/` or a secure subdirectory. On shared hosting, consider moving sensitive `app/`, `schema.sql` and configuration outside the web root and adjust references, even though Apache blocks them here.
5. Copy `config.example.php` to `config.local.php`; set database credentials, URL/path and a unique `AFRIVOX_APP_KEY` containing base64-encoded 32 random bytes. **Never post, rotate casually, or share this key:** losing it makes encrypted source notes unreadable.
6. Ensure the `/uploads/` directory is writable by PHP but not executable, and check `.htaccess` protections work in your hosting environment.
7. Visit `https://yourdomain.rw/install.php`. Create your own administrator using a unique password of 14+ characters. The installer builds SQL tables, sample categories and sample DEMO articles. **Do not use a shared/public admin password.**
8. **DELETE `install.php` immediately** after the installer returns success. Do not rely only on the lock file.
9. Sign in, complete mandatory MFA, then create separate editorial and operational accounts. A system administrator deliberately cannot publish. Verify two-person approval, suspension/session revocation and media rights gates.
10. Before public launch: remove all fictional DEMO stories in Admin > Articles > Remove demo; replace example brand, illustrations, publication identity, policy text and sample content.
11. Set `robots.txt` to your real sitemap URL; connect Google Search Console; validate RSS, structured data and mobile rendering.
12. Configure backups, disaster recovery, monitoring, WAF/rate limits, email validation, image metadata stripping and legal registration before collecting real users.

### Permissions and deployment notes

- `.htaccess` is Apache-specific. If you deploy to Nginx or a different web server, reimplement all denials and upload-execution restrictions.
- `config.local.php` must not be in the repository, backup archives that are shared, or publicly reachable.
- Image originals are stored outside the document root, public copies are re-encoded, and rights are recorded. Production still needs malware screening, resizing variants and an external storage/backup policy.
- Password hashing, separate password/MFA throttles, TOTP recovery codes, audited second-admin MFA reset, suspension and session revocation are implemented. Production still needs a reviewed password-reset delivery channel and remote append-only audit storage.
- Avoid using external image URLs as evidence for real news events. Demo SVG illustrations are explicitly not reportage.
- `sitemap.php` is the general public archive sitemap; `news-sitemap.php` is limited to eligible non-demo articles published in the preceding 48 hours. Add sitemap indexes/pagination before the archive outgrows one sitemap.
- Your host must permit CSP and security headers as configured; test YouTube privacy-enhanced embedding and external font loads before publishing.
- App timestamps are formatted in `Africa/Kigali`. Ensure the database and backup strategy use a documented timezone convention.

## Directory overview

```
/
├── index.php             Public homepage
├── article.php           Published article view
├── category.php          Topic archive
├── search.php            Published content search
├── media.php             Photo/video article archive
├── contact.php           Contact form / corrections intake
├── newsletter.php        Double-opt-in request (delivery provider still external)
├── verify-newsletter.php Confirmation endpoint
├── unsubscribe-newsletter.php  One-click suppression endpoint
├── about.php             Demonstration editorial policy
├── privacy.php           Incomplete privacy template
├── feed.php              RSS
├── sitemap.php           XML sitemap (MVP)
├── install.php           One-time DB/bootstrap installer (DELETE after setup)
├── schema.sql            Relational schema
├── config.example.php    Configuration template
├── config.local.php      YOU create; private; never share
├── app/bootstrap.php     Security, database, common templates, helpers
├── admin/login.php       Staff login
├── admin/index.php       Workflow, encrypted source editor, staff, audit
├── admin/media-library.php  Rights-aware media intake and approval
├── admin/newsroom-operations.php  Pitches, assignments and calendar
├── admin/live-operations.php  Live event and independent update approval
├── admin/categories.php  Taxonomy administration
├── admin/homepage-manager.php  Scheduled homepage curation
├── admin/modules.php     Audience, campaigns, invoices/payments, analytics, system
├── cli/                  Source encryption and scheduled publication commands
├── assets/               Styles, JS, SVG brand illustrations
├── uploads/              Approved media uploads (execution disabled)
├── docs/                 Research, roadmap, architecture and policies
└── preview.html          Offline visual preview ONLY
```

## Editorial workflow

```
Journalist (reporter) -> DRAFT -> SUBMIT FOR REVIEW
                                       |
                                       v
Independent editor checks facts, sources, photos, fairness, legal/privacy risk
                                       |
                         request revision / approve
                                       |
                                       v
                         PUBLIC ARTICLE + AUTHOR + DATE
                                       |
                         correction notice (when needed)
                                       |
                              AUDIT LOG ENTRY
```

The backend enforces a separate approver for approval and retains transitions/revisions. It does **not yet** provide court/legal hold or deletion-proof external event logs.

## Database

The normalized MySQL schema covers users/multiple roles, encrypted-source fields, revisions, workflow/checks, pitches/assignments, taxonomy, media rights, live reporting, audience consent, advertising, integer-minor-unit invoices/payments, first-party analytics, notifications, audit logs and settings. Existing installations take a verified backup, run `php cli/migration_preflight.php`, then apply the additive migrations in this order: `20261028_phase2.sql`, `20261028_p0_security_mms.sql`, `20261029_v3_security_gates.sql`, `20261029_v4_newsroom_operations.sql`, `20261029_v5_taxonomy.sql`, and `20261029_v5_audience.sql`. Rehearse the exact sequence and rollback on representative MySQL/MariaDB staging before production. Run `php cli/encrypt_legacy_sources.php --apply` only under the approved maintenance procedure.

## Verification

Run `php tools/setup_local_sqlite.php`, `php cli/migration_preflight.php`, `php tests/run.php` and `php cli/verify_audit_chain.php`, then syntax-check all PHP files and execute the HTTP/role matrix. The current local automated result is **70 passed, 0 failed**. SQLite is only a local preview; production remains MySQL/MariaDB. Start with `docs/ULTIMATE_IMPLEMENTATION_AUDIT.md`, `docs/ULTIMATE_TRACEABILITY.md`, `docs/IMPLEMENTATION_REPORT_ULTIMATE.md` and `docs/RELEASE_CHECKLIST_ULTIMATE.md` for exact status and launch blockers.

## Branding

The public edition is configured as **AFRIVOX NEWS — Africa Has a Voice.** Confirm company name, domain, trademark/logo rights, ownership and legal publication identity before production. New deployments use the `AFRIVOX_*` environment variables and AFRIVOX NEWS encryption context. The application can still read legacy environment variables and encrypted source-note envelopes during the controlled migration period; remove that compatibility only after every deployed secret and confidential record has been rotated and verified.

The homepage never invents news to fill the design. It reads published CMS records, uses scheduled homepage slots where configured, excludes the lead story from Featured News, and keeps test records visibly labelled `DEMO / URUGERO`.

## Commercial operations

Advertiser/campaign records, independent campaign approval, auditable invoice/payment ledgers, idempotency keys and invoice-creator/payment-recorder separation are included. There is no quote/contract repository, payment gateway, bank/mobile-money reconciliation import, live ad serving, targeting or certified impression billing; homepage ad content remains a clearly labelled placeholder.

## More documentation

- `docs/AFRIVOX_HOMEPAGE_IMPLEMENTATION.md` — homepage architecture, CMS selection rules, visual evidence and configuration dependencies.
- `docs/USER_MANUAL.md` — role-based instructions and permission boundaries.
- `docs/STAFF_TRAINING_GUIDE.md` — staged training, exercises and assessment evidence.
- `docs/DAILY_NEWSROOM_PROCEDURES.md` — opening, handover, breaking-news, correction and closing SOPs.
- `docs/TECHNICAL_ADMIN_GUIDE.md` — configuration, migration, verification, recovery and security operations.
- `docs/STRATEGY_AND_PRODUCT_SPEC.md` — full business, content, product and audience requirements.
- `docs/PRODUCTION_ROADMAP.md` — what remains before launch and scale.
- `docs/EDITORIAL_CHARTER.md` — editorial checks and newsroom governance.
- `docs/SECURITY_QA.md` — operational security and test matrix.
