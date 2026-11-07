# AFRIVOX NEWS backend integration audit — 7 November 2026

## Outcome

The existing PHP/PDO newsroom was preserved. It already provided authentication/MFA, RBAC, editorial state transitions, specialist checks, revisions, confidential-source encryption, image rights approval, advertising, audience records, finance ledgers, analytics and audit chaining. No parallel application was created.

The verified missing backend capability was a first-class editorial video registry. Existing `articles.video_url` supported a direct YouTube link but did not provide a video-specific rights/accessibility record, independent review, controlled article attachment or takedown lifecycle.

## Implemented increment

- `VideoService` validates and normalizes allow-listed HTTPS YouTube references.
- `video_assets` stores metadata, language/duration, transcript or confirmed captions, accessibility description, rights holder, licence reference, expiry/restrictions, reviewer, status and optimistic lock version.
- `article_videos` records one managed primary video per article, its attaching user and time.
- Workflow: draft → submitted → independent approve/reject; approved/rejected records may be archived with a retained reason.
- Creator self-approval and self-rejection are blocked.
- Only approved, unexpired video can attach to an article.
- Existing article edit permissions and `media.use`/`media.edit_all` boundaries are reused.
- Attached video participates in the existing article media-rights approval context.
- Public article playback uses `youtube-nocookie.com`; rejected, archived or expired managed video is not rendered.
- Legacy `articles.video_url` remains supported and is synchronized only for compatibility. Existing URLs/data were not removed.
- Every video workflow/attachment action uses the existing signed audit-log path.
- Admin navigation, Image Library cross-link, CMS article editor and public article template were integrated.
- Fresh schema, local SQLite setup and additive V7 production migration were updated.

## Verification evidence

| Component | Existing implementation | Changes made | Verified result |
| --- | --- | --- | --- |
| Frontend | Server-rendered CMS article page with legacy privacy-enhanced YouTube embed | Managed approved video takes precedence; inaccessible/expired/archived video is suppressed; legacy fallback retained | PASS — homepage and representative article HTTP 200; 30-resource static export completed |
| Backend | PHP/PDO services, CSRF, sessions, RBAC, editorial workflow and audit logging | Added `VideoService`, Video Library admin, CMS attachment and audited archive/takedown | PASS — 83 automated tests; authenticated admin dashboard and Video Library HTTP 200 |
| Database | Normalized MySQL schema plus SQLite developer approximation | Added additive `video_assets`/`article_videos`, indexes and V7 migration marker | PASS on SQLite — both tables, two indexes and migration marker verified |
| CMS | Article create/edit, revisions, media library and specialist checks | Added approved-video selection while preserving legacy URL and optimistic article locking | PASS — permission/service tests and authenticated page smoke check |
| Security | Prepared statements, CSRF, escaping, MFA/RBAC, source encryption and signed audits | HTTPS/provider allow-list, no arbitrary iframe URL, separation of duties, captions/transcript and rights gates, expiry/takedown fail-closed | PASS — preflight READY; 29 signed audit events verified; 57 PHP files linted |

## Test detail

- Automated suite: **83 passed, 0 failed**.
- PHP syntax: **57 files passed**.
- Migration preflight: **READY**; no plaintext legacy source notes or unknown contact states.
- Audit verifier: **29 signed events verified**.
- Public routes: homepage, article, login and export routes responded as expected.
- Anonymous Video Library access redirects to login; an authenticated local editorial session received HTTP 200 for dashboard and Video Library.
- `git diff --check`: passed.

## Explicit production gates

- This workstation has `pdo_sqlite` but no `pdo_mysql`, MySQL/MariaDB server or client. Therefore `20261106_v7_video_library.sql` still requires backup/restore rehearsal and execution against the exact production-compatible MySQL/MariaDB release in isolated staging.
- YouTube availability, geo-restrictions, provider policy, licence territory and caption quality remain operational/editorial checks.
- Self-hosted video upload/transcoding is not implemented. It must not be enabled until object storage, MIME/signature inspection, malware scanning, transcoding, poster generation, caption handling, CDN delivery, quotas, retention and backup are reviewed and tested.
- No GitHub or production deployment was performed in this phase. Source changes remain local pending review and authorization.
