# SECURITY, QUALITY AND DEPLOYMENT TEST PLAN

## Tests actually performed for this package

- PHP syntax lint on all `.php` files: **passed** on PHP 8.4 CLI.
- Syntax-only test does **not** validate database SQL, session behavior, production hosting or media operations.
- Offline static design preview: included; website dynamic functions require real MySQL installation.

## Mandatory tests on the target host

### Database and installation
- [ ] Verify PDO MySQL connection and charset utf8mb4.
- [ ] Installer creates all tables; admin password is hashed.
- [ ] Installer blocks reruns and `install.php` is deleted.
- [ ] Sensitive configuration cannot be fetched as URL; `/app/` and `/docs/` denied.
- [ ] MySQL least privilege; encrypted backups; restore rehearsal.

### Authorization and content
- [ ] Reporter can save draft/submit review but not publish.
- [ ] Reporter cannot change another user's story.
- [ ] Reporter cannot edit an already published story.
- [ ] Editor can publish and add a correction.
- [ ] Administrator can create staff; non-admin cannot.
- [ ] HTML and script pasted into article display as escaped text, not executable code.
- [ ] CSRF rejection and login throttling tested.
- [ ] Password, session and account management reviewed; add MFA and session revocation.

### Files and legal
- [ ] No executable uploads even through server misconfiguration; isolate upload storage from PHP execution.
- [ ] Invalid MIME / oversize images rejected; malformed/polyglot image processing screened.
- [ ] Strip all sensitive EXIF; confirm permission, credit, consent and caption; resize and serve responsive assets.
- [ ] All demonstration articles removed and no invented news published.
- [ ] Photo/video licenses and third-party embedding terms recorded.

### Product
- [ ] Homepage cards and category/search/article links operate.
- [ ] Article structured data valid; canonical URLs include unique slug.
- [ ] RSS and sitemap are valid XML; pagination handles large sites.
- [ ] Contact/newsletter have proper throttling, anti-spam and retention processes; pending subscribers cannot receive mail.
- [ ] Accessibility audit: keyboard, contrast, forms, focus, image alt text, screen reader headings.
- [ ] Mobile devices: low-end Android, Chrome, iOS Safari, landscape/portrait, 320-1440px screens.
- [ ] Performance on 3G/4G; server caching/CDN readiness; image sizes and LCP/CLS/INP.
- [ ] Monitoring, uptime alerts, log retention, backup and disaster recovery.

## Security gaps in MVP that are NOT negotiable before large-scale use

- Immutable signed revision history and real dual control for sensitive publications.
- MFA, safe password resets, user suspension/session revocation.
- Production image re-encoding and malware/metadata handling.
- Deployment-based request rate limits / anti-bot protection for public forms; current public forms use basic honeypot only.
- Newsroom legal counsel/privacy processes and source security.
- Fully compliant privacy notices, data deletion requests and email confirmations.
- Dedicated secrets management, logging, security scanning and automated regression suite.
