# MASTER BUILD SPEC — WORDPRESS TOUR WEBSITE

Version: 2.0  
Date: 2026-08-19  
Visual and functional reference: https://looptrails.com/  
Platform: WordPress, custom block theme, no paid dependency

> This document is the single source of truth for the coding agent. Build a production-ready WordPress website whose public layout, visual rhythm, responsive behavior and user flows closely reproduce the current public Loop Trails website, while using an original brand, original code, legally reusable images and replaceable sample content. Do not copy proprietary source code, logo, reviews or business text.

## 0. Non-negotiable outcome

The implementation is not accepted merely because it is a travel website with similar sections. It is accepted only when:

1. The header, menus, page order, section composition, grids, cards, tabs, forms, calls to action and footer match the reference site's current public presentation at the defined viewports.
2. Typography, colors, spacing, widths, image ratios, radii, borders, shadows and responsive transitions are measured from the reference and encoded as global design tokens.
3. All public content and business data can be added, edited, reordered, hidden or deleted in WordPress Admin without editing PHP, JavaScript or CSS.
4. A non-technical administrator can add a new tour, its price variants, itinerary, capacity, add-ons and translations; the new tour automatically appears in menus, listings, search, booking and related-tour areas according to admin settings.
5. Booking information is saved in WordPress Admin and emailed to the customer and configured administrators. Payment supports the reference flow plus OnePay, MoMo and VNPay adapters.
6. English is the default language and displays USD. Vietnamese displays VND. Language switching changes both localized content and display currency.
7. The implementation passes the visual and functional acceptance tests in this document.

Words such as “inspired by”, “approximately”, “use your judgment”, “similar travel style” or “make it modern” are not permission to invent a different layout. When the reference is observable, measure and reproduce it. When it is not observable, choose the smallest implementation consistent with adjacent reference patterns and document the assumption.

## 1. Legal and content boundary

- Reproduce observable layout, interaction patterns and functionality with original code.
- Do not download or redistribute Loop Trails' logo, photos, maps, customer reviews, articles or private assets.
- Use neutral placeholders and legally reusable images from Unsplash, Pexels or Pixabay, recording source URL and license/source note in Media Library attachment metadata.
- Do not copy reference business claims, legal terms, addresses, people, ratings or contact information. Seed clearly labeled demo text of comparable length so layout remains faithful.
- Use open-licensed fonts only. If the exact public font is identifiable and legally available through Google Fonts or another permitted source, self-host it. Otherwise select the closest open alternative and record the substitution.

## 2. Required implementation architecture

### 2.1 Theme and plugins

Build an original custom WordPress block theme named `tour-reference-theme`.

- Use current stable WordPress and PHP 8.2+.
- Use Gutenberg/block editor, `theme.json`, native Navigation and reusable block patterns.
- Do not use Elementor, Elementor Pro, Divi, WPBakery, a commercial theme or a page-builder runtime.
- Do not require a paid plugin or paid SaaS to render or operate the core site.
- Polylang Free may be used for EN/VI content relationships. If it cannot cover a required workflow cleanly, implement the minimal translation linkage in the companion plugin.
- Use a companion plugin named `tour-booking-core` for post types, fields, booking, pricing, availability, payments, emails, REST/AJAX endpoints and scheduled jobs. Business logic must not live in the theme.
- Prefer native meta boxes and registered post meta. ACF Free may be used only where it remains fully exportable/version-controlled and does not create a paid dependency. Repeater-like structures must be implemented with a small purpose-built admin UI or child post types, not ACF Pro.
- All custom code follows WordPress Coding Standards, uses nonces, capabilities, escaping, sanitization and prepared queries.

### 2.2 Repository/deliverables

The coding agent must deliver:

- `wp-content/themes/tour-reference-theme/`
- `wp-content/plugins/tour-booking-core/`
- version-controlled field/schema definitions and database migration/version logic
- sample-data importer and safe sample-data remover
- `.env.example` or documented environment constants without secrets
- installation, admin, payment sandbox and deployment documentation
- automated tests and a manual QA checklist
- screenshot baselines and visual comparison report

No domain, email, API key, gateway secret, phone number, exchange rate or business identity may be embedded in source code.

## 3. Reference-first visual reconstruction protocol

Before implementing UI, the agent must inspect every public reference template listed in section 5. Capture full-page screenshots and DOM/CSS measurements at:

