# Design Measurements

Raw computed-CSS measurement data for the reference site (looptrails.com), collected 2026-08-19 via `tools/design-audit/run-audit.mjs`, for Milestone 2b to turn into `docs/reference-audit.md`, a component inventory, and `theme.json` design tokens.

**This directory's JSON contents are git-ignored** (only this README is committed) — the data includes short verbatim text samples from the reference site captured purely to help identify which measured style maps to which UI element, and per this project's content-copyright boundary those never get published, even as structured data. Only measured *values* (pixel sizes, hex colors, counts) may be quoted in Milestone 2b's committed audit doc — never the reference site's prose.

## What's captured

- `<template-slug>/<viewport>.json` for 10 templates × 5 viewports (50 files): typography styles, colors, container width, card-grid geometry, button styles (rest + hover), all frequency-ranked.
- `tour-detail-variation/<sample-slug>.json` for the 3 tour-detail page samples (2 Days 1 Night / 4 Days 3 Nights / Cao Bang 6 Days 5 Nights): itinerary day count, price-row count, image count — what varies by tour length within the one shared Tour Detail template, rather than 3 redundant full measurements of an identical layout.
- `summary.json`: a flat index of every file above.

## Re-run

```
cd tools/design-audit && npm ci && npx playwright install chromium && npm run run-audit
```

`run-audit.mjs` skips any page whose output file already exists, so re-running on a
machine that already has data just logs "Skipping..." for everything. To force
re-measuring pages that already have output (e.g. after fixing a probe):

```
cd tools/design-audit && FORCE=1 npm run run-audit
```
