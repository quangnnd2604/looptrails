# Global

Source data: `docs/design-measurements/home/*.json` (all 5 viewports: desktop 1440px, laptop 1280px, tablet 768px, mobile 390px, narrow-mobile 360px), cross-checked against `docs/design-measurements/tour-detail/desktop.json`, `docs/design-measurements/about/desktop.json`, and `docs/design-measurements/contact/desktop.json`. Visual context from `docs/reference-screenshots/home/*.png`. All values below are measured computed-style output (px sizes, hex colors) or my own descriptive wording — no reference-site prose is quoted.

## 1. Typography Scale

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

## 2. Color Palette

Colors with `count >= 5` on Home desktop, cross-checked against `tour-detail/desktop.json`, `about/desktop.json`, and `contact/desktop.json` to separate true global tokens from page/component-specific ones.

### Global palette (confirmed present, by similar role, across all four cross-checked templates)

| Role | Hex | CSS property | Cross-template evidence |
|---|---|---|---|
| Primary brand / accent (orange) | `#ff6602` | color / backgroundColor | Home (51 color + 25 bg), tour-detail (38 + 17), about (13 + 2), contact (4 + 2) |
| Ink / shadow-and-border accent | `#36343b` | color / borderTopColor | Present as the border/shadow color on every measured button (`boxShadow: rgb(54, 52, 59) …`) in Home, tour-detail, and elsewhere; also the button-shadow ink used sitewide |
| Body text | `#212121` | color (on `body`) | The dominant `body`-level text color on every template checked: Home (206), tour-detail (165), about (150), contact (113). Note this is not necessarily the single highest-count color entry on every template — e.g. on Home the menu-toggle ink `#36343b` outranks it (count 555), and on tour-detail the widget slate `#1e293b` outranks it (count 189) — but `#212121` is the one consistently set as the base body text color across all four. |
| Secondary heading / dark text | `#333333` | color | Home (17), about (30), contact (12), tour-detail (16) |
| Page background | `#ffffff` | backgroundColor (on `body`) | Present on all four templates |
| Secondary accent (magenta/pink) | `#e5396e` | color | Home (41), tour-detail (35), about (26), contact (24) — consistently present, likely a link/hover accent |
| Header/footer surface (cream) | `#e4e0da` | backgroundColor | Home, tour-detail, about, contact all show this on the shared header (`elementor-element-27d9`, semi-transparent at `rgba(228,224,218,0.85)` on Home) and shared footer (`elementor-element-50aa`) sections — the matching Elementor element IDs across templates confirm this is the same reused header/footer template part, not a page-specific match |
| Social icon brand colors | `#1877f2` (Facebook), `#e1306c` (Instagram), `#25d366` (WhatsApp), `#000000` (TikTok), `#34e0a1` (Tripadvisor) | color | Identical set present in Home, tour-detail, and about's color arrays — confirms the footer's social-icon row is shared sitewide |

### Page/component-specific — do not treat as global tokens

- **Light section background** `#f7f7f7` — seen on Home (count 20) and about (count 11), but absent from the tour-detail and contact samples checked. Likely a section-background choice used on specific page templates, not a universal surface color.
- **Divider/border gray** `#dddddd` — seen on Home (95) and about (22) and contact (4), but not in the tour-detail sample. Tour-detail instead uses `#e2e8f0` for its equivalent border role (see below) — evidence of two coexisting component systems.
- **"Widget" / booking-form palette** — a visually distinct Tailwind-style palette appears only inside booking-widget markup (`hgl-*` classes on Home, `ltw-*` classes on tour-detail), not on the simpler about/contact templates: navy `#1a1a2e` (booking panel), slate borders `#e2e8f0`/`#e2e4e9`, slate backgrounds `#f8fafc`/`#f1f5f9`/`#f9fafb`/`#f3f4f6`, slate text `#4a5568`/`#64748b`/`#94a3b8`/`#1e293b`/`#1f2937`/`#374151`/`#111827`, plus status colors `#10b981` (green/discount), `#f59e0b`/`#fbbf24` (amber/stars), `#ef4444` (red/required). This is a component-scoped design system for the booking/tour-widget UI, not a site-wide palette — reuse it only when rebuilding the booking widget, not for general page chrome.
- **Muted gray text** has two distinct, non-identical values depending on component generation: `#6b696f` on Home's older `lt-` components vs. `#6b7280` on tour-detail's newer `ltw-` components. Treat these as two separate tokens tied to their respective component families, not one global "muted text" color.
- **Data quirk (not a global-vs-specific finding):** `contact/desktop.json`'s `container` block reports `mostCommonContainerWidth: null` at the 1440px desktop viewport, even though its own `topCandidates` list two elements at `maxWidth: 1200` / `renderedWidth: 1200`. This looks like a threshold/consensus quirk in the Milestone 2a measurement script for that specific page (which has very few container candidates), not evidence that the 1200px container doesn't apply on Contact at desktop width.

## 3. Container / Breakpoint Behavior

`container` field per viewport, from Home (all 5 viewports), cross-checked against tour-detail, about, and contact desktop:

