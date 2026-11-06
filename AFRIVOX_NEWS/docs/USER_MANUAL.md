# AFRIVOX NEWS role-based user manual

**Applies to:** Ultimate phased staging candidate, 30 October 2026  
**Release state:** staging use only; production is `NO-GO` until the release checklist is accepted.  
**Never use this manual as authority to bypass a permission, specialist gate or second-person approval.**

## 1. Before you sign in

1. Use only the account issued to you. Never share an account, password, MFA secret or recovery code.
2. Confirm you are using the approved HTTPS staging/production URL—not an emailed look-alike link.
3. Use a managed device, current browser and trusted network. Report a lost device or suspected phishing immediately.
4. Do not enter real confidential-source identities, production audience data or live financial data while the platform remains a staging candidate.
5. If your role is wrong or an action is unavailable, stop and ask an authorized administrator/editor. Do not borrow another role.

There is no default operational password. Initial credentials must be delivered through an approved private channel and changed immediately when required. Privileged staff must complete MFA enrollment when the system prompts them.

## 2. Account and session controls

- Sign in at `admin/login.php`.
- Complete MFA or forced password change before using protected pages.
- Use **Log out** when leaving a workstation. Idle and absolute timeouts may end the session automatically.
- A suspension, role change, password reset or administrator session revocation invalidates existing sessions.
- Lost MFA access is an incident. The audited reset procedure requires a different authorized administrator, a reason and current-password reauthentication.

## 3. Role boundaries at a glance

| Role group | Normal work | Must not do |
|---|---|---|
| Reporter / investigative reporter / contributor | Pitch, accept assignment, draft own story, protect own source material, upload media, draft live update | Approve or publish own story/update; approve own media |
| Editor / section editor / managing editor / Editor-in-Chief | Decide pitches, assign work, review/edit, request revision, approve/publish when gates pass, manage live coverage | Complete fact/legal/media-rights check without that specialist permission; self-approve |
| Fact-checker / legal reviewer / copy editor | Perform the exact specialist check authorized to the role | Substitute for another specialist or publish |
| Photographer / video producer / multimedia editor | Upload and describe media; multimedia editor independently approves eligible assets | Uploader approving the same asset; using unlicensed/expired assets |
| SEO & audience / subscriber support | SEO and audience checks, subscriber/contact operations and permitted analytics | Editorial approval, finance approval or exposing reader PII |
| Advertising manager | Advertiser and campaign drafts, submission and permitted campaign status work | Approve own campaign or influence independent editorial conclusions |
| Publisher / executive | Independent campaign approval, management/commercial reporting and permitted finance oversight | Editorial approval/publication; approving a campaign they created |
| Finance officer | Issue invoices, record settlement evidence and review allowed finance data | Record payment on an invoice they created; represent the ledger as bank-reconciled without external evidence |
| System administrator | Accounts, roles, sessions, security/configuration and audit operations | Read confidential sources or approve/publish editorial content |

The authoritative permissions are in `app/EditorialWorkflow.php` and summarized in `RBAC_MATRIX.md`.

## 4. Reporter workflow

### Submit and work a pitch

1. Open **Pitches & Assignments** → **Story Pitches**.
2. Enter a working title, description, proposed angle, source plan, urgency, risk, location, expected whole RWF amount and proposed deadline.
3. Do **not** put source identities or secret contact details in the pitch. Use protected article source fields later.
4. Submit. The pitch starts as `submitted`.
5. If the desk requests clarification or places it on hold, edit the description/angle and resubmit.
6. After acceptance and assignment, open **Assignments**, select **Start Work**, and later **Mark Complete** when the reporting task—not necessarily publication—is complete.

### Draft an article

1. Open **New Article**.
2. Complete headline, summary, body, category, type, language and risk classification.
3. Put reader-visible references only in **Public Sources**.
4. Put protected source notes and verification evidence only in the encrypted fields visible to your role. Never copy them into headline, body, revision note, pitch, comments or public sources.
5. If uploading media, provide title, caption, alt text, creator/source, licence, copyright holder, restrictions and expiry. The upload remains private and cannot publish until independently approved.
6. Save, review the result, then submit for review. Saving is not publication.

