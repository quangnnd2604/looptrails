import { chromium } from 'playwright';
import { mkdirSync, writeFileSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname( fileURLToPath( import.meta.url ) );
const PROJECT_ROOT = path.resolve( __dirname, '../..' );
const LOCAL_BASE_URL = 'http://localhost/looptrails';
const OUT_DIR = path.join( PROJECT_ROOT, 'docs/reference-screenshots/local-m5' );

const VIEWPORTS = [
	{ name: 'desktop', width: 1440, height: 1000 },
	{ name: 'laptop', width: 1280, height: 800 },
	{ name: 'tablet', width: 768, height: 1024 },
	{ name: 'mobile', width: 390, height: 844 },
	{ name: 'narrow-mobile', width: 360, height: 800 },
];

mkdirSync( OUT_DIR, { recursive: true } );

const regions = {};

const browser = await chromium.launch();
for ( const viewport of VIEWPORTS ) {
	const context = await browser.newContext( { viewport: { width: viewport.width, height: viewport.height } } );
	const page = await context.newPage();
	await page.goto( LOCAL_BASE_URL, { waitUntil: 'networkidle' } );
	await page.addStyleTag( { content: '*, *::before, *::after { animation: none !important; transition: none !important; }' } );

	await page.screenshot( { path: path.join( OUT_DIR, `${viewport.name}-full.png` ), fullPage: true } );

	const headerLocator = page.locator( 'header.wp-block-template-part' );
	const footerLocator = page.locator( 'footer.wp-block-template-part' );

	if ( await headerLocator.count() > 0 ) {
		await headerLocator.first().screenshot( { path: path.join( OUT_DIR, `${viewport.name}-header.png` ) } );
	}
	if ( await footerLocator.count() > 0 ) {
		await footerLocator.first().screenshot( { path: path.join( OUT_DIR, `${viewport.name}-footer.png` ) } );
	}

	const geometry = await page.evaluate( () => {
		const box = ( el ) => {
			if ( ! el ) return null;
			const r = el.getBoundingClientRect();
			return {
				x: Math.round( r.x + window.scrollX ),
				y: Math.round( r.y + window.scrollY ),
				width: Math.round( r.width ),
				height: Math.round( r.height ),
			};
		};
		const header = document.querySelector( 'header.wp-block-template-part' );
		const footer = document.querySelector( 'footer.wp-block-template-part' );

		return {
			pageHeight: Math.round( document.documentElement.scrollHeight ),
			headerBox: box( header ),
			footerBox: box( footer ),
		};
	} );

	regions[ viewport.name ] = {
		width: viewport.width,
		height: viewport.height,
		...geometry,
	};

	await context.close();
}
await browser.close();

writeFileSync( path.join( OUT_DIR, 'regions.json' ), JSON.stringify( regions, null, 2 ) );
console.log( 'Local capture complete:', OUT_DIR );
