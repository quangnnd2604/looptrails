import { PNG } from 'pngjs';
import pixelmatch from 'pixelmatch';
import { readFileSync, writeFileSync } from 'node:fs';

/**
 * Pixel-diffs the reference crop against the local crop for one region.
 *
 * Run crop-reference.mjs first: it produces `{viewport}-{region}-ref.png` and
 * `{viewport}-{region}-cmp.png`, both cropped to the SAME reference-derived
 * region and the same pixel dimensions (see that file's docblock for the
 * history of the two region-mismatch bugs this replaced). pixelmatch therefore
 * needs no Math.min fallback at all, and a dimension mismatch is a hard error.
 *
 * Two numbers are reported per viewport:
 *
 *   rawPercent    -- every pixel compared.
 *   maskedPercent -- the site identity slot (reference wordmark / local
 *                    site-logo + site-title) zeroed to opaque black on BOTH
 *                    images first. Spec section 4 measures the diff
 *                    POST-masking and explicitly allows the logo to differ.
 *                    The masked rectangle is the union of the reference's
 *                    branding box and the local branding box, so the identical
 *                    region is blanked on both sides -- masking different
 *                    regions per side would itself manufacture a difference.
 *
 * The masked denominator is still the full region area (masked pixels count as
 * matching, not as removed), which keeps maskedPercent <= rawPercent and makes
 * the two directly comparable.
 */

const VIEWPORTS = [ 'desktop', 'laptop', 'tablet', 'mobile', 'narrow-mobile' ];
// `node diff.mjs footer` or REGION=footer; defaults to the header region.
// The positional argument exists because npm scripts run through cmd.exe on
// Windows, where a `REGION=footer node ...` prefix is not valid syntax.
const REGION = process.argv[ 2 ] ?? process.env.REGION ?? 'header';
const OUT_DIR = 'docs/reference-screenshots/local-m4';

const referenceRegions = JSON.parse( readFileSync( `${OUT_DIR}/reference-regions.json`, 'utf8' ) );
const localRegions = JSON.parse( readFileSync( `${OUT_DIR}/regions.json`, 'utf8' ) );

const MASK_PAD = 2; // px of slack around a measured branding box

function clipRect( rect, width, height ) {
	if ( ! rect ) {
		return null;
	}
	const x0 = Math.max( 0, Math.floor( rect.x ) - MASK_PAD );
	const y0 = Math.max( 0, Math.floor( rect.y ) - MASK_PAD );
	const x1 = Math.min( width, Math.ceil( rect.x + rect.width ) + MASK_PAD );
	const y1 = Math.min( height, Math.ceil( rect.y + rect.height ) + MASK_PAD );
	if ( x1 <= x0 || y1 <= y0 ) {
		return null;
	}
	return { x: x0, y: y0, width: x1 - x0, height: y1 - y0 };
}

function unionRect( a, b ) {
	if ( ! a ) return b;
	if ( ! b ) return a;
	const x = Math.min( a.x, b.x );
	const y = Math.min( a.y, b.y );
	return { x, y, width: Math.max( a.x + a.width, b.x + b.width ) - x, height: Math.max( a.y + a.height, b.y + b.height ) - y };
}

/**
 * Branding boxes converted into crop-relative coordinates.
 *  - header crops are anchored at page y = 0 on both sides, so page-absolute
 *    y == crop y for both.
 *  - footer crops are anchored at each page's own footer top edge, so the
 *    offset from that edge is what transfers.
 */
function maskRectFor( viewport, region ) {
	const ref = referenceRegions[ viewport ];
	const local = localRegions[ viewport ];

	if ( region === 'header' ) {
		return unionRect( ref.headerLogoBox, local.headerBrandBox );
	}

	const refBox = ref.footerLogoOffset;
	const localBox = local.footerBrandBox
		? { ...local.footerBrandBox, y: local.footerBrandBox.y - local.footerBox.y }
		: null;
	return unionRect( refBox, localBox );
}

function blank( png, rect ) {
	for ( let y = rect.y; y < rect.y + rect.height; y++ ) {
		for ( let x = rect.x; x < rect.x + rect.width; x++ ) {
			const idx = ( png.width * y + x ) << 2;
			png.data[ idx ] = 0;
			png.data[ idx + 1 ] = 0;
			png.data[ idx + 2 ] = 0;
			png.data[ idx + 3 ] = 255;
		}
	}
}

function copyPng( src ) {
	const out = new PNG( { width: src.width, height: src.height } );
	src.data.copy( out.data );
	return out;
}

const results = [];
for ( const viewport of VIEWPORTS ) {
	const refPath = `${OUT_DIR}/${viewport}-${REGION}-ref.png`;
	const localPath = `${OUT_DIR}/${viewport}-${REGION}-cmp.png`;

	const ref = PNG.sync.read( readFileSync( refPath ) );
	const local = PNG.sync.read( readFileSync( localPath ) );

	if ( ref.width !== local.width || ref.height !== local.height ) {
		throw new Error(
			`${viewport}-${REGION}: dimension mismatch ref ${ref.width}x${ref.height} vs local ${local.width}x${local.height} -- run crop-reference.mjs first`
		);
	}
	const { width, height } = ref;

	// --- raw
	const rawDiff = new PNG( { width, height } );
	const rawMismatch = pixelmatch( ref.data, local.data, rawDiff.data, width, height, { threshold: 0.1 } );
	writeFileSync( `${OUT_DIR}/${viewport}-${REGION}-diff.png`, PNG.sync.write( rawDiff ) );

	// --- masked
	const mask = clipRect( maskRectFor( viewport, REGION ), width, height );
	const maskedRef = copyPng( ref );
	const maskedLocal = copyPng( local );
	if ( mask ) {
		blank( maskedRef, mask );
		blank( maskedLocal, mask );
	}
	const maskedDiff = new PNG( { width, height } );
	const maskedMismatch = pixelmatch( maskedRef.data, maskedLocal.data, maskedDiff.data, width, height, { threshold: 0.1 } );
	writeFileSync( `${OUT_DIR}/${viewport}-${REGION}-diff-masked.png`, PNG.sync.write( maskedDiff ) );

	results.push( {
		viewport,
		region: REGION,
		width,
		height,
		mask: mask ?? 'none measured',
		rawMismatchPixels: rawMismatch,
		rawPercent: ( ( rawMismatch / ( width * height ) ) * 100 ).toFixed( 2 ),
		maskedMismatchPixels: maskedMismatch,
		maskedPercent: ( ( maskedMismatch / ( width * height ) ) * 100 ).toFixed( 2 ),
	} );
}

console.log( JSON.stringify( results, null, 2 ) );