- desktop: 1440 × 1000
- laptop: 1280 × 800
- tablet: 768 × 1024
- mobile: 390 × 844
- narrow mobile: 360 × 800

Create `docs/reference-audit.md` containing, for every page/section:

- screenshot filename and capture date
- section order and anchor behavior
- container width, gutters and vertical spacing
- font family, weight, size, line height and letter spacing
- exact visible colors in HEX/RGBA
- card dimensions, column count, gaps, image aspect ratio, radius, border and shadow
- button height, padding, radius, icon placement, hover/focus behavior
- breakpoint behavior: hidden, stacked, scrollable, collapsed, sticky or reordered
- animation/transition type and duration

Do not start final styling until this audit exists. Reference values observed by the agent override provisional token examples in this spec.

Encode measurements in `theme.json` and CSS custom properties. Components must consume tokens; do not scatter raw colors and arbitrary spacing through component files. Admin must expose brand logo, core colors and contact/social settings. Content editors may change text/media without breaking the layout.

## 4. Visual acceptance rule

Render local pages at the same viewports with animation disabled and compare them beside the current reference. Use Playwright screenshots plus an image-diff tool.

Acceptance targets:

- same page section order and same component count for the corresponding seeded demo page
- no missing header, navigation, CTA, booking control or footer element
- global container edges within 8 px of reference at desktop and 6 px at mobile
- major section start positions within 24 px until content-length differences naturally accumulate
- font size within 1 px, line height within 2 px and font weight visually equivalent
- color channel difference no greater than 5 for measured solid UI colors
- component radius, gap and padding within 2 px
- no horizontal overflow at 360 px
- overall screenshot pixel difference target below 8% after masking legally substituted photos, logo, map tiles and text regions whose original wording cannot be copied

If the target is missed, the agent must iterate rather than declare completion. The final report must list remaining deltas with screenshots; “looks close” is not a test result.

## 5. Complete public page scope

Build all current public page types and flows visible from the reference navigation, internal links, booking journey and footer:

1. Home.
2. Tours archive/listing.
3. Tour detail template, supporting all observed tour variations.
4. Motorbike Rental landing/detail content.
5. Blog archive, category/tag archive, search and single article.
6. Contact.
7. About.
8. Terms, Conditions & Privacy Policy presentation.
9. Booking/checkout interface, whether displayed as an anchored home section, modal or dedicated page according to the current reference behavior.
10. Payment return: success, failed, cancelled and pending.
11. Booking lookup using booking code plus verified email or signed token.
12. Generic search results, empty states and 404.
13. Cookie consent and privacy controls if tracking is enabled.

### 5.1 Global header/navigation

Reproduce the reference desktop and mobile header precisely:

- configurable light/dark logos and accessible site-name fallback
- primary navigation with Home, Tours dropdown and the remaining reference destinations in the same order for seeded data
- Tours dropdown populated from selected/published tours, not hardcoded links
- prominent Book Now control with identical placement and scroll/navigation behavior
- language/currency control for EN/USD and VI/VND
- sticky state, background/color change, spacing and mobile hamburger/drawer behavior matching reference
- keyboard-operable dropdown/drawer, visible focus and correct ARIA state

Menus are managed through WordPress Navigation. Admin can change label, target, hierarchy and order. Header contact/CTA visibility and destination are configurable.

### 5.2 Home composition

The seeded Home page must reproduce the current reference section order and component behavior, including:

- hero with background media, headline, supporting text and booking CTA
- featured tour grid/cards with badge, rating display, taxonomy chips, multi-option prices, Book Now and Details
- narrative destination/brand section with media, feature statistics and CTA
- tabbed “Top destinations & everything you'll need” area: Destinations, Itinerary & Map, Transport and Accommodation
- destination cards and metadata chips
- itinerary duration tabs and day-by-day panels, route map, included and excluded lists
- transport option cards
- accommodation gallery/cards
- “Why choose us” feature grid and statistics
- review/testimonial presentation using original demo reviews only
- large editorial CTA section
- full booking interface in the same location/behavior as reference
- footer

Each section is a registered block pattern or dynamic block with an Inspector setting for visibility, anchor and style variant. Repeating content comes from post types/relationships. Editors can reorder page sections in Gutenberg. Seed data must initially preserve reference order.

