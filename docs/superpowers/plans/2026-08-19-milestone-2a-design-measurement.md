# Milestone 2a: Design Measurement Tooling & Raw Data Collection Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a Playwright-based measurement tool that extracts real computed-CSS design data (typography, colors, container widths, card-grid geometry, button styles, tour-detail content variation) from the live reference site, and run it to produce raw JSON evidence Milestone 2b will turn into `docs/reference-audit.md`, a component inventory, and `theme.json` tokens.

**Architecture:** No WordPress code changes. A new standalone Node/Playwright tool at `tools/design-audit/`, structured as small "probe" modules (one per measurement concern) that each take a Playwright `page` and return deduplicated, frequency-ranked findings — not raw per-element dumps. An orchestrator runs every probe against 10 distinct page templates (deduplicating the 3 captured tour-detail pages down to one measured template, per the user's explicit correction that they share one admin-driven template) at all 5 spec viewports, plus a lightweight content-variation probe against all 3 tour-detail samples. Because we don't know looptrails.com's actual CSS class names in advance, every probe uses **generic structural heuristics** (frequency-based style deduplication, repeated-sibling-geometry detection for card grids, padding/background heuristics for buttons) rather than hardcoded selectors — this also makes the tool resilient if the reference site's markup changes.

**Tech Stack:** Node.js v22.14.0, Playwright 1.62.1 (same versions already verified working in Milestone 1).

**Spec:** `docs/AI_AGENT_WORDPRESS_TOUR_WEBSITE_SPEC.md` (v2.0) — this plan implements the measurement half of spec §3 ("Reference-first visual reconstruction protocol"): container width/gutters, typography, exact colors, card dimensions/radius/shadow, button height/padding/radius/hover, for spec §13 Milestone 2. The written analysis (`docs/reference-audit.md`, component inventory, `theme.json`) is Milestone 2b, a separate plan — this plan's job ends at producing verified raw measurement data.

## Global Constraints

- Capture viewports: exactly desktop 1440×1000, laptop 1280×800, tablet 768×1024, mobile 390×844, narrow mobile 360×800 (spec §3) — same set Milestone 1 used, reuse the same names.
- **Reuse Milestone 1's hard-won fixes from the start, don't reintroduce the same bugs:** derive the repo root from the script's own file location (`fileURLToPath(import.meta.url)`), never from `process.cwd()` — a Milestone 1 bug (`path.resolve('../../...')`) silently wrote output outside the repo when invoked from the wrong directory. Use `process.argv[1] === fileURLToPath(import.meta.url)` for the CLI entry-point guard — the naive `import.meta.url === \`file://${process.argv[1]}\`` comparison silently never matches on Windows. Scroll the full page and force `loading="lazy"` images eager before measuring — a Milestone 1 Critical bug shipped blank below-the-fold screenshots because nothing triggered lazy-loaded content before capture; the same content must be loaded before this tool measures it, for the same reason.
- Do not download or redistribute Loop Trails' actual content. Short text samples (≤60 characters) captured by probes purely to help a human identify which measured style maps to which UI role (e.g. "this 32px/700 style appears on: 'Ha Giang Loop...'") are working data, not something to publish — `docs/design-measurements/` (the raw JSON output) is git-ignored, local-only, same treatment Milestone 1 gave `docs/reference-screenshots/` and for the same reason ([[feedback_image_copyright_boundary]]). Only measured **values** (pixel sizes, hex colors, counts) — never reference-site prose — may ever be committed; that happens in Milestone 2b's synthesized audit doc, not this plan.
- Every task ends with a git commit, pushed to `origin/master` (working directly on master, per established project convention this session — see [[project_tour_booking_website]]).
- If a probe legitimately finds zero or very few results against the real site (e.g. no repeated-sibling group meets the card-grid tolerance on some page), that is valid data to report, not a bug to paper over by loosening the heuristic without comment.

---

### Task 1: Typography & Color Style Probes

**Files:**
- Create: `tools/design-audit/package.json`
- Create: `tools/design-audit/probes/dedupe.mjs`
- Create: `tools/design-audit/probes/typography.mjs`
- Create: `tools/design-audit/probes/colors.mjs`

**Interfaces:**
- Produces: `dedupeBySignature(items, signatureFn)` (exported from `dedupe.mjs`) — takes an array of plain objects and a function mapping an item to a string signature, returns an array of `{ ...firstItemMinusSample, count, samples: string[] }` sorted by `count` descending. Consumed by both probes in this task, and by probes Task 2 writes.
- Produces: `scanTypography(page)` — async, takes a Playwright `Page`, returns `Promise<Array<{ tag, className, fontFamily, fontSize, fontWeight, lineHeight, letterSpacing, count, samples: string[] }>>` sorted by frequency descending.
- Produces: `scanColors(page)` — async, takes a Playwright `Page`, returns `Promise<Array<{ property: 'backgroundColor'|'color'|'borderTopColor', value: string, tag, className, count, samples: string[], hex: string }>>` sorted by frequency descending.
- These two functions are consumed by Task 3's orchestrator (`run-audit.mjs`) exactly as named here — do not rename.

- [ ] **Step 1: Scaffold the tool package**

Create `tools/design-audit/package.json`:

```json
{
  "name": "design-audit",
  "private": true,
  "type": "module",
  "scripts": {
    "smoke-test": "node smoke-test.mjs",
    "run-audit": "node run-audit.mjs"
  },
  "devDependencies": {
    "playwright": "1.62.1"
  }
}
```

Run:

```bash
cd tools/design-audit
npm install
npx playwright install chromium
```

Expected: `node_modules/` created (already git-ignored by the root `.gitignore`'s `node_modules/` rule), Chromium confirmed present (Milestone 1 already downloaded it once; this may be a no-op).

- [ ] **Step 2: Write the dedup helper**

Create `tools/design-audit/probes/dedupe.mjs`:

```javascript
export function dedupeBySignature(items, signatureFn) {
  const map = new Map();
  for (const item of items) {
    const sig = signatureFn(item);
    if (!map.has(sig)) {
      const { sample, ...rest } = item;
      map.set(sig, { ...rest, count: 0, samples: [] });
    }
    const entry = map.get(sig);
    entry.count += 1;
    if (entry.samples.length < 3 && item.sample) {
      entry.samples.push(item.sample);
    }
  }
  return [...map.values()].sort((a, b) => b.count - a.count);
}
```

- [ ] **Step 3: Write the typography probe**

Create `tools/design-audit/probes/typography.mjs`:

```javascript
import { dedupeBySignature } from './dedupe.mjs';

export async function scanTypography(page) {
  const raw = await page.evaluate(() => {
    const results = [];
    const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_ELEMENT);
    let node = walker.currentNode;
    while (node) {
      const hasDirectText = Array.from(node.childNodes).some(
        (n) => n.nodeType === Node.TEXT_NODE && n.textContent.trim().length > 0
      );
      if (hasDirectText) {
        const cs = getComputedStyle(node);
        const rect = node.getBoundingClientRect();
        if (rect.width > 0 && rect.height > 0) {
          results.push({
            tag: node.tagName.toLowerCase(),
            className: typeof node.className === 'string' ? node.className.slice(0, 80) : '',
            fontFamily: cs.fontFamily,
            fontSize: cs.fontSize,
            fontWeight: cs.fontWeight,
            lineHeight: cs.lineHeight,
            letterSpacing: cs.letterSpacing,
            sample: node.textContent.trim().slice(0, 60),
          });
        }
      }
      node = walker.nextNode();
    }
    return results;
  });

  return dedupeBySignature(
    raw,
    (item) => `${item.fontFamily}|${item.fontSize}|${item.fontWeight}|${item.lineHeight}|${item.letterSpacing}`
  );
}
```

- [ ] **Step 4: Write the color probe**

Create `tools/design-audit/probes/colors.mjs`:

```javascript
import { dedupeBySignature } from './dedupe.mjs';

function toHex(rgbString) {
  const match = rgbString.match(/rgba?\(([^)]+)\)/);
  if (!match) return rgbString;
  const parts = match[1].split(',').map((s) => parseFloat(s.trim()));
  const [r, g, b, a] = parts;
  if (a === 0) return 'transparent';
  const hex = '#' + [r, g, b].map((v) => Math.round(v).toString(16).padStart(2, '0')).join('');
  return a !== undefined && a < 1 ? `${hex} (alpha ${a})` : hex;
}

export async function scanColors(page) {
  const raw = await page.evaluate(() => {
    const results = [];
    document.querySelectorAll('body, body *').forEach((el) => {
      const cs = getComputedStyle(el);
      const rect = el.getBoundingClientRect();
      if (rect.width === 0 || rect.height === 0) return;
      ['backgroundColor', 'color', 'borderTopColor'].forEach((prop) => {
        const value = cs[prop];
        if (!value || value === 'rgba(0, 0, 0, 0)' || value === 'transparent') return;
        results.push({
          property: prop,
          value,
          tag: el.tagName.toLowerCase(),
          className: typeof el.className === 'string' ? el.className.slice(0, 80) : '',
          sample: el.tagName.toLowerCase(),
        });
      });
    });
    return results;
  });

  const deduped = dedupeBySignature(raw, (item) => `${item.property}|${item.value}`);
  return deduped.map((entry) => ({ ...entry, hex: toHex(entry.value) }));
}
```

- [ ] **Step 5: Verify both probes against the live reference site**

Run from `tools/design-audit/`:

```bash
node --input-type=module -e "
import { chromium } from 'playwright';
import { scanTypography } from './probes/typography.mjs';
import { scanColors } from './probes/colors.mjs';
const browser = await chromium.launch();
const page = await (await browser.newContext({ viewport: { width: 1440, height: 1000 } })).newPage();
await page.goto('https://looptrails.com/', { waitUntil: 'networkidle', timeout: 30000 });
const typo = await scanTypography(page);
const colors = await scanColors(page);
console.log('typography styles:', typo.length, 'colors:', colors.length);
console.log(JSON.stringify(typo.slice(0, 3), null, 2));
console.log(JSON.stringify(colors.slice(0, 3), null, 2));
await browser.close();
"
```

Expected: both counts are greater than 0 (a real page has more than zero distinct text styles and colors), and the printed sample entries show real, plausible values — actual pixel font sizes (e.g. `"16px"`, not `undefined`), a real font-family string, real hex colors (e.g. `"#1a1a1a"`, not `NaN` or malformed). If either probe returns 0 results or malformed values, the probe has a bug — fix it before moving on; do not proceed with broken probes.

- [ ] **Step 6: Commit**

```bash
git add tools/design-audit/package.json tools/design-audit/package-lock.json tools/design-audit/probes/dedupe.mjs tools/design-audit/probes/typography.mjs tools/design-audit/probes/colors.mjs
git commit -m "feat: add typography and color style-inventory probes"
git push
```

---

### Task 2: Layout Probes (Container, Card Grids, Buttons) & Tour-Detail Variation Probe

**Files:**
- Create: `tools/design-audit/probes/container.mjs`
- Create: `tools/design-audit/probes/cards.mjs`
- Create: `tools/design-audit/probes/buttons.mjs`
- Create: `tools/design-audit/variation-probe.mjs`

**Interfaces:**
- Consumes: nothing from Task 1 (these probes are independent of the typography/color probes — no shared helper needed since the geometry/text heuristics here don't need frequency deduplication in the same shape).
- Produces: `scanContainer(page)` — async, returns `Promise<{ viewportWidth, mostCommonContainerWidth: number|null, candidateCount, topCandidates: Array }>`.
- Produces: `scanCardGrids(page)` — async, returns `Promise<Array<{ parentTag, parentClassName, itemCount, itemWidth, itemHeight, borderRadius, boxShadow, gapX, imageAspectRatio }>>`.
- Produces: `scanButtons(page)` — async, returns `Promise<Array<{ tag, text, rest: {height, paddingTop, paddingRight, borderRadius, backgroundColor, color, boxShadow, fontSize, fontWeight}, hover: {backgroundColor, color, boxShadow}|null }>>`.
- Produces: `scanTourVariation(page)` — async, returns `Promise<{ itineraryDayCount, itineraryDaySamples, priceLikeElementCount, priceSamples, totalVisibleImageCount }>`.
- All four are consumed by Task 3's `run-audit.mjs` exactly as named here — do not rename.

- [ ] **Step 1: Write the container-width probe**

Create `tools/design-audit/probes/container.mjs`:

```javascript
export async function scanContainer(page) {
  return page.evaluate(() => {
    const viewportWidth = window.innerWidth;
    const candidates = [];
    document.querySelectorAll('body *').forEach((el) => {
      const cs = getComputedStyle(el);
      const rect = el.getBoundingClientRect();
      if (rect.width <= 0) return;
      const maxWidth = cs.maxWidth;
      if (maxWidth && maxWidth !== 'none') {
        const parsedMaxWidth = parseFloat(maxWidth);
        if (!Number.isNaN(parsedMaxWidth) && parsedMaxWidth > 320 && parsedMaxWidth < viewportWidth) {
          candidates.push({
            tag: el.tagName.toLowerCase(),
            className: typeof el.className === 'string' ? el.className.slice(0, 80) : '',
            maxWidth: parsedMaxWidth,
            renderedWidth: Math.round(rect.width),
            paddingLeft: cs.paddingLeft,
            paddingRight: cs.paddingRight,
          });
        }
      }
    });

    const widthCounts = new Map();
    for (const c of candidates) {
      widthCounts.set(c.renderedWidth, (widthCounts.get(c.renderedWidth) || 0) + 1);
    }
    const sortedWidths = [...widthCounts.entries()].sort((a, b) => b[1] - a[1]);
    const topWidth = sortedWidths[0] ? sortedWidths[0][0] : null;

    return {
      viewportWidth,
      mostCommonContainerWidth: topWidth,
      candidateCount: candidates.length,
      topCandidates: candidates.filter((c) => c.renderedWidth === topWidth).slice(0, 3),
    };
  });
}
```

- [ ] **Step 2: Write the card-grid probe**

Create `tools/design-audit/probes/cards.mjs`:

```javascript
export async function scanCardGrids(page) {
  return page.evaluate(() => {
    const TOLERANCE = 0.1;
    function rectsSimilar(a, b) {
      const widthDiff = Math.abs(a.width - b.width) / Math.max(a.width, b.width);
      const heightDiff = Math.abs(a.height - b.height) / Math.max(a.height, b.height);
      return widthDiff <= TOLERANCE && heightDiff <= TOLERANCE;
    }

    const groups = [];
    document.querySelectorAll('body *').forEach((parent) => {
      const children = Array.from(parent.children).filter((child) => {
        const rect = child.getBoundingClientRect();
        return rect.width > 60 && rect.height > 60;
      });
      if (children.length < 3) return;

      const rects = children.map((child) => child.getBoundingClientRect());
      const allSimilar = rects.every((r) => rectsSimilar(rects[0], r));
      if (!allSimilar) return;

      const first = children[0];
      const firstRect = rects[0];
      const cs = getComputedStyle(first);
      const image = first.querySelector('img');
      let imageAspectRatio = null;
      if (image) {
        const imgRect = image.getBoundingClientRect();
        if (imgRect.height > 0) imageAspectRatio = +(imgRect.width / imgRect.height).toFixed(3);
      }
      const gapX = rects.length > 1 ? Math.round(rects[1].left - rects[0].right) : null;

      groups.push({
        parentTag: parent.tagName.toLowerCase(),
        parentClassName: typeof parent.className === 'string' ? parent.className.slice(0, 80) : '',
        itemCount: children.length,
        itemWidth: Math.round(firstRect.width),
        itemHeight: Math.round(firstRect.height),
        borderRadius: cs.borderRadius,
        boxShadow: cs.boxShadow,
        gapX,
        imageAspectRatio,
      });
    });

    const seen = new Set();
    return groups.filter((g) => {
      const key = `${g.parentTag}|${g.parentClassName}|${g.itemCount}`;
      if (seen.has(key)) return false;
      seen.add(key);
      return true;
    });
  });
}
```

- [ ] **Step 3: Write the button probe (with hover-state capture)**

Create `tools/design-audit/probes/buttons.mjs`:

```javascript
export async function scanButtons(page) {
  const handles = await page.locator('button, a').elementHandles();
  const results = [];

  for (const handle of handles) {
    const box = await handle.boundingBox();
    if (!box || box.height < 24 || box.height > 90 || box.width < 40) continue;

    const text = (await handle.textContent())?.trim() ?? '';
    if (!text || text.length > 40) continue;

    const restStyle = await handle.evaluate((el) => {
      const cs = getComputedStyle(el);
      return {
        height: cs.height,
        paddingTop: cs.paddingTop,
        paddingRight: cs.paddingRight,
        borderRadius: cs.borderRadius,
        backgroundColor: cs.backgroundColor,
        color: cs.color,
        boxShadow: cs.boxShadow,
        fontSize: cs.fontSize,
        fontWeight: cs.fontWeight,
      };
    });

    const looksLikeButton =
      restStyle.backgroundColor !== 'rgba(0, 0, 0, 0)' || restStyle.boxShadow !== 'none';
    if (!looksLikeButton) continue;

    let hoverStyle = null;
    try {
      await handle.hover({ timeout: 2000 });
      hoverStyle = await handle.evaluate((el) => {
        const cs = getComputedStyle(el);
        return { backgroundColor: cs.backgroundColor, color: cs.color, boxShadow: cs.boxShadow };
      });
    } catch {
      hoverStyle = null;
    }

    results.push({
      tag: await handle.evaluate((el) => el.tagName.toLowerCase()),
      text: text.slice(0, 40),
      rest: restStyle,
      hover: hoverStyle,
    });
  }

  const seen = new Set();
  return results.filter((r) => {
    const key = `${r.tag}|${r.rest.height}|${r.rest.paddingTop}|${r.rest.borderRadius}|${r.rest.backgroundColor}`;
    if (seen.has(key)) return false;
    seen.add(key);
    return true;
  });
}
```

- [ ] **Step 4: Write the tour-detail content-variation probe**

Create `tools/design-audit/variation-probe.mjs`:

```javascript
export async function scanTourVariation(page) {
  return page.evaluate(() => {
    const bodyText = document.body.innerText;
    const dayMatches = bodyText.match(/\bDay\s+\d+\b/gi) || [];
    const uniqueDays = [...new Set(dayMatches.map((d) => d.toLowerCase()))];

    const priceLikeElements = Array.from(document.querySelectorAll('body *')).filter((el) => {
      if (el.children.length > 0) return false;
      const text = el.textContent.trim();
      return /(\$|USD|VND|₫)\s?[\d,.]+/.test(text) && text.length < 40;
    });

    const allImages = Array.from(document.querySelectorAll('img')).filter((img) => {
      const rect = img.getBoundingClientRect();
      return rect.width > 80 && rect.height > 80;
    });

    return {
      itineraryDayCount: uniqueDays.length,
      itineraryDaySamples: uniqueDays.slice(0, 5),
      priceLikeElementCount: priceLikeElements.length,
      priceSamples: priceLikeElements.slice(0, 5).map((el) => el.textContent.trim()),
      totalVisibleImageCount: allImages.length,
    };
  });
}
```

- [ ] **Step 5: Verify all four probes against the live reference site**

Run from `tools/design-audit/`:

```bash
node --input-type=module -e "
import { chromium } from 'playwright';
import { scanContainer } from './probes/container.mjs';
import { scanCardGrids } from './probes/cards.mjs';
import { scanButtons } from './probes/buttons.mjs';
import { scanTourVariation } from './variation-probe.mjs';
const browser = await chromium.launch();
const ctx = await browser.newContext({ viewport: { width: 1440, height: 1000 } });
const page = await ctx.newPage();
await page.goto('https://looptrails.com/', { waitUntil: 'networkidle', timeout: 30000 });
console.log('container:', JSON.stringify(await scanContainer(page)));
console.log('cards found:', (await scanCardGrids(page)).length);
console.log('buttons found:', (await scanButtons(page)).length);
await page.goto('https://looptrails.com/tours/ha-giang-loop-4-days-3-nights/', { waitUntil: 'networkidle', timeout: 30000 });
console.log('tour variation:', JSON.stringify(await scanTourVariation(page)));
await browser.close();
"
```

Expected: `scanContainer` returns a plausible `mostCommonContainerWidth` (a number between roughly 960 and 1440 for a desktop viewport, not `null`); `scanCardGrids` and `scanButtons` return arrays (zero results is acceptable and worth noting in your report if it happens — see this plan's Global Constraints — but confirm it's not crashing or throwing); `scanTourVariation` on the tour detail page returns `itineraryDayCount` greater than 0 (a multi-day tour page should mention "Day 1", "Day 2", etc. somewhere in its content).

- [ ] **Step 6: Commit**

```bash
git add tools/design-audit/probes/container.mjs tools/design-audit/probes/cards.mjs tools/design-audit/probes/buttons.mjs tools/design-audit/variation-probe.mjs
git commit -m "feat: add container, card-grid, button, and tour-variation probes"
git push
```

---

### Task 3: Orchestration — Full Measurement Run

**Files:**
- Create: `tools/design-audit/pages.mjs`
- Create: `tools/design-audit/run-audit.mjs`
- Create: `tools/design-audit/smoke-test.mjs`
- Create: `docs/design-measurements/README.md` (committed — meta-documentation only, no reference-site content)
- Modify: `.gitignore` (ignore `docs/design-measurements/` contents, but allow the README through)

**Interfaces:**
- Consumes: `scanTypography`, `scanColors` from Task 1; `scanContainer`, `scanCardGrids`, `scanButtons`, `scanTourVariation` from Task 2.
- Produces: `REPO_ROOT` (exported from `run-audit.mjs`), `measurePage(browser, target, viewport)` (exported from `run-audit.mjs`, used by the smoke test), `runFullAudit()` (exported and also runnable as a CLI via `npm run run-audit`). These are for this plan's own smoke test only — Milestone 2b's plan will read the JSON files this task produces, not import these functions.

- [ ] **Step 1: Write the deduplicated page/template list**

Create `tools/design-audit/pages.mjs`:

```javascript
export const templates = [
  { label: 'Home', slug: 'home', url: 'https://looptrails.com/' },
  { label: 'Tours Archive', slug: 'tours-archive', url: 'https://looptrails.com/tours/' },
  { label: 'Tour Detail', slug: 'tour-detail', url: 'https://looptrails.com/tours/ha-giang-loop-4-days-3-nights/' },
  { label: 'Motorbike Rental', slug: 'motorbike-rental', url: 'https://looptrails.com/ha-giang-motorbike-rental/' },
  { label: 'Blog Archive', slug: 'blog-archive', url: 'https://looptrails.com/blog/' },
  { label: 'Blog Single Article', slug: 'blog-single', url: 'https://looptrails.com/guide/getting-sick-on-the-loop/' },
  { label: 'Contact', slug: 'contact', url: 'https://looptrails.com/contact/' },
  { label: 'About', slug: 'about', url: 'https://looptrails.com/about-loop-trails-tours-ha-giang/' },
  { label: 'Terms, Conditions & Privacy Policy', slug: 'terms', url: 'https://looptrails.com/terms-and-conditions/' },
  { label: '404 Not Found', slug: '404', url: 'https://looptrails.com/this-page-does-not-exist-xyz123/' },
];

export const tourDetailVariationSamples = [
  { label: 'Tour Detail — 2 Days 1 Night', slug: 'tour-detail-2d1n', url: 'https://looptrails.com/tours/ha-giang-loop-2-days-1-night/' },
  { label: 'Tour Detail — 4 Days 3 Nights (primary)', slug: 'tour-detail-4d3n', url: 'https://looptrails.com/tours/ha-giang-loop-4-days-3-nights/' },
  { label: 'Tour Detail — Cao Bang 6 Days 5 Nights', slug: 'tour-detail-6d5n', url: 'https://looptrails.com/tours/ha-giang-cao-bang-6-days/' },
];

export const VIEWPORTS = [
  { name: 'desktop', width: 1440, height: 1000 },
  { name: 'laptop', width: 1280, height: 800 },
  { name: 'tablet', width: 768, height: 1024 },
  { name: 'mobile', width: 390, height: 844 },
  { name: 'narrow-mobile', width: 360, height: 800 },
];
```

This deduplicates the 3 tour-detail pages captured in Milestone 1 down to ONE measured template (`tour-detail`, using the 4-Days-3-Nights page as the representative sample since it's the middle length), per the user's explicit correction that all tour detail pages share one admin-driven template rather than being 3 distinct page designs. The 3 original URLs are preserved separately in `tourDetailVariationSamples` for the content-variation probe (Task 2's `scanTourVariation`), which measures what changes with tour length (itinerary day count, price row count, image count) rather than re-measuring the same fixed layout 3 times.

- [ ] **Step 2: Write the orchestrator**

Create `tools/design-audit/run-audit.mjs`:

```javascript
import { chromium } from 'playwright';
import { mkdir, writeFile } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { templates, tourDetailVariationSamples, VIEWPORTS } from './pages.mjs';
import { scanTypography } from './probes/typography.mjs';
import { scanColors } from './probes/colors.mjs';
import { scanContainer } from './probes/container.mjs';
import { scanCardGrids } from './probes/cards.mjs';
import { scanButtons } from './probes/buttons.mjs';
import { scanTourVariation } from './variation-probe.mjs';

export const REPO_ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../..');
const OUT_DIR = path.join(REPO_ROOT, 'docs/design-measurements');

export async function measurePage(browser, target, viewport) {
  const context = await browser.newContext({ viewport: { width: viewport.width, height: viewport.height } });
  const page = await context.newPage();
  await page.goto(target.url, { waitUntil: 'networkidle', timeout: 30000 });

  await page.evaluate(async () => {
    document.querySelectorAll('img[loading="lazy"], iframe[loading="lazy"]')
      .forEach((el) => el.setAttribute('loading', 'eager'));
    await new Promise((resolve) => {
      let y = 0;
      const step = () => {
        window.scrollTo(0, y);
        y += window.innerHeight;
        if (y < document.body.scrollHeight) setTimeout(step, 150);
        else { window.scrollTo(0, 0); setTimeout(resolve, 500); }
      };
      step();
    });
  });
  await page.waitForLoadState('networkidle');

  const [typography, colors, container, cards, buttons] = await Promise.all([
    scanTypography(page),
    scanColors(page),
    scanContainer(page),
    scanCardGrids(page),
    scanButtons(page),
  ]);

  await context.close();
  return { typography, colors, container, cards, buttons, capturedAt: new Date().toISOString() };
}

export async function runFullAudit() {
  const browser = await chromium.launch();
  const summary = [];

  for (const target of templates) {
    const dir = path.join(OUT_DIR, target.slug);
    await mkdir(dir, { recursive: true });
    for (const viewport of VIEWPORTS) {
      const data = await measurePage(browser, target, viewport);
      const filePath = path.join(dir, `${viewport.name}.json`);
      await writeFile(filePath, JSON.stringify(data, null, 2) + '\n');
      summary.push({
        label: target.label,
        slug: target.slug,
        viewport: viewport.name,
        file: path.relative(REPO_ROOT, filePath).replace(/\\/g, '/'),
      });
      console.log(`Measured ${target.slug} @ ${viewport.name}`);
    }
  }

  const variationDir = path.join(OUT_DIR, 'tour-detail-variation');
  await mkdir(variationDir, { recursive: true });
  for (const sample of tourDetailVariationSamples) {
    const context = await browser.newContext({ viewport: { width: 1440, height: 1000 } });
    const page = await context.newPage();
    await page.goto(sample.url, { waitUntil: 'networkidle', timeout: 30000 });
    const variation = await scanTourVariation(page);
    await context.close();
    const filePath = path.join(variationDir, `${sample.slug}.json`);
    await writeFile(filePath, JSON.stringify({ ...sample, ...variation }, null, 2) + '\n');
    summary.push({
      label: sample.label,
      slug: sample.slug,
      viewport: 'desktop-only',
      file: path.relative(REPO_ROOT, filePath).replace(/\\/g, '/'),
    });
    console.log(`Measured tour-detail variation: ${sample.slug}`);
  }

  await browser.close();
  const summaryPath = path.join(OUT_DIR, 'summary.json');
  await writeFile(summaryPath, JSON.stringify(summary, null, 2) + '\n');
  console.log(`Done. ${summary.length} measurement files written. Summary: ${summaryPath}`);
  return summary;
}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
  runFullAudit();
}
```

- [ ] **Step 3: Write the smoke test**

Create `tools/design-audit/smoke-test.mjs`:

```javascript
import { chromium } from 'playwright';
import { measurePage } from './run-audit.mjs';

const browser = await chromium.launch();
const target = { label: 'Home (smoke test)', slug: 'home', url: 'https://looptrails.com/' };
const viewport = { name: 'desktop', width: 1440, height: 1000 };
const result = await measurePage(browser, target, viewport);
await browser.close();

if (result.typography.length === 0) throw new Error('No typography styles detected — probe likely broken');
if (result.colors.length === 0) throw new Error('No colors detected — probe likely broken');
if (!result.container.mostCommonContainerWidth) throw new Error('No container width detected — probe likely broken');

console.log(
  `Smoke test passed: ${result.typography.length} typography styles, ${result.colors.length} colors, ` +
  `container width ${result.container.mostCommonContainerWidth}px, ${result.cards.length} card-grid groups, ` +
  `${result.buttons.length} button styles detected.`
);
```

- [ ] **Step 4: Run the smoke test and verify it passes**

```bash
cd tools/design-audit
npm run smoke-test
```

Expected: `Smoke test passed: ...` with plausible non-zero counts for typography and colors (cards/buttons may legitimately be 0, but should not error). If the reference site is unreachable, stop and report — don't fabricate a pass.

- [ ] **Step 5: Update `.gitignore` for the raw measurement data**

Edit the root `.gitignore`, adding near the existing `/docs/reference-screenshots/` line:

```
# Design measurement raw data (contains short reference-site text samples for
# identification — local-only evidence, same treatment as reference-screenshots/)
/docs/design-measurements/*
!/docs/design-measurements/README.md
```

- [ ] **Step 6: Run the full measurement collection**

```bash
cd tools/design-audit
npm run run-audit
```

Expected: 10 templates × 5 viewports = 50 lines of `Measured <slug> @ <viewport>`, plus 3 lines of `Measured tour-detail variation: <slug>`, ending with `Done. 53 measurement files written. Summary: ...`. This will take noticeably longer than Milestone 1's screenshot capture (each page now runs 5 computed-style probes plus button hover simulation, not just a screenshot) — that's expected, not a sign of a hang.

- [ ] **Step 7: Verify the output**

```bash
node -e "const s=require('./docs/design-measurements/summary.json'); console.log(s.length)"
```

Expected: `53`. Spot-check 2-3 of the actual JSON files (e.g. `docs/design-measurements/home/desktop.json`, `docs/design-measurements/tour-detail/desktop.json`) to confirm they contain real, plausible data (non-empty `typography`/`colors` arrays, real pixel/hex values) — not just that the files exist.

- [ ] **Step 8: Write the README (committed, meta-only)**

Create `docs/design-measurements/README.md`:

```markdown
# Design Measurements

Raw computed-CSS measurement data for the reference site (looptrails.com), collected 2026-08-19 via `tools/design-audit/run-audit.mjs`, for Milestone 2b to turn into `docs/reference-audit.md`, a component inventory, and `theme.json` design tokens.

**This directory's JSON contents are git-ignored** (only this README is committed) — the data includes short verbatim text samples from the reference site captured purely to help identify which measured style maps to which UI element, and per this project's content-copyright boundary those never get published, even as structured data. Only measured *values* (pixel sizes, hex colors, counts) may be quoted in Milestone 2b's committed audit doc — never the reference site's prose.

## What's captured

- `<template-slug>/<viewport>.json` for 10 templates × 5 viewports (50 files): typography styles, colors, container width, card-grid geometry, button styles (rest + hover), all frequency-ranked.
- `tour-detail-variation/<sample-slug>.json` for the 3 tour-detail page samples (2 Days 1 Night / 4 Days 3 Nights / Cao Bang 6 Days 5 Nights): itinerary day count, price-row count, image count — what varies by tour length within the one shared Tour Detail template, rather than 3 redundant full measurements of an identical layout.
- `summary.json`: a flat index of every file above.

## Re-run

```
cd tools/design-audit && npm ci && npx playwright install chromium && npm run run-audit
```
```

- [ ] **Step 9: Commit**

```bash
git add tools/design-audit/pages.mjs tools/design-audit/run-audit.mjs tools/design-audit/smoke-test.mjs docs/design-measurements/README.md .gitignore
git status --short
```

Confirm the status output shows only the files above staged — no files under `docs/design-measurements/` other than `README.md` should appear (they must be git-ignored by Step 5's rule). If any JSON measurement file appears in `git status`, the `.gitignore` pattern is wrong — fix it before committing.

```bash
git commit -m "chore: run full design measurement collection"
git push
```

---

## Definition of done for this milestone

- All 6 probe modules (`dedupe`, `typography`, `colors`, `container`, `cards`, `buttons`) plus `variation-probe.mjs` exist and were each verified against the live reference site with plausible, non-fabricated output.
- `tools/design-audit/` has a working, re-runnable orchestrator with a passing smoke test.
- `docs/design-measurements/` contains 53 real JSON files plus `summary.json` (git-ignored, local-only) and a committed `README.md`.
- No reference-site prose is committed to git — only the README's meta-description and measured values.
- All three tasks are committed and pushed to `origin/master`.
- **Report back to the user for a test/approval checkpoint before starting Milestone 2b** (writing `docs/reference-audit.md`, the component inventory, and `theme.json` tokens from this data).
