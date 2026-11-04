# AFRIVOX NEWS Ultimate requirement traceability

**Trace date:** 30 October 2026  
**Release class:** staging candidate; not production-ready.

## Status rules

- `COMPLETE`: scoped artifact/local behavior exists with evidence.
- `PARTIAL`: implementation exists but required breadth or verification is missing.
- `BLOCKED_EXTERNAL`: infrastructure, provider, credential, operating data, professional approval or deployment authority is required.
- `DEFERRED`: deliberately postponed behind an approved business/safety gate.
- `NOT_STARTED`: no material deliverable was found.

Artifact completeness is separate from operational acceptance. For example, a `COMPLETE` recovery runbook does not make recovery operational until a real restore drill passes.

## A. Ultimate prompt capability map

| Prompt area | Status | Implementation evidence | Test/acceptance boundary |
|---|---|---|---|
| 3. Existing-project audit | `COMPLETE` | `IMPLEMENTATION_ASSESSMENT.md`, `ULTIMATE_IMPLEMENTATION_AUDIT.md`, source/schema inspection | Current increment still requires final regression |
| 4. Business strategy/investment | `COMPLETE` as planning artifact | `PHASE0_MEDIA_BUSINESS_PLAN.md`, `MARKET_AND_AUDIENCE_ASSESSMENT.md`, `FINANCIAL_MODEL_AND_RISK.md` | Interviews, quotes, contracts and financing are `BLOCKED_EXTERNAL` |
| 5–6. Organization/responsibilities | `COMPLETE` as operating model | `ORGANIZATION_STAFFING_AND_RACI.md` | Named appointments, qualifications and employment terms are external |
| 7. Granular RBAC/multiple roles | `COMPLETE` locally | `EditorialWorkflow.php`, `bootstrap.php`, `roles.php`, `RBAC_MATRIX.md`, `user_roles` | Full browser role matrix pending staging |
| 8. Fourteen-stage editorial workflow | `PARTIAL` | State machine, pitches/assignments, checks, revisions, approval, scheduling, correction/retraction | Research claim-level register, distribution and complete browser E2E need depth |
| 9. Professional newsroom dashboard | `PARTIAL` | Admin CMS, specialist queues, newsroom operations, notifications foundations | Workload, shifts/handover, investigations and report views incomplete |
| 10. Configurable categories/topics | `PARTIAL` | Persistent categories, category administration, article classification | Tags, series and richer topic pages incomplete |
| 11–13. Public design/pages | `PARTIAL` | Responsive homepage/article/category/author/search/media/live/policies/contact routes | Full page inventory, account/membership pages and device/AT acceptance incomplete |
| 14. Photojournalism | `PARTIAL` | Quarantine, rights fields, approval, captions/alt/credit | Positive processing/scanning E2E blocked; galleries/essays/edit history incomplete |
| 15. Video/podcasts | `PARTIAL` | Article types and basic public media presentation | Ingest, storage, captions/transcripts, transcoding and podcast operations absent |
| 16. Newspaper/e-paper | `DEFERRED` | Feasibility and procurement gates documented | Explicit `NO-GO`; no edition/export/circulation module |
| 17–18. Advertising/CRM | `PARTIAL` | Advertisers, campaigns and basic commercial dashboard | Lead/quote/contract/creative/placement/delivery/credit workflow incomplete |
| 19. Finance/accounting | `PARTIAL` | Integer invoice/payment records and reporting foundation | Approval, reconciliation, credits/refunds, tax/accounting integration incomplete |
| 20. Audience management | `PARTIAL` | Subscribers, contact inbox and first-party events | Reader accounts, profiles, memberships, consent request operations incomplete |
| 21. Comments/feedback | `PARTIAL` | Contact/correction inbox | Comments/moderation intentionally not operational until staffed |
| 22. Newsletter/notifications | `PARTIAL` | Subscriber lifecycle foundation and staff notifications | Approved mail provider/domain, delivery webhooks and campaign operations blocked |
| 23. Digital marketing/growth | `PARTIAL` | SEO/RSS/sitemap/analytics foundations and strategy | Campaign attribution, experiments and real dashboards not acceptance-tested |
| 24. Business/economic reporting | `PARTIAL` | Category/data-story capability and editorial standards | Dedicated data workflow, analyst capacity and sourced datasets external |
| 25. Equipment/physical operations | `COMPLETE` as plan | `EQUIPMENT_AND_PROCUREMENT_PLAN.md` | Quotes, premises, assets, providers and contracts external |
| 26. Complete admin dashboard | `PARTIAL` | Editorial, staff, media, audience, commercial, finance, analytics and system pages | Several modules are foundational, not complete workflows |
| 27. Executive dashboard | `PARTIAL` | Summary counts and module reports | Cash runway, budget/actual, risk/KPI governance and verified live data incomplete |
| 28–29. Technical/database architecture | `PARTIAL` | Modular PHP/PDO services, normalized schema, migrations, API/CLI | MySQL rehearsal, tag/series/reader/print/CRM depth and scale infrastructure remain |
| 30. Security/reliability/privacy | `PARTIAL` | MFA, revocation, encrypted sources, audit chain, media quarantine, runbooks | Pen test, scanner, remote audit, monitoring, restore and privacy operations blocked |
| 31. Legal/regulatory compliance | `BLOCKED_EXTERNAL` | Official-source readiness checklist and internal controls | Only authorities/qualified advisers can confirm applicability and compliance |
| 32. SEO | `PARTIAL` | Canonical, article metadata/JSON-LD, RSS, sitemaps and language model | Live-domain crawl/Search Console/Rich Results validation external |
| 33. Accessibility/performance | `PARTIAL` | Responsive design, semantic/focus/reduced-motion foundations | Human screen-reader/device audit and measured Web Vitals/load tests missing |
| 34. Multilingual publishing | `PARTIAL` | `rw/en/fr` fields and original-translation relationship | Assignment/reviewer UI, synchronized corrections and published counterparts incomplete |
| 35. Responsible AI | `PARTIAL` as policy | Human/provenance requirements in architecture/strategy | No approved AI integration or operational register |
| 36. Phased execution | `COMPLETE` as plan | Roadmaps, phase decisions, traceability and release gates | Execution beyond current staging scope remains |

