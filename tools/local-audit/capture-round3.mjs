import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

const outDir = path.resolve('docs/reference-screenshots/round3');
if (!fs.existsSync(outDir)) {
	fs.mkdirSync(outDir, { recursive: true });
}

const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 1440, height: 1000 } });

const pagesToCapture = [
	{ url: 'http://localhost/looptrails/', file: 'home-desktop.png' },
	{ url: 'http://localhost/looptrails/tours/northern-highlands-loop/', file: 'single-tour-desktop.png' },
	{ url: 'http://localhost/looptrails/motorbike-rental/', file: 'motorbike-rental-desktop.png' },
];

for (const p of pagesToCapture) {
	await page.goto(p.url, { waitUntil: 'networkidle' });
	await page.screenshot({ path: path.join(outDir, p.file), fullPage: true });
	console.log(`Captured: ${p.file} from ${p.url}`);
}

await browser.close();
console.log('Round 3 visual captures completed successfully.');
