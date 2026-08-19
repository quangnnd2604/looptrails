# Environment Audit

Date: 2026-08-19
Scope: `c:\xampp\htdocs\looptrails` local XAMPP install, ahead of Milestone 1 (spec §13.1).

## Local stack

| Component | Value |
|---|---|
| OS | Windows 11 Pro |
| Web server | XAMPP Apache (started via `C:\xampp\apache_start.bat` / stopped via `apache_stop.bat` — not a Windows service; `net start/stop` will not work) |
| PHP | 8.2.31, ZTS, at `C:\xampp\php\php.exe` |
| WordPress core | 7.0.4 |
| Database | MySQL/MariaDB via XAMPP, `DB_NAME=looptrails`, `DB_HOST=localhost` (credentials in `wp-config.php`, git-ignored) |
| WP-CLI | `C:\xampp\wp-cli.bat` — wraps XAMPP's own PHP against `wp-cli.phar`. **Always use this wrapper, never a bare `wp`/`php` on PATH** — a second, unrelated PHP install exists on this machine's PATH. |
| Node.js | v22.14.0 |
| npm | 10.9.2 |
| Playwright | 1.62.1, available via `npx playwright` (no persisted global install; project-local install added in Task 2) |
| GD extension | enabled (`php -m` lists `gd`) — required for WordPress image thumbnail generation |

## WordPress install state (fresh, confirmed empty of prior work)

- Active theme: `twentytwentyfive` (default). Inactive: `twentytwentyfour`, `twentytwentythree`, `twentytwentytwo`. No custom theme present.
- Plugins: `akismet` (inactive), `hello` (inactive). No Elementor, no PRO Elements, no `tour-booking-core`, no leftover plugin from any prior build attempt.
- This confirms the install is a genuine fresh start for spec v2.0 (native Gutenberg block theme architecture) — a prior session's Elementor-based work (see [[technical_wordpress_elementor_conventions]], now superseded/historical) is not present in this codebase and will not be built upon.

## Safety / staging confirmation

- This is a local-only XAMPP development environment (`localhost`), not a production or publicly reachable host. No production credentials, no live payment gateway access, no real customer data exist here.
- Version control: git repository initialized at `c:\xampp\htdocs\looptrails`, remote `origin` = `https://github.com/quangnnd2604/looptrails.git` (private working repo, confirmed empty before first push). `.gitignore` excludes WordPress core files, `wp-config.php`, uploads, default themes/plugins we do not modify, and `docs/reference-screenshots/`. Only our custom theme/plugin/docs are tracked.
- Backup strategy for this milestone: git history is the backup for source; the WordPress database itself holds no meaningful demo data yet at this stage, so no DB backup is required before Milestone 1 work. A DB export step will be added to the plan for Milestone 3 (companion plugin schema/migrations) before any schema changes are applied.

## Screenshot capture inventory

Captured 2026-08-19 via `tools/reference-audit/capture.mjs`. Full manifest: `docs/reference-screenshot-manifest.json` (60 entries: 12 reference pages × 5 viewports each).

Pages captured: Home, Tours Archive, 3 representative Tour Details (2D1N, 4D3N, Cao Bang 6D5N), Motorbike Rental, Blog Archive, one Blog Single Article, Contact, About, Terms/Privacy, and a 404 page.

Not captured (not observable on the public reference site as of this date): blog category/tag archive, on-site search results — the live `/blog/` page has no visible category/tag links or search box. These page types will still be built per spec §5 items 5 and 12, using WordPress's own standard archive/search template conventions since there is no reference layout to measure.
