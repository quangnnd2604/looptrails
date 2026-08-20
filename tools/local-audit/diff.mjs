import { PNG } from 'pngjs';
import pixelmatch from 'pixelmatch';
import { readFileSync, writeFileSync } from 'node:fs';

const VIEWPORTS = [ 'desktop', 'laptop', 'tablet', 'mobile', 'narrow-mobile' ];
const REGION = process.env.REGION ?? 'header'; // set REGION=footer for the footer comparison

// NOTE: the brief's original diff.mjs compared the LOCAL header/footer crop
// (e.g. 200px tall) directly against the RAW full-page Milestone-1 reference
// PNG (thousands of px tall) via pixelmatch's Math.min(width,height) sizing.
// For the footer region in particular this silently diffs the *top* of the
// tall reference page (Math.min always takes the smaller local height, so
// pixelmatch only reads the first N rows of *both* buffers) against the
// local footer crop -- a meaningless region mismatch, not a real visual
// delta. Fixed by pre-cropping the reference PNGs to the same header/footer
// regions with crop-reference.mjs (run before this script), and diffing
// against those `${viewport}-${REGION}-ref.png` crops instead of the raw
// reference. Both sides are now the *same* real element regions and the
// *same* pixel dimensions, so pixelmatch needs no Math.min fallback at all.

const results = [];
for ( const viewport of VIEWPORTS ) {
	const refPath = `docs/reference-screenshots/local-m4/${viewport}-${REGION}-ref.png`;
	const localPath = `docs/reference-screenshots/local-m4/${viewport}-${REGION}.png`;

	const ref = PNG.sync.read( readFileSync( refPath ) );
	const local = PNG.sync.read( readFileSync( localPath ) );

	if ( ref.width !== local.width || ref.height !== local.height ) {
		throw new Error(
			`${viewport}-${REGION}: dimension mismatch ref ${ref.width}x${ref.height} vs local ${local.width}x${local.height} -- run crop-reference.mjs first`
		);
	}
	const { width, height } = ref;
	const diff = new PNG( { width, height } );

	const mismatch = pixelmatch( ref.data, local.data, diff.data, width, height, { threshold: 0.1 } );
	const percent = ( ( mismatch / ( width * height ) ) * 100 ).toFixed( 2 );

	writeFileSync( `docs/reference-screenshots/local-m4/${viewport}-${REGION}-diff.png`, PNG.sync.write( diff ) );
	results.push( { viewport, region: REGION, width, height, mismatchPixels: mismatch, mismatchPercent: percent } );
}

console.log( JSON.stringify( results, null, 2 ) );
