# Milestone 2b: Reference Audit Doc, Component Inventory & Design Tokens Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Turn Milestone 2a's raw measurement data (53 JSON files in `docs/design-measurements/`) and Milestone 1's screenshots (`docs/reference-screenshots/`) into three deliverables spec §13 Milestone 2 requires: `docs/reference-audit.md` (the human-readable measurement record spec §3 mandates), `docs/component-inventory.md` (the reusable-component catalog later theme/plugin milestones build against), and `docs/design-tokens.md` (the consolidated typography/color/spacing/radius token values Milestone 4's `theme.json` will encode — this plan does not touch `wp-content/themes/`, that's Milestone 4's job).

**Architecture:** No WordPress code changes. Four content-authoring tasks read the existing JSON/PNG evidence and write markdown; a fifth merges/finalizes. Tasks 2-5 (the four audit-doc sections) touch entirely separate files and share no state — each can run standalone, and the controller may dispatch them in parallel via `superpowers:dispatching-parallel-agents` instead of sequentially, if time favors it (this was agreed with the user during 2a's planning). Task 1 (component inventory) and Task 6 (merge + tokens) are sequential — Task 1 needs the full data picture, Task 6 needs Tasks 2-5's output.

**Tech Stack:** No new tooling — this plan is markdown authorship from existing JSON/PNG data (`docs/design-measurements/*.json`, `docs/reference-screenshots/*.png`), both already on disk from Milestones 1 and 2a.

**Spec:** `docs/AI_AGENT_WORDPRESS_TOUR_WEBSITE_SPEC.md` (v2.0) — this plan implements spec §3's required `docs/reference-audit.md` content list and spec §13 Milestone 2's "component inventory" deliverable.

## Global Constraints

- **Never quote the reference site's verbatim prose/marketing copy in any committed file** — only measured *values* (pixel sizes, hex colors, counts, structural descriptions). `docs/design-measurements/*.json`'s `samples`/`className` fields contain real reference-site text; treat them as data to extract values FROM, never as text to copy INTO a committed doc. (See [[feedback_image_copyright_boundary]].)
- Every measurement claim in `docs/reference-audit.md` must trace to a real file in `docs/design-measurements/` — no invented/estimated values. If a value is genuinely uncertain (e.g. `mostCommonContainerWidth: null` at a viewport, per Milestone 2a's fix), say so explicitly rather than guessing a number.
- Every task ends with a git commit, pushed to `origin/master` (working directly on master, per established project convention — see [[project_tour_booking_website]]).
- The 12 Minor findings and 2 Low residuals parked during Milestone 2a's reviews are known, real, minor imperfections in the source data (e.g. `rest.height: "auto"` on some button entries, card `gapX` meaningless for stacked mobile layouts, first-seen-not-representative `tag`/`className` on deduped entries) — when a task hits one of these while reading the data, work around it using the `samples` array or cross-referencing another viewport's data, don't treat it as a blocker.

---

### Task 1: Component Inventory

**Files:**
- Create: `docs/component-inventory.md`

**Interfaces:**
- Consumes: all 53 files under `docs/design-measurements/` (read-only), all screenshots under `docs/reference-screenshots/` (read-only).
- Produces: a catalog later milestones (4-6) will use to decide what reusable theme components/block patterns to build. No code interface — this is a planning document for humans and future planning sessions.

- [ ] **Step 1: Identify every distinct reusable UI component across all 10 templates**

Read every file under `docs/design-measurements/<template-slug>/*.json` (10 templates × 5 viewports) plus the 3 files under `docs/design-measurements/tour-detail-variation/`. Cross-reference against the screenshots at `docs/reference-screenshots/<template-slug>/*.png` for visual context the JSON alone doesn't convey (layout arrangement, icons, imagery style).

For each `cards` entry (from the `scanCardGrids` probe) that appears on 2+ templates with matching `itemWidth`/`itemHeight`/`borderRadius`, treat it as ONE reusable component (e.g. "Tour Card" appearing on Home and Tours Archive), not a separate component per page. For each distinct `buttons` entry pattern (grouped by `borderRadius`+`backgroundColor` combination) appearing across multiple templates, treat it as one reusable button variant (e.g. "Primary CTA Button", "Secondary Outline Button").

- [ ] **Step 2: Write the inventory**

Create `docs/component-inventory.md` with one `##` section per identified component. For each:

```markdown
## <Component Name>

**Appears on:** <list of template slugs from docs/design-measurements/>
**Measured dimensions:** <pull real values from the JSON — width/height/radius/shadow/gap>
**Variants observed:** <e.g. "3-column desktop, 1-column mobile" — cite the specific viewport files that show this>
**Spec cross-reference:** <which spec §5.x subsection this component fulfills, e.g. "§5.3 Tour Cards">
```

Do not include any reference-site prose — component names and descriptions must be your own original wording (e.g. "Tour Card" not the reference site's actual heading text). Include a component for: primary navigation, footer columns, tour card, badge/chip, price row, primary/secondary buttons, itinerary day panel, accommodation card, testimonial card, tab control (for the Home page's "Destinations/Itinerary/Transport/Accommodation" tabs per spec §5.2), FAQ accordion if evidenced in the data, and any other repeating structural pattern you find with real supporting measurement data — do not invent a component with no measurement evidence behind it.

- [ ] **Step 3: Self-check against spec §5**

Re-read spec section 5 (Complete public page scope) and its subsections 5.1-5.5. For every named UI element in that section (nav, Book Now control, tour cards, itinerary tabs, footer columns, etc.), confirm your inventory either includes it or explicitly notes "not observed in captured data, will need direct inspection" — do not silently omit a spec-required component just because the automated probes didn't surface it cleanly.

- [ ] **Step 4: Commit**

```bash
git add docs/component-inventory.md
git commit -m "docs: add component inventory from Milestone 2a measurement data"
git push
```

---

### Task 2: Reference Audit — Global (Header, Footer, Typography Scale, Color Palette, Breakpoints)

**Files:**
- Create: `docs/reference-audit/00-global.md`

**Interfaces:**
- Consumes: `docs/design-measurements/home/*.json` (all 5 viewports — header/footer are present on every page but Home is the canonical sample), plus cross-check 2-3 other templates' `typography`/`colors` arrays to confirm which styles are truly global (appear on every template) vs. page-specific.
- Produces: one section of the final merged doc. Task 6 concatenates this file's content under a `## Global (Header, Footer, Typography, Colors)` heading — write this file's own top-level heading as `# Global` so Task 6's concatenation reads cleanly.

- [ ] **Step 1: Document the typography scale**

From `docs/design-measurements/home/desktop.json`'s `typography` array (sorted by frequency already), identify the top 8-10 distinct styles by `count` and map each to a role (H1, H2, H3, body, nav link, button label, caption/small print) using the `samples` array and cross-referencing the screenshot at `docs/reference-screenshots/home/desktop.png` for visual placement. Repeat for `docs/design-measurements/home/mobile.json` to capture the responsive scale (per spec §3's "font size within 1px" acceptance target in §4, exact values matter). Write a markdown table: `Role | Desktop size/weight/line-height | Mobile size/weight/line-height | Font family`.

- [ ] **Step 2: Document the color palette**

From `docs/design-measurements/home/desktop.json`'s `colors` array, list every color with `count >= 5` (frequent = likely a real design-system color, not a one-off). Group into: primary/brand, text (body, heading, muted), background (page, card, section), and border. Note the hex value for each. Cross-check against `docs/design-measurements/tour-detail/desktop.json` and one more template to confirm which colors are truly global vs. page-specific — mark page-specific colors as such rather than including them in the "global palette."

- [ ] **Step 3: Document the container/breakpoint behavior**

From the `container` field in every viewport's JSON across at least 3 templates, document: desktop/laptop container width (should be ~1200px per Milestone 2a's real data), and explicitly note that `mostCommonContainerWidth` is `null` at tablet/mobile/narrow-mobile — explain why per Milestone 2a's finding (the reference site's container `max-width` constraint doesn't bind below a certain viewport, so there's no distinct boxed width to detect; content is effectively full-width with side padding at those sizes). Do not present a fabricated "container width" for those viewports.

