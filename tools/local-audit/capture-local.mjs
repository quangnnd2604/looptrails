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

// Region manifest: the real page-absolute geometry of the rendered
// header/footer elements per viewport, plus the branding box that diff.mjs
// masks and the two elements whose wrapping explains the footer's height
// growth at narrow widths. crop-reference.mjs consumes this together with
// reference-regions.json (the live reference's own measurements) so both
// sides of every diff are cropped to the SAME, reference-derived region.
const regions = {};

const browser = await chromium.launch();
for ( const viewport of VIEWPORTS ) {
	const context = await browser.newContext( { viewport: { width: viewport.width, height: viewport.height } } );
	const page = await context.newPage();
	await page.goto( LOCAL_BASE_URL, { waitUntil: 'networkidle' } );
	// Animation kill-switch must be injected AFTER goto(): addStyleTag()
	// operates on the current document, and a navigation discards it.
	await page.addStyleTag( { content: '*, *::before, *::after { animation: none !important; transition: none !important; }' } );

	// Full-page capture. crop-reference.mjs crops BOTH sides out of the
	// full-page PNGs (not the element screenshots) because the comparison
	// region height is reference-derived and is generally taller than the
	// local element.
	await page.screenshot( { path: `${OUT_DIR}/${viewport.name}-full.png`, fullPage: true } );

	const headerLocator = page.locator( 'header.wp-block-template-part' );
	const footerLocator = page.locator( 'footer.wp-block-template-part' );

	// Element screenshots are still produced: they document what the theme
	// actually renders, and check-* scripts / the report reference them.
	await headerLocator.screenshot( { path: `${OUT_DIR}/${viewport.name}-header.png` } );
	await footerLocator.screenshot( { path: `${OUT_DIR}/${viewport.name}-footer.png` } );

	const geometry = await page.evaluate( () => {
		const box = ( el ) => {
			if ( ! el ) {
				return null;
			}
			const r = el.getBoundingClientRect();
			return {
				x: Math.round( r.x + window.scrollX ),
				y: Math.round( r.y + window.scrollY ),
				width: Math.round( r.width ),
				height: Math.round( r.height ),
			};
		};
		const union = ( a, b ) => {
			if ( ! a ) return b;
			if ( ! b ) return a;
			const x = Math.min( a.x, b.x );
			const y = Math.min( a.y, b.y );
			return {
				x,
				y,
				width: Math.max( a.x + a.width, b.x + b.width ) - x,
				height: Math.max( a.y + a.height, b.y + b.height ) - y,
			};
		};
		const header = document.querySelector( 'header.wp-block-template-part' );
		const footer = document.querySelector( 'footer.wp-block-template-part' );
		const tagline = footer ? footer.querySelector( 'p.has-body-font-size' ) : null;
		const navButton = header ? header.querySelector( '.wp-block-navigation__responsive-container-open' ) : null;

		return {
			pageHeight: Math.round( document.documentElement.scrollHeight ),
			headerBox: box( header ),
			footerBox: box( footer ),
			// Site identity slot (site-logo renders empty with no custom_logo
			// theme mod set, site-title always renders) -- the region diff.mjs
			// masks per spec section 4's logo-difference allowance.
			headerBrandBox: union(
				box( header ? header.querySelector( '.wp-block-site-logo' ) : null ),
				box( header ? header.querySelector( '.wp-block-site-title' ) : null )
			),
			footerBrandBox: box( footer ? footer.querySelector( '.wp-block-site-logo' ) : null ),
			// Recorded to settle, factually, what makes the footer taller at
			// narrow widths: the tagline paragraph wrapping to more lines, or
			// the header's hamburger button wrapping.
			taglineBox: box( tagline ),
			taglineLineHeight: tagline ? parseFloat( getComputedStyle( tagline ).lineHeight ) : null,
			navButtonBox: box( navButton ),
		};
	} );

	regions[ viewport.name ] = {
		width: viewport.width,
		headerHeight: geometry.headerBox.height,
		footerHeight: geometry.footerBox.height,
		...geometry,
	};

	await context.close();
}
await browser.close();
writeFileSync( `${OUT_DIR}/regions.json`, JSON.stringify( regions, null, 2 ) + '\n' );
console.log( 'Local capture complete:', OUT_DIR );
console.log( JSON.stringify( regions, null, 2 ) );
