# Changelog — NusantaraReborn (OJS 3.5 track)

## 2.0.1.0 — 2026-11-20

Performance fix for large journals and a date-format fix.

- Archive page: article counts and page ranges per issue now come from one lightweight, parameterised query on `publications.issue_id` and publication_settings (`pages`) instead of loading every published submission with its publications, authors, galleys and keywords. Before, the cost grew by about 23 SQL queries per article, so journals with hundreds or thousands of articles per issue timed out (for example HTTP 524 behind Cloudflare). The query count of this step no longer depends on the number of articles.
- Counts and page ranges are unchanged (verified against the previous release on the synthetic test stack).
- Fixed dates written in the old strftime style, which OJS 3.4+ (Carbon `translatedFormat`) printed literally: archive issue cards showed "%750 %2026" instead of the month and year, and homepage announcement `datetime` attributes were invalid. They now use `F Y` (localised month) and `Y-m-d`.

## 2.0.0.0 — 2026-11-11

First release for OJS 3.5 (tested on 3.5.0-6, PHP 8.2). Same plugin identity and option names as the 1.x OJS 3.3 track (`nusantaraReborn`, storage name `nusantararebornthemeplugin`), so settings carry over an OJS upgrade.

- Namespaced class `APP\plugins\themes\nusantaraReborn\NusantaraRebornThemePlugin`, `PKP\plugins\Hook`, locale folders `id` and `en`.
- Data access ported to OJS 3.5 repositories: decisions (`Repo::decision()`, class constants), submissions, issues, sections, keywords from publication data, statistics from the publication statistics service, file sizes through Flysystem 3.
- Article page rebuilt from the OJS 3.5 template: multiple author affiliations with ROR, verified/unverified ORCID, data availability statement, usage statistics chart (`displayStats` option of the Default Theme kept), CSL citation block left to its plugin hook (no duplicate), new preview URL.
- Homepage: OJS 3.5 Highlights component below the hero; announcements read from the 3.5 announcement model.
- Editorial Team: the native OJS 3.5 masthead and editorial history are inherited and styled; the 3.3 HTML-parsing override is not carried over.
- Upgrade from 1.x: multilingual theme options saved under OJS 3.3 locale keys (`en_US`, `id_ID`) are read as `en`, `id` and shown in the settings form; the next save stores them with the new keys. No database migration. Verified with an OJS 3.3.0-24 → 3.5.0-6 upgrade fixture.
- All 1.0.2.0 features and security controls: five presets, ornaments, font source, settings guide with Navigation warning, `richHtml()` sanitising (custom pages, license terms), https-only Editor-in-Chief photo, optional editorial dates.
