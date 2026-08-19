import { chromium } from 'playwright';
import { mkdir, writeFile } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { pages, VIEWPORTS } from './pages.mjs';

const OUT_DIR = path.resolve('../../docs/reference-screenshots');
const MANIFEST_PATH = path.resolve('../../docs/reference-screenshot-manifest.json');

function slugify(url) {
  const p = new URL(url).pathname.replace(/^\/|\/$/g, '').replace(/\//g, '-');
  return p || 'home';
}

export async function captureAll(pageList) {
  const browser = await chromium.launch();
  const manifest = [];
  for (const page of pageList) {
    const slug = slugify(page.url);
    const dir = path.join(OUT_DIR, slug);
    await mkdir(dir, { recursive: true });
    for (const vp of VIEWPORTS) {
      const context = await browser.newContext({
        viewport: { width: vp.width, height: vp.height },
        reducedMotion: 'reduce',
      });
      const tab = await context.newPage();
      let status = null;
      try {
        const response = await tab.goto(page.url, { waitUntil: 'networkidle', timeout: 30000 });
        status = response ? response.status() : null;
      } catch (err) {
        status = `error: ${err.message}`;
      }
      await tab.addStyleTag({ content: '*, *::before, *::after { animation-duration: 0s !important; animation-delay: 0s !important; transition-duration: 0s !important; transition-delay: 0s !important; }' });
      const filePath = path.join(dir, `${vp.name}.png`);
      await tab.screenshot({ path: filePath, fullPage: true });
      manifest.push({
        label: page.label,
        url: page.url,
        viewport: vp.name,
        width: vp.width,
        height: vp.height,
        status,
        file: path.relative(path.resolve('../../'), filePath).replace(/\\/g, '/'),
        capturedAt: new Date().toISOString(),
      });
      await context.close();
      console.log(`Captured ${slug} @ ${vp.name} (status ${status})`);
    }
  }
  await browser.close();
  return manifest;
}

async function main() {
  const manifest = await captureAll(pages);
  await writeFile(MANIFEST_PATH, JSON.stringify(manifest, null, 2));
  console.log(`Done. ${manifest.length} screenshots captured. Manifest: ${MANIFEST_PATH}`);
}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
  main();
}
