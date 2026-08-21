import { PNG } from 'pngjs';
import pixelmatch from 'pixelmatch';
import { readFileSync, writeFileSync, mkdirSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname( fileURLToPath( import.meta.url ) );
const PROJECT_ROOT = path.resolve( __dirname, '../..' );

const VIEWPORTS = [ 'desktop', 'laptop', 'tablet', 'mobile', 'narrow-mobile' ];
const REGION = process.env.REGION || 'header';

const results = [];
for ( const viewport of VIEWPORTS ) {
	const refPath = path.join( PROJECT_ROOT, `docs/reference-screenshots/home/${viewport}.png` );
	const localPath = path.join( PROJECT_ROOT, `docs/reference-screenshots/local-m4/${viewport}-${REGION}.png` );

	try {
		const ref = PNG.sync.read( readFileSync( refPath ) );
		const local = PNG.sync.read( readFileSync( localPath ) );

		const width = Math.min( ref.width, local.width );
		const height = Math.min( ref.height, local.height );

		// Create cropped reference PNG to match local crop region
		const croppedRef = new PNG( { width, height } );
		let startY = 0;
		if ( REGION === 'footer' ) {
			startY = Math.max( 0, ref.height - height );
		}

		PNG.bitblt( ref, croppedRef, 0, startY, width, height, 0, 0 );

		const diff = new PNG( { width, height } );
		const mismatch = pixelmatch( croppedRef.data, local.data, diff.data, width, height, { threshold: 0.1 } );
		const percent = ( ( mismatch / ( width * height ) ) * 100 ).toFixed( 2 );

		writeFileSync( path.join( PROJECT_ROOT, `docs/reference-screenshots/local-m4/${viewport}-${REGION}-diff.png` ), PNG.sync.write( diff ) );
		results.push( { viewport, region: REGION, width, height, mismatchPixels: mismatch, mismatchPercent: percent } );
	} catch ( err ) {
		results.push( { viewport, region: REGION, error: err.message } );
	}
}

console.log( JSON.stringify( results, null, 2 ) );
