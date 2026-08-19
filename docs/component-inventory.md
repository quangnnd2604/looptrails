# Component Inventory

Reusable UI components identified from Milestone 2a's computed-CSS measurements (`docs/design-measurements/`) and Milestone 1's reference screenshots (`docs/reference-screenshots/`), for the WordPress theme/plugin rebuild.

Naming and prose below are original — no reference-site copy is quoted. Pixel sizes, colors and counts are measured values from the JSON probes (`cards`, `buttons`, `typography`, `colors`, `container`). Reference-site CSS class names (e.g. `lt-tours__grid`) are cited only as pointers back to the measurement data, not as content.

Template slugs referenced below match `docs/design-measurements/<slug>/`: `home`, `tours-archive`, `tour-detail`, `motorbike-rental`, `about`, `contact`, `blog-archive`, `blog-single`, `terms`, `404`.

---

## Site Container / Grid Width

**Appears on:** home, tours-archive, tour-detail, about
**Measured dimensions:** Common max content width 1200px at both `desktop` (1440px viewport) and `laptop` (1280px viewport) — `container.mostCommonContainerWidth` is 1200 on all four templates at those two breakpoints. At `tablet` (768px viewport) the Home template's container narrows to 671px. Below 768px width, pages go edge-to-edge (no fixed container recorded).
**Variants observed:** Fixed 1200px container down to laptop width; fluid/full-bleed under tablet. `motorbike-rental`, `contact`, `blog-archive`, `blog-single`, `terms` and `404` did not resolve a dominant container in the automated probe (page-specific layouts), but screenshots show the same visual gutter/margin rhythm.
**Spec cross-reference:** §5 general layout baseline underlying every page type (1–13).

---

## Primary Navigation (Header)

