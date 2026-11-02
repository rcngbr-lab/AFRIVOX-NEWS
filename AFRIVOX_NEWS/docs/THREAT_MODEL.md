# Threat model: AFRIVOX NEWS newsroom

## Assets and trust boundaries

The highest-value assets are confidential-source identities and evidence, unpublished investigations, staff credentials/MFA recovery codes, editorial approval history, licensed media, payment records and the audit trail. The public site, admin session, private media directory, database, scheduled worker, mail/scanner providers and backup destination are separate trust boundaries.

## Priority threats and controls

| Threat | Main controls | Residual risk / owner |
|---|---|---|
| Source identity leaks through public output | Explicit public article projection; internal fields encrypted; publication blocks plaintext; source views/updates logged | Search/API/feed additions must reuse the projection. Security/editorial leadership reviews access reports. |
| Account takeover | Password hashing, password/MFA rate limits, TOTP, single-use recovery codes, session regeneration, per-request account/session-version checks | Phishing-resistant MFA and managed devices are future improvements. Operations owns alerting. |
| Insider misuse of confidential sources | Least-privilege roles, source access log, technical/editorial separation, encrypted storage | Step-up for every restricted-source view and dual-control export remain future controls. |
| Editorial bypass | Server-side state machine, exact specialist permissions, approval/publish separation, failed-check and high-risk gates | Real staging E2E and periodic policy review required. Editor-in-Chief owns policy. |
| Malicious or polyglot uploads | MIME and pixel checks, GD decode/re-encode, private quarantine, no public derivative before approval | Positive path is blocked until GD and a malware scanner are installed. Security owns scanner operation. |
| Copyright/license violation | Required creator/source/license/holder/alt metadata, independent approval, expiry and prohibited-use checks | Human legal interpretation and license taxonomy remain external. |
| Audit deletion/tampering | No ordinary delete UI; HMAC chained events; verifier CLI | Database administrators can still rewrite both rows and keys; export to WORM/SIEM is required in production. |
| Role escalation | Version-controlled permission policy, system administrator isolation, password reauthentication, privileged MFA, session revocation | A second-person approval queue for role changes is recommended. |
| Race conditions | Article lock versions and conditional state transitions in transactions | MySQL concurrent-load tests are still required. |
| Commercial influence | Technical, editorial, advertising and finance permissions are separated | Governance and sponsored-content policy require human approval. |

## Abuse cases to retest before each release

1. Reporter calls transition endpoints directly to publish.
2. Generic editor posts a legal or fact-check result.
3. Author approves their own story or a critical story is approved/published by the same person.
4. Pending, expired, prohibited or self-approved media is attached and publication attempted.
5. Plaintext source data is inserted by a legacy migration and approval attempted.
6. Suspended user, expired role or revoked session repeats an old request.
7. Reused recovery code or ninth MFA failure is submitted.
8. Two editors save or transition the same article simultaneously.
9. Audit row is modified and chain verification is run.

## Accepted vs unaccepted risk

No production risk is accepted by this document. Missing provider/legal/infrastructure controls are launch blockers, not waivers. Any exception needs named owner, expiry date, compensating controls and written approval outside the application.
