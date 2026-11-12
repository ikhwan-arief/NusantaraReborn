# NusantaraReborn

Theme jurnal ilmiah umum bernuansa Indonesia untuk **Open Journal Systems (OJS) 3.3, 3.4, dan 3.5**. Theme ini menata beranda, halaman artikel, arsip, Dewan Redaksi, dan halaman kebijakan supaya rapi di desktop maupun ponsel. Identitas dan isi jurnal tetap diambil dari pengaturan OJS.

*English summary at the end of this page.*

## Unduh

Pilih paket sesuai versi OJS. Paket untuk satu versi **tidak** dapat dipakai di versi OJS lain.

| Versi OJS | Seri theme | Versi terbaru | Paket | Rilis |
|---|---|---|---|---|
| OJS 3.3 (diuji 3.3.0-24) | 1.x | **1.0.2.0** | [`dist/ojs33/nusantaraReborn-1.0.2.0.tar.gz`](dist/ojs33/nusantaraReborn-1.0.2.0.tar.gz) | [v1.0.2.0](../../releases/tag/v1.0.2.0) |
| OJS 3.4 (diuji 3.4.0-12) | 3.x | **3.0.0.0** | [`dist/ojs34/nusantaraReborn-3.0.0.0.tar.gz`](dist/ojs34/nusantaraReborn-3.0.0.0.tar.gz) | [v3.0.0.0](../../releases/tag/v3.0.0.0) |
| OJS 3.5 (diuji 3.5.0-6) | 2.x | **2.0.0.0** | [`dist/ojs35/nusantaraReborn-2.0.0.0.tar.gz`](dist/ojs35/nusantaraReborn-2.0.0.0.tar.gz) | [v2.0.0.0](../../releases/tag/v2.0.0.0) |

Semua rilis tersedia di halaman [Releases](../../releases). Checksum SHA-256 setiap paket ada di [`dist/SHA256SUMS`](dist/SHA256SUMS). Versi lama OJS 3.3 (1.0.0.0 dan 1.0.1.0) tetap disimpan di `dist/ojs33/` sebagai arsip; gunakan versi terbaru.

## Fitur

- **Lima preset warna** bernuansa Nusantara: Zamrud Khatulistiwa (hijau-emas), Bahari Nusantara (biru-turkuois), Batik Sogan (cokelat-tembaga), Merah Putih (merah-arang), dan Tenun Senja (nila-terakota). Semua preset lolos uji kontras WCAG 2.1 AA.
- **Ornamen Nusantara** halus di header, hero, dan footer, yang dapat dimatikan. Area baca selalu polos.
- **Font sistem** (tanpa permintaan ke server luar) atau pasangan **Google Fonts** sesuai preset.
- **Beranda lengkap**: hero dengan teks dan tombol aksi, *About the Journal* beserta sorotan profil, kotak *Journal Information*, terbitan terkini, artikel terbaru, pengumuman, dan kartu Editor-in-Chief.
- **Halaman artikel**: panel unduh PDF dan sitasi, informasi artikel (DOI, statistik), tanggal editorial (opsional), penulis korespondensi, lisensi, dan artikel terkait. Bendera negara ditampilkan dari afiliasi penulis.
- **Dewan Redaksi** (OJS 3.3 dan 3.4): daftar HTML berformat Heading 2 dan daftar berpoin otomatis menjadi kartu profil dan tabel berbendera. Di OJS 3.5, *masthead* dan riwayat editorial bawaan OJS ikut ditata.
- **Halaman kebijakan (Custom Page)**: daftar isi *On this page*, bagian bernomor dengan ikon, kotak catatan, dan daftar dua kolom, dibentuk otomatis dari Heading 2.
- **Menu responsif** dari Navigasi OJS, *breadcrumb*, dan penanda halaman aktif.
- **Multibahasa**: antarmuka theme dalam bahasa Indonesia dan Inggris, dan setiap isian teks dapat diisi per bahasa.
- **Data terstruktur** untuk mesin pencari, dan **tanpa klaim palsu**: isian kosong disembunyikan, tidak pernah diganti teks contoh.
- **Keamanan**: konten HTML disaring dengan HTMLPurifier bawaan OJS; skrip, iframe, dan atribut berbahaya selalu dibuang.
- **Khusus OJS 3.5**: afiliasi ROR, ORCID terverifikasi, pernyataan ketersediaan data, grafik statistik penggunaan, dan slider *Highlights*.

