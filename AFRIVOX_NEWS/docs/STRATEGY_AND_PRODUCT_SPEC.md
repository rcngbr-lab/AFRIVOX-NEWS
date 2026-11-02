# COMPREHENSIVE PRODUCT & BUSINESS SPECIFICATION
## Luxury Digital Newspaper for Rwanda — AFRIVOX NEWS prototype

**Status:** Strategic blueprint and demonstration source, not a registered media company or fully deployed publication.

## 1. Investment thesis

Build an independent digital news publication rooted in Rwanda with professional journalism, mobile-first distribution, distinct specialist desks, visual storytelling, strict editorial review and financially viable advertising/reader products. A beautiful website does not by itself establish a media business: the expense and scarcity lie in reporting, verification, distribution, legal risk and predictable revenue.

**Fundamental market risk:** An attractive news site can have low direct traffic because social platforms mediate discovery. Reuters Institute 2026 across 48 markets reports 54% using social/video networks weekly for online news versus 51% using publishers' own websites/apps; this is NOT Rwanda-specific evidence. User acquisition assumptions for Rwanda must be validated independently.

Primary reference: https://reutersinstitute.politics.ox.ac.uk/digital-news-report/2026/dnr-executive-summary

## 2. Audience segments and jobs-to-be-done

| Segment | Reader need | Format | Monetization hypothesis | Risk |
|---|---|---|---|---|
| Broad Kinyarwanda audience | Accurate, fast local information | concise article, explainer, audio, vertical video | sponsorship + direct advertising | platform dependency, low repeat visits |
| Professionals/entrepreneurs | Market, policy, jobs, financing context | business analysis, daily briefing | high-value sponsor, limited membership | expensive original reporting |
| Students & young people | Clarify current affairs, opportunities, technology | explainers, short video, accessible language | sponsored education series (separated from editorial) | competition for attention |
| Diaspora | trustworthy local coverage, context and translation | bilingual weekly digest | subscriptions, institutional partners | small reachable niche, payment issues |
| Policymakers/researchers | reliable datasets, source links, historical archives | data journalism, fact-checks | research products / licensing | conflicts of interest, costly verification |

**Validation plan:** 25-40 qualitative interviews across segments; diary study, 4-week social content distribution test, sign-up landing page, advertiser discovery interviews (10-15), and measured repeat visits. Neither online impressions nor social followers alone demonstrate willingness to pay.

## 3. Newsroom sections

National news, politics/governance, Africa/world, economy, finance/business, technology/AI, health/science, education, sports, culture/entertainment, lifestyle/tourism, environment/climate, investigations, fact-checking, opinions, editorial, data journalism, photo essays, documentary video and podcasts. Treat sections as an editorial taxonomy; avoid launching all specialist desks before staffing them.

Each section should have a qualified editor, documented coverage policy, source validation requirements and topic-specific verification checklist. Consider 3-5 core desks initially rather than 15 under-resourced ones.

## 4. Article and media data model (future complete)

Each news item requires at minimum: headline, slug, standfirst, category, tags, author(s), editor, type, language, locality, publication timestamp, last updated, status, references, source notes, corrections, feature/breaking flags, images with caption/credit/permission, social OG metadata, related content, SEO fields and audit history.

For investigations add confidential source protection, document provenance, legal review and secure channels outside standard web contact forms. For photojournalism capture capture time, location when ethically safe, permissions, licensing agreement, consent, edit history, alt text and crop derivatives. Avoid disclosing precise locations or metadata if doing so threatens sources or vulnerable persons.

Editorial types must be visible and distinct: **News**, **Explainer**, **Analysis**, **Opinion**, **Fact Check**, **Sponsored/Advertisement**. A sponsored story must not inherit the ordinary news label.

## 5. UX / Premium design requirements

- Positioning: high-end, quiet, trustworthy, not crowded or flashy; serious newspaper typographic hierarchy.
- Type system: serif for reporting headlines and sans for navigational/interface text; sensible fallbacks for Kinyarwanda glyphs.
- Colors: warm off-white `#faf9f5`, deep slate `#172327`, restrained warm red `#be4239`; strict contrast and not overused accent colors.
- Desktop: magazine masthead, date/edition, category navigation, one strong lead feature, 2 editor picks, short recency row, deep dives, economic desk, media zone, clearly marked ad inventory, newsletter and footer transparency.
- Mobile: thumb-friendly links, no hidden article headline behind ads, single-column features, readable 16-18px body, no horizontal overscroll, low bandwidth images.
- Usability: descriptive headings, semantic HTML, labels/alt text, visible focus rings, reduced-motion support, keyboard navigation, straightforward search and error pages; test against WCAG 2.2 AA.
- Performance: locally hosted images via responsive `srcset`, CDN, AVIF/WebP derivatives, lazy-loading below fold, critical CSS, minimal JS. Track Core Web Vitals LCP, INP, CLS with real mobile data.
- Dark mode: optional after accessibility tests; do not prioritize over readability.
- Typography and images: luxury must never imply editing documentary photos in misleading ways or using dramatic images unrelated to the article.

## 6. Content operations and governance

- Editor-in-chief has final responsibility; assignment editor plans daily news budgets; senior editor verifies and publishes; reporter researches/writes; photo/video editors clear rights; legal/data-protection expertise available when needed.
- Beat planning + editorial calendar + assignment desk + fact-check + legal review for high-risk stories + publish + corrections + archival retention.
- Publication policy must cover anonymous sources, conflicts of interest, gifts, breaking news, corrections, right of reply, minors, privacy, copyright, defamation, sponsored stories, generative AI, access control and takedown requests.
- For political reporting: verify primary records, attribute contested claims, distinguish factual news from commentary, give appropriate right of response.
- Never use a generic website contact form as an anonymous whistleblower security platform. Use purpose-built secure channels and reviewed source-handling processes.

