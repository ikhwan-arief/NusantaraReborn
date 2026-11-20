# Changelog — NusantaraReborn (OJS 3.4 track)

## 3.0.3.0 — 2026-11-20

Clean-up release; no visible change on public pages.

- Removed an unused template variable (`nrThemeUrl`) and an unused style rule (`.nr_editor_role`).
- Locale: dropped the unused copy-citation string (OJS 3.4 leaves that button to the CSL plugin).

## 3.0.2.0 — 2026-11-20

Consolidated release: reviewed and kept every change made after the last release, plus the archive fix of 3.0.1.0.

- Editorial Team (from 2026-11-12/13 changes): external profile links on names are kept and shown on cards and table rows (CV, Scopus, etc. open in a new tab with `rel="noopener noreferrer"`); bracket and dash leftovers are trimmed from member lines.
- Layout: the OJS default theme column lines no longer appear on any page.
- How to Cite: full-width citation formats dropdown and Copy Citation button; heading styled like other section titles.
- Footer: the copyright mark moved from the journal name to the theme credit (`© year NusantaraReborn theme`), so the theme no longer states a copyright on behalf of the journal.
- Fixed in review: the double accent bar of the How to Cite heading used a selector that never matched (`.nr_ornaments_subtle .nr_theme …`, both classes are on `<body>`), so it did not show on OJS 3.4/3.5 with subtle ornaments.
- Includes 3.0.1.0: archive statistics from one query, and Carbon date formats.

## 3.0.1.0 — 2026-11-20

Performance fix for large journals and a date-format fix.

- Archive page: article counts and page ranges per issue now come from one lightweight, parameterised query on publication_settings (`issueId`, `pages`) instead of loading every published submission with its publications, authors, galleys and keywords. Before, the cost grew by about 23 SQL queries per article, so journals with hundreds or thousands of articles per issue timed out (for example HTTP 524 behind Cloudflare). The query count of this step no longer depends on the number of articles.
- Counts and page ranges are unchanged (verified against the previous release on the synthetic test stack).
- Fixed dates written in the old strftime style, which OJS 3.4+ (Carbon `translatedFormat`) printed literally: archive issue cards showed "%750 %2026" instead of the month and year, and homepage announcement `datetime` attributes were invalid. They now use `F Y` (localised month) and `Y-m-d`.

## 3.0.0.0 — 2026-11-11

First release for OJS 3.4 (tested on 3.4.0-12, PHP 8.2). Same plugin identity and option names as the 1.x (OJS 3.3) and 2.x (OJS 3.5) tracks (`nusantaraReborn`, storage name `nusantararebornthemeplugin`).

- Derived from the 2.0.0.0 OJS 3.5 code: namespaced class, PKP Hook API, OJS 3.4 repositories (decisions with class constants, submissions, issues, sections), publication statistics service, Flysystem 3, locale folders `id` and `en`.
- Article page built from the OJS 3.4 template: one affiliation string per author (numbered when they differ), ORCID from the orcidProfile plugin with an iD badge, data availability statement, usage statistics chart (`displayStats` kept), CSL citation block left to its plugin hook.
- Editorial Team: the HTML masthead (Settings > Journal) is sanitised and enhanced into profile cards and tables, as in the 1.x track.
- Homepage announcements read from the OJS 3.4 announcement objects; no Highlights component (not available in OJS 3.4).
- Upgrade from 1.x: multilingual options saved with OJS 3.3 keys (`en_US`, `id_ID`) are read as `en`, `id` and shown in the settings form.
- All 1.0.2.0 / 2.0.0.0 features and security controls: five presets, ornaments, font source, settings guide with Navigation warning, `richHtml()` sanitising, https-only Editor-in-Chief photo, optional editorial dates.
