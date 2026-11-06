# AFRIVOX NEWS Existing-System Baseline — 6 November 2026

## Scope and preservation decision

This audit treats the tracked PHP/MySQL application in `AFRIVOX_NEWS/` as the only production application. The `public-pages/` directory remains an isolated static GitHub Pages preview. No parallel application, replacement schema, route rewrite, content reset or branding migration is authorized by this phase.

## Verified existing implementation

| Component | Existing implementation | Baseline evidence | Decision |
| --- | --- | --- | --- |
| Frontend | Bilingual responsive public templates, headline carousel, CMS-backed homepage, archives, article pages, advertising, newsletter and footer | Public homepage, article, category, search and media routes returned HTTP 200 | Preserve and incrementally optimize |
| Backend | PHP services for editorial workflow, media, advertising, newsletter, password reset, forms and security | 55 PHP files passed syntax validation | Preserve service boundaries |
| Database | MySQL/MariaDB production schema plus additive migrations; SQLite is local verification only | Migration preflight returned `READY`; no plaintext source-note findings | No schema change in this phase |
| CMS | Draft-to-publication workflow, revisions, specialist checks, media rights, taxonomy, homepage slots, live operations and advertising approvals | 74 policy/data-integrity tests passed | Preserve permissions and separation of duties |
| Security | MFA, password throttling/reset, CSRF, prepared statements, session revocation, AES-GCM source notes and HMAC audit chain | Audit chain verified 29 signed events; tracked-secret hygiene passed | Preserve fail-closed controls |
| Deployment | Static Pages pipeline plus verified PHP artifact pipeline and gated SSH production deployment | Both workflows exist; production deployment remains safety-gated | Do not bypass deployment gate |

## Actual gaps found

1. The bundled Kigali hero PNG is 1,672×941 and 2,873,963 bytes. A visually equivalent WebP derivative at quality 85 is approximately 323 KB, an 88% transfer reduction. The CMS source reference and original PNG must remain unchanged.
2. Article records already support `type=video` and privacy-enhanced YouTube playback, but the homepage has no dedicated CMS-driven featured-video presentation. The local dataset currently contains no published video article; therefore the module must not fabricate content and must remain absent until an authorized video article is published.
3. External production prerequisites remain unresolved: live MySQL/MariaDB rehearsal, malware scanner, approved email dispatchers, remote immutable audit export, monitoring/backups, penetration testing and legal/privacy approval. These are operational blockers, not features to simulate in code.

## Baseline test result

- Automated policy/data-integrity suite: **74 passed, 0 failed**.
- PHP syntax: **55 files passed**.
- JavaScript syntax: **passed**.
- Migration preflight: **READY**.
- Audit chain: **29 signed events verified**.
- HTTP smoke routes: homepage, article, category, search, media, login and health all returned **200**.
- Tracked secret hygiene: **passed**.

## Increment authorized by this evidence

- Add an optional WebP derivative lookup for bundled editorial artwork while retaining the original CMS path as fallback.
- Add a responsive featured-video section that reads only published CMS records with `type=video` and links to the existing article/media routes.
- Keep the current AFRIVOX NEWS logo, navigation, data sources, URLs, permissions, workflows and database schema unchanged.
