# NusantaraReborn

Theme jurnal ilmiah umum bernuansa Indonesia untuk OJS. Rencana: `IMPLEMENTATION-PLAN.md`.

| Jalur | Status | Paket |
|---|---|---|
| OJS 3.3 (3.3.0-24) | 1.0.2.0 (seri 1.x) | `dist/ojs33/nusantaraReborn-1.0.2.0.tar.gz` |
| OJS 3.4 (3.4.0-12) | 3.0.0.0 (seri 3.x) | `dist/ojs34/nusantaraReborn-3.0.0.0.tar.gz` |
| OJS 3.5 (3.5.0-6) | 2.0.0.0 (seri 2.x) | `dist/ojs35/nusantaraReborn-2.0.0.0.tar.gz` |

## Struktur

- `shared/` — CSS, JS, aset, dan berkas lisensi lintas versi.
- `ojs33/`, `ojs34/`, `ojs35/` (`nusantaraReborn/`) — PHP, template, README/CHANGELOG per jalur (locale dihasilkan).
- `scripts/` — `presets.json` (token warna), `build.sh`, `check-contrast.py`, `make-*.py`.
- `tests/runtime33/` (8099), `tests/runtime34/` (8102), `tests/runtime35/` (8100), `tests/upgrade34/` (8103), `tests/upgrade35/` (8101) — stack Docker lokal dengan data sintetis; `tests/e2e/` — uji Playwright (`NR_TRACK=33|34|35`).
- `docs/` — rencana 3.5, panduan pengaturan, laporan uji dan audit keamanan per jalur.
- `legacy/` — snapshot Unand Journal Theme 1.2.0.0 (tidak didistribusikan).

## Build dan uji

```bash
scripts/build.sh 33|34|35                       # kontras, locale, presets.css, tarball + SHA256SUMS
tests/runtime35/setup.sh --reset              # stack uji bersih (menghapus data stack ini saja)
NR_TRACK=35 python tests/e2e/install_theme.py # unggah + aktifkan theme lewat UI
tests/runtime35/verify.sh                     # pemeriksaan penerimaan sisi server
tests/upgrade35/upgrade-test.sh               # fixture upgrade OJS 3.3 -> 3.5
```

Developed by Ikhwan Arief (ikhwan[at]unand.ac.id). Copyright (c) 2026 Ikhwan Arief. Kode GPL-3.0-or-later; dokumentasi dan aset visual orisinal CC BY-NC 4.0.
