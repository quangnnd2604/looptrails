import { chromium } from 'playwright';
import { mkdir, writeFile, readFile } from 'node:fs/promises';
import { existsSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { templates, tourDetailVariationSamples, VIEWPORTS } from './pages.mjs';
import { scanTypography } from './probes/typography.mjs';
import { scanColors } from './probes/colors.mjs';
import { scanContainer } from './probes/container.mjs';
import { scanCardGrids } from './probes/cards.mjs';
import { scanButtons } from './probes/buttons.mjs';
import { scanTourVariation } from './variation-probe.mjs';

export const REPO_ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../..');
const OUT_DIR = path.join(REPO_ROOT, 'docs/design-measurements');

async function withTimeout(promise, ms, label) {
  let timer;
  const timeout = new Promise((_, reject) => {
    timer = setTimeout(() => reject(new Error(`Timeout after ${ms}ms: ${label}`)), ms);
  });
  try {
    return await Promise.race([promise, timeout]);
  } finally {
    clearTimeout(timer);
  }
}

export async function scrollAndSettle(page) {
  await page.evaluate(async () => {
    document.querySelectorAll('img[loading="lazy"], iframe[loading="lazy"]')
      .forEach((el) => el.setAttribute('loading', 'eager'));
    await new Promise((resolve) => {
      let y = 0;
      let steps = 0;
      const MAX_STEPS = 40;
      const step = () => {
        window.scrollTo(0, y);
        y += window.innerHeight;
        steps += 1;
        if (y < document.body.scrollHeight && steps < MAX_STEPS) setTimeout(step, 150);
        else { window.scrollTo(0, 0); setTimeout(resolve, 500); }
      };
      step();
    });
  });
  await page.waitForLoadState('networkidle');
}

export async function measurePage(browser, target, viewport) {
  const context = await browser.newContext({ viewport: { width: viewport.width, height: viewport.height } });
  const page = await context.newPage();
  await page.goto(target.url, { waitUntil: 'networkidle', timeout: 30000 });

  await scrollAndSettle(page);

  const [typography, colors, container, cards, buttons] = await Promise.all([
    scanTypography(page),
    scanColors(page),
    scanContainer(page),
    scanCardGrids(page),
    scanButtons(page),
  ]);

  await context.close();
  return { typography, colors, container, cards, buttons, capturedAt: new Date().toISOString() };
}

export async function runFullAudit() {
  let browser = await chromium.launch();
  const summary = [];

  for (const target of templates) {
    const dir = path.join(OUT_DIR, target.slug);
    await mkdir(dir, { recursive: true });
    for (const viewport of VIEWPORTS) {
      const filePath = path.join(dir, `${viewport.name}.json`);

      if (!process.env.FORCE && existsSync(filePath)) {
        console.log(`Skipping ${target.slug} @ ${viewport.name}, already measured`);
        const existing = JSON.parse(await readFile(filePath, 'utf8'));
        summary.push({
          label: target.label,
          slug: target.slug,
          viewport: viewport.name,
          file: path.relative(REPO_ROOT, filePath).replace(/\\/g, '/'),
          capturedAt: existing.capturedAt,
          skipped: true,
        });
        continue;
      }

      try {
        if (!browser.isConnected()) {
          console.warn('Browser disconnected — relaunching before continuing');
          browser = await chromium.launch();
        }
        const data = await withTimeout(measurePage(browser, target, viewport), 45000, `${target.slug}@${viewport.name}`);
        await writeFile(filePath, JSON.stringify(data, null, 2) + '\n');
        summary.push({
          label: target.label,
          slug: target.slug,
          viewport: viewport.name,
          file: path.relative(REPO_ROOT, filePath).replace(/\\/g, '/'),
        });
        console.log(`Measured ${target.slug} @ ${viewport.name}`);
      } catch (err) {
        console.warn(`FAILED ${target.slug} @ ${viewport.name}: ${err.message}`);
        summary.push({
          label: target.label,
          slug: target.slug,
          viewport: viewport.name,
          file: path.relative(REPO_ROOT, filePath).replace(/\\/g, '/'),
          error: err.message,
        });
        continue;
      }
    }
  }

  const variationDir = path.join(OUT_DIR, 'tour-detail-variation');
  await mkdir(variationDir, { recursive: true });
  for (const sample of tourDetailVariationSamples) {
    const filePath = path.join(variationDir, `${sample.slug}.json`);

    if (!process.env.FORCE && existsSync(filePath)) {
      console.log(`Skipping tour-detail variation: ${sample.slug}, already measured`);
      summary.push({
        label: sample.label,
        slug: sample.slug,
        viewport: 'desktop-only',
        file: path.relative(REPO_ROOT, filePath).replace(/\\/g, '/'),
        skipped: true,
      });
      continue;
    }

    try {
      if (!browser.isConnected()) {
        console.warn('Browser disconnected — relaunching before continuing');
        browser = await chromium.launch();
      }
      const variation = await withTimeout((async () => {
        const context = await browser.newContext({ viewport: { width: 1440, height: 1000 } });
        const page = await context.newPage();
        await page.goto(sample.url, { waitUntil: 'networkidle', timeout: 30000 });
        await scrollAndSettle(page);
        const result = await scanTourVariation(page);
        await context.close();
        return result;
      })(), 45000, `variation:${sample.slug}`);
      await writeFile(filePath, JSON.stringify({ ...sample, ...variation }, null, 2) + '\n');
      summary.push({
        label: sample.label,
        slug: sample.slug,
        viewport: 'desktop-only',
        file: path.relative(REPO_ROOT, filePath).replace(/\\/g, '/'),
      });
      console.log(`Measured tour-detail variation: ${sample.slug}`);
    } catch (err) {
      console.warn(`FAILED tour-detail variation: ${sample.slug}: ${err.message}`);
      summary.push({
        label: sample.label,
        slug: sample.slug,
        viewport: 'desktop-only',
        file: path.relative(REPO_ROOT, filePath).replace(/\\/g, '/'),
        error: err.message,
      });
      continue;
    }
  }

  await browser.close();
  const summaryPath = path.join(OUT_DIR, 'summary.json');
  await writeFile(summaryPath, JSON.stringify(summary, null, 2) + '\n');
  console.log(`Done. ${summary.length} measurement files written. Summary: ${summaryPath}`);
  return summary;
}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
  runFullAudit();
}
