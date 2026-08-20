# Milestone 4 Visual Acceptance Report

Captured 2026-08-20. Reference: `docs/reference-screenshots/home/*.png` (Milestone 1, full-page). Local: `docs/reference-screenshots/local-m4/*.png` (Milestone 4, `http://localhost/looptrails/`, theme `tour-reference-theme` active).

## Critical bug found and fixed before this run

Before any meaningful capture could happen, activating the theme and loading the home page showed a **completely empty `<header>` and `<footer>`** — `<header class="wp-block-template-part">\n</header>` with zero children, confirmed via raw `curl` output (not a CSS/rendering artifact). Root cause: `parts/header.html` and `parts/footer.html` (added in Tasks 2 and 3) each wrapped their own content in a second, self-referential `<!-- wp:template-part {"slug":"header",...} -->` block comment, in addition to `templates/index.html`'s own (correct) reference to the same slug. WordPress's `render_block_core_template_part()` (`wp-includes/blocks/template-part.php` lines 130–157) keeps a static `$seen_ids` recursion guard keyed by slug; the part-file's own nested self-reference hit that guard on the second pass and silently rendered as `''`. This produced **zero PHP warnings or fatal errors** (block rendering, not PHP, absorbed the failure), which is why `WP_DEBUG`/`debug.log` never flagged it, and it produced **zero PHPUnit failures**, because `tests/test-header-part.php` and `tests/test-footer-part.php` only run `assertStringContainsString()` against the raw file text (`file_get_contents(.../parts/header.html)`) — a check that passes identically whether the block engine can actually render that text or not.

