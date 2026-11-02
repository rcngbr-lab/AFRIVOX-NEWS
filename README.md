# AFRIVOX NEWS

AFRIVOX NEWS is a full-stack PHP newsroom and digital publishing platform. The production application lives in [`AFRIVOX_NEWS/`](AFRIVOX_NEWS/); the top-level standalone HTML files and screenshots are retained design evidence, not the production application.

## Deployment status

- Source repository: `https://github.com/rcngbr-lab/AFRIVOX-NEWS`
- Production branch: `main`
- Runtime: PHP 8.1+, PDO MySQL, MySQL 8.0.29+ or MariaDB 10.6+, Apache/cPanel-compatible hosting and HTTPS
- CI: `.github/workflows/production-pipeline.yml`
- GitHub Pages: not supported for the full application because it cannot execute PHP, authentication, editorial workflows, uploads or database operations

Every pull request and every push to `main` runs PHP syntax checks, the automated policy/data-integrity suite, secret-hygiene checks and deployment-artifact validation. Production deployment stays disabled until an approved server, GitHub production environment and required secrets are configured. See [`GITHUB_DEPLOYMENT.md`](GITHUB_DEPLOYMENT.md).

## Local verification

```powershell
cd AFRIVOX_NEWS
C:\php85\php.exe tests\run.php
C:\php85\php.exe cli\migration_preflight.php
```

Local secrets, runtime databases, uploads and release archives are ignored by Git. Never add `config.local.php`, `.env` files, SQLite databases or private media to source control.

