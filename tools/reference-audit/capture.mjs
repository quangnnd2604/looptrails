import { chromium } from 'playwright';
import { mkdir, writeFile } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { pages, VIEWPORTS } from './pages.mjs';

export const REPO_ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../..');
const OUT_DIR = path.join(REPO_ROOT, 'docs/reference-screenshots');
const MANIFEST_PATH = path.join(REPO_ROOT, 'docs/reference-screenshot-manifest.json');

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
        await tab.addStyleTag({ content: '*, *::before, *::after { animation-duration: 0s !important; animation-delay: 0s !important; transition-duration: 0s !important; transition-delay: 0s !important; }' });
        // Force lazy-loaded images/iframes eager and scroll the full page so
        // scroll-reveal content and loading="lazy" media actually render
        // before the full-page screenshot is taken.
        await tab.evaluate(async () => {
          document.querySelectorAll('img[loading="lazy"], iframe[loading="lazy"]')
            .forEach((el) => el.setAttribute('loading', 'eager'));
          await new Promise((resolve) => {
            let y = 0;
            const step = () => {
              window.scrollTo(0, y);
              y += window.innerHeight;
              if (y < document.body.scrollHeight) setTimeout(step, 150);
              else { window.scrollTo(0, 0); setTimeout(resolve, 500); }
            };
            step();
          });
        });
        await tab.waitForLoadState('networkidle');
      } catch (err) {
        // If goto itself never produced a response, record the error as the
        // status. If goto succeeded but a later step (style injection, the
        // scroll pass, settling) failed, keep the real HTTP status and just
        // warn — a post-load hiccup shouldn't erase a valid 200/404 or abort
        // the rest of the capture run.
        if (status === null) {
          status = `error: ${err.message}`;
        } else {
          console.warn(`Warning: post-load step failed for ${page.url} @ ${vp.name}: ${err.message}`);
        }
      }
      const filePath = path.join(dir, `${vp.name}.png`);
      try {
        await tab.screenshot({ path: filePath, fullPage: true, animations: 'disabled', timeout: 60000 });
      } catch (err) {
        // A single slow/tall page shouldn't crash the whole batch. Retry
        // once (transient), then fall back to a viewport-only screenshot so
        // every manifest entry still gets a real, non-empty file.
        console.warn(`Warning: full-page screenshot failed for ${page.url} @ ${vp.name} (${err.message}); retrying once...`);
        try {
          await tab.screenshot({ path: filePath, fullPage: true, animations: 'disabled', timeout: 60000 });
        } catch (err2) {
          console.warn(`Warning: retry also failed for ${page.url} @ ${vp.name} (${err2.message}); falling back to viewport-only screenshot.`);
          await tab.screenshot({ path: filePath, fullPage: false, animations: 'disabled', timeout: 60000 });
        }
      }
      manifest.push({
        label: page.label,
        url: page.url,
        viewport: vp.name,
        width: vp.width,
        height: vp.height,
        status,
        file: path.relative(REPO_ROOT, filePath).replace(/\\/g, '/'),
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
  await writeFile(MANIFEST_PATH, JSON.stringify(manifest, null, 2) + '\n');
  console.log(`Done. ${manifest.length} screenshots captured. Manifest: ${MANIFEST_PATH}`);
}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
  main();
}
