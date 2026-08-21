# Milestone 4 Visual Acceptance Report

Originally captured 2026-08-20; **fully re-measured 2026-08-21** after the final whole-branch review's fix wave (see "What changed in the 2026-08-21 re-run" below — every number in this document is from the re-run, none are carried over).

Reference: `docs/reference-screenshots/home/*.png` (Milestone 1, full-page). Local: `docs/reference-screenshots/local-m4/*.png` (Milestone 4, `http://localhost/looptrails/`, theme `tour-reference-theme` active).

Pipeline, in required run order:

```
node tools/local-audit/measure-reference.mjs     # live reference geometry -> reference-regions.json
node tools/local-audit/capture-local.mjs         # local screenshots + geometry -> regions.json
node tools/local-audit/crop-reference.mjs        # both sides cropped to the SAME reference-derived region
node tools/local-audit/diff.mjs header           # raw + masked pixel diff
node tools/local-audit/diff.mjs footer
node tools/local-audit/check-colors.mjs
node tools/local-audit/check-metrics.mjs
node tools/local-audit/check-section-position.mjs
node tools/local-audit/check-overflow.mjs
```

(`npm run audit` in `tools/local-audit/` runs everything except `measure-reference`, which hits the live reference site and is only re-run when the reference itself may have changed.)

## Critical bug found and fixed before the first run

Before any meaningful capture could happen, activating the theme and loading the home page showed a **completely empty `<header>` and `<footer>`** — `<header class="wp-block-template-part">\n</header>` with zero children, confirmed via raw `curl` output (not a CSS/rendering artifact). Root cause: `parts/header.html` and `parts/footer.html` (added in Tasks 2 and 3) each wrapped their own content in a second, self-referential `<!-- wp:template-part {"slug":"header",...} -->` block comment, in addition to `templates/index.html`'s own (correct) reference to the same slug. WordPress's `render_block_core_template_part()` (`wp-includes/blocks/template-part.php` lines 130–157) keeps a static `$seen_ids` recursion guard keyed by slug; the part-file's own nested self-reference hit that guard on the second pass and silently rendered as `''`. This produced **zero PHP warnings or fatal errors** and **zero PHPUnit failures**, because the tests only ran `assertStringContainsString()` against the raw file text — a check that passes identically whether the block engine can render that text or not.

Fix: removed the erroneous outer `wp:template-part` wrapper from both part files. Verified via `curl` that both regions now render their full intended markup.

This was the second of **three** rendering-time bugs this milestone that were 100% invisible to string-match tests (the others: the footer icon-colour context-inheritance bug, and the missing `<main>` landmark found by the final review). The suite now carries `do_blocks()`-based rendering assertions that guard all three — see "What changed in the 2026-08-21 re-run".

