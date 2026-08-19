import { dedupeBySignature } from './dedupe.mjs';

export async function scanTypography(page) {
  const raw = await page.evaluate(() => {
    const results = [];
    const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_ELEMENT);
    let node = walker.currentNode;
    while (node) {
      const hasDirectText = Array.from(node.childNodes).some(
        (n) => n.nodeType === Node.TEXT_NODE && n.textContent.trim().length > 0
      );
      if (hasDirectText) {
        const cs = getComputedStyle(node);
        const rect = node.getBoundingClientRect();
        if (rect.width > 0 && rect.height > 0) {
          results.push({
            tag: node.tagName.toLowerCase(),
            className: typeof node.className === 'string' ? node.className.slice(0, 80) : '',
            fontFamily: cs.fontFamily,
            fontSize: cs.fontSize,
            fontWeight: cs.fontWeight,
            lineHeight: cs.lineHeight,
            letterSpacing: cs.letterSpacing,
            sample: node.textContent.trim().slice(0, 60),
          });
        }
      }
      node = walker.nextNode();
    }
    return results;
  });

  return dedupeBySignature(
    raw,
    (item) => `${item.fontFamily}|${item.fontSize}|${item.fontWeight}|${item.lineHeight}|${item.letterSpacing}`
  );
}
