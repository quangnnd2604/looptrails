# Home

Audit of the reference Home page, based on visual inspection of the full-page
captures in `docs/reference-screenshots/home/*.png` (5 viewports: desktop
1440px, laptop 1280px, tablet 768px, mobile 390px, narrow-mobile 360px) and
the automated measurements in `docs/design-measurements/home/*.json`. Text
below is original description/paraphrase only — no reference-site copy is
quoted.

## 1. Section order (top to bottom, from `desktop.png`)

1. **Site header** — logo (top-left) and a single primary CTA button
   (top-right). No expanded nav-link row is visible at any captured width;
   likely a minimal/anchor-scroll header or a collapsed menu control too
   small to resolve at screenshot scale.
2. **Hero** — full-bleed background photo, headline referencing the tour
   region and available vehicle types, a short supporting line, and a
   primary booking CTA button.
3. **Featured tour grid** — heading, then a 3×2 grid of 6 tour cards. Each
   card shows a badge, an image, a title, multiple price options (by
   transport type), and two actions (a primary "book" button and a
   secondary "details" link).
4. **Narrative destination/brand section** — two-column layout: descriptive
   copy and a short feature-statistics callout on one side, a photo with an
   overlaid stat/price element and a CTA button on the other.
5. **Tabbed "destinations & essentials" area** — section heading, a row of
   4 tab controls, and (in the captured default state) a destination-cards
   panel: 8 image cards showing candidate stop/destination locations with
   short captions.
6. **"Why choose us" section** — heading, a 3×2 grid of 6 feature cards
   (icon + short heading + short body text), followed by a dark stat band
   with 4 summary numbers (counts/ratings) laid out in a single row.
7. **Testimonials/reviews** — heading, a third-party review-platform badge,
   and a row of review cards (3 visible at once; measurements show more
   cards exist in an underlying track, i.e. this is a carousel/slider, not
   a static grid).
8. **Editorial CTA** — a photo panel paired with a short motivational
   headline and a CTA button, styled as a large standalone banner distinct
   from the narrative section in step 4.
9. **Booking interface** — section heading, a promo/notice banner, and a
   full multi-field booking form (tour selection, date, transport type,
   group size, contact details, promo-code field, an order/booking summary
   block, payment-method selection, and a submit button).
10. **Blog/articles teaser** — heading and a row of 3 post cards (image,
    title, meta).
11. **FAQ accordion** — heading and a vertical list of expandable Q&A rows.
12. **Footer** — a single centered column (not multi-column link lists):
    logo, then stacked address/contact/hotline/email/website lines, legal
    text, and a certification badge, all center-aligned; followed by a
    separate bottom bar containing the copyright line and social icons.

## 2. Cross-reference against spec §5.2

Spec §5.2 expected Home sections vs. what was observed in `desktop.png`:

| Spec §5.2 item | Observed | Notes |
|---|---|---|
| Hero with background media, headline, supporting text, booking CTA | Yes | Section 2 above |
| Featured tour grid/cards (badge, rating, taxonomy chips, multi-option prices, Book Now + Details) | Yes | Section 3. Rating display and taxonomy chips were not clearly resolvable at screenshot scale on the card face; badge, multi-price rows, and the two actions were clearly visible. Needs a closer per-card look (or DOM inspection) to confirm rating stars/chips are present, not assumed absent. |
| Narrative destination/brand section (media, feature stats, CTA) | Yes | Section 4 |
| Tabbed "Top destinations & everything you'll need" area (Destinations / Itinerary & Map / Transport / Accommodation) | Partially | Section 5. All 4 tab controls are visible, but the full-page screenshot only captures the **default active tab's** content. See §3 "Audit gap" below. |
| Destination cards and metadata chips | Yes (cards) / Unconfirmed (chips) | 8 cards visible under the default tab; small metadata chips on each card were not clearly legible at screenshot scale |
| Itinerary duration tabs, day-by-day panels, route map, included/excluded lists | **Not observed** | Hidden behind an inactive tab — not present in any static capture |
| Transport option cards | **Not observed** | Same reason |
| Accommodation gallery/cards | **Not observed** | Same reason |
| "Why choose us" feature grid and statistics | Yes | Section 6 |
| Review/testimonial presentation | Yes | Section 7 |
| Large editorial CTA section | Yes | Section 8 |
| Full booking interface | Yes | Section 9 |
| Footer | Yes | Section 12 |

**Sections observed but not listed in spec §5.2:** a blog/articles teaser
(section 10) and an FAQ accordion (section 11). Both sit between the
booking interface and the footer. Recommend confirming with stakeholders
whether these are in-scope for the rebuild or reference-site extras.

## 3. Card-grid measurements

Values pulled from the `cards` array in each viewport's JSON. Viewport
widths confirmed via each file's `container.viewportWidth`: desktop 1440,
laptop 1280, tablet 768, mobile 390, narrow-mobile 360.