### 5.3 Tour cards

Tour cards are one reusable server-rendered component used everywhere. Fields:

- localized title, excerpt and slug
- featured image and mobile crop focal point
- badge label/type and visibility
- rating value/count display; no fabricated third-party schema rating
- tour type, destination and feature tags
- duration/days/nights
- price rows by ride/vehicle option and party-size tier
- “from” price computed server-side from currently active bookable variants
- Book Now deep-link preselecting the tour
- Details link

The card layout, image ratio, badges, price alignment and responsive stacking must match reference screenshots.

### 5.4 Tour detail

Provide editable fields/blocks for:

- hero/gallery, title, badges, rating presentation, overview and quick facts
- price variants and booking CTA/sticky CTA
- destination highlights
- itinerary with Day 0 and unlimited following days
- route map image or configurable map embed
- included/excluded lists
- vehicle/ride options
- accommodation options and upgrades
- bus transfers and add-ons
- safety/requirements content
- FAQs
- related tours
- final CTA

No fixed limit on itinerary days, price variants, destinations, add-ons, gallery images or FAQs.

### 5.5 Footer

Reproduce reference structure, proportions, colors, typography and mobile accordion/stacking behavior. Admin controls:

- logo/about text
- navigation columns and headings
- contact details, address, map URL, email, phones and WhatsApp
- social profiles
- legal links
- copyright text
- accepted payment icons
- newsletter block visibility/configuration

No footer text or URL is hardcoded.

## 6. Content and admin data model

Register, at minimum:

- `tour`
- `destination`
- `itinerary_day` or an ordered structured child model
- `vehicle_option`
- `accommodation`
- `transfer_option`
- `addon`
- `testimonial`
- `faq`
- `booking`
- `voucher`
- `availability_rule`

Use taxonomies where filtering/archives are useful: tour type, destination region, duration, feature and blog taxonomy.

Every translatable public field has EN and VI values or a linked translation record. Shared operational values—SKU, capacity, dates, stock, base VND price, payment state—must not be duplicated between translations.

Admin must support:

- add/edit/clone/archive/reorder tours
- featured/best-seller/new badges
- unlimited price variants and party-size tiers
- date ranges, departure dates, capacity, available/limited/full/blocked states and blackout dates
- bulk availability and seasonal-price editing
- add/edit/reorder itinerary days and included/excluded items
- media focal points and alt text per language
- voucher type, amount/percent, validity, usage limit, eligible tours and minimum spend
- global settings for branding, contact, email recipients, deposit, fees, currency, exchange-rate source, gateway sandbox/live mode and legal text
- role capabilities so Booking Manager cannot edit code/plugins and Translator cannot edit prices/payments

All delete actions use trash/soft deletion where WordPress supports it. Operational records retain an audit trail.

## 7. Internationalization and currency

- Default locale: English.
- English frontend currency: USD.
- Vietnamese frontend currency: VND.
- Store canonical prices as integer VND minor-safe amounts; never use binary floating point for financial calculation.
- USD is a display/charge currency derived using an admin-configured rate. Store the rate and converted amounts used on each booking so historic totals never change.
- Exchange rate modes: manual (always available), scheduled external provider (optional), and emergency fallback to last successful rate.
- Admin configures provider, update frequency, markup/rounding, stale-rate threshold and fallback behavior.
- Show a clear warning in Admin when rate data is stale. Do not silently use zero or an unavailable API.
- Locale-specific formatting is required throughout cards, booking summary, emails and Admin.
- Language switch preserves the current translated page/tour and booking selections where possible.
- Static strings use WordPress i18n functions and ship with translation templates; no UI string is embedded only in JavaScript.

## 8. Booking form and pricing engine

Reproduce the reference field order, conditional controls, summary and mobile behavior. The required seeded flow includes:

1. Tour.
2. Tour Start Date with availability legend/status.
3. Riding Option: Motorbike or Jeep.
4. Experience: Easy Rider or Self-driving when applicable.
5. Jeep Type when applicable.
6. Party Size per vehicle when applicable.
7. Accommodation.
8. Motorbike Rental cards/options and per-day pricing.
9. Bus to destination: option, pickup date and pickup time.
10. Bus after tour: option, return date and return time.
11. Full Name.
12. Number of People.
13. Email.
14. WhatsApp/phone with international validation.
15. Special Request / Message.
16. Voucher Code and Apply.
17. Honeypot/anti-spam control invisible to legitimate users.
18. Live Booking Summary with Tour, pickup bus, return bus, accommodation, rental, subtotal, discount and total.
19. Payment Type: 20% deposit or full payment; threshold rules configurable.
20. Payment Method.
21. Terms acceptance.

