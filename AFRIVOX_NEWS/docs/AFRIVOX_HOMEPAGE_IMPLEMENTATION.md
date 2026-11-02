# AFRIVOX NEWS homepage implementation

**Implementation date:** 2 November 2026  
**Release state:** local/staging candidate; production deployment still follows the existing release gates.

## Outcome

The public homepage now follows the supplied AFRIVOX NEWS reference: a white masthead, AFRIVOX wordmark and tagline, Kinyarwanda/English selector, search, configurable social controls, dark navy navigation, three-column lead area, category quick navigation, four-column featured layout, newsletter widget, branding banner and continued category sections.

All news components read published records from the existing CMS. The implementation does not create fictional stories to fill empty positions. Existing test stories retain their visible `DEMO / URUGERO` label.

## Main source changes

- `index.php`: dynamic screenshot-led homepage, language copy, CMS slots, published-story fallbacks, newsletter form, category sections and analytics-backed Most Read.
- `app/bootstrap.php`: AFRIVOX masthead/footer, working navigation/search, language URLs, translation preference and accessible mobile controls.
- `assets/style.css`: AFRIVOX design system and desktop/tablet/mobile breakpoints.
- `assets/site.js`: accessible mobile-menu state.
- `assets/afrivox-logo.svg`, `assets/africa-mark.svg`, `assets/favicon.svg`: local vector brand assets.
- `config.example.php`: site identity, tagline and verified-profile configuration fields.
- `admin/homepage-manager.php`: `secondary_3` and `secondary_4` curation slots, using the existing table and permission model.

No database migration was required. Existing arbitrary `homepage_slots.slot_key` storage supports the additional slots.

## Content-selection behavior

1. `lead` is selected from the active homepage slot, then falls back to the newest published story.
2. `secondary_1` through `secondary_4` use active slots and chronological published fallbacks.
3. Featured News excludes the lead story.
4. Interface language changes between Kinyarwanda and English. Where a published article translation is linked through `original_article_id`, that translation is preferred; otherwise the published original remains visible.
5. Empty category sections are omitted.
6. Most Read appears only when first-party article-view data exists.

## Configuration still required

- Add verified HTTPS profile URLs to `social_profiles`; empty values intentionally render disabled icons rather than invented official accounts.
- Confirm the legal owner, publication identity, domain and AFRIVOX logo/trademark rights.
- Configure the approved newsletter confirmation dispatcher and authenticated mail domain before real delivery.
- Replace or remove all demo records before public launch.

## Visual evidence

- `docs/screenshots/AFRIVOX_homepage_desktop.png` — 1536 × 864 desktop review.
- `docs/screenshots/AFRIVOX_homepage_mobile.png` — 390 × 844 mobile review.

## Verification boundary

The homepage was rendered through the local PHP application, not a static mockup. Desktop and mobile visual review, bilingual switching, public HTTP routes, PHP syntax and the existing automated security/workflow suite are part of the recorded handoff. Production MySQL, real mail delivery, production media processing, external social profiles and public hosting remain separate deployment gates.