## Instalasi singkat

1. Masuk sebagai Journal Manager atau administrator, lalu buka **Settings > Website > Plugins > Upload A New Plugin**.
2. Unggah berkas `.tar.gz` sesuai versi OJS **tanpa mengganti nama berkas**.
3. Aktifkan **NusantaraReborn**, lalu pilih di **Settings > Website > Appearance > Theme** dan klik **Save**.

Pemasangan manual: ekstrak paket ke `plugins/themes/nusantaraReborn/`, lalu jalankan dari root OJS:

```bash
php lib/pkp/tools/installPluginVersion.php plugins/themes/nusantaraReborn/version.xml
```

**Upgrade OJS** (3.3 → 3.4/3.5): ikuti upgrade resmi OJS, lalu ganti seluruh folder theme dengan paket untuk versi OJS baru. Pengaturan theme ikut terbawa. Simpan form pengaturan theme sekali setelah upgrade.

## Dokumentasi

| Dokumen | Isi |
|---|---|
| [Panduan Pemakaian OJS 3.3 (PDF)](docs/Panduan-NusantaraReborn-OJS33.pdf) | Instalasi, menu Navigasi, seluruh isian Appearance, penulisan Dewan Redaksi, dan Custom Page kebijakan, lengkap dengan tangkapan layar. |
| [Panduan Warna Kustom (PDF)](docs/Panduan-Warna-Kustom-NusantaraReborn.pdf) | Mengganti warna theme hanya dengan *Journal style sheet* OJS; berlaku untuk ketiga versi. |
| [Contoh stylesheet warna](docs/nusantara-warna-kustom.css) | Berkas `.css` siap unggah (palet "Ungu Akademik") untuk panduan warna kustom. |
| [Panduan Pengaturan](docs/PANDUAN-PENGATURAN.md) | Ringkasan arti setiap isian theme dan pengaturan OJS terkait, termasuk perbedaan OJS 3.4/3.5 dan daftar periksa. |
| README dan CHANGELOG per versi | [OJS 3.3](ojs33/nusantaraReborn/README.md) · [OJS 3.4](ojs34/nusantaraReborn/README.md) · [OJS 3.5](ojs35/nusantaraReborn/README.md); riwayat perubahan: [3.3](ojs33/nusantaraReborn/CHANGELOG.md) · [3.4](ojs34/nusantaraReborn/CHANGELOG.md) · [3.5](ojs35/nusantaraReborn/CHANGELOG.md) |
| Laporan uji | [OJS 3.3](docs/TEST-REPORT-OJS33.md) · [OJS 3.4](docs/TEST-REPORT-OJS34.md) · [OJS 3.5](docs/TEST-REPORT-OJS35.md) |
| Audit keamanan | [OJS 3.3](docs/SECURITY-AUDIT-OJS33.md) · [OJS 3.4](docs/SECURITY-AUDIT-OJS34.md) · [OJS 3.5](docs/SECURITY-AUDIT-OJS35.md) |
| Rencana pengembangan | [IMPLEMENTATION-PLAN.md](IMPLEMENTATION-PLAN.md) · [PLAN-OJS35.md](docs/PLAN-OJS35.md) |

## Struktur repositori

