# Milestone 11 — Visual Diff Iteration Across All Viewports

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Execute visual diff iteration comparing local rendered pages against reference screenshots across all 5 viewports (1440px Desktop, 1280px Laptop, 768px Tablet, 390px Mobile, 360px Narrow-Mobile) matching Spec §13.11.

**Architecture:**
- Automated capture script `tools/local-audit/capture-local.mjs`
- Automated metric checking `tools/local-audit/check-metrics.mjs`
- Automated color checking `tools/local-audit/check-colors.mjs`
- Automated horizontal overflow checking `tools/local-audit/check-overflow.mjs`
- Pixel diff verification `tools/local-audit/diff.mjs`

**Spec Reference:** `docs/AI_AGENT_WORDPRESS_TOUR_WEBSITE_SPEC.md` §13.11, §14.

---

## Tasks

### Task 1: Multi-Viewport Automated Capture
- [ ] Capture all 5 viewports (1440px, 1280px, 768px, 390px, 360px) in `docs/reference-screenshots/local-m5/`.
- [ ] Record geometry regions for Header, Main, and Footer.

### Task 2: Visual Acceptance & Geometry Alignment
- [ ] Verify typography and button geometry metrics across all breakpoints (`check-metrics.mjs`).
- [ ] Verify palette hex values and computed RGB colors (`check-colors.mjs`).
- [ ] Verify zero horizontal overflow at 360px width (`check-overflow.mjs`).

### Task 3: Visual Acceptance Documentation
- [ ] Produce detailed Visual Acceptance Reports comparing rendered layout against reference screenshots.
