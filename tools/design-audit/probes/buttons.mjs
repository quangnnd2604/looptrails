export async function scanButtons(page) {
  const handles = await page.locator('button, a').elementHandles();
  const results = [];

  for (const handle of handles) {
    const box = await handle.boundingBox();
    if (!box || box.height < 24 || box.height > 90 || box.width < 40) continue;

    const text = (await handle.textContent())?.trim() ?? '';
    if (!text || text.length > 40) continue;

    const restStyle = await handle.evaluate((el) => {
      const cs = getComputedStyle(el);
      return {
        height: cs.height,
        paddingTop: cs.paddingTop,
        paddingRight: cs.paddingRight,
        borderRadius: cs.borderRadius,
        backgroundColor: cs.backgroundColor,
        color: cs.color,
        boxShadow: cs.boxShadow,
        fontSize: cs.fontSize,
        fontWeight: cs.fontWeight,
      };
    });

    const looksLikeButton =
      restStyle.backgroundColor !== 'rgba(0, 0, 0, 0)' || restStyle.boxShadow !== 'none';
    if (!looksLikeButton) continue;

    let hoverStyle = null;
    try {
      await handle.hover({ timeout: 2000 });
      hoverStyle = await handle.evaluate((el) => {
        const cs = getComputedStyle(el);
        return { backgroundColor: cs.backgroundColor, color: cs.color, boxShadow: cs.boxShadow };
      });
    } catch {
      hoverStyle = null;
    }

    results.push({
      tag: await handle.evaluate((el) => el.tagName.toLowerCase()),
      text: text.slice(0, 40),
      rest: restStyle,
      hover: hoverStyle,
    });
  }

  const seen = new Set();
  return results.filter((r) => {
    const key = `${r.tag}|${r.rest.height}|${r.rest.paddingTop}|${r.rest.borderRadius}|${r.rest.backgroundColor}`;
    if (seen.has(key)) return false;
    seen.add(key);
    return true;
  });
}
