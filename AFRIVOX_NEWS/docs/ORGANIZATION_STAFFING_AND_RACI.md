# Organization, staffing and RACI

**Status:** operating-model proposal. Named appointments, employment terms, qualifications and compensation are `BLOCKED_EXTERNAL` until approved by the organization and applicable advisers.

## 1. Governance and reporting structure

```text
Board / Ownership
└── Publisher / CEO
    ├── Editor-in-Chief — Editorial & Newsroom
    │   ├── Managing Editor / News Desk
    │   │   ├── Section Editors
    │   │   │   ├── Reporters / Investigative Reporters / Contributors
    │   │   │   ├── Fact-Checker / Research
    │   │   │   └── Copy Editor
    │   │   └── Photo / Video / Podcast production
    │   └── Legal/Compliance reviewer (independent escalation access)
    ├── Audience & Digital Publishing Lead
    │   ├── SEO / newsletter / social distribution
    │   └── Community support/moderation
    ├── Commercial & Finance Lead
    │   ├── Advertising sales and campaign operations
    │   └── Finance/accounting and circulation (if activated)
    └── Technology & Administration Lead
        ├── System/security/development/backup
        └── HR/administration and service providers
```

The Editor-in-Chief owns factual/editorial decisions under the editorial charter. The Publisher/CEO owns strategy, finance and institutional accountability but cannot silently bypass verification. System administration has no automatic source-reading or publication authority. Commercial staff cannot edit independent reporting.

## 2. Staffing models

### Lean digital pilot (functions, not necessarily one person each)

| Function | Illustrative FTE/range | Non-negotiable boundary |
|---|---:|---|
| Publisher/operations | 0.5–1 | Cannot be the only high-risk editorial reviewer |
| Editor-in-Chief/Managing Editor | 1–2 | Accountable approval coverage and correction ownership |
| Reporters | 3–5 | Cannot approve own work |
| Fact-check/copy/legal access | 0.5–1.5 combined staff/retainer | Legal/fact decisions recorded by qualified assigned role |
| Photo/video producer | 1 | Cannot approve own media rights |
| Audience/SEO/newsletter | 1 | No editorial fact override |
| Sales/campaign operations | 1 | No editorial publication permission |
| Finance/accounting | 0.5–1 staff/provider | Payment reconciliation independent from sales where possible |
| Technology/security | 0.25–1 staff/provider | No editorial publishing authority |
| Support/moderation | Shared/on demand | Comments stay disabled if moderation coverage is absent |

Minimum headcount does not cancel separation: use independent contractors/retainers or delay the action when one employee holds multiple operational functions.

### Growth newsroom

- Publisher/CEO and operations/HR support.
- Editor-in-Chief, Managing Editor, assignment/news desk and 2–4 section editors.
- 8–15 reporters/contributors across supported beats.
- Dedicated fact-check/research and copy desks.
- Photo editor, photographers, video producer and audio producer.
- Audience development, SEO/newsletter, social distribution and moderator/support.
- Commercial director, account executives and campaign operations.
- Finance manager/accountant with independent reconciliation.
- Technology lead/developer and managed security/infrastructure support.

### Established newsroom

Add only when volume/risk justifies: deputy editors, data/investigations teams, translation desk, visual investigations, legal/DPO function, institutional sales, circulation/print management, internal audit, database/SRE/analytics engineering and formal 24-hour shifts.

## 3. Core role responsibility matrix

| Role | Reports to | Authority and daily work | KPI categories | Prohibited actions / escalation |
|---|---|---|---|---|
| Publisher/CEO | Board | Strategy, capital, contracts, enterprise risk | Runway, governance, lawful operations | No factual override; disclose conflicts; escalate independence disputes to board/editorial charter |
| Editor-in-Chief | Publisher/board under charter | Policy, high-risk decisions, standards, corrections | Accuracy, correction response, quality/cadence | No specialist impersonation or own-story approval |
| Managing Editor | Editor-in-Chief | News budget, assignments, deadlines, capacity, handovers | On-time assignments, workload, review latency | No bypass of fact/legal/media gates |
| Section Editor | Managing Editor | Desk review, revision, delegated approval | Desk accuracy, cycle time, source diversity | Cannot approve own authored story; escalate legal/high-risk work |
| Reporter | Section Editor | Research, interviews, evidence, draft/submission | Verified original work, deadline/quality | No self-approval; no disclosure of restricted sources |
| Investigative Reporter | Investigations editor/EIC | Long-form investigation and evidence chain | Evidence quality, safety, impact without sensationalism | Follow source-security/legal protocols |
| Fact-Checker | Managing/EIC | Verify claims, figures, quotations and evidence | Resolved claims, documented uncertainty | Cannot convert uncertainty into “passed” under deadline pressure |
| Copy Editor | Managing Editor | Language, structure, style and presentation | Error reduction, clarity, turnaround | Must not alter factual meaning silently |
| Legal/Compliance Reviewer | EIC/authorized governance line | Privacy, defamation, copyright, right of reply, regulatory issues | Timely recorded advice and escalations | Does not replace editorial verification; conflicts disclosed |
| Photographer | Visual editor | Licensed documentary images, caption/metadata | Rights completeness, caption accuracy | No deceptive manipulation; no self-approval |
| Video/Podcast Producer | Visual/digital lead | Plan, record, edit, caption/transcribe and clear rights | Completion, accessibility, rights | No unverified/fabricated documentary material |
| Audience/SEO Manager | Digital lead | Distribution, newsletters, analytics, audience research | Direct/return audience, consent quality | No misleading headline or editorial override |
| Social Media Manager | Audience lead | Distribute approved public content, record updates | Accurate/timely distribution | No unpublished/source disclosure |
| Advertising Manager | Commercial lead | Pipeline, contracts, campaigns, delivery | Contracted revenue, delivery, receivables | No news editing or promised positive coverage |
| Finance Officer | Finance lead/CEO | Invoices, receipts, reconciliation, reports | Accuracy, close timeliness, aging | No unsupported payment/refund; separate approvals |
| System Administrator | Technology lead/CEO | Infrastructure, users, logs, backup and security | Availability, patch/restore results | No automatic editorial/source access or publication |
| Moderator/Support | Audience lead | Feedback, complaints, moderation and appeals | Response time, consistent decisions | No deletion outside policy; no treating comments as verified facts |

