import { chromium } from 'playwright';
import { mkdirSync, writeFileSync } from 'node:fs';

/**
 * Measures the REFERENCE page's real header/footer element geometry.
 *
 * Why this script exists (final-review finding C2): crop-reference.mjs used to
 * slice the reference full-page PNG using the *local* header/footer heights.
 * The reference footer is several hundred pixels tall (wordmark, address,
 * hotline, email, website, legal block, compliance badge, 5-icon social row)
 * while the local footer is ~190-230px, so "the bottom 193px of the reference
 * page" was the reference footer's bottom third being diffed against the local
 * footer's entire content -- a mismatched-region comparison whose numbers
 * happened to look in-tolerance only because two flat cream backgrounds
 * overlapped.
 *
 * Same live-site Playwright pattern as Task 1's tools/reference-audit/
 * interaction-audit.mjs. Nothing from the reference site is copied or
 * redistributed here -- only element geometry is recorded.
 *
 * Output: docs/reference-screenshots/local-m4/reference-regions.json
 */

const REFERENCE_URL = 'https://looptrails.com/';
const OUT_DIR = 'docs/reference-screenshots/local-m4';

const VIEWPORTS = [
	{ name: 'desktop', width: 1440, height: 1000 },
	{ name: 'laptop', width: 1280, height: 800 },
	{ name: 'tablet', width: 768, height: 1024 },
	{ name: 'mobile', width: 390, height: 844 },
	{ name: 'narrow-mobile', width: 360, height: 800 },
];

// Element selection rule. The reference is an Elementor build and carries
// SEVERAL <header>/<footer> tags:
//   - <header class="elementor-location-header"> is a 0px-tall wrapper (its
//     only child is position:fixed, so the wrapper collapses),
//   - <header id="headerScroll"> is the real 66px fixed bar,
//   - a further half-dozen <header class="lt-*__head"> section headings sit
//     mid-page.
// A naive `.first()` (or even "first element with height > 0") therefore
// yields a 0px or a mid-page region. The rule below is explicit instead:
// take the widest, tallest full-bleed candidate anchored at the correct end
// of the page.
const MEASURE = () => {
	const pageRect = ( el ) => {
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

	const fullBleed = ( selector ) =>
		Array.from( document.querySelectorAll( selector ) )
			.map( ( el ) => ( { el, rect: pageRect( el ) } ) )
			.filter( ( c ) => c.rect.height > 0 && c.rect.width >= window.innerWidth * 0.9 );

	// Header: the tallest full-bleed <header> whose top edge is in the first
	// 120px of the page (excludes the mid-page section headings). Measured at
	// scroll-top so a scroll-reactive header cannot skew the number.
	const headerCandidates = fullBleed( 'header' ).filter( ( c ) => c.rect.y <= 120 );
	headerCandidates.sort( ( a, b ) => b.rect.height - a.rect.height );
	const header = headerCandidates[ 0 ] ?? null;

	// Footer: the tallest full-bleed <footer> whose bottom edge reaches the
	// last 120px of the document.
	const docHeight = Math.round( document.documentElement.scrollHeight );
	const footerCandidates = fullBleed( 'footer' ).filter( ( c ) => c.rect.y + c.rect.height >= docHeight - 120 );
	footerCandidates.sort( ( a, b ) => b.rect.height - a.rect.height );
	const footer = footerCandidates[ 0 ] ?? null;

	// Branding marks, for the masking step in diff.mjs (spec section 4 allows
	// masking logo differences). Recorded as page-absolute boxes; diff.mjs
	// converts them into crop-relative coordinates.
	const firstImageIn = ( root ) =>
		root ? Array.from( root.querySelectorAll( 'img, svg' ) ).find( ( el ) => el.getBoundingClientRect().height > 0 ) ?? null : null;

	return {
		headerBox: header ? header.rect : null,
		footerBox: footer ? footer.rect : null,
		headerLogoBox: pageRect( firstImageIn( header?.el ) ),
		footerLogoBox: pageRect( firstImageIn( footer?.el ) ),
		pageHeight: docHeight,
	};
};

mkdirSync( OUT_DIR, { recursive: true } );

const regions = {};
const browser = await chromium.launch();

for ( const viewport of VIEWPORTS ) {
	const context = await browser.newContext( { viewport: { width: viewport.width, height: viewport.height } } );
	const page = await context.newPage();
	await page.goto( REFERENCE_URL, { waitUntil: 'networkidle' } );
	await page.addStyleTag( { content: '*, *::before, *::after { animation: none !important; transition: none !important; }' } );

	const top = await page.evaluate( MEASURE );

	// The footer only reaches its full height once lazy-loaded content below
	// the fold has rendered, so measure it after scrolling to the bottom.
	await page.evaluate( () => window.scrollTo( 0, document.body.scrollHeight ) );
	await page.waitForTimeout( 1500 );
	const bottom = await page.evaluate( MEASURE );

	// Fail loudly rather than writing a 0px region that would silently produce
	// an empty crop downstream (this exact failure mode produced a
	// headerHeight of 0 in the first draft of this script).
	if ( ! top.headerBox || top.headerBox.height === 0 ) {
		throw new Error( `${viewport.name}: could not measure a non-zero reference header` );
	}
	if ( ! bottom.footerBox || bottom.footerBox.height === 0 ) {
		throw new Error( `${viewport.name}: could not measure a non-zero reference footer` );
	}

	regions[ viewport.name ] = {
		width: viewport.width,
		headerHeight: top.headerBox.height,
		footerHeight: bottom.footerBox.height,
		// footerTopOffsetFromPageBottom lets crop-reference.mjs anchor the
		// footer crop to the *bottom* of the Milestone-1 reference PNG, which
		// is a different (older) capture whose total page height no longer
		// matches today's live page height.
		footerTopOffsetFromPageBottom: bottom.pageHeight - bottom.footerBox.y,
		pageHeight: bottom.pageHeight,
		headerLogoBox: top.headerLogoBox,
		// Footer wordmark, expressed relative to the footer's own top edge so
		// it survives the page-height drift described above.
		footerLogoOffset:
			bottom.footerLogoBox && bottom.footerBox
				? {
						x: bottom.footerLogoBox.x,
						y: bottom.footerLogoBox.y - bottom.footerBox.y,
						width: bottom.footerLogoBox.width,
						height: bottom.footerLogoBox.height,
				  }
				: null,
	};

	await context.close();
}

await browser.close();
writeFileSync( `${OUT_DIR}/reference-regions.json`, JSON.stringify( regions, null, 2 ) + '\n' );
console.log( 'Reference region measurement complete:', `${OUT_DIR}/reference-regions.json` );
console.log( JSON.stringify( regions, null, 2 ) );
