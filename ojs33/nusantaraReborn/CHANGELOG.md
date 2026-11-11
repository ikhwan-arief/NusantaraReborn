# Changelog — NusantaraReborn (OJS 3.3 track)

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
- Site-level pages (journal list without a journal context) show the OJS site title; the footer stays at the bottom of short pages.
- Fallback issue covers are text-free and follow the active preset.
