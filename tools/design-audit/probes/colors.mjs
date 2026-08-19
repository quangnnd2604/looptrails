import { dedupeBySignature } from './dedupe.mjs';

function toHex(rgbString) {
  const match = rgbString.match(/rgba?\(([^)]+)\)/);
  if (!match) return rgbString;
  const parts = match[1].split(',').map((s) => parseFloat(s.trim()));
  const [r, g, b, a] = parts;
  if (a === 0) return 'transparent';
  const hex = '#' + [r, g, b].map((v) => Math.round(v).toString(16).padStart(2, '0')).join('');
  return a !== undefined && a < 1 ? `${hex} (alpha ${a})` : hex;
}

export async function scanColors(page) {
  const raw = await page.evaluate(() => {
    const results = [];
    document.querySelectorAll('body, body *').forEach((el) => {
      const cs = getComputedStyle(el);
      const rect = el.getBoundingClientRect();
      if (rect.width === 0 || rect.height === 0) return;
      ['backgroundColor', 'color', 'borderTopColor'].forEach((prop) => {
        const value = cs[prop];
        if (!value || value === 'rgba(0, 0, 0, 0)' || value === 'transparent') return;
        results.push({
          property: prop,
          value,
          tag: el.tagName.toLowerCase(),
          className: typeof el.className === 'string' ? el.className.slice(0, 80) : '',
          sample: el.tagName.toLowerCase(),
        });
      });
    });
    return results;
  });

  const deduped = dedupeBySignature(raw, (item) => `${item.property}|${item.value}`);
  return deduped.map((entry) => ({ ...entry, hex: toHex(entry.value) }));
}
