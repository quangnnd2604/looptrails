# Reference Audit

**Capture date:** 2026-08-19. Full-page screenshots were captured ~02:15-02:21 UTC via `tools/reference-audit/capture.mjs` (manifest: `docs/reference-screenshot-manifest.json`, 60 entries — 12 reference pages x 5 viewports each; individual files are named `desktop.png` / `laptop.png` / `tablet.png` / `mobile.png` / `narrow-mobile.png` inside each page's folder under `docs/reference-screenshots/`). DOM/computed-style measurements were captured ~06:24-06:33 UTC the same day via `tools/design-audit/run-audit.mjs` (raw JSON per page/viewport under `docs/design-measurements/`, indexed at `docs/design-measurements/summary.json`). Per Milestone 1's capture procedure, animation was disabled during screenshot capture (spec §4) — this is also the reason animation/transition timing cannot be derived from this audit; see "Known gaps" at the end of this document.

**Source data:** `docs/design-measurements/` (computed-style JSON for all 5 viewports, covering Home, Tour Detail plus 3 tour-length variations, Tours Archive, Motorbike Rental, Blog Archive, Blog Single, Contact, About, Terms, and 404) and `docs/reference-screenshots/` (matching full-page PNG captures) — both produced in Milestones 1-2a.

**Structure of this document:** the four sections below are Tasks 2-5 of Milestone 2b (Milestone 2b's own task numbering), concatenated in the order required by this milestone's plan, with each section's internal heading levels shifted down by one (original `#` -> `##`, `##` -> `###`, `###` -> `####`) so they nest under this single top-level document. Each source section also remains standalone under `docs/reference-audit/` (`00-global.md`, `01-home.md`, `02-tour-detail.md`, `03-secondary-pages.md`) for traceability to its own review history; this merged file is the canonical path required by spec §3. No content was reworded during the merge — only heading levels changed.

---

## Global

Source data: `docs/design-measurements/home/*.json` (all 5 viewports: desktop 1440px, laptop 1280px, tablet 768px, mobile 390px, narrow-mobile 360px), cross-checked against `docs/design-measurements/tour-detail/desktop.json`, `docs/design-measurements/about/desktop.json`, and `docs/design-measurements/contact/desktop.json`. Visual context from `docs/reference-screenshots/home/*.png`. All values below are measured computed-style output (px sizes, hex colors) or my own descriptive wording — no reference-site prose is quoted.

### 1. Typography Scale

The top styles by occurrence count on Home desktop are dominated by small UI text (pill tags, price rows, form labels) rather than headings, because headings appear once or a handful of times per page while repeated card/list elements appear dozens of times. The table below blends the true top-10-by-count entries with the structurally-essential heading roles (H1/H3/H4), located by tag and cross-checked against the desktop screenshot for placement. Font sizes are compared 1px-level between the desktop (1440px) and mobile (390px) JSON exports, per the accuracy target for this audit.

| Role | Desktop (1440px) | Mobile (390px) | Font family |
|---|---|---|---|
| H1 (hero heading) | 60px / 800 / 64.8px line-height / -1px tracking | 32px / 800 / 35.84px / -0.5px | Montserrat |
| H2 (section heading, bare `h2`) | 22px / 700 / 27.5px / -0.3px | 18px / 700 / 22.5px / -0.2px | Montserrat |
| H3 (card title, bare `h3`) | 18px / 600 / 21.96px / -0.2px | 18px / 600 / 21.96px / -0.2px (fixed, not responsive) | Poppins |
| H3 (review card title, `.lt-review__title`) | 16px / 600 / 20.8px / -0.2px | 16px / 600 / 20.8px / -0.2px (fixed) | Poppins |
| H4 (destination/day card title, bare `h4`) | 18px / 600 / 21.6px / -0.2px | 18px / 600 / 21.6px / -0.2px (fixed) | Poppins |
| Body paragraph (bare `p`) | 14.5px / 400 / 23.925px | 14.5px / 400 / 23.925px (fixed) | Inter |
| Body emphasis (bare `strong`) | 14.5px / 600 / 23.925px | 14.5px / 600 / 23.925px (fixed) | Inter |
| Category pill / tag label (`.lt-pill`) | 10.5px / 700 / 16.275px / 0.8px tracking | 10.5px / 700 / 16.275px / 0.8px (fixed) | Montserrat |
| Form field label (`.hgl-label`) | 14.4px / 600 / 18px | 14.4px / 600 / 18px (fixed) | DM Sans |
| Card-level button label (`.lt-btn.lt-btn--primary.lt-book-btn`) | 11px / 700 / 11px / 0.8px tracking | 11px / 700 / 11px / 0.8px (fixed) | Montserrat |
| Price value (`.lt-price-row__value`) | 14px / 700 / 21.7px / -0.1px | 14px / 700 / 21.7px / -0.1px (fixed) | Inter |
| Caption / small print (`.lt-review__author-meta`) | 11.5px / 400 / 13.8px | 11.5px / 400 / 13.8px (fixed) | Inter |

**Observation:** most component-level type (cards, pills, prices, labels, buttons) is set in fixed pixel values that do **not** change across breakpoints — only the page-level headline styles (H1, H2, and the hero sub-line) scale down with viewport width. Full 5-viewport progression for the two fluid styles:

| Viewport | H1 size / weight / line-height / tracking | Section H2 size / weight / line-height / tracking |
|---|---|---|
| Desktop (1440px) | 60px / 800 / 64.8px / -1px | 22px / 700 / 27.5px / -0.3px |
| Laptop (1280px) | 60px / 800 / 64.8px / -1px | 22px / 700 / 27.5px / -0.3px |
| Tablet (768px) | 43.008px / 800 / 46.4486px / -1px | 20px / 700 / 25px / -0.3px |
| Mobile (390px) | 32px / 800 / 35.84px / -0.5px | 18px / 700 / 22.5px / -0.2px |
| Narrow-mobile (360px) | 28px / 800 / 31.36px / -0.4px | 18px / 700 / 22.5px / -0.2px |

**Caveat:** the main site-header nav links did not surface as a distinct, high-count style in Home's typography array — they likely render through the theme's native menu markup and were not captured as a separate grouping by the Milestone 2a crawler, or their computed style coincides with another already-listed entry. Do not infer nav-link size/weight from this table; verify directly against the built header if pixel-exact matching is required (Task 1's component inventory may have this).

### 2. Color Palette

Colors with `count >= 5` on Home desktop, cross-checked against `tour-detail/desktop.json`, `about/desktop.json`, and `contact/desktop.json` to separate true global tokens from page/component-specific ones.

#### Global palette (confirmed present, by similar role, across all four cross-checked templates)

| Role | Hex | CSS property | Cross-template evidence |
|---|---|---|---|
| Primary brand / accent (orange) | `#ff6602` | color / backgroundColor | Home (51 color + 25 bg), tour-detail (38 + 17), about (13 + 2), contact (4 + 2) |
| Ink / shadow-and-border accent | `#36343b` | color / borderTopColor | Present as the border/shadow color on every measured button (`boxShadow: rgb(54, 52, 59) …`) in Home, tour-detail, and elsewhere; also the button-shadow ink used sitewide |
| Body text | `#212121` | color (on `body`) | Top color by count on every single template checked: Home (206), tour-detail (165), about (150), contact (113) |
| Secondary heading / dark text | `#333333` | color | Home (17), about (30), contact (12), tour-detail (16) |
| Page background | `#ffffff` | backgroundColor (on `body`) | Present on all four templates |
| Secondary accent (magenta/pink) | `#e5396e` | color | Home (41), tour-detail (35), about (26), contact (24) — consistently present, likely a link/hover accent |
| Header/footer surface (cream) | `#e4e0da` | backgroundColor | Home, tour-detail, about, contact all show this on the shared header (`elementor-element-27d9`, semi-transparent at `rgba(228,224,218,0.85)` on Home) and shared footer (`elementor-element-50aa`) sections — the matching Elementor element IDs across templates confirm this is the same reused header/footer template part, not a page-specific match |
| Social icon brand colors | `#1877f2` (Facebook), `#e1306c` (Instagram), `#25d366` (WhatsApp), `#000000` (TikTok), `#34e0a1` (Tripadvisor) | color | Identical set present in Home, tour-detail, and about's color arrays — confirms the footer's social-icon row is shared sitewide |

#### Page/component-specific — do not treat as global tokens

- **Light section background** `#f7f7f7` — seen on Home (count 20) and about (count 11), but absent from the tour-detail and contact samples checked. Likely a section-background choice used on specific page templates, not a universal surface color.
- **Divider/border gray** `#dddddd` — seen on Home (95) and about (22) and contact (4), but not in the tour-detail sample. Tour-detail instead uses `#e2e8f0` for its equivalent border role (see below) — evidence of two coexisting component systems.
- **"Widget" / booking-form palette** — a visually distinct Tailwind-style palette appears only inside booking-widget markup (`hgl-*` classes on Home, `ltw-*` classes on tour-detail), not on the simpler about/contact templates: navy `#1a1a2e` (booking panel), slate borders `#e2e8f0`/`#e2e4e9`, slate backgrounds `#f8fafc`/`#f1f5f9`/`#f9fafb`/`#f3f4f6`, slate text `#4a5568`/`#64748b`/`#94a3b8`/`#1e293b`/`#1f2937`/`#374151`/`#111827`, plus status colors `#10b981` (green/discount), `#f59e0b`/`#fbbf24` (amber/stars), `#ef4444` (red/required). This is a component-scoped design system for the booking/tour-widget UI, not a site-wide palette — reuse it only when rebuilding the booking widget, not for general page chrome.
- **Muted gray text** has two distinct, non-identical values depending on component generation: `#6b696f` on Home's older `lt-` components vs. `#6b7280` on tour-detail's newer `ltw-` components. Treat these as two separate tokens tied to their respective component families, not one global "muted text" color.
- **Data quirk (not a global-vs-specific finding):** `contact/desktop.json`'s `container` block reports `mostCommonContainerWidth: null` at the 1440px desktop viewport, even though its own `topCandidates` list two elements at `maxWidth: 1200` / `renderedWidth: 1200`. This looks like a threshold/consensus quirk in the Milestone 2a measurement script for that specific page (which has very few container candidates), not evidence that the 1200px container doesn't apply on Contact at desktop width.

### 3. Container / Breakpoint Behavior

`container` field per viewport, from Home (all 5 viewports), cross-checked against tour-detail, about, and contact desktop:

| Viewport | Viewport width | `mostCommonContainerWidth` | Notes |
|---|---|---|---|
| Desktop | 1440px | **1200px** | Confirmed identically on Home, tour-detail, and about (all report `maxWidth: 1200`, `renderedWidth: 1200`, zero side padding on the container element itself — side gutters come from elsewhere). |
| Laptop | 1280px | **1200px** | Same 1200px container persists down to 1280px — the container does not need to shrink until the viewport itself is narrower than 1200px plus its own side clearance. |
| Tablet | 768px | **671px** (rendered); CSS `maxWidth` on the matched elements is 760px | The three top candidates at this viewport are per-section header wrapper `<div>`s (e.g. tour-list section header, info section header), not the page-level `.elementor-container` seen at desktop/laptop — a different DOM element became the "best candidate" at this width, and its rendered box (671px) is narrower than its own CSS max-width (760px), likely due to nested padding/margins on that specific element rather than a page-wide boxed-content width. Treat 671px as a lower-confidence data point compared to the clean 1200px reads at desktop/laptop. |
| Mobile | 390px | **`null`** (0 candidates) | Confirmed real behavior, not missing data. |
| Narrow-mobile | 360px | **`null`** (0 candidates) | Confirmed real behavior, not missing data. |

**Why mobile/narrow-mobile are `null`:** per Milestone 2a's finding, the site's container `max-width` constraint (the ~1200px/760px boxed-content rule visible at wider viewports) simply doesn't bind at these narrow widths — there is no element on the page whose rendered width is measurably narrower than the viewport itself in a way that qualifies as a distinct "container." Content is effectively full-width with side padding at 390px and 360px, so there is no boxed container width to report. This is not a capture failure; do not fabricate a container width for these two viewports when rebuilding — instead replicate full-bleed content with side padding at these breakpoints.

### 4. Header Structure

Based on `docs/reference-screenshots/home/desktop.png`, `tablet.png`, and `mobile.png`, plus the `buttons` array in each viewport's JSON:

- **Logo:** wordmark positioned top-left of the header bar, present at every viewport in the screenshots checked (desktop, tablet, mobile).
- **Header surface:** a semi-transparent cream panel (`rgba(228, 224, 218, 0.85)`, i.e. `#e4e0da` at 85% opacity) sits over the hero photo on Home. The section carries the same Elementor element ID (`elementor-element-27d9`) on Home, tour-detail, about, and contact — strong evidence this is one shared/global header template part reused across the whole site, not independently built per page.
- **"Book Now" CTA placement:** a pill-style button (white background, orange text, hard drop-shadow using the ink color `#36343b`) sits at the right side of the header bar on every viewport checked. It is the first entry in every viewport's `buttons` array (height ~35px on desktop/laptop, growing to ~33–36px with a larger 15–16px font on mobile/tablet — a deliberately larger tap target rather than a literal proportional scale-down).
- **Nav item order:** not confidently determinable from the screenshots at the resolution captured, or from the typography JSON (see caveat in §1) — the tablet screenshot shows the header bar at a similar visual width/density to desktop, suggesting the nav may not collapse to a hamburger menu until below 768px, but this is a visual impression, not a measured fact. Verify directly in a browser or against Task 1's component inventory before implementing exact breakpoint behavior.
- **Sticky behavior:** requires direct interaction testing (scroll behavior in a live browser), not inferable from a static screenshot or a single-snapshot computed-style JSON. Do not assume sticky/fixed positioning without checking the live reference site or documenting it as unverified.

### 5. Footer Structure

Based on the same screenshots and the color-array evidence for the shared cream surface color (`#e4e0da`): tour-detail and about each record their footer background under a specific Elementor element ID, `elementor-element-50aa` (tag `footer`, `samples: ["footer"]`, count 1). Home and contact don't have a `50aa`-tagged entry, but each still shows `"footer"` inside the `samples` array of their own `#e4e0da` color entry — contact's entry is recorded under the header's own ID (`elementor-element-27d9`, `samples: ["header", "footer"]`), and Home's plain (non-alpha) entry is recorded under an unrelated representative class (`elementor-alert`, `samples: ["div", "footer"]`). The measurement tool appears to log one representative element per unique computed-style bucket, so the specific ID captured differs by template even when "footer" is present in the same bucket's samples every time. Net effect: all four templates confirm the same cream footer background color, even though only tour-detail and about expose it under a literal, dedicated footer element ID — treat the shared color (not a single universal element ID) as the evidence for one global footer surface.

- **Layout:** at the screenshot resolutions available, the footer reads as a single centered content block rather than an obvious multi-column grid: a logo mark, a short contact/tagline line, then a row of social-platform icons, then a visually separated bottom bar carrying a copyright line. This is a visual read at reduced scale, not a pixel-measured column count — confirm exact column structure (if any) against Task 1's component inventory or the live reference site before building.
- **Social icon row:** confirmed present and identical across templates via color data — Facebook (`#1877f2`), Instagram (`#e1306c`), WhatsApp (`#25d366`), TikTok (`#000000`), Tripadvisor (`#34e0a1`) — each icon rendered in its platform's brand color, appearing in Home, tour-detail, and about's color arrays.
- **Surface color:** `#e4e0da` (cream/tan), same token used on the header, giving header and footer a matching surface color across the whole site.
- **Bottom bar:** a visually distinct, smaller-type line sits below the main footer content (measured at 12px, weight 400, using the `"Open Sans"` font family — the only place this font family appears in the typography data), separated from the rest of the footer content, consistent with a standard copyright/legal bar.


---

## Home

Audit of the reference Home page, based on visual inspection of the full-page
captures in `docs/reference-screenshots/home/*.png` (5 viewports: desktop
1440px, laptop 1280px, tablet 768px, mobile 390px, narrow-mobile 360px) and
the automated measurements in `docs/design-measurements/home/*.json`. Text
below is original description/paraphrase only — no reference-site copy is
quoted.

### 1. Section order (top to bottom, from `desktop.png`)

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
12. **Footer** — logo, link columns, social icons, and a copyright line.

### 2. Cross-reference against spec §5.2

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

### 3. Card-grid measurements

Values pulled from the `cards` array in each viewport's JSON. Viewport
widths confirmed via each file's `container.viewportWidth`: desktop 1440,
laptop 1280, tablet 768, mobile 390, narrow-mobile 360.

#### 3.1 Featured tour grid (`lt-tours__grid`, 6 items)

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

#### 3.2 "Why choose us" feature grid (`lt-feats`, 6 items)

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

#### 3.3 Stats band (`lt-stats`, 4 items)

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

#### 3.4 Destination cards (`lt-grid`, 8 items)

| Viewport | Item W×H | Radius | Gap X |
|---|---|---|---|
| Tablet (768) | 327×337 | 14px | 18px |
| Narrow-mobile (360) | 300×318 | 14px | −300px (collapsed to 1 col) |
| Desktop / Laptop / Mobile | not captured | — | — |

Only captured at 2 of 5 viewports (tooling gap, see §5), but the two
available data points show the same collapse pattern as the tour grid:
tablet keeps multiple columns, narrow-mobile collapses to 1 column.
Desktop screenshot shows this grid at 4 columns × 2 rows (8 cards).

#### 3.5 Testimonial/review carousel (`lt-slider__track`, 9 items)

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

#### 3.6 Blog/articles teaser (`elementor-posts-container…skin-cards`, 3 items)

| Viewport | Item W×H | Gap X | Image aspect |
|---|---|---|---|
| Desktop (1440) | 373×472 | 30px | 1.5 |
| Laptop (1280) | 373×472 | 30px | 1.5 |
| Tablet / Mobile / Narrow-mobile | not captured | — | — |

Built on an Elementor Posts widget (`elementor-posts--skin-cards`), not the
site's custom `lt-` card classes. Only captured at the two widest
viewports; screenshots confirm the section itself is present at all 5.

#### 3.7 Unidentified small 4-item grid (`hgl-grid hgl-grid-2`, 4 items)

| Viewport | Item W×H | Gap X |
|---|---|---|
| Desktop (1440) | 397×68 | 16px |
| Laptop (1280) | 397×68 | 16px |
| Tablet (768) | 311×66 | 16px |
| Mobile (390) | 322×64 | −322px (collapsed) |
| Narrow-mobile (360) | 292×64 | −292px (collapsed) |

Short item height (~64–68px) rules this out as a photo/content card; it is
most likely a 2-column field-pair layout inside the booking form (the
`hgl-` prefix also appears on booking-form field labels in the typography
data), not a card grid in the visual sense described by the brief. Flagged
here for completeness rather than as a confirmed "card" section. Collapses
to 1 column at mobile widths, same pattern as the true card grids.

#### 3.8 FAQ list (`lt-faq__list`, 9 items)

| Viewport | Item W×H | Gap X |
|---|---|---|
| Mobile (390) | 330×74 | −330px (collapsed) |
| Narrow-mobile (360) | 300×74 | −300px (collapsed) |
| Desktop / Laptop / Tablet | not captured | — |

Not a card grid (single-column accordion rows), included here only because
it appears in the same `cards` array. Only captured at the two mobile
widths even though the FAQ section is visible in every screenshot — another
instance of the tooling gap noted in §5.

### 4. Responsive behavior per breakpoint

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

### 5. Audit gaps / follow-ups

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


---

## Reference Audit — Tour Detail Template

### Scope and evidence base

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

### 1. Fixed template structure (spec §5.4 field list)

#### Hero, title, badges, rating presentation

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
- Gallery: the hero itself reads as a single static background image per tour
  in every screenshot reviewed — no thumbnail strip, dot indicators, or
  multi-image carousel controls are visible adjacent to the hero on any
  viewport. A chevron button pair (`‹`/`›`, height 47px, orange background)
  does exist in `desktop.json`'s buttons array, which is consistent with a
  photo-carousel control somewhere on the page, but its position couldn't be
  pinned to the hero specifically from this data — it's more likely tied to
  the day-by-day itinerary photo rows (see Itinerary, below) or the reviews
  list. Whether a dedicated top-of-page multi-image gallery exists separate
  from the per-day itinerary photos is **not resolved** by this data and
  would need direct inspection if that distinction matters for the build.
- Rating presentation (in the content body, not the hero): a large numeric score
  `div.lt-rating-number` — **44.8px / weight 700** — paired with
  `div.lt-rating-stars` — **16px**, star glyph color `rgb(251,191,36)` /
  `#fbbf24` (amber) (source: `desktop.json` typography + colors).
- Badges: small pill labels such as `div.ltw-tour-badge` (**12px / weight 600**)
  on the booking widget, and availability-legend dots (`span.ltw-avail-legend-dot`)
  colored amber `#f59e0b`, red `#ef4444`, and grey `#9ca3af` for calendar-day
  status (source: `desktop.json` colors).

#### Price variants and booking CTA

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

#### Destination highlights / overview / quick facts

An `Overview` section (h2 `.lt-section-title`, **22.4px/700**, shared heading
style for all section titles on this template) followed by body paragraphs at
**16.8px/400, line-height 30.24px** with a bold inline emphasis variant at the
same size/700 weight (source: `desktop.json` typography, `p`/`strong` with no
class, both count 3). No distinct "destination highlight" chip/card grid was
detected by the card-grid probe on this template — highlights appear to be
delivered as prose within Overview rather than as a separate structured list.

**Quick facts (spec §5.4 "overview and quick facts"):** the `cards` probe on
`desktop.json` does contain one entry parented under `div.lt-overview-text`:
**3 items, 706×121px, border-radius 0, box-shadow none, gapX −706**. A
negative `gapX` equal to `-itemWidth` means the 3 items are stacked full-width
(vertically, zero horizontal offset) rather than arranged as a horizontal
row of compact fact chips — that layout signature does not match the
typical "quick facts" pattern (a tight row of icon+short-label pairs for
duration/group size/start point, etc.). Cross-referencing against the
typography scan, the *only* `p`/`strong` pair with a matching count of 3 is
the 16.8px/400 body-paragraph rule cited above, and 121px height is
consistent with roughly 4 lines at that line-height. **My read: this `cards`
entry is more likely the 3 intro paragraphs of the Overview copy being
mis-detected as "cards" by the generic grid probe (three full-width text
blocks stacked with no gap), not a genuine "quick facts" UI element.** I
can't fully rule out the alternative — the probe has no semantic awareness,
so a compact quick-facts row using unusual full-bleed styling can't be
excluded with certainty from this data alone. Flagging this as ambiguous
rather than resolved: if the build needs a literal "quick facts" component,
this reference page provides no confirmed visual precedent for one, and it
should be treated as new UI design rather than a direct measurement port,
pending direct visual re-inspection to settle the ambiguity.

#### Itinerary (Day 0 + following days)

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

#### Route map

A single static illustrated map image with numbered route pins, visible in all
three tour screenshots (different route shapes per tour, same visual treatment:
white card background, rounded corners, thin border). A caption below it:
`p.lt-map-caption` — **13.6px/400** (source: `desktop.json` typography). No
iframe/embed markup was detected by any of the 5 viewport probes — the
measurement data is consistent with a static image asset rather than a live map
embed, though the spec allows either.

#### Included / excluded lists

Two-column layout (`Included` / `Not Included`), each an `ul.lt-included-list`
card grid — **3 visible items, 291×70px at desktop** (source: `desktop.json`
cards), reflowing to a single narrower column at mobile (286×70 at 390px,
256×70 at 360px). Section sub-headings `h3` (no class) at **16px/700**
(source: `desktop.json` typography, count 2 — "Included"/"Not Included"
labels). A colored container variant `div.lt-included-box.yes` uses a light
green border `rgba(16,185,129,0.25)` (source: `desktop.json` colors).

#### Vehicle/ride options

Not a separate page section — this is delivered as the ride-type selector card
group inside the booking widget (see "Price variants" above:
`div.ltw-option-title` / `div.ltw-option-subtitle`). The subtitle text style is
reused for short qualifier labels (e.g. distinguishing tiers) at
**12px/700, letter-spacing 0.5px**.

#### Accommodation options and upgrades

**Gap:** no distinct accommodation-selector UI or accommodation card grid was
found by any probe on this template. Accommodation appears only as a plain
text line inside the Included list (nights of homestay/guesthouse lodging
mentioned as one bullet, not a structured, selectable option). The spec
requires "accommodation options and upgrades" as an editable field — the
reference page gives no visual precedent for how upgrades/tiers would be
presented; this will need original UI design rather than a direct measurement
port.

#### Bus transfers and add-ons

**Gap:** same situation as accommodation — bus transfer mentions appear only
as individual bullet lines inside the Included/Not-Included lists (e.g. a
transfer-arrangement note appears as one "Not Included" bullet on the 2D1N
sample), not as a dedicated add-on selector or line-item UI. No distinct
"add-ons" component was detected structurally.

#### Safety/requirements content

**Gap:** no dedicated "Safety" or "Requirements" section/heading was found.
The closest analogues are (a) the itinerary callout note (`div.lt-note`,
described above) and (b) FAQ answers that likely cover fitness/age/weather
topics conversationally. Treat this as content that needs its own admin field
even though the reference page folds it into FAQ/notes rather than surfacing
it as a standalone block.

#### FAQs

Accordion list: numbered index `span.lt-faq-number` — **12.8px/700**,
background `rgb(243,244,246)` circular chip; question text
`span.lt-faq-q-text` — **16px/600**; answer paragraph
`p.lt-faq-a-content` — **15.2px/400, line-height 26.6px**; item border
`rgb(226,228,233)` / `#e2e4e9`; toggle icon border tint
`rgb(254,215,170)` / `#fed7aa` (source: `desktop.json` typography + colors,
all at count 7 — the primary tour has 7 FAQ items). Row container
`div.lt-faq-item`; expand/collapse control class `lt-faq-toggle`.

#### Related tours

Rendered using the same card-grid component as the blog/related-posts widget:
`div.elementor-posts-container` — **3 items, 373×464px at desktop, image
aspect ratio 1.5** (source: `desktop.json` cards), reflowing to 359×441 at
tablet. Card link text style: `a.elementor-post__read-more` —
**20px/600**, title link `a` (no class) — **22px/700** (source: `desktop.json`
typography). This section sits at the very bottom of the page, after
FAQs/Reviews and before the footer, on all three tour screenshots reviewed.

#### Final CTA

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

#### Sticky secondary nav (in-page tabs)

Not explicitly in the spec's field list but structurally important: a sticky
nav bar with in-page jump links — `a.lt-nav-link` — **13.6px/600**, active
state background `rgb(255,247,237)` / `#fff7ed`, active text color
`rgb(255,102,2)` / `#ff6602` (source: `desktop.json` typography + colors).
Labels visible in the primary sample: Overview / Itinerary / Route Map /
What's Included / Reviews / FAQs (6 tab links, matching typography count 6 for
`.lt-nav-link.active` and `.lt-section-title`, i.e. one tab per major
section).

---

### 2. Variation across tour lengths (spec §5.4: "Day 0 and unlimited following days")

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

#### Day 0 confirmation

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

#### Price sample count

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

#### Visible image count

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

### 3. Summary of gaps for spec compliance

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
- **Quick facts** — ambiguous, not confirmed either way: the one candidate
  measurement (`lt-overview-text` cards entry, 3×706×121, stacked full-width)
  reads more like 3 stacked Overview paragraphs than a compact facts row, but
  this isn't certain from probe data alone. Treat as no confirmed precedent.
- **Top-of-page gallery** (distinct from the hero background and from the
  per-day itinerary photos) — not confirmed present or absent; no
  carousel/thumbnail UI was pinned to the hero specifically.
- **Final CTA** — no standalone bottom CTA band; the persistent/sticky
  booking widget (or its mobile stacked-position equivalent) serves this role
  implicitly.

Everything else in spec §5.4's field list (hero background, price variants +
CTA, itinerary with Day 0, route map, included/excluded lists, vehicle/ride
options via the booking-widget selector, FAQs, related tours) has clear,
measured structural precedent in the captured data cited above.


---

## Secondary Pages

Source data: `docs/design-measurements/{tours-archive,motorbike-rental,blog-archive,blog-single,contact,about,terms,404}/*.json` (5 viewports each — desktop 1440px, laptop, tablet 768px, mobile 390px, narrow-mobile), cross-referenced against the matching folders under `docs/reference-screenshots/` (slugs there differ from the measurement slugs; the mapping used is noted per template below). Values not explicitly cited below (body text color, primary orange, header/footer background, social-icon brand colors) match the global palette already documented in `00-global.md` and are not repeated here — this section covers only what's distinct to each template.

One data-quality note that applies throughout: colors sampled from the `skip-link screen-reader-text` element (an off-screen accessibility-only link) are a measurement artifact, not a visible design color — they are excluded from the per-page palettes below even where they show a high `count`.

---

### Tours Archive

Screenshot folder: `docs/reference-screenshots/tours/`. Measurement folder: `tours-archive/`.

**Section order** (from `tours/desktop.png`): hero banner (dark photo background, eyebrow label, H1, subtitle, CTA button) → a heading introducing the tour package grid → 2×2 tour package card grid → promo/permit banner strip → full booking widget (tour/date selectors, riding option, accommodation, bus transfer, personal info, voucher, cost summary, payment type/method, terms checkbox, a "Confirm Booking" button) → a benefits/differentiators accordion → Tripadvisor strip → footer.

**Typography/color distinct from global:** price figures use a dedicated red, `rgb(230,0,35)` / `#e60023` (`.price-value`), with a muted slate `#64748b` for the USD conversion and `#94a3b8`/`#718096` for secondary label text. A green `#10b981` marks discount amounts. Tour titles (`h3.tour-title`) use the system font stack at 20px/700 in `#1a202c`, distinct from the Montserrat headings used elsewhere on the page. Pill/spec badges use `#475569` text on a `#fff5f0` background. None of this is present in the Home page's typography sample, so it's specific to this template's card + booking-widget components.

**Card/button measurements:** the automated card-grid probe only picked up one grid on this page — `.hgl-grid-2` (4 items, 397×68px desktop / 312×64px mobile, `gapX: 16`, no radius/shadow) — which is the bus-transfer summary row inside the booking widget, not the visible tour package cards. The 2×2 tour package grid seen in the screenshot (thumbnail with corner ribbon badge, title, star rating, spec pills, dual price rows, "Book Now" + "View Details" buttons) was not captured by the probe's selector heuristics; its dimensions are not available as exact pixel values and would need direct DOM inspection during build. Button styles measured: "Book Now" pill (44px, radius 25px, orange fill or white/orange outline depending on placement, `boxShadow: rgba(255,102,2,0.3) 0px 4px 15px 0px`) and "View Details" (44px, radius 25px, white fill/orange text, no shadow) — both 13px/700 text.

**Responsive behavior:** the 2×2 tour card grid collapses to a single column at mobile (390px), each card full-width and stacked vertically (confirmed via `tours/mobile.png`); the booking widget stays a single column at all widths below desktop. Container width is a confirmed 1200px at desktop/laptop (`mostCommonContainerWidth: 1200`, 6 matching candidates); at tablet/mobile/narrow-mobile it is `null` — consistent with the rest of the site, content runs full-width with side padding below the 1200px breakpoint rather than being boxed to a smaller fixed width.

---

### Motorbike Rental

Screenshot folder: `docs/reference-screenshots/ha-giang-motorbike-rental/`. Measurement folder: `motorbike-rental/`.

**Section order**: hero banner (photo of bikes, a short two-word H1 tagline, subtitle, "Rent Now" CTA) → intro copy block → a benefits-focused accordion for the rental service (4 items) → a heading introducing the bike selection grid → 2×2 bike selection card grid (badge, photo, transmission-type label, title, spec description, price, "Book Now") → requirements info banner → full rental booking widget (bike picker, rental dates/days/count, personal info, cost summary, payment type/method, terms, "Book Now & Pay") → FAQ accordion (6 items) → footer.

**Typography/color distinct from global:** this is the one template whose typography array is dominated by the DM Sans family for nearly all UI text (`.ltr-*` classes — bike price, bike name/desc, form labels, summary rows), whereas most other templates lean on the system font stack or Montserrat for the same roles — worth flagging for the design-tokens pass since it means DM Sans is a real secondary UI font, not a one-off. Colors: bike-card border `#e5e7eb` (rest) / orange `#ff6602` (selected), selected-card background `#fff5f0`, feature-icon chip background `#f7f7f7`, required-field red `#ef4444`.

**Card/button measurements:** `.ltr-bikes` grid — 4 items, 310×319px at desktop, radius 12px, `boxShadow: rgba(255,102,2,0.15) 0px 0px 0px 3px` (soft orange ring, not a drop shadow), `gapX: 12`, `imageAspectRatio: 1.365`. At mobile (390px) itemWidth is 314×322 — nearly unchanged from desktop's 310×319, confirming the grid is already a single column at mobile rather than shrinking a multi-column layout. Buttons: "Rent Now" primary (47-55px height, radius 7px, orange fill, `boxShadow: rgb(54,52,59) 2px 3px 0px 0px` — the site's consistent hard-offset shadow style), "Book Now" header variant (35px, radius 7px, white fill/orange text, same offset shadow).