**Page container width:** Home's `desktop.json`/`laptop.json` report
`mostCommonContainerWidth: 1200` (candidateCount 27 at desktop). This is
the same clean 1200px boxed-content width reported for the tour-detail
template (`docs/design-measurements/tour-detail/desktop.json`,
`mostCommonContainerWidth: 1200`, candidateCount 7) — i.e. Home is
consistent with the site-wide 1200px container documented in
`docs/reference-audit/00-global.md`, not a page-specific value.

### 3.1 Featured tour grid (`lt-tours__grid`, 6 items)

| Viewport | Item W×H | Radius | Gap X | Columns implied |
|---|---|---|---|---|
| Desktop (1440) | 336×649 | 14px | 22px | 3 |
| Laptop (1280) | 336×649 | 14px | 22px | 3 |
| Tablet (768) | 325×619 | 14px | 22px | 2 |
| Mobile (390) | 330×623 | 14px | −330px (collapsed) | 1 |
| Narrow-mobile (360) | 300×622 | 14px | −300px (collapsed) | 1 |

A negative `gapX` equal to `−itemWidth` indicates consecutive cards share
the same horizontal offset, i.e. the grid has collapsed to a single column
(cards stack vertically). This confirms the brief's expected mobile
behavior: 3 columns → 2 columns (tablet) → 1 column (mobile/narrow-mobile).
Box shadow is `none` at every breakpoint (radius-only card style, no drop
shadow). Image aspect ratio is a constant 1.344 (≈4:3 wide) at desktop.

### 3.2 "Why choose us" feature grid (`lt-feats`, 6 items)

| Viewport | Item W×H | Radius | Gap X |
|---|---|---|---|
| Desktop (1440) | 336×338 | 14px | 22px |
| Laptop (1280) | 336×338 | 14px | 22px |
| Tablet (768) | 325×338 | 14px | 22px |
| Mobile / Narrow-mobile | not captured | — | — |

Same item width/gap pattern as the tour grid at desktop/laptop/tablet
(3 columns → 2 columns), but the measurement tool did not detect this
container at the two mobile widths even though the section is clearly
present in `mobile.png`/`narrow-mobile.png`. Treat as a tooling gap, not
evidence the section disappears — see §5.

### 3.3 Stats band (`lt-stats`, 4 items)

| Viewport | Item W×H | Gap X |
|---|---|---|
| Desktop (1440) | 244×81 | 0 |
| Laptop (1280) | 244×81 | 0 |
| Tablet (768) | 311×70 | 0 |
| Mobile (390) | 143×89 | 0 |
| Narrow-mobile (360) | 128×89 | 0 |

Unlike the card grids, this band stays a single row of 4 at every
breakpoint — items narrow proportionally rather than stacking. `gapX` is 0
throughout (edge-to-edge or border-divided blocks, not gapped cards).

### 3.4 Destination cards (`lt-grid`, 8 items)

| Viewport | Item W×H | Radius | Gap X |
|---|---|---|---|
| Tablet (768) | 327×337 | 14px | 18px |
| Narrow-mobile (360) | 300×318 | 14px | −300px (collapsed to 1 col) |
| Desktop / Laptop / Mobile | not captured | — | — |

Only captured at 2 of 5 viewports (tooling gap, see §5), but the two
available data points show the same collapse pattern as the tour grid:
tablet keeps multiple columns, narrow-mobile collapses to 1 column.
Desktop screenshot shows this grid at 4 columns × 2 rows (8 cards).

### 3.5 Testimonial/review carousel (`lt-slider__track`, 9 items)

| Viewport | Item W×H | Gap X |
|---|---|---|
| Desktop (1440) | 336×397 | 22px |
| Laptop (1280) | 336×397 | 22px |
| Tablet (768) | 327×394 | 18px |
| Mobile (390) | 330×394 | 18px |
| Narrow-mobile (360) | 300×437 | 18px |

Present at all 5 viewports with a constant 9-item count and a positive
(non-collapsing) gap at every width, confirming this is a horizontally
scrolling carousel/track rather than a wrapping grid — it does not stack
to 1 column the way the static card grids do; instead fewer cards are
visible in the viewport at once (screenshot shows 3 visible on desktop).

### 3.6 Blog/articles teaser (`elementor-posts-container…skin-cards`, 3 items)

| Viewport | Item W×H | Gap X | Image aspect |
|---|---|---|---|
| Desktop (1440) | 373×472 | 30px | 1.5 |
| Laptop (1280) | 373×472 | 30px | 1.5 |
| Tablet / Mobile / Narrow-mobile | not captured | — | — |

Built on an Elementor Posts widget (`elementor-posts--skin-cards`), not the
site's custom `lt-` card classes. Only captured at the two widest
viewports; screenshots confirm the section itself is present at all 5.

### 3.7 Transport/bus-transfer option cards (`hgl-grid hgl-grid-2`, 4 items)