**Environment note:** the live site at `http://localhost/looptrails/` is served from `C:\xampp\htdocs\looptrails\wp-content\themes\tour-reference-theme\`, an **untracked, manually-synced copy** separate from this worktree's copy (git worktrees do not share working-directory files). Both copies were verified byte-identical (except the gitignored `.phpunit.result.cache`) immediately before the 2026-08-21 re-run, so the numbers below measure the same code that is committed.

## Region-mismatch fix — two rounds

**Round 1 (Task 6).** The brief's literal `diff.mjs` compared a local header/footer crop against the *raw, full-page* Milestone-1 reference PNG via `pixelmatch`'s `Math.min(width, height)` sizing. Because `Math.min` always takes the smaller (local) height, it silently diffed only the **first N rows of the reference's full page** against the local footer crop, regardless of `REGION`. Fixed by adding `crop-reference.mjs` to pre-crop the reference PNG.

**Round 2 (final review, finding C2).** That first crop used the **local** element's height. The reference footer is 526–622px tall (wordmark, address, hotline, email, website link, legal block, compliance badge, 5-icon social row); the local footer is 192–231px. "The bottom 192px of the reference page" was therefore the reference footer's **bottom third** being diffed against the local footer's **entire** content — the same class of bug, one level subtler, and the reason the old report's footer numbers (3.36% / 3.70% / 5.87%, the only in-tolerance pixel numbers in the whole report) looked like passes: they were mostly two flat cream backgrounds overlapping. **Those numbers are withdrawn.**

Round 2's fix:

- New `tools/local-audit/measure-reference.mjs` visits the live reference site with Playwright and records the **reference's own** header/footer element geometry per viewport. Element selection is explicit rather than `.first()`: the reference is an Elementor build carrying a 0px-tall `<header class="elementor-location-header">` wrapper, the real `<header id="headerScroll">` fixed bar, and half a dozen mid-page `<header class="lt-*__head">` section headings. The rule is "tallest full-bleed `<header>` whose top edge is within the first 120px of the page" (and the bottom-anchored equivalent for the footer), with a hard throw if the measured height is 0 — the first draft of the script silently recorded `headerHeight: 0` for every viewport.
- `crop-reference.mjs` now crops **both** images to the **reference-derived** region height, out of the full-page PNGs on both sides:
  - header: rows `[0, refHeaderHeight)` of each page (both headers are top-anchored at page y = 0);
  - footer: `refFooterHeight` rows starting at each page's **own** footer top edge — the reference's from the bottom of the reference PNG (that PNG is an older M1 capture whose total page height no longer matches the live page, but the footer is bottom-anchored in both), the local's from `regions.json`'s real DOM box.
  - Where the local page has fewer rows than the reference region needs, the shortfall is left transparent and **counted as mismatch**, and the padded row count is reported (see the table below) — the height delta is deliberately allowed to show in the numbers rather than being hidden by shrinking the region.
  - A width-assert guard now throws before `PNG.bitblt` if the reference and local captures disagree on width (a stride mismatch would silently produce a sheared crop).
- `diff.mjs` reports **both** a raw and a masked percentage (see next section) and throws on any dimension mismatch.

### Comparison regions actually used

Heights below are **real DOM element heights** measured via Playwright `boundingBox()` on both sides (`measure-reference.mjs` for the reference, `capture-local.mjs` for the local render). *(The equivalent table in the pre-2026-08-21 version of this report labelled its figures as `boundingBox()` values when they were in fact PNG pixel heights read back off the captured element screenshots — that mislabel is corrected here, and these figures genuinely are `boundingBox()` measurements.)*

| Viewport | Reference header | Local header | Reference footer | Local footer | Comparison region (header × footer) | Local rows padded in footer region |
|---|---|---|---|---|---|---|
| desktop (1440) | 66px | 59px | 528px | 192px | 66px × 528px | 0 |
| laptop (1280) | 66px | 59px | 528px | 192px | 66px × 528px | 0 |
| tablet (768) | 91px | 59px | 526px | 192px | 91px × 526px | 0 |
| mobile (390) | 61px | 59px | 586px | 209px | 61px × 586px | 22 |
| narrow-mobile (360) | 61px | 59px | 622px | 231px | 61px × 622px | 102 |

Two things this table makes visible that the old local-height crop hid entirely:

1. **The local header is 2–32px shorter than the reference header** at every viewport (7px at desktop/laptop, 32px at tablet, 2px at mobile widths). Cropping to the local 59px previously made this delta invisible; it is now a real contributor to the header diff (a 7px band across a 66px region is ~10.6% of the region on its own; the tablet's 32px band across 91px is ~35%).
2. **The reference footer holds 2.5–3× the content of this milestone's footer** (528px vs 192px at desktop). That is the footer scope reduction disclosed in the plan (spec §5.5's contact/legal/payment/newsletter surface deferred to the milestone that adds the site-wide settings API), now quantified rather than described.

## Masking (spec §4)

Spec §4 measures the pixel diff **post-masking** and explicitly allows the logo to differ. Masking only became meaningful once the header actually had a site-identity element to mask — before the fix wave the header rendered nothing in that slot (see "What changed" item I2), so there was nothing to mask and only a raw number was reported.

`diff.mjs` now zeroes the site-identity region to opaque black on **both** images before running `pixelmatch`. The masked rectangle is the **union** of the reference's branding box and the local branding box (plus 2px slack), so the *identical* region is blanked on both sides — masking different regions per side would itself manufacture a difference. The masked denominator remains the full region area (masked pixels count as matching, not as removed), which keeps `masked ≤ raw` and makes the two directly comparable.

Masked regions actually applied (crop-relative):

| Viewport | Header mask | Footer mask |
|---|---|---|
| desktop | 189×39 @ (18,13) | 134×30 @ (653,38) |
| laptop | 189×39 @ (18,13) | 134×30 @ (573,38) |
| tablet | 189×47 @ (18,18) | 124×28 @ (322,38) |
| mobile | 136×31 @ (13,15) | 114×26 @ (138,33) |
| narrow-mobile | 124×31 @ (13,15) | 114×26 @ (123,33) |

The footer mask covers the reference's footer wordmark only: the local footer's `wp:site-logo` renders empty (no `custom_logo` theme mod), so there is no local branding box to union with it.

## Results by viewport

| Viewport | Header diff (raw) | Header diff (masked) | Footer diff (raw) | Footer diff (masked) | Container edges within tolerance? | Font metrics within tolerance? | Horizontal overflow at 360px? |
|---|---|---|---|---|---|---|---|
| desktop (1440) | 16.38% | **14.94%** | 63.13% | **63.02%** | yes (full-width bar, 0px edge delta; no overflow) | yes (14.5px / 700 / height 35px / radius 25px) | n/a |
| laptop (1280) | 16.51% | **14.89%** | 63.06% | **62.94%** | yes | yes (14.5px / 700 / 35px / 25px) | n/a |
| tablet (768) | 44.12% | **40.46%** | 62.57% | **62.32%** | yes | yes (15.5px / 700 / 35px / 25px) | n/a |
| mobile (390) | 13.68% | **10.35%** | 62.84% | **62.51%** | yes | yes (15.5px / 700 / 35px / 25px) | no (`scrollWidth === clientWidth === 390`) |
| narrow-mobile (360) | 13.47% | **10.19%** | 61.32% | **60.98%** | yes | yes (15.5px / 700 / 35px / 25px) | no (`scrollWidth === clientWidth === 360`, `check-overflow.mjs`) |

"Container edges" means: both local and reference header/footer render as full-viewport-width bars (not the 1200px boxed content container, which M1 documented as not applying to header/footer either) — confirmed 0px edge delta and zero horizontal scroll at every viewport via `check-overflow.mjs`.

**Overall average across all 10 measurements (5 viewports × header + footer): 41.71% raw, 40.26% masked — far above spec §4's <8% target. This is reported as a fail, not massaged into a pass.**

Header-only average: 20.83% raw / 18.17% masked. Footer-only average: 62.58% raw / 62.35% masked.

### Why these numbers went **up** versus the pre-fix-wave report, even though the theme got better

This is important and must not be misread. The previous report's numbers (header 9.50%–35.11%, footer 3.36%–10.09%, overall 13.80%) were produced by cropping the reference to the **local** element's height — i.e. by comparing the wrong regions. The fix-wave changes made the *theme* measurably closer to the reference (site-title fallback restored the header's `space-between` layout, button font-size now matches at touch widths, social icons now match the reference's bare-icon style, all 11 measured colours are now exact), while the *measurement* stopped flattering itself. Concretely:

- The footer number rose from ~3–10% to ~61–63% because it is now honestly comparing this milestone's 192px footer against the reference's 528px footer. The old number was two cream backgrounds overlapping. **The old number was wrong; the new number is right and it is a fail.**
- The tablet header number rose from 17.36% to 44.12% because the reference tablet header is 91px tall and the local one is 59px — a 32px band (35% of the region) that the old 59px crop simply never looked at.
- The desktop/laptop header numbers rose ~7 points for the same reason at 7px scale.

Every other measured criterion **improved**, several from fail to pass — see the criteria section below.

## Remaining spec §4 criteria (line height, colour channels, radius/gap/padding, section positions)

**Line height within 2px.** The Book Now button's `line-height` computes to the CSS keyword `normal` at every viewport (never explicitly overridden by the theme) — per the CSS Values spec, `normal` *is* the computed value, so `getComputedStyle` never resolves it to a px number. Measured the used value instead via an isolated same-font probe span (`check-metrics.mjs`):

| Viewport | Resolved line-height (local) | Reference line-height |
|---|---|---|
| desktop (1440) | 18px | not captured by M1/M2 |
| laptop (1280) | 18px | not captured by M1/M2 |
| tablet (768) | 19px | not captured by M1/M2 |
| mobile (390) | 19px | not captured by M1/M2 |
| narrow-mobile (360) | 19px | not captured by M1/M2 |

Neither `docs/design-tokens.md`'s typography table, `docs/reference-audit/00-global.md` §4, nor `docs/reference-audit/02-tour-detail.md` ever recorded a line-height figure for this element. Honest answer: **not scorable against a ≤2px tolerance, because no reference line-height value exists to diff against.** What is verifiable: the local value tracks the (now correct) font-size, and the button's height is fixed via explicit CSS (`height: 35px`, `align-items: center`) rather than derived from line-height, so the text stays vertically centred regardless.

**Colour channel diff ≤5 for solid UI colours — now 11/11 PASS (was 9/11).** Measured via `check-colors.mjs`, comparing rendered `getComputedStyle` colours (not screenshot pixel sampling, which would add anti-aliasing/compression noise) against the hex values in `docs/design-tokens.md` / `00-global.md` encoded into `theme.json`:

| Element | Rendered | Reference | Max channel diff | Result |
|---|---|---|---|---|
| Header/footer surface background | rgb(228,224,218) | `#e4e0da` | 0 | PASS |
| Book Now text colour | rgb(255,102,2) | `#ff6602` | 0 | PASS |
| Book Now background | rgb(255,255,255) | `#ffffff` | 0 | PASS |
| Facebook icon circle background | rgba(0,0,0,0) | none — reference renders bare icons | n/a | PASS |
| Facebook icon glyph fill | rgb(24,119,242) | `#1877f2` | 0 | PASS |
| Instagram icon circle background | rgba(0,0,0,0) | none | n/a | PASS |
| Instagram icon glyph fill | rgb(225,48,108) | `#e1306c` | 0 | PASS |
| WhatsApp icon circle background | rgba(0,0,0,0) | none | n/a | PASS |
| WhatsApp icon glyph fill | rgb(37,211,102) | `#25d366` | 0 | PASS |
| TikTok icon circle background | rgba(0,0,0,0) | none | n/a | PASS |
| TikTok icon glyph fill | rgb(0,0,0) | `#000000` | 0 | PASS |