**Responsive behavior:** container `mostCommonContainerWidth` is `null` at every viewport including desktop on this template (`candidateCount: 6` but none reach majority) — same "full-width content, no single dominant boxed width" pattern noted for other secondary pages; only Home, Tours Archive and About showed a clean 1200px majority. Bike grid and requirements FAQ both go to single-column stacking at mobile per `ha-giang-motorbike-rental/mobile.png`.

---

### Blog Archive

Screenshot folder: `docs/reference-screenshots/blog/`. Measurement folder: `blog-archive/`.

**Section order**: a short, generic archive-page H1 → flat vertical list of post entries, each: full-width featured photo, colored title link, one-paragraph excerpt truncated with an ellipsis → a pagination control at the bottom → footer. There is no card-grid layout here — it's a simple stacked post list, confirmed by both the screenshot and the JSON (`cards: []`, no card-grid pattern detected).

**Typography/color distinct from global:** minimal — the only page-specific style is the `h1.entry-title` (Montserrat 22px/700) and the post-link color, which reuses the site's pink-magenta `#e5396e` (also seen elsewhere as an accent, so not unique to this template). No distinct card or button styling beyond the shared header "Book Now" button.

**Responsive behavior:** container is `null` at every viewport (max-width candidates cap at 1140px on `.site-main`, below the 1200px Elementor container used elsewhere — this template uses the theme's default WordPress content width rather than an Elementor page-builder container, worth noting for the theme build). At mobile the post list stays single-column (it already is one at desktop) with images scaling to full width.

This is the shortest write-up of the eight because the page itself is the simplest: one heading style, one repeating text-link pattern, no cards, no distinct color usage.

---

### Blog Single Article

Screenshot folder: `docs/reference-screenshots/guide-getting-sick-on-the-loop/` (the Milestone 1 capture used a real article slug; it stands in for the single-post template generally). Measurement folder: `blog-single/`.

**Section order**: featured image → title/meta bar (date, author) → an auto-generated in-article table-of-contents box → body content (H2/H3 section headings, paragraphs, bold lead-ins) repeated for each article section → social share row (Facebook/X/Reddit icons) → author box → a related-content heading followed by a related-posts strip → footer.

**Typography/color distinct from global:** a genuine heading hierarchy exists here that the other templates don't need: H1 22px/700 (Montserrat, title/TOC header), H2 20px/600 (Poppins, major section headings), H3 18px/700 (Montserrat, sub-headings), body paragraph 15px/400 (Inter, `lineHeight: 22px`) with `strong` lead-ins at 15px/700. Distinct colors: post-meta text `#adadad`, related-post title `#e4e0da` (light, sits on a dark card background per the screenshot), share-button brand colors (Facebook `#3b5998`, Reddit `#ff4500`, X/Twitter black).

**Card/button measurements:** `cards: []` — the related-posts strip is visually card-like in the screenshot but wasn't picked up by the automated card-grid probe (likely a non-standard Elementor post-widget markup); no exact pixel dimensions available, would need direct inspection. No distinct button styling beyond the shared header CTA.

**Responsive behavior:** container `null` at every viewport, same 1200px Elementor-container candidates as other templates. The table-of-contents box and share-button row are the elements most likely to reflow at mobile (stacking vertically) based on the very tall, narrow full-page screenshot (`guide-getting-sick-on-the-loop/mobile.png`, ~18,000px tall at 390px width — a long-form article), though exact breakpoint behavior for the TOC box itself isn't independently measurable from the JSON.

---

### Contact

Screenshot folder: `docs/reference-screenshots/contact/`. Measurement folder: `contact/`.

**Section order**: two-column layout — left: info card (address, WhatsApp/phone, email, business hours, each with an icon) with light shadow; right: heading, intro copy, contact form (name, phone/WhatsApp number, email, and message fields, plus a "Send" button) → footer.

**Typography/color distinct from global:** form field labels use Montserrat 18px/700 (`.elementor-field-label`) — notably larger/bolder than the Motorbike Rental template's form labels (13.6px/500 DM Sans), so form-label styling is not consistent site-wide and a theme build will need to pick one convention rather than copying either literally. Icon-box description text (address/contact details) is Inter 16px/400-500.

**Card/button measurements:** no card grid on this page (`cards: []` — the info panel is a single icon-list block, not a repeating grid). "Send" button: 47px height, radius 7px, orange fill/white text, 20px/600 text, same hard-offset shadow (`rgb(54,52,59) 2px 3px 0px 0px`) used site-wide.

**Responsive behavior:** the two-column layout (info card | form) stacks to a single column at mobile, info card first then form, confirmed via `contact/mobile.png`. Container `null` at every viewport (candidates cap at 1200px, same Elementor-container pattern as other secondary pages).

---

### About

Screenshot folder: `docs/reference-screenshots/about-loop-trails-tours-ha-giang/`. Measurement folder: `about/`.

**Section order**: gradient hero banner (company name, tagline, license badge) → a 4-stat row (traveler count, years operating, review rating, guide-credential count) → a company-history narrative section → a Mission/Vision two-column callout → a services overview as a 6-item icon grid → a destinations tag-chip list (grouped by region) → a dark panel presenting operator licensing/compliance information with a license document image → a second 6-item icon grid covering quality-commitment points → a bullet list of reasons to book with the operator → a closing CTA band → Tripadvisor strip → footer.

**Typography/color distinct from global:** this is the one template where body/heading text runs on **Inter** almost throughout (`h2.section-title` 32px/500, `h4` service titles 17.6-24px, body paragraphs 15-15.2px/400) rather than the Montserrat/DM Sans mix seen on other pages — a real, page-specific typography choice worth flagging for the design-tokens pass rather than an artifact. Stat numbers are large and bold: 40px/700 Inter. Distinct colors: stat-card background `#f7f7f7` with `#e4e0da` border, destination-tag chip border `#dddddd`, legal panel background `#36343b` (dark, matches the header/menu-toggle dark tone used elsewhere), license-badge translucent white overlays (`rgba(255,255,255,0.1-0.3)`) sitting on the gradient hero.

**Card/button measurements:** two distinct grids — stats row (4 items, 266×116px desktop, radius 15px, `gapX: 25`, no shadow) and services grid (6 items, 363×238px desktop, radius 12px, `gapX: 25`, no shadow). The closing CTA button (a multi-word marketing phrase, not reproduced here): 52px height, radius 30px (pill), orange fill/white text, no shadow, 15px/400 text — a rounder, flatter style than the hard-shadow buttons used on the tour/booking-heavy pages.

**Responsive behavior:** this is one of the only secondary-page templates where `mostCommonContainerWidth` resolves to a real value (1200px, `candidateCount: 4`) at desktop — matching Home and Tours Archive rather than the `null` pattern seen on most other secondary pages. At mobile (`about-loop-trails-tours-ha-giang/mobile.png`) the stat row, services grid, and destination tags all reflow to narrower multi-item rows/stacks; exact column count at mobile isn't reliably inferable from the screenshot's resolution alone, but the underlying items themselves (`itemWidth` 330px range in the mobile JSON) indicate they're no longer laid out at their desktop widths.

---

### Terms & Privacy

Screenshot folder: `docs/reference-screenshots/terms-and-conditions/`. Measurement folder: `terms/`.

**Section order**: this is a single long-form legal document, not a composed page — eyebrow label, an H1 naming the combined terms/privacy document, a "last updated" date line, then roughly 15 numbered legal sections covering booking/payment terms, a cancellation policy with a fee table scaled by days-before-departure, risk/liability and customer-conduct topics, and a lettered privacy sub-policy appended at the end → a closing contact callout inviting questions about the terms/privacy content → a back-to-top link → footer.

**Typography/color distinct from global:** dedicated legal-doc type scale not used anywhere else: `h2.lt-legal__major` (Poppins 17px/600, section titles), `span.lt-num` (Montserrat 17px/700, section numbers), `h3` sub-headings (Montserrat 12px/700, 1.4px letter-spacing, uppercase-style), body copy `p.lt-legal__updated` (Inter 14.5px/400, 24.65px line-height — noticeably more generous leading than the 22px body line-height used elsewhere, appropriate for long-form reading). Cancellation-fee callouts use a warning red `#d63031` and a light gray callout background `#f7f7f7`.

**Card/button measurements:** no card grids and no page-specific buttons — `cards: []`, the only button is the shared header "Book Now". This matches the plan's spot-check finding that `terms/tablet.json` is "sparse but legitimate": at tablet (768px) the container-detection probe found zero candidates (`candidateCount: 0`), consistent with a plain single-column text template that doesn't use the site's usual Elementor-container markup at that breakpoint.

**Responsive behavior:** container `null`/absent at every viewport — this template is effectively a full-width text column with side padding at all sizes, no boxed max-width layout to speak of. Given the page is prose-only, this short write-up is intentionally proportional — a legal page needs far less design documentation than a card-grid archive page.

---

### 404

Screenshot folder: `docs/reference-screenshots/this-page-does-not-exist-xyz123/`. Measurement folder: `404/`.

**Section order**: two-column hero split — left: full-bleed photo (motorbike on a mountain road); right: a "404" status label, a large H2-level error message in a conversational/apologetic tone, one-line body copy with a link back to the homepage, search box with a "GO" button → footer (no other sections).

**Typography/color distinct from global:** this template breaks from the Montserrat/Inter/DM Sans mix used everywhere else and runs on **Rubik** for its own content (`h2` 55px/400, body 16px/400, "GO" button 15px/400) — a one-off font choice specific to this error page. The "404" eyebrow label uses an accent pink-red `#f40045`, distinct from the site's usual orange/pink accents.

**Card/button measurements:** no cards. "GO" search-submit button: 48px height, square corners (radius 0px), black fill/white text, same hard-offset shadow as other buttons (`rgb(54,52,59) 2px 3px 0px 0px`) — the one place on the site where a primary action button uses black instead of orange. Search input border `#dddddd`/`#e2e2e2`.

**Responsive behavior:** the two-column hero (photo | text+search) stacks to photo-on-top, text+search below at mobile (`this-page-does-not-exist-xyz123/mobile.png`). Container `null` at every viewport with only a single 1200px candidate detected even at desktop — the simplest container profile of any of the eight templates, consistent with this being the shortest/simplest page.

---

### Page Types With No Reference Data

Per spec §5 items 5 and 12 ("Blog archive, category/tag archive, search and single article" / "Generic search results, empty states and 404"), two of the listed page types were never captured during Milestone 1's screenshot pass or Milestone 2a's measurement pass:

- **Blog category/tag archive** — no reference screenshot or measurement JSON exists for a filtered archive view (e.g. `/category/health/` or `/tag/motorbike/`); only the unfiltered `/blog/` archive (documented above) and a single article were captured live.
- **On-site search results** — no reference screenshot or measurement JSON exists for a populated search-results page; the 404 template's search box (documented above) is the only search-related UI observed, and it was captured empty/unused.

Neither was observable on the live reference site during the audit window (Milestone 2a's plan noted this gap explicitly). Both will be built per spec §5 items 5 and 12 using standard WordPress archive/search template conventions (`archive.php`/category-tag templates and `search.php`) rather than a measured reference — in practice this means reusing the Blog Archive template's list layout and typography documented above (same post-list pattern, same typography scale) for category/tag archives, and a similar list layout with a "no results" empty state for search results, rather than inventing new unreferenced designs.


---

## Known Gaps (Spec §3 Self-Review)

Spec §3 requires this audit to document, for every page/section: screenshot filename and capture date; section order and anchor behavior; container width, gutters and vertical spacing; font family/weight/size/line-height/letter-spacing; exact visible colors in hex/rgba; card dimensions/columns/gaps/image-aspect-ratio/radius/border/shadow; button height/padding/radius/icon placement/hover/focus behavior; breakpoint behavior (hidden/stacked/scrollable/collapsed/sticky/reordered); and animation/transition type and duration.

Reviewing the four sections above against that list:

- **Screenshot filename and capture date** — covered. Every section cites its screenshot folder (e.g. `docs/reference-screenshots/home/`, `.../tours-ha-giang-loop-4-days-3-nights/`) and viewport filenames (`desktop.png`, `mobile.png`, etc.); capture date for the whole set is recorded once, above, rather than repeated per section.
- **Section order and anchor behavior** — covered for section order (documented per page: Global §4-5 for header/footer, Home §1, Tour Detail §1, and per-template in Secondary Pages). Anchor/in-page-jump behavior is documented where it exists (Tour Detail's sticky in-page nav, `.lt-nav-link`, Global §4/§5). No other template shows in-page anchor navigation in the captured data, which is treated as a real absence, not a gap.
- **Container width, gutters and vertical spacing** — **partially covered.** Container *width* is thoroughly measured per viewport and per template (Global §3; repeated per-template in Secondary Pages). Side *gutters* and inter-section *vertical spacing* were not independently quantified anywhere in the source data — Global §3 notes only that the container itself carries "zero side padding … side gutters come from elsewhere" without pinning down that value, and no section measures vertical rhythm between page sections. This is a real gap: gutters/vertical spacing will need direct DOM/CSS inspection (or a dedicated computed-style pass targeting section `padding`/`margin`) before `theme.json` spacing tokens can be finalized.
- **Font family, weight, size, line height, letter spacing** — covered. Global §1 gives the full typography scale including letter-spacing/tracking; page-specific type scales are documented per template in Tour Detail and Secondary Pages.
- **Exact visible colors (hex/rgba)** — covered extensively across all four sections (Global §2 for the sitewide/global palette; page- and component-specific palettes documented per template elsewhere).
- **Card dimensions, columns, gaps, image aspect ratio, radius, border, shadow** — covered for the great majority of card grids identified, with per-viewport values where the measurement tool captured them. A few grids were only captured at some viewports (noted as a tooling gap in Home §5, not a design difference) and a small number of card groups (e.g. Tour Detail's included/excluded list, related-tours grid) have dimensions but no measured border/shadow value — treated as "not observed" rather than assumed absent.
- **Button height, padding, radius, icon placement, hover/focus behavior** — **partially covered.** Height, padding, radius, fill/shadow are well measured for most primary/secondary CTA buttons across templates (see `docs/design-tokens.md`). Icon placement on buttons was not observed in any section — the buttons captured in this data all read as text-only (no icon-plus-label pattern was detected by the measurement probes or visible in screenshots at the resolution reviewed). Hover behavior is documented for exactly one button (Tour Detail's primary CTA, hover background `#e4e0da`); no other button's hover state was captured. Focus behavior is not documented anywhere — like animation, focus/hover states generally require live interaction (`:hover`/`:focus` capture), which this audit's static screenshots and single-snapshot computed-style JSON cannot provide. Treat icon placement, remaining hover states, and all focus states as unmeasured, not as "no icon" / "no focus style."
- **Breakpoint behavior (hidden/stacked/scrollable/collapsed/sticky/reordered)** — covered thoroughly: column-collapse patterns, single-column stacking, the testimonial carousel's scrollable (non-collapsing) behavior, and the tour-detail booking widget's sticky-then-stacked behavior are all documented with supporting per-viewport measurements.
- **Animation/transition type and duration — not covered; confirmed unmeasurable from this audit's data.** Milestone 1's capture procedure explicitly disabled animation during screenshot capture (spec §4) so that full-page screenshots render in a settled state, and Milestone 2a's measurement script reads a single static computed-style snapshot (colors, sizes, container widths) rather than live `transition`/`animation` CSS properties or interaction states (hover/focus timing, scroll-triggered reveals, the testimonial carousel's autoplay speed, FAQ accordion expand/collapse easing, tab-switch transitions). Nowhere above should be read as implying an animation/transition value for these behaviors — they are described only in terms of their static/structural layout. **This is a known, already-anticipated limitation of static-screenshot-based measurement, not an oversight.** Closing it requires a dedicated follow-up pass that reads computed `transition-property` / `transition-duration` / `transition-timing-function` / `animation-*` CSS values directly from the live reference site (e.g. a small Playwright script calling `getComputedStyle()` on the relevant elements, or manual browser DevTools inspection) — not a re-read of the screenshots or JSON already captured.

**Net finding:** every spec §3 item is addressed in this document, either with measured values or with an explicit, evidence-based note on why a value is not available. Three items carry a confirmed measurement gap rather than a full answer — gutters/vertical spacing, button icon-placement/hover/focus, and animation/transition — and each is called out above rather than filled in with an invented value.
