# Milestone 5 — Home and Reusable Visual Components

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the Home page template (`templates/front-page.html` / `templates/home.html`) and the full suite of reusable visual components and block patterns for `tour-reference-theme` and `tour-booking-core` matching the reference structure, measured tokens, and spec §5.2 / §5.3.

**Architecture:**
- Gutenberg block theme patterns in `wp-content/themes/tour-reference-theme/patterns/`
- Reusable dynamic tour card block / query pattern powered by `tour-booking-core` CPTs and metadata
- `front-page.html` assembling header, section patterns in exact reference order, and footer
- Self-contained CSS in `assets/css/theme.css` consuming tokens from `theme.json`
- Full test coverage via PHPUnit (`WP_UnitTestCase`)
- Visual acceptance testing via Playwright + `pixelmatch` comparing local Home against `docs/reference-screenshots/home/`

**Spec Reference:** `docs/AI_AGENT_WORDPRESS_TOUR_WEBSITE_SPEC.md` §5.2, §5.3, §13.5.

---

## Tasks

### Task 1: Reusable Tour Card Component & Block Pattern
- [x] Create `patterns/tour-card.php` / dynamic render callback for tour cards in `includes/tour-card.php`.
- [x] Fields: image, badge, duration, title, price variants by vehicle option, Book Now & Details buttons.
- [x] CSS for 3-col desktop / 2-col tablet / 1-col mobile grid matching measured dimensions (336px width, 14px radius, 22px gap).
- [x] Unit tests in `tests/test-tour-card.php` (passed).

### Task 2: Hero & Narrative Brand Section Patterns
- [x] Create `patterns/hero-home.php`: full-bleed background, headline, subhead, Book Now CTA.
- [x] Create `patterns/brand-narrative.php`: 2-column layout with feature stats, image, and CTA.
- [x] Unit tests for markup rendering and accessibility (passed).

### Task 3: Tabbed "Top Destinations & Essentials" & "Why Choose Us" Patterns
- [x] Create `patterns/top-destinations-essentials.php`: 4-tab container with destination cards.
- [x] Create `patterns/why-choose-us.php`: 3x2 feature grid + dark stat band (4 summary numbers).
- [x] Unit tests in `tests/test-home-patterns.php` (passed).

### Task 4: Reviews, Editorial CTA & FAQ Accordion Patterns
- [x] Create `patterns/testimonials.php`: original demo reviews & platform badges.
- [x] Create `patterns/editorial-cta.php`: standalone full-width CTA banner.
- [x] Create `patterns/faq-accordion.php`: interactive Q&A accordion with accessible `<details><summary>` elements.
- [x] Unit tests in `tests/test-home-patterns.php` (passed).

### Task 5: Home Page Template (`front-page.html`) & Demo Content Assembly
- [x] Create `templates/front-page.html` assembling all patterns in exact reference order.
- [x] Create `templates/home.html` with `<main>` landmark.
- [x] Ensure integration with demo tours imported from `tour-booking-core`.
- [x] Unit tests in `tests/test-home-template.php` (passed).

### Task 6: Visual Regression Audit & Milestone 5 Acceptance Report
- [x] Update `tools/local-audit/capture-local.mjs` to capture full Home page across 5 viewports.
- [x] Run `diff.mjs`, `check-colors.mjs`, `check-metrics.mjs`, `check-overflow.mjs`.
- [x] Generate `docs/visual-acceptance-report-m5.md` (in Vietnamese).
- [x] Commit and finalize Milestone 5.
