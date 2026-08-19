# Design Tokens

Consolidated from `docs/reference-audit.md` (Milestone 2b, Tasks 2-5): colors, typography and container widths are pulled from the Global section (`docs/reference-audit/00-global.md`); card/button radius, shadow and spacing values are pulled from the Home, Tour Detail and Secondary Pages sections (`01-home.md`, `02-tour-detail.md`, `03-secondary-pages.md`). Every value below is a measured number/hex code from `docs/design-measurements/*.json` — nothing here is invented. Token names are original and do not reuse the reference site's own CSS class or variable names (its `lt-`/`ltw-`/`hgl-` prefixes appear only in the "Source" column, for traceability). This file is input for a future `theme.json` author (Milestone 4); it is not itself a `theme.json`.

## Colors

### Global / sitewide

| Token name | Hex | Usage | Source |
|---|---|---|---|
| `color-primary` | `#ff6602` | Primary brand/accent orange — links, buttons, active states. Confirmed on Home, Tour Detail, About, Contact. | `00-global.md` §2 |
| `color-ink` | `#36343b` | Border/shadow "ink" — the hard offset-shadow color used on nearly every button and interactive card sitewide. | `00-global.md` §2, §4 |
| `color-text-body` | `#212121` | Default body text color (`body`). Top color by count on every template checked. | `00-global.md` §2 |
| `color-text-heading` | `#333333` | Secondary heading / dark text. | `00-global.md` §2 |
| `color-surface` | `#ffffff` | Page background. | `00-global.md` §2 |
| `color-accent-secondary` | `#e5396e` | Secondary accent (magenta/pink) — likely a link/hover accent; also reused as the blog post-link color. | `00-global.md` §2 |
| `color-surface-header-footer` | `#e4e0da` | Shared header/footer surface (cream/tan). On Home's header it is semi-transparent: `rgba(228,224,218,0.85)`. | `00-global.md` §2, §4, §5 |
| `color-surface-muted` | `#f7f7f7` | Light section background. Seen on Home and About only — not confirmed global; treat as a component/page choice. | `00-global.md` §2 |
| `color-border-default` | `#dddddd` | Divider/border gray on Home, About, Contact. Tour Detail uses `color-border-widget` (`#e2e8f0`) for the equivalent role instead — two coexisting component systems. | `00-global.md` §2 |
| `color-social-facebook` | `#1877f2` | Footer social icon. | `00-global.md` §2, §5 |
| `color-social-instagram` | `#e1306c` | Footer social icon. | `00-global.md` §2, §5 |
| `color-social-whatsapp` | `#25d366` | Footer social icon. | `00-global.md` §2, §5 |
| `color-social-tiktok` | `#000000` | Footer social icon. | `00-global.md` §2, §5 |
| `color-social-tripadvisor` | `#34e0a1` | Footer social icon. | `00-global.md` §2, §5 |

### Booking-widget component system (`hgl-`/`ltw-` markup) — scoped, not global

