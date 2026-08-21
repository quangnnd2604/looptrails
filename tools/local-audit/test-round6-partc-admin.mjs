import { chromium } from 'playwright';
import path from 'path';

const outDir = 'c:/xampp/htdocs/looptrails/docs/reference-screenshots';

const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 1440, height: 1000 } });

console.log('=== Step 1: Login to wp-admin ===');
await page.goto('http://localhost/looptrails/wp-login.php');
await page.fill('#user_login', 'admin');
await page.fill('#user_pass', 'admin');
await page.click('#wp-submit');
await page.waitForURL('**/wp-admin/**');
console.log('Logged in successfully to wp-admin.');

console.log('=== Step 2: Edit About Page (ID 300) in Gutenberg Editor ===');
await page.goto('http://localhost/looptrails/wp-admin/post.php?post=300&action=edit');
await page.waitForSelector('.edit-post-layout, .block-editor-page', { timeout: 15000 });
await page.waitForTimeout(3000);

// Close welcome guide modal if open
const closeGuideBtn = page.locator('button[aria-label="Đóng hộp thoại"], button[aria-label="Close dialog"], .components-modal__header button');
if (await closeGuideBtn.count() > 0) {
	await closeGuideBtn.first().click();
	await page.waitForTimeout(500);
}

const iframeCount = await page.locator('iframe[name="editor-canvas"]').count();
let canvas = page;
if (iframeCount > 0) {
	canvas = page.frameLocator('iframe[name="editor-canvas"]');
	console.log('Using iframed editor canvas for About');
} else {
	console.log('Using top-level editor canvas for About');
}

// Click on the paragraph or heading in post_content
const aboutBlock = canvas.locator('p, h1, h2').nth(1);
await aboutBlock.waitFor({ timeout: 10000 });
await aboutBlock.click();
await page.keyboard.press('End');
await page.keyboard.type(' [ABOUT LIVE EDIT VERIFIED]');
await page.waitForTimeout(1000);

// Screenshot the admin editor with modified text
await page.screenshot({ path: path.join(outDir, 'round6-about-admin-edit.png') });
console.log('Saved round6-about-admin-edit.png');

// Click Update button
const updateBtn = page.locator('button.editor-post-publish-button, button.editor-post-publish-panel__toggle');
if (await updateBtn.count() > 0 && !await updateBtn.first().isDisabled()) {
	await updateBtn.first().click();
} else {
	await page.evaluate(async () => {
		await wp.data.dispatch('core/editor').savePost();
	});
}
await page.waitForTimeout(4000);
console.log('Updated About page in wp-admin.');

console.log('=== Step 3: Verify About page on Frontend ===');
const frontendPage = await browser.newPage({ viewport: { width: 1440, height: 1000 } });
await frontendPage.goto('http://localhost/looptrails/about/', { waitUntil: 'networkidle' });
const aboutHtml = await frontendPage.content();
const hasAboutEdit = aboutHtml.includes('[ABOUT LIVE EDIT VERIFIED]');
console.log('About frontend contains updated text:', hasAboutEdit);

await frontendPage.screenshot({ path: path.join(outDir, 'round6-about-frontend-updated.png'), fullPage: true });
console.log('Saved round6-about-frontend-updated.png');


console.log('=== Step 4: Edit Home Page (ID 315) in Gutenberg Editor ===');
await page.goto('http://localhost/looptrails/wp-admin/post.php?post=315&action=edit');
await page.waitForSelector('.edit-post-layout, .block-editor-page', { timeout: 15000 });
await page.waitForTimeout(3000);

// Close welcome guide modal if open
if (await closeGuideBtn.count() > 0) {
	await closeGuideBtn.first().click();
	await page.waitForTimeout(500);
}

const homeIframeCount = await page.locator('iframe[name="editor-canvas"]').count();
let homeCanvas = page;
if (homeIframeCount > 0) {
	homeCanvas = page.frameLocator('iframe[name="editor-canvas"]');
	console.log('Using iframed editor canvas for Home');
} else {
	console.log('Using top-level editor canvas for Home');
}

// Find the hero headline or heading in the home editor to modify
const homeHeading = homeCanvas.locator('[data-type="core/heading"], h1.hero-headline, h1, h2').first();
await homeHeading.scrollIntoViewIfNeeded();
await homeHeading.click();
await page.keyboard.press('End');
await page.keyboard.type(' [HOMEPAGE LIVE EDIT VERIFIED]');
await page.waitForTimeout(1000);

// Screenshot the admin editor with modified home page
await page.screenshot({ path: path.join(outDir, 'round6-home-admin-edit.png') });
console.log('Saved round6-home-admin-edit.png');

// Save post via Gutenberg API
await page.evaluate(async () => {
	await wp.data.dispatch('core/editor').savePost();
});
await page.waitForTimeout(4000);
console.log('Updated Home page in wp-admin.');

console.log('=== Step 5: Verify Home page on Frontend ===');
await frontendPage.goto('http://localhost/looptrails/', { waitUntil: 'networkidle' });
const homeHtml = await frontendPage.content();
const hasHomeEdit = homeHtml.includes('[HOMEPAGE LIVE EDIT VERIFIED]');
console.log('Home frontend contains updated text:', hasHomeEdit);

await frontendPage.screenshot({ path: path.join(outDir, 'round6-home-frontend-updated.png'), fullPage: true });
console.log('Saved round6-home-frontend-updated.png');

await browser.close();
console.log('=== Part C Admin Editing Live Verification Completed Successfully ===');