### Respond to revision

Read the recorded explanation, change only what is required, add a useful revision note and resubmit. Do not delete evidence simply to make a gate pass.

## 5. Desk and editor workflow

### Decide and assign a pitch

1. Open **Pitches & Assignments**.
2. Review public-interest value, duplication, feasibility, risk, source plan, cost and deadline.
3. Record **Accept**, **Reject**, **Request clarification**, **Hold** or **Merge** with a reason of at least ten characters.
4. For an accepted pitch, choose an active reporting-role assignee, priority, future due time and actionable briefing. Link an existing article only when correct.
5. Use the calendar view for deadlines and scheduled stories. Record handover outside the system as required by the daily procedure.

### Review and publish an article

1. Open **Articles** and select the submitted item.
2. Confirm author, ownership, category/type/language, risk, public sources, source classification, media rights and right-of-reply state.
3. Begin review and route required work to fact-check or legal review. Generic editor permission cannot record those specialist outcomes.
4. Request revision with a clear explanation when needed.
5. Approval must be by someone other than the author. Failed checks, plaintext protected data, missing source classification, pending/expired media or missing high-risk evidence block approval.
6. Schedule only with a future time or publish after approval. Critical-risk publication requires an actor different from both author and approver.
7. Verify the public article’s title, byline, dates, category, sources, media caption/credit, language and correction notice.

### Curate taxonomy and homepage

- **Categories & Topics:** create/edit active language-specific categories and parent relationships. A category with articles cannot be deactivated until content is reassigned.
- **Homepage Manager:** place only published stories in approved slots. Optional start/end times control visibility; empty/expired slots fall back to chronological content. Visual, investigation and opinion slots validate eligible article types.
- **Featured Videos:** the public homepage automatically shows up to four approved, published articles whose content type is `Video`. The section stays hidden when no eligible video exists; staff must never create placeholder reporting merely to fill the layout. Video playback continues to use the article's reviewed privacy-enhanced YouTube URL.

## 6. Specialist checks

1. Open the assigned article and read the evidence relevant to your permission.
2. Record `passed`, `failed`, `pending` or `not_applicable` honestly and add a useful evidence note.
3. A failed check is a publication blocker. Escalate uncertainty; do not mark `passed` to move the queue.
4. Fact-check, legal, copy, media-rights, SEO, source, fairness, right-of-reply and accessibility checks are distinct.

## 7. Live coverage

### Event editor

1. Open **Live Operations** and create an event draft with title, summary and optional slug.
2. Move `draft` → `live` only after staffing, verification and escalation coverage are ready.
3. Review draft updates. An update author cannot approve the same update.
4. Approve and publish verified updates, pin/unpin important items, or add a correction/withdrawal reason.
5. Corrections retain the original body and add a public note. Close the event when rolling coverage ends, then archive it according to policy.

### Reporter

Open the active event, write a concise attributed update and save it as draft. It is not public until another authorized editor approves it.

## 8. Media library

1. Upload only authorized JPG/PNG/WebP material within configured limits.
2. Complete rights, creator, source, copyright, restriction, expiry, caption and alt-text fields.
3. The original stays quarantined. A different multimedia approver reviews rights and safety.
4. Production approval must fail closed if the required malware scanner or image processor is unavailable.
5. Never work around quarantine by placing files directly in the public upload directory.

## 9. Audience, newsletter and reader contact

- Contact messages are typed as general, correction, complaint, news tip, rights or privacy. Route them only to authorized staff and follow approved retention/response deadlines.
- The public contact form is **not** a secure anonymous source channel.
- Newsletter records remain `pending` until a recipient uses the expiring confirmation link. Tokens are stored as hashes; unsubscribe changes the record and suppression must not be silently reversed.
- A configured, reviewed mail dispatcher and authenticated sending domain are still `BLOCKED_EXTERNAL`. Do not claim a message was sent when no provider is active.
- Comments remain disabled until moderation staffing, escalation and appeals are approved.