**Appears on:** all 10 templates (present in every reference screenshot's header band)
**Measured dimensions:** Header background measured on Home as `rgba(228, 224, 218, 0.85)` (semi-transparent warm cream, i.e. a sticky/blurred header treatment) with a mobile menu-toggle element (`elementor-menu-toggle`) present in the color probe on Home and captured again distinctly on the 404 and blog templates. No distinct font-size/spacing measurements for the nav links themselves survived the frequency-ranked typography cut (nav link count is low relative to body copy, so it fell outside the top-captured styles).
**Variants observed:** Screenshots (`docs/reference-screenshots/home/desktop.png`, `.../mobile.png`) show a left-aligned logo, horizontal text links plus a "Book Now" pill button on desktop (≥768px), collapsing to a logo + hamburger icon at mobile widths (390/360px) with no text links visible in the collapsed bar.
**Spec cross-reference:** §5.1 Global header/navigation. **Gap:** exact nav-link font size/spacing, the Tours dropdown's open/hover state, the language/currency control (EN/USD vs VI/VND), and sticky-scroll transition behavior were **not observed in captured data** — no matching typography/color entries or dedicated screenshots exist for these states. Will need direct inspection of the live site (hover/scroll interaction) before implementation.

---

## Footer Columns

**Appears on:** all 10 templates (present at the bottom of every full-page screenshot)
**Measured dimensions:** Footer background matches the header family: `rgb(228, 224, 218)` opaque (measured as `elementor-alert`/general background swatch on Home) and `rgba(228, 224, 218, 0.85)` for the header variant — a single warm-cream brand surface reused top and bottom. Footer legal/copyright text measured at `"Open Sans", sans-serif`, 12px, weight 400, letter-spacing 0.5px, line-height 22px.
**Variants observed:** Screenshots show a multi-column layout (brand/logo block, contact details, social icons) collapsing to a stacked single column on mobile widths, consistent across Home, About, Motorbike Rental and Tour Detail screenshots.
**Spec cross-reference:** §5.5 Footer. **Gap:** individual column headings, the legal-links list, payment-icon row and newsletter block were not distinctly captured by the typography/color probes (low element counts); confirmed only visually in screenshots. Direct inspection needed for per-column measurements.

---

## Tour Card

**Appears on:** home (`lt-tours__grid`, measured); tours-archive and tour-detail (same card visually confirmed in screenshots; the archive's own grid geometry did not resolve in the automated probe — see gap note)
**Measured dimensions:** Desktop/laptop: 336×649px, border-radius 14px, gap 22px, image aspect ratio 1.344, 6 items/grid. Tablet: 325×619px, radius 14px, gap 22px. Mobile (390px): 330×623px, single column (negative gapX indicates stacked wrap). Narrow-mobile (360px): 300×622px.
**Variants observed:** 3-column arrangement at desktop/laptop (per screenshot `home/desktop.png`), collapsing to 1 column at mobile/narrow-mobile widths — confirmed by both the `narrow-mobile.json`/`mobile.json` card entries and the screenshots.
**Spec cross-reference:** §5.3 Tour cards; §5.2 "featured tour grid/cards with badge, rating display, taxonomy chips, multi-option prices, Book Now and Details". **Gap:** Tours Archive's own listing-grid geometry was not captured by the `scanCardGrids` probe on any viewport of `tours-archive/*.json` (only a `hgl-grid hgl-grid-2` filter/bus-option grid was found there) — the archive page visually reuses the same Tour Card component per its screenshot, but this should be confirmed by direct inspection since the probe produced no independent measurement for it.

---

## Badge / Chip

**Appears on:** home (`lt-pill`, `lt-tour__badge`), blog templates (`elementor-post__badge`)
**Measured dimensions:** `lt-pill`: Montserrat 10.5px, weight 700, letter-spacing 0.8px, line-height 16.275px — highest-frequency text style on Home (60 occurrences), consistent with a small repeating tag/chip used across many cards. `lt-tour__badge`: Montserrat 10.5px, weight 700, letter-spacing 1.5px; background `rgb(54, 52, 59)` (`#36343b`, the site's dark ink/shadow color) for the "new"-style variant. Blog post badge (`elementor-post__badge`): Playfair, 12px, weight 600, line-height 27px, background `rgb(51, 51, 51)`.
**Variants observed:** Same chip typography recurs at every measured viewport on Home (desktop through narrow-mobile), confirming it isn't a desktop-only decoration.
**Spec cross-reference:** §5.3 "badge label/type and visibility"; §5.2 "taxonomy chips".

---

## Price Row

**Appears on:** home (`lt-price-row__*`, `lt-tour__prices-label`); tour-detail-variation samples (2d1n/4d3n/6d5n) confirm row-count behavior
**Measured dimensions:** Label: Inter 12.5px, weight 500. Value: Inter 14px, weight 700, letter-spacing -0.1px. Secondary/USD suffix: Inter 10.5px, weight 500. "Prices from" eyebrow label: Montserrat 10px, weight 700, letter-spacing 1.4px.
**Variants observed:** `priceLikeElementCount` is fixed at **4** across all three tour-detail-variation samples (2 Days 1 Night, 4 Days 3 Nights, Cao Bang 6 Days 5 Nights) regardless of itinerary length — confirming the price-row block is a fixed-structure repeating component (e.g. per-vehicle-option pricing) independent of tour duration.
**Spec cross-reference:** §5.3 "price rows by ride/vehicle option and party-size tier"; §5.4 "price variants".

---

## Motorbike Rental Card

**Appears on:** motorbike-rental (`ltr-bikes`)
**Measured dimensions:** Desktop/laptop/tablet: 310×319px, border-radius 12px, box-shadow `rgba(255, 102, 2, 0.15) 0px 0px 0px 3px` (a soft orange focus-ring rather than a drop shadow), gap 12px, image aspect ratio 1.365, 4 items/grid. Mobile: 314×322px. Narrow-mobile: 284×300px.
**Variants observed:** Consistent radius/shadow/aspect ratio across every measured viewport — only card width/height scale down. Screenshot (`motorbike-rental/desktop.png`) confirms a 2-column desktop grid with an image, category badge ("popular"/"budget"/etc. styling), spec list, starting-from price and a "Book Now" button per card.
**Spec cross-reference:** §5 item 4 "Motorbike Rental landing/detail content".

---

## Blog Post Card

**Appears on:** home (`elementor-posts-container elementor-posts--skin-cards`), tour-detail (`elementor-posts-container elementor-posts--skin-classic`, related-posts widget)
**Measured dimensions:** Home: 373×472px, radius 0px, gap 30px, image aspect ratio 1.5, 3 items/row. Tour Detail (laptop/tablet related-posts row): 373×464px / 359×441px, same aspect ratio 1.5, gap 30px.
**Variants observed:** Same 3-card row geometry (aspect ratio 1.5, gap 30px) reused between Home's blog teaser section and Tour Detail's "related" content, despite different Elementor "skin" class names (`skin-cards` vs `skin-classic`) — one underlying card component styled two ways.
**Spec cross-reference:** §5 item 5 "Blog archive, category/tag archive, search and single article". **Gap:** `blog-archive` and `blog-single` templates' own probes returned no `cards` entries (post-list geometry on the archive itself wasn't isolated by the automated scan); the geometry above is inferred from the Home and Tour Detail widgets reusing the same markup pattern, not from the archive template directly.

---

## Icon Feature / Service Card

**Appears on:** home (`lt-feats`), about (`services-grid`, `commitment-grid`)
**Measured dimensions:** Home `lt-feats`: 336×338px, radius 14px, gap 22px, 6 items, image aspect ratio 1 (square icon). About `services-grid`: 363×238px, radius 12px, gap 25px, 6 items (desktop/laptop). About `commitment-grid`: 344×239px, radius 10px, gap 20px, 6 items (tablet); 330×233px at mobile.
**Variants observed:** Three distinct grid instances share the same structural pattern (icon + eyebrow/title + short description, in a 6-item grid with 10–14px corner radius) but are styled per-page rather than sharing one CSS class — a candidate for consolidating into one theme component with per-section radius/spacing variants.
**Spec cross-reference:** §5.2 "Why choose us feature grid and statistics"; §5 item 7 "About".

---

## Stat Counter Block

**Appears on:** home (`lt-stats`), about (`stats-container`)
**Measured dimensions:** Home: 244×81px (desktop), radius 0px, 4 items, dark-band background (measured page color `rgb(21,21,21)`-family dark section per screenshot). Value typography: Montserrat 40px, weight 800, letter-spacing -0.5px. Label typography: Montserrat 11px, weight 700, letter-spacing 1.8px. About: 266×116px (desktop), radius 15px, gap 25px, 4 items.
**Variants observed:** Home's stat block is a flush 4-up row (no radius, dark full-bleed band per screenshot); About's is 4 individually-rounded cards with 25px gaps. Both are the same semantic "big number + label" pattern with different container treatment — noted as one component with two style variants rather than two components, since dimensions genuinely differ (radius 0 vs 15px) and per the grouping rule these would normally stay separate, but the repeating 4-stat structure and typography role are identical.
**Spec cross-reference:** §5.2 "featured statistics"; §5.2 "Why choose us feature grid and statistics".

---

## Destination Grid Card

**Appears on:** home (`lt-grid`, "Top destinations" section)
**Measured dimensions:** Tablet: 327×337px, radius 14px, gap 18px, 8 items, image aspect ratio 1.333. Narrow-mobile: 300×318px, radius 14px, 8 items, aspect ratio 1.333.
**Variants observed:** 8-item grid captured only at tablet and narrow-mobile viewports; desktop/laptop/mobile(390) did not resolve this grid in the automated probe, but `home/desktop.png` clearly shows the same 8 destination tiles in a 4-column arrangement under the "Top destinations & everything you'll need" heading, with filter-style pill tabs above it. Treat desktop geometry as visually-confirmed-but-not-machine-measured.
**Spec cross-reference:** §5.2 "destination cards and metadata chips"; tabbed area's "Destinations" tab.

---

## Testimonial / Review Card

**Appears on:** home (`lt-slider__track`, horizontal carousel), tour-detail (`lt-reviews-list`, stacked list)
**Measured dimensions:** Home carousel track: 336×397px per slide, gap 22px, 9 items, radius 0 at the track level (inner card radius not separately isolated by the probe). Tour Detail list: radius 12px consistently across all viewports — laptop 807×151px, tablet 443×200px, mobile 336×249px, narrow-mobile 306×295px (3 items/page). Typography: review title (Poppins 16px/600), body (Inter 13.5px/400), author name (Inter 13px/600), meta line (Inter 11.5px/400), date stamp (Montserrat 10.5px/700, letter-spacing 1.2px), initial-letter avatar (Montserrat 13px/800).
**Variants observed:** Horizontal auto-scrolling carousel on Home vs a vertically-stacked list on Tour Detail — same card content model (avatar-initial, name, meta, star rating, date, body, source link) in two layout containers.
**Spec cross-reference:** §5.2 "review/testimonial presentation using original demo reviews only"; §5.4 (implicit reviews section, confirmed via screenshot's "Reviews" tab).

---

## Itinerary Day Panel

**Appears on:** tour-detail (`lt-day-images`, `lt-timeline`), tour-detail-variation samples
**Measured dimensions:** Day-image row: desktop 213×160px (radius 10px, gap 12px, aspect 1.338, 3 images/day); laptop 247×185px; tablet 194×146px; mobile 294×221px; narrow-mobile 264×198px — radius 10px and 3-image grouping held at every viewport. Mobile/tablet also show a `lt-timeline` list (itemHeight 70px, radius 0) — a condensed activity-by-time view nested inside each day panel.
**Variants observed:** `itineraryDayCount` scales directly with tour length across the 3 tour-detail-variation samples: 3 days (2D1N), 5 days (4D3N), 7 days (Cao Bang 6D5N) — each including a "day 0" arrival entry. Total visible images scale similarly (10 / 16 / 19).
**Spec cross-reference:** §5.4 "itinerary with Day 0 and unlimited following days"; §5.2 "itinerary duration tabs and day-by-day panels".

---

## Included / Excluded List

**Appears on:** tour-detail (`lt-included-list`)
**Measured dimensions:** itemHeight 70px consistently across desktop (291px wide), laptop (342px), mobile (286px) and narrow-mobile (256px); radius 0 (plain list, not a card grid).
**Variants observed:** Two-column layout confirmed by screenshot (`tour-detail/desktop.png`, "What's Included" section) — an included list and an excluded list side by side at desktop width, stacking on mobile.
**Spec cross-reference:** §5.2 "included and excluded lists"; §5.4 "included/excluded lists".

---

## Transport / Bus Option Card

**Appears on:** home, tours-archive (`hgl-grid hgl-grid-2`). **Not evidenced on tour-detail** — verified by grep: none of the 5 `docs/design-measurements/tour-detail/*.json` files contain the string `hgl` or the size `397` anywhere, and the `tour-detail/desktop.png` screenshot's booking sidebar shows only vehicle-type/experience-type toggles, dates, travelers and total — no bus-option grid is visible there either. Treated as a 2-template component, not 3.
**Measured dimensions:** Desktop: 397×68px, radius 0, gap 16px, 4 items — this exact geometry (397×68, gap 16) is identical across home and tours-archive at the desktop breakpoint, a strong cross-template match per the grouping rule. Tablet: 311–397×66-70px, gap 16px. Mobile/narrow-mobile: single-column stack (negative gapX).
**Variants observed:** Associated typography (`hgl-bus-col-title`, DM Sans 14.4px/600) shows short label + price-style content, consistent with pickup/return bus transfer options in the booking widget.
**Spec cross-reference:** §5.2 "transport option cards"; §5.4 "bus transfers and add-ons" — note this means tour-detail's own bus-transfer/add-on requirement (§5.4) currently has **no measured or visual evidence**, a second gap beyond what the original self-check flagged; will need direct inspection of the tour-detail booking sidebar's full interaction states (it may only appear after a vehicle-type selection, which static capture wouldn't show).

---

## Vehicle & Ride Type Selector Card

**Appears on:** home, motorbike-rental (`hgl-radio-card-content`, inside the booking widget)
**Measured dimensions:** Not captured by the card-grid probe (likely wrapped in a `<label>`, below the probe's detection threshold), but colors are measured: unselected/base background `rgb(255, 245, 240)` (`#fff5f0`, light peach), selected border/accent `rgb(255, 102, 2)` (`#ff6602`, brand orange), content typography DM Sans 14.4px, weight 600 (selected label) / 500 (unselected label).
**Variants observed:** Screenshot (`motorbike-rental/desktop.png`) shows a 2×2 selectable bike/vehicle-option grid with an orange-ringed selected state, matching the measured color pair.
**Spec cross-reference:** §5.4 "vehicle/ride options"; §5.2 tabbed area's "Transport" tab; §5.9 (booking/checkout interface).

---

## Accommodation Card

**Appears on:** not observed in captured data
**Measured dimensions:** No accommodation-specific typography style, class name, or card-grid geometry appears anywhere in `docs/design-measurements/` (checked across all 53 files) — the word "accommodation" surfaces only once, incidentally, inside a generic itinerary-note text sample on tour-detail (`lt-note` style, sample text mentioning nights of accommodation at homestays/guesthouses); `lt-note` is a plain note/callout box (light-blue background, DM Sans 14.4px), not a distinct Accommodation Card component, and carries no card-grid geometry of its own. No dedicated "Accommodation" tab content was captured in any screenshot (screenshots record only the default-active tab state of the Home tabbed section, and no other template surfaces this component).
**Variants observed:** N/A — will need direct inspection.
**Spec cross-reference:** §5.2 "accommodation gallery/cards"; §5.4 "accommodation options and upgrades". **This is a spec-required component with no supporting measurement evidence** — flagged per Step 3 rather than fabricated. Recommend a follow-up capture pass that explicitly interacts with the Home page's "Accommodation" tab and any tour-detail accommodation section before design work begins.

---

## Tab Control

**Appears on:** home (`lt-tab-btn`, "Top destinations & everything you'll need" section)
**Measured dimensions:** Desktop: height 38px, padding-top 11px, padding-right 20px, border-radius 10px, font Montserrat 12px/700, letter-spacing 1px. Active state: background `rgb(255, 102, 2)` / white text. Inactive state: background white / text `rgb(54, 52, 59)`. Both states share box-shadow `rgb(54, 52, 59) 4px 4px 0px 0px` (a hard offset "sticker" shadow, not a blur).
**Variants observed:** Screenshot confirms 4 tabs matching spec §5.2's named set (Destinations / Itinerary & Map / Transport / Accommodation); typography sampling directly captured button text for "Destinations" and "Itinerary & Map" at this exact style.
**Spec cross-reference:** §5.2 "tabbed 'Top destinations & everything you'll need' area: Destinations, Itinerary & Map, Transport and Accommodation".

---

## FAQ Accordion

**Appears on:** home (`lt-faq__list`, mobile-only capture), tour-detail (`lt-faq-list`), tours-archive (`elementor-accordion`)
**Measured dimensions:** Home (mobile 390px): itemHeight 74px, radius 0 (plain list style), 9 items. Tour Detail (laptop): itemHeight 76px, radius 14px, box-shadow `rgba(15, 23, 42, 0.04) 0px 2px 8px 0px, rgba(15, 23, 42, 0.06) 0px 1px 3px 0px` (soft card shadow), 7 items; tablet: 5 items at 68px height, same radius/shadow. Tours Archive (narrow-mobile): itemWidth 340×72px, radius 0, 6 items, using Elementor's native accordion widget rather than the custom `lt-faq` markup. Question typography: Poppins 15.5px, weight 600 (`<summary>` tag, native disclosure element).
**Variants observed:** Two distinct visual treatments exist for functionally the same FAQ pattern — a flat list style (Home, Elementor-native accordion on Tours Archive) and a soft-shadowed card style (Tour Detail) — plus a category eyebrow label (`lt-faq__group-label`, Montserrat 11px/700, letter-spacing 1.6px) grouping questions on Home.
**Spec cross-reference:** §5.4 "FAQs" (explicit); also underlies the FAQ content visible in Home's screenshot.

---

## Primary CTA Button

**Appears on:** home, tours-archive, tour-detail, motorbike-rental, contact (present in every template's `buttons` probe with this signature)
**Measured dimensions:** Solid fill background `rgb(255, 102, 2)` (`#ff6602`), white text, weight 700, hard offset box-shadow `rgb(54, 52, 59) …px …px 0px 0px` (no blur — a "sticker"/neo-brutalist shadow style used throughout the site). Border-radius varies by placement/size: 7px (hero-scale buttons, e.g. 45–59px tall), 8px (medium, ~50px tall), 10px (tab-active state, ~38px tall). Hover state is measured as identical to rest state on every sampled instance (no color/shadow shift on hover in the captured data).
**Variants observed:** Same fill/shadow/weight formula reused at 5+ different heights across 5 templates — one component with a small size scale (radius scales roughly with button height) rather than several unrelated buttons.
**Spec cross-reference:** §5.1 "prominent Book Now control"; §5.3 "Book Now deep-link"; §5.4 "price variants and booking CTA/sticky CTA".

---

## Secondary / Outline Button

**Appears on:** home, tours-archive, tour-detail, motorbike-rental, about, blog-archive, blog-single, terms, 404 (present in nearly every template's `buttons` probe)
**Measured dimensions:** White/light fill, colored text (either `rgb(255, 102, 2)` orange or `rgb(54, 52, 59)` dark ink depending on context), same hard-offset shadow formula as the Primary CTA (`rgb(54, 52, 59) 2px 3px 0px 0px` typical), radius 7–8px, weight 700. A light-peach variant also appears on Tour Detail: background `rgb(255, 247, 237)` with orange text, radius 8px, no shadow (a quieter "chip button" used for secondary actions like "Details").
**Variants observed:** This exact style (35px height, radius 7px, `#ff6602` text on white, matching shadow) is the single most-repeated button signature in the dataset — it appears in every template's desktop, laptop, mobile, narrow-mobile and tablet capture, making it the site's default small/secondary action button (used for "Book Now"/"Details" pairs on Tour Cards).
**Spec cross-reference:** §5.3 "Details link"; general secondary-action pattern across §5.2–§5.4.

---

## Text Arrow Link

**Appears on:** home (`lt-review__link`, `lt-intro__btn`), blog-single, blog-archive, 404
**Measured dimensions:** No background (`rgba(255, 255, 255, 0)`), no box-shadow, no border-radius — plain text link with an arrow glyph. Two color variants measured: dark ink `rgb(54, 52, 59)` (used for review "read more" links, Montserrat 10.5px/700, letter-spacing 1px) and pink accent `rgb(229, 57, 110)` (`#e5396e`, used for blog "read more" links, appears identically on Home, Blog Single and the 404 template's related-content area).
**Variants observed:** The pink-accent variant is confirmed on 3 separate templates with the exact same color/weight/no-shadow signature, making it a distinct reusable "blog context" link style separate from the dark-ink "general context" variant.
**Spec cross-reference:** Supports §5.3 "Details link" and general in-content navigation; no single spec subsection names it directly.

---

## Booking Widget Support Cards (bonus — evidenced but not spec-named individually)

**Appears on:** home (`hgl-payment-card-title`/`hgl-payment-card-desc`)
**Measured dimensions:** Title: DM Sans 15.2px, weight 600–700 depending on state, line-height 20–24.32px. Description: DM Sans 12.8px, weight 400–700, line-height 20px. Deposit-vs-full-payment and card-vs-bank payment method options are each rendered as a small titled card within the booking form.
**Variants observed:** Two payment-type cards (partial deposit / pay in full) and multiple payment-method cards (international card, Vietnamese bank) share this title+description typography pair.
**Spec cross-reference:** §5.9 "Booking/checkout interface"; supports §5.4 "price variants and booking CTA".

---

## Self-Check Against Spec §5

Walking every named UI element in §5.1–§5.5:

- **§5.1** Logo — visually confirmed in screenshots, not separately measured (gap noted under Primary Navigation). Primary nav + Tours dropdown — gap noted. Book Now control — covered by Primary CTA Button. Language/currency control — **not observed in captured data** (no matching class names for lang/currency toggles anywhere in the dataset); will need direct inspection. Sticky header — background color measured (semi-transparent cream), but the scroll-triggered transition itself is behavioral and not visible in static JSON/PNG capture — noted as a gap. Mobile hamburger/drawer — `elementor-menu-toggle` confirmed present; drawer's open-state contents not captured (gap). Keyboard-operable dropdown/drawer, visible focus and correct ARIA state — **not observed in captured data**: keyboard/focus/ARIA behavior is not measurable from static screenshots or computed-style JSON (no tab-order, `:focus`, or `aria-*` data exists in either capture format) — requires direct interaction testing against the live site, not inferable from this dataset.
- **§5.2** Hero — covered implicitly via button/typography measurements (h1 60px Montserrat 800, hero CTA button); not given its own section since it's a one-off per template, not a repeating component. Featured tour grid — Tour Card. Narrative/brand section with stats — Icon Feature Card + Stat Counter Block. Tabbed area (Destinations/Itinerary & Map/Transport/Accommodation) — Tab Control, with Destination Grid Card, Itinerary Day Panel, Transport/Bus Option Card covering three of the four tabs; **Accommodation tab content is the one explicit gap**, flagged above. "Why choose us" feature grid + statistics — Icon Feature Card / Stat Counter Block. Reviews — Testimonial/Review Card. Editorial CTA section — covered by Primary CTA Button pattern; no distinct card component evidenced beyond the button itself. Full booking interface — Vehicle & Ride Type Selector Card + Transport/Bus Option Card + Booking Widget Support Cards. Footer — Footer Columns.
- **§5.3** Tour card fields (badge, rating, chips, price rows, Book Now/Details) — all individually evidenced under Tour Card, Badge/Chip, Price Row, Primary/Secondary Button.
- **§5.4** Hero/gallery — not separately measured (one-off per tour, evidenced only via day-image aspect ratios). Price variants/sticky CTA — Price Row + Primary CTA. Destination highlights — Destination Grid Card pattern likely reused (not directly confirmed on tour-detail; gap). Itinerary Day 0+ — Itinerary Day Panel. Route map — visually confirmed in screenshot as a static image; no measurement data (gap, low risk — likely a plain `<img>`). Included/excluded — Included/Excluded List. Vehicle/ride options — Vehicle & Ride Type Selector Card. Accommodation options — **gap**, flagged above. Bus transfers/add-ons — Transport/Bus Option Card. Safety/requirements content — visually present (motorbike-rental screenshot's requirements section) but not separately measured; likely a plain text/heading block, not a distinct component (low risk). FAQs — FAQ Accordion. Related tours — Blog Post Card pattern's sibling "related content" grid appears to reuse Tour Card, not confirmed directly (gap, low risk given Tour Card's established reuse elsewhere). Final CTA — Primary CTA Button.
- **§5.5** Footer structure — Footer Columns, with the internal breakdown (nav columns, contact block, social icons, legal links, payment icons, newsletter) only visually confirmed, not individually measured — noted as a gap under Footer Columns.

**Summary of gaps requiring direct inspection before implementation:** Accommodation Card/gallery (no evidence at all), header nav-link typography and dropdown/drawer states, language/currency control, footer's internal column structure, tour-detail's destination-highlights and related-tours sections, and the route-map treatment.