## B. Required 46 deliverables

| # | Deliverable | Status | Evidence / remaining requirement |
|---:|---|---|---|
| 1 | Media business plan | `COMPLETE` | `PHASE0_MEDIA_BUSINESS_PLAN.md`; validation external |
| 2 | Market assessment | `COMPLETE` | `MARKET_AND_AUDIENCE_ASSESSMENT.md`; Rwanda-specific primary research pending |
| 3 | Audience strategy | `COMPLETE` | Phase 0 and strategy documents; actual cohorts/targets pending |
| 4 | Organizational chart | `COMPLETE` | `ORGANIZATION_STAFFING_AND_RACI.md` |
| 5 | Staff responsibility matrix | `COMPLETE` | Role responsibilities, boundaries and escalation recorded |
| 6 | Staffing plan | `COMPLETE` | Lean/growth/established models; hiring external |
| 7 | Equipment/procurement plan | `COMPLETE` | Planning ranges are explicitly not quotes |
| 8 | Startup budget | `COMPLETE` as scenario | `FINANCIAL_MODEL_AND_RISK.md`; verified quotes external |
| 9 | Monthly operating budget | `COMPLETE` as scenario | Assumptions documented; not actual performance |
| 10 | Financial forecast/risk | `COMPLETE` as scenario | Cash/runway/scenario/risk model documented |
| 11 | Editorial policy | `PARTIAL` | `EDITORIAL_CHARTER.md`; formal organizational approval pending |
| 12 | Journalism ethics policy | `PARTIAL` | Charter/strategy cover boundaries; dedicated approved code/training needed |
| 13 | Complete editorial workflow | `PARTIAL` | Server state machine plus newsroom operations; integrated E2E pending |
| 14 | Assignment procedures | `PARTIAL` | Pitch/assignment/calendar implementation and operating rhythm; handover/reassignment depth pending |
| 15 | Verification checklist | `PARTIAL` | Gate matrix/check records; claim-level verification UI/procedure incomplete |
| 16 | Source protection policy | `COMPLETE` as technical/operational baseline | Encryption/access/audit/threat model; legal retention and device training external |
| 17 | Corrections/complaints policy | `PARTIAL` | CMS corrections and inbox foundation; approved SLA/appeals procedure pending |
| 18 | Editorial approval matrix | `COMPLETE` | `EDITORIAL_WORKFLOW_GATE_MATRIX.md`, `RBAC_MATRIX.md` |
| 19 | Multimedia editorial standards | `PARTIAL` | Rights workflow and procurement guidance; full video/audio/photo standards needed |
| 20 | Newspaper production procedures | `DEFERRED` | Print is `NO-GO` until separate validation |
| 21 | Updated frontend source | `COMPLETE` as source artifact | Dynamic public application exists; complete page inventory/device acceptance partial |
| 22 | Updated backend source | `COMPLETE` as source artifact | Security/editorial/admin services exist; full institution modules partial |
| 23 | Database schema/migrations | `COMPLETE` as artifact | Schema/migrations/local adapter exist; MySQL rehearsal blocked |
| 24 | Complete Admin Dashboard | `PARTIAL` | Multi-domain dashboard exists; several workflows/reporting views incomplete |
| 25 | Staff/permission management | `COMPLETE` locally | Multiple roles, suspension, MFA, revocation, audit; staging browser E2E pending |
| 26 | Editorial CMS | `COMPLETE` locally | Draft/review/check/approval/publish/revision/correction foundation |
| 27 | Multimedia management | `PARTIAL` | Media rights/quarantine implemented; pipeline and rich format operations incomplete |
| 28 | Audience management | `PARTIAL` | Subscriber/contact/analytics foundation; accounts/membership/privacy ops incomplete |
| 29 | Advertising management | `PARTIAL` | Advertiser/campaign foundation; complete campaign delivery not implemented |
| 30 | Finance integration/accounting controls | `PARTIAL` | Invoice/payment ledger; reconciliation/accounting/tax provider path incomplete |
| 31 | Newspaper edition module | `DEFERRED` | Deliberate `NO-GO` |
| 32 | SEO implementation | `PARTIAL` | Core technical SEO exists; live-domain/index validation blocked |
| 33 | API documentation | `PARTIAL` | `API.openapi.yaml` covers a limited read-only API, not every internal module |
| 34 | Automated tests | `PARTIAL` | Strong policy/data tests; full E2E/MySQL/browser/load/accessibility tests absent |
| 35 | Deployment configuration | `PARTIAL` | Example config, Apache controls and runbooks; target hardening/secret/monitoring config external |
| 36 | Security assessment | `COMPLETE` as internal assessment | Threat model/security QA/audit exist; independent pen test absent |
| 37 | Privacy/compliance checklist | `COMPLETE` as readiness artifact | Official decisions and registrations `BLOCKED_EXTERNAL` |
| 38 | Backup/recovery procedures | `COMPLETE` as runbook | Actual restore scenario is `BLOCKED_EXTERNAL` |
| 39 | Monitoring configuration | `BLOCKED_EXTERNAL` | No selected destination, credentials, alert routing or on-call evidence |
| 40 | User manuals | `COMPLETE` as artifact | `USER_MANUAL.md` provides role-based operating guidance; user acceptance remains external |
| 41 | Staff training guide | `COMPLETE` as artifact | `STAFF_TRAINING_GUIDE.md`; delivery, assessment and completion records remain external |
| 42 | Daily newsroom procedures | `COMPLETE` as artifact | `DAILY_NEWSROOM_PROCEDURES.md`; named shift ownership and completed handover/incident records remain operational evidence |
| 43 | Technical administrator guide | `COMPLETE` as artifact | `TECHNICAL_ADMIN_GUIDE.md`; production infrastructure acceptance and drill records remain external |
| 44 | Production readiness report | `COMPLETE` | This audit, implementation report and release checklist clearly say `NO-GO` |
| 45 | Risks/dependencies | `COMPLETE` | Audit, threat, financial, compliance and roadmap records |
| 46 | Future roadmap | `COMPLETE` | `PRODUCTION_ROADMAP.md` and prioritized audit actions |

## C. Test and evidence index

| Evidence | Current record |
|---|---|
| Local policy/data-integrity suite | Current merged baseline: 70 passed, 0 failed; SQLite setup/upgrade and HTTP/XML/admin render smoke matrices also pass locally |
| PHP syntax | All 51 current PHP files pass `php -l` on PHP 8.5.5 |
| Migration preflight | Current local result `READY`; MySQL/MariaDB rehearsal still blocked |
| Audit-chain verification | Current local signed chain verifies; remote immutable export absent |
| MySQL integration/concurrency | `BLOCKED_EXTERNAL` |
| Positive media upload/scanning | `BLOCKED_EXTERNAL` |
| Browser/role E2E | `PARTIAL` / not fully recorded |
| Backup restore | `BLOCKED_EXTERNAL` |
| Accessibility/device/load/security independent tests | `BLOCKED_EXTERNAL` or not yet performed |

## D. Release rule

No requirement changes from `PARTIAL`, `BLOCKED_EXTERNAL`, `DEFERRED` or `NOT_STARTED` to `COMPLETE` without an evidence link, test result, named owner and review date. Current source is eligible for controlled staging only; production remains `NO-GO`.
