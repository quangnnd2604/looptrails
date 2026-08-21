import { chromium } from 'playwright';
import { writeFileSync, mkdirSync } from 'node:fs';
import { pages, VIEWPORTS } from './pages.mjs';

const OUT_DIR = 'docs/reference-screenshots/interaction-audit';
mkdirSync(OUT_DIR, { recursive: true });

const homePage = pages.find((p) => p.label === 'Home') ?? pages[0];
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
