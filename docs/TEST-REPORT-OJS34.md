# Laporan Uji NusantaraReborn 3.0.0.0 — OJS 3.4

Developed by Ikhwan Arief (ikhwan[at]unand.ac.id). Copyright (c) 2026 Ikhwan Arief. Dokumentasi: CC BY-NC 4.0.

**Tanggal:** 11 November 2026 · **Paket:** `dist/ojs34/nusantaraReborn-3.0.0.0.tar.gz` (SHA-256 di `dist/SHA256SUMS`)
**Dasar:** jalur OJS 3.5 (2.0.0.0) disesuaikan dengan API OJS 3.4; keputusan versi 3.0.0.0 dari pemilik produk. **Data:** sintetis (`tests/runtime34/seed/`).

## Lingkungan

| Komponen | Versi |
|---|---|
| OJS | 3.4.0-12 (arsip lokal `ojs-3.4.0-12.tar.gz`, SHA-256 `830ce886…ba02a0`) |
| PHP / Database | 8.2 (php:8.2-apache) / MariaDB 10.11 |
| Browser | Google Chrome 157 (Playwright, headless) |
| Plugin aktif | citationStyleLanguage, googleScholar, dublinCoreMeta, customBlockManager, staticPages, pdfJsViewer, htmlArticleGalley, DOI (core) |
| Stack | `tests/runtime34` (port 8102, `setup.sh --reset`), fixture upgrade `tests/upgrade34` (port 8103) |

## Perbedaan OJS 3.4 yang ditangani

| Area | OJS 3.4 | Penanganan theme |
|---|---|---|
| Afiliasi penulis | Satu string per penulis | Daftar afiliasi bernomor dari string; JSON-LD `Organization` |
| ORCID | Plugin `orcidProfile` (verifikasi via token) | Lencana "iD" milik theme (terverifikasi/tidak) |
| Keyword | Array string | Didukung bersama format 3.5 |
| Dewan Redaksi | HTML dari Pengaturan > Jurnal | Override `editorialTeam.tpl` + kartu/tabel JS; HTML disaring `richHtml()` |
| Pengumuman | DataObject | `getLocalizedTitle()`, `getDatePosted()`, `getAnnouncementTypeName()` |
| Statistik/berkas | `Services::get()` | `publicationStats`, `file` |
| Highlights | Template tidak tersedia | Tidak ditampilkan |
| Referensi kosong | `$parsedCitations` bernilai `null` | Kondisi mengikuti template core 3.4 |
| `ThemePlugin::getOptionValues()` | Parameter tanpa tipe | Signature kompatibel |

## Hasil

| Skenario | Status | Bukti / catatan |
|---|---|---|
| PHP lint 8.2, kontras WCAG AA | Lulus | `scripts/build.sh 34` |
| Stack dari nol dengan satu skrip | Lulus | `tests/runtime34/setup.sh --reset` |
| Unggah via Plugin Manager (tarball final), enable, pilih theme | Lulus | `NR_TRACK=34 tests/e2e/install_theme.py`; isi terpasang identik dengan tarball |
| Simpan opsi multibahasa via UI | Lulus | Tersimpan sekali-*encode*, tampil ter-*escape* |
| 14 halaman jurnal + halaman situs | Lulus (HTTP 200) | `tests/runtime34/verify.sh` |
| Hook Index/Article/Footer/header | Lulus | Tepat sekali |
| Afiliasi bernomor, ORCID, data availability, grafik statistik, tanggal editorial, CSL (1 blok), DOI, JSON-LD, artikel terkait, blok kustom | Lulus | Halaman artikel |
| Dewan Redaksi: kartu, tabel, bendera | Lulus | 1 kartu, 3 baris, 4 bendera |
| Halaman kebijakan: bagian bernomor + daftar isi | Lulus | 4 bagian |
| Naskah belum terbit | Lulus | Tidak tampil di beranda, arsip, artikel |
| Lokal `id`, jurnal berbahasa utama Indonesia, isolasi antarjurnal | Lulus | `lang="id"`, Batik Sogan vs Zamrud, tanpa kebocoran |
| Escape opsi, URL `javascript:`, foto `http://`, sanitasi Custom Page | Lulus | Tanpa skrip dieksekusi, tanpa dialog `alert` |
| Lima preset × 375/1440; 6 halaman × 375/768/1440 | Lulus | Tanpa overflow, konsol bersih |
| Font sistem; mode Google + ornamen nonaktif | Lulus | Font eksternal hanya pada mode Google |
| Upgrade OJS 3.3.0-24 + NR 1.x → OJS 3.4.0-12 + NR 3.0.0.0 | Lulus | `tests/upgrade34/upgrade-test.sh`: opsi bertahan; kunci `en_US`/`id_ID` terbaca, tampil di form, tersimpan ulang sebagai `en`/`id`; tanggal editorial benar |
| Log PHP | Lulus | Tidak ada error dari theme |

## Catatan dan keterbatasan

- Plugin `orcidProfile` tidak diaktifkan di fixture karena memerlukan kredensial ORCID; tanpa pengaturan, plugin core itu melempar *exception*. Tampilan ORCID diuji dari data penulis.
- Plugin Gallery core mencoba mengunduh katalog dari pkp.sfu.ca (timeout di lingkungan uji); bukan bagian theme.
- Upgrade 3.3 → 3.4 memerlukan penggabungan `config.inc.php` dengan template 3.4 dan `base_url` lama selama proses; sudah otomatis di fixture.
- Firefox, Safari, dan pembaca layar belum diuji.