The two previous failures (Facebook glyph off by 17, Instagram by 48) are closed. Cause and fix: an earlier "make every icon its own colour" change set `background-color` per network, which (a) invented a circular background the reference does not have and (b) left WordPress core's own hardcoded `is-style-logos-only` glyph fill (`#0866ff`, `#f00075`) sitting on top of it. Core's `wp-includes/blocks/social-links/style.css` sets `.wp-block-social-link-anchor svg { color: currentColor; fill: currentColor }` and colours each network via `color:` inside `:where()` — so swapping the theme rule from `background-color:` to `color:` at specificity (0,3,0) beats core's `:where()` (0,1,0), propagates through `currentColor` into the SVG fill, and reproduces the reference's bare-coloured-icon style. `check-colors.mjs`'s expectations were corrected to match that (circle background must now be *transparent*; the old expectation of a network-coloured circle encoded the regression, not the reference).

**Radius/gap/padding within 2px.** Radius: `border-radius: var(--wp--custom--button--radius-pill)` = 25px at every viewport, matching the `radius-button-pill` token M1 measured from the reference's pill CTAs (`design-tokens.md` line 109) — 0px diff, PASS. Gap and padding: **no reference gap or padding value was ever measured for the header or footer regions** by Milestone 1/2a — `design-tokens.md`'s "Grid gap" section only documents card-grid gaps elsewhere on the page, and no header/footer padding figure appears in `00-global.md` or the component-inventory addendum. Task 2's header padding (12px top/bottom, 20px left/right) and the internal flex gaps are implementation defaults, not values verified against a measured target — **not scorable**, rather than a fabricated pass or fail.

