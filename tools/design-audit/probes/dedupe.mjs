export function dedupeBySignature(items, signatureFn) {
  const map = new Map();
  for (const item of items) {
    const sig = signatureFn(item);
    if (!map.has(sig)) {
      const { sample, ...rest } = item;
      map.set(sig, { ...rest, count: 0, samples: [] });
    }
    const entry = map.get(sig);
    entry.count += 1;
    if (entry.samples.length < 3 && item.sample) {
      entry.samples.push(item.sample);
    }
  }
  return [...map.values()].sort((a, b) => b.count - a.count);
}
