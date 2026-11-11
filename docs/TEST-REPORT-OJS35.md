# Laporan Uji NusantaraReborn 2.0.0.0 — OJS 3.5

Developed by Ikhwan Arief (ikhwan[at]unand.ac.id). Copyright (c) 2026 Ikhwan Arief. Dokumentasi: CC BY-NC 4.0.

**Tanggal:** 11 November 2026 · **Paket:** `dist/ojs35/nusantaraReborn-2.0.0.0.tar.gz` (SHA-256 di `dist/SHA256SUMS`)
**Rencana:** `docs/PLAN-OJS35.md` · **Data:** seluruhnya sintetis (`tests/runtime35/seed/`), kecuali satu ID ROR yang diambil dari registri ROR bawaan OJS untuk menguji afiliasi ROR.

## Lingkungan

| Komponen | Versi |
|---|---|
| OJS | 3.5.0-6 (arsip lokal, SHA-256 cocok dengan rencana induk §4.1) |
| PHP | 8.2 (php:8.2-apache, ekstensi termasuk `ftp` yang diwajibkan platform check Composer OJS 3.5) |
| Database | MariaDB 10.11 |
| Browser | Google Chrome 157 (Playwright, headless) |
| Plugin aktif | citationStyleLanguage, googleScholar, dublinCoreMeta, customBlockManager, staticPages, pdfJsViewer, htmlArticleGalley, credit, pflPlugin, DOI (core) |
| Fixture hook | `tests/fixtures/ojs35/nrHookFixture` (uji saja, tidak dikemas) |
| Stack | `tests/runtime35` (port 8100, dibangun ulang dari nol dengan `setup.sh --reset`); fixture upgrade `tests/upgrade35` (port 8101) |

## Hasil

| Skenario | Status | Bukti / catatan |
|---|---|---|
| PHP lint 8.2, kontras WCAG AA lima preset | Lulus | `scripts/build.sh 35` (kontras), `php -l` |
| Stack dari nol dengan satu skrip | Lulus | `tests/runtime35/setup.sh --reset` |
| Unggah via Plugin Manager (tarball final), enable, pilih theme | Lulus | `NR_TRACK=35 tests/e2e/install_theme.py`; isi folder terpasang identik dengan tarball |
| Instalasi manual `installPluginVersion.php` | Lulus | Versi 2.0.0.0 tercatat sebagai versi aktif |
| Nama penyimpanan opsi | Lulus | `nusantararebornthemeplugin`, sama dengan 1.x |
| Opsi multibahasa disimpan dan dirender | Lulus | Isian UI tersimpan dan tampil ter-*escape* |
| 15 halaman jurnal + halaman situs | Lulus (HTTP 200) | `tests/runtime35/verify.sh` |
| Hook Index/Article Main/Details/Footer, Common Footer, header | Lulus | Setiap penanda tampil tepat sekali |
| Afiliasi ganda + ROR (nama dari registri ROR), ORCID | Lulus | Nomor afiliasi `1,2 / 3 / 4`; tautan ROR; ikon ORCID |
| Data availability statement | Lulus | Bagian tersendiri di halaman artikel |
| Grafik statistik `displayStats` | Lulus | Kanvas tergambar; data dari metrik sintetis |
| Tanggal editorial dari repository keputusan 3.5 | Lulus | Dikirim/Direvisi/Diterima/Terbit benar |
| Blok sitasi CSL | Lulus | Tepat satu blok; 13 format; dropdown berfungsi |
| Highlights, pengumuman, kartu artikel beranda | Lulus | 2 slide, 4 tautan pengumuman, 3 kartu |
| Masthead dan riwayat editorial core | Lulus | 2 editor sintetis, gaya NR |
| Menu Navigation, blok sidebar | Lulus | Item *Policies* dan blok kustom tampil |
| Naskah belum terbit | Lulus | Tidak tampil di beranda, arsip, artikel terkait, JSON-LD |
| Lokal `id`, jurnal berbahasa utama Indonesia | Lulus | `lang="id"`, label theme berbahasa Indonesia |
| Isolasi opsi antarjurnal | Lulus | Jurnal 2 Batik Sogan, jurnal 1 Zamrud; tanpa kebocoran data |
| Escape opsi berbahaya, URL `javascript:`, foto `http://` | Lulus | Skrip ter-*escape*, URL tak aman dibuang |
| Sanitasi Custom Page (skrip, iframe, `onerror`) | Lulus | Dibuang; heading dan tabel tetap; tidak ada dialog `alert` |
| Lima preset × 375/1440 (beranda, artikel, masthead); 6 halaman × 375/768/1440 | Lulus | Tanpa overflow, konsol bersih, tanpa permintaan gagal |
| Font sistem tanpa font eksternal; mode Google + ornamen nonaktif | Lulus | Google Fonts hanya dimuat pada mode Google |
| Upgrade OJS 3.3.0-24 + NR 1.x → OJS 3.5.0-6 + NR 2.0.0.0 | Lulus | `tests/upgrade35/upgrade-test.sh`: opsi bertahan; kunci `en_US`/`id_ID` terbaca sebagai `en`/`id`, tampil di form, tersimpan ulang dengan kunci baru; tanggal editorial benar setelah renumbering keputusan |
| Log PHP | Lulus | Tidak ada error dari theme |

## Catatan dan keterbatasan

- **Plugin PFL (Publication Facts Label) core** melempar `TypeError` di `getReviewerAverage()` pada artikel tanpa data penelaah. Error yang sama muncul dengan Default Theme, jadi bukan dari theme. Blok PFL tidak tampil pada data sintetis ini.
- Upgrade OJS 3.3 → 3.5 mengharuskan penggabungan `config.inc.php` dengan template 3.5 (`app_key`, `[email] default`) dan `base_url` yang sama dengan log statistik lama selama proses upgrade. Ini persyaratan OJS, sudah dibuat otomatis di fixture.
- Firefox dan Safari belum diuji; pengujian keyboard dan pembaca layar baru sebatas pemeriksaan markup.
- Plugin crossref/datacite tidak diaktifkan (tidak ada deposit eksternal); CRediT aktif tetapi data peran kontributor tidak diisi.
- Log PHP berisi *deprecation* dan *notice* dari core OJS 3.5.
