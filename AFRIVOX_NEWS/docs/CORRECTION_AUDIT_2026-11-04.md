# AFRIVOX NEWS correction audit — 2026-11-04

## Scope and result

This audit covers the static GitHub Pages preview and the production PHP application corrections requested after the homepage integration audit. Source-level, local automated and GitHub Pages verification pass for implementation commit `c59a05a`. Production server deployment remains intentionally disabled by the repository safety gate and is not implied by a green source build.

## Corrected controls

- Static navigation no longer uses `href="#"`, fake form actions or PHP-only destinations. Article, search, newsletter, language, media and policy links resolve to `preview-info.html`, which states exactly what requires the production backend.
- Static search and newsletter controls do not claim success or collect data. The static page is marked `noindex,nofollow`, and RSS/hreflang signals that cannot be fulfilled by the static preview are omitted.
- Static 404 is an accessible, branded page with no automatic redirect.
- Production search uses prepared statements, literalized wildcard input, 12-result pagination, bounded pages and query length, language filtering and published-only filtering.
- Public article, category, related-story, RSS and navigation links preserve `lang=rw|en`. Missing translations use an explicit fallback notice rather than fabricated translation.
- Public publication queries require a non-null timestamp that is not in the future. Production mode also excludes `is_demo=1` content.
- Article and policy misses return HTTP 404. General/news sitemaps exclude demo, unpublished and future-dated articles.
- Canonical query parameters are allowlisted. Production pages emit rw/en/x-default hreflang alternatives; the static preview intentionally does not.
- `health.php` returns minimal JSON and HTTP 200 only after a database probe; errors return 503 without secret detail.
- The production workflow keeps the deployment gate intact and reports verification separately from deployment success, failure or intentional skip.
- Unconfigured social profiles remain visibly disabled and no official URL is invented.

## Verification evidence

- PHP 8.5.5 syntax: 51 files checked, 0 failures.
- Automated policy/data tests: 70 passed, 0 failed.
- Migration preflight: `READY`; encryption and audit key checks passed; plaintext/legacy confidential-source counters were zero.
- Signed audit chain: verified.
- Correction render probes: matching search, wildcard-only search, published article, hidden under-review article and missing policy passed.
- Production-mode data probe: `published_where()` contained status, publication time and `is_demo=0` gates; the local demo-only public count and search count were both zero.
- Static export scan: zero placeholder href/actions, fake submit handlers, local-host URLs, PHP destinations and static hreflang links.
- GitHub Pages workflow run `41787215125` completed successfully, including artifact validation and Pages deployment.
- Production pipeline run `41787215540` completed successfully: verify/package and status-report jobs passed; the SSH production deployment job was intentionally `skipped` by the unchanged safety gate.
- Live HTTPS returned 200 for the homepage, capability page, stylesheet and JavaScript, and 404 with the branded non-redirect page for an unknown path.
- Live Edge device-metrics checks at 375×812, 768×1024, 1024×900 and 1440×1024 reported `scrollWidth == innerWidth` and no page runtime/console errors. Mobile/tablet menu interaction changed `aria-expanded` and navigation state from closed → open → closed.
- Browser screenshots at all four viewports preserve the approved hierarchy; the 1440-pixel layout tracks the supplied homepage mockup and the 375-pixel page has no horizontal overflow.

## Remaining external or operational evidence

- A real MySQL 8/MariaDB migration, concurrency and rollback rehearsal remains `BLOCKED_EXTERNAL`; SQLite success does not authorize production.
- Newsletter token lifecycle passes locally, but authenticated mail delivery, domain reputation, bounce/complaint processing and inbox receipt remain `NOT TESTED` until a provider is configured.
- The PHP production deploy is `NOT EXECUTED` while `PRODUCTION_DEPLOY_ENABLED` is not exactly `true` and approved SSH/environment secrets are absent.
- Independent penetration, accessibility/assistive-technology, load, backup/restore, WAF/monitoring and remote immutable-audit evidence remains external.
- Legal identity, policy ownership, retention, licensing and Rwanda regulatory applicability require accountable human approval.

## Release classification

The corrected repository is a staging candidate. GitHub Pages may be released as a clearly labelled static design preview. Production PHP remains `NO-GO` until the external gates above are evidenced and the protected deployment gate is deliberately approved.