Conditional fields must disappear and be excluded from totals when irrelevant. Changing an upstream choice must invalidate incompatible downstream selections visibly.

### 8.1 Server-authoritative pricing

- Browser values are never trusted.
- Every update requests or receives a server-calculated quote with a signed quote ID and expiry.
- Server reloads active tour, dates, variants, capacity, add-ons, voucher, tax/fee and exchange rate.
- On submission and before payment capture, recalculate and compare. If changed, stop and request customer confirmation.
- Return an itemized money breakdown in both frontend and stored booking.
- Deposit default is 20%, editable globally and overridable per tour.
- Processing fees are configurable by gateway/method and may be disabled. Seed reference-like examples only as clearly labeled demo settings.
- Use idempotency keys for booking creation, gateway requests and callbacks.

### 8.2 Availability

- Calendar represents available, limited, full and blocked states as the reference does, plus accessible text not color alone.
- Capacity is checked and locked atomically at confirmation to prevent concurrent overbooking.
- Pending unpaid reservations expire after an admin-configured period and release held capacity.
- Admin may override capacity with a required reason recorded in the audit log.

## 9. Booking lifecycle, Admin and email

Booking statuses: draft, pending-payment, payment-pending, deposit-paid, paid, confirmed, cancelled, failed, refunded, partially-refunded and expired.

Every booking stores:

- immutable booking code
- customer and selected service details
- itemized canonical VND and displayed/charged currency totals
- language, exchange rate, deposit/full choice and balance due
- gateway/method, external transaction IDs and callback history
- consent timestamp, policy version, IP privacy-safe metadata and audit notes
- email delivery log without exposing secrets

Admin provides filter/search/export CSV, booking detail, status transitions, resend email, internal notes and payment timeline. Destructive or financial actions require capability checks, confirmation and audit entries.

Emails:

- new booking to configured administrator recipients
- booking received/pending payment to customer
- payment/deposit receipt
- confirmation and balance information
- failed/pending payment instructions
- cancellation/refund status

Templates are editable/brandable in Admin with safe placeholders and EN/VI versions. Use WordPress mail through configured SMTP/provider; document that reliable SMTP is required in production.

## 10. Payments

Implement a gateway interface and separate adapters for:

1. OnePay International Card (Visa/Mastercard).
2. OnePay Domestic/Vietnamese Bank.
3. VNPay.
4. MoMo.

Requirements:

- sandbox/live modes and enable/order controls in Admin
- credentials stored through environment constants or encrypted/appropriately protected settings; never printed or committed
- signed requests and verified callback signatures per current official gateway documentation
- return URLs are not proof of payment; only verified server callbacks/status queries update paid state
- exact-amount and currency validation, replay protection, idempotency and transaction logging with sensitive fields redacted
- pending/timeout reconciliation job
- deposit and full-payment support
- mobile redirect/deep-link recovery and clear success/failure/pending pages
- production gateway stays disabled until merchant credentials and sandbox acceptance tests exist

Do not invent gateway parameters. At implementation time consult current official OnePay, VNPay and MoMo documentation and record documentation version/date in `docs/payments.md`.

## 11. Performance, accessibility, SEO and security

Performance targets on optimized staging, measured for Home and a Tour detail:

- Lighthouse mobile Performance ≥ 85
- Accessibility ≥ 95
- Best Practices ≥ 95
- SEO ≥ 95
- no avoidable layout shift; properly sized responsive WebP/AVIF images
- self-host fonts with preload only for critical faces; no page-builder payload
- defer non-critical scripts; tabs/drawers/book form progressively enhance server-rendered HTML

Accessibility:

- WCAG 2.2 AA target
- full keyboard booking flow, menus, drawers, tabs, modal and date controls
- visible focus, semantic landmarks, labels/errors, focus management and reduced-motion support
- color contrast and touch targets compliant even while matching the reference

SEO:

