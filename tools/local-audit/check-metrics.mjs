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

		// line-height computes to the keyword "normal" here (never explicitly
		// set), and per the CSS Values spec that keyword is the *computed*
		// value -- getComputedStyle never resolves it to a px number. To get
		// the actual used/rendered line-box height, probe it directly: render
		// the same font/size/weight in an isolated span and measure its
		// rendered height.
		const probe = document.createElement( 'span' );
		probe.style.fontFamily = cs.fontFamily;
		probe.style.fontSize = cs.fontSize;
		probe.style.fontWeight = cs.fontWeight;
		probe.style.lineHeight = 'normal';
		probe.style.position = 'absolute';
		probe.style.visibility = 'hidden';
		probe.style.whiteSpace = 'nowrap';
		probe.textContent = 'Book Now';
		document.body.appendChild( probe );
		const resolvedLineHeightPx = probe.getBoundingClientRect().height;
		probe.remove();

		return {
			fontSize: cs.fontSize,
			lineHeight: cs.lineHeight,
			resolvedLineHeightPx,
			fontWeight: cs.fontWeight,
			height: rect.height,
			borderRadius: cs.borderRadius,
		};
	} );
	console.log( vp.name, JSON.stringify( metrics ) );
	await context.close();
}
await browser.close();
