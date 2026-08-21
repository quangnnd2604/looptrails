import { chromium } from 'playwright';

const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 1440, height: 1000 } });

console.log('=== Step 1: Open Motorbike Rental Page ===');
await page.goto('http://localhost/looptrails/motorbike-rental/', { waitUntil: 'networkidle' });

console.log('=== Step 2: Click Rent This Bike on Honda XR 150L ===');
const xrBtn = page.locator('a[data-bike="xr150l"]');
await xrBtn.click();
await page.waitForTimeout(500);

const selectedBike = await page.locator('#rental_bike').inputValue();
console.log('Selected bike in dropdown after click:', selectedBike);

const costText = await page.locator('#rental-cost-display').innerText();
console.log('Initial estimated total for 3 days:', costText);

console.log('=== Step 3: Fill Rental Booking Form ===');
await page.fill('#rental_start_date', '2026-09-15');
await page.fill('#rental_days', '4');
await page.waitForTimeout(300);

const updatedCostText = await page.locator('#rental-cost-display').innerText();
console.log('Updated estimated total for 4 days ($22 * 4):', updatedCostText);

await page.fill('#rental_customer_name', 'Alex Motorider');
await page.fill('#rental_customer_email', 'alex.motorider@example.com');
await page.fill('#rental_customer_phone', '+84988776655');

console.log('=== Step 4: Submit Reservation ===');
await page.click('#rental-submit-btn');
await page.waitForSelector('#rental-form-feedback:visible', { timeout: 10000 });

const feedbackText = await page.locator('#rental-form-feedback').innerText();
console.log('Submission feedback:', feedbackText);

await page.screenshot({ path: 'docs/reference-screenshots/round6-motorbike-rental-submission.png', fullPage: true });
console.log('Saved round6-motorbike-rental-submission.png');

await browser.close();
console.log('=== Part B Verification Complete ===');
