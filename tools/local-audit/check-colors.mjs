import { chromium } from 'playwright';

// Reference values as measured/documented in Milestone 1/2a
// (docs/design-tokens.md, docs/reference-audit/00-global.md, docs/reference-audit/02-tour-detail.md)
// and encoded verbatim into theme.json's color palette.
const REFERENCE = {
	'header/footer surface': { hex: '#e4e0da', rgb: [ 228, 224, 218 ] },
	'Book Now text (primary)': { hex: '#ff6602', rgb: [ 255, 102, 2 ] },
	'Book Now background': { hex: '#ffffff', rgb: [ 255, 255, 255 ] },
	'social-facebook': { hex: '#1877f2', rgb: [ 24, 119, 242 ] },
	'social-instagram': { hex: '#e1306c', rgb: [ 225, 48, 108 ] },
	'social-whatsapp': { hex: '#25d366', rgb: [ 37, 211, 102 ] },
	'social-tiktok': { hex: '#000000', rgb: [ 0, 0, 0 ] },
};

function parseRgb( str ) {
	const m = str.match( /rgba?\(([^)]+)\)/ );
	if ( !m ) return null;
	return m[ 1 ].split( ',' ).map( ( s ) => parseFloat( s.trim() ) ).slice( 0, 3 );
}

function maxChannelDiff( a, b ) {
	return Math.max( ...a.map( ( v, i ) => Math.abs( v - b[ i ] ) ) );
}

const browser = await chromium.launch();
const context = await browser.newContext( { viewport: { width: 1440, height: 1000 } } );
const page = await context.newPage();
await page.goto( 'http://localhost/looptrails', { waitUntil: 'networkidle' } );

const rendered = await page.evaluate( () => {
	function cs( sel, prop ) {
		const el = document.querySelector( sel );
		return el ? getComputedStyle( el )[ prop ] : null;
	}
	return {
		'header/footer surface': cs( 'header.wp-block-template-part .wp-block-group', 'backgroundColor' ),
		'Book Now text (primary)': cs( '.wp-block-button.is-style-book-now .wp-block-button__link', 'color' ),
		'Book Now background': cs( '.wp-block-button.is-style-book-now .wp-block-button__link', 'backgroundColor' ),
		'social-facebook (circle bg)': cs( '.wp-social-link-facebook', 'backgroundColor' ),
		'social-facebook (icon fill)': cs( '.wp-social-link-facebook path', 'fill' ),
		'social-instagram (circle bg)': cs( '.wp-social-link-instagram', 'backgroundColor' ),
		'social-instagram (icon fill)': cs( '.wp-social-link-instagram path', 'fill' ),
		'social-whatsapp (circle bg)': cs( '.wp-social-link-whatsapp', 'backgroundColor' ),
		'social-whatsapp (icon fill)': cs( '.wp-social-link-whatsapp path', 'fill' ),
		'social-tiktok (circle bg)': cs( '.wp-social-link-tiktok', 'backgroundColor' ),
		'social-tiktok (icon fill)': cs( '.wp-social-link-tiktok path', 'fill' ),
	};
} );

console.log( 'Rendered (raw computed style):' );
console.log( JSON.stringify( rendered, null, 2 ) );

// Expectation note (final-review I10). The reference footer renders its social
// icons in WordPress core's native `is-style-logos-only` shape: a bare,
// network-coloured glyph on the cream footer surface, with NO circular
// background behind it (see docs/reference-audit/00-global.md and the
// desktop-footer-ref.png crop). An earlier "make every icon its own colour"
// fix set `background-color` per network, which both invented a circle the
// reference does not have and left core's own hardcoded glyph fill sitting on
// top of it. The fix swapped that to `color`, which core's SVG rules inherit
// via `currentColor`. The expectations below encode that corrected intent:
//   - circle background: must be fully transparent (no circle at all)
//   - icon glyph fill:   must equal the measured network token
const TRANSPARENT = 'transparent (no circle -- reference renders bare icons)';

console.log( '\nChannel-diff vs. reference (max abs diff across R/G/B, tolerance <=5):' );
for ( const [ label, refKey ] of [
	[ 'header/footer surface', 'header/footer surface' ],
	[ 'Book Now text (primary)', 'Book Now text (primary)' ],
	[ 'Book Now background', 'Book Now background' ],
	[ 'social-facebook (circle bg)', TRANSPARENT ],
	[ 'social-facebook (icon fill)', 'social-facebook' ],
	[ 'social-instagram (circle bg)', TRANSPARENT ],
	[ 'social-instagram (icon fill)', 'social-instagram' ],
	[ 'social-whatsapp (circle bg)', TRANSPARENT ],
	[ 'social-whatsapp (icon fill)', 'social-whatsapp' ],
	[ 'social-tiktok (circle bg)', TRANSPARENT ],
	[ 'social-tiktok (icon fill)', 'social-tiktok' ],
] ) {
	const raw = rendered[ label ];

	if ( refKey === TRANSPARENT ) {
		// `rgba(0, 0, 0, 0)` is how getComputedStyle reports "no background".
		const alpha = raw && raw.startsWith( 'rgba(' ) ? parseFloat( raw.split( ',' )[ 3 ] ) : 1;
		const pass = alpha === 0;
		console.log( `${label}: rendered ${raw} vs reference ${TRANSPARENT} -> ${pass ? 'PASS' : 'FAIL'}` );
		continue;
	}

	const renderedRgb = parseRgb( raw );
	const refRgb = REFERENCE[ refKey ].rgb;
	if ( !renderedRgb ) {
		console.log( `${label}: COULD NOT PARSE (${raw})` );
		continue;
	}
	const diff = maxChannelDiff( renderedRgb, refRgb );
	console.log( `${label}: rendered rgb(${renderedRgb.join( ',' )}) vs reference ${REFERENCE[ refKey ].hex} rgb(${refRgb.join( ',' )}) -> max channel diff = ${diff} -> ${diff <= 5 ? 'PASS' : 'FAIL'}` );
}

await browser.close();
