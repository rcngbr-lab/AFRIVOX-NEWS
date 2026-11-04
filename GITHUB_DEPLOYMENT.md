# AFRIVOX NEWS — GitHub and production deployment

## Architecture decision

AFRIVOX NEWS is not a static GitHub Pages application. It requires PHP execution, database persistence, authenticated admin routes, editorial workflow enforcement, file uploads and private media handling. GitHub is the source and CI/CD control plane; production must be an approved PHP/MySQL host. The current application documentation selects an Apache/cPanel-compatible PHP host, so the pipeline deploys over SSH only after explicit production configuration.

GitHub Pages publishes only the isolated `public-pages/` design preview at `https://rcngbr-lab.github.io/AFRIVOX-NEWS/`. That preview is not the production newsroom, does not expose PHP source or credentials, and must not be treated as evidence that admin, database, publishing or upload workflows are live.

## Pipeline behavior

`.github/workflows/production-pipeline.yml` runs on pull requests, pushes to `main` and manual dispatches. It:

1. Checks out the exact commit.
2. Configures PHP 8.3 with the required test extensions.
3. Lints every PHP source file.
4. Runs `AFRIVOX_NEWS/tests/run.php`.
5. Fails if local configuration, environment files, SQLite data or other runtime secrets are tracked.
6. Builds and validates an artifact that excludes private configuration, databases, runtime uploads, logs and installation state.
7. Uploads the verified artifact for 14 days.
8. Deploys only from `main`, only after verification, and only when the repository variable `PRODUCTION_DEPLOY_ENABLED` is exactly `true`.
9. Requires a successful HTTPS health check after deployment.
10. Always publishes a workflow summary that distinguishes verification success/failure from deployment success/failure/skipped status.

The deployment job uses minimal repository permissions (`contents: read`), strict host-key checking and the protected GitHub `production` environment. It preserves server-side `config.local.php`, `storage/`, `uploads/` and `app/installed.lock` while synchronizing application source.

## One-time GitHub configuration

In the repository settings:

1. Create an environment named `production` and add required reviewers if the GitHub plan supports them.
2. Add these **environment secrets**:
   - `PRODUCTION_SSH_HOST`
   - `PRODUCTION_SSH_USER`
   - `PRODUCTION_SSH_PRIVATE_KEY` — a dedicated, restricted deployment key
   - `PRODUCTION_SSH_KNOWN_HOSTS` — the reviewed host-key line, obtained out of band
   - `PRODUCTION_DEPLOY_PATH` — the application document root on the server
   - `PRODUCTION_HEALTHCHECK_URL` — the canonical HTTPS `health.php` URL, for example `https://news.example/health.php`; it returns HTTP 200 only when the application configuration and database probe succeed
3. On the server, create `config.local.php` privately and configure MySQL credentials, production URLs, key identifiers and dispatchers. Never store this file in GitHub.
4. Configure `AFRIVOX_APP_KEY` and `AFRIVOX_AUDIT_KEY` in the host secret manager or protected server environment.
5. Confirm the deploy user can write application code but cannot read unrelated accounts or system secrets.
6. Complete the MySQL migration/rollback rehearsal, malware-scanner integration, mail-provider setup, backups and restore drill listed in `AFRIVOX_NEWS/docs/RELEASE_CHECKLIST_ULTIMATE.md`.
7. Set the repository variable `PRODUCTION_DEPLOY_ENABLED` to `true` only after the production readiness review passes.

Until step 7 is complete, pushes still run CI and produce a verified artifact, but the live deployment job is intentionally skipped.

## Everyday workflow

Run these commands from the repository root, never from `C:\WINDOWS\System32`:

```powershell
cd "C:\Users\IT BASE\AFRIVOX_NEWS"
git pull --ff-only origin main
git status
git add -A
git diff --cached
git commit -m "Describe the AFRIVOX NEWS change"
git push origin main
```

Open the repository's **Actions** tab and verify that `AFRIVOX NEWS production pipeline` completes. A local edit does not update production until it is committed, pushed, verified and deployed.

## Synchronization conflicts

Before starting work, use `git pull --ff-only origin main`. If Git refuses because local and remote work diverged:

1. Do not force-push and do not reset hard.
2. Commit or safely stash the local work.
3. Fetch with `git fetch origin`.
4. Inspect `git log --oneline --left-right main...origin/main` and resolve the specific files with their owners.
5. Re-run the test suite before pushing.

## Rollback

1. Identify the last known-good commit in GitHub Actions.
2. Create a new revert commit: `git revert <bad-commit-sha>`.
3. Push the revert to `main`.
4. The same pipeline verifies and deploys the reverted source.
5. If the application is unavailable, disable `PRODUCTION_DEPLOY_ENABLED`, restore the last server backup through the approved operations procedure, and verify the database separately before re-enabling deployment.

Never roll back the database by replacing files or guessing migration state. Follow the documented database backup and rollback runbook.

