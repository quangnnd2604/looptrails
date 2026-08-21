# Milestone 4 — Theme Shell: Header, Navigation, Footer, Global Tokens

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the `tour-reference-theme` block theme's shell — `theme.json` encoding every measured design token, a header template part (logo, hamburger-only navigation at every viewport, sticky behavior, "Book Now" CTA), a footer template part, self-hosted fonts, and local Playwright-based visual-regression tooling that measures the shell against the live reference site per spec §4's acceptance rule.

**Architecture:** A full Site-Editing (FSE) block theme — no `header.php`/`footer.php`, no page-builder. `theme.json` is the single source of truth for color/typography/spacing/radius/shadow tokens; `parts/header.html` and `parts/footer.html` are block markup consuming those tokens; `functions.php` only registers theme supports, self-hosted fonts, and enqueues one small progressive-enhancement script for sticky-header/drawer behavior — no business logic (that stays in `tour-booking-core`, per spec §2.1). Two things the earlier reference audit (Milestone 2b) could not observe from static screenshots — the header's interactive nav structure and sticky behavior, and the footer's internal DOM structure — are resolved by Task 1's live interaction audit before any markup is written, so no task after Task 1 has to guess or invent what the reference site actually does.

**Tech Stack:** WordPress 7.0.4 block theme (theme.json schema v3), PHP 8.2, self-hosted Google Fonts via `@fontsource` npm packages, PHPUnit + `wp-phpunit` (same pattern as `tour-booking-core`'s harness, applied to a theme via `switch_theme()`), Playwright for both the live-interaction audit and local visual-regression tooling, `pixelmatch` for image diffing (not yet installed anywhere in the project — Task 5 adds it).

**Spec:** `docs/AI_AGENT_WORDPRESS_TOUR_WEBSITE_SPEC.md` (v2.0) — this plan implements §13 milestone 4 ("Theme shell: header, navigation, footer and global responsive tokens"), drawing on §2.1 (theme architecture), §3 (reference-first protocol — already executed for static measurements in M1/M2b; Task 1 extends it for interaction states), §4 (visual acceptance rule), §5.1 (header/navigation requirements), §5.5 (footer requirements), and the accessibility/font/performance items in §11 relevant to a shell. Full page templates, Home/Tour Detail content, and business-data-driven nav (Tours dropdown from real tours) are out of scope — those are milestones 5/6 and depend on `tour-booking-core` content that doesn't exist yet.

**Scope reduction, disclosed up front:** spec §5.5 lists a full footer admin-control surface — navigation columns/headings, contact details/address/map/email/phones/WhatsApp, legal links, accepted payment icons, newsletter block. None of that data exists yet: `tour-booking-core` (Milestone 3) has no site-wide settings API for contact/social/payment/legal content, and building one is out of this shell milestone's scope (spec §13 lists it as "Theme shell: header, navigation, footer and **global responsive tokens**" — structural/visual shell, not the settings backend). Task 4 below builds the footer's visual structure (logo, tagline, social links, copyright bar) using static placeholder content, matching what Task 1's audit can actually observe on the reference site today. The admin-editable settings layer (contact/legal/payment/newsletter, multi-column nav-in-footer) is deferred to whichever later milestone adds the site-wide settings mechanism this needs — likely alongside Milestone 8's "global settings for branding, contact, email recipients..." (spec §8.1) since that's the first point the spec requires such a settings screen to exist. This must be reported to the user as a disclosed deviation, not silently left out.

**Known gaps entering this plan, and how each is closed:** Milestone 2b's static-screenshot-based audit could not observe: (a) the header's nav-link structure and content (no separate nav row was visible at any viewport, only a hamburger toggle icon, at counts consistent with it appearing on every breakpoint — but this needs confirming, not assuming), (b) sticky-header scroll behavior, (c) the footer's internal structure (single centered block vs. multi-column grid), (d) the language/currency switcher's markup, (e) keyboard/focus/ARIA behavior for the nav drawer. Task 1 resolves (a)-(d) via a live-interaction Playwright audit against the real reference site (the same tool this project has used since Milestone 1). (e) has no reference equivalent to observe — spec §0 permits documenting an assumption when nothing is observable, so Task 3 implements WCAG 2.2 AA keyboard/focus/ARIA behavior as the smallest correct implementation consistent with the observed structure, not copied from the reference (which spec §11 explicitly allows to diverge from the reference on accessibility grounds).

## Global Constraints

- No Elementor/Divi/WPBakery/page-builder runtime; no paid plugin or paid SaaS required to operate the core site (spec §2.1).
- Use Gutenberg/block editor, `theme.json`, native Navigation block, and reusable block patterns — no `header.php`/`footer.php` classic templates (spec §2.1).
- Business logic lives in `tour-booking-core`, never the theme (spec §2.1).
- Components must consume theme.json tokens; do not scatter raw colors/arbitrary spacing through template parts (spec §3).
- Self-host fonts (open-licensed only) with preload only for critical faces; no page-builder payload (spec §1, §11).
- Do not download or redistribute Loop Trails' logo/photos; the theme shell itself doesn't need photos, but any placeholder logo asset must be an original mark, not a copy (spec §1).
- WCAG 2.2 AA target: full keyboard operability for menus/drawers, visible focus, semantic landmarks, reduced-motion support (spec §11).
- Visual acceptance rule (spec §4): container edges within 8px desktop / 6px mobile of reference; major section positions within 24px; font size within 1px, line height within 2px; color channel difference ≤5; radius/gap/padding within 2px; no horizontal overflow at 360px; overall pixel diff <8% after masking substituted assets. If missed, iterate — do not declare completion; list remaining deltas.
- When a reference value is not observable, choose the smallest implementation consistent with adjacent reference patterns and document the assumption (spec §0) — never invent unobserved values as if measured.
- No domain, email, API key, gateway secret, phone number, exchange rate or business identity may be embedded in source code (spec §2.2).

---

## Task 1: Live reference interaction audit

**Files:**
- Create: `tools/reference-audit/interaction-audit.mjs`
- Create: `docs/component-inventory-addendum-header-nav-footer.md`
- Create: `docs/reference-screenshots/interaction-audit/` (screenshots, written by the script)

**Interfaces:**
- Consumes: `tools/reference-audit/pages.mjs`'s existing `VIEWPORTS` export (5 viewports: desktop 1440×1000, laptop 1280×800, tablet 768×1024, mobile 390×844, narrow-mobile 360×800) and its `pages` list (reuse the Home page URL from there — do not hardcode a second copy of the reference domain).
- Produces: `docs/component-inventory-addendum-header-nav-footer.md`, containing four required sections (Nav structure, Sticky behavior, Footer structure, Language/currency control) each with a definite finding (not "unknown" — if genuinely absent from the live site, state that explicitly as a finding, not a gap). Tasks 3 and 4 read this file as their primary source of truth for header/nav/footer structure, overriding the "best-evidence" descriptions in this plan's own header text if they conflict.

- [ ] **Step 1: Write `tools/reference-audit/interaction-audit.mjs`**

```javascript
import { chromium } from 'playwright';
import { writeFileSync, mkdirSync } from 'node:fs';
import { pages, VIEWPORTS } from './pages.mjs';

const OUT_DIR = 'docs/reference-screenshots/interaction-audit';
mkdirSync(OUT_DIR, { recursive: true });

const homePage = pages.find((p) => p.slug === 'home') ?? pages[0];
const findings = {
	nav: [],
	sticky: [],
	footer: null,
	langSwitcher: null,
};

async function auditViewport(browser, viewport) {
	const context = await browser.newContext({ viewport: { width: viewport.width, height: viewport.height } });
	const page = await context.newPage();
	await page.addStyleTag({ content: '*, *::before, *::after { animation: none !important; transition: none !important; }' });
	await page.goto(homePage.url, { waitUntil: 'networkidle' });

	// --- Nav / hamburger structure ---
	const toggle = page.locator('.elementor-menu-toggle').first();
	const toggleVisible = await toggle.isVisible().catch(() => false);
	await page.screenshot({ path: `${OUT_DIR}/${viewport.name}-header-before-click.png`, clip: { x: 0, y: 0, width: viewport.width, height: 200 } });

	let menuTextAfterOpen = null;
	if (toggleVisible) {
		await toggle.click();
		await page.waitForTimeout(400);
		await page.screenshot({ path: `${OUT_DIR}/${viewport.name}-header-after-click.png` });
		const menuLinks = page.locator('nav a, [class*="menu"] a, [class*="nav"] a');
		menuTextAfterOpen = await menuLinks.allTextContents();
		await toggle.click();
		await page.waitForTimeout(200);
	}

	findings.nav.push({
		viewport: viewport.name,
		toggleVisible,
		menuTextAfterOpen: menuTextAfterOpen ? [...new Set(menuTextAfterOpen.map((t) => t.trim()).filter(Boolean))] : null,
	});

	// --- Sticky behavior ---
	const header = page.locator('header').first();
	const beforeScroll = await header.evaluate((el) => {
		const style = getComputedStyle(el);
		return { position: style.position, top: style.top, backgroundColor: style.backgroundColor };
	});
	await page.evaluate(() => window.scrollTo(0, 900));
	await page.waitForTimeout(400);
	const afterScroll = await header.evaluate((el) => {
		const style = getComputedStyle(el);
		return { position: style.position, top: style.top, backgroundColor: style.backgroundColor };
	});
	await page.screenshot({ path: `${OUT_DIR}/${viewport.name}-header-after-scroll.png`, clip: { x: 0, y: 0, width: viewport.width, height: 200 } });
	findings.sticky.push({ viewport: viewport.name, beforeScroll, afterScroll });

	// --- Footer structure (desktop only, once) ---
	if (viewport.name === 'desktop' && !findings.footer) {
		await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
		await page.waitForTimeout(400);
		const footerData = await page.evaluate(() => {
			const footer = document.querySelector('footer');
			if (!footer) return null;
			const style = getComputedStyle(footer);
			const directChildren = Array.from(footer.children).map((el) => ({
				tag: el.tagName.toLowerCase(),
				className: el.className,
				childCount: el.children.length,
			}));
			return {
				display: style.display,
				gridTemplateColumns: style.gridTemplateColumns,
				flexDirection: style.flexDirection,
				directChildren,
			};
		});
		findings.footer = footerData;
		await page.screenshot({ path: `${OUT_DIR}/desktop-footer-full.png`, fullPage: false });
	}

	// --- Language/currency switcher ---
	if (!findings.langSwitcher) {
		const candidate = await page.evaluate(() => {
			const all = Array.from(document.querySelectorAll('header *'));
			const match = all.find((el) => /\b(EN|VI|USD|VND)\b/.test(el.textContent ?? '') && el.children.length <= 2);
			return match ? { tag: match.tagName.toLowerCase(), className: match.className, text: match.textContent.trim().slice(0, 80) } : null;
		});
		findings.langSwitcher = candidate;
	}

	await context.close();
}

const browser = await chromium.launch();
for (const viewport of VIEWPORTS) {
	await auditViewport(browser, viewport);
}
await browser.close();

writeFileSync(`${OUT_DIR}/findings.json`, JSON.stringify(findings, null, 2));
console.log('Interaction audit complete. Findings written to', `${OUT_DIR}/findings.json`);
```

- [ ] **Step 2: Run the audit**

Run (from repo root):
```bash
node tools/reference-audit/interaction-audit.mjs
```
Expected: exits 0, `docs/reference-screenshots/interaction-audit/findings.json` exists, and contains 5 entries in `nav` (one per viewport), 5 entries in `sticky`, a non-null `footer` object, and a `langSwitcher` value (object or explicit `null` if genuinely absent — both are valid findings).

- [ ] **Step 3: Write `docs/component-inventory-addendum-header-nav-footer.md`**

Read `findings.json` and write a markdown doc with exactly these four `##` sections, each stating a definite conclusion drawn from the JSON (not a restatement of the raw JSON):

```markdown
# Header/Nav/Footer Interaction Audit Addendum

Captured [DATE] via `tools/reference-audit/interaction-audit.mjs` against the live reference site. Supplements `docs/component-inventory.md`'s "Primary Navigation (Header)" and "Footer Block" entries, which were built from static screenshots/computed-style JSON and explicitly flagged these as gaps.

## Nav structure

[State, per viewport, whether the hamburger toggle is present and what menu items it reveals when opened — cite the exact menuTextAfterOpen array. State the conclusion: is this a hamburger-at-every-viewport pattern (as component-inventory.md's evidence-based inference suggested) or does desktop show a different structure?]

## Sticky behavior

[State, per viewport, whether position/backgroundColor changed between beforeScroll and afterScroll. State the conclusion: is the header sticky (position: fixed/sticky) with a color/background change on scroll, or static?]

## Footer structure

[State the footer's computed display/gridTemplateColumns/flexDirection and the direct-children breakdown. State the conclusion: single centered block, or a multi-column grid — and if a grid, how many columns.]

## Language/currency control

[State whether a candidate element was found, its tag/class/text if so. State the conclusion: is there a visible language/currency switcher in the header, and if so what does its markup look like.]
```

- [ ] **Step 4: Commit**

```bash
git add tools/reference-audit/interaction-audit.mjs docs/component-inventory-addendum-header-nav-footer.md docs/reference-screenshots/interaction-audit
git commit -m "docs: add live header/nav/footer interaction audit for Milestone 4"
```

---

## Task 2: `theme.json`, self-hosted fonts, and theme PHPUnit harness

**Files:**
- Create: `wp-content/themes/tour-reference-theme/style.css`
- Create: `wp-content/themes/tour-reference-theme/theme.json`
- Create: `wp-content/themes/tour-reference-theme/functions.php`
- Create: `wp-content/themes/tour-reference-theme/composer.json`
- Create: `wp-content/themes/tour-reference-theme/phpunit.xml.dist`
- Create: `wp-content/themes/tour-reference-theme/tests/wp-tests-config.php`
- Create: `wp-content/themes/tour-reference-theme/tests/bootstrap.php`
- Create: `wp-content/themes/tour-reference-theme/tests/test-theme-setup.php`
- Create: `wp-content/themes/tour-reference-theme/assets/fonts/` (font files, via `@fontsource` packages)
- Modify: `.gitignore` — ignore the theme's `vendor/` and a temporary `node_modules/` used only to fetch font files

**Interfaces:**
- Consumes: nothing from Task 1 directly (typography/color/spacing tokens come from `docs/design-tokens.md`, already measured in Milestone 2b).
- Produces: `theme.json` with every token below registered; `TOUR_THEME_DIR` PHP constant; the test harness pattern (`vendor/bin/phpunit` runnable from this directory) that Tasks 3-4 add tests to.

- [ ] **Step 1: Write `style.css`** (WordPress requires this exact header block to recognize a theme; no other CSS goes in this file — block themes use `theme.json` + block styles, not a monolithic stylesheet)

```css
/*
Theme Name: Tour Reference Theme
Theme URI: https://github.com/quangnnd2604/looptrails
Author: Loop Trails Reference Build
Description: Original block theme for a tour-booking site, built to reproduce the measured layout/behavior of a public reference site with entirely original code, branding and content.
Version: 0.1.0
Requires at least: 6.6
Requires PHP: 8.2
Text Domain: tour-reference-theme
*/
```

- [ ] **Step 2: Write `theme.json`**

```json
{
	"$schema": "https://schemas.wp.org/trunk/theme.json",
	"version": 3,
	"settings": {
		"appearanceTools": true,
		"layout": {
			"contentSize": "1200px",
			"wideSize": "1320px"
		},
		"color": {
			"palette": [
				{ "slug": "primary", "color": "#ff6602", "name": "Primary" },
				{ "slug": "ink", "color": "#36343b", "name": "Ink" },
				{ "slug": "text-body", "color": "#212121", "name": "Body text" },
				{ "slug": "text-heading", "color": "#333333", "name": "Heading text" },
				{ "slug": "surface", "color": "#ffffff", "name": "Surface" },
				{ "slug": "accent-secondary", "color": "#e5396e", "name": "Accent secondary" },
				{ "slug": "surface-header-footer", "color": "#e4e0da", "name": "Header/footer surface" },
				{ "slug": "surface-muted", "color": "#f7f7f7", "name": "Muted surface" },
				{ "slug": "border-default", "color": "#dddddd", "name": "Default border" },
				{ "slug": "social-facebook", "color": "#1877f2", "name": "Facebook" },
				{ "slug": "social-instagram", "color": "#e1306c", "name": "Instagram" },
				{ "slug": "social-whatsapp", "color": "#25d366", "name": "WhatsApp" },
				{ "slug": "social-tiktok", "color": "#000000", "name": "TikTok" },
				{ "slug": "social-tripadvisor", "color": "#34e0a1", "name": "Tripadvisor" }
			],
			"custom": true,
			"customDuotone": false
		},
		"typography": {
			"fluid": true,
			"fontFamilies": [
				{ "slug": "heading", "fontFamily": "Montserrat, sans-serif", "name": "Montserrat" },
				{ "slug": "card-title", "fontFamily": "Poppins, sans-serif", "name": "Poppins" },
				{ "slug": "body", "fontFamily": "Inter, sans-serif", "name": "Inter" },
				{ "slug": "form-label", "fontFamily": "\"DM Sans\", sans-serif", "name": "DM Sans" },
				{ "slug": "footer-legal", "fontFamily": "\"Open Sans\", sans-serif", "name": "Open Sans" }
			],
			"fontSizes": [
				{ "slug": "caption", "size": "11.5px", "name": "Caption" },
				{ "slug": "body", "size": "14.5px", "name": "Body" },
				{ "slug": "footer-legal", "size": "12px", "name": "Footer legal" },
				{ "slug": "h3", "size": "18px", "name": "H3" },
				{ "slug": "h2", "size": "22px", "fluid": { "min": "18px", "max": "22px" }, "name": "H2" },
				{ "slug": "h1", "size": "60px", "fluid": { "min": "28px", "max": "60px" }, "name": "H1" }
			],
			"customFontSize": false
		},
		"spacing": {
			"units": ["px", "%", "vw", "rem"],
			"customSpacingSize": true
		},
		"shadow": {
			"presets": [
				{ "slug": "button-hard-offset", "shadow": "2px 3px 0px 0px #36343b", "name": "Button hard offset" }
			]
		},
		"custom": {
			"button": {
				"radius-standard": "7px",
				"radius-pill": "25px",
				"height-nav": "35px"
			}
		}
	},
	"styles": {
		"color": {
			"background": "var(--wp--preset--color--surface)",
			"text": "var(--wp--preset--color--text-body)"
		},
		"typography": {
			"fontFamily": "var(--wp--preset--font-family--body)",
			"fontSize": "var(--wp--preset--font-size--body)"
		},
		"elements": {
			"h1": {
				"typography": {
					"fontFamily": "var(--wp--preset--font-family--heading)",
					"fontSize": "var(--wp--preset--font-size--h1)",
					"fontWeight": "800",
					"letterSpacing": "-1px",
					"lineHeight": "1.08"
				},
				"color": { "text": "var(--wp--preset--color--text-heading)" }
			},
			"h2": {
				"typography": {
					"fontFamily": "var(--wp--preset--font-family--heading)",
					"fontSize": "var(--wp--preset--font-size--h2)",
					"fontWeight": "700",
					"letterSpacing": "-0.3px",
					"lineHeight": "1.25"
				},
				"color": { "text": "var(--wp--preset--color--text-heading)" }
			},
			"link": {
				"color": { "text": "var(--wp--preset--color--primary)" }
			}
		}
	}
}
```

- [ ] **Step 3: Self-host fonts via `@fontsource`**

Run (from repo root, using a throwaway `node_modules` just to extract font files — not committed):
```bash
mkdir -p wp-content/themes/tour-reference-theme/assets/fonts
npm --prefix /tmp/fontsource-fetch init -y
npm --prefix /tmp/fontsource-fetch install @fontsource/montserrat @fontsource/poppins @fontsource/inter @fontsource/dm-sans @fontsource/open-sans
cp /tmp/fontsource-fetch/node_modules/@fontsource/montserrat/files/montserrat-latin-700-normal.woff2 wp-content/themes/tour-reference-theme/assets/fonts/
cp /tmp/fontsource-fetch/node_modules/@fontsource/montserrat/files/montserrat-latin-800-normal.woff2 wp-content/themes/tour-reference-theme/assets/fonts/
cp /tmp/fontsource-fetch/node_modules/@fontsource/poppins/files/poppins-latin-600-normal.woff2 wp-content/themes/tour-reference-theme/assets/fonts/
cp /tmp/fontsource-fetch/node_modules/@fontsource/inter/files/inter-latin-400-normal.woff2 wp-content/themes/tour-reference-theme/assets/fonts/
cp /tmp/fontsource-fetch/node_modules/@fontsource/inter/files/inter-latin-600-normal.woff2 wp-content/themes/tour-reference-theme/assets/fonts/
cp /tmp/fontsource-fetch/node_modules/@fontsource/inter/files/inter-latin-700-normal.woff2 wp-content/themes/tour-reference-theme/assets/fonts/
cp /tmp/fontsource-fetch/node_modules/@fontsource/dm-sans/files/dm-sans-latin-600-normal.woff2 wp-content/themes/tour-reference-theme/assets/fonts/
cp /tmp/fontsource-fetch/node_modules/@fontsource/open-sans/files/open-sans-latin-400-normal.woff2 wp-content/themes/tour-reference-theme/assets/fonts/
cp /tmp/fontsource-fetch/node_modules/@fontsource/montserrat/LICENSE wp-content/themes/tour-reference-theme/assets/fonts/LICENSE-fonts.txt
rm -rf /tmp/fontsource-fetch
```
Expected: 8 `.woff2` files plus `LICENSE-fonts.txt` (SIL Open Font License — required attribution per spec §1) exist in `assets/fonts/`. If any `@fontsource` package's exact file name differs from above, list the actual directory contents (`ls /tmp/fontsource-fetch/node_modules/@fontsource/montserrat/files/`) and copy the matching latin/weight/normal file — do not skip a weight silently.

- [ ] **Step 4: Write `functions.php`**

```php
<?php
/**
 * Theme bootstrap: supports, self-hosted fonts. No business logic — see tour-booking-core.
 */

defined( 'ABSPATH' ) || exit;

define( 'TOUR_THEME_DIR', get_template_directory() );
define( 'TOUR_THEME_URI', get_template_directory_uri() );

add_action( 'after_setup_theme', 'tour_theme_supports' );
function tour_theme_supports() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	register_nav_menus(
		array(
			'primary' => __( 'Primary Navigation', 'tour-reference-theme' ),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'tour_theme_enqueue_fonts' );
function tour_theme_enqueue_fonts() {
	$fonts = array(
		'montserrat-700' => 'montserrat-latin-700-normal.woff2',
		'montserrat-800' => 'montserrat-latin-800-normal.woff2',
		'poppins-600'    => 'poppins-latin-600-normal.woff2',
		'inter-400'      => 'inter-latin-400-normal.woff2',
		'inter-600'      => 'inter-latin-600-normal.woff2',
		'inter-700'      => 'inter-latin-700-normal.woff2',
		'dm-sans-600'    => 'dm-sans-latin-600-normal.woff2',
		'open-sans-400'  => 'open-sans-latin-400-normal.woff2',
	);

	foreach ( array( 'montserrat-800', 'inter-400' ) as $critical ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( TOUR_THEME_URI . '/assets/fonts/' . $fonts[ $critical ] )
		);
	}

	wp_enqueue_style( 'tour-theme-fonts', TOUR_THEME_URI . '/assets/css/fonts.css', array(), '0.1.0' );
}
```

- [ ] **Step 5: Write `assets/css/fonts.css`** (the `@font-face` declarations `functions.php` enqueues)

```css
@font-face {
	font-family: "Montserrat";
	font-style: normal;
	font-weight: 700;
	font-display: swap;
	src: url("../fonts/montserrat-latin-700-normal.woff2") format("woff2");
}
@font-face {
	font-family: "Montserrat";
	font-style: normal;
	font-weight: 800;
	font-display: swap;
	src: url("../fonts/montserrat-latin-800-normal.woff2") format("woff2");
}
@font-face {
	font-family: "Poppins";
	font-style: normal;
	font-weight: 600;
	font-display: swap;
	src: url("../fonts/poppins-latin-600-normal.woff2") format("woff2");
}
@font-face {
	font-family: "Inter";
	font-style: normal;
	font-weight: 400;
	font-display: swap;
	src: url("../fonts/inter-latin-400-normal.woff2") format("woff2");
}
@font-face {
	font-family: "Inter";
	font-style: normal;
	font-weight: 600;
	font-display: swap;
	src: url("../fonts/inter-latin-600-normal.woff2") format("woff2");
}
@font-face {
	font-family: "Inter";
	font-style: normal;
	font-weight: 700;
	font-display: swap;
	src: url("../fonts/inter-latin-700-normal.woff2") format("woff2");
}
@font-face {
	font-family: "DM Sans";
	font-style: normal;
	font-weight: 600;
	font-display: swap;
	src: url("../fonts/dm-sans-latin-600-normal.woff2") format("woff2");
}
@font-face {
	font-family: "Open Sans";
	font-style: normal;
	font-weight: 400;
	font-display: swap;
	src: url("../fonts/open-sans-latin-400-normal.woff2") format("woff2");
}
```

- [ ] **Step 6: Set up the PHPUnit test harness** (same pattern as `tour-booking-core`'s Task 1, applied to a theme)

`composer.json`:
```json
{
	"name": "looptrails/tour-reference-theme",
	"description": "PHPUnit harness for the tour-reference-theme block theme.",
	"type": "wordpress-theme",
	"license": "GPL-2.0-or-later",
	"require-dev": {
		"phpunit/phpunit": "^9.6",
		"yoast/phpunit-polyfills": "^2.0 || ^3.0",
		"wp-phpunit/wp-phpunit": "*"
	},
	"minimum-stability": "stable"
}
```

`tests/wp-tests-config.php` (identical DB target to `tour-booking-core`'s test config — same `looptrails_test` database, safe to share since tests run in isolated transactions):
```php
<?php
define( 'ABSPATH', 'C:/xampp/htdocs/looptrails/' );

define( 'DB_NAME', 'looptrails_test' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

$table_prefix = 'wptests_';

define( 'WP_TESTS_DOMAIN', 'looptrails.test' );
define( 'WP_TESTS_EMAIL', 'admin@looptrails.test' );
define( 'WP_TESTS_TITLE', 'Loop Trails Test' );

define( 'WP_PHP_BINARY', 'php' );
define( 'WPLANG', '' );
define( 'WP_DEBUG', true );
```

`tests/bootstrap.php` (switches the active theme instead of loading a plugin file):
```php
<?php
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

putenv( 'WP_TESTS_CONFIG_FILE_PATH=' . __DIR__ . '/wp-tests-config.php' );
putenv( 'WP_PHPUNIT__DIR=' . dirname( __DIR__ ) . '/vendor/wp-phpunit/wp-phpunit' );

$_tests_dir = getenv( 'WP_PHPUNIT__DIR' );

require $_tests_dir . '/includes/functions.php';

function tour_theme_switch_to_theme_under_test() {
	switch_theme( 'tour-reference-theme' );
}
tests_add_filter( 'muplugins_loaded', 'tour_theme_switch_to_theme_under_test' );

require $_tests_dir . '/includes/bootstrap.php';
```

`phpunit.xml.dist`:
```xml
<?xml version="1.0"?>
<phpunit
	bootstrap="tests/bootstrap.php"
	colors="true"
>
	<testsuites>
		<testsuite name="tour-reference-theme">
			<directory suffix=".php">./tests/</directory>
			<exclude>./tests/bootstrap.php</exclude>
			<exclude>./tests/wp-tests-config.php</exclude>
		</testsuite>
	</testsuites>
</phpunit>
```

`tests/test-theme-setup.php`:
```php
<?php
class Test_Theme_Setup extends WP_UnitTestCase {

	public function test_active_theme_is_tour_reference_theme() {
		$this->assertSame( 'tour-reference-theme', get_stylesheet() );
	}

	public function test_theme_json_declares_expected_color_palette() {
		$theme_json = wp_get_global_settings( array( 'color', 'palette', 'theme' ) );
		$slugs      = wp_list_pluck( $theme_json, 'slug' );

		foreach ( array( 'primary', 'ink', 'surface-header-footer', 'social-whatsapp' ) as $expected ) {
			$this->assertContains( $expected, $slugs, "Missing color token: {$expected}" );
		}
	}

	public function test_theme_json_declares_fluid_h1() {
		$sizes = wp_get_global_settings( array( 'typography', 'fontSizes', 'theme' ) );
		$h1     = current( array_filter( $sizes, fn( $s ) => 'h1' === $s['slug'] ) );

		$this->assertNotFalse( $h1 );
		$this->assertSame( '28px', $h1['fluid']['min'] );
		$this->assertSame( '60px', $h1['fluid']['max'] );
	}

	public function test_primary_nav_menu_location_is_registered() {
		$this->assertArrayHasKey( 'primary', get_registered_nav_menus() );
	}

	public function test_font_files_exist_on_disk() {
		$fonts_dir = TOUR_THEME_DIR . '/assets/fonts/';
		foreach ( array( 'montserrat-latin-800-normal.woff2', 'inter-latin-400-normal.woff2' ) as $file ) {
			$this->assertFileExists( $fonts_dir . $file, "Missing font file: {$file}" );
		}
	}
}
```

- [ ] **Step 7: Install dependencies and run the suite**

Run (from `wp-content/themes/tour-reference-theme/`):
```bash
composer install
vendor/bin/phpunit
```
Expected: `OK (5 tests, N assertions)`.

- [ ] **Step 8: Update `.gitignore`**

Add:
```
# Theme PHPUnit/Composer dev dependencies (reinstall via `composer install`)
/wp-content/themes/tour-reference-theme/vendor/
```

- [ ] **Step 9: Commit**

```bash
git add wp-content/themes/tour-reference-theme .gitignore
git commit -m "feat: add tour-reference-theme skeleton with theme.json tokens and self-hosted fonts"
```

---

## Task 3: Header template part + nav/sticky behavior

**Files:**
- Create: `wp-content/themes/tour-reference-theme/parts/header.html`
- Create: `wp-content/themes/tour-reference-theme/assets/js/header-interactions.js`
- Modify: `wp-content/themes/tour-reference-theme/functions.php` — enqueue the script, register the "Book Now" button style variation
- Create: `wp-content/themes/tour-reference-theme/tests/test-header-part.php`

**Interfaces:**
- Consumes: Task 1's `docs/component-inventory-addendum-header-nav-footer.md` findings (nav structure, sticky behavior) — if those findings contradict the hamburger-at-every-viewport / sticky-with-background-change assumptions this task's code below is written against, the implementer must adjust the markup/JS to match the actual findings, not this plan's prose, and note the deviation in their report. Task 2's `theme.json` tokens (`--wp--preset--color--surface-header-footer`, `--wp--custom--button--height-nav`, `--wp--custom--button--radius-pill`, `--wp--preset--shadow--button-hard-offset`).
- Produces: `parts/header.html`, referenced by Task 5's `templates/index.html`.

- [ ] **Step 1: Write `parts/header.html`**

Uses WP core's Navigation block with `overlayMenu:"always"` (available since WP 6.3) to force the hamburger-only pattern the audit is expected to confirm — a custom button style variation (`is-style-book-now`) applies the hard-offset-shadow pill styling from the token data:

```html
<!-- wp:template-part {"slug":"header","tagName":"header","area":"header","className":"site-header"} -->
<!-- wp:group {"layout":{"type":"flex","justifyContent":"space-between"},"style":{"spacing":{"padding":{"top":"12px","bottom":"12px","left":"20px","right":"20px"}},"color":{"background":"var:preset|color|surface-header-footer"}}} -->
<div class="wp-block-group" style="background-color:var(--wp--preset--color--surface-header-footer);padding-top:12px;padding-right:20px;padding-bottom:12px;padding-left:20px">
	<!-- wp:site-logo {"width":140} /-->

	<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
	<div class="wp-block-group">
		<!-- wp:buttons -->
		<div class="wp-block-buttons">
			<!-- wp:button {"className":"is-style-book-now"} -->
			<div class="wp-block-button is-style-book-now"><a class="wp-block-button__link wp-element-button" href="#booking">Book Now</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->

		<!-- wp:navigation {"overlayMenu":"always","icon":"menu","layout":{"type":"flex","justifyContent":"right"}} /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
<!-- /wp:template-part -->
```

- [ ] **Step 2: Register the `is-style-book-now` button style in `functions.php`** (append)

```php
add_action( 'init', 'tour_theme_register_block_styles' );
function tour_theme_register_block_styles() {
	register_block_style(
		'core/button',
		array(
			'name'  => 'book-now',
			'label' => __( 'Book Now (pill, hard shadow)', 'tour-reference-theme' ),
		)
	);
}
```

- [ ] **Step 3: Write the CSS for that style variation** — append to `assets/css/fonts.css`'s file (rename it in this step to `assets/css/theme.css` since it now holds more than fonts; update the `wp_enqueue_style` filename in `functions.php` accordingly)

```css
.wp-block-button.is-style-book-now .wp-block-button__link {
	background-color: var(--wp--preset--color--surface);
	color: var(--wp--preset--color--primary);
	border-radius: var(--wp--custom--button--radius-pill);
	height: var(--wp--custom--button--height-nav);
	display: inline-flex;
	align-items: center;
	padding: 0 18px;
	box-shadow: var(--wp--preset--shadow--button-hard-offset);
	font-family: var(--wp--preset--font-family--heading);
	font-weight: 700;
}

.site-header {
	position: sticky;
	top: 0;
	z-index: 100;
}

@media (prefers-reduced-motion: no-preference) {
	.site-header {
		transition: background-color 0.2s ease;
	}
}
```

**Important:** the `position: sticky` rule above is the plan's best-evidence default. Before implementing this step, the implementer must re-check Task 1's addendum's "Sticky behavior" section — if it found the header is NOT sticky (static position, no scroll-triggered change), remove this rule instead and note the deviation in the task report.

- [ ] **Step 4: Write `assets/js/header-interactions.js`** (progressive enhancement — the Navigation block's overlay menu already works with JS disabled via native `<dialog>`/CSS; this script only adds the sticky-scroll background-swap behavior, per spec §11's "progressively enhance server-rendered HTML")

```javascript
( function () {
	var header = document.querySelector( '.site-header' );
	if ( ! header ) {
		return;
	}

	var SCROLL_THRESHOLD = 40;

	function onScroll() {
		if ( window.scrollY > SCROLL_THRESHOLD ) {
			header.classList.add( 'is-scrolled' );
		} else {
			header.classList.remove( 'is-scrolled' );
		}
	}

	window.addEventListener( 'scroll', onScroll, { passive: true } );
	onScroll();
} )();
```

Add the matching CSS to `assets/css/theme.css`:
```css
.site-header.is-scrolled {
	background-color: var(--wp--preset--color--surface-header-footer);
}
```

- [ ] **Step 5: Enqueue the script in `functions.php`** (append to `tour_theme_enqueue_fonts`, or add a new hooked function)

```php
add_action( 'wp_enqueue_scripts', 'tour_theme_enqueue_scripts' );
function tour_theme_enqueue_scripts() {
	wp_enqueue_script(
		'tour-theme-header-interactions',
		TOUR_THEME_URI . '/assets/js/header-interactions.js',
		array(),
		'0.1.0',
		array( 'strategy' => 'defer' )
	);
}
```

- [ ] **Step 6: Write `tests/test-header-part.php`**

```php
<?php
class Test_Header_Part extends WP_UnitTestCase {

	private function header_markup() {
		return file_get_contents( TOUR_THEME_DIR . '/parts/header.html' );
	}

	public function test_header_part_file_exists() {
		$this->assertFileExists( TOUR_THEME_DIR . '/parts/header.html' );
	}

	public function test_header_contains_navigation_block_with_always_overlay() {
		$this->assertStringContainsString( 'wp:navigation', $this->header_markup() );
		$this->assertStringContainsString( '"overlayMenu":"always"', $this->header_markup() );
	}

	public function test_header_contains_site_logo_block() {
		$this->assertStringContainsString( 'wp:site-logo', $this->header_markup() );
	}

	public function test_header_contains_book_now_button() {
		$this->assertStringContainsString( 'is-style-book-now', $this->header_markup() );
		$this->assertStringContainsString( 'Book Now', $this->header_markup() );
	}

	public function test_book_now_style_is_registered() {
		$styles = WP_Block_Styles_Registry::get_instance()->get_registered_styles_for_block( 'core/button' );
		$this->assertArrayHasKey( 'book-now', $styles );
	}
}
```

- [ ] **Step 7: Run tests and verify**

Run: `vendor/bin/phpunit`
Expected: `OK` — all tests from Task 2 and Task 3 pass.

- [ ] **Step 8: Commit**

```bash
git add wp-content/themes/tour-reference-theme
git commit -m "feat: add header template part with hamburger nav and Book Now CTA"
```

---

## Task 4: Footer template part

**Files:**
- Create: `wp-content/themes/tour-reference-theme/parts/footer.html`
- Modify: `wp-content/themes/tour-reference-theme/assets/css/theme.css` — footer-legal typography
- Create: `wp-content/themes/tour-reference-theme/tests/test-footer-part.php`

**Interfaces:**
- Consumes: Task 1's addendum's "Footer structure" finding — the markup below defaults to the single-centered-block structure that was the best evidence going into this plan (component-inventory.md deliberately avoided calling it "Footer Columns"); if Task 1 found a genuine multi-column grid instead, the implementer must restructure this markup into a matching multi-column `wp:group` layout and note the deviation. Task 2's tokens (`surface-header-footer`, social colors, `footer-legal` font family/size).

- [ ] **Step 1: Write `parts/footer.html`**

```html
<!-- wp:template-part {"slug":"footer","tagName":"footer","area":"footer","className":"site-footer"} -->
<!-- wp:group {"layout":{"type":"constrained"},"style":{"color":{"background":"var:preset|color|surface-header-footer"},"spacing":{"padding":{"top":"48px","bottom":"0"}}}} -->
<div class="wp-block-group" style="background-color:var(--wp--preset--color--surface-header-footer);padding-top:48px">

	<!-- wp:group {"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
	<div class="wp-block-group">
		<!-- wp:site-logo {"width":120} /-->

		<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"var:preset|font-size|body"}}} -->
		<p class="has-text-align-center">Original tour operator. Demo content for development purposes.</p>
		<!-- /wp:paragraph -->

		<!-- wp:social-links {"iconColor":"social-facebook","className":"is-style-logos-only footer-social"} -->
		<ul class="wp-block-social-links is-style-logos-only footer-social">
			<!-- wp:social-link {"url":"#","service":"facebook"} /-->
			<!-- wp:social-link {"url":"#","service":"instagram"} /-->
			<!-- wp:social-link {"url":"#","service":"whatsapp"} /-->
			<!-- wp:social-link {"url":"#","service":"tiktok"} /-->
		</ul>
		<!-- /wp:social-links -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"16px","bottom":"16px"}},"border":{"top":{"color":"var:preset|color|border-default","width":"1px"}}},"className":"footer-legal-bar"} -->
	<div class="wp-block-group footer-legal-bar" style="border-top-color:var(--wp--preset--color--border-default);border-top-width:1px;padding-top:16px;padding-bottom:16px">
		<!-- wp:paragraph {"align":"center","fontFamily":"footer-legal","fontSize":"footer-legal"} -->
		<p class="has-text-align-center has-footer-legal-font-size has-footer-legal-font-family">© 2026 Tour Reference. All rights reserved. Demo content only.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->
<!-- /wp:template-part -->
```

- [ ] **Step 2: Add the social icon brand colors and legal-bar letter-spacing to `assets/css/theme.css`**

```css
.footer-social .wp-social-link.wp-social-link-facebook { background-color: var(--wp--preset--color--social-facebook); }
.footer-social .wp-social-link.wp-social-link-instagram { background-color: var(--wp--preset--color--social-instagram); }
.footer-social .wp-social-link.wp-social-link-whatsapp { background-color: var(--wp--preset--color--social-whatsapp); }
.footer-social .wp-social-link.wp-social-link-tiktok { background-color: var(--wp--preset--color--social-tiktok); }

.footer-legal-bar p {
	letter-spacing: 0.5px;
	line-height: 22px;
}
```

- [ ] **Step 3: Write `tests/test-footer-part.php`**

```php
<?php
class Test_Footer_Part extends WP_UnitTestCase {

	private function footer_markup() {
		return file_get_contents( TOUR_THEME_DIR . '/parts/footer.html' );
	}

	public function test_footer_part_file_exists() {
		$this->assertFileExists( TOUR_THEME_DIR . '/parts/footer.html' );
	}

	public function test_footer_contains_social_links() {
		foreach ( array( 'facebook', 'instagram', 'whatsapp', 'tiktok' ) as $service ) {
			$this->assertStringContainsString( '"service":"' . $service . '"', $this->footer_markup() );
		}
	}

	public function test_footer_contains_copyright_line() {
		$this->assertStringContainsString( 'All rights reserved', $this->footer_markup() );
	}

	public function test_footer_uses_footer_legal_font() {
		$this->assertStringContainsString( 'fontFamily":"footer-legal"', $this->footer_markup() );
	}
}
```

- [ ] **Step 4: Run tests and verify**

Run: `vendor/bin/phpunit`
Expected: `OK` — all tests from Tasks 2-4 pass.

- [ ] **Step 5: Commit**

```bash
git add wp-content/themes/tour-reference-theme
git commit -m "feat: add footer template part with social links and legal bar"
```

---

## Task 5: Minimal template wiring header + footer

**Files:**
- Create: `wp-content/themes/tour-reference-theme/templates/index.html`
- Create: `wp-content/themes/tour-reference-theme/tests/test-templates.php`

**Interfaces:**
- Consumes: `parts/header.html`, `parts/footer.html` (Tasks 3-4) by slug reference.
- Produces: the minimum template WordPress requires to render any page with the header/footer visible — full per-page-type templates (Home, Tour Detail, etc.) are Milestone 5/6's job; this task exists only so the shell is visibly testable end-to-end now.

- [ ] **Step 1: Write `templates/index.html`**

```html
<!-- wp:template-part {"slug":"header","tagName":"header","area":"header"} /-->

<!-- wp:group {"layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"40px","bottom":"40px"}}}} -->
<div class="wp-block-group" style="padding-top:40px;padding-bottom:40px">
	<!-- wp:post-title /-->
	<!-- wp:post-content /-->
</div>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer","area":"footer"} /-->
```

- [ ] **Step 2: Write `tests/test-templates.php`**

```php
<?php
class Test_Templates extends WP_UnitTestCase {

	public function test_index_template_exists() {
		$this->assertFileExists( TOUR_THEME_DIR . '/templates/index.html' );
	}

	public function test_index_template_references_header_and_footer_parts() {
		$markup = file_get_contents( TOUR_THEME_DIR . '/templates/index.html' );

		$this->assertStringContainsString( '"slug":"header"', $markup );
		$this->assertStringContainsString( '"slug":"footer"', $markup );
	}

	public function test_theme_renders_a_page_without_fatal_error() {
		$post_id = self::factory()->post->create( array( 'post_status' => 'publish' ) );

		ob_start();
		query_posts( array( 'p' => $post_id ) );
		the_post();
		locate_template( 'templates/index.html', true, false );
		$output = ob_get_clean();

		$this->assertIsString( $output );
	}
}
```

Note on Step 2's third test: `locate_template()` on a raw HTML block template won't fully render block markup outside a real front-end request (block parsing happens in `wp_head`/template-loading machinery, not a bare `include`). If this test can't be made to pass meaningfully in the WP_UnitTestCase harness, replace it with a simpler assertion that the theme activates and `get_block_templates()` includes an `index` entry — verify which approach actually works before committing to one, and note the reasoning in the task report.

- [ ] **Step 3: Run tests and verify**

Run: `vendor/bin/phpunit`
Expected: `OK` — all tests from Tasks 2-5 pass.

- [ ] **Step 4: Commit**

```bash
git add wp-content/themes/tour-reference-theme
git commit -m "feat: add minimal index template wiring header and footer parts"
```

---

## Task 6: Local visual-regression tooling + acceptance run

**Files:**
- Create: `tools/local-audit/capture-local.mjs`
- Create: `tools/local-audit/diff.mjs`
- Create: `tools/local-audit/package.json`
- Create: `docs/visual-acceptance-report-m4.md`
- Modify: root `package.json` or `tools/local-audit/package.json` — add `pixelmatch` and `pngjs` dependencies

**Interfaces:**
- Consumes: `docs/reference-screenshots/home/{viewport}.png` (already captured in Milestone 1, one reference image per viewport) and this milestone's new local build's header/footer, reachable at `http://localhost/looptrails/` (the XAMPP local URL — confirm the exact local URL against `wp-config.php`'s `WP_HOME`/`WP_SITEURL` or by checking what M1's environment audit recorded, before hardcoding it).
- Produces: `docs/visual-acceptance-report-m4.md`, the spec §4-required deltas report — this is the milestone's actual acceptance gate, not the PHPUnit suite (which only checks markup exists, not that it visually matches).

- [ ] **Step 1: Activate the theme on the local site**

Run:
```bash
C:/xampp/wp-cli.bat theme activate tour-reference-theme --path=c:/xampp/htdocs/looptrails
```
Expected: no fatal errors, no PHP warnings. If any appear, fix them before proceeding — do not run the visual audit against a broken activation.

- [ ] **Step 2: Write `tools/local-audit/package.json`**

```json
{
	"name": "local-audit",
	"private": true,
	"type": "module",
	"scripts": {
		"capture": "node capture-local.mjs",
		"diff": "node diff.mjs"
	},
	"dependencies": {
		"playwright": "^1.62.1",
		"pixelmatch": "^6.0.0",
		"pngjs": "^7.0.0"
	}
}
```

Run: `npm --prefix tools/local-audit install`

- [ ] **Step 2: Write `tools/local-audit/capture-local.mjs`**

```javascript
import { chromium } from 'playwright';
import { mkdirSync } from 'node:fs';

const LOCAL_BASE_URL = 'http://localhost/looptrails'; // confirm against wp-config.php before running
const OUT_DIR = 'docs/reference-screenshots/local-m4';

const VIEWPORTS = [
	{ name: 'desktop', width: 1440, height: 1000 },
	{ name: 'laptop', width: 1280, height: 800 },
	{ name: 'tablet', width: 768, height: 1024 },
	{ name: 'mobile', width: 390, height: 844 },
	{ name: 'narrow-mobile', width: 360, height: 800 },
];

mkdirSync( OUT_DIR, { recursive: true } );

const browser = await chromium.launch();
for ( const viewport of VIEWPORTS ) {
	const context = await browser.newContext( { viewport: { width: viewport.width, height: viewport.height } } );
	const page = await context.newPage();
	await page.addStyleTag( { content: '*, *::before, *::after { animation: none !important; transition: none !important; }' } );
	await page.goto( LOCAL_BASE_URL, { waitUntil: 'networkidle' } );
	await page.screenshot( { path: `${OUT_DIR}/${viewport.name}-header.png`, clip: { x: 0, y: 0, width: viewport.width, height: 200 } } );
	await page.evaluate( () => window.scrollTo( 0, document.body.scrollHeight ) );
	await page.waitForTimeout( 300 );
	await page.screenshot( { path: `${OUT_DIR}/${viewport.name}-footer.png` } );
	await context.close();
}
await browser.close();
console.log( 'Local capture complete:', OUT_DIR );
```

Run: `node tools/local-audit/capture-local.mjs`

- [ ] **Step 3: Write `tools/local-audit/diff.mjs`**

```javascript
import { PNG } from 'pngjs';
import pixelmatch from 'pixelmatch';
import { readFileSync, writeFileSync } from 'node:fs';

const VIEWPORTS = [ 'desktop', 'laptop', 'tablet', 'mobile', 'narrow-mobile' ];
const REGION = 'header'; // run again with REGION=footer for the footer comparison

const results = [];
for ( const viewport of VIEWPORTS ) {
	const refPath = `docs/reference-screenshots/home/${viewport}.png`;
	const localPath = `docs/reference-screenshots/local-m4/${viewport}-${REGION}.png`;

	const ref = PNG.sync.read( readFileSync( refPath ) );
	const local = PNG.sync.read( readFileSync( localPath ) );

	const width = Math.min( ref.width, local.width );
	const height = Math.min( ref.height, local.height );
	const diff = new PNG( { width, height } );

	const mismatch = pixelmatch( ref.data, local.data, diff.data, width, height, { threshold: 0.1 } );
	const percent = ( ( mismatch / ( width * height ) ) * 100 ).toFixed( 2 );

	writeFileSync( `docs/reference-screenshots/local-m4/${viewport}-${REGION}-diff.png`, PNG.sync.write( diff ) );
	results.push( { viewport, region: REGION, mismatchPixels: mismatch, mismatchPercent: percent } );
}

console.log( JSON.stringify( results, null, 2 ) );
```

Run for both regions, capturing output:
```bash
REGION=header node tools/local-audit/diff.mjs > /tmp/diff-header.json
REGION=footer node tools/local-audit/diff.mjs > /tmp/diff-footer.json
```
(On Windows Git Bash, `REGION=header node ...` inline env-var syntax works as shown; if it doesn't in your shell, edit the `REGION` constant directly in `diff.mjs` between runs instead.)

Note: the `home/{viewport}.png` reference screenshots are full-page, while the local captures are cropped to header/footer regions — `pixelmatch` requires equal dimensions, so this diff script's naive `Math.min` crop is a rough approximation. If the reported percentages look meaningless (e.g., near 100% because you're diffing a cropped 200px-tall header image against a multi-thousand-pixel full page image), fix this before trusting the output: either crop the reference image to the same header/footer regions first (using a quick Playwright/sharp crop step, or by regenerating reference crops from the already-existing full-page reference PNGs), or capture the local screenshots as full-page and compare those instead. Get real, meaningful numbers before writing the report in Step 4 — do not report a diff percentage you know is comparing mismatched image regions.

- [ ] **Step 4: Write `docs/visual-acceptance-report-m4.md`**

Using the diff script's actual output (after fixing any region-mismatch issue from Step 3) plus manual visual comparison of the screenshot pairs, write a report following spec §4's rule literally:

```markdown
# Milestone 4 Visual Acceptance Report

Captured [DATE]. Reference: `docs/reference-screenshots/home/*.png` (Milestone 1). Local: `docs/reference-screenshots/local-m4/*.png`.

## Results by viewport

| Viewport | Header diff % | Footer diff % | Container edges within tolerance? | Font metrics within tolerance? | Horizontal overflow at 360px? |
|---|---|---|---|---|---|
| desktop (1440) | [X]% | [X]% | [yes/no] | [yes/no] | n/a |
| laptop (1280) | [X]% | [X]% | [yes/no] | [yes/no] | n/a |
| tablet (768) | [X]% | [X]% | [yes/no] | [yes/no] | n/a |
| mobile (390) | [X]% | [X]% | [yes/no] | [yes/no] | [yes/no] |
| narrow-mobile (360) | [X]% | [X]% | [yes/no] | [yes/no] | [yes/no] |

## Remaining deltas

[List every specific deviation found — do not write "looks close". For each: what differs, measured amount, whether it's within spec §4's tolerance or a genuine gap, and whether it's masked per spec §4's allowance (substituted photos/logo/map tiles/uncopyable text) or a real implementation gap to fix in a later milestone.]

## Known out-of-tolerance items carried forward

[Anything that misses the <8% overall pixel-diff target or a specific metric tolerance, with a stated reason — e.g., "logo is a placeholder mark, not the reference's logo, masked per spec §4" — and which milestone (if any) is expected to close the gap.]
```

- [ ] **Step 5: Commit**

```bash
git add tools/local-audit docs/visual-acceptance-report-m4.md docs/reference-screenshots/local-m4
git commit -m "test: add local visual-regression tooling and Milestone 4 acceptance report"
```

---

## Milestone completion checklist

- [x] All 6 tasks' PHPUnit tests pass in a single `vendor/bin/phpunit` run (from `wp-content/themes/tour-reference-theme/`).
- [x] `wp-cli.bat theme activate tour-reference-theme` succeeds with no fatal errors or PHP warnings against the real `looptrails` database.
- [x] `docs/visual-acceptance-report-m4.md` exists with real, non-fabricated numbers (not "looks close") — if any metric misses spec §4's tolerance, it is listed with a reason, not silently omitted.
- [x] Report to the user: files changed, tests run/passed, the visual acceptance report's headline numbers, and any known deviations — explicitly including any place Task 1's live-audit findings caused the implementation to diverge from this plan's best-evidence defaults (sticky behavior, footer structure), per spec §15's zero-undisclosed-deviation rule.
- [ ] Wait for the user's explicit pass before starting Milestone 5 (Home and reusable visual components).
