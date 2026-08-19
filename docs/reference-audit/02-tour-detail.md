# Reference Audit — Tour Detail Template

## Scope and evidence base

The reference site does not have three different tour-detail *layouts* — it has one
template rendered with three different content payloads. This is confirmed by:

- Identical section order and identical component class names (`lt-*`, `ltw-*`
  prefixes) across all three captured screenshot sets:
  `docs/reference-screenshots/tours-ha-giang-loop-2-days-1-night/`,
  `docs/reference-screenshots/tours-ha-giang-loop-4-days-3-nights/`,
  `docs/reference-screenshots/tours-ha-giang-cao-bang-6-days/`.
- The three `docs/design-measurements/tour-detail-variation/*.json` files exist
  specifically to isolate *content-count* variation (day count, price count, image
  count) from a single shared structural probe — there is no separate `container`,
  `typography`, or `cards` probe per tour, because those are identical across tours.

Primary structural/styling evidence: `docs/design-measurements/tour-detail/*.json`
(5 viewport files: `desktop.json` 1440px, `laptop.json` 1280px, `tablet.json` 768px,
`mobile.json` 390px, `narrow-mobile.json` 360px), captured against the 4 Days 3
Nights tour. Visual cross-reference: the 5 screenshots in
`docs/reference-screenshots/tours-ha-giang-loop-4-days-3-nights/`, plus the desktop
screenshots of the other two tours for template-identity confirmation.

All values below are measured numbers/hex codes from these JSON files, or my own
description of visual structure. No reference-site prose is quoted.

---

## 1. Fixed template structure (spec §5.4 field list)

### Hero, title, badges, rating presentation

Full-bleed hero background image (mountain/motorbike photography) with a
left-aligned overline label, a large title, a short subtitle line, and a primary
"Book Now" button, all visible in
`docs/reference-screenshots/tours-ha-giang-loop-4-days-3-nights/desktop.png`.

- Title heading: `h2.elementor-heading-title` — **65px / weight 900 / line-height
  78px** (source: `tour-detail/desktop.json` typography, count 1).
- Overline/eyebrow label: `h5.elementor-heading-title` — **12px / weight 600**,
  color `rgb(255,255,255)` / `#ffffff` (source: `desktop.json` typography +
  colors, count 47 for the white-text rule shared with several hero/header
  elements).
- A floating WhatsApp contact bubble sits over the hero on all viewports (visible
  in all 5 screenshots).
- Rating presentation (in the content body, not the hero): a large numeric score
  `div.lt-rating-number` — **44.8px / weight 700** — paired with
  `div.lt-rating-stars` — **16px**, star glyph color `rgb(251,191,36)` /
  `#fbbf24` (amber) (source: `desktop.json` typography + colors).
- Badges: small pill labels such as `div.ltw-tour-badge` (**12px / weight 600**)
  on the booking widget, and availability-legend dots (`span.ltw-avail-legend-dot`)
  colored amber `#f59e0b`, red `#ef4444`, and grey `#9ca3af` for calendar-day
  status (source: `desktop.json` colors).

### Price variants and booking CTA

A booking widget occupies the right column on desktop/laptop/tablet and moves
below the main content on mobile/narrow-mobile (confirmed by comparing
`desktop.png` two-column layout against `mobile.png`, where the widget reappears
near the bottom under a "Book This Tour" heading, right before the related-posts
block).

