export async function scanTourVariation(page) {
  return page.evaluate(() => {
    const bodyText = document.body.innerText;
    const dayMatches = bodyText.match(/\bDay\s+\d+\b/gi) || [];
    const uniqueDays = [...new Set(dayMatches.map((d) => d.toLowerCase()))];

    const priceLikeElements = Array.from(document.querySelectorAll('body *')).filter((el) => {
      if (el.children.length > 0) return false;
      const text = el.textContent.trim();
      return /(\$|USD|VND|₫)\s?[\d,.]+/.test(text) && text.length < 40;
    });

    const allImages = Array.from(document.querySelectorAll('img')).filter((img) => {
      const rect = img.getBoundingClientRect();
      return rect.width > 80 && rect.height > 80;
    });

    return {
      itineraryDayCount: uniqueDays.length,
      itineraryDaySamples: uniqueDays.slice(0, 5),
      priceLikeElementCount: priceLikeElements.length,
      priceSamples: priceLikeElements.slice(0, 5).map((el) => el.textContent.trim()),
      totalVisibleImageCount: allImages.length,
    };
  });
}
