import { PNG } from 'pngjs';
import { readFileSync, writeFileSync } from 'node:fs';

/**
 * Crops BOTH sides of every header/footer comparison to the SAME,
 * REFERENCE-DERIVED region.
 *
 * History of this file:
 *
 * 1. The brief's naive diff.mjs compared a local header/footer crop (e.g.
 *    200px tall) against a full-page reference PNG (thousands of px tall)
 *    using pixelmatch's Math.min(width,height) trick, which silently diffed
 *    the *top* of the reference page against the local footer.
 *
 * 2. This script's first version fixed that by cropping the reference PNG --
 *    but using the LOCAL element's height. Final-review finding C2: that is
 *    the same class of bug one level subtler. The reference footer is 526-622px
 *    tall (wordmark, address, hotline, email, website, legal block, compliance
 *    badge, 5-icon social row); the local footer is 192-231px. Taking "the
 *    bottom 192px of the reference page" compared the reference footer's
 *    bottom third against the local footer's entire content, and the resulting
 *    3.36%/3.70%/5.87% "passes" were mostly two flat cream backgrounds
 *    overlapping.
 *
 * 3. This version measures the reference's own header/footer heights on the
 *    live site (measure-reference.mjs -> reference-regions.json) and crops
 *    both images to that reference-derived height:
 *
 *      header region: rows [0, refHeaderHeight) of each page, top-anchored
 *                     (both headers start at page y = 0).
 *      footer region: refFooterHeight rows starting at each page's own footer
 *                     top edge -- the reference's footer top is derived from
 *                     the bottom of the reference PNG (that PNG is an older
 *                     Milestone-1 capture whose total page height no longer
 *                     matches the live page, but the footer is bottom-anchored
 *                     in both), the local footer top comes from regions.json's
 *                     real DOM box.
 *
 *    Where the local page has fewer rows than the reference region needs, the
 *    shortfall is left transparent and reported, rather than shrinking the
 *    region to hide it. The height delta is therefore visible in the diff
 *    image and in the diff percentage as real, honest mismatch: "the reference
 *    footer contains N px of content this milestone's footer does not."
 *
 * Outputs per viewport and region:
 *   {viewport}-{region}-ref.png  reference crop
 *   {viewport}-{region}-cmp.png  local crop, same width x height
 */

const VIEWPORTS = [ 'desktop', 'laptop', 'tablet', 'mobile', 'narrow-mobile' ];
const OUT_DIR = 'docs/reference-screenshots/local-m4';

const referenceRegions = JSON.parse( readFileSync( `${OUT_DIR}/reference-regions.json`, 'utf8' ) );
const localRegions = JSON.parse( readFileSync( `${OUT_DIR}/regions.json`, 'utf8' ) );

/**
 * Copies `height` rows starting at `srcY` out of `src` into a fresh
 * width x height PNG. Rows outside `src` are left transparent.
 */
function cropRows( src, srcY, width, height ) {
	const out = new PNG( { width, height } );
	const from = Math.max( 0, srcY );
	const to = Math.min( src.height, srcY + height );
	const rows = to - from;
	let padded = height;
	if ( rows > 0 ) {
		// PNG.sync.read() returns a plain metadata object, not a PNG instance,
		// so use the static PNG.bitblt(src, dst, ...) rather than an instance
		// method.
		PNG.bitblt( src, out, 0, from, width, rows, 0, from - srcY );
		padded = height - rows;
	}
	return { png: out, paddedRows: padded };
}

const summary = [];

for ( const viewport of VIEWPORTS ) {
	const ref = PNG.sync.read( readFileSync( `docs/reference-screenshots/home/${viewport}.png` ) );
	const local = PNG.sync.read( readFileSync( `${OUT_DIR}/${viewport}-full.png` ) );
	const refRegion = referenceRegions[ viewport ];
	const localRegion = localRegions[ viewport ];

	// Width-assert guard (final-review Minor, T6): a width mismatch would make
	// PNG.bitblt read rows at the wrong stride and produce a sheared crop, so
	// fail loudly instead.
	if ( ref.width !== local.width ) {
		throw new Error( `${viewport}: reference PNG is ${ref.width}px wide but local PNG is ${local.width}px -- capture widths must match` );
	}
	if ( ref.width !== refRegion.width ) {
		throw new Error( `${viewport}: reference PNG is ${ref.width}px wide but reference-regions.json recorded ${refRegion.width}px` );
	}
	const width = ref.width;

	// ---- Header: reference-derived height, both crops anchored at page top.
	const headerHeight = refRegion.headerHeight;
	const headerRef = cropRows( ref, 0, width, headerHeight );
	const headerCmp = cropRows( local, 0, width, headerHeight );
	writeFileSync( `${OUT_DIR}/${viewport}-header-ref.png`, PNG.sync.write( headerRef.png ) );
	writeFileSync( `${OUT_DIR}/${viewport}-header-cmp.png`, PNG.sync.write( headerCmp.png ) );

	// ---- Footer: reference-derived height, each crop anchored at its own
	// footer's top edge.
	const footerHeight = refRegion.footerHeight;
	const refFooterTop = ref.height - refRegion.footerTopOffsetFromPageBottom;
	const localFooterTop = localRegion.footerBox.y;
	const footerRef = cropRows( ref, refFooterTop, width, footerHeight );
	const footerCmp = cropRows( local, localFooterTop, width, footerHeight );
	writeFileSync( `${OUT_DIR}/${viewport}-footer-ref.png`, PNG.sync.write( footerRef.png ) );
	writeFileSync( `${OUT_DIR}/${viewport}-footer-cmp.png`, PNG.sync.write( footerCmp.png ) );

	summary.push( {
		viewport,
		width,
		header: {
			regionHeight: headerHeight,
			referenceElementHeight: refRegion.headerHeight,
			localElementHeight: localRegion.headerHeight,
			localPaddedRows: headerCmp.paddedRows,
		},
		footer: {
			regionHeight: footerHeight,
			referenceElementHeight: refRegion.footerHeight,
			localElementHeight: localRegion.footerHeight,
			localPaddedRows: footerCmp.paddedRows,
		},
	} );
}

writeFileSync( `${OUT_DIR}/crop-summary.json`, JSON.stringify( summary, null, 2 ) + '\n' );
console.log( JSON.stringify( summary, null, 2 ) );
