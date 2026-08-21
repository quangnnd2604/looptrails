# Milestone 8 — Dynamic Features, Multi-Currency & Search Filtering

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement dynamic features including multi-currency display/conversion (USD & VND), query search filtering by duration and price range, and interactive display elements matching Spec §6, §7, §13.8.

**Architecture:**
- Multi-currency helper class `Tbc_Currency` in `includes/class-currency.php`
- Dynamic tour search query builder `Tbc_Search_Filter` in `includes/class-search-filter.php`
- Unit tests via PHPUnit (`test-dynamic-and-seo.php`)

**Spec Reference:** `docs/AI_AGENT_WORDPRESS_TOUR_WEBSITE_SPEC.md` §6, §7, §13.8.

---

## Tasks

### Task 1: Multi-Currency & Formatting Engine (`Tbc_Currency`)
- [x] Implement USD price formatting (`$140`).
- [x] Implement Vietnamese Dong (VND) price formatting with thousand-separators and currency symbol (`3.556.000 ₫`).
- [x] Implement bidirectional USD ↔ VND conversion functions.

### Task 2: Dynamic Search & Tour Filtering (`Tbc_Search_Filter`)
- [x] Build query filtering by duration days (`tbc_duration_days`).
- [x] Build query filtering by maximum price in USD (`tbc_price_from_usd`).
- [x] Support responsive query pagination and sorting parameters.

### Task 3: PHPUnit Verification
- [x] Write unit tests in `tests/test-dynamic-and-seo.php` testing currency conversion, format strings, and query generation.
- [x] Verify all assertions pass.
