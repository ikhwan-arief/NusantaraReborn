# NusantaraReborn

Theme jurnal ilmiah umum bernuansa Indonesia untuk OJS. Rencana: `IMPLEMENTATION-PLAN.md`.

| Jalur | Status | Paket |
|---|---|---|
| OJS 3.3 (3.3.0-24) | 1.0.1.0 dibangun dan diuji lokal | `dist/ojs33/nusantaraReborn-1.0.1.0.tar.gz` |
| OJS 3.5 (3.5.0-6) | Belum dikerjakan | — |

## Struktur

- `shared/` — CSS, JS, aset, dan berkas lisensi lintas versi.
- `ojs33/nusantaraReborn/` — PHP, template, README/CHANGELOG khusus 3.3 (locale dihasilkan).
- `scripts/` — `presets.json` (token warna), `build.sh`, `check-contrast.py`, `make-*.py`.
- `tests/runtime33/` — stack Docker OJS 3.3.0-24 lokal (port 8099, data sintetis); `tests/e2e/` — uji Playwright.
- `docs/TEST-REPORT-OJS33.md` — hasil uji.
- `legacy/` — snapshot Unand Journal Theme 1.2.0.0 (tidak didistribusikan).

## Build dan uji

```bash
scripts/build.sh 33                      # kontras, locale, presets.css, tarball + SHA256SUMS
tests/runtime33/setup.sh --reset         # stack uji bersih (menghapus data stack ini saja)
python tests/e2e/install_theme.py        # unggah + aktifkan theme lewat UI
tests/runtime33/sync-theme.sh            # salin build terbaru ke stack saat iterasi
```

Developed by Ikhwan Arief (ikhwan[at]unand.ac.id). Copyright (c) 2026 Ikhwan Arief. Kode GPL-3.0-or-later; dokumentasi dan aset visual orisinal CC BY-NC 4.0.