- editable SEO title/description, canonical and social image
- XML sitemap, robots controls, breadcrumbs and clean localized URLs
- valid Organization, WebSite, BreadcrumbList, Tour/Trip/Product where semantically correct, BlogPosting and FAQ schema
- never seed fake AggregateRating/review schema
- hreflang for `en`, `vi` and `x-default`

Security:

- sanitize input, escape output, nonces, least-privilege capabilities and rate limiting
- prepared SQL and REST permission callbacks
- CSRF, stored/reflected XSS, IDOR, price tampering, coupon abuse, upload and callback replay tests
- secure cookies/HTTPS guidance, security headers, secret management and redacted logs
- data-retention and export/erasure support for customer data

## 12. Demo content

Provide a one-click importer with enough original demo content to render every layout state:

- at least 6 tours matching the reference card variety and duration variety, with generic/original names and prose
- destinations, multiple itinerary lengths, transport, accommodation and add-ons
- availability states, price variants, vouchers and reviews clearly marked demo
- English and Vietnamese versions
- legally reusable remote image attribution followed by local Media Library import; do not hotlink in production

All sample operational records carry an `is_demo` marker. Provide a safe remover that deletes only marked demo records after confirmation.

## 13. Implementation milestones

The coding agent must execute in this order and keep the site runnable after each milestone:

1. Environment audit, backup/staging confirmation and reference screenshots.
2. Reference audit/design tokens and approved component inventory.
3. Companion plugin schema, roles, migrations and demo importer.
4. Theme shell: header, navigation, footer and global responsive tokens.
5. Home and reusable visual components.
6. Archives, tour detail, rental, blog, information/legal and error templates.
7. EN/VI and USD/VND behavior.
8. Booking form, server pricing, availability, Admin and emails.
9. Gateway adapters in sandbox only.
10. Accessibility, security, performance and SEO hardening.
11. Visual diff iteration at all target viewports.
12. Final automated/manual QA, documentation and handover.

At each milestone report files changed, migrations, tests run, screenshots and known deviations. Do not ask the owner to decide ordinary implementation details already specified here.

## 14. Required automated tests

- unit tests for every price component, rounding, conversion, deposit, fee, voucher and expiry rule
- unit/integration tests for availability and simultaneous booking attempts
- REST/AJAX permission, nonce and tampered-price tests
- gateway signature/callback/idempotency tests using official sandbox fixtures
- booking status-transition tests
- email template/render tests for EN and VI
- Playwright end-to-end flows for each ride option, add-ons, deposit/full payment and gateway outcome
- Playwright keyboard/mobile navigation and booking tests
- screenshot tests at all defined viewports
- PHP lint, WordPress Coding Standards and JavaScript lint

Tests must not call live payment endpoints or send real customer email.

## 15. Definition of Done

The project is complete only when all are true:

- all page types in section 5 exist in EN and VI
- seeded pages pass the visual acceptance rule or documented exceptions are explicitly approved
- desktop/mobile header, menu, home section order, cards, tabs, booking and footer reproduce the reference behavior
- no business content, menu item, tour, price, availability, itinerary, footer or contact datum requires a code edit
- an administrator can add a tour end-to-end and book it immediately
- booking records save correctly and customer/admin emails are verified in a mail sandbox
- OnePay, VNPay and MoMo sandbox flows and verified callbacks pass
- EN uses USD and VI uses VND with stored booking exchange-rate snapshots
- security, accessibility, performance and SEO targets are tested
- no paid theme/plugin is required
- no secret, copied proprietary asset, fake review/schema claim or live demo payment configuration ships
- installation, admin, backup, gateway and deployment documentation is complete
- final handover includes a deviation report; zero undisclosed deviation is allowed

## 16. Instructions to the coding agent receiving this file

Start by reading this entire file. Then inspect the current public reference and the target WordPress environment. Do not immediately generate a generic theme. First produce the reference audit, screenshots, component inventory, data schema and implementation plan. Proceed autonomously through the milestones unless credentials, production access, legal/business text or an irreversible external action is required.

If the reference changes after the capture date, treat the newest captured current public version as the visual target and record the date. If access to the reference is blocked, stop visual implementation and report the specific missing evidence; do not substitute a generic design.

The final result must feel like the same carefully measured interface system and booking product, reimplemented legally for a different operator—not merely another orange travel template.
