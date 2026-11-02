# Equipment and procurement plan

**Status:** specification and budgeting framework. All RWF ranges are planning allowances, not supplier quotations or verified current market prices. Obtain at least the procurement-policy minimum number of written quotes, validate taxes/warranty/import costs and record selection. Current suppliers/prices/contracts are `BLOCKED_EXTERNAL`.

## 1. Procurement principles

- Buy for a defined workflow, named custodian and supported lifespan.
- Prefer repairable, warrantied equipment with locally supportable parts.
- Record asset ID, serial, custodian, condition, warranty, encryption and disposal.
- Encrypt endpoints; enforce supported OS/software, MFA, patching, backup and remote-loss procedure.
- Rent specialized gear and outsource printing/transcoding before demand justifies ownership.
- Separate approval, receiving and payment where staffing permits.
- Never procure a printing press under this plan.

## 2. Launch newsroom equipment

| Item | Priority | Illustrative planning range (RWF) | Qty lean | Lifespan | Notes/dependency |
|---|---|---:|---:|---:|---|
| Business laptop, 16 GB RAM/SSD class | Essential | 800,000–1,800,000 each | 6–10 | 3–5 yr | Verify warranty, battery, repair and encryption support |
| Editing workstation | Growth/visual essential | 1,500,000–3,500,000 | 1–2 | 3–5 yr | Video/photo workload benchmark required |
| External monitor | Recommended | 250,000–700,000 | 3–6 | 4–6 yr | Color accuracy for visual desk where needed |
| Business firewall/router + secure Wi-Fi | Essential | 500,000–2,000,000 | 1 set | 3–5 yr | Managed configuration and guest isolation |
| Primary internet | Essential recurring | Quote required | 1 | Contract | SLA/real measured upload/latency |
| Independent backup connection | Essential | Quote required | 1 | Contract | Different failure path/provider where possible |
| UPS/power protection | Essential | 300,000–2,000,000 | 2–5 | 2–5 yr | Load/runtime test and battery replacement |
| Encrypted backup storage | Essential | 300,000–1,500,000 | 2 sets | 3–5 yr | One offline/offsite; not sole backup |
| Office printer/scanner | Optional/shared | 300,000–1,200,000 | 1 | 4–6 yr | Avoid if secure digital workflow suffices |
| Headset | Essential | 40,000–250,000 | per staff | 1–3 yr | Calls/transcription; hearing comfort |
| Secure smartphone allowance/device | Role-based | 300,000–1,500,000 | 2–6 | 2–4 yr | MDM/loss and source-safety policy |

## 3. Photo/video/audio kit

| Package | Decision | Illustrative allowance (RWF) | Contents/quality gate |
|---|---|---:|---|
| Mobile journalism kit | Essential pilot | 400,000–1,500,000 per kit | Approved phone where needed, lav/shotgun mic, small tripod, light, power bank, storage |
| Photo body + general lens | Rent/buy after workload | 2,000,000–6,000,000 per kit | Body, suitable lens, batteries, cards, bag, weather protection; rights/metadata workflow |
| Specialist lens/second body | Rent/optional | Quote per assignment | Sports/low-light/remote work only when assignment economics justify |
| Video camera kit | Rent first | 3,000,000–12,000,000 if bought | Camera, audio, stabilization, light, batteries, cards; benchmark codec/edit workflow |
| Field audio recorder/mics | Recommended | 500,000–3,000,000 | Redundant recording, headphones, windscreens, cables |
| Podcast starter | Experimental | 1,500,000–6,000,000 | 2–4 mics, interface/recorder, arms, headphones, basic acoustic control |
| Studio build | Specialized/no-go initially | Separate study | Space, treatment, lighting, power, safety, staffing and utilization |

Documentary images may receive exposure/color/crop adjustments that preserve reality; composites, illustrations and synthetic media need explicit labels. Retain original files privately with rights and edit history.

## 4. Software and services

| Service | Priority | Buy/outsource decision | Selection criteria |
|---|---|---|---|
| Domain/DNS/TLS | Essential | Managed provider | Account MFA, change logs, recovery, DNS security |
| Production hosting/CDN/WAF | Essential | Managed hosting | PHP/MySQL support, staging, backups, logs, limits, DPA, incident SLA |
| Mail/newsletter delivery | Essential after launch gate | Approved provider | Domain authentication, opt-in/unsubscribe, suppression/webhooks, DPA |
| Monitoring/error/uptime | Essential | Managed/open tools | Alert ownership, retention, sensitive-data redaction |
| Offsite immutable backup | Essential | Managed storage | Encryption, versioning/immutability, restore test, residency/contract |
| Endpoint protection/management | Essential | Managed or supported native controls | Patch, encryption, inventory, incident response |
| Office/collaboration | Essential | Licensed service | Identity/MFA, retention, DPA, export/offboarding |
| Photo/video/audio editing | Role-based | Licensed tools | Format support, local skills, cost, provenance, accessibility |
| Accounting | Essential commercial | External software preferred | Local finance review, audit/export, roles, supported currency/tax workflows |
| Malware scanning/media processing | Essential for uploads | Host/service integration | Fail-closed, file limits, logs, update/SLA |
| Object storage/transcoding | Growth multimedia | Managed provider | Cost/egress, derivatives, signed access, regional/legal review |
| Stock/news-agency media | Optional | Licensed contract only | Territory, channel, term, archive/reuse and attribution rights |

## 5. Facilities and continuity

- Secure premises/access, lockable equipment/media storage and visitor rules.
- Stable power, grounding, UPS runtime and safe generator/shared-building procedures.
- Primary/backup connectivity tested quarterly.
- Private call/interview space and source-safety procedure.
- Ergonomic desks/seating and accessible routes/workstations.
- Fire, theft and equipment insurance review (`BLOCKED_EXTERNAL`).
- Offsite working procedure for disaster/major event coverage.

## 6. Print decision

Outsource any validated print pilot. Obtain printer specifications covering page size, bleed/trim, PDF standard, color profile, image resolution, proofing, plate/change deadline, spoilage, quantity tolerances, confidentiality, delivery, payment and reprint remedy. Confirm distributor responsibilities, returns, cash custody and sales reporting.

Owned press: `NO-GO` without an independent capital, staffing, maintenance, facility, safety, consumables and utilization study.

## 7. Procurement workflow

Need request → budget/technical specification → conflict declaration → quotes/tender → documented evaluation → authorization → contract/order → receipt/inspection → asset register → invoice match/payment → maintenance → secure disposal.

Emergency procurement must be retrospectively documented and cannot bypass conflict/rights/security rules.

## 8. Replacement and maintenance

- Quarterly inventory and access review.
- Battery/storage-health checks; tested spare kit for critical assignments.
- Annual lifecycle forecast with 20–30% contingency for failure/price movement as an internal planning choice, not a market fact.
- Secure wipe/destruction certificate before resale/disposal.
- Subscription renewal owner and cancellation/export plan.
- Supplier concentration and exit risk reviewed annually.
