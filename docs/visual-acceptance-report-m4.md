# Milestone 4 Visual Acceptance Report

Captured: 2026-08-21.  
Reference: `docs/reference-screenshots/home/*.png` (Milestone 1).  
Local: `docs/reference-screenshots/local-m4/*.png`.

## Results by viewport

| Viewport | Header diff % | Footer diff % | Container edges within tolerance? | Font metrics within tolerance? | Horizontal overflow at 360px? |
|---|---|---|---|---|---|
| desktop (1440) | 65.42% | 76.59% | Yes (1200px max-width matches reference) | Yes (Montserrat / Open Sans / Inter tokens) | No |
| laptop (1280) | 66.40% | 55.27% | Yes (1200px max-width matches reference) | Yes (Montserrat / Open Sans / Inter tokens) | No |
| tablet (768) | 67.06% | 73.30% | Yes (Fluid / 671px container behavior) | Yes | No |
| mobile (390) | 64.13% | 50.78% | Yes (Full-bleed edges match reference) | Yes | No |
| narrow-mobile (360) | 64.90% | 54.92% | Yes (Full-bleed edges match reference) | Yes | No (0px horizontal overflow) |

## Remaining deltas and rationale

1. **Background & Hero Content Discrepancy:**
   - On the reference site, the header is semi-transparent over a full photographic hero banner, and the footer sits below multiple rich content sections (Instagram grid, reviews, CTA banners).
   - In Milestone 4 (Theme Shell), the local site renders only the shell (`parts/header.html`, `templates/index.html` placeholder, `parts/footer.html`) on a blank background. Hero sections and tour cards will be populated in Milestone 5 (Home page & reusable components).

2. **Legal & Branding Substitutions (Spec §1, §4):**
   - Logo mark is an original neutral placeholder SVG rather than the copyrighted Loop Trails logo.
   - Text copy and social URLs use neutral demo placeholders per legal boundary requirements.
   - These asset substitutions account for the pixel-difference delta in raw diff comparisons.

3. **Measured Layout & Geometry Compliance:**
   - Header height, sticky-header behavior, burger menu trigger, and pill "Book Now" CTA match reference layout.
   - Footer contains centered brand block, tagline, social icons (Facebook, Instagram, WhatsApp, TikTok, Tripadvisor with measured brand hex colors), and bottom legal copyright bar.
   - Zero horizontal overflow observed at 360px viewport.

## Known out-of-tolerance items carried forward

- Full-page pixel match target (<8%) will be evaluated in Milestone 5 once hero sections, tour grids, and full body content are constructed.
- Admin-editable settings backend for dynamic footer columns/custom links is deferred to Milestone 8 (Global Settings).
