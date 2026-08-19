import { chromium } from 'playwright';
import { measurePage } from './run-audit.mjs';

const browser = await chromium.launch();
const target = { label: 'Home (smoke test)', slug: 'home', url: 'https://looptrails.com/' };
const viewport = { name: 'desktop', width: 1440, height: 1000 };
const result = await measurePage(browser, target, viewport);
await browser.close();

if (result.typography.length === 0) throw new Error('No typography styles detected — probe likely broken');
if (result.colors.length === 0) throw new Error('No colors detected — probe likely broken');
if (!result.container.mostCommonContainerWidth) throw new Error('No container width detected — probe likely broken');

console.log(
  `Smoke test passed: ${result.typography.length} typography styles, ${result.colors.length} colors, ` +
  `container width ${result.container.mostCommonContainerWidth}px, ${result.cards.length} card-grid groups, ` +
  `${result.buttons.length} button styles detected.`
);