## 10. Advertising and finance

### Advertising manager

1. Open **Management Modules** → **Advertising**.
2. Add a valid advertiser, create a campaign draft with pricing model, currency, whole/smallest-unit budget and date range.
3. Submit the draft for independent approval.
4. A publisher/approver who did not create it approves the campaign; the creator cannot self-approve.
5. Upload a 60×90 JPEG/PNG/WebP photo creative (for example 600×900), a portrait MP4/WebM video creative, or a 970×90 JPEG/PNG/WebP header banner, together with title, descriptive alt text, HTTPS destination, language, disclosure, rights holder and licence/contract reference. Photos/banners are re-encoded to a safe JPEG; videos require genuine container signatures and independent playback/framing review.
6. Submit the creative. A different authorized publisher previews and approves or rejects it with a reason. Only approval publishes the safe derivative.
7. Create a placement in **Right Rail 1–4** for a 60×90 creative or **Header Banner 1–4** for a 970×90 image banner. Placement dates must stay inside the campaign; overlaps in the same slot are rejected.
8. Activate the campaign and placement only during their approved windows. The homepage, news archives and published article pages show the approved right-rail carousel and the header rotates approved banners automatically every 5.5 seconds. Both visibly disclose advertising, use sponsored links, pause for accessibility and return to AFRIVOX placeholders when no paid placement is eligible.
9. Review served, one-second/50%-viewable and click counts in step 8. They store no IP, referrer or user-agent and are operational—not certified billing evidence.
10. Contracts, invoice evidence and any external reconciliation remain governed by the approved business procedure; this is not a complete CRM or certified ad-verification service.

### Finance officer

1. Open **Finance** and issue an invoice using a unique number and 12+ character idempotency key.
2. Link only a campaign belonging to the selected advertiser and use matching currency.
3. A different authorized user records payment with amount, method, reference, received time and a unique idempotency key.
4. The amount cannot exceed the outstanding balance. The application updates partial/paid state.
5. Bank/mobile-money statement import, independent reconciliation, credits/refunds, VAT/EBM and accounting integration remain external gates. Do not report an application balance as certified cash.

## 11. Corrections, retractions and incidents

- Use a visible correction note for material public changes and retain the revision/transition history.
- Unpublish or retract only with the required explanation and editorial authority.
- Immediately escalate suspected source exposure, account compromise, malicious upload, inaccurate breaking update, legal complaint, privacy request, financial discrepancy or audit-chain failure.
- Do not erase evidence, rotate keys, reset accounts or restore backups without the named incident/release authority.

## 12. Common messages

| Message/condition | Meaning and response |
|---|---|
| Permission denied | Your active role does not allow the action; request role review, not someone else’s account |
| Changed concurrently | Another user changed the record; reload, compare and reapply intentionally |
| Publication blocked | One or more source, specialist, media, risk or separation gates failed; fix the underlying issue |
| Rate limited | Too many attempts from the same keyed request identity; wait and investigate abuse if repeated |
| Database unavailable | Stop data entry and notify technical operations; do not retry destructive actions |
| Media scanner/processor unavailable | Keep the asset quarantined; production approval must not be forced |

## 13. Role acceptance signoff

Complete after supervised staging exercises:

| Field | Record |
|---|---|
| Staff member / role | ______________________________ |
| Trainer / supervisor | ______________________________ |
| Environment and release | ______________________________ |
| Required exercises passed | ______________________________ |
| Permission denials verified | ______________________________ |
| Source/privacy/media/commercial boundaries understood | Yes / No |
| Open remediation | ______________________________ |
| Staff signature / date | ______________________________ |
| Supervisor signature / date | ______________________________ |

Signing confirms training evidence only; it does not change the production `NO-GO` decision.
