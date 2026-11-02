# AFRIVOX NEWS daily newsroom procedures

**Applies to:** Ultimate phased staging candidate, 30 October 2026  
**Release state:** staging use only; production remains `NO-GO` until every external launch gate is accepted.  
**Owner:** Editor-in-Chief or formally delegated managing editor.  
**Rule:** these procedures never override server permissions, independent approval, specialist checks or source-protection controls.

## 1. Purpose and operating record

Use this procedure for every staffed shift. The shift lead must maintain an approved handover record outside confidential-source fields containing the date, duty roster, priority stories, scheduled publications, live events, unresolved corrections, incidents, commercial conflicts and the person accepting the next shift. Never copy a confidential source's identity into chat, email, a handover note or an assignment title.

Production operation additionally requires named duty staff, emergency contacts, approved communications channels and management acceptance. Those are organizational records and are not created by this software release.

## 2. Opening the shift

The shift lead completes and records these checks before routine publishing begins:

1. Confirm the approved site URL, public homepage, login page and one published article load over HTTPS. Stop if the certificate, hostname or visible release is unexpected.
2. Confirm the previous shift's handover was accepted by a named editor. Review open incidents, complaints, correction requests, scheduled stories, pending specialist checks and live events.
3. Confirm the duty roster: assigning editor, publishing editor, fact-check/legal/media-rights contacts where required, audience duty and technical/on-call contact. No person may be silently treated as a specialist merely because another specialist is absent.
4. Check the admin dashboard, newsroom operations queue, scheduled publications, notifications, media quarantine/rights queue and live-operations queue.
5. Confirm scheduler and monitoring status from the technical owner. Do not manually publish a queue merely to hide a failed scheduler; record and escalate the failure.
6. Check the public contact/correction inbox without exporting unnecessary personal data. Assign an owner and target time for urgent complaints.
7. Record high-risk coverage expected that shift: elections/public safety, minors, sexual violence, health, legal allegations, confidential sources, investigations and conflicts of interest.

If any core control is unavailable—authentication, audit writing, source encryption, media scanning, independent approval or database integrity—freeze the affected workflow and escalate. Availability pressure is not authority to bypass a control.

## 3. Pitch and assignment desk

1. A reporter submits a pitch through **Newsroom Operations**. The desk records a decision: accept, reject, request clarification, hold or merge.
2. An accepted pitch receives an assignment with a responsible reporter, assigning editor, desk, due date and risk notes. Use neutral language in the title; put protected detail only in the authorized encrypted source-note field.
3. The assignee acknowledges the work and updates assignment state truthfully. Reassignment must identify the new owner and preserve the prior record.
4. The assigning editor identifies expected verification evidence and whether fact-check, legal, media-rights or other specialist review is mandatory.
5. A story must not be created as a way to evade the pitch/assignment history when that history is required by desk policy.

## 4. Reporting, source protection and verification

- Classify each source and record only the minimum necessary identifying information.
- Confidential source identities and sensitive notes belong only in the encrypted source-note mechanism. Never place them in titles, slugs, body copy, media filenames, alt text, ordinary comments, exported spreadsheets or third-party AI services.
- Keep claim-level evidence, right-of-reply attempts, dates, document provenance and unresolved contradictions in the approved editorial record.
- Treat a decryption failure, unexpected plaintext source data, unusual access or lost device as a critical incident. Stop access, preserve evidence and follow section 11.
- AI output may assist research or drafting only under the approved policy; it is not a source, verification step or publishing authority. A human remains accountable.
- A reporter submits only when the article is ready for review. Submission is not approval.

## 5. Review and publication sequence

The normal sequence is **draft → submitted → review → required specialist checks → approval → scheduled/published**.

1. The reviewing editor checks accuracy, fairness, attribution, right of reply, headline/body consistency, privacy, harm, language and conflicts.
2. Required fact-check, legal and media-rights records must be completed by authorized roles. A failed required check blocks approval.
3. The article author cannot approve the same article. Critical-risk approval and publication must remain independently controlled as required by the system.
4. The editor verifies media status, licence, credit, caption, alt text and expiry before approval.
5. The publishing editor checks category, author/byline, timestamps, canonical/SEO fields, language, homepage treatment and scheduled time.
6. After publish, open the public URL and verify the headline, body, images, byline, date, language, links and structured presentation. Record any defect; do not edit silently.

An editor must never change a database row or role to force a blocked article through a gate.

## 6. Homepage and taxonomy operations

- Only active, published and eligible articles may occupy a homepage slot.
- Scheduled curation requires a start/end time and an accountable editor. The chronological fallback must remain usable when no slot is active.
- Category changes must preserve hierarchy and public navigation. Deactivation must be intentional and reviewed for affected content.
- Investigation, opinion, editorial, analysis and other labelled formats must retain their correct public labels; commercial material must never masquerade as independent editorial content.

