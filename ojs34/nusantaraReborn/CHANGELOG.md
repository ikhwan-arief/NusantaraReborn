# Changelog — NusantaraReborn (OJS 3.4 track)

## 3.0.0.0 — 2026-11-11

First release for OJS 3.4 (tested on 3.4.0-12, PHP 8.2). Same plugin identity and option names as the 1.x (OJS 3.3) and 2.x (OJS 3.5) tracks (`nusantaraReborn`, storage name `nusantararebornthemeplugin`).

- Derived from the 2.0.0.0 OJS 3.5 code: namespaced class, PKP Hook API, OJS 3.4 repositories (decisions with class constants, submissions, issues, sections), publication statistics service, Flysystem 3, locale folders `id` and `en`.
- Article page built from the OJS 3.4 template: one affiliation string per author (numbered when they differ), ORCID from the orcidProfile plugin with an iD badge, data availability statement, usage statistics chart (`displayStats` kept), CSL citation block left to its plugin hook.
- Editorial Team: the HTML masthead (Settings > Journal) is sanitised and enhanced into profile cards and tables, as in the 1.x track.
- Homepage announcements read from the OJS 3.4 announcement objects; no Highlights component (not available in OJS 3.4).
- Upgrade from 1.x: multilingual options saved with OJS 3.3 keys (`en_US`, `id_ID`) are read as `en`, `id` and shown in the settings form.
- All 1.0.2.0 / 2.0.0.0 features and security controls: five presets, ornaments, font source, settings guide with Navigation warning, `richHtml()` sanitising, https-only Editor-in-Chief photo, optional editorial dates.
