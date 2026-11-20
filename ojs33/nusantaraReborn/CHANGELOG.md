# Changelog — NusantaraReborn (OJS 3.3 track)

## 1.0.4.0 — 2026-11-20

Consolidated release: reviewed and kept every change made after the last release, plus the archive fix of 1.0.3.0.

- Editorial Team (from 2026-11-12/13 changes): external profile links on names are kept and shown on cards and table rows (CV, Scopus, etc. open in a new tab with `rel="noopener noreferrer"`); bracket and dash leftovers are trimmed from member lines.
- Layout: the OJS default theme column lines no longer appear on any page.
- How to Cite: full-width citation formats dropdown and Copy Citation button; heading styled like other section titles.
- Footer: the copyright mark moved from the journal name to the theme credit (`© year NusantaraReborn theme`), so the theme no longer states a copyright on behalf of the journal.
- Fixed in review: the double accent bar of the How to Cite heading used a selector that never matched (`.nr_ornaments_subtle .nr_theme …`, both classes are on `<body>`), so it did not show on OJS 3.4/3.5 with subtle ornaments.
- Includes 1.0.3.0: archive statistics from one query.

## 1.0.3.0 — 2026-11-20

Performance fix for large journals.

- Archive page: article counts and page ranges per issue now come from one lightweight, parameterised query on publication_settings (`issueId`, `pages`) instead of loading every published submission with its publications, authors, galleys and keywords. Before, the cost grew by about 23 SQL queries per article, so journals with hundreds or thousands of articles per issue timed out (for example HTTP 524 behind Cloudflare). The query count of this step no longer depends on the number of articles.
- Counts and page ranges are unchanged (verified against the previous release on the synthetic test stack).

## 1.0.2.0 — 2026-11-11

Security hardening from docs/SECURITY-AUDIT-OJS33.md.

- T-1: Editorial Team text, Custom Pages and license terms are sanitised with the HTMLPurifier bundled in OJS. Allowlist = server `allowed_html` + headings, tables, blockquote. Scripts, iframes, `style` and event attributes are removed. (Plain `strip_unsafe_html` was rejected: the default OJS 3.3 allowlist drops headings and breaks these pages.)
- T-2: New option "Editorial dates on article pages" (show/hide, default show).
- T-3: Editor-in-Chief photo accepts https:// only and is loaded with `referrerpolicy="no-referrer"`. `safeUrl()` takes an optional scheme list.

## 1.0.1.0 — 2026-11-11

- Site-level pages (journal list without a journal context) show the OJS site title; the footer stays at the bottom of short pages.
- Theme settings start with a guide block: a warning to build menus with OJS Navigation, a live count of Primary Navigation Menu items, and a link to docs/PANDUAN-PENGATURAN.md (included in the package).

## 1.0.0.0 — 2026-11-11

First release of NusantaraReborn for OJS 3.3 (tested on 3.3.0-24). New plugin; no automatic migration from Unand Journal Theme.

- New identity: plugin `nusantaraReborn`, class `NusantaraRebornThemePlugin`, storage name `nusantararebornthemeplugin`, locale prefix `plugins.themes.nusantaraReborn`, CSS/JS prefix `nr`.
- Five Indonesian-inspired presets with WCAG AA-checked tokens: Zamrud Khatulistiwa (default), Bahari Nusantara, Batik Sogan, Merah Putih, Tenun Senja.
- New options: Nusantara ornaments (subtle/off) and font source (system/Google Fonts). System mode loads no external font, including fonts the parent theme would inject.
- Removed every institutional fallback: logo, crest, favicon, publisher name and URL, taglines, captions, peer-review and open-access defaults. Empty values are hidden.
- Header shows the journal logo from OJS or the journal name as text.
- Sidebar blocks now also appear on full-width pages (homepage, article, archive, editorial team, custom pages) as a band below the content.
- "Indexed in" option is rendered in the footer (it was stored but never shown before).
- Ported the OJS 3.3.0-24 escaping of the article copyright statement.
- Editorial dates appear only on published versions and read only decision type and date. JSON-LD and related articles use published content only.
- Corresponding author email is hidden by default (option to show).
- Fallback issue covers are text-free and follow the active preset.
