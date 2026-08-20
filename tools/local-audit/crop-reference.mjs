import { PNG } from 'pngjs';
import { readFileSync, writeFileSync } from 'node:fs';

// The brief's naive diff.mjs compares a local header/footer crop (e.g. 200px
// tall) against a full-page reference PNG (thousands of px tall) using
// pixelmatch's Math.min(width,height) trick. For the footer region that
// silently compares the *top* of the tall reference page (Math.min takes the
// smaller local height, so pixelmatch reads only the first N rows of both
// buffers) against the local footer crop — a meaningless region mismatch.
//
// This script fixes that at the source: it crops the Milestone-1 reference
// full-page PNGs down to the *same* header/footer pixel regions that
// capture-local.mjs recorded (via real DOM boundingBox() heights in
// regions.json), so diff.mjs ends up comparing matching regions of matching
// size for both the header and the footer.

const VIEWPORTS = [ 'desktop', 'laptop', 'tablet', 'mobile', 'narrow-mobile' ];
const OUT_DIR = 'docs/reference-screenshots/local-m4';

for ( const viewport of VIEWPORTS ) {
	// Read the exact pixel dimensions off the actual captured local
	// header/footer PNGs (rather than the boundingBox() heights recorded in
	// regions.json) so the crop is guaranteed pixel-identical to what
	// diff.mjs will compare against -- Playwright's element screenshot can
	// round sub-pixel layout heights up by a pixel versus boundingBox().
	const localHeader = PNG.sync.read( readFileSync( `${OUT_DIR}/${viewport}-header.png` ) );
	const localFooter = PNG.sync.read( readFileSync( `${OUT_DIR}/${viewport}-footer.png` ) );
	const width = localHeader.width;
	const headerHeight = localHeader.height;
	const footerHeight = localFooter.height;
	const ref = PNG.sync.read( readFileSync( `docs/reference-screenshots/home/${viewport}.png` ) );

	// Header region: top `headerHeight` px of the reference full page —
	// matches capture-local.mjs's header.wp-block-template-part element crop.
	// PNG.sync.read() returns a plain metadata object, not a PNG instance, so
	// use the static PNG.bitblt(src, dst, ...) rather than an instance method.
	const headerCrop = new PNG( { width, height: headerHeight } );
	PNG.bitblt( ref, headerCrop, 0, 0, width, headerHeight, 0, 0 );
	writeFileSync( `${OUT_DIR}/${viewport}-header-ref.png`, PNG.sync.write( headerCrop ) );

	// Footer region: bottom `footerHeight` px of the reference full page —
	// matches the local footer.wp-block-template-part element crop.
	const footerCrop = new PNG( { width, height: footerHeight } );
	PNG.bitblt( ref, footerCrop, 0, ref.height - footerHeight, width, footerHeight, 0, 0 );
	writeFileSync( `${OUT_DIR}/${viewport}-footer-ref.png`, PNG.sync.write( footerCrop ) );

	console.log( `${viewport}: header ${width}x${headerHeight}, footer ${width}x${footerHeight} (ref page was ${ref.width}x${ref.height})` );
}