**Major section start positions within 24px — improved from 149–1193px off to 3–48px off; now passes at 2 of 5 viewports.** `check-section-position.mjs` measures the Book Now button's x-position locally via DOM `boundingBox()` and in the reference by pixel-scanning the cropped reference header for its white pill background:

| Viewport | Local Book Now x-range | Reference white-region x-range | Left-edge offset | Right-edge offset | Within 24px? |
|---|---|---|---|---|---|
| desktop (1440) | [1258, 1372] (w 114) | [1213, 1328] (w 115) | **45px** | 44px | **no** |
| laptop (1280) | [1098, 1212] (w 114) | [1063, 1177] (w 114) | **35px** | 35px | **no** |
| tablet (768) | [580, 700] (w 120) | [532, 662] (w 130) | **48px** | 38px | **no** |
| mobile (390) | [202, 322] (w 120) | [194, 315] (w 121) | 8px | 7px | yes |
| narrow-mobile (360) | [172, 292] (w 120) | [169, 290] (w 121) | 3px | 2px | yes |

The 149–1193px failure in the previous report was the direct consequence of the header having **no** site-identity element: `render_block_core_site_logo()` returns empty with no `custom_logo` theme mod set, which left the `space-between` group with a single child that collapsed to the left. Adding spec §5.1's required `wp:site-title` fallback beside `wp:site-logo` restored the two-child layout, and the button moved from x=20 to the right-hand group. The residual 35–48px at the three wider viewports is a genuine remaining gap: the button width now matches the reference almost exactly (114 vs 115px at desktop), so the offset is horizontal *padding/gap* difference in the header's right-hand cluster, not a layout collapse. Left and right edges are off by nearly the same amount, confirming it is a translation rather than a sizing mismatch.

