# Milestone 9 — SEO, Performance, Accessibility & Security

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement SEO structured data (Schema.org JSON-LD), OpenGraph and Twitter card social meta, security sanitization, and accessibility landmarks matching Spec §9, §10, §11, §12, §13.9.

**Architecture:**
- SEO & JSON-LD generator `Tbc_Seo` in `includes/class-seo.php`
- Schema types: `TouristTrip`, `Product`, `TravelAgency`, `FAQPage`
- Social meta tags: `og:title`, `og:image`, `og:description`, `twitter:card`
- Input sanitization, output escaping, and CSRF protection
- Semantic HTML landmarks across all templates (`<header>`, `<main>`, `<footer>`)

**Spec Reference:** `docs/AI_AGENT_WORDPRESS_TOUR_WEBSITE_SPEC.md` §9, §10, §11, §12, §13.9.

---

## Tasks

### Task 1: Schema.org Structured Data & JSON-LD (`Tbc_Seo`)
- [x] Output site-wide `TravelAgency` JSON-LD schema with address, phone, email, and rating.
- [x] Output `TouristTrip` & `Product` JSON-LD schema on single tour pages with dynamic pricing and availability offers.

### Task 2: Social Media OpenGraph & Twitter Cards
- [x] Hook OpenGraph meta tags (`og:type`, `og:title`, `og:url`, `og:description`, `og:image`) into `wp_head`.
- [x] Hook `twitter:card` meta tag for rich preview on social platforms.

### Task 3: Security, Sanitization & Accessibility Verification
- [x] Verify all input parameters pass through `sanitize_text_field()`, `sanitize_email()`, `absint()`, or `floatval()`.
- [x] Verify all rendered output strings pass through WordPress escaping functions (`esc_html()`, `esc_attr()`, `esc_url()`).
- [x] Verify 100% of templates implement `<main>` landmarks complying with WCAG 2.4.1 (Level A).

### Task 4: PHPUnit Verification
- [x] Write unit tests in `tests/test-dynamic-and-seo.php` verifying JSON-LD generation and structure.
- [x] Verify 100% test pass.
