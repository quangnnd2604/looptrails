import { chromium } from 'playwright';
import { PNG } from 'pngjs';
import { readFileSync } from 'node:fs';

// Measures the "Book Now" button's left-edge x position in the local render
// (via real DOM boundingBox()) and estimates the same in the Milestone-1
// reference header crop (via pixel-scanning for its white background against
// the cream #e4e0da header surface -- the button is the only white region in
// that crop), to give a real, load-bearing number for spec 4's "major
// section start positions within 24px" criterion.

const VIEWPORTS = [
	{ name: 'desktop', width: 1440, height: 1000 },
	{ name: 'laptop', width: 1280, height: 800 },
	{ name: 'tablet', width: 768, height: 1024 },
	{ name: 'mobile', width: 390, height: 844 },
	{ name: 'narrow-mobile', width: 360, height: 800 },
];

function findWhiteRegionXRange( png ) {
	// Scan the vertical-middle row of the crop for near-white pixels (the
	// button's white pill background) and return [minX, maxX] plus diagnostics.
	//
	// Known limitation (final-review Minor): this is a min/max span, not a
	// contiguity test. That is deliberate -- the reference pill contains orange
	// glyphs and an orange circular icon, so the white run is genuinely
	// interrupted and a "longest contiguous run" would measure a fragment of
	// the pill instead of the pill. The span is only trustworthy because
	// nothing else in that row is near-white (the header surface is cream
	// #e4e0da, well under the 245 threshold). `whitePixels` and `gaps` are
	// printed so a future reader can see whether that assumption still holds
	// rather than taking the numbers on faith.
	const y = Math.floor( png.height / 2 );
	let minX = null;
	let maxX = null;
	let whitePixels = 0;
	let gaps = 0;
	let prevWasWhite = false;
	for ( let x = 0; x < png.width; x++ ) {
		const idx = ( png.width * y + x ) << 2;
		const r = png.data[ idx ];
		const g = png.data[ idx + 1 ];
		const b = png.data[ idx + 2 ];
		const isWhite = r > 245 && g > 245 && b > 245;
		if ( isWhite ) {
			if ( minX === null ) minX = x;
			maxX = x;
			whitePixels++;
		} else if ( prevWasWhite && minX !== null ) {
			gaps++;
		}
		prevWasWhite = isWhite;
	}
	// The trailing transition out of the final white run is not an interior gap.
	if ( gaps > 0 ) {
		gaps--;
	}
	return { minX, maxX, whitePixels, gaps };
}

const browser = await chromium.launch();
for ( const vp of VIEWPORTS ) {
	const context = await browser.newContext( { viewport: { width: vp.width, height: vp.height } } );
	const page = await context.newPage();
	await page.goto( 'http://localhost/looptrails', { waitUntil: 'networkidle' } );
	const box = await page.locator( '.wp-block-button.is-style-book-now' ).boundingBox();
	await context.close();

	const ref = PNG.sync.read( readFileSync( `docs/reference-screenshots/local-m4/${vp.name}-header-ref.png` ) );
	const { minX, maxX, whitePixels, gaps } = findWhiteRegionXRange( ref );

	const localLeft = Math.round( box.x );
	const localRight = Math.round( box.x + box.width );
	const leftOffset = minX === null ? null : Math.abs( localLeft - minX );
	const rightOffset = maxX === null ? null : Math.abs( localRight - maxX );

	console.log(
		`${vp.name}: local Book Now x=[${localLeft},${localRight}] (w ${localRight - localLeft}) | ` +
			`reference white-region x=[${minX},${maxX}] (w ${maxX - minX}, ${whitePixels}px white, ${gaps} interior gap(s)) | ` +
			`left-edge offset=${leftOffset ?? 'n/a'}px ${leftOffset !== null && leftOffset <= 24 ? 'PASS' : 'FAIL'} | ` +
			`right-edge offset=${rightOffset ?? 'n/a'}px`
	);
}
await browser.close();