*Measurement caveat (known limitation):* the reference-side figure is a min/max span of near-white pixels on the crop's vertical-middle row, not a contiguity test. That is deliberate — the reference pill contains orange glyphs and an orange circular icon, so the white run is genuinely interrupted (7–11 interior gaps, 38–46 white pixels of a 114–130px span) and a "longest contiguous run" would measure a fragment of the pill instead of the pill. The span is only trustworthy because nothing else in that row is near-white (the header surface is cream `#e4e0da`, well under the 245 threshold); the script prints the white-pixel count and gap count so a future reader can check that assumption rather than take the number on faith.

## What changed in the 2026-08-21 re-run

The final whole-branch review (`.superpowers/sdd/2026-08-20-milestone-4-theme-shell/final-review-findings.md`) raised 2 Critical and 11 Important findings. All were fixed before this re-run:

- **C1** — `templates/index.html` had no `<main>`, which silently disabled WordPress core's automatic skip-link (`_block_template_add_skip_link()` bails if `next_tag('MAIN')` fails) — a WCAG 2.4.1 Level A failure. Fixed with `tagName:"main"`; the rendered page now emits `<a class="skip-link" href="#wp--skip-link--target">` and a `<main id="wp--skip-link--target">`.
- **C2** — the crop region mismatch, fixed as described above.
- **I1** — `theme.json` now declares `templateParts` with explicit `header`/`footer` areas.
- **I2** — `parts/header.html` now carries spec §5.1's required accessible site-name fallback (`wp:site-title` beside `wp:site-logo`). This is what closed the section-position collapse.
- **I3** — `theme.json` now sets `defaultPalette: false`, `defaultFontSizes: false`, `defaultSpacingSizes: false`, `defaultPresets: false`, so core's presets no longer merge in alongside the measured tokens.
- **I4** — the eight `.woff2` faces moved from a front-end-only `@font-face` block into `theme.json`'s `fontFamilies[].fontFace`, plus `add_theme_support('editor-styles')` / `add_editor_style()`, so the Site Editor now renders in the real fonts and styles.
- **I5** — `do_blocks()`-based rendering assertions added (header renders non-empty, header renders a site-title element, header part registers in the `header` area, index template renders a `<main>` landmark, footer part registers in the `footer` area). These guard all three of this milestone's silent-rendering bugs.
- **I6** — `LICENSE-fonts.txt` now carries all five redistributed families' OFL copyright notices, not only Montserrat's.
- **I8** — `.phpunit.result.cache` removed from tracking and added to the root `.gitignore`.
- **I9** — new non-fluid `nav-cta` / `nav-cta-touch` font-size tokens (14.5px / 15.5px) with a 781px breakpoint. Button font-size now measures 15.5px at tablet/mobile/narrow-mobile against the reference's documented ~15–16px, closing the previous ±1px failure at three breakpoints.
- **I10** — social icon `background-color` → `color` swap, closing both colour-channel failures (see above).
- **I11** — `wp:navigation` now sets `overlayBackgroundColor: surface-header-footer` / `overlayTextColor: text-body` instead of inheriting core's white/black defaults.
- **Minors** — `parts/footer.html` attribute/markup mismatches fixed (tagline `fontSize`, group `padding.bottom`); stylesheet enqueue handle renamed `tour-theme-fonts` → `tour-theme-styles`; `capture-local.mjs`'s animation kill-switch moved after `goto()` (it was a no-op before it); `regions.json` is now genuinely consumed by `crop-reference.mjs` rather than written and ignored; width-assert guard added before `PNG.bitblt`; `npm run audit` added for the correct run order; `check-section-position.mjs` now prints white-pixel/gap diagnostics behind its span measurement.