## 7. Breaking news and live coverage

### Start

1. The duty editor names the live-event owner, update authors, independent approver and closure authority.
2. Create the event in draft, define the public title/context and confirm the risk level. Only an authorized editor starts live status.
3. Establish a verification channel and cadence. “Breaking” shortens the cycle; it does not remove verification.

### During

- A reporter/editor drafts an update with source/time context. The update author cannot approve that update.
- The independent approver checks the text and publishes it. Clearly label information that is preliminary or attributed.
- Use pinning sparingly for the most important verified context.
- Do not publish graphic, identifying or unlicensed media while rights/safety review is incomplete.
- If a dangerous falsehood is circulating, explain what is known and unknown; do not amplify unsupported detail.

### Correct, withdraw and close

- Correct through the live correction action so the original body remains preserved and a public correction note is added.
- Withdraw only with an editorial reason and escalation appropriate to the harm; preserve the audit record.
- Close the event when active coverage ends, add final context and assign follow-up work. Archive only under the authorized workflow.

## 8. Corrections, complaints and retractions

1. Log every material correction/complaint with received time, public URL, allegation, contact preference and assigned owner. Minimize personal data.
2. Triage immediately for safety, legal exposure, source risk, hate/harassment, privacy harm or materially false information.
3. Verify the issue against evidence and consult the responsible desk. The subject of a complaint must not be promised removal or a result before review.
4. Make a correction through the controlled correction/revision path with an accurate public note and timestamp. Never silently rewrite a material error.
5. A retraction requires an explanation and the authorized editorial decision. Preserve the article and audit evidence according to policy; do not delete history ad hoc.
6. Notify the complainant only through an approved channel, and record the decision, rationale, reviewer and time. Appeals follow the approved policy once adopted.

## 9. Media desk procedure

1. Upload only through the media library. Originals remain quarantined/private until processing and scanning succeed.
2. Record creator, licence/basis, credit, caption, consent where applicable, expiry/territory restrictions and alt text.
3. The uploader cannot approve the same asset. A qualified independent reviewer verifies rights and suitability.
4. If scanning is unavailable in an environment where scanning is required, the asset fails closed. Do not move it directly into a public folder.
5. Report unexpected executable content, metadata leakage, duplicate ownership claims or expired rights to editorial and technical owners.

## 10. Commercial and editorial separation

- Advertising staff may prepare advertiser/campaign records but cannot approve their own campaign.
- Sponsored/native content must be unmistakably labelled and use the approved commercial workflow.
- Advertisers, donors and executives cannot direct an editorial conclusion or bypass a newsroom gate.
- A commercial conflict, threatened withdrawal of spend or proposed quid pro quo is recorded and escalated to the Editor-in-Chief/publisher without exposing a confidential source.

## 11. Security, source or publishing incident

Treat suspected source exposure, key compromise, editorial bypass, malicious media or unauthorized publication as critical.

1. Stop the affected action; do not destroy, “clean up” or privately investigate evidence.
2. Notify the named duty editor and technical/on-call owner using the approved out-of-band channel.
3. Contain the minimum necessary scope: suspend the affected account/session, uploads, scheduler or service only under authorized incident control.
4. Protect people first. Never paste source plaintext, secrets or audience records into a ticket or group chat.
5. Record detection time, affected environment/content, actions, owners and preserved evidence. Follow `INCIDENT_RESPONSE_RUNBOOK.md`.
6. Reopen only after the accountable technical and editorial owners confirm integrity checks and required tests.

External/legal notification decisions belong to authorized management and qualified advisers; newsroom staff must not improvise them.

## 12. Handover and close of shift

Before leaving, the outgoing shift lead records:

- active assignments and next action/owner/deadline;
- submitted stories and missing specialist checks;
- scheduled publications and who will verify them after release;
- live event state, last verified update and closure plan;
- correction/complaint queue and any safety/legal escalation;
- quarantined or rights-blocked media;
- open security/technical incident and containment state;
- commercial conflict or sponsored-content review;
- scheduler/monitoring anomaly and on-call acknowledgement.

The incoming lead reads back critical items and accepts the handover with name and time. The outgoing lead signs out, secures devices/notes and does not leave an unattended privileged session. If no qualified incoming owner is available, freeze affected publication rather than assigning responsibility implicitly.

## 13. Daily evidence checklist

At minimum retain, under the approved retention policy:

- opening and closing checklist with named lead;
- role/duty roster and handover acceptance;
- published/scheduled story verification record;
- unresolved correction, complaint and incident register;
- live-event owners and closure record;
- technical health acknowledgement and any failed job alert;
- exceptions, decisions, responsible owner and due date.

Do not store credentials, MFA secrets, encryption keys or confidential-source plaintext in this evidence. Management must review operating records and recurring exceptions; the existence of this document is not proof that a shift followed it.
