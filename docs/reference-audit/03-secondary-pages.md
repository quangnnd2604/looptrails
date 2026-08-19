# Secondary Pages

Source data: `docs/design-measurements/{tours-archive,motorbike-rental,blog-archive,blog-single,contact,about,terms,404}/*.json` (5 viewports each — desktop 1440px, laptop, tablet 768px, mobile 390px, narrow-mobile), cross-referenced against the matching folders under `docs/reference-screenshots/` (slugs there differ from the measurement slugs; the mapping used is noted per template below). Values not explicitly cited below (body text color, primary orange, header/footer background, social-icon brand colors) match the global palette already documented in `00-global.md` and are not repeated here — this section covers only what's distinct to each template.

One data-quality note that applies throughout: colors sampled from the `skip-link screen-reader-text` element (an off-screen accessibility-only link) are a measurement artifact, not a visible design color — they are excluded from the per-page palettes below even where they show a high `count`.

---

## Tours Archive

Screenshot folder: `docs/reference-screenshots/tours/`. Measurement folder: `tours-archive/`.

**Section order** (from `tours/desktop.png`): hero banner (dark photo background, eyebrow label, H1, subtitle, CTA button) → "OUR HA GIANG LOOP TOUR PACKAGES" heading → 2×2 tour package card grid → promo/permit banner strip → full booking widget (tour/date selectors, riding option, accommodation, bus transfer, personal info, voucher, cost summary, payment type/method, terms checkbox, Confirm Booking button) → "WHY CHOOSE US" accordion → Tripadvisor strip → footer.

**Typography/color distinct from global:** price figures use a dedicated red, `rgb(230,0,35)` / `#e60023` (`.price-value`), with a muted slate `#64748b` for the USD conversion and `#94a3b8`/`#718096` for secondary label text. A green `#10b981` marks discount amounts. Tour titles (`h3.tour-title`) use the system font stack at 20px/700 in `#1a202c`, distinct from the Montserrat headings used elsewhere on the page. Pill/spec badges use `#475569` text on a `#fff5f0` background. None of this is present in the Home page's typography sample, so it's specific to this template's card + booking-widget components.

**Card/button measurements:** the automated card-grid probe only picked up one grid on this page — `.hgl-grid-2` (4 items, 397×68px desktop / 312×64px mobile, `gapX: 16`, no radius/shadow) — which is the bus-transfer summary row inside the booking widget, not the visible tour package cards. The 2×2 tour package grid seen in the screenshot (thumbnail with corner ribbon badge, title, star rating, spec pills, dual price rows, "Book Now" + "View Details" buttons) was not captured by the probe's selector heuristics; its dimensions are not available as exact pixel values and would need direct DOM inspection during build. Button styles measured: "Book Now" pill (44px, radius 25px, orange fill or white/orange outline depending on placement, `boxShadow: rgba(255,102,2,0.3) 0px 4px 15px 0px`) and "View Details" (44px, radius 25px, white fill/orange text, no shadow) — both 13px/700 text.

**Responsive behavior:** the 2×2 tour card grid collapses to a single column at mobile (390px), each card full-width and stacked vertically (confirmed via `tours/mobile.png`); the booking widget stays a single column at all widths below desktop. Container width is a confirmed 1200px at desktop/laptop (`mostCommonContainerWidth: 1200`, 6 matching candidates); at tablet/mobile/narrow-mobile it is `null` — consistent with the rest of the site, content runs full-width with side padding below the 1200px breakpoint rather than being boxed to a smaller fixed width.

---

## Motorbike Rental

Screenshot folder: `docs/reference-screenshots/ha-giang-motorbike-rental/`. Measurement folder: `motorbike-rental/`.

**Section order**: hero banner (photo of bikes, H1 "Explore More", subtitle, "Rent Now" CTA) → intro copy block → "Why Choose Our Ha Giang Motorbike Rental Service" accordion (4 items) → "Choose Your Ha Giang Motorbike" heading → 2×2 bike selection card grid (badge, photo, transmission-type label, title, spec description, price, "Book Now") → requirements info banner → full rental booking widget (bike picker, rental dates/days/count, personal info, cost summary, payment type/method, terms, "Book Now & Pay") → FAQ accordion (6 items) → footer.