## 7. Distribution (do not rely on search alone)

- Owned: SEO, direct visits, newsletters, RSS, browser notifications (after consent), branded app in later phases.
- Third party: Facebook, Instagram, YouTube, TikTok, WhatsApp channels and responsible syndication. Adapt format and editorial standards, avoid copying arbitrary viral content.
- Partnerships: universities, business associations, creators and trusted local institutions subject to conflict-of-interest policy.
- Analytics: first-party, consent-aware analytics where required. Segment by source, geography only at appropriate granularity, device, reading depth, return visits and subscriber conversion. Do not assume all readers can pay.

## 8. Revenue architecture

1. Clearly labeled sponsorship and advertising: sell placements, branded packages and safe sponsor inventory without editorial influence.
2. Programmatic display advertising: later, after traffic and privacy safeguards justify ad-tech overhead.
3. Paid membership/reader support: test willingness to pay, payment processing and cancellation rules before deploying a hard paywall.
4. Events and public discussions: budgets, logistics, safety and venue operations matter.
5. B2B research briefs and corporate subscriptions: only if independent expertise and demand are proven.
6. Training and licensing: consider rights and conflicts of interest.

**Unit economics:** Monthly revenue = contracted direct advertisements + sold sponsorships + net membership payments + events/research - cancellations/refunds. Monthly operating costs = payroll + reporting travel + equipment + rights/licensing + hosting/CDN + distribution + security + legal/accounting + payment processing + tax. Break-even requires monthly gross margin covering overhead and reserve; traffic alone does not equal monetization.

Illustrative, **not market price benchmarks**: With 4 reporters, 1 editor, 1 video/visual producer, 1 sales/account person, and freelance/legal support, monthly payroll and fieldwork will likely dominate server cost. Estimate every role using verified local quotes before funding.

**Advertiser interviews:** ask for last 3 campaigns, budget, approval cycle, measurement and purchasing terms. A claimed interest is not signed revenue. Track sell-through, receivables aging, advertiser concentration and cancellations.

## 9. Technology architecture (target beyond prototype)

```
Reader (desktop/mobile)
  -> DNS + TLS + CDN/WAF + edge caching
  -> Web frontend (SSR responsive editorial pages)
  -> Application API (role-based access + article service)
     -> CMS / Newsroom / review engine
     -> Search service / queue / webhooks
     -> relational database (news, users, permissions, audit)
     -> object storage (masters, image variants, videos)
     -> external video platform/transcoding as needed
     -> email delivery provider (verified opt-in)
     -> billing/payment provider (only after PCI/security scoping)
     -> privacy-aware analytics and observability
     -> backup and disaster recovery
```

**Current MVP:** monolithic PHP 8 + MySQL/MariaDB + CSS/vanilla JS to suit common cPanel deployment. No React/Node runtime required. This is a low-operations-cost baseline; it lacks content caching, a dedicated queue, datawarehouse and HA.

**Scalability trigger:** if write contention, traffic spikes, or newsroom needs justify, isolate media into object storage/CDN, add Redis cache, background image jobs, dedicated search, API endpoints and read replicas. Maintain editorial integrity and deployment observability before microservices.

## 10. Risk register

| Assumption | What breaks | Mitigation / validation |
|---|---|---|
| Users will visit homepage directly | social distribution gets views but site remains empty | newsletter/direct traffic experiments |
| Ad buyers will pay | inventory unsold, slow accounts receivable | pre-sell pilot campaigns with real contracts |
| Freelancers can supply verified reporting | plagiarism, libel, misinformation | named editors, verification checklists, payment/time budget |
| Content can be produced cheaply with AI | hallucinations, weak trust, legal disputes | human original reporting; AI transparency rules |
| Media is just software | field reporting, translation, legal work consume budgets | real news production staffing plan |
| Shared cPanel will scale indefinitely | outages at spikes, no job queue | capacity test; staged hosting migration |
| Photos are free to copy | copyright claims and loss of reputation | rights register, author credit, license verification |
| A paywall is easy revenue | payment friction, cancellations and churn | market willingness-to-pay experiment |
| Compliance can wait until launch | sanctions, takedowns and reputation loss | licensing and DPO work before collecting data |

## 11. Rwanda-specific regulatory checklist

- Check RURA Online Newspaper licensing procedure and current terms at `licensing.rura.rw`: RMC media registration, leadership CVs/records/IDs, business plan and formal company registration are listed; do not infer that website registration alone meets these obligations.
- Confirm applicable obligations with Rwanda Media Commission (`rmc.rw`), RURA, and qualified counsel based on the business and media type.
- Rwanda Law Nº 058/2021 on personal data and privacy: map data categories, purpose/legal basis, retention, international processors/transfers, data subject requests, registration where required, security, incident response.
- Separate advertising rules, copyright and licensing, right of correction/reply, privacy, minors and defamation review in editorial policies.

Official sources:
- https://www.licensing.rura.rw/
- https://www.rura.rw/sectors/ict/sub-sectors-and-services/ict-media-regulation
- https://rmc.rw/accreditations
- https://dpo.gov.rw/dpp-law/registration-of-a-data-controller-and-a-data-processor
- https://rwandalii.org/akn/rw/act/law/2013/2/eng%402013-03-11

## 12. Go/no-go

**Verdict: SALVAGE / PROCEED ONLY WITH A NARROW PILOT.** Build brand and readership through 3-5 desks, a credible editor, verified original coverage, a consistent distribution operation and customer development with actual advertisers. Kill early expansion into 15 sections, expensive native apps, full studios and a hard paywall until demand and editorial capacity are demonstrated.
