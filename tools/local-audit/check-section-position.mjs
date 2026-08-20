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
	// Scan the vertical-middle row of the crop for near-white pixels
	// (button background), return [minX, maxX] of the contiguous-ish white run.
	const y = Math.floor( png.height / 2 );
	let minX = null;
	let maxX = null;
	for ( let x = 0; x < png.width; x++ ) {
		const idx = ( png.width * y + x ) << 2;
		const r = png.data[ idx ];
		const g = png.data[ idx + 1 ];
		const b = png.data[ idx + 2 ];
		if ( r > 245 && g > 245 && b > 245 ) {
			if ( minX === null ) minX = x;
			maxX = x;
		}
	}
	return { minX, maxX };
}

const browser = await chromium.launch();
for ( const vp of VIEWPORTS ) {
	const context = await browser.newContext( { viewport: { width: vp.width, height: vp.height } } );
	const page = await context.newPage();
	await page.goto( 'http://localhost/looptrails', { waitUntil: 'networkidle' } );
	const box = await page.locator( '.wp-block-button.is-style-book-now' ).boundingBox();
	await context.close();

	const ref = PNG.sync.read( readFileSync( `docs/reference-screenshots/local-m4/${vp.name}-header-ref.png` ) );
	const { minX, maxX } = findWhiteRegionXRange( ref );

	console.log( `${vp.name}: local Book Now left-x=${Math.round( box.x )} (width ${vp.width}) | reference white-region x=[${minX},${maxX}] (width ${vp.width}) | offset=${minX === null ? 'n/a' : Math.abs( Math.round( box.x ) - minX )}px` );
}
await browser.close();