| Viewport | Item W×H | Gap X |
|---|---|---|
| Desktop (1440) | 397×68 | 16px |
| Laptop (1280) | 397×68 | 16px |
| Tablet (768) | 311×66 | 16px |
| Mobile (390) | 322×64 | −322px (collapsed) |
| Narrow-mobile (360) | 292×64 | −292px (collapsed) |

This is a bus/transport-transfer option card grid, not an unidentified
control. The typography data for Home's `desktop.json` includes a
`hgl-bus-col-title` entry (DM Sans, 14.4px/600, count 9) whose text
samples describe named bus-transfer routes and their prices — this title
class belongs inside these 397×68 grid items. `docs/component-inventory.md`
independently identifies the same `hgl-bus-col-title` signature (also
present on the tours-archive template, with matching 397×68 geometry) as a
"Transport/Bus Option Card," which this file's numbers corroborate.
2-column layout (`hgl-grid-2`) at desktop/laptop/tablet, collapsing to 1
column at mobile/narrow-mobile (negative `gapX`), same pattern as the
other card grids in this section.

### 3.8 FAQ list (`lt-faq__list`, 9 items)

| Viewport | Item W×H | Gap X |
|---|---|---|
| Mobile (390) | 330×74 | −330px (collapsed) |
| Narrow-mobile (360) | 300×74 | −300px (collapsed) |
| Desktop / Laptop / Tablet | not captured | — |

Not a card grid (single-column accordion rows), included here only because
it appears in the same `cards` array. Only captured at the two mobile
widths even though the FAQ section is visible in every screenshot — another
instance of the tooling gap noted in §5.

## 4. Responsive behavior per breakpoint

- **Desktop (1440px) / Laptop (1280px)** — visually near-identical layouts.
  Featured tour grid and "why choose us" grid both run 3 columns. Full
  header content visible (logo + CTA; no separate hamburger icon
  detected). All 12 sections from §1 present and in the same order.
- **Tablet (768px)** — featured tour grid and destination-cards grid drop
  to 2 columns (confirmed numerically in §3.1/§3.4). Header still shows
  logo + CTA only, no additional nav items appearing. Section order
  unchanged from desktop. Booking form fields appear to stay 2-up here
  (§3.7) rather than collapsing yet.
- **Mobile (390px)** — featured tour grid, destination-cards area, and the
  small booking-form field grid all collapse to a single column (confirmed
  via negative `gapX` in §3.1/§3.4/§3.7). Stats band stays a single row of
  4 narrow tiles rather than stacking (§3.3). Testimonial carousel keeps
  its horizontal-scroll behavior rather than stacking. Header remains
  logo + CTA at screenshot resolution; a hamburger/menu icon may be present
  but is not clearly resolvable at this capture scale — flagged as needing
  closer inspection, not confirmed absent.
- **Narrow-mobile (360px)** — same collapse pattern as mobile (390px), just
  narrower; no additional structural changes observed (no extra sections
  hidden, no visibly different stacking order). Item heights for the
  destination-cards grid and tour grid are close to the 390px values,
  indicating the layout is fluid/proportional rather than switching to a
  distinct narrow-mobile-specific arrangement.

Across all 5 breakpoints, section order (§1) is unchanged — no sections
are added, removed, or reordered between viewports; only column counts and
element sizes change.

## 5. Audit gaps / follow-ups

1. **Inactive tab content not captured.** The tabbed "Destinations /
   Itinerary & Map / Transport / Accommodation" area (§1 item 5) was only
   captured in its default "Destinations" state. Itinerary day-panels/route
   map, transport option cards, and the accommodation gallery (all
   explicitly expected by spec §5.2) were not visible in any of the 5
   screenshots and have no corresponding entries in the measurement JSON.
   Recommend a follow-up capture pass that clicks through each tab before
   screenshotting, or a live-site DOM inspection, before building those
   sub-panels.
2. **Inconsistent per-viewport measurement capture.** Several containers
   were only detected by the measurement tool at some viewports even
   though the corresponding section is visibly present in every screenshot:
   `lt-feats` (missing at mobile/narrow-mobile), `lt-grid` (present only at
   tablet/narrow-mobile), `elementor-posts-container` (present only at
   desktop/laptop), `lt-faq__list` (present only at mobile/narrow-mobile).
   This looks like a limitation of the measurement script's card-detection
   heuristic (possibly tied to scroll position or viewport-visibility
   during capture) rather than a real design difference. Values reported
   above are only for the viewports where a given container was actually
   captured; absence in a table is not evidence the section is missing at
   that width.
3. **Rating display and taxonomy chips on tour cards, and metadata chips
   on destination cards** (both explicitly named in spec §5.2) were not
   clearly legible at full-page screenshot scale. Confirm their presence
   via closer inspection or DOM/CSS review rather than assuming from this
   audit alone.
4. **Header/nav structure** (hamburger menu presence/behavior at narrow
   widths) could not be confirmed with confidence from these full-page
   captures — the header band is only a few pixels tall relative to the
   full page height. Recommend a dedicated header-only screenshot or DOM
   check if precise nav behavior is needed.
