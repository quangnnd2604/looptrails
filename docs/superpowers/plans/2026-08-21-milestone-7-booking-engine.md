# Milestone 7 — Booking Engine, Pricing Calculations & Checkout Flow

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the server-authoritative pricing engine, quote generation, booking REST API, antispam protection, and automated email dispatching matching Spec §6, §7, §8, §13.7.

**Architecture:**
- Authoritative calculation class `Tbc_Pricing_Engine` in `includes/class-pricing-engine.php`
- REST API Controller `Tbc_Booking_Handler` in `includes/class-booking-handler.php`
- Email notification handler `Tbc_Mailer` in `includes/class-mailer.php`
- Form fields validation & honeypot spam protection
- Unit tests via PHPUnit (`test-booking-engine.php`)

**Spec Reference:** `docs/AI_AGENT_WORDPRESS_TOUR_WEBSITE_SPEC.md` §6, §7, §8, §13.7.

---

## Tasks

### Task 1: Server-Authoritative Pricing Engine (`Tbc_Pricing_Engine`)
- [x] Implement base tour price per person calculation with party size multiplier.
- [x] Implement vehicle option surcharge (Self-Ride, Easy Rider with driver, Jeep).
- [x] Implement transfer addons (pickup bus, return bus) and motorbike rental per-day rates.
- [x] Implement voucher code validation (`WELCOME10`, `EARLYBIRD`) and discount computation.
- [x] Implement 20% deposit calculation vs full payment amount.
- [x] Implement integer VND conversion using configured exchange rate.
- [x] Implement HMAC quote payload signing for tamper prevention.

### Task 2: Booking REST API Endpoints (`Tbc_Booking_Handler`)
- [x] Register `POST /wp-json/tour-booking/v1/quote` endpoint for real-time frontend calculations.
- [x] Register `POST /wp-json/tour-booking/v1/book` endpoint for order submission.
- [x] Implement honeypot antispam validation (rejection of bot submissions).
- [x] Generate unique booking reference `LT-YYYYMMDD-XXXX`.
- [x] Create `booking` post type entry and save all booking metadata in WP database.

### Task 3: Email Notification System (`Tbc_Mailer`)
- [x] Customer HTML booking confirmation email with itemized money breakdown and booking reference.
- [x] Admin alert email with customer details and direct WP Admin edit link.

### Task 4: PHPUnit Test Coverage
- [x] Write unit tests in `tests/test-booking-engine.php` covering quote calculation, voucher discounts, honeypot rejection, and booking post insertion.
- [x] Verify 100% test passing in PHPUnit.