**Typography/color distinct from global:** this is the one template whose typography array is dominated by the DM Sans family for nearly all UI text (`.ltr-*` classes — bike price, bike name/desc, form labels, summary rows), whereas most other templates lean on the system font stack or Montserrat for the same roles — worth flagging for the design-tokens pass since it means DM Sans is a real secondary UI font, not a one-off. Colors: bike-card border `#e5e7eb` (rest) / orange `#ff6602` (selected), selected-card background `#fff5f0`, feature-icon chip background `#f7f7f7`, required-field red `#ef4444`.

**Card/button measurements:** `.ltr-bikes` grid — 4 items, 310×319px at desktop, radius 12px, `boxShadow: rgba(255,102,2,0.15) 0px 0px 0px 3px` (soft orange ring, not a drop shadow), `gapX: 12`, `imageAspectRatio: 1.365`. At mobile (390px) itemWidth is 314×322 — nearly unchanged from desktop's 310×319, confirming the grid is already a single column at mobile rather than shrinking a multi-column layout. Buttons: "Rent Now" primary (47-55px height, radius 7px, orange fill, `boxShadow: rgb(54,52,59) 2px 3px 0px 0px` — the site's consistent hard-offset shadow style), "Book Now" header variant (35px, radius 7px, white fill/orange text, same offset shadow).

**Responsive behavior:** container `mostCommonContainerWidth` is `null` at every viewport including desktop on this template (`candidateCount: 6` but none reach majority) — same "full-width content, no single dominant boxed width" pattern noted for other secondary pages; only Home, Tours Archive and About showed a clean 1200px majority. Bike grid and requirements FAQ both go to single-column stacking at mobile per `ha-giang-motorbike-rental/mobile.png`.

---

## Blog Archive

Screenshot folder: `docs/reference-screenshots/blog/`. Measurement folder: `blog-archive/`.

**Section order**: H1 "Archives" → flat vertical list of post entries, each: full-width featured photo, colored title link, one-paragraph excerpt ending in "[…]" → pagination ("Next →" visible at bottom) → footer. There is no card-grid layout here — it's a simple stacked post list, confirmed by both the screenshot and the JSON (`cards: []`, no card-grid pattern detected).

**Typography/color distinct from global:** minimal — the only page-specific style is the `h1.entry-title` (Montserrat 22px/700) and the post-link color, which reuses the site's pink-magenta `#e5396e` (also seen elsewhere as an accent, so not unique to this template). No distinct card or button styling beyond the shared header "Book Now" button.

