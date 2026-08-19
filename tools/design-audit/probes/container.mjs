export async function scanContainer(page) {
  return page.evaluate(() => {
    const viewportWidth = window.innerWidth;
    const candidates = [];
    document.querySelectorAll('body *').forEach((el) => {
      const cs = getComputedStyle(el);
      const rect = el.getBoundingClientRect();
      if (rect.width <= 0) return;
      const maxWidth = cs.maxWidth;
      if (maxWidth && maxWidth !== 'none') {
        const parsedMaxWidth = parseFloat(maxWidth);
        if (!Number.isNaN(parsedMaxWidth) && parsedMaxWidth > 320 && parsedMaxWidth < viewportWidth) {
          candidates.push({
            tag: el.tagName.toLowerCase(),
            className: typeof el.className === 'string' ? el.className.slice(0, 80) : '',
            maxWidth: parsedMaxWidth,
            renderedWidth: Math.round(rect.width),
            paddingLeft: cs.paddingLeft,
            paddingRight: cs.paddingRight,
          });
        }
      }
    });

    const widthCounts = new Map();
    for (const c of candidates) {
      widthCounts.set(c.renderedWidth, (widthCounts.get(c.renderedWidth) || 0) + 1);
    }
    const sortedWidths = [...widthCounts.entries()].sort((a, b) => b[1] - a[1]);
    const topWidth = sortedWidths[0] ? sortedWidths[0][0] : null;

    return {
      viewportWidth,
      mostCommonContainerWidth: topWidth,
      candidateCount: candidates.length,
      topCandidates: candidates.filter((c) => c.renderedWidth === topWidth).slice(0, 3),
    };
  });
}
