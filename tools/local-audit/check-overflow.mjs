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
	const { scrollWidth, clientWidth } = await page.evaluate( () => ( {
		scrollWidth: document.documentElement.scrollWidth,
		clientWidth: document.documentElement.clientWidth,
	} ) );
	console.log( `${vp.name} (${vp.width}px viewport): scrollWidth=${scrollWidth}, clientWidth=${clientWidth}, overflow=${scrollWidth > clientWidth ? 'YES +' + ( scrollWidth - clientWidth ) + 'px' : 'no'}` );
	await context.close();
}
await browser.close();