| Viewport | Viewport width | `mostCommonContainerWidth` | Notes |
|---|---|---|---|
| Desktop | 1440px | **1200px** | Confirmed identically on Home, tour-detail, and about (all report `maxWidth: 1200`, `renderedWidth: 1200`, zero side padding on the container element itself — side gutters come from elsewhere). |
| Laptop | 1280px | **1200px** | Same 1200px container persists down to 1280px — the container does not need to shrink until the viewport itself is narrower than 1200px plus its own side clearance. |
| Tablet | 768px | **671px** (rendered); CSS `maxWidth` on the matched elements is 760px | The three top candidates at this viewport are per-section header wrapper `<div>`s (e.g. tour-list section header, info section header), not the page-level `.elementor-container` seen at desktop/laptop — a different DOM element became the "best candidate" at this width, and its rendered box (671px) is narrower than its own CSS max-width (760px), likely due to nested padding/margins on that specific element rather than a page-wide boxed-content width. Treat 671px as a lower-confidence data point compared to the clean 1200px reads at desktop/laptop. |
| Mobile | 390px | **`null`** (0 candidates) | Confirmed real behavior, not missing data. |
| Narrow-mobile | 360px | **`null`** (0 candidates) | Confirmed real behavior, not missing data. |

**Why mobile/narrow-mobile are `null`:** per Milestone 2a's finding, the site's container `max-width` constraint (the ~1200px/760px boxed-content rule visible at wider viewports) simply doesn't bind at these narrow widths — there is no element on the page whose rendered width is measurably narrower than the viewport itself in a way that qualifies as a distinct "container." Content is effectively full-width with side padding at 390px and 360px, so there is no boxed container width to report. This is not a capture failure; do not fabricate a container width for these two viewports when rebuilding — instead replicate full-bleed content with side padding at these breakpoints.

## 4. Header Structure

Based on `docs/reference-screenshots/home/desktop.png`, `tablet.png`, and `mobile.png`, plus the `buttons` array in each viewport's JSON:

- **Logo:** wordmark positioned top-left of the header bar, present at every viewport in the screenshots checked (desktop, tablet, mobile).
- **Header surface:** a semi-transparent cream panel (`rgba(228, 224, 218, 0.85)`, i.e. `#e4e0da` at 85% opacity) sits over the hero photo on Home. The section carries the same Elementor element ID (`elementor-element-27d9`) on Home, tour-detail, about, and contact — strong evidence this is one shared/global header template part reused across the whole site, not independently built per page.
- **"Book Now" CTA placement:** a pill-style button (white background, orange text, hard drop-shadow using the ink color `#36343b`) sits at the right side of the header bar on every viewport checked. It is the first entry in every viewport's `buttons` array (height ~35px on desktop/laptop, growing to ~33–36px with a larger 15–16px font on mobile/tablet — a deliberately larger tap target rather than a literal proportional scale-down).
- **Nav item order:** not confidently determinable from the screenshots at the resolution captured, or from the typography JSON (see caveat in §1). What is confirmed, though: a hamburger toggle icon is visible in `docs/reference-screenshots/home/desktop.png` at the full 1440px desktop width — this is not a narrow-viewport-only affordance. There is no separate horizontal nav-link row visible at any captured width; the header structure appears to be logo + "Book Now" pill + hamburger toggle at every viewport (desktop, laptop, tablet, mobile, narrow-mobile alike), with the actual menu items revealed only once the toggle is interacted with. This is corroborated by the color data: `div.elementor-menu-toggle`'s color (`#36343b`) is Home desktop's single highest-count color entry (count 555), consistent with an icon that renders identically at every breakpoint rather than one that only appears below a specific width. Exact nav item order/content is still not determinable from static screenshots alone — verify against Task 1's component inventory or the live reference site.
- **Sticky behavior:** requires direct interaction testing (scroll behavior in a live browser), not inferable from a static screenshot or a single-snapshot computed-style JSON. Do not assume sticky/fixed positioning without checking the live reference site or documenting it as unverified.

## 5. Footer Structure

Based on the same screenshots and the color-array evidence for the shared cream surface color (`#e4e0da`): tour-detail and about each record their footer background under a specific Elementor element ID, `elementor-element-50aa` (tag `footer`, `samples: ["footer"]`, count 1). Home and contact don't have a `50aa`-tagged entry, but each still shows `"footer"` inside the `samples` array of their own `#e4e0da` color entry — contact's entry is recorded under the header's own ID (`elementor-element-27d9`, `samples: ["header", "footer"]`), and Home's plain (non-alpha) entry is recorded under an unrelated representative class (`elementor-alert`, `samples: ["div", "footer"]`). The measurement tool appears to log one representative element per unique computed-style bucket, so the specific ID captured differs by template even when "footer" is present in the same bucket's samples every time. Net effect: all four templates confirm the same cream footer background color, even though only tour-detail and about expose it under a literal, dedicated footer element ID — treat the shared color (not a single universal element ID) as the evidence for one global footer surface.

- **Layout:** at the screenshot resolutions available, the footer reads as a single centered content block rather than an obvious multi-column grid: a logo mark, a short contact/tagline line, then a row of social-platform icons, then a visually separated bottom bar carrying a copyright line. This is a visual read at reduced scale, not a pixel-measured column count — confirm exact column structure (if any) against Task 1's component inventory or the live reference site before building.
- **Social icon row:** confirmed present and identical across templates via color data — Facebook (`#1877f2`), Instagram (`#e1306c`), WhatsApp (`#25d366`), TikTok (`#000000`), Tripadvisor (`#34e0a1`) — each icon rendered in its platform's brand color, appearing in Home, tour-detail, and about's color arrays.
- **Surface color:** `#e4e0da` (cream/tan), same token used on the header, giving header and footer a matching surface color across the whole site.
- **Bottom bar:** a visually distinct, smaller-type line sits below the main footer content (measured at 12px, weight 400, using the `"Open Sans"` font family — the only place this font family appears in the typography data), separated from the rest of the footer content, consistent with a standard copyright/legal bar.
