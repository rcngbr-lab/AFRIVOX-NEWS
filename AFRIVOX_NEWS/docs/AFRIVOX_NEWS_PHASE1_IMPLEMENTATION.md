# AFRIVOX NEWS transformation — Phase 1 implementation

Date: 2026-11-12  
Branch: `feature/afrivox-news-transformation`

## Scope delivered

The existing AFRIVOX NEWS PHP/PDO application remains the foundation. Authentication, RBAC, editorial workflow, article publishing, media rights, advertising governance, audit logs and newsletter double opt-in were preserved.

The public health experience now includes:

- AFRIVOX NEWS identity, responsive header, navigation and footer.
- Health-focused CMS homepage with honest empty states and explicit demo labels.
- Health News, Health Topics, Health Tips, Health Tools, Wellness, Health Talks and Partners routes.
- BMI Calculator for adults, Pregnancy Calculator and WHO weight-for-age Child Growth Tracker.
- Health-aware search across published articles, topics, tips and tools.
- Rebranded double-opt-in newsletter presentation.
- Additive health taxonomy migration and local SQLite seed support.

## Medical and privacy controls

- The tools are educational and do not diagnose or prescribe treatment.
- BMI uses `kg / m²` and neutral adult screening bands.
- Pregnancy estimates use 280 days from the first day of the last menstrual period and state that clinical dating may differ.
- Child weight-for-age uses the WHO 0–5 LMS reference data stored in `assets/data/wfa-0-5.json`.
- Calculator inputs run in the browser and are not submitted, logged or stored by the application.
- No fictional health result is displayed when inputs or reference data are invalid.

Primary references:

- WHO Child Growth Standards, weight-for-age: <https://www.who.int/tools/child-growth-standards/standards/weight-for-age>
- WHO BMI screening definition and adult ranges: <https://www.who.int/data/gho/data/themes/topics/topic-details/GHO/body-mass-index>
- ACOG Methods for Estimating the Due Date: <https://www.acog.org/clinical/clinical-guidance/committee-opinion/articles/2017/05/methods-for-estimating-the-due-date>

## Verification

| Component | Existing implementation | Changes made | Result |
|---|---|---|---|
| Frontend | AFRIVOX responsive public templates | AFRIVOX NEWS health design and routes | PASS |
| Backend | PHP/PDO CMS and public projections | Reused unchanged for health article publication | PASS |
| Database | MySQL schema + SQLite local mirror | Additive health category migration | PASS |
| CMS | Draft/review/approval/publish workflow | Preserved; homepage reads published `ubuzima` content | PASS |
| Security | RBAC, CSRF, MFA, encrypted sources, audit logs | Preserved; tools do not transmit health inputs | PASS |
| Tests | 83 security/editorial/business tests | Full suite rerun | 83 PASS, 0 FAIL |
| Routes | Existing public site | 13 AFRIVOX NEWS route checks | HTTP 200 |

## Known production gates

- Replace demo-labelled health story records only through the existing editorial workflow with verified, dated sources.
- Assign qualified medical review for high-risk clinical content before public release.
- Configure the newsletter delivery provider and production domain; double opt-in remains mandatory.
- Apply the MySQL migration in staging, validate rollback/backup evidence, then authorize production separately.
