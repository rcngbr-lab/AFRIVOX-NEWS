# Homepage Content-Balance Audit — 6 November 2026

## Scope

Compared the local PHP/CMS homepage at `http://127.0.0.1:8765/?lang=en` with the static GitHub Pages preview at `https://rcngbr-lab.github.io/AFRIVOX-NEWS/index.html`. Desktop (1440 px) and mobile (430 px) full-page renders were inspected. The static preview and dynamic homepage used the same editorial layout; GitHub Pages remained a non-operational client preview.

## Finding

The homepage was image-heavy, but not video-heavy. Both pages rendered 34 images and zero video/iframe elements. Thirty-eight article blocks reused a small demonstration dataset across hero, featured, latest, category and most-read sections. Category cards used large landscape images and produced excessive vertical length and empty white space. Only 10 paragraph elements were available to balance the visual content.

This was a presentation defect rather than a CMS or database defect. No article, media record, route or workflow needed replacement.

## Incremental correction

- Preserved the hero, Featured News, Latest News, CMS queries, article links, navigation, advertising rail and responsive breakpoints.
- Replaced only the category presentation with compact text-led cards: small thumbnail, category/type label, headline, summary and timestamp.
- Replaced image-based Most Read cards with a ranked text list and short summaries.
- Kept the Featured Videos module conditional on approved published `type=video` records; no fake video content was introduced.

## Measured result

| Metric | Before | After |
| --- | ---: | ---: |
| Images | 34 | 30 |
| Video/iframe elements | 0 | 0 |
| Article blocks | 38 | 38 |
| Paragraphs | 10 | 21 |
| Approximate visible words | 823 | 938 |
| Compact category cards | 0 | 7 |
| Ranked text stories | 0 | 4 |

Desktop and mobile re-renders show a shorter edition, denser editorial information, earlier footer discovery and less repeated-image dominance. The correction does not change the database schema, article selection rules, permissions or publication workflow.