| Token name | Hex | Usage | Source |
|---|---|---|---|
| `color-widget-navy` | `#1a1a2e` | Booking panel navy. | `00-global.md` §2 |
| `color-widget-border` | `#e2e8f0` | Widget slate border (Tour Detail's equivalent of `color-border-default`). | `00-global.md` §2 |
| `color-widget-border-alt` | `#e2e4e9` | Widget slate border, alternate value (also used as FAQ item border on Tour Detail). | `00-global.md` §2 |
| `color-widget-surface-1` | `#f8fafc` | Widget slate background. | `00-global.md` §2 |
| `color-widget-surface-2` | `#f1f5f9` | Widget slate background. | `00-global.md` §2 |
| `color-widget-surface-3` | `#f9fafb` | Widget slate background. | `00-global.md` §2 |
| `color-widget-surface-4` | `#f3f4f6` | Widget slate background. | `00-global.md` §2 |
| `color-widget-text-1` | `#4a5568` | Widget slate text. | `00-global.md` §2 |
| `color-widget-text-2` (= `color-text-muted-widget`) | `#64748b` | Widget slate text; also the "muted gray" used on Tour Detail's newer `ltw-` components (e.g. day-description text). | `00-global.md` §2; `02-tour-detail.md` |
| `color-widget-text-3` | `#94a3b8` | Widget slate text. | `00-global.md` §2 |
| `color-widget-text-4` | `#1e293b` | Widget slate text. | `00-global.md` §2 |
| `color-widget-text-5` | `#1f2937` | Widget slate text; also the Tour Detail booking-widget's dominant tour-name-label color. | `00-global.md` §2; `02-tour-detail.md` |
| `color-widget-text-6` | `#374151` | Widget slate text. | `00-global.md` §2 |
| `color-widget-text-7` | `#111827` | Widget slate text. | `00-global.md` §2 |
| `color-text-muted-legacy` | `#6b696f` | Muted gray text on Home's older `lt-` components. Not the same value as `color-text-muted-widget` — keep as two separate tokens. | `00-global.md` §2 |
| `color-status-success` | `#10b981` | Green — discount amounts; also the itinerary meal-indicator badge background. | `00-global.md` §2; `02-tour-detail.md` |
| `color-status-warning` | `#f59e0b` | Amber — stars/rating, also an availability-legend dot color. | `00-global.md` §2; `02-tour-detail.md` |
| `color-status-warning-alt` (= `color-rating-star`) | `#fbbf24` | Amber — alternate value; also the rating-star glyph color on Tour Detail. | `00-global.md` §2; `02-tour-detail.md` |
| `color-status-error` | `#ef4444` | Red — required-field indicator. | `00-global.md` §2 |
| `color-status-neutral` | `#9ca3af` | Grey — availability-legend "unavailable" dot on Tour Detail's booking calendar. | `02-tour-detail.md` |

### Card/callout accents (Tour Detail)

| Token name | Value | Usage | Source |
|---|---|---|---|
| `color-callout-info-bg` | `rgba(59,130,246,0.08)` | Info callout background (itinerary weather/road-conditions note). | `02-tour-detail.md` |
| `color-callout-info-border` | `rgba(59,130,246,0.2)` | Info callout border, paired with the above. | `02-tour-detail.md` |
| `color-callout-success-border` | `rgba(16,185,129,0.25)` | "Included" list positive-variant container border. | `02-tour-detail.md` |
| `color-faq-toggle-tint` | `#fed7aa` | FAQ accordion toggle-icon border tint. | `02-tour-detail.md` |
| `color-nav-link-active-bg` | `#fff7ed` | Sticky in-page nav, active tab background. | `02-tour-detail.md` |

**Not included as tokens:** one-off accent colors that appear on a single secondary-page template only (e.g. Tours Archive's price red, Motorbike Rental's card border, 404's accent pink-red, Blog Single's share-button brand colors) are documented in their own template's write-up in `docs/reference-audit.md` (Secondary Pages section) but are out of scope for this consolidated table per this task's brief, which scopes the Colors table to the Global section's palette. Pull them directly from the relevant Secondary Pages sub-section if and when that specific template is built.

## Typography

Desktop values from the 1440px viewport, mobile values from the 390px viewport (`00-global.md` §1). Most component-level type is set in **fixed** pixel values that do not change across breakpoints — only `font-size-h1` and `font-size-h2` are fluid; see `docs/reference-audit.md` (Global §1) for the full 5-viewport progression of those two styles.

| Token name | Font family | Size (desktop) | Size (mobile) | Weight | Line height (desktop) | Letter spacing (desktop) |
|---|---|---|---|---|---|---|
| `font-size-h1` | Montserrat | 60px | 32px | 800 | 64.8px | -1px |
| `font-size-h2` | Montserrat | 22px | 18px | 700 | 27.5px | -0.3px |
| `font-size-h3-card-title` | Poppins | 18px | 18px (fixed) | 600 | 21.96px | -0.2px |
| `font-size-h3-review-title` | Poppins | 16px | 16px (fixed) | 600 | 20.8px | -0.2px |
| `font-size-h4-card-title` | Poppins | 18px | 18px (fixed) | 600 | 21.6px | -0.2px |
| `font-size-body` | Inter | 14.5px | 14.5px (fixed) | 400 | 23.925px | not reported (no tracking value recorded for this row) |
| `font-size-body-emphasis` | Inter | 14.5px | 14.5px (fixed) | 600 | 23.925px | not reported |
| `font-size-pill-label` | Montserrat | 10.5px | 10.5px (fixed) | 700 | 16.275px | 0.8px |
| `font-size-form-label` | DM Sans | 14.4px | 14.4px (fixed) | 600 | 18px | not reported |
| `font-size-button-card` | Montserrat | 11px | 11px (fixed) | 700 | 11px | 0.8px |
| `font-size-price-value` | Inter | 14px | 14px (fixed) | 700 | 21.7px | -0.1px |
| `font-size-caption` | Inter | 11.5px | 11.5px (fixed) | 400 | 13.8px | not reported |

**Secondary font families observed on specific templates** (not part of the global scale above, documented per-template in `docs/reference-audit.md`'s Secondary Pages section): DM Sans as the dominant UI font on Motorbike Rental; Inter as the dominant heading/body font on About; Rubik on the 404 page; Open Sans on the footer's bottom/copyright bar only.

## Spacing / Radius / Shadow

### Container widths

| Token name | Value | Usage | Source |
|---|---|---|---|
| `container-max-width` | 1200px | Boxed content width at desktop (1440px) and laptop (1280px) viewports; confirmed on Home, Tour Detail, About, Tours Archive. | `00-global.md` §3 |
| `container-max-width-tablet` | 760px (CSS `max-width`); renders at ~671px | Single lower-confidence reading at 768px — treat as tentative pending direct verification, not a confirmed sitewide tablet container. | `00-global.md` §3 |
| `container-mobile` | none (full-bleed with side padding) | Confirmed real behavior at 390px and 360px — the container constraint does not bind at these widths; do not fabricate a container width here. | `00-global.md` §3 |

### Card radius

| Token name | Value | Usage | Source |
|---|---|---|---|
| `radius-card-default` | 14px | Home's `lt-` card family: featured tour grid, "why choose us" grid, destination cards grid. | `01-home.md` §3.1-3.4 |
| `radius-card-day-image` | 10px | Tour Detail's per-day 3-image row. | `02-tour-detail.md` §1 |
| `radius-card-product` | 12px | Motorbike Rental's bike-selection cards; About's services icon grid (same value reused across two unrelated templates). | `03-secondary-pages.md` (Motorbike Rental, About) |
| `radius-card-stat` | 15px | About's 4-item stats row. | `03-secondary-pages.md` (About) |
| `radius-card-flat` | 0px | Ambiguous source: the one `lt-overview-text` "cards" entry on Tour Detail, more likely 3 stacked Overview paragraphs mis-detected as cards than a real "quick facts" component — see `docs/reference-audit.md` (Tour Detail §1, "Destination highlights / overview / quick facts") before reusing. | `02-tour-detail.md` §1 |

### Button radius

| Token name | Value | Usage | Source |
|---|---|---|---|
| `radius-button-standard` | 7px | Site-wide primary/secondary CTA buttons: header/hero "Book Now", Tour Detail's booking CTA, Motorbike Rental's "Rent Now", Contact's "Send". | `02-tour-detail.md`, `03-secondary-pages.md` (Motorbike Rental, Contact) |
| `radius-button-pill` | 25px | Tours Archive's "Book Now" / "View Details" pill buttons. | `03-secondary-pages.md` (Tours Archive) |
| `radius-button-pill-large` | 30px | About's closing CTA band button. | `03-secondary-pages.md` (About) |
| `radius-button-square` | 0px | 404 page's "GO" search-submit button — the one place a primary action button departs from the rounded/pill styles used elsewhere. | `03-secondary-pages.md` (404) |

### Shadow

| Token name | Value | Usage | Source |
|---|---|---|---|
| `shadow-button-hard-offset` | `2px 3px 0px 0px` `color-ink` (`#36343b`) | The site's signature hard offset shadow (no blur) — used on nearly every button across every template reviewed (header nav button, Tour Detail CTA, Motorbike Rental buttons, Contact's "Send", 404's "GO"). | `02-tour-detail.md`, `03-secondary-pages.md` |
| `shadow-button-soft-glow` | `0px 4px 15px 0px rgba(255,102,2,0.3)` | Tours Archive's "Book Now" pill button — a soft colored glow, not the hard-offset style used elsewhere. | `03-secondary-pages.md` (Tours Archive) |
| `shadow-button-soft-ring` | `0px 0px 0px 3px rgba(255,102,2,0.15)` | Motorbike Rental's selected-bike-card ring (a spread-only "shadow" used as a selection indicator, not a drop shadow). | `03-secondary-pages.md` (Motorbike Rental) |
| `shadow-card-none` | none | Default for most card grids: featured tour grid, "why choose us" grid, stats bands, About's services grid — explicitly confirmed `boxShadow: none`, not an omission. | `01-home.md` §3.1-3.3; `03-secondary-pages.md` (About) |

### Grid gap

| Token name | Value | Usage | Source |
|---|---|---|---|
| `gap-grid-standard` | 22px | Desktop/laptop card grids: featured tour grid, "why choose us" grid, testimonial carousel. | `01-home.md` §3.1, §3.2, §3.5 |
| `gap-grid-tablet` | 18px | Tablet/mobile-width card grids: destination cards, testimonial carousel below desktop. | `01-home.md` §3.4, §3.5 |
| `gap-grid-wide` | 30px | Blog/articles teaser cards. | `01-home.md` §3.6 |
| `gap-grid-tight` | 12px | Tour Detail's per-day image row; Motorbike Rental's bike-selection grid. | `02-tour-detail.md` §1; `03-secondary-pages.md` (Motorbike Rental) |
| `gap-grid-transport-option` | 16px | Transport/bus-transfer option card grid (`hgl-grid hgl-grid-2` pattern, 4 items, 2-column at desktop/laptop/tablet collapsing to 1 at mobile/narrow-mobile) — a real reusable "Transport/Bus Option Card" component, corroborated by `docs/component-inventory.md`'s independent `hgl-bus-col-title` signature match, not a generic form field-pair layout. Reused on Tours Archive's equivalent bus-transfer option row. | `01-home.md` §3.7; `03-secondary-pages.md` (Tours Archive) |
| `gap-grid-loose` | 25px | About's stats row and services grid. | `03-secondary-pages.md` (About) |
| `gap-grid-none` | 0px | Home's stats band (`lt-stats`) — edge-to-edge/border-divided blocks, not gapped cards. | `01-home.md` §3.3 |

### Button height (representative values by template — not a single sitewide constant)

| Token name | Value | Usage | Source |
|---|---|---|---|
| `button-height-nav` | 35px | Header "Book Now" nav-bar button, consistent across templates. | `00-global.md` §4; `02-tour-detail.md` §1 |
| `button-height-cta` | 47-59px (varies: Tour Detail 59px, Motorbike Rental 47-55px, Contact 47px) | Primary in-page/widget CTA buttons — height is not a fixed constant across templates; record the specific value per template when building. | `02-tour-detail.md` §1; `03-secondary-pages.md` (Motorbike Rental, Contact) |
| `button-height-pill` | 44px | Tours Archive's "Book Now" / "View Details" pill buttons. | `03-secondary-pages.md` (Tours Archive) |
| `button-height-cta-round` | 52px | About's closing CTA band button. | `03-secondary-pages.md` (About) |
| `button-height-search` | 48px | 404 page's "GO" search-submit button. | `03-secondary-pages.md` (404) |

**Not measurable from this audit (see `docs/reference-audit.md`, "Known Gaps"):** button padding beyond the one recorded `padding-top: 15px` value (Tour Detail's CTA), icon placement on buttons, hover states beyond the one recorded Tour Detail CTA hover background, all focus states, inter-section vertical spacing, and side gutters below the measured container width. Do not invent values for these — measure them directly against the live reference site (or its DOM/CSS) before encoding them in `theme.json`.