| Folder/berkas | Isi |
|---|---|
| `dist/` | Paket siap pasang per versi OJS dan `SHA256SUMS`. |
| `ojs33/`, `ojs34/`, `ojs35/` | Kode plugin per versi OJS: kelas PHP, template Smarty, berkas locale, `version.xml`, README, dan CHANGELOG. |
| `shared/` | Bagian yang sama untuk semua versi: `styles/` (CSS dan token preset), `js/`, `assets/` (latar hero, ornamen, sampul bawaan; semuanya orisinal tanpa logo atau lambang), dan `package/` (LICENSE dan NOTICE). |
| `scripts/` | Alat build: `presets.json` (sumber token warna), `build.sh`, uji kontras WCAG, dan pembangkit locale, CSS preset, serta aset visual. |
| `tests/` | Stack Docker lokal dengan **data sintetis** untuk uji runtime (`runtime33`, `runtime34`, `runtime35`) dan uji upgrade (`upgrade34`, `upgrade35`), fixture, dan uji browser Playwright (`e2e/`). |
| `docs/` | Panduan, contoh stylesheet, laporan uji, audit keamanan, dan rencana. |

## Build dan uji (untuk pengembang)

Diperlukan Bash, Python 3, Docker, dan arsip resmi OJS untuk stack uji (tidak disertakan di repositori).

```bash
scripts/build.sh 33|34|35                      # uji kontras, locale, presets.css, tarball + SHA256SUMS
tests/runtime35/setup.sh --reset               # stack uji bersih dengan data sintetis
NR_TRACK=35 python tests/e2e/install_theme.py  # unggah dan aktifkan theme lewat UI (Playwright)
tests/runtime35/verify.sh                      # pemeriksaan penerimaan sisi server
tests/upgrade35/upgrade-test.sh                # fixture upgrade OJS 3.3 -> 3.5
```

Port stack lokal: OJS 3.3 `8099`, OJS 3.4 `8102`, OJS 3.5 `8100`, upgrade 3.4 `8103`, upgrade 3.5 `8101`. Kredensial stack uji dibuat saat setup dan tidak disimpan di repositori.

## Lisensi

- **Kode** (PHP, template, CSS, JavaScript, locale): **GPL-3.0-or-later**, agar kompatibel dengan OJS. Lihat [`shared/package/LICENSE`](shared/package/LICENSE) dan [`LICENSE-GPL-3.0.txt`](shared/package/LICENSE-GPL-3.0.txt).
- **Dokumentasi dan aset visual orisinal**: **CC BY-NC 4.0**, kecuali dinyatakan lain.
- Template bertanda *Derived from* mengadaptasi template OJS (© Simon Fraser University dan John Willinsky, GPL v3). Lihat [`NOTICE.md`](shared/package/NOTICE.md).
- Nama, logo, kebijakan, dan isi jurnal yang ditampilkan theme adalah milik masing-masing jurnal atau penerbit.

Developed by Ikhwan Arief (ikhwan[at]unand.ac.id). Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.

---

## English summary

**NusantaraReborn** is a general scholarly journal theme with an Indonesian character for **OJS 3.3 (series 1.x), OJS 3.4 (3.x) and OJS 3.5 (2.x)**. Download the package that matches your OJS version from [`dist/`](dist/) or [Releases](../../releases), upload it unchanged under *Settings > Website > Plugins*, enable it and select it under *Appearance > Theme*.

Features: five WCAG AA-checked colour presets, optional Nusantara ornaments, system or Google fonts, a full homepage (hero, journal information, current issue, latest articles, announcements, Editor-in-Chief card), an enriched article page, Editorial Team cards and flagged tables, structured policy pages built from Heading 2 sections, a responsive menu from OJS Navigation, Indonesian and English locales, and HTMLPurifier sanitising. Empty settings are hidden and never replaced by sample claims. On OJS 3.5 it also supports ROR affiliations, verified ORCID, data availability, usage charts and Highlights.

Guides (Indonesian): [OJS 3.3 user guide](docs/Panduan-NusantaraReborn-OJS33.pdf), [custom colours with the journal style sheet](docs/Panduan-Warna-Kustom-NusantaraReborn.pdf) (all versions), [settings guide](docs/PANDUAN-PENGATURAN.md). Code: GPL-3.0-or-later; original documentation and visual assets: CC BY-NC 4.0.