Widget contents, by measured class:
- Tour name label: `div.ltw-tour-name` — color `rgb(31,41,55)` / `#1f2937`
  (source: `desktop.json` colors, count 41 — the widget's dominant text color).
- Two selector groups laid out as labeled option cards: a ride-type selector
  (`div.ltw-option-title` **14.4px/600**, `div.ltw-option-subtitle`
  **12px/700**) and an experience-type selector using the same classes — both
  groups share styling, confirming they're the same reusable "option card"
  component reused twice (source: `desktop.json` typography, both count 4).
- A calendar date picker: day cells `div.ltw-cal-day` at **13.6px/500**
  (disabled state) and **13.6px/700** (today/available state), with weekday
  headers at **11.2px/600** (source: `desktop.json` typography).
- A travelers stepper: `+`/`−` buttons (`button.ltw-people-btn`,
  **19.2px/600**) with a numeric count (`span.ltw-people-count`,
  **19.2px/700**) and a `Travelers` label (`span.ltw-people-label`,
  **14.4px/500**).
- Primary submit button `button.ltw-cta` — **16px/600**, and a second, larger
  "Book Now" style CTA button measured at **height 59px, padding-top 15px,
  border-radius 7px, background `rgb(255,102,2)` (`#ff6602`), text
  `rgb(255,255,255)`, hard offset shadow `rgb(54,52,59) 2px 3px 0px 0px`, hover
  background `rgb(228,224,218)`** (source: `desktop.json` buttons array).
- A small header/nav "Book Now" button variant is also present: **height 35px,
  background white, text `#ff6602`**, same offset-shadow style (source:
  `desktop.json` buttons array, entry 1).
- Trust-signal micro-copy row at **12px/400** (samples grouped under a
  className-less `span`, source: `desktop.json` typography, count 3).

The offset drop-shadow (`2px 3px 0px 0px`, no blur, dark navy `#36343b`) recurs
on nearly every button and interactive card in this template — it functions as
a consistent "hard shadow" motif rather than a one-off style.

### Destination highlights / overview

An `Overview` section (h2 `.lt-section-title`, **22.4px/700**, shared heading
style for all section titles on this template) followed by body paragraphs at
**16.8px/400, line-height 30.24px** with a bold inline emphasis variant at the
same size/700 weight (source: `desktop.json` typography). No distinct
"destination highlight" chip/card grid was detected by the card-grid probe on
this template — highlights appear to be delivered as prose within Overview
rather than as a separate structured list.

### Itinerary (Day 0 + following days)

Each day is a self-contained panel:
- Day badge: `span.lt-day-badge.pre-tour` — **12.8px/700**, samples confirm
  "Day 0" is a real rendered label distinct from "Day 1"/"Day 2" (source:
  `desktop.json` typography, count 5 — the primary 4D3N tour has exactly 5 day
  panels).
- Day title: `h3.lt-day-title` — **17.6px/700**.
- Day description paragraph: `p.lt-day-desc` — **15.2px/400, line-height
  25.84px**, color `rgb(100,116,139)` / `#64748b` (source: `desktop.json`
  colors, count 33).
- A timeline of timestamped activity rows inside each day: time label
  `div.lt-time` — **14.4px/700** (24 occurrences across the page — roughly 4-5
  per day), each paired with a sub-heading `h4` at **15.2px/600** and a
  description `p` at **14.4px/400**, plus a meal-indicator badge
  `span.lt-meal` — **11.2px/700**, background `rgb(16,185,129)` / `#10b981`
  (green — likely B/L/D meal chips).
- A 3-image row per day: `div.lt-day-images` card grid, **3 items, 213×160px,
  border-radius 10px, gap 12px, aspect ratio ≈1.34** at desktop (source:
  `desktop.json` cards). This shrinks responsively: 247×185 at laptop,
  194×146 at tablet, 294×221 at mobile, 264×198 at narrow-mobile (each
  viewport's own `cards` array) — same 3-image grid at every breakpoint, just
  narrower.
- A callout note (`div.lt-note`) below the itinerary — **14.4px/400,
  line-height 24.48px**, background `rgba(59,130,246,0.08)` with border
  `rgba(59,130,246,0.2)` (light blue info-box styling), used for a
  weather/road-conditions type disclaimer (source: `desktop.json` typography
  count 19, colors).

### Route map

A single static illustrated map image with numbered route pins, visible in all
three tour screenshots (different route shapes per tour, same visual treatment:
white card background, rounded corners, thin border). A caption below it:
`p.lt-map-caption` — **13.6px/400** (source: `desktop.json` typography). No
iframe/embed markup was detected by any of the 5 viewport probes — the
measurement data is consistent with a static image asset rather than a live map
embed, though the spec allows either.

### Included / excluded lists

Two-column layout (`Included` / `Not Included`), each an `ul.lt-included-list`
card grid — **3 visible items, 291×70px at desktop** (source: `desktop.json`
cards), reflowing to a single narrower column at mobile (286×70 at 390px,
256×70 at 360px). Section sub-headings `h3` (no class) at **16px/700**
(source: `desktop.json` typography, count 2 — "Included"/"Not Included"
labels). A colored container variant `div.lt-included-box.yes` uses a light
green border `rgba(16,185,129,0.25)` (source: `desktop.json` colors).

### Vehicle/ride options

Not a separate page section — this is delivered as the ride-type selector card
group inside the booking widget (see "Price variants" above:
`div.ltw-option-title` / `div.ltw-option-subtitle`). The subtitle text style is
reused for short qualifier labels (e.g. distinguishing tiers) at
**12px/700, letter-spacing 0.5px**.

### Accommodation options and upgrades

**Gap:** no distinct accommodation-selector UI or accommodation card grid was
found by any probe on this template. Accommodation appears only as a plain
text line inside the Included list (nights of homestay/guesthouse lodging
mentioned as one bullet, not a structured, selectable option). The spec
requires "accommodation options and upgrades" as an editable field — the
reference page gives no visual precedent for how upgrades/tiers would be
presented; this will need original UI design rather than a direct measurement
port.

### Bus transfers and add-ons

**Gap:** same situation as accommodation — bus transfer mentions appear only
as individual bullet lines inside the Included/Not-Included lists (e.g. a
transfer-arrangement note appears as one "Not Included" bullet on the 2D1N
sample), not as a dedicated add-on selector or line-item UI. No distinct
"add-ons" component was detected structurally.

### Safety/requirements content

**Gap:** no dedicated "Safety" or "Requirements" section/heading was found.
The closest analogues are (a) the itinerary callout note (`div.lt-note`,
described above) and (b) FAQ answers that likely cover fitness/age/weather
topics conversationally. Treat this as content that needs its own admin field
even though the reference page folds it into FAQ/notes rather than surfacing
it as a standalone block.

### FAQs

Accordion list: numbered index `span.lt-faq-number` — **12.8px/700**,
background `rgb(243,244,246)` circular chip; question text
`span.lt-faq-q-text` — **16px/600**; answer paragraph
`p.lt-faq-a-content` — **15.2px/400, line-height 26.6px**; item border
`rgb(226,228,233)` / `#e2e4e9`; toggle icon border tint
`rgb(254,215,170)` / `#fed7aa` (source: `desktop.json` typography + colors,
all at count 7 — the primary tour has 7 FAQ items). Row container
`div.lt-faq-item`; expand/collapse control class `lt-faq-toggle`.

### Related tours

Rendered using the same card-grid component as the blog/related-posts widget:
`div.elementor-posts-container` — **3 items, 373×464px at desktop, image
aspect ratio 1.5** (source: `desktop.json` cards), reflowing to 359×441 at
tablet. Card link text style: `a.elementor-post__read-more` —
**20px/600**, title link `a` (no class) — **22px/700** (source: `desktop.json`
typography). This section sits at the very bottom of the page, after
FAQs/Reviews and before the footer, on all three tour screenshots reviewed.

### Final CTA

**No separate "final CTA band" distinct from the booking widget itself was
found.** On desktop/laptop/tablet the booking widget occupies the sticky right
column for the full scroll depth, functioning as a persistent CTA. On
mobile/narrow-mobile, where the widget drops out of the two-column layout, it
re-appears as a "Book This Tour" block near the bottom of the page (visible in
`mobile.png`, positioned after FAQs and before the related-posts/blog row) —
this stacked-position widget is effectively the template's final CTA on small
viewports. There is a secondary small "Book Now" button embedded in the sticky
top nav bar as well (source: `desktop.json` buttons array, entry 1, height
35px).

### Sticky secondary nav (in-page tabs)

Not explicitly in the spec's field list but structurally important: a sticky
nav bar with in-page jump links — `a.lt-nav-link` — **13.6px/600**, active
state background `rgb(255,247,237)` / `#fff7ed`, active text color
`rgb(255,102,2)` / `#ff6602` (source: `desktop.json` typography + colors).
Labels visible in the primary sample: Overview / Itinerary / Route Map /
What's Included / Reviews / FAQs (6 tab links, matching typography count 6 for
`.lt-nav-link.active` and `.lt-section-title`, i.e. one tab per major
section).

---

## 2. Variation across tour lengths (spec §5.4: "Day 0 and unlimited following days")

Real values read directly from the 3 files in
`docs/design-measurements/tour-detail-variation/` (re-measured during
Milestone 2a's fix wave, confirmed current as of this audit):

| Tour | Itinerary Day Count | Price Sample Count | Visible Image Count |
|---|---|---|---|
| Ha Giang Loop 2 Days 1 Night | 3 | 4 | 10 |
| Ha Giang Loop 4 Days 3 Nights (primary) | 5 | 4 | 16 |
| Ha Giang Cao Bang 6 Days 5 Nights | 7 | 4 | 19 |

Source files: `tour-detail-2d1n.json`, `tour-detail-4d3n.json`,
`tour-detail-6d5n.json` (each has `itineraryDayCount`, `priceLikeElementCount`,
`totalVisibleImageCount` fields plus `itineraryDaySamples`/`priceSamples`
arrays).

### Day 0 confirmation

The `itineraryDaySamples` arrays confirm a "Day 0" panel is real, rendered
content, not a probe artifact:

- **2 Days 1 Night** → day samples `["day 1", "day 0", "day 2"]`, count 3.
  A tour named for 2 touring days has 3 rendered day panels: Day 0 (arrival/
  travel day) + Day 1 + Day 2.
- **4 Days 3 Nights** → day samples `["day 0", "day 1", "day 2", "day 3",
  "day 4"]`, count 5. A tour named for 4 touring days has 5 panels: Day 0 +
  Days 1-4.
- **6 Days 5 Nights (Cao Bang)** → day samples (first 5 shown)
  `["day 0", "day 1", "day 2", "day 3", "day 4"]`, count 7. A tour named for
  6 touring days has 7 panels: Day 0 + Days 1-6 (days 5 and 6 exist in the
  page but aren't in the truncated 5-item samples array — the count field is
  authoritative here, not the samples list).

**Pattern confirmed: `itineraryDayCount` = (tour's marketed "N Days") + 1** in
all three samples, i.e. every tour prepends a non-touring "Day 0" arrival/
departure-travel panel ahead of the N numbered touring days. This directly
matches spec §5.4's "itinerary with Day 0 and unlimited following days"
requirement — the reference site already treats Day 0 as a first-class,
separately numbered itinerary entry, not a special case bolted onto Day 1.

### Price sample count

All three tours show exactly 4 `priceLikeElementCount` matches, and in each
case the 4 samples resolve to only 2 distinct amounts, each appearing twice
(e.g. the 4D3N tour: `5.790.000đ`, `11.580.000đ`, `11.580.000đ`,
`5.790.000đ` — a base price and what is very likely a double-occupancy/2-
traveler total, each rendered in two places on the page, such as the hero
price badge and the sidebar widget). This is consistent across all three
tours regardless of day count, meaning "price sample count" measures a fixed
UI pattern (2 price points × 2 render locations), not a per-tour-length
variable — unlike itinerary day count, this number should not be expected to
scale with tour length.

### Visible image count

Image count scales with itinerary length but not by one single clean
multiplier across all three samples:

- 2D1N (10 images): consistent with Day 0 contributing 0 images, then 2
  touring days × 3 images/day (6) + 1 route-map image + 3 related-post
  thumbnails = 10. Confirmed by direct inspection of
  `tours-ha-giang-loop-2-days-1-night/desktop.png` (Day 1 and Day 2 each show
  a 3-image row; Day 0 has none).
- 4D3N (16 images): 4 touring days × 3 images/day (12) + 1 route map + 3
  related-post thumbnails = 16. Confirmed against
  `tours-ha-giang-loop-4-days-3-nights/desktop.png`.
- 6D5N/Cao Bang (19 images): visually confirmed against
  `tours-ha-giang-cao-bang-6-days/desktop.png` to have the same per-day
  3-image row pattern, plus route map, plus a 3-image related-posts row at
  the bottom — but 6 touring days × 3 (18) + 1 route map + 3 related = 22,
  which overshoots the measured 19. The screenshot shows at least one day's
  photo row with fewer than 3 images. Rather than assert an unverified exact
  per-day breakdown, the safe conclusion is: image count grows with itinerary
  length at roughly 3 images per touring day, but is not a rigid fixed count
  per day — the CMS field should support a variable number of gallery images
  per itinerary day (which also matches spec §5.4's "no fixed limit on ...
  gallery images").

---

## 3. Summary of gaps for spec compliance

The reference site's tour-detail page does not visually separate every field
the spec requires as distinct, admin-editable content. These need original UI
design (not direct measurement ports) when building the WordPress template:

- **Accommodation options and upgrades** — reference shows this only as
  unstructured bullet text inside the Included list, no selector UI.
- **Bus transfers and add-ons** — same: bullet text only, no dedicated add-on
  line items or selector.
- **Safety/requirements content** — no standalone section observed; folded
  into itinerary notes/FAQ content only.
- **Destination highlights** — no distinct card/chip component detected;
  appears as prose within Overview only.
- **Final CTA** — no standalone bottom CTA band; the persistent/sticky
  booking widget (or its mobile stacked-position equivalent) serves this role
  implicitly.

Everything else in spec §5.4's field list (hero/gallery, price variants +
CTA, itinerary with Day 0, route map, included/excluded lists, vehicle/ride
options via the booking-widget selector, FAQs, related tours) has clear,
measured structural precedent in the captured data cited above.
