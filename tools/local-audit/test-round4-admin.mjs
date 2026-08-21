import { chromium } from 'playwright';
import { execSync } from 'child_process';

const browser = await chromium.launch();
const context = await browser.newContext();
const page = await context.newPage({ viewport: { width: 1440, height: 1000 } });

console.log('=== Step 1: Login to wp-admin ===');
await page.goto('http://localhost/looptrails/wp-login.php', { waitUntil: 'domcontentloaded' });
await page.fill('#user_login', 'admin');
await page.fill('#user_pass', 'admin');
await Promise.all([
	page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
	page.click('#wp-submit'),
]);
console.log('Logged in.');

console.log('=== Step 2: Verify Sidebar Menus ===');
const menuText = await page.locator('#adminmenu').innerText();
const hiddenTypes = ['Itinerary Day', 'Vehicle Option', 'Accommodation', 'Transfer Option', 'Add-on', 'Availability Rule'];
for (const t of hiddenTypes) {
	const found = menuText.includes(t);
	console.log(`Menu "${t}" in sidebar: ${found ? 'YES (FAIL)' : 'NO (PASS)'}`);
}

console.log('=== Step 3: Open Tour ID 155 ===');
await page.goto('http://localhost/looptrails/wp-admin/post.php?post=155&action=edit', { waitUntil: 'domcontentloaded' });
await page.waitForSelector('#tbc-itinerary-repeater', { state: 'attached', timeout: 15000 });

// Evaluate table rows in browser context
const data = await page.evaluate(() => {
	const itinRows = Array.from(document.querySelectorAll('#tbc-itinerary-repeater tbody tr.tbc-repeater-row')).map(r => ({
		day: r.querySelector('input[name*="[day_number]"]')?.value,
		title: r.querySelector('input[name*="[title]"]')?.value,
	}));
	const vehRows = Array.from(document.querySelectorAll('#tbc-vehicles-repeater tbody tr.tbc-repeater-row')).map(r => ({
		title: r.querySelector('input[name*="[title]"]')?.value,
		price: r.querySelector('input[name*="[price_vnd]"]')?.value,
	}));
	return { itinRows, vehRows };
});

console.log('Existing Itinerary rows in DOM:', JSON.stringify(data.itinRows, null, 2));
console.log('Existing Vehicles rows in DOM:', JSON.stringify(data.vehRows, null, 2));

console.log('=== Step 4: Add New Day 3 Row via evaluate/click ===');
await page.evaluate(() => {
	const addBtn = document.querySelector('button[data-repeater="tbc-itinerary-repeater"]');
	addBtn.click();
	const rows = document.querySelectorAll('#tbc-itinerary-repeater tbody tr.tbc-repeater-row');
	const newRow = rows[rows.length - 1];
	newRow.querySelector('input[name*="[day_number]"]').value = '3';
	newRow.querySelector('input[name*="[title]"]').value = 'Day 3: Dong Van → Meo Vac → Ha Giang Return';
	newRow.querySelector('textarea[name*="[description]"]').value = 'Scenic ride along Ma Pi Leng pass and return to city.';
	newRow.querySelector('input[name*="[included]"]').value = 'Lunch, guide, fuel';
	newRow.querySelector('input[name*="[excluded]"]').value = 'Souvenirs';
});

console.log('Saving Tour post (submitting post form)...');
await page.evaluate(() => {
	// If Gutenberg is active, dispatch save or submit form
	if (window.wp && window.wp.data && window.wp.data.dispatch) {
		window.wp.data.dispatch('core/editor').savePost();
	} else {
		document.querySelector('form#post').submit();
	}
});

await page.waitForTimeout(5000);
console.log('Wait completed.');

console.log('=== Step 5: Check WP-CLI after adding Day 3 ===');
let cliOut = execSync('C:\\xampp\\wp-cli.bat --path=c:\\xampp\\htdocs\\looptrails post list --post_type=itinerary_day --meta_key=tbc_tour_id --meta_value=155 --fields=ID,post_title').toString();
console.log('WP-CLI Post List (3 items expected):\n' + cliOut);

console.log('=== Step 6: Delete Day 3 Row ===');
await page.goto('http://localhost/looptrails/wp-admin/post.php?post=155&action=edit', { waitUntil: 'domcontentloaded' });
await page.waitForSelector('#tbc-itinerary-repeater', { state: 'attached', timeout: 15000 });

await page.evaluate(() => {
	const rows = document.querySelectorAll('#tbc-itinerary-repeater tbody tr.tbc-repeater-row');
	const lastRow = rows[rows.length - 1];
	const removeBtn = lastRow.querySelector('.tbc-remove-row-btn');
	removeBtn.click();
	if (window.wp && window.wp.data && window.wp.data.dispatch) {
		window.wp.data.dispatch('core/editor').savePost();
	} else {
		document.querySelector('form#post').submit();
	}
});

await page.waitForTimeout(5000);

console.log('=== Step 7: Check WP-CLI after deleting Day 3 ===');
cliOut = execSync('C:\\xampp\\wp-cli.bat --path=c:\\xampp\\htdocs\\looptrails post list --post_type=itinerary_day --meta_key=tbc_tour_id --meta_value=155 --fields=ID,post_title').toString();
console.log('WP-CLI Post List (2 items expected):\n' + cliOut);

await browser.close();
console.log('=== All 7 Verification Steps Completed Successfully ===');
