# RBAC matrix

Permissions are version-controlled in `app/EditorialWorkflow.php`. Database `user_roles` contains global, active, expiring role assignments; it does not redefine permissions. Every request revalidates the user and session version. System administration cannot be combined with editorial/commercial authority.

| Role | Principal capabilities | Explicit prohibitions |
|---|---|---|
| System Administrator | Accounts, session revocation, security/configuration, backups, audit viewing | No article content, source access, approval or publication |
| Publisher / Executive | Management/commercial reporting, independent campaign approval, finance view/reconciliation authority | No editorial approval/publication; cannot approve a campaign they created |
| Editor-in-Chief / Managing Editor | Editorial workflow, pitch/assignment management, live management/approval, taxonomy/homepage curation, approval/publication and confidential-source leadership access | Cannot impersonate fact, legal or media-rights specialist; cannot approve own story/live update |
| Section Editor / Editor | Edit/review/approve/publish; pitch/assignment and live management; source/fairness/right-of-reply/accessibility checks | No fact/legal/media-rights specialist checks; no own-story/live-update approval |
| Reporter / Investigative Reporter | Create/edit/submit own stories, submit pitches, work assignments, draft live updates, manage own confidential source data and upload media | No article or live-update approval/publication |
| Fact-Checker | Fact-check state and fact-check outcome; confidential-source view | No legal approval or publication |
| Legal Reviewer | Legal outcome and legal workflow; confidential-source access | No fact-check or publication |
| Copy Editor | Copy check and editorial editing | No fact/legal/media-rights approval or publication |
| Multimedia Editor | Media management, rights check and independent approval | Cannot approve own upload; no article publication |
| Photographer / Video Producer | Upload/edit own media | No media approval or article publication |
| SEO & Audience Editor | SEO check, audience/newsletter/analytics | No editorial approval/publication |
| Social Media Manager | Publish approved public social material | No newsroom approval |
| Advertising Manager | Advertisers, campaign drafts/submission/status and commercial reports | No campaign approval and no editorial workflow |
| Finance Officer | Invoice creation, payment recording, finance view and commercial reports | Cannot record payment on an invoice they created; no editorial workflow or independent reconciliation approval |
| Subscriber Support | Audience/subscriber/inbox operations | No editorial or finance approval |
| External Contributor | Create/edit/submit own work | No confidential work of others, approval or publication |

Specialist check mapping: `fact_check → checks.fact_check`, `legal → checks.legal`, `media_rights → checks.media_rights`, `copy → checks.copy`, `seo → checks.seo`, and explicit `sources`, `fairness`, `right_of_reply`, `accessibility` permissions. Generic `workflow.review` never satisfies a specialist check.