Role-specific job descriptions must be finalized with qualifications, employment terms, daily/weekly deliverables, delegation limits and deputies before recruitment (`BLOCKED_EXTERNAL`).

## 4. RACI legend

- **R** — performs work.
- **A** — owns outcome; exactly one preferred.
- **C** — consulted before decision.
- **I** — informed.
- **—** — no ordinary role.

Abbreviations: PUB Publisher/CEO, EIC Editor-in-Chief, ME Managing Editor, SE Section Editor, REP Reporter, FC Fact-Checker, LE Legal reviewer, MED Media editor/producer, AUD Audience, COM Commercial, FIN Finance, IT System admin.

## 5. Editorial RACI

| Workflow | PUB | EIC | ME | SE | REP | FC | LE | MED | AUD | IT |
|---|---|---|---|---|---|---|---|---|---|---|
| Editorial policy | C | A/R | C | C | I | C | C | C | I | I |
| Accept/assign pitch | I | A | R | R | C | I | I | I | I | — |
| Research/draft | — | I | C | A | R | C | C | R | — | — |
| Source classification | — | A | C | C | R | C | C | — | — | — |
| Section review/revision | — | I | C | A/R | C | C | C | C | — | — |
| Fact-check | — | A | C | C | C | R | I | — | — | — |
| Legal/ethical review | I | A | C | C | C | C | R | C | — | — |
| Media-rights approval | — | A | C | C | I | — | C | R | — | — |
| High-risk approval | I | A | R | C | I | C | C | C | I | — |
| Publish/schedule | — | A | R | R by delegation | I | I | I | I | I | — |
| Correction/retraction | I | A | R | R | C | C | C | C | I | I for system event |
| Distribution | — | A for content | C | I | I | — | — | C | R | — |

## 6. Business/technology RACI

| Workflow | PUB | EIC | AUD | COM | FIN | IT |
|---|---|---|---|---|---|---|
| Annual strategy/budget | A | C | C | C | R | C |
| Advertising policy | A | C | C | R | C | I |
| Campaign contract | A by threshold | C for conflicts | I | R | C | — |
| Sponsored-content acceptance | C | A | C | R for commercial data | I | — |
| Invoice creation | I | — | — | C | R/A by approved matrix | — |
| Payment recording | I | — | — | I | R | — |
| Reconciliation | A/oversight | — | — | I | R by separate person/provider | — |
| Refund/credit approval | A by threshold | — | — | C | R | — |
| User/role administration | I | C for editorial assignment | — | — | — | R/A under approved request |
| Backup/restore | I | I | — | — | C for records | R/A; independent verification C |
| Security incident | A organizationally | A for sources/editorial | C | I | C | R technical containment |
| Privacy request | A/DPO | C for editorial exemptions | R support | — | C | R data retrieval under authorization |

## 7. Separation and toxic combinations

- Reporter/author + final approver for the same item: prohibited.
- Media uploader + media-rights approver for the same asset: prohibited.
- Advertising sales + independent editorial approval: prohibited.
- Invoice creator + payment reconciler + refund approver in one uncontrolled chain: prohibited.
- System administrator + routine confidential-source reader/publisher: prohibited.
- High-risk author + approver + publisher overlap: prohibited by system policy.
- Sole employee exception does not authorize bypass; delay, obtain independent review or narrow the operation.

## 8. Delegation and continuity

- Delegation must be named, time-limited, scoped and audited.
- The delegate must already be qualified for the permission and cannot inherit prohibited combinations.
- Shift handovers record deadlines, embargoes, unresolved checks, safety risk and source classification without copying secret plaintext unnecessarily.
- Deputies are assigned for Editor-in-Chief, incident commander, finance approval and system recovery.
- Account suspension/termination revokes sessions immediately; handover uses reassignment rather than shared credentials.

## 9. Performance and review

Performance measures must balance quality, safety and sustainability. Do not reward raw article volume/clicks alone. Quarterly review should include accuracy/corrections, source diversity, verified originality, deadline reliability, accessibility/rights completeness, reader trust feedback, campaign compliance, receivable quality, security training and policy adherence.

