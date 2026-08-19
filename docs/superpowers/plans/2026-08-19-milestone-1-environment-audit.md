# Milestone 1: Environment Audit & Reference Screenshots Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Confirm the local build environment is sound and capture full-page reference screenshots of looptrails.com at every required viewport, producing the raw evidence Milestone 2 (reference audit / design tokens) will measure against.

**Architecture:** No WordPress code changes in this milestone. Two deliverables: (1) `docs/environment-audit.md`, a written record of the local toolchain state; (2) a small standalone Node/Playwright CLI tool under `tools/reference-audit/` that screenshots a fixed list of reference URLs at the 5 spec-mandated viewports and writes a JSON manifest. Screenshots themselves are evidence artifacts, not source — they are written to `docs/reference-screenshots/` which is git-ignored (see [[feedback_image_copyright_boundary]]: never redistribute the reference site's real assets; local-only measurement screenshots are fine, publishing/committing them is not).

**Tech Stack:** Node.js 22 (already installed), Playwright 1.62 (already available via `npx`), PHP 8.2.31 / XAMPP (already installed), `C:\xampp\wp-cli.bat` (already installed and verified working against the current WP 7.0.4 install).

**Spec:** `docs/AI_AGENT_WORDPRESS_TOUR_WEBSITE_SPEC.md` (v2.0, 2026-08-19) — this plan implements spec section 13 Milestone 1 ("Environment audit, backup/staging confirmation and reference screenshots") and the viewport list from section 3.

## Global Constraints

- Platform: WordPress current stable (installed: 7.0.4), PHP 8.2+ (installed: 8.2.31). (spec §2.1)
- No Elementor, Elementor Pro, Divi, WPBakery, commercial theme, or page-builder runtime anywhere in this build. (spec §2.1)
- No paid plugin or paid SaaS required to render or operate the core site. (spec §2.1)
- Capture viewports, exactly these five: desktop 1440×1000, laptop 1280×800, tablet 768×1024, mobile 390×844, narrow mobile 360×800. (spec §3)
- Animation must be disabled during screenshot capture. (spec §4)
- Do not download or redistribute Loop Trails' logo, photos, reviews, or private assets — reference screenshots are local-only measurement evidence, never committed or published. (spec §1, [[feedback_image_copyright_boundary]])
- Every task ends with a git commit (local repo `c:\xampp\htdocs\looptrails`, remote `https://github.com/quangnnd2604/looptrails.git`, already initialized and pushed as of the root commit).

---

### Task 1: Environment Audit Document

**Files:**
- Create: `docs/environment-audit.md`

**Interfaces:**
- Produces: a written record later tasks and Milestone 2 can cite; no code interface.

- [ ] **Step 1: Gather current environment facts**

Run each command and note its output (values below were already captured this session — re-run only to confirm nothing drifted):

```bash
php --version
"/c/xampp/wp-cli.bat" core version
"/c/xampp/wp-cli.bat" plugin list --format=csv
"/c/xampp/wp-cli.bat" theme list --format=csv
node --version
npm --version
npx --yes playwright --version
php -m | grep -i gd
git remote -v
```

Expected (as confirmed this session):
- PHP 8.2.31 (XAMPP, `C:\xampp\php\php.exe`)
- WordPress 7.0.4
- Plugins: `akismet` (inactive), `hello` (inactive) — no Elementor, no PRO Elements, no leftover plugin from any prior build
- Themes: only the four WordPress default themes (`twentytwentyfive` active) — no leftover custom theme
- Node v22.14.0, npm 10.9.2, Playwright 1.62.1 available via `npx`
- GD extension: enabled
- git remote `origin` → `https://github.com/quangnnd2604/looptrails.git`

- [ ] **Step 2: Write the audit document**

Create `docs/environment-audit.md`:

```markdown
# Environment Audit

Date: 2026-08-19
Scope: `c:\xampp\htdocs\looptrails` local XAMPP install, ahead of Milestone 1 (spec §13.1).

## Local stack

| Component | Value |
|---|---|
| OS | Windows 11 Pro |
| Web server | XAMPP Apache (started via `C:\xampp\apache_start.bat` / stopped via `apache_stop.bat` — not a Windows service; `net start/stop` will not work) |
| PHP | 8.2.31, ZTS, at `C:\xampp\php\php.exe` |
| WordPress core | 7.0.4 |
| Database | MySQL/MariaDB via XAMPP, `DB_NAME=looptrails`, `DB_HOST=localhost` (credentials in `wp-config.php`, git-ignored) |
| WP-CLI | `C:\xampp\wp-cli.bat` — wraps XAMPP's own PHP against `wp-cli.phar`. **Always use this wrapper, never a bare `wp`/`php` on PATH** — a second, unrelated PHP install exists on this machine's PATH. |
| Node.js | v22.14.0 |
| npm | 10.9.2 |
| Playwright | 1.62.1, available via `npx playwright` (no persisted global install; project-local install added in Task 2) |
| GD extension | enabled (`php -m` lists `gd`) — required for WordPress image thumbnail generation |

## WordPress install state (fresh, confirmed empty of prior work)

- Active theme: `twentytwentyfive` (default). Inactive: `twentytwentyfour`, `twentytwentythree`, `twentytwentytwo`. No custom theme present.
- Plugins: `akismet` (inactive), `hello` (inactive). No Elementor, no PRO Elements, no `tour-booking-core`, no leftover plugin from any prior build attempt.
- This confirms the install is a genuine fresh start for spec v2.0 (native Gutenberg block theme architecture) — a prior session's Elementor-based work (see [[technical_wordpress_elementor_conventions]], now superseded/historical) is not present in this codebase and will not be built upon.

## Safety / staging confirmation

- This is a local-only XAMPP development environment (`localhost`), not a production or publicly reachable host. No production credentials, no live payment gateway access, no real customer data exist here.
- Version control: git repository initialized at `c:\xampp\htdocs\looptrails`, remote `origin` = `https://github.com/quangnnd2604/looptrails.git` (private working repo, confirmed empty before first push). `.gitignore` excludes WordPress core files, `wp-config.php`, uploads, default themes/plugins we do not modify, and `docs/reference-screenshots/`. Only our custom theme/plugin/docs are tracked.
- Backup strategy for this milestone: git history is the backup for source; the WordPress database itself holds no meaningful demo data yet at this stage, so no DB backup is required before Milestone 1 work. A DB export step will be added to the plan for Milestone 3 (companion plugin schema/migrations) before any schema changes are applied.

## Screenshot capture inventory

See `docs/reference-screenshot-manifest.json` (produced by Task 3 of this plan) for the full list of captured reference pages, viewports, and HTTP status codes.
```

- [ ] **Step 3: Verify the document renders the real gathered values**

Read the file back and confirm every table cell matches the command output from Step 1 (no placeholder text, no TBD).

- [ ] **Step 4: Commit**

```bash
git add docs/environment-audit.md
git commit -m "docs: add Milestone 1 environment audit"
```

---

### Task 2: Reference Screenshot Capture Tool

**Files:**
- Create: `tools/reference-audit/package.json`
- Create: `tools/reference-audit/pages.mjs`
- Create: `tools/reference-audit/capture.mjs`
- Create: `tools/reference-audit/capture-smoke-test.mjs`

**Interfaces:**
- Consumes: nothing from Task 1.
- Produces: `pages` array (exported from `pages.mjs`, shape `{ label: string, url: string }[]`) and a `capture.mjs` CLI that Task 3 runs directly — both are consumed by Task 3.

- [ ] **Step 1: Scaffold the tool package**

Create `tools/reference-audit/package.json`:

```json
{
  "name": "reference-audit",
  "private": true,
  "type": "module",
  "scripts": {
    "smoke-test": "node capture-smoke-test.mjs",
    "capture": "node capture.mjs"
  },
  "devDependencies": {
    "playwright": "1.62.1"
  }
}
```

Run:

```bash
cd tools/reference-audit
npm install
npx playwright install chromium
```

Expected: `node_modules/` created (git-ignored by the root `.gitignore`'s `node_modules/` rule), Chromium browser downloaded for Playwright.

- [ ] **Step 2: Write the reference page list**

Create `tools/reference-audit/pages.mjs`:

```javascript
export const pages = [
  { label: 'Home', url: 'https://looptrails.com/' },
  { label: 'Tours Archive', url: 'https://looptrails.com/tours/' },
  { label: 'Tour Detail — 2 Days 1 Night', url: 'https://looptrails.com/tours/ha-giang-loop-2-days-1-night/' },
  { label: 'Tour Detail — 4 Days 3 Nights', url: 'https://looptrails.com/tours/ha-giang-loop-4-days-3-nights/' },
  { label: 'Tour Detail — Cao Bang 6 Days 5 Nights', url: 'https://looptrails.com/tours/ha-giang-cao-bang-6-days/' },
  { label: 'Motorbike Rental', url: 'https://looptrails.com/ha-giang-motorbike-rental/' },
  { label: 'Blog Archive', url: 'https://looptrails.com/blog/' },
  { label: 'Blog Single Article', url: 'https://looptrails.com/guide/getting-sick-on-the-loop/' },
  { label: 'Contact', url: 'https://looptrails.com/contact/' },
  { label: 'About', url: 'https://looptrails.com/about-loop-trails-tours-ha-giang/' },
  { label: 'Terms, Conditions & Privacy Policy', url: 'https://looptrails.com/terms-and-conditions/' },
  { label: '404 Not Found', url: 'https://looptrails.com/this-page-does-not-exist-xyz123/' },
];

export const VIEWPORTS = [
  { name: 'desktop', width: 1440, height: 1000 },
  { name: 'laptop', width: 1280, height: 800 },
  { name: 'tablet', width: 768, height: 1024 },
  { name: 'mobile', width: 390, height: 844 },
  { name: 'narrow-mobile', width: 360, height: 800 },
];
```

This list was compiled from the live reference site's header menu, Tours dropdown, and footer during this planning session (2026-08-19). It intentionally omits blog category/tag archive and on-site search: the reference site's `/blog/` page was inspected directly and has no visible category/tag links or search box — there is nothing observable to screenshot for those two page types. Note this gap in Task 3's audit summary rather than inventing a URL.

- [ ] **Step 3: Write the capture script**

Create `tools/reference-audit/capture.mjs`:

```javascript
import { chromium } from 'playwright';
import { mkdir, writeFile } from 'node:fs/promises';
import path from 'node:path';
import { pages, VIEWPORTS } from './pages.mjs';

const OUT_DIR = path.resolve('../../docs/reference-screenshots');
const MANIFEST_PATH = path.resolve('../../docs/reference-screenshot-manifest.json');

function slugify(url) {
  const p = new URL(url).pathname.replace(/^\/|\/$/g, '').replace(/\//g, '-');
  return p || 'home';
}

export async function captureAll(pageList) {
  const browser = await chromium.launch();
  const manifest = [];
  for (const page of pageList) {
    const slug = slugify(page.url);
    const dir = path.join(OUT_DIR, slug);
    await mkdir(dir, { recursive: true });
    for (const vp of VIEWPORTS) {
      const context = await browser.newContext({
        viewport: { width: vp.width, height: vp.height },
        reducedMotion: 'reduce',
      });
      const tab = await context.newPage();
      let status = null;
      try {
        const response = await tab.goto(page.url, { waitUntil: 'networkidle', timeout: 30000 });
        status = response ? response.status() : null;
      } catch (err) {
        status = `error: ${err.message}`;
      }
      const filePath = path.join(dir, `${vp.name}.png`);
      await tab.screenshot({ path: filePath, fullPage: true });
      manifest.push({
        label: page.label,
        url: page.url,
        viewport: vp.name,
        width: vp.width,
        height: vp.height,
        status,
        file: path.relative(path.resolve('../../'), filePath).replace(/\\/g, '/'),
        capturedAt: new Date().toISOString(),
      });
      await context.close();
      console.log(`Captured ${slug} @ ${vp.name} (status ${status})`);
    }
  }
  await browser.close();
  return manifest;
}

async function main() {
  const manifest = await captureAll(pages);
  await writeFile(MANIFEST_PATH, JSON.stringify(manifest, null, 2));
  console.log(`Done. ${manifest.length} screenshots captured. Manifest: ${MANIFEST_PATH}`);
}

if (import.meta.url === `file://${process.argv[1]}`) {
  main();
}
```

- [ ] **Step 4: Write a one-page smoke test**

Create `tools/reference-audit/capture-smoke-test.mjs`:

```javascript
import { stat } from 'node:fs/promises';
import { captureAll } from './capture.mjs';

const smokePage = { label: 'Home (smoke test)', url: 'https://looptrails.com/' };
const manifest = await captureAll([smokePage]);

if (manifest.length !== 5) {
  throw new Error(`Expected 5 viewport captures, got ${manifest.length}`);
}

for (const entry of manifest) {
  const filePath = entry.file.replace('docs/', '../../docs/');
  const s = await stat(filePath);
  if (s.size === 0) {
    throw new Error(`${entry.file} is empty (0 bytes)`);
  }
  if (entry.status !== 200) {
    throw new Error(`${entry.file} got HTTP status ${entry.status}, expected 200`);
  }
}

console.log('Smoke test passed: 5 non-empty screenshots at HTTP 200.');
```

- [ ] **Step 5: Run the smoke test and verify it passes**

```bash
cd tools/reference-audit
npm run smoke-test
```

Expected: `Smoke test passed: 5 non-empty screenshots at HTTP 200.` printed, and `docs/reference-screenshots/home/{desktop,laptop,tablet,mobile,narrow-mobile}.png` exist and are non-empty. If the reference site is unreachable (network/DNS error), stop here and report — do not fabricate a passing result (spec §16: "If access to the reference is blocked, stop visual implementation and report the specific missing evidence").

- [ ] **Step 6: Commit**

```bash
git add tools/reference-audit/package.json tools/reference-audit/pages.mjs tools/reference-audit/capture.mjs tools/reference-audit/capture-smoke-test.mjs tools/reference-audit/package-lock.json
git commit -m "feat: add Playwright reference-screenshot capture tool"
```

Note: `docs/reference-screenshots/` stays untracked (git-ignored) — the smoke-test images are local evidence only, not committed.

---

### Task 3: Full Reference Capture Run

**Files:**
- Create: `docs/reference-screenshot-manifest.json` (generated, committed — JSON metadata only, no images)
- Modify: `docs/environment-audit.md` (append captured-inventory summary)

**Interfaces:**
- Consumes: `captureAll(pageList)` and `pages`/`VIEWPORTS` from Task 2's `tools/reference-audit/capture.mjs` and `pages.mjs`.
- Produces: `docs/reference-screenshot-manifest.json`, the manifest Milestone 2's reference audit will read to locate every screenshot file.

- [ ] **Step 1: Run the full capture**

```bash
cd tools/reference-audit
npm run capture
```

Expected: 12 pages × 5 viewports = 60 lines of `Captured <slug> @ <viewport> (status <code>)`, ending with `Done. 60 screenshots captured. Manifest: ...reference-screenshot-manifest.json`. All entries should show status `200` except the `404 Not Found` page, which should show status `404` (that is the correct/expected result, not a failure).

- [ ] **Step 2: Verify the manifest**

```bash
node -e "const m=require('../../docs/reference-screenshot-manifest.json'); console.log(m.length); console.log(m.filter(e=>typeof e.status!=='number').length)"
```

Expected: `60` then `0` (every entry has a numeric HTTP status — no thrown errors/timeouts). If any entry has a non-numeric status (a caught error message), re-run capture for that single page manually and investigate before proceeding; do not silently accept a partial capture.

- [ ] **Step 3: Append the inventory summary to the audit doc**

Edit `docs/environment-audit.md`, replacing the "Screenshot capture inventory" section's single sentence with:

```markdown
## Screenshot capture inventory

Captured 2026-08-19 via `tools/reference-audit/capture.mjs`. Full manifest: `docs/reference-screenshot-manifest.json` (60 entries: 12 reference pages × 5 viewports each).

Pages captured: Home, Tours Archive, 3 representative Tour Details (2D1N, 4D3N, Cao Bang 6D5N), Motorbike Rental, Blog Archive, one Blog Single Article, Contact, About, Terms/Privacy, and a 404 page.

Not captured (not observable on the public reference site as of this date): blog category/tag archive, on-site search results — the live `/blog/` page has no visible category/tag links or search box. These page types will still be built per spec §5 items 5 and 12, using WordPress's own standard archive/search template conventions since there is no reference layout to measure.
```

- [ ] **Step 4: Commit**

```bash
git add docs/reference-screenshot-manifest.json docs/environment-audit.md
git commit -m "chore: run full reference screenshot capture, update environment audit"
git push
```

---

## Definition of done for this milestone

- `docs/environment-audit.md` exists and every value in it is a real, freshly-confirmed fact (no placeholders).
- `tools/reference-audit/` contains a working, re-runnable Playwright capture tool with a passing smoke test.
- `docs/reference-screenshot-manifest.json` lists 60 entries, all with numeric HTTP status codes.
- 60 PNG files exist under `docs/reference-screenshots/` (local only, not committed).
- All three tasks are committed and pushed to `origin/master`.
- **Report back to the user for a test/approval checkpoint before starting Milestone 2** (reference audit / design tokens / component inventory) per the user's explicit phase-by-phase workflow request.