- [ ] **Step 4: Document header and footer structure**

Using the screenshots (`docs/reference-screenshots/home/*.png` at all 5 viewports) plus `docs/design-measurements/home/*.json`'s `buttons` array (the header's "Book Now" CTA and nav links should appear there), describe: header layout (logo position, nav item order, Book Now button placement, sticky behavior — sticky behavior is NOT directly measurable from a static screenshot/JSON snapshot, note this as "requires direct interaction testing, not inferable from this data" rather than guessing), and footer column structure (visible from the screenshot's visual layout — describe column count and general content grouping, not exact text).

- [ ] **Step 5: Commit**

```bash
git add docs/reference-audit/00-global.md
git commit -m "docs: add global (header/footer/typography/color) reference audit section"
git push
```

---

### Task 3: Reference Audit — Home Page

**Files:**
- Create: `docs/reference-audit/01-home.md`

**Interfaces:**
- Consumes: `docs/design-measurements/home/*.json` (all 5 viewports), `docs/reference-screenshots/home/*.png`.
- Produces: `# Home`-headed file, concatenated by Task 6.

- [ ] **Step 1: Document section order**

From the screenshot `docs/reference-screenshots/home/desktop.png` (a full-page capture), visually walk top-to-bottom and list every distinct section in order, matching against spec §5.2's expected list (hero, featured tour grid, narrative/destination section, tabbed area, destination cards, itinerary tabs, transport cards, accommodation gallery, "why choose us" grid, testimonials, editorial CTA, booking interface, footer). Note any spec-listed section NOT observed, or any observed section not in the spec list.

- [ ] **Step 2: Document section-level measurements**

For the featured tour grid and any other card-grid sections, pull exact values from `docs/design-measurements/home/desktop.json`'s `cards` array (item width/height/radius/shadow/gap/image aspect ratio) and the equivalent mobile/narrow-mobile files (to document responsive stacking — 1 column at mobile is expected, confirm via `itemWidth` scaling down and `parentClassName` staying consistent across viewports).

- [ ] **Step 3: Document responsive behavior per breakpoint**

For each of the 5 viewports, note what visibly changes using the screenshots (hidden elements, stacked columns, hamburger menu appearing) — this is a visual comparison task using the actual PNG files, not something in the JSON.

- [ ] **Step 4: Commit**

```bash
git add docs/reference-audit/01-home.md
git commit -m "docs: add Home page reference audit section"
git push
```

---

### Task 4: Reference Audit — Tour Detail Template

**Files:**
- Create: `docs/reference-audit/02-tour-detail.md`

**Interfaces:**
- Consumes: `docs/design-measurements/tour-detail/*.json` (the primary 4-Days-3-Nights sample, all 5 viewports), `docs/design-measurements/tour-detail-variation/*.json` (all 3 samples, content-variation only), `docs/reference-screenshots/tours-ha-giang-loop-4-days-3-nights/*.png` (and the other 2 tour screenshot folders from Milestone 1 for visual cross-reference only).

- [ ] **Step 1: Document the fixed template structure**

Using the primary sample's measurements and screenshots, document per spec §5.4's required field list: hero/gallery, price variants and CTA, destination highlights, itinerary layout, included/excluded lists, vehicle/ride options, accommodation options, FAQs, related tours, final CTA — for each, note the measured styling (typography role, colors, spacing) from the JSON, explicitly citing which template/viewport file the values came from.

- [ ] **Step 2: Document what varies by tour length (using the variation samples)**

From the 3 `tour-detail-variation/*.json` files, write a table: Tour | Itinerary Day Count | Price Sample Count | Visible Image Count, showing the real numbers (2 Days 1 Night: 3 days/4 prices/10 images; 4 Days 3 Nights: 5 days/4 prices/16 images; Cao Bang 6 Days 5 Nights: 7 days/4 prices/19 images — verify these against the actual files, don't assume these numbers are still current, Milestone 2a's fix wave re-measured this data). Note that itinerary day count includes a "Day 0" per spec §5.4 ("Day 0 and unlimited following days") — confirm this against the real day-count numbers (e.g. is 3 days really "Day 0, 1, 2" for a "2 Days 1 Night" tour?).

- [ ] **Step 3: Commit**

```bash
git add docs/reference-audit/02-tour-detail.md
git commit -m "docs: add Tour Detail template reference audit section"
git push
```

---

### Task 5: Reference Audit — Secondary Pages

**Files:**
- Create: `docs/reference-audit/03-secondary-pages.md`

**Interfaces:**
- Consumes: `docs/design-measurements/{tours-archive,motorbike-rental,blog-archive,blog-single,contact,about,terms,404}/*.json` and matching `docs/reference-screenshots/` folders.

- [ ] **Step 1: Document each of the 8 remaining templates**

For each of: Tours Archive, Motorbike Rental, Blog Archive, Blog Single Article, Contact, About, Terms/Privacy, 404 — write a subsection with: section order (from the screenshot), any distinct typography/color not already covered in Task 2's global section, card/button measurements if present, and responsive behavior notes. Keep each subsection proportional to the page's actual complexity (per Task 3's spot-check: `terms/tablet.json` was noted as "sparse but legitimate" — a legal page needs a much shorter writeup than the Tours Archive).

- [ ] **Step 2: Note the two page types with no reference data**

Per Milestone 2a's plan, blog category/tag archive and on-site search were never captured (not observable on the live reference site). Carry this forward explicitly in this file — these two page types will be built per spec §5 items 5 and 12 using standard WordPress archive/search template conventions, not measured from a reference.

- [ ] **Step 3: Commit**

```bash
git add docs/reference-audit/03-secondary-pages.md
git commit -m "docs: add secondary pages reference audit section"
git push
```

---

### Task 6: Merge Audit Sections & Extract Design Tokens

**Files:**
- Create: `docs/reference-audit.md` (the final spec-mandated file — merges Tasks 2-5's output)
- Create: `docs/design-tokens.md`
- Modify: none of the section files (Tasks 2-5's files stay as-is under `docs/reference-audit/` for traceability)

**Interfaces:**
- Consumes: `docs/reference-audit/00-global.md`, `01-home.md`, `02-tour-detail.md`, `03-secondary-pages.md` (Tasks 2-5's output — this task cannot start until all four exist).
- Produces: `docs/reference-audit.md` (the file spec §3 explicitly requires by this exact path), `docs/design-tokens.md` (input for Milestone 4's `theme.json`).

- [ ] **Step 1: Concatenate the four section files**

Create `docs/reference-audit.md` starting with a short header (capture date, source data references — `docs/design-measurements/`, `docs/reference-screenshots/`, both from Milestones 1-2a), then the contents of `00-global.md`, `01-home.md`, `02-tour-detail.md`, `03-secondary-pages.md` in that order, each under its own `##` heading (adjust each section file's internal heading level from `#` to `##` when concatenating, or use `##` consistently — pick one and apply it uniformly).

- [ ] **Step 2: Extract and write the consolidated design tokens**

Create `docs/design-tokens.md` — pull every concrete value documented in Task 2 (typography scale, color palette, container widths) plus any card/button radius/shadow values referenced across Tasks 3-5, into clean token tables ready for a future `theme.json` author to encode directly:

```markdown
## Colors
| Token name | Hex | Usage |
|---|---|---|

## Typography
| Token name | Font family | Size (desktop) | Size (mobile) | Weight | Line height |
|---|---|---|---|---|---|

## Spacing / Radius / Shadow
| Token name | Value | Usage |
|---|---|---|
```

Invent clear, original token names (e.g. `color-primary`, `font-size-h1`, `radius-card`) — do not use the reference site's own CSS class/variable names even if visible in the raw JSON's `className` fields.

- [ ] **Step 3: Self-review against spec §3's required content list**

Re-read spec §3's bullet list (screenshot filename/date, section order, container width/gutters, typography, colors, card dimensions, button specs, breakpoint behavior, animation/transition type). Confirm `docs/reference-audit.md` covers every item. Animation/transition type/duration is explicitly NOT measurable from this plan's static-screenshot-based data (noted as a gap in Milestone 1's own environment audit) — call this out as a known gap requiring a follow-up computed-style pass (reading `transition`/`animation` CSS properties directly) rather than silently omitting it.

- [ ] **Step 4: Commit**

```bash
git add docs/reference-audit.md docs/design-tokens.md
git commit -m "docs: merge reference audit sections and extract design tokens"
git push
```

---

## Definition of done for this milestone

- `docs/component-inventory.md` exists, covers every spec §5-named UI element (or explicitly notes it wasn't observed).
- `docs/reference-audit.md` exists at the exact path spec §3 requires, covering every item in its required content list (with animation/transition explicitly flagged as a known gap).
- `docs/design-tokens.md` exists with clean, original token names ready for Milestone 4.
- No reference-site prose is committed anywhere — only measured values and original descriptive wording.
- All 6 tasks are committed and pushed to `origin/master`.
- **Report back to the user for a test/approval checkpoint before starting Milestone 3** (companion plugin schema, roles, migrations, demo importer).
