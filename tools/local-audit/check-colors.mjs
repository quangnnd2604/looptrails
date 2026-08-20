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

console.log( '\nChannel-diff vs. reference (max abs diff across R/G/B, tolerance <=5):' );
for ( const [ label, refKey ] of [
	[ 'header/footer surface', 'header/footer surface' ],
	[ 'Book Now text (primary)', 'Book Now text (primary)' ],
	[ 'Book Now background', 'Book Now background' ],
	[ 'social-facebook (circle bg)', 'social-facebook' ],
	[ 'social-facebook (icon fill)', 'social-facebook' ],
	[ 'social-instagram (circle bg)', 'social-instagram' ],
	[ 'social-instagram (icon fill)', 'social-instagram' ],
	[ 'social-whatsapp (circle bg)', 'social-whatsapp' ],
	[ 'social-whatsapp (icon fill)', 'social-whatsapp' ],
	[ 'social-tiktok (circle bg)', 'social-tiktok' ],
	[ 'social-tiktok (icon fill)', 'social-tiktok' ],
] ) {
	const renderedRgb = parseRgb( rendered[ label ] );
	const refRgb = REFERENCE[ refKey ].rgb;
	if ( !renderedRgb ) {
		console.log( `${label}: COULD NOT PARSE (${rendered[ label ]})` );
		continue;
	}
	const diff = maxChannelDiff( renderedRgb, refRgb );
	console.log( `${label}: rendered rgb(${renderedRgb.join( ',' )}) vs reference ${REFERENCE[ refKey ].hex} rgb(${refRgb.join( ',' )}) -> max channel diff = ${diff} -> ${diff <= 5 ? 'PASS' : 'FAIL'}` );
}

await browser.close();