**Responsive behavior:** container is `null` at every viewport (max-width candidates cap at 1140px on `.site-main`, below the 1200px Elementor container used elsewhere — this template uses the theme's default WordPress content width rather than an Elementor page-builder container, worth noting for the theme build). At mobile the post list stays single-column (it already is one at desktop) with images scaling to full width.

This is the shortest write-up of the eight because the page itself is the simplest: one heading style, one repeating text-link pattern, no cards, no distinct color usage.

---

## Blog Single Article

Screenshot folder: `docs/reference-screenshots/guide-getting-sick-on-the-loop/` (the Milestone 1 capture used a real article slug; it stands in for the single-post template generally). Measurement folder: `blog-single/`.

**Section order**: featured image → title/meta bar (date, author) → auto-generated Table of Contents box → body content (H2/H3 section headings, paragraphs, bold lead-ins) repeated for each article section → social share row (Facebook/X/Reddit icons) → author box → "More to Explore" related-posts strip → footer.

**Typography/color distinct from global:** a genuine heading hierarchy exists here that the other templates don't need: H1 22px/700 (Montserrat, title/TOC header), H2 20px/600 (Poppins, major section headings), H3 18px/700 (Montserrat, sub-headings), body paragraph 15px/400 (Inter, `lineHeight: 22px`) with `strong` lead-ins at 15px/700. Distinct colors: post-meta text `#adadad`, related-post title `#e4e0da` (light, sits on a dark card background per the screenshot), share-button brand colors (Facebook `#3b5998`, Reddit `#ff4500`, X/Twitter black).

**Card/button measurements:** `cards: []` — the "More to Explore" related-posts strip is visually card-like in the screenshot but wasn't picked up by the automated card-grid probe (likely a non-standard Elementor post-widget markup); no exact pixel dimensions available, would need direct inspection. No distinct button styling beyond the shared header CTA.

**Responsive behavior:** container `null` at every viewport, same 1200px Elementor-container candidates as other templates. The Table of Contents and share-button row are the elements most likely to reflow at mobile (stacking vertically) based on the very tall, narrow full-page screenshot (`guide-getting-sick-on-the-loop/mobile.png`, ~18,000px tall at 390px width — a long-form article), though exact breakpoint behavior for the TOC box itself isn't independently measurable from the JSON.

---

## Contact

Screenshot folder: `docs/reference-screenshots/contact/`. Measurement folder: `contact/`.

**Section order**: two-column layout — left: info card (address, WhatsApp/phone, email, business hours, each with an icon) with light shadow; right: heading, intro copy, contact form (Name, WhatsApp Number, Email, Message, "Send" button) → footer.

**Typography/color distinct from global:** form field labels use Montserrat 18px/700 (`.elementor-field-label`) — notably larger/bolder than the Motorbike Rental template's form labels (13.6px/500 DM Sans), so form-label styling is not consistent site-wide and a theme build will need to pick one convention rather than copying either literally. Icon-box description text (address/contact details) is Inter 16px/400-500.

**Card/button measurements:** no card grid on this page (`cards: []` — the info panel is a single icon-list block, not a repeating grid). "Send" button: 47px height, radius 7px, orange fill/white text, 20px/600 text, same hard-offset shadow (`rgb(54,52,59) 2px 3px 0px 0px`) used site-wide.

**Responsive behavior:** the two-column layout (info card | form) stacks to a single column at mobile, info card first then form, confirmed via `contact/mobile.png`. Container `null` at every viewport (candidates cap at 1200px, same Elementor-container pattern as other secondary pages).

---

## About

Screenshot folder: `docs/reference-screenshots/about-loop-trails-tours-ha-giang/`. Measurement folder: `about/`.

**Section order**: gradient hero banner (company name, tagline, license badge) → 4-stat row (Happy Travelers / Years of Excellence / 5-Star Reviews / Licensed Tour Guides) → "Our Journey" narrative → Mission/Vision two-column callout → "Our Services" 6-item icon grid → "Spectacular Destinations" tag-chip list (grouped by region) → "Legal Certification & Compliance" dark panel with license document image → "Our Quality Commitment" 6-item icon grid → "Why Travel With Us" bullet list → "Start Your Adventure Today" CTA band → Tripadvisor strip → footer.

**Typography/color distinct from global:** this is the one template where body/heading text runs on **Inter** almost throughout (`h2.section-title` 32px/500, `h4` service titles 17.6-24px, body paragraphs 15-15.2px/400) rather than the Montserrat/DM Sans mix seen on other pages — a real, page-specific typography choice worth flagging for the design-tokens pass rather than an artifact. Stat numbers are large and bold: 40px/700 Inter. Distinct colors: stat-card background `#f7f7f7` with `#e4e0da` border, destination-tag chip border `#dddddd`, legal panel background `#36343b` (dark, matches the header/menu-toggle dark tone used elsewhere), license-badge translucent white overlays (`rgba(255,255,255,0.1-0.3)`) sitting on the gradient hero.

**Card/button measurements:** two distinct grids — stats row (4 items, 266×116px desktop, radius 15px, `gapX: 25`, no shadow) and services grid (6 items, 363×238px desktop, radius 12px, `gapX: 25`, no shadow). "Book Your Adventure Now" CTA button: 52px height, radius 30px (pill), orange fill/white text, no shadow, 15px/400 text — a rounder, flatter style than the hard-shadow buttons used on the tour/booking-heavy pages.

**Responsive behavior:** this is one of the only secondary-page templates where `mostCommonContainerWidth` resolves to a real value (1200px, `candidateCount: 4`) at desktop — matching Home and Tours Archive rather than the `null` pattern seen on most other secondary pages. At mobile (`about-loop-trails-tours-ha-giang/mobile.png`) the stat row, services grid, and destination tags all reflow to narrower multi-item rows/stacks; exact column count at mobile isn't reliably inferable from the screenshot's resolution alone, but the underlying items themselves (`itemWidth` 330px range in the mobile JSON) indicate they're no longer laid out at their desktop widths.

---

## Terms & Privacy

Screenshot folder: `docs/reference-screenshots/terms-and-conditions/`. Measurement folder: `terms/`.

**Section order**: this is a single long-form legal document, not a composed page — eyebrow label, H1 "Terms & Conditions and Privacy Policy", "Last updated" date line, then ~15 numbered major sections (Prices, Deposit & Full Payment, Booking Confirmation, Cancellation Policy [with a fee table by days-before-departure], Force Majeure, Customer Responsibilities, Motorbike Damage/Loss & Liability, Medical Emergencies & Health, Travel Insurance, Travel Documents & Visa, Photography & Marketing Consent, Liability & Disclaimer, Claims & Disputes, Governing Law, Privacy Policy with its own lettered sub-sections A-J) → "Questions about our Terms or Privacy?" contact callout → "Back to top" link → footer.

**Typography/color distinct from global:** dedicated legal-doc type scale not used anywhere else: `h2.lt-legal__major` (Poppins 17px/600, section titles), `span.lt-num` (Montserrat 17px/700, section numbers), `h3` sub-headings (Montserrat 12px/700, 1.4px letter-spacing, uppercase-style), body copy `p.lt-legal__updated` (Inter 14.5px/400, 24.65px line-height — noticeably more generous leading than the 22px body line-height used elsewhere, appropriate for long-form reading). Cancellation-fee callouts use a warning red `#d63031` and a light gray callout background `#f7f7f7`.

**Card/button measurements:** no card grids and no page-specific buttons — `cards: []`, the only button is the shared header "Book Now". This matches the plan's spot-check finding that `terms/tablet.json` is "sparse but legitimate": at tablet (768px) the container-detection probe found zero candidates (`candidateCount: 0`), consistent with a plain single-column text template that doesn't use the site's usual Elementor-container markup at that breakpoint.

**Responsive behavior:** container `null`/absent at every viewport — this template is effectively a full-width text column with side padding at all sizes, no boxed max-width layout to speak of. Given the page is prose-only, this short write-up is intentionally proportional — a legal page needs far less design documentation than a card-grid archive page.

---

## 404

Screenshot folder: `docs/reference-screenshots/this-page-does-not-exist-xyz123/`. Measurement folder: `404/`.

**Section order**: two-column hero split — left: full-bleed photo (motorbike on a mountain road); right: "404" eyebrow, large H2 "Oops! It seems like you got lost…", one-line body copy with a "Home Page" link, search box with "GO" button → footer (no other sections).

**Typography/color distinct from global:** this template breaks from the Montserrat/Inter/DM Sans mix used everywhere else and runs on **Rubik** for its own content (`h2` 55px/400, body 16px/400, "GO" button 15px/400) — a one-off font choice specific to this error page. The "404" eyebrow label uses an accent pink-red `#f40045`, distinct from the site's usual orange/pink accents.

**Card/button measurements:** no cards. "GO" search-submit button: 48px height, square corners (radius 0px), black fill/white text, same hard-offset shadow as other buttons (`rgb(54,52,59) 2px 3px 0px 0px`) — the one place on the site where a primary action button uses black instead of orange. Search input border `#dddddd`/`#e2e2e2`.

**Responsive behavior:** the two-column hero (photo | text+search) stacks to photo-on-top, text+search below at mobile (`this-page-does-not-exist-xyz123/mobile.png`). Container `null` at every viewport with only a single 1200px candidate detected even at desktop — the simplest container profile of any of the eight templates, consistent with this being the shortest/simplest page.

---

## Page Types With No Reference Data

Per spec §5 items 5 and 12 ("Blog archive, category/tag archive, search and single article" / "Generic search results, empty states and 404"), two of the listed page types were never captured during Milestone 1's screenshot pass or Milestone 2a's measurement pass:

- **Blog category/tag archive** — no reference screenshot or measurement JSON exists for a filtered archive view (e.g. `/category/health/` or `/tag/motorbike/`); only the unfiltered `/blog/` archive (documented above) and a single article were captured live.
- **On-site search results** — no reference screenshot or measurement JSON exists for a populated search-results page; the 404 template's search box (documented above) is the only search-related UI observed, and it was captured empty/unused.

Neither was observable on the live reference site during the audit window (Milestone 2a's plan noted this gap explicitly). Both will be built per spec §5 items 5 and 12 using standard WordPress archive/search template conventions (`archive.php`/category-tag templates and `search.php`) rather than a measured reference — in practice this means reusing the Blog Archive template's list layout and typography documented above (same post-list pattern, same typography scale) for category/tag archives, and a similar list layout with a "no results" empty state for search results, rather than inventing new unreferenced designs.