Fix: removed the erroneous outer `<!-- wp:template-part ... --> ... <!-- /wp:template-part -->` wrapper from both `parts/header.html` and `parts/footer.html`, leaving just their inner block content (the correct convention — a template part *file* holds the part's content; the `wp:template-part` *reference* to it belongs only in the template that includes it, which `templates/index.html` already had right). Verified via `curl` that both regions now render their full intended markup (nav, Book Now button, logo block, paragraph, social links, legal bar).

This fix was applied inside Task 6 because it is a mechanical, unambiguous structural error (not a design judgment call) and leaving it in place would have made every number in this report a meaningless "100% missing content" result rather than a real visual-fidelity measurement. It is disclosed here in full per spec §15's zero-undisclosed-deviation rule — **this is a real bug in Tasks 2/3's deliverables that a future reviewer should be aware existed**, not a Task 6 artifact.

**Environment note:** the live site at `http://localhost/looptrails/` is served from `C:\xampp\htdocs\looptrails\wp-content\themes\tour-reference-theme\`, an **untracked, manually-synced copy** separate from this worktree's `wp-content\themes\tour-reference-theme\` (git worktrees do not share working-directory files). The fix above was applied to both copies. Diffing the two copies beforehand showed only 4 files differed: the two buggy part files (now fixed in both), a `.phpunit.result.cache` artifact, and `tests/bootstrap.php` (the live copy has one extra `require .../functions.php` line not present in the worktree — left untouched as out of this task's scope and not related to the visual bug).

## Region-mismatch fix (brief Step 4)

The brief's literal `diff.mjs` compares a local header/footer crop against the *raw, full-page* Milestone-1 reference PNG via `pixelmatch`'s `Math.min(width, height)` sizing. For the footer region this is not just "a rough approximation" — because `Math.min` always takes the smaller (local) height, it silently diffs only the **first N rows of the reference's full page** (the top of the page) against the local footer crop, regardless of `REGION`. That is a comparison of genuinely unrelated regions, not a real footer measurement.

Fixed by adding `tools/local-audit/crop-reference.mjs`, which crops each Milestone-1 reference PNG down to the *same* header/footer pixel regions as the local capture, using real DOM `boundingBox()`-derived heights (not a guessed fixed value):
- Local capture (`capture-local.mjs`) now screenshots the actual `header.wp-block-template-part` and `footer.wp-block-template-part` elements via Playwright locators, instead of a fixed 200px clip — giving the *true* rendered header/footer height per viewport.
- `crop-reference.mjs` crops the reference full-page PNG to the top `headerHeight` px (header) and bottom `footerHeight` px (footer) using those same real heights, per viewport.
- `diff.mjs` now diffs two same-size, same-region crops directly (throwing if dimensions ever mismatch, rather than silently falling back to `Math.min`).

Real measured header/footer heights (from Playwright `boundingBox()`, same across desktop/laptop/tablet; mobile/narrow-mobile grow slightly because the hamburger-menu button wraps at narrower widths):

| Viewport | Header height | Footer height |
|---|---|---|
| desktop (1440) | 59px | 193px |
| laptop (1280) | 59px | 193px |
| tablet (768) | 59px | 193px |
| mobile (390) | 59px | 210px |
| narrow-mobile (360) | 59px | 232px |

## Results by viewport

| Viewport | Header diff % | Footer diff % | Container edges within tolerance? | Font metrics within tolerance? | Horizontal overflow at 360px? |
|---|---|---|---|---|---|
| desktop (1440) | 9.50% | 3.36% | yes (full-width bar, 0px edge delta; no overflow) | yes (Book Now: 14.5px/700/height 35px, matches `button-height-nav` token exactly) | n/a |
| laptop (1280) | 10.66% | 3.70% | yes | yes (14.48px/700/height 35px) | n/a |
| tablet (768) | 17.36% | 5.87% | yes | no — font ~14.22px vs reference's documented ~15–16px at this breakpoint (M1's `00-global.md` §4: reference nav button font grows to 15–16px at tablet/mobile, local button font stays on the sitewide fluid body scale and never grows) | n/a |
| mobile (390) | 32.86% | 10.09% | yes | no — same gap, ~14.04px vs ~15–16px reference | no (measured `scrollWidth === clientWidth === 390` via Playwright) |
| narrow-mobile (360) | 35.11% | 9.46% | yes | no — same gap, ~14.02px vs ~15–16px reference | no (measured `scrollWidth === clientWidth === 360`, confirmed with `tools/local-audit/check-overflow.mjs`) |

"Container edges" here means: both local and reference header/footer render as full-viewport-width bars (not the 1200px boxed content container, which M1 documented as not applying to header/footer either) — confirmed 0px edge delta and zero horizontal scroll at every viewport via `check-overflow.mjs`.

Overall average across all 10 measurements (5 viewports × header + footer): **13.80%**, above spec §4's <8% target. See below for how much of that is attributable to maskable/expected differences vs. genuine gaps.

## Remaining spec §4 criteria (line height, color channels, radius/gap/padding, section positions)

The table above covers 5 of spec §4's 9 acceptance criteria (section order/component count and no-missing-elements are covered qualitatively in "Remaining deltas" below; container edges, font size, 360px overflow, and overall pixel diff are in the table itself). The 4 remaining criteria, measured directly rather than left as prose asides:

**Line height within 2px.** The Book Now button's `line-height` computes to the CSS keyword `normal` at every viewport (never explicitly overridden by the theme) — per the CSS Values spec, `normal` *is* the computed value, so `getComputedStyle` never resolves it to a px number on its own. Measured the actual used value instead, by rendering an isolated probe span with the same font-family/size/weight and reading its line-box height (`tools/local-audit/check-metrics.mjs`):

| Viewport | Resolved line-height (local) | Reference line-height |
|---|---|---|
| desktop (1440) | 18px | not captured by M1/M2 |
| laptop (1280) | 18px | not captured by M1/M2 |
| tablet (768) | 18px | not captured by M1/M2 |
| mobile (390) | 18px | not captured by M1/M2 |
| narrow-mobile (360) | 18px | not captured by M1/M2 |

Neither `docs/design-tokens.md`'s typography table, `docs/reference-audit/00-global.md` §4, nor `docs/reference-audit/02-tour-detail.md` (which documents the header "Book Now" button's height/background/text-color specifically) ever recorded a line-height figure for this exact element — only height (35px), colors, and shadow style were measured. Honest answer: **cannot be scored pass/fail against a ≤2px tolerance because no reference line-height value exists to diff against.** What is verifiable: the local value is stable (18px) across all 5 viewports, and because the button's height is fixed via explicit CSS (`height: 35px`, `align-items: center`) rather than derived from line-height, the text remains vertically centered regardless of line-height's exact value — so this gap is unlikely to be visually significant even though it can't be formally scored.

**Color channel diff ≤5 for solid UI colors.** Measured via `tools/local-audit/check-colors.mjs`, comparing rendered `getComputedStyle` colors (not screenshot pixel sampling, which would add anti-aliasing/compression noise) against the exact reference hex values documented in `docs/design-tokens.md`/`docs/reference-audit/00-global.md` and encoded into `theme.json`:

| Element | Rendered | Reference | Max channel diff | Result |
|---|---|---|---|---|
| Header/footer surface background | rgb(228,224,218) | `#e4e0da` rgb(228,224,218) | 0 | PASS |
| Book Now text color | rgb(255,102,2) | `#ff6602` rgb(255,102,2) | 0 | PASS |
| Book Now background | rgb(255,255,255) | `#ffffff` rgb(255,255,255) | 0 | PASS |
| Facebook icon circle background | rgb(24,119,242) | `#1877f2` rgb(24,119,242) | 0 | PASS |
| **Facebook icon glyph fill** | rgb(8,102,255) | `#1877f2` rgb(24,119,242) | **17** | **FAIL** |
| Instagram icon circle background | rgb(225,48,108) | `#e1306c` rgb(225,48,108) | 0 | PASS |
| **Instagram icon glyph fill** | rgb(240,0,117) | `#e1306c` rgb(225,48,108) | **48** | **FAIL** |
| WhatsApp icon circle background | rgb(37,211,102) | `#25d366` rgb(37,211,102) | 0 | PASS |
| WhatsApp icon glyph fill | rgb(37,211,102) | `#25d366` rgb(37,211,102) | 0 | PASS |
| TikTok icon circle background | rgb(0,0,0) | `#000000` rgb(0,0,0) | 0 | PASS |
| TikTok icon glyph fill | rgb(0,0,0) | `#000000` rgb(0,0,0) | 0 | PASS |

All theme-controlled color tokens (surfaces, text, circle backgrounds) are byte-exact matches — 0 channel diff, as expected since they're the same hex values M1/M2a measured, wired directly through `theme.json`. The two failures are both the *icon glyph fill*, not the circle background: WordPress core's `is-style-logos-only` block style hardcodes its own per-network SVG fill colors (`#0866ff` for Facebook, a magenta `#f00075`-ish value for Instagram — not our tokens), and those hardcoded fills sit on top of our theme's per-network background-color fix. This is the precise, quantified version of the "illegible icon" finding in item 2 below — Facebook is off by 17 and Instagram by 48, both well outside the ≤5 tolerance, while WhatsApp/TikTok happen to have core fills that already match our tokens exactly (0 diff, coincidence not design).

**Radius/gap/padding within 2px.** Radius: the Book Now button uses `border-radius: var(--wp--custom--button--radius-pill)` = 25px, matching the `radius-button-pill` token M1 measured from the reference's pill-style CTAs (`docs/design-tokens.md` line 109; `00-global.md` §4 confirms the header CTA is "pill-style") — 0px diff, PASS. Gap and padding: **no reference gap or padding value was ever measured for the header or footer regions specifically** by Milestone 1/2a — `docs/design-tokens.md`'s "Grid gap" section only documents card-grid gaps elsewhere on the page (featured tours, testimonials, stats band), and no header/footer padding figure appears anywhere in `docs/reference-audit/00-global.md` or the component-inventory addendum. Task 2's header padding (`12px` top/bottom, `20px` left/right`) and the header/footer internal flex gaps are therefore implementation defaults, not values verified against a specific measured target — honest answer is **not applicable / not independently verifiable**, rather than a fabricated pass or fail, since no Milestone 2b measurement of this specific dimension exists to diff against.

**Major section start positions within 24px.** Measured the Book Now button's left-edge x-position: locally via real DOM `boundingBox()`, and in the reference via pixel-scanning the cropped reference header image for its white background region (the button is the only near-white region against the cream `#e4e0da` header) — `tools/local-audit/check-section-position.mjs`:

| Viewport | Local Book Now left-x | Reference Book Now left-x | Offset | Within 24px? |
|---|---|---|---|---|
| desktop (1440) | 20px | 1213px | **1193px** | **no** |
| laptop (1280) | 20px | 1063px | **1043px** | **no** |
| tablet (768) | 20px | 537px | **517px** | **no** |
| mobile (390) | 20px | 194px | **174px** | **no** |
| narrow-mobile (360) | 20px | 169px | **149px** | **no** |

This is the direct, measured consequence of item 1 below (absent site logo collapsing the header's `space-between` flex layout to a single left-aligned child): the button sits at the header's left padding edge (`20px`, matching the container's own `padding-left`) instead of the reference's far-right position. **Fails spec §4's 24px section-position tolerance at every viewport, by 149px–1193px** — this is not a rounding-error miss, it is a structural layout difference and is scored here as an explicit fail, not left as background color in the prose deltas below.

## Remaining deltas

1. **No site logo is configured** (`wp-cli theme mod get custom_logo` returns empty; no logo asset exists anywhere in the theme). WordPress's `core/site-logo` block renders nothing when no logo is set — this is the single largest driver of the header diff. Visually confirmed via `desktop-header-diff.png`: the reference's left-side "LOOP TRAILS" wordmark area and the reference's right-aligned Book Now+hamburger group both show as full mismatch, while the shared cream background in between correctly matches (gray/unhighlighted in the diff image).
   - This is **more than a simple maskable logo substitution** (spec §4 allows masking a *different* logo image in the *same* slot): because the header's flex layout is `justify-content: space-between` with the logo as one child and the buttons+nav group as the other, an *entirely absent* logo child leaves only one flex item, which collapses to the start of the row. The local header currently renders "Book Now" + hamburger at the **far left** with an empty right two-thirds, instead of logo-left/actions-right like the reference. This is a real, visible layout difference, not just a missing image — expected to resolve once a placeholder logo (an original mark, not the reference's real logo, per this project's copyright boundary) is uploaded and set via Site Identity. Recommend closing this before Milestone 5's Home build, since Home's hero sits directly under this header. Measured precisely under "Major section start positions within 24px" above: the Book Now button is 149px–1193px off from its reference position depending on viewport — a direct, explicit fail against that specific spec §4 criterion, not just a qualitative observation.
   - Masked-per-spec discount: the logo-image content itself (whatever mark ends up there) is expected to differ from the reference's real logo and should be masked in any *future* re-run once a placeholder exists — the layout-collapse side effect described above is not something masking would hide, since it isn't a difference in pixel content but a difference in DOM structure/behavior.

2. **Footer social icons render as visually illegible solid-colored circles**, not the reference's bare colored-icon-only style. Confirmed via computed styles: `.wp-social-link-facebook` has `background-color: rgb(24,119,242)` (our theme's Facebook token, `#1877f2`, correctly reused from Task 3's earlier icon-color fix) but the SVG `<path>` inside it has `fill: rgb(8,102,255)` (WordPress core's own hardcoded `is-style-logos-only` per-network fill, `#0866ff`) — two very close-but-different shades of blue on top of each other, so the actual Facebook glyph is nearly invisible against its own circular background at normal viewing size. Quantified precisely under "Color channel diff ≤5" above: Facebook's glyph fill is off from our own token by 17 channels and Instagram's by 48 — both real ≤5-tolerance failures, not just a subjective "hard to see." The reference site does not add a background at all for its footer icons (`is-style-logos-only`'s native behavior: bare colored icon, no circle) — see `desktop-footer-ref.png` vs `desktop-footer.png`. This is a real, disclosable regression introduced by the earlier "fix all icons render the same color" change (which added `.footer-social .wp-social-link-{network} { background-color: ... }` in `theme.css` lines 71–74): it fixed the *background* color per network, but that background wasn't in the reference design to begin with, and it now visually competes with WP core's own hardcoded icon-fill color. Not fixed in this task (Task 6 is scoped to tooling + reporting, this is a Task 3 CSS decision) — flagged as a genuine gap for a follow-up.

3. **Footer is missing a Tripadvisor social link.** `theme.json` defines a `social-tripadvisor` color token (`#34e0a1`, line 25) sourced from M1's measurement of the reference's 5-icon footer, but `parts/footer.html`'s `wp:social-links` block only includes facebook/instagram/whatsapp/tiktok (4 of 5). Reference footer (`desktop-footer-ref.png`) visibly shows a 5th Tripadvisor icon. Real, disclosable implementation gap in Task 3's footer, not something Task 6 should silently patch.

4. **Footer is missing the reference's government/business certification badge** ("ĐÃ THÔNG BÁO BỘ CÔNG THƯƠNG" trust badge, visible at the top of `desktop-footer-ref.png`). This is expected and correctly masked per spec §4's allowance for business-specific/uncopyable assets — not a gap to close.

5. **Footer copyright text and layout intentionally diverge from the reference's left-copyright/right-icons bottom bar**, in favor of a single vertically-centered stack (logo → tagline → icons → legal bar). This was a deliberate, already-disclosed decision from Task 1's live interaction audit (`docs/component-inventory-addendum-header-nav-footer.md`, based on `findings.json`'s `display:block`/single-child measurement of the live reference footer), not a new Task 6 finding — re-confirmed here as still the case, not re-litigated.

6. **Header background is opaque** (`var(--wp--preset--color--surface-header-footer)`, `#e4e0da`, no alpha) while M1's global audit documented the *reference's* Home-page header as semi-transparent (`rgba(228,224,218,0.85)`) layered over a hero photo (`docs/reference-audit/00-global.md` §4). Since Milestone 4 ships no hero image yet (the index template is a minimal shell — a default WP post, no Home page content), this difference isn't currently visible/testable against a hero background, but it is a real, disclosed gap the theme.json token doesn't yet capture (an opacity value), worth resolving when Milestone 5 builds the Home hero.

7. **Button font-size does not grow at tablet/mobile/narrow-mobile.** Reference's nav "Book Now" button was measured by M1 at ~15–16px font on mobile/tablet (a deliberately larger tap target, not a proportional scale of the desktop size — `00-global.md` §4). The local button's font size follows the sitewide fluid `--wp--preset--font-size--body` scale (`clamp(14px, ...)`), which barely moves (14.5px → 14.02px) across the same range. Height (35px), weight (700), and radius (25px pill) all match the reference exactly at every viewport — only the font-size fails the ±1px tolerance at the three narrower breakpoints, by roughly 1–2px.

## Known out-of-tolerance items carried forward

- **Header diff 9.50%–35.11%** across all 5 viewports, all above spec §4's <8% overall target. Primary cause: item 1 above (absent site logo + resulting flex-layout collapse). Not masked in this run's raw numbers because the effect is structural (layout shift), not a simple pixel-content swap — a masking pass on the raw logo pixels alone would understate the real difference. Expected to close substantially once a placeholder logo is set (unverified without live-testing a logo upload, which was out of this task's scope — no number is claimed for the post-fix state).
- **Footer diff 3.36%–10.09%**: desktop/laptop/tablet (3.36%, 3.70%, 5.87%) are within the <8% target; mobile/narrow-mobile (10.09%, 9.46%) are not, driven by the same absent-logo-slot effect in the footer's vertical stack plus the icon-legibility and Tripadvisor gaps (items 2–3 above). Reason: content-configuration and CSS gaps in Tasks 2/3, not Task 6.
- **Font-size tolerance miss at tablet/mobile/narrow-mobile** (item 7) — a real ~1–2px miss against spec's 1px tolerance, not close enough to round to "yes."
- **Header opacity token gap** (item 6) — currently untestable (no hero behind the header yet) but flagged so it isn't lost before Milestone 5.
- **Major section start position miss at every viewport** (149px–1193px off spec's 24px tolerance) — the Book Now button's position, driven by the same absent-logo flex-collapse as the header diff. Same root cause and same expected remediation (set a placeholder logo) as the header-diff item above.
- **Color channel diff miss on 2 of 11 measured colors**: Facebook icon glyph fill (diff 17) and Instagram icon glyph fill (diff 48), both from WP core's hardcoded `is-style-logos-only` per-network fill sitting on top of our theme's own (correct) per-network background-color. The other 9 measured colors (all theme-token-driven surfaces/text/backgrounds, plus WhatsApp/TikTok's icon fills) are exact 0-diff matches.
- **Line height and header/footer gap/padding: not scorable**, not fabricated — no reference measurement of either exists from Milestone 1/2a for these specific elements, so no pass/fail claim is made rather than inventing a target to compare against.
- Items 4 (certification badge) and 5 (footer layout shape) are **not** carried forward as gaps — they are correctly masked/already-disclosed per spec §4 and Task 1's audit respectively.

## Files produced

- `tools/local-audit/capture-local.mjs`, `crop-reference.mjs`, `diff.mjs`, `check-overflow.mjs`, `check-metrics.mjs`, `check-colors.mjs`, `check-section-position.mjs`, `package.json`, `package-lock.json`
- `docs/reference-screenshots/local-m4/*.png` (local header/footer/full-page captures, cropped reference regions, and pixelmatch diff visualizations — gitignored per existing repo policy, not committed)
- This report.

## Fix note (post-review)

An initial review approved the tooling, the bug diagnosis/fix, and the region-mismatch fix, but found the results table only covered 5 of spec §4's 9 acceptance criteria. Added the "Remaining spec §4 criteria" section above, covering the 4 that were missing:

1. **Line height within 2px** — `check-metrics.mjs` was extended with a line-height probe (an isolated same-font span, since `line-height: normal` never resolves to a px number via `getComputedStyle`). Measured 18px at every viewport; no reference figure exists to diff against, stated explicitly rather than guessed.
2. **Color channel diff ≤5** — new `check-colors.mjs` compares rendered `getComputedStyle` colors (not screenshot pixel sampling) for 11 header/footer UI colors against the exact hex values in `docs/design-tokens.md`. 9/11 are exact 0-diff matches; the Facebook (17) and Instagram (48) icon glyph fills fail, both traced to WP core's hardcoded `is-style-logos-only` per-network SVG fill colors.
3. **Radius/gap/padding within 2px** — radius (25px pill) matches the measured `radius-button-pill` token exactly. Gap/padding: confirmed no Milestone 1/2a measurement of header/footer gap or padding exists anywhere in the docs to diff against, so this is stated as not independently verifiable rather than a fabricated pass.
4. **Major section start positions within 24px** — new `check-section-position.mjs` measures the Book Now button's x-position locally (DOM `boundingBox()`) and in the reference (pixel-scanning the cropped reference header for its white background region). Fails at every viewport by 149px–1193px, the direct, now-quantified consequence of the absent-logo flex-collapse already described in "Remaining deltas" item 1.

Re-ran `vendor/bin/phpunit` after these changes (no PHP touched): still 17 tests, 29 assertions, 0 failures.

All previously-reported numbers (13.80% overall diff, the 6 original deltas, the >8% miss disclosure) are unchanged — this was an additive fix, not a rewrite.