**PHPUnit after all of the above: 33 tests, 73 assertions, 0 failures** (`vendor/bin/phpunit` from `wp-content/themes/tour-reference-theme/`; up from 17 tests / 29 assertions).

## Remaining deltas

1. **Footer content scope.** The reference footer (528px desktop) contains a wordmark, address, hotline, email, website link, legal/registration block, a compliance badge and a 5-icon social row. This milestone's footer (192px) contains a logo slot, tagline, 4 social icons and a legal bar. This is the **plan's own disclosed scope reduction** — spec §5.5's contact/legal/payment/newsletter surface needs a site-wide settings API that `tour-booking-core` does not yet have, and is deferred to the milestone that adds it (likely M8, spec §8.1). It is now the single largest driver of the 61–63% footer diff, and it is a *content/data* gap, not a layout defect.
2. **Header height 59px vs reference 66px (91px at tablet).** Newly visible now that the crop is reference-derived. The reference header's extra height comes from its taller logo lockup (35px logo + 15px top offset at desktop) and, at tablet, a materially taller bar (91px). The theme's 12px vertical padding + 35px button yields 59px. No measured reference header-height token exists in `design-tokens.md`, so this was never encoded; it should be measured and tokenised in Milestone 5 when the Home hero sits directly beneath it.
3. **Book Now horizontal position off by 35–48px at desktop/laptop/tablet** (passes at mobile/narrow-mobile). Residual padding/gap difference in the header's right-hand cluster, not a layout collapse — see the section-position table.
4. **No site logo image is configured** (`wp-cli theme mod get custom_logo` returns empty). `wp:site-logo` renders nothing, so the header shows the site-title text fallback. This is now correct *behaviour* per spec §5.1 (the fallback is what the spec requires to exist), and the branding slot is masked in the masked diff per spec §4. Setting an original placeholder mark via Site Identity remains a site-configuration step, not a code gap.
5. **Footer is missing a Tripadvisor social link.** `theme.json` defines a `social-tripadvisor` token (`#34e0a1`) sourced from M1's measurement of the reference's 5-icon footer, but `parts/footer.html`'s `wp:social-links` includes only facebook/instagram/whatsapp/tiktok (4 of 5). Real, disclosable implementation gap carried forward.
6. **Footer is missing the reference's government/business certification badge** ("ĐÃ THÔNG BÁO BỘ CÔNG THƯƠNG"). Expected and correctly masked per spec §4's allowance for business-specific/uncopyable assets — not a gap to close.
7. **Footer copyright/layout intentionally diverges** from the reference's left-copyright/right-icons bottom bar, in favour of a single vertically-centred stack. Deliberate, already-disclosed decision from Task 1's live interaction audit (the live reference footer measured `display: block` with a single child).
8. **Header background is opaque** (`#e4e0da`, no alpha) while M1 documented the reference's Home-page header as semi-transparent `rgba(228,224,218,0.85)` over a hero photo. Milestone 4 ships no hero, so this is not currently visible or testable, but the token does not yet capture an opacity value. Resolve when Milestone 5 builds the Home hero.
9. **Footer height grows at narrow widths — corrected attribution.** The previous report attributed this to "the hamburger button wraps at narrower widths." That is factually wrong and is withdrawn. Measured directly (`regions.json`, plus a per-child probe of the footer):
   - The hamburger button measures exactly 24×24px at **all five** viewports and the header stays 59px tall at all five — it never wraps.
   - Desktop/laptop/tablet footer = 192px, with the tagline paragraph at 17px (one line) and the legal-bar paragraph at 22px (one line).
   - mobile (390) footer = 209px: **+17px, exactly one extra tagline line** — the tagline paragraph wraps to two lines (17px → 34px).
   - narrow-mobile (360) footer = 231px: **+22px on top of that** — the legal-bar copyright paragraph *also* wraps to two lines (22px → 44px).
   So the growth is entirely text reflow in the footer's own two paragraphs, in two distinct steps at two distinct breakpoints. (Re-verified after the fix wave's `parts/footer.html` `fontSize` attribute correction, which did not change the rendered sizes — the attribute previously failed to emit its class, and adding it produced the same computed sizes.)

