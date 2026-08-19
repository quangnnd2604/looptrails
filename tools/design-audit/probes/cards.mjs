export async function scanCardGrids(page) {
  return page.evaluate(() => {
    const TOLERANCE = 0.1;
    function rectsSimilar(a, b) {
      const widthDiff = Math.abs(a.width - b.width) / Math.max(a.width, b.width);
      const heightDiff = Math.abs(a.height - b.height) / Math.max(a.height, b.height);
      return widthDiff <= TOLERANCE && heightDiff <= TOLERANCE;
    }

    const groups = [];
    document.querySelectorAll('body *').forEach((parent) => {
      const children = Array.from(parent.children).filter((child) => {
        const rect = child.getBoundingClientRect();
        return rect.width > 60 && rect.height > 60;
      });
      if (children.length < 3) return;

      const rects = children.map((child) => child.getBoundingClientRect());
      const allSimilar = rects.every((r) => rectsSimilar(rects[0], r));
      if (!allSimilar) return;

      const first = children[0];
      const firstRect = rects[0];
      const cs = getComputedStyle(first);
      const image = first.querySelector('img');
      let imageAspectRatio = null;
      if (image) {
        const imgRect = image.getBoundingClientRect();
        if (imgRect.height > 0) imageAspectRatio = +(imgRect.width / imgRect.height).toFixed(3);
      }
      const gapX = rects.length > 1 ? Math.round(rects[1].left - rects[0].right) : null;

      groups.push({
        parentTag: parent.tagName.toLowerCase(),
        parentClassName: typeof parent.className === 'string' ? parent.className.slice(0, 80) : '',
        itemCount: children.length,
        itemWidth: Math.round(firstRect.width),
        itemHeight: Math.round(firstRect.height),
        borderRadius: cs.borderRadius,
        boxShadow: cs.boxShadow,
        gapX,
        imageAspectRatio,
      });
    });

    const seen = new Set();
    return groups.filter((g) => {
      const key = `${g.parentTag}|${g.parentClassName}|${g.itemCount}`;
      if (seen.has(key)) return false;
      seen.add(key);
      return true;
    });
  });
}
