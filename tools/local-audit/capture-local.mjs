import { chromium } from 'playwright';
import { mkdirSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname( fileURLToPath( import.meta.url ) );
const PROJECT_ROOT = path.resolve( __dirname, '../..' );
const LOCAL_BASE_URL = 'http://localhost/looptrails';
const OUT_DIR = path.join( PROJECT_ROOT, 'docs/reference-screenshots/local-m4' );

const VIEWPORTS = [
	{ name: 'desktop', width: 1440, height: 1000 },
	{ name: 'laptop', width: 1280, height: 800 },
	{ name: 'tablet', width: 768, height: 1024 },
	{ name: 'mobile', width: 390, height: 844 },
	{ name: 'narrow-mobile', width: 360, height: 800 },
];

mkdirSync( OUT_DIR, { recursive: true } );

const browser = await chromium.launch();
for ( const viewport of VIEWPORTS ) {
	const context = await browser.newContext( { viewport: { width: viewport.width, height: viewport.height } } );
	const page = await context.newPage();
	await page.addStyleTag( { content: '*, *::before, *::after { animation: none !important; transition: none !important; }' } );
	await page.goto( LOCAL_BASE_URL, { waitUntil: 'networkidle' } );
	await page.screenshot( { path: path.join( OUT_DIR, `${viewport.name}-header.png` ), clip: { x: 0, y: 0, width: viewport.width, height: 200 } } );
	await page.evaluate( () => window.scrollTo( 0, document.body.scrollHeight ) );
	await page.waitForTimeout( 300 );
	await page.screenshot( { path: path.join( OUT_DIR, `${viewport.name}-footer.png` ) } );
	await context.close();
}
await browser.close();
console.log( 'Local capture complete:', OUT_DIR );
