# AFRIVOX NEWS video library and publishing workflow

Status: implemented application workflow; production migration and provider/legal acceptance still require staging approval.

## Purpose and boundary

The Video Library extends the existing PHP/PDO newsroom. It is not a parallel CMS. It registers externally hosted YouTube editorial video, records rights and accessibility evidence, requires independent review, attaches one approved primary video to an article and exposes only an eligible record on the public article page.

The service deliberately does **not** claim to upload, scan, transcode, stream or back up a YouTube binary. Self-hosted video needs a reviewed object-storage, malware-scanning, transcoding, thumbnail, caption and CDN pipeline before it can be added safely.

## Roles and separation of duties

| Action | Required permission | Typical role | Enforced boundary |
| --- | --- | --- | --- |
| Create/edit/submit video | `media.upload` | Video producer, multimedia editor | Creator edits only their own draft/rejected record unless they have `media.edit_all` |
| Approve/reject/archive | `media.approve` | Multimedia editor, editorial leadership | Creator cannot approve or reject the same video |
| Attach to article | Article edit authority plus `media.use`, `media.edit_all` or `article.*` | Reporter/editor within article scope | Only approved, unexpired video is attachable |
| Public playback | No staff permission | Reader | Article must already be published; attached video must remain approved and within rights window |

All create, update, submit, approve, reject, archive, attach and detach actions produce an audit event.

## Workflow

```text
Draft → Submitted → Approved → attached to article → public playback
                  ↘ Rejected → corrected draft → Submitted
Approved/Rejected → Archived (record retained; playback disabled)
```

1. Open **Newsroom → Video Library**.
2. Register an HTTPS YouTube watch, short, embed or `youtu.be` URL.
3. Complete title, accessible description, language, duration, captions/transcript, rights holder, licence/contract reference, optional rights expiry and usage restrictions.
4. Save a draft and submit it.
5. A different user with `media.approve` verifies the provider reference, rights, captions/transcript, description and restrictions, then approves or rejects it with a reason.
6. Open an existing article in the CMS. In **Managed story video**, select an approved record and attach it.
7. Complete the article’s normal media-rights editorial check and article approval/publishing workflow.

## Publication rules

- Only HTTPS YouTube hosts and 11-character provider identifiers are accepted and normalized.
- Public embeds use `https://www.youtube-nocookie.com/embed/{id}` under the existing CSP.
- A managed attachment takes precedence over legacy `articles.video_url`.
- If a managed record is rejected, archived or rights-expired, it is not rendered; the system does not fall back to its copied legacy URL.
- `workflow_context()` counts the attached video as media. Expired or non-approved video blocks article approval when media is present, and the specialist media-rights check remains required by the existing editorial gates.
- The compatibility URL is synchronized on attachment so existing homepage/video-article selection continues to work. Detaching clears it only when it still equals the managed source; unrelated legacy URLs are preserved.
- Records are archived, not deleted, so review and audit history remain available.

## Database and migration

- Fresh databases receive `video_assets` and `article_videos` from `schema.sql`.
- Existing databases apply `migrations/20261106_v7_video_library.sql` only after all earlier migrations through V6.
- `video_assets` stores provider identity, metadata, accessibility evidence, rights, review state, reviewer and optimistic lock version.
- `article_videos` stores one primary managed video per article and preserves the attaching staff identity.
- The change is additive. It does not remove or rewrite `articles.video_url`.

Before migration: take a verified backup, rehearse restore on representative staging, quiesce writes as required and run `php cli/migration_preflight.php`. After migration: verify both tables and indexes, run the automated suite, lint all PHP, exercise two-user approval, attach to a non-production article and verify public playback/expiry/archive behavior.

Rollback is release restoration plus the rehearsed pre-migration database backup. Do not drop the new tables ad hoc after staff have created records; that would destroy editorial and audit relationships.

## Operational checks

- Daily: submitted queue age, rejected items awaiting correction, rights expiring soon, archived/takedown reasons and unexpected public embed failures.
- Before publication: provider video is available, captions are correct, transcript/accessibility description is accurate, licence covers the intended territories/channels/dates, and any people/minors/privacy restrictions are resolved.
- Incident/takedown: an authorized reviewer archives the record with a clear reason. Confirm the public article no longer embeds it and preserve audit/log evidence.
- Provider deletion, geo-blocking and policy changes remain external dependencies and need monitoring.
