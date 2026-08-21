# Milestone 6 — Archives, Tour Detail, Rental, Blog, Information/Legal, and Error Templates

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build all secondary templates and block patterns for `tour-reference-theme` matching the specification (§5.4, §5.5, §13.6) and measured reference audits (`docs/reference-audit/02-tour-detail.md`, `03-secondary-pages.md`):
1. `single-tour.html` (Tour Detail)
2. `archive-tour.html` (Tours Archive)
3. `page-motorbike-rental.html` (Motorbike Rental)
4. `single.html` & `archive.html` & `search.html` (Blog & Search)
5. `page-contact.html` & `page-about.html` (Contact & About)
6. `page.html` (Terms / Privacy / Generic Page) & `404.html` (Error Page)

---

## Tasks

### Task 1: Tour Detail Template (`single-tour.html`) & Patterns
- [x] Single tour template with Hero, Overview, Itinerary Day-by-Day timeline, Included/Excluded highlights, Pricing breakdown by vehicle option, and Related Tours grid.
- [x] Unit tests in `tests/test-secondary-templates.php` (passed).

### Task 2: Tours Archive Template (`archive-tour.html`) & Patterns
- [x] Tours archive template with header banner, 12-tour dynamic grid, benefits strip, and CTA.
- [x] Unit tests in `tests/test-secondary-templates.php` (passed).

### Task 3: Motorbike Rental Template (`page-motorbike-rental.html`) & Patterns
- [x] Motorbike rental landing template with bike cards grid (specs, transmission, rates), rental requirements, and FAQ.
- [x] Unit tests in `tests/test-secondary-templates.php` (passed).

### Task 4: Blog Single (`single.html`), Blog Archive (`archive.html`), and Search (`search.html`)
- [x] Clean editorial layout with featured image, meta, related posts navigation, query archive, and search results template.
- [x] Unit tests in `tests/test-secondary-templates.php` (passed).

### Task 5: Contact (`page-contact.html`), About (`page-about.html`), Legal (`page.html`), and 404 (`404.html`)
- [x] Contact page with info box & form.
- [x] About page with stats, story narrative, and services grid.
- [x] Terms & Privacy long-form reading layout in `page.html`.
- [x] 404 error page.
- [x] Unit tests in `tests/test-secondary-templates.php` (passed).

### Task 6: PHPUnit Verification & Milestone 6 Acceptance Report
- [x] Run all test suites (theme 42/42 + plugin 47/47).
- [x] Create `docs/visual-acceptance-report-m6.md` in Vietnamese.
- [x] Git commit and push to master.
