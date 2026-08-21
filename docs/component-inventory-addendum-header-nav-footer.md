# Header/Nav/Footer Interaction Audit Addendum

Captured 2026-08-20 via `tools/reference-audit/interaction-audit.mjs` against the live reference site (https://looptrails.com/, `pages[0]`/"Home"). Supplements `docs/component-inventory.md`'s "Primary Navigation (Header)" and "Footer Block" entries, which were built from static screenshots/computed-style JSON and explicitly flagged these as gaps. Raw data: `docs/reference-screenshots/interaction-audit/findings.json`; screenshots in the same directory.

## Nav structure

The `.elementor-menu-toggle` hamburger toggle was found and reported `toggleVisible: true` at **all five audited viewports** — desktop (1440px), laptop (1280px), tablet (768px), mobile (390px), and narrow-mobile (360px). This is confirmed visually: `desktop-header-before-click.png` shows only the wordmark, a "Book Now" button, and a three-line hamburger icon in the header at 1440px — no inline nav bar is rendered at any width.

Clicking the toggle produced the identical `menuTextAfterOpen` array at every one of the 5 viewports (deduplicated, in DOM order):

```
Home, Tours, Ha Giang Loop 2 Days 1 Night, Ha Giang Loop 3 Days 2 Nights,
Ha Giang Loop 4 Days 3 Nights, Ha Giang Cao Bang 5 Days 4 Nights,
Ha Giang Cao Bang 6 Days 5 Nights, Cao Bang Loop 3 Days 2 Nights,
Motorbike Rental, Blog, Contact, About, Terms, Conditions & Privacy Policy
```

Note: this array is the text of every `nav a` / `[class*="menu"] a` / `[class*="nav"] a` element in the DOM after the click, which includes submenu links that exist in markup but are not necessarily expanded on screen. `desktop-header-after-click.png` confirms the *visibly rendered* panel at 1440px shows only the 7 top-level items (Home, Tours, Motorbike Rental, Blog, Contact, About, Terms/Conditions & Privacy Policy) as a vertical stacked list overlaying the page below the header — the 6 "Ha Giang Loop …" / "Cao Bang Loop …" entries are Tours submenu items present in the DOM but collapsed until Tours itself is expanded/hovered.

**Conclusion:** the reference site uses a hamburger-toggle nav pattern uniformly across all breakpoints, including desktop — there is no separate horizontal desktop nav bar. This confirms (rather than merely infers) the hamburger-at-every-viewport pattern component-inventory.md flagged as a gap. The revealed panel is a single-column vertical list of top-level pages, with tour sub-pages nested under a "Tours" submenu rather than flattened into the top-level list.

## Sticky behavior

At all five viewports, `beforeScroll` and `afterScroll` computed styles on the `header` element were **identical**: `position: static`, `top: auto`, `backgroundColor: rgba(0, 0, 0, 0)` — no change after scrolling to `y: 900`. This holds for desktop, laptop, tablet, mobile, and narrow-mobile alike.

**Conclusion:** the header is not sticky. It uses static positioning at every breakpoint and does not gain a background color, change position, or otherwise visually react to scrolling. Tasks 3/4 should implement a plain (non-fixed, non-sticky) header — no scroll-triggered appearance change should be reproduced.

## Footer structure

The computed style of the `<footer>` element is `display: block`, `gridTemplateColumns: none`, `flexDirection: row` (the `flexDirection` value is the browser default and not meaningful since `display` is not `flex`). The footer has exactly **one direct child** — an Elementor section (`class="elementor-section elementor-top-section elementor-element elementor-element-50aa2b46 elementor-section-content-middle elementor-section-full_width …"`) which itself contains 1 child (i.e. no direct-child multi-column layout at the DOM level captured here).

`desktop-footer-full.png` (captured after scrolling to the bottom of the page) shows the visible footer content as a single vertically-stacked, horizontally-centered column: wordmark, address, hotline/phone, email, website link, a legal/registration text block, a compliance badge, and a row of social icons — all center-aligned in one block, not arranged in side-by-side columns.

**Conclusion:** the footer is a single centered block, not a multi-column grid. There is no `display: grid` or `display: flex` with multiple columns at the footer's top level — content is stacked vertically as one column. Tasks 3/4 should build the footer as a single centered content block rather than a grid layout.

## Language/currency control

`findings.langSwitcher` is `null`. The script only stops re-checking once a match is found (`if (!findings.langSwitcher)`), so with no match ever found it re-ran the same search at all five viewports — desktop, laptop, tablet, mobile, and narrow-mobile all returned no candidate. The search looked for any element inside `header` whose text matches `/\b(EN|VI|USD|VND)\b/` and has at most 2 children.

**Conclusion:** there is no visible language or currency switcher in the header. The reference site's header does not expose a language or currency selector element matching the audited patterns — this is a genuine absence, not an unmeasured gap.

**Correction (final review, 2026-08-21) — do not read the above as "don't build it".** An earlier version of this section told implementers that Tasks 3/4 "should not include a language/currency switcher in the header." That instruction was wrong and has been withdrawn. Spec §5.1 requires a *"language/currency control for EN/USD and VI/VND"* in the header **unconditionally**; the reference site's absence of one is a measurement of the reference, not a waiver of a spec requirement. The reference is the visual target, not the requirements document.

What actually applies:

- The control **is** required by spec §5.1 and **will** be built.
- It is **deferred to Milestone 7** ("EN/VI and USD/VND behavior"), which is where the language/currency switching behavior itself is specified — deferring it there is legitimate under spec §13's milestone ordering, since Milestone 4 ships the theme shell and Milestone 7 ships the behavior the control would drive. Building an inert control in Milestone 4 with no switching behavior behind it would be worse than deferring it.
- This deferral is a **disclosed deviation** per spec §15's zero-undisclosed-deviation rule and is recorded as such in `docs/visual-acceptance-report-m4.md` ("Disclosed deviations carried out of Milestone 4").
- Because there is no reference-site precedent for its appearance, Milestone 7's implementer will need to design it as original UI consistent with the theme's tokens, rather than matching a measured reference component.
