import { chromium } from 'playwright';

const VIEWPORTS = [
	{ name: 'desktop', width: 1440, height: 1000 },
	{ name: 'laptop', width: 1280, height: 800 },
	{ name: 'tablet', width: 768, height: 1024 },
	{ name: 'mobile', width: 390, height: 844 },
	{ name: 'narrow-mobile', width: 360, height: 800 },
];

const browser = await chromium.launch();
for ( const vp of VIEWPORTS ) {
	const context = await browser.newContext( { viewport: { width: vp.width, height: vp.height } } );
	const page = await context.newPage();
	await page.goto( 'http://localhost/looptrails', { waitUntil: 'networkidle' } );
	const metrics = await page.evaluate( () => {
		const btn = document.querySelector( '.wp-block-button.is-style-book-now .wp-block-button__link' );
		const cs = getComputedStyle( btn );
		const rect = btn.getBoundingClientRect();
		return {
			fontSize: cs.fontSize,
			lineHeight: cs.lineHeight,
			fontWeight: cs.fontWeight,
			height: rect.height,
			borderRadius: cs.borderRadius,
		};
	} );
	console.log( vp.name, JSON.stringify( metrics ) );
	await context.close();
}
await browser.close();
