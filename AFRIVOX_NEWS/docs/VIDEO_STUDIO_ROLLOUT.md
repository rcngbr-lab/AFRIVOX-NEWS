# AFRIVOX NEWS — Video Studio rollout

## Scope implemented
- A native, database-backed video catalogue with metadata, category, language, rights owner, licence, optional expiry and optional article association.
- Strict HTTPS YouTube link parsing (watch, Shorts, live, youtu.be); video IDs alone are stored. Original video files stay on YouTube.
- Draft → pending → independently approved → published workflow. Reporter/producer cannot approve or publish own clip. Publishing requires existing \`workflow.publish\` permission.
- Public \`media.php\` video grid with click-to-play privacy-enhanced YouTube embeds; requests for embedded players begin only after user clicks. Existing published photo stories remain.
- The public catalogue automatically excludes unpublished items and expired rights. Editorial status updates and video changes are audit logged.
- Video Studio is available at \`admin/videos.php\` and linked from the main newsroom navigation.

## Activate on a PHP server (NOT on GitHub Pages)
1. Back up the production database and application files.
2. Deploy code from the reviewed feature branch to a PHP 8.1+ environment with MySQL/MariaDB.
3. On an existing installation run \`migrations/20261105_v7_video_library.sql\` once with a privileged database client. On a new installation the video table is included in \`schema.sql\`.
4. Confirm the video roles: \`video_producer\` or \`reporter\` for upload; \`multimedia_editor\` or managing editor for rights approval; managing/editorial publisher with \`workflow.publish\` to publish.
5. Sign in to \`admin/videos.php\`. Register a real, rights-cleared, publicly accessible YouTube video. Use one account to submit and a different authorized account to approve. An editor can publish once approved.
6. Open \`media.php?lang=en\` to verify thumbnail, player, correct category filter, localization, and related article visibility. Test desktop/mobile, content security policy and restricted/private YouTube videos.
7. If using the GitHub Pages demonstration, regenerate \`public-pages/\` by the existing static export process after the PHP database has published videos. GitHub Pages **cannot** run PHP, accept uploads, provide authentication or accept editorial updates. A PHP deployment is required for the real CMS workflow.

## Remaining production dependencies
- Video files are NOT uploaded, stored or transcoded on the server. Do not advertise native MP4 upload or adaptive streaming as implemented. YouTube channel ownership, online availability, captioning and playback restrictions require verification.
- The editor must verify source footage and written reuse rights; the workflow only records rights metadata. The system does not independently prove licensing or scan remote videos.
- A robust in-house video host would require object storage, a secure upload pipeline, background transcoding, multi-bitrate HLS/DASH, CDN, storage quotas and abuse monitoring. This is intentionally not included.
- New \`video_clips\` records are separate from the legacy \`articles.video_url\` links, which retain their existing article embed behavior.
- Run automated PHP tests via \`php AFRIVOX_NEWS/tests/run.php\` in a configured development runtime; manually exercise database migrations and state transitions. Feature development does not itself establish live production success.

## Acceptance criteria
1. Malicious/malformed provider URLs fail validation.
2. Empty licensing fields prevent save; expired rights prevent approval or appearance.
3. The creator cannot approve or publish their own video.
4. A draft/pending/rejected clip never appears on the public page.
5. Published clips appear with clickable thumbnails and actual playable content, provided the original video permits embedding.
6. Public pages load no YouTube iframe until clicked.
7. An unlinked video works; links to unpublished or demo-only articles never display as public article links.