## Disclosed deviations carried out of Milestone 4

Per spec §15's zero-undisclosed-deviation rule. Items 1–3 are new disclosures added by the final review's finding I7; items 4–7 restate deviations already disclosed elsewhere so this section is the single complete list.

1. **Language/currency control (EN/USD, VI/VND) is not in the header — deferred to Milestone 7.** Spec §5.1 requires it unconditionally. Task 1's live audit found the reference site has no such control (`findings.langSwitcher` is `null` at all five viewports), and an earlier version of `docs/component-inventory-addendum-header-nav-footer.md` wrongly concluded from that measurement that implementers "should not include" one. The reference is the visual target, not the requirements document: the control **is** required and **will** be built, in Milestone 7, where the EN/VI and USD/VND switching behaviour it drives is specified (deferral is legitimate under spec §13's milestone ordering; shipping an inert control in the shell milestone would be worse). The addendum has been corrected with an explicit note to this effect. Because there is no reference precedent for its appearance, M7 will need to design it as original token-consistent UI.
2. **The primary navigation has no real menu items.** `<!-- wp:navigation ... /-->` carries no `ref` and no inner blocks, so `block_core_navigation_get_fallback_blocks()` falls back, and `WP_Navigation_Fallback::get_fallback()` will `wp_insert_post()` a `wp_navigation` post containing `<!-- wp:page-list /-->` on first front-end render. **What ships today is WordPress core's page list, not spec §5.1's required destination ordering.** This is deliberate: the destination content those menu items would point at does not exist until later milestones. `register_nav_menus('primary')` in `functions.php` is not dead code — `get_nav_menu_at_primary_location()` consumes it — so the wiring is in place for a real menu to be attached later. **No real navigation content was built in this fix wave**; this entry exists so the gap is disclosed rather than discovered.
3. **The Book Now control's `href="#booking"` does not target a real element.** No `#booking` anchor exists anywhere in the theme or in any rendered page. The booking flow is later-milestone work, so the link is currently inert (it will scroll nowhere rather than 404). **No booking anchor was built in this fix wave**; disclosed here so it is not mistaken for a working control. When the booking flow lands, the button must be re-pointed at the real target and the link re-tested.
4. **Sticky-header behaviour was overridden by evidence.** The plan's best-evidence default assumed a sticky header with a scroll-triggered background change. Task 1's live audit measured the reference header as statically positioned with no scroll-reactive change, so the sticky CSS/JS was deliberately not implemented (independently re-verified in the final review: zero `position: sticky`, no `.is-scrolled`, no `header-interactions.js` anywhere in the theme).
5. **Footer scope reduction** (spec §5.5's contact/address/map/email/phone/WhatsApp/legal/payment/newsletter admin surface deferred to the milestone that adds the site-wide settings API — see Remaining deltas item 1). Disclosed up front in the plan; restated here with the measured consequence.
6. **Two rendering-time bugs were found and fixed during this milestone's own review cycle**, not shipped: the self-referential `wp:template-part` recursion that rendered both parts empty, and the footer icon-colour context-inheritance bug. Both are documented above / in the ledger rather than silently repaired.
7. **The overlay-open navigation state has never been visually compared.** `overlayMenu:"always"` makes the hamburger overlay the only path to navigation at every viewport, but the audit pipeline only captures the closed state. The overlay's colours are now measured tokens rather than core defaults (finding I11), but no screenshot comparison of the open overlay exists. Ledgered as a deferred Task 6 tooling gap for a later milestone.

## Known out-of-tolerance items carried forward

- **Overall pixel diff 41.71% raw / 40.26% masked, against spec §4's <8% target — FAIL at every viewport, for both regions.** Not forced to a pass. Dominated by two structural facts, both disclosed deviations rather than defects: the footer's deferred content scope (deltas 1) and the header height delta (deltas 2). The previous report's 13.80% figure is withdrawn as an artifact of comparing mismatched regions.
- **Major section start position 35–48px off at desktop/laptop/tablet**, against spec's 24px tolerance. Improved from 149–1193px; passes at mobile (8px) and narrow-mobile (3px).
- **Header height 59px vs 66/91px reference** — no reference header-height token was ever measured, so this was never encoded. Flagged for Milestone 5.
- **Header opacity token gap** — currently untestable (no hero behind the header yet) but flagged so it isn't lost.
- **Line height, and header/footer gap/padding: not scorable** — no Milestone 1/2a reference measurement exists for these specific elements, so no pass/fail claim is made rather than inventing a target.
- **Missing Tripadvisor social link** (delta 5) — real implementation gap, carried forward.
- **Now closed** (previously carried forward): colour-channel diff (11/11 PASS, was 9/11), Book Now font-size at tablet/mobile/narrow-mobile (15.5px vs reference ~15–16px, was 14.0–14.2px), and the absent-logo layout collapse.
- Items 6 and 7 in Remaining deltas (certification badge, footer layout shape) are **not** carried forward as gaps — correctly masked / already-disclosed respectively.

## Files produced

- `tools/local-audit/measure-reference.mjs`, `capture-local.mjs`, `crop-reference.mjs`, `diff.mjs`, `check-overflow.mjs`, `check-metrics.mjs`, `check-colors.mjs`, `check-section-position.mjs`, `package.json`, `package-lock.json`
- `docs/reference-screenshots/local-m4/*.png` — local header/footer/full-page captures, `-ref.png` / `-cmp.png` comparison crops, and `-diff.png` / `-diff-masked.png` pixelmatch visualisations (gitignored per existing repo policy, not committed)
- `docs/reference-screenshots/local-m4/reference-regions.json`, `regions.json`, `crop-summary.json` — the measured geometry every number above is derived from
- This report.
