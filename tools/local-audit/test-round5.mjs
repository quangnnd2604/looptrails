import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

const outDir = path.resolve('docs/reference-screenshots/round5');
if (!fs.existsSync(outDir)) {
	fs.mkdirSync(outDir, { recursive: true });
}

const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 1440, height: 1000 } });

console.log('=== Step 1: Login to wp-admin ===');
await page.goto('http://localhost/looptrails/wp-login.php');
await page.fill('#user_login', 'admin');
await page.fill('#user_pass', 'admin');
await page.click('#wp-submit');
await page.waitForURL('**/wp-admin/**');
console.log('Logged in successfully. URL:', page.url());

console.log('=== Step 2: Open post-new.php?post_type=tour ===');
await page.goto('http://localhost/looptrails/wp-admin/post-new.php?post_type=tour');
await page.waitForSelector('#title', { timeout: 10000 });

// Check Classic Editor presence
const hasClassicTitle = await page.locator('#title').count() > 0;
const hasClassicEditorDiv = await page.locator('#postdivrich').count() > 0;
const hasGutenberg = await page.locator('.block-editor-page').count() > 0;
console.log(`Tour Editor: Classic Title input (#title)=${hasClassicTitle}, Classic Editor Container (#postdivrich)=${hasClassicEditorDiv}, Gutenberg Block Editor=${hasGutenberg}`);

// Check 6 Meta Boxes visibility immediately on page
const metaboxes = [
	'tbc_itinerary_metabox',
	'tbc_vehicles_metabox',
	'tbc_accommodation_metabox',
	'tbc_transfer_metabox',
	'tbc_addons_metabox',
	'tbc_availability_metabox',
];

for (const mb of metaboxes) {
	const el = page.locator('#' + mb);
	const exists = await el.count() > 0;
	const visible = exists ? await el.isVisible() : false;
	console.log(`  Metabox #${mb}: exists=${exists}, visible=${visible}`);
}

await page.screenshot({ path: path.join(outDir, 'tour-new-classic-editor.png'), fullPage: true });
console.log('Saved screenshot: tour-new-classic-editor.png');

console.log('=== Step 3: Open post.php?post=155&action=edit (Northern Highlands Loop) ===');
await page.goto('http://localhost/looptrails/wp-admin/post.php?post=155&action=edit');
await page.waitForSelector('#tbc-itinerary-repeater', { timeout: 10000 });

const itinRows = await page.locator('#tbc-itinerary-repeater tbody tr.tbc-repeater-row').count();
console.log('Itinerary rows loaded in tour 155:', itinRows);
for (let i = 0; i < itinRows; i++) {
	const row = page.locator('#tbc-itinerary-repeater tbody tr.tbc-repeater-row').nth(i);
	const dayNum = await row.locator('input[name*="[day_number]"]').inputValue();
	const title = await row.locator('input[name*="[title]"]').inputValue();
	console.log(`  Row ${i + 1}: Day ${dayNum} - ${title}`);
}

const vehRows = await page.locator('#tbc-vehicles-repeater tbody tr.tbc-repeater-row').count();
console.log('Vehicles rows loaded in tour 155:', vehRows);
for (let i = 0; i < vehRows; i++) {
	const row = page.locator('#tbc-vehicles-repeater tbody tr.tbc-repeater-row').nth(i);
	const title = await row.locator('input[name*="[title]"]').inputValue();
	const price = await row.locator('input[name*="[price_vnd]"]').inputValue();
	console.log(`  Vehicle ${i + 1}: ${title} - ${price} VND`);
}

await page.screenshot({ path: path.join(outDir, 'tour-edit-155-classic.png'), fullPage: true });
console.log('Saved screenshot: tour-edit-155-classic.png');

console.log('=== Step 4: Open post-new.php?post_type=page (Verify Block Editor still used for Page) ===');
await page.goto('http://localhost/looptrails/wp-admin/post-new.php?post_type=page');
await page.waitForSelector('.edit-post-layout, .block-editor-page, #editor, body.block-editor-page', { timeout: 10000 });
const pageIsGutenberg = await page.locator('.block-editor-page, .edit-post-layout, #editor').count() > 0;
console.log('Page Editor: Gutenberg Block Editor active =', pageIsGutenberg);

await page.screenshot({ path: path.join(outDir, 'page-new-block-editor.png') });
console.log('Saved screenshot: page-new-block-editor.png');

await browser.close();
console.log('=== Round 5 Verification Completed Successfully ===');
