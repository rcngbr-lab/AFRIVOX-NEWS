# SEO implementation guidance record

Source review: 2026-10-29. AFRIVOX NEWS implements server-rendered pages, canonical URLs, `NewsArticle` metadata, RSS and XML sitemap foundations. These controls improve machine readability but do not guarantee ranking or Google News inclusion.

- Google says news sitemaps should contain only articles from the last two days and use the original publication time: [News sitemap guidance](https://developers.google.com/search/docs/crawling-indexing/sitemaps/news-sitemap).
- Article structured data should reflect visible content and include applicable author, date, headline and representative image fields: [Article structured-data guidance](https://developers.google.com/search/docs/appearance/structured-data/article).
- Localized versions should use distinct URLs and reciprocal self/alternate `hreflang` annotations; the page’s visible content must actually be translated: [Localized-version guidance](https://developers.google.com/search/docs/specialty/international/localized-versions).
- Sitemaps assist discovery but do not guarantee crawling, indexing or ranking: [Sitemap overview](https://developers.google.com/search/docs/crawling-indexing/sitemaps/overview).

Search Console ownership, live Rich Results testing, crawl diagnostics and production news-sitemap submission remain `BLOCKED_EXTERNAL` until the real domain is deployed and verified.
