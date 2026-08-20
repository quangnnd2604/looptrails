import { chromium } from 'playwright';
import { mkdirSync, writeFileSync } from 'node:fs';

const LOCAL_BASE_URL = 'http://localhost/looptrails'; // confirmed against wp-cli.bat option get siteurl
const OUT_DIR = 'docs/reference-screenshots/local-m4';

const VIEWPORTS = [
	{ name: 'desktop', width: 1440, height: 1000 },
	{ name: 'laptop', width: 1280, height: 800 },
	{ name: 'tablet', width: 768, height: 1024 },
	{ name: 'mobile', width: 390, height: 844 },
	{ name: 'narrow-mobile', width: 360, height: 800 },
];

mkdirSync( OUT_DIR, { recursive: true } );

// Region manifest: the exact pixel height of the rendered header/footer
// elements per viewport, recorded from real DOM bounding boxes rather than
// guessed. crop-reference.mjs uses this to crop the Milestone-1 reference
// full-page PNGs to the *same* regions, so diff.mjs compares matching image
// areas instead of a naive Math.min crop of mismatched regions (see brief
// Step 4's warning).
const regions = {};

const browser = await chromium.launch();
for ( const viewport of VIEWPORTS ) {
	const context = await browser.newContext( { viewport: { width: viewport.width, height: viewport.height } } );
	const page = await context.newPage();
	await page.addStyleTag( { content: '*, *::before, *::after { animation: none !important; transition: none !important; }' } );
	await page.goto( LOCAL_BASE_URL, { waitUntil: 'networkidle' } );

	// Full-page capture for a whole-page sanity check / the visual acceptance report.
	await page.screenshot( { path: `${OUT_DIR}/${viewport.name}-full.png`, fullPage: true } );

	const headerLocator = page.locator( 'header.wp-block-template-part' );
	const footerLocator = page.locator( 'footer.wp-block-template-part' );

	const headerBox = await headerLocator.boundingBox();
	await headerLocator.screenshot( { path: `${OUT_DIR}/${viewport.name}-header.png` } );

	const footerBox = await footerLocator.boundingBox();
	await footerLocator.screenshot( { path: `${OUT_DIR}/${viewport.name}-footer.png` } );

	regions[ viewport.name ] = {
		width: viewport.width,
		headerHeight: Math.round( headerBox.height ),
		footerHeight: Math.round( footerBox.height ),
	};

	await context.close();
}
await browser.close();
writeFileSync( `${OUT_DIR}/regions.json`, JSON.stringify( regions, null, 2 ) + '\n' );
console.log( 'Local capture complete:', OUT_DIR );
console.log( JSON.stringify( regions, null, 2 ) );
