# Rencana Port NusantaraReborn ke OJS 3.5 (Tahap D)

**Tanggal:** 11 November 2026
**Status:** Dilaksanakan — NusantaraReborn 2.0.0.0 dibangun dan diuji (lihat `docs/TEST-REPORT-OJS35.md`, `docs/SECURITY-AUDIT-OJS35.md`). Keputusan §10 disetujui sesuai usulan.
**Acuan induk:** `IMPLEMENTATION-PLAN.md` (§4, §7, §8.2–8.3, §11.2, §12 Tahap D–F, §13, §14)
**Pengembang:** Ikhwan Arief (ikhwan[at]unand.ac.id). Copyright (c) 2026 Ikhwan Arief. Dokumentasi: CC BY-NC 4.0.

Dokumen ini merinci pelaksanaan jalur OJS 3.5 berdasarkan kondisi akhir jalur 3.3 dan inspeksi statis arsip `ojs-3.5.0-6.tar.gz`. Inspeksi dilakukan dengan mengekstrak arsip ke folder sementara di luar proyek, tanpa menjalankan PHP OJS dan tanpa mengubah isi arsip.

---

## 1. Titik awal

### 1.1 Status jalur 3.3

| Item | Status |
|---|---|
| Versi terbaru | 1.0.2.0, rilis GitHub `v1.0.2.0` (Latest); arsip `v1.0.1.0` dan `v1.0.0.0` |
| Pengujian | OJS 3.3.0-24, PHP 8.2.35, MariaDB 10.11, Chrome; laporan `docs/TEST-REPORT-OJS33.md` |
| Audit keamanan | `docs/SECURITY-AUDIT-OJS33.md`; T-1, T-2, T-3 diperbaiki di 1.0.2.0 |
| Panduan pengguna | `docs/PANDUAN-PENGATURAN.md`; blok panduan + peringatan Navigation di pengaturan theme |
| Repo | `ikhwan-arief/NusantaraReborn` (publik), cabang `main` bersih |

### 1.2 Aset yang dipakai ulang tanpa perubahan

- `shared/styles/nusantara.css` dan `shared/styles/presets.css` (hasil `scripts/make-presets-css.py`).
- `shared/js/nusantara.js`.
- `shared/assets/` (heroes, covers, ornaments, manifest) dan `shared/package/` (LICENSE, GPL, NOTICE).
- `scripts/presets.json`, `scripts/check-contrast.py`, `scripts/make-visuals.py`.
- `scripts/make-locales.py`, yang sudah memiliki target `35` (locale `id` dan `en`).
- `docs/PANDUAN-PENGATURAN.md`. Isinya perlu disesuaikan untuk 3.5 (lihat §9).

### 1.3 Baseline arsip 3.5 yang diverifikasi

| Item | Nilai |
|---|---|
| Arsip | `ojs-3.5.0-6.tar.gz`, SHA-256 `f5b85703…627c97` (cocok dengan rencana induk §4.1) |
| Versi internal | `3.5.0.6` (`dbscripts/xml/version.xml`) |
| PHP | `^8.2` (`lib/pkp/composer.json`) |
| Smarty | 4.5.5 |
| Bahasa tersedia | `en`, `id`, dan lainnya (tanpa sufiks wilayah) |
| Plugin generic bawaan | announcementFeed, citationStyleLanguage, **credit**, **crossref**, **datacite**, customBlockManager, driver, dublinCoreMeta, googleAnalytics, googleScholar, htmlArticleGalley, **jatsTemplate**, lensGalley, pdfJsViewer, **pflPlugin**, recommendByAuthor, recommendBySimilarity, staticPages, tinymce, usageEvent, webFeed |

---

## 2. Keputusan teknis yang sudah ditetapkan rencana induk

| Elemen | Nilai 3.5 |
|---|---|
| Folder sumber | `ojs35/nusantaraReborn/` |
| Versi awal | **2.0.0.0** |
| Class | `NusantaraRebornThemePlugin`, berkas `NusantaraRebornThemePlugin.php` |
| Namespace | `APP\plugins\themes\nusantaraReborn` |
| Loader `index.php` | Mengembalikan instance class bernamespace secara langsung, mengikuti Default Theme 3.5 |
| `version.xml` | `application` = `nusantaraReborn`; `class` = nama pendek `NusantaraRebornThemePlugin` (pola Default Theme 3.5) |
| Nama penyimpanan opsi | `nusantararebornthemeplugin`, sama dengan 3.3. Terbukti dari `LazyLoadPlugin::getName()` 3.5, yang mengambil nama class tanpa namespace lalu mengecilkan hurufnya |
| Parent | `defaultthemeplugin` |
| Locale | `locale/id/locale.po`, `locale/en/locale.po` |
| Paket | `dist/ojs35/nusantaraReborn-2.0.0.0.tar.gz`, satu root `nusantaraReborn/` |

---

## 3. Hasil inspeksi: perubahan API 3.3 ke 3.5

Tabel ini memetakan setiap akses data di `NusantaraRebornThemePlugin.inc.php` 3.3 ke padanannya di 3.5.

| Pemakaian di theme 3.3 | Status di 3.5 | Arah port |
|---|---|---|
| `import()` dan `HookRegistry::register` | Dihapus | `use` namespace; `PKP\plugins\Hook::add` |
| `AppLocale::getLocale()`, `getAllLocales()` | Dihapus | Facade `PKP\facades\Locale` |
| `EditDecisionDAO::getEditorDecisions()` | **Kelas tidak ada** | `Repo::decision()->getCollector()->filterBySubmissionIds()->filterByDecisionTypes()` |
| Konstanta `SUBMISSION_EDITOR_DECISION_*` | Diganti `PKP\decision\Decision::*` dengan **nilai berbeda** (ACCEPT 1→2, PENDING_REVISIONS 2→4, RESUBMIT 3→5) | Gunakan konstanta kelas; jangan menulis angka. Verifikasi data hasil upgrade 3.3→3.5 (§7.4) |
| `SubmissionKeywordDAO::getKeywords()` | **Kelas tidak ada** | Keyword dari `publication.keywords` (array objek dengan `name`) |
| `Services::get('submission')->getMany([...])` | Diganti Collector | `Repo::submission()->getCollector()` (`filterByContextIds`, `filterByIssueIds`, `filterByStatus`, `orderBy`) |
| `DAORegistry::getDAO('IssueDAO')->getById()` | Repository | `Repo::issue()->get()`; `customIssueOrderingExists` masih ada di DAO isu |
| `DAORegistry::getDAO('SectionDAO')` | Repository | `Repo::section()->get()` |
| `Services::get('file')->fs->getSize()` | Flysystem 3: `getSize` **tidak ada** | `fileSize()`; tetap dibungkus `try/catch` |
| `$article->getViews()`, `$galley->getViews()` | **Tidak ada** | Layanan statistik `getTotalsByType()`; atau serahkan ke grafik `displayStats` native (keputusan §10 D-35-3) |
| Afiliasi string `getLocalizedData('affiliation')` (penulis) | Penulis memakai `getAffiliations()` (model, dengan ROR) | Render semua afiliasi + ROR; JSON-LD memakai model yang sama |
| `getStoredPubId('doi')` | Masih ada; template core memakai `doiObject` | Gunakan `getDoi()` / `doiObject` sesuai template target |
| `$smarty.const.ROUTE_PAGE`, `STATUS_PUBLISHED`, `PUBLISHING_MODE_OPEN` | Konstanta global tidak tersedia | Konstanta kelas di template (pola core: `PKP\core\PKPApplication::ROUTE_PAGE`, `PKP\submission\PKPSubmission::STATUS_PUBLISHED`, `APP\journal\Journal::PUBLISHING_MODE_*`) |
| Pengumuman: `getAnnouncementTypeName()`, `getLocalizedDescriptionShort()`, `getDatePosted()` | Model Eloquent | `getLocalizedData('title' / 'descriptionShort')`, `datePosted` (objek tanggal); nama tipe perlu dicek di D0 |
| Sokil `IsoCodesFactory` langsung | Tersedia via facade | `Locale::getCountries()` |
| `NavigationMenuDAO::getByArea`, `NavigationMenuItemAssignmentDAO::getByMenuId` | Masih ada | Dipakai ulang untuk blok panduan |
| `HTMLPurifier` (sanitasi T-1) | Masih dibundel; `PKPString::stripUnsafeHtml` kini menerima kunci konfigurasi | Pertahankan `richHtml()`; daftar `allowed_html` bawaan 3.5 **sama sempitnya** dengan 3.3 (tanpa heading/tabel) |
| `FieldHTML` (blok panduan) | Tersedia `PKP\components\forms\FieldHTML` | Dipakai ulang |
| `Application::getCurrentVersion()`, `getCCLicenseBadge()` | Masih ada | Dipakai ulang |
| `getCountryLocalized()` | Masih ada | Dipakai ulang |

---

## 4. Perubahan Default Theme dan template target

### 4.1 Default Theme 3.5

- Opsi baru **`displayStats`** (`none`/`bar`/`line`). Grafik dipanggil dari template artikel melalui `$activeTheme->displayUsageStatsGraph(...)`, dan skrip `lib/pkp/js/usage-stats-chart.js` dimuat oleh `ThemePlugin`. **Theme tidak boleh menghapus opsi ini**, dan override artikel wajib mempertahankan pemanggilannya.
- Aset baru: **Swiper** (CSS+JS) untuk komponen *Highlights* beranda; jQuery dan jQuery UI kini dari `js/build/`.
- Opsi lama (`typography`, `baseColour`, `useHomepageImageAsHeader`, `showDescriptionInJournalIndex`) masih ada. Theme menghapusnya seperti di 3.3, berikut gaya `font` dan `homepageImage`.
- `isActive()` Default Theme mengembalikan `true` saat sesi nonaktif. `ThemePlugin::isActive()` dasar mengembalikan `false`. Perilaku ini perlu diuji untuk permintaan tanpa sesi (API, CLI).

### 4.2 Template yang di-override di 3.3 dan statusnya di 3.5

| Template 3.3 theme | Sumber core 3.5 | Perbedaan utama 3.3 → 3.5 | Strategi 3.5 |
|---|---|---|---|
| `components/headerHead.tpl` | pkp | Tidak berubah | Port langsung |
| `components/header.tpl` | pkp | Hanya `ROUTE_PAGE` | Port; ganti konstanta |
| `components/footer.tpl` | pkp | `aria-label` sidebar dihapus core; anotasi `@hook` | Port; pertahankan `aria-label` (lebih aksesibel) |
| `pages/indexJournal.tpl` | app | **Komponen `highlights.tpl`** baru; pengumuman memakai `announcements_list.tpl` | Bangun ulang dari template 3.5 (§4.3) |
| `pages/article.tpl` | app | Judul memakai format `html` + `strip_unsafe_html` | Ikuti pola 3.5 |
| `objects/article_details.tpl` | app | Afiliasi + ROR, ORCID terverifikasi, `doiObject`, keyword `name`, `displayStats`, `dataAvailability`, `userGroupsById`, sitasi lewat hook CSL | **Bangun ulang dari template 3.5**, terapkan komponen desain NR |
| `objects/issue_summary.tpl` | app | Tidak berubah | Port langsung |
| `pages/issueArchive.tpl` | app | Hanya `ROUTE_PAGE` | Port; ganti konstanta |
| `pages/editorialTeam.tpl` | **tidak ada** | Diganti `editorialMasthead.tpl`, `editorialMastheadDisabled.tpl`, `editorialHistory.tpl` | **Tidak di-port.** Warisi template native; beri styling saja (§4.4) |
| `pages/navigationMenuItemViewContent.tpl` | pkp | Tidak berubah | Port langsung; tetap disanitasi `richHtml()` |
| `objects/nr_article_card.tpl` | milik theme | — | Port; ganti konstanta dan API keyword/afiliasi |

Hook yang wajib tetap muncul tepat sekali: `Templates::Index::journal`, `Templates::Article::Main`, `Templates::Article::Details`, `Templates::Article::Details::Reference`, `Templates::Article::Footer::PageFooter`, `Templates::Common::Sidebar`, `Templates::Common::Footer::PageFooter`. Semuanya masih ada di template 3.5.

### 4.3 Beranda 3.5

- **Highlights** (fitur core baru, slider Swiper) ditampilkan di bawah hero NR, memakai komponen core `frontend/components/highlights.tpl` agar data, aksesibilitas, dan terjemahan Swiper tetap dari core. Theme hanya memberi styling.
- **Pengumuman:** pertahankan tampilan unggulan + daftar dari 3.3, dengan sumber data model Eloquent. Alternatifnya memakai `announcements_list.tpl` core (keputusan D-35-2).
- **Isu terkini, artikel terbaru, kotak informasi, kartu Editor-in-Chief, konten tambahan:** paritas dengan 3.3. Kartu artikel diambil dari `publishedSubmissions` yang masih disediakan `IssueHandler`.

### 4.4 Masthead dan riwayat editorial

- Core 3.5 membangun masthead dari **peran pengguna** (`$mastheadRoles`, `$mastheadUsers`), bukan dari HTML bebas. Fitur kartu/tabel 3.3 yang mem-parsing HTML lewat JavaScript **tidak diperlukan**, dan pemetaan bendera negara di masthead tidak dibawa.
- Theme menyediakan CSS untuk masthead, riwayat editorial (`about/editorialHistory`), daftar penelaah tahun sebelumnya (`$reviewers`, `$previousYear`), dan halaman masthead nonaktif.
- Afiliasi **pengguna** di masthead masih dibaca core sebagai `getLocalizedData('affiliation')`. Afiliasi **penulis** memakai model baru. Jangan menyamakan keduanya (§8.3 rencana induk).
- Tidak membuat query penelaah sendiri.

---

## 5. Fitur baru 3.5 yang harus dipertahankan

| Fitur | Sumber | Rencana theme |
|---|---|---|
| Afiliasi ganda + ROR penulis | `getAffiliations()`, `getRor()`, ikon `$rorIdIcon` | Daftar afiliasi bernomor seperti 3.3, ditambah tautan ROR; JSON-LD `affiliation` sebagai `Organization` dengan `sameAs` ROR |
| ORCID terverifikasi | `hasVerifiedOrcid()`, `getOrcidDisplayValue()`, `$orcidIcon` | Bedakan ikon terverifikasi/tidak, sesuai core |
| Data Availability Statement | `publication.dataAvailability` | Bagian tersendiri setelah referensi, `strip_unsafe_html` |
| Statistik `displayStats` | Default Theme + `ThemePlugin` | Pertahankan opsi parent dan grafik native |
| Sitasi CSL | Hook `Templates::Article::Details` (`addCitationMarkup`) | **Tidak menyalin blok sitasi inline 3.3.** Blok CSL dari plugin muncul sekali di kolom samping; theme hanya menata CSS |
| CRediT | plugin `credit` | Uji keluaran peran kontributor bila diaktifkan |
| Publication Facts Label | `pflPlugin` | Uji sisipan lewat hook; tidak tertutup layout |
| Highlights | core | §4.3 |
| Judul berformat (`getLocalizedTitle(null, 'html')`) | core | Tampilkan dengan `strip_unsafe_html`; untuk meta tag dan JSON-LD gunakan versi teks |

---

## 6. Rencana kode (garis besar, belum dikerjakan)

1. **Kerangka plugin 3.5:** loader, class bernamespace, `version.xml`, locale `id`/`en` dari `make-locales.py 35`.
2. **Opsi:** identik dengan 3.3 (nama dan nilai sama). Opsi `displayStats` parent tidak dihapus. Blok `FieldHTML` panduan dipertahankan.
3. **Pemuatan aset:** sama dengan 3.3. Pastikan Swiper dan jQuery parent tetap dimuat sekali, dan mode font sistem tidak memuat font eksternal.
4. **Port akses data** sesuai tabel §3, tanpa *fallback* ke API lama yang menyembunyikan kegagalan.
5. **Template** sesuai tabel §4.2.
6. **SEO/JSON-LD:** port dengan afiliasi model, DOI `getDoi()`, keyword `name`, judul teks. Tetap tanpa identitas fallback dan tanpa konten belum terbit.
7. **Keamanan:** bawa semua kontrol audit 3.3 (escape opsi, `safeUrl`, `richHtml`, `referrerpolicy`, opsi tanggal editorial).
8. **CSS tambahan** di `shared/styles/nusantara.css`, tetap berprefiks `nr_` atau terbatas di `body.nr_theme`: masthead, riwayat editorial, highlights/Swiper, grafik statistik, ROR/ORCID, data availability, CRediT, PFL.
9. **Build:** `scripts/build.sh 35` dengan daftar putih terpisah (berkas `.php`, bukan `.inc.php`); penjaga identitas lama dan berkas tersembunyi tetap aktif.

---

## 7. Lingkungan dan data uji 3.5

### 7.1 Stack Docker

- Folder `tests/runtime35/` dengan pola yang sama seperti `runtime33`: Dockerfile `php:8.2-apache` + ekstensi `bcmath gd intl mbstring xml zip mysqli pdo_mysql`, MariaDB 10.11, port **127.0.0.1:8100**, nama proyek `nr35-test`.
- Kredensial acak di `.env` dan `credentials.txt` (sudah di `.gitignore`).
- Verifikasi hash arsip sebelum ekstraksi; tidak menjalankan `composer` atau `npm`.
- Theme dipasang dari tarball melalui Plugin Manager, seperti uji 3.3.

### 7.2 Data sintetis

Generator Native XML baru untuk 3.5 (locale `en`/`id`):

- Dua jurnal: dwibahasa lengkap, dan jurnal kosong.
- Penulis dengan **afiliasi ganda, dengan dan tanpa ROR**, nama non-ASCII, ORCID terverifikasi dan tidak.
- Data availability statement; judul dengan format HTML (*italic*); judul sangat panjang.
- Artikel tanpa galley; naskah antrean (`[UNPUBLISHED]`).
- Keputusan editorial sintetis melalui tabel `edit_decisions` 3.5, dengan konstanta baru.
- Masthead: pengguna dengan peran editor dan penelaah tahun lalu; uji masthead aktif dan nonaktif.
- Highlights, pengumuman, Custom Page kebijakan, dan blok kustom.
- Metrik sintetis untuk grafik `displayStats`.
- Plugin fixture hook 3.5 (namespace, `Hook::add`) yang hanya untuk uji.

### 7.3 Plugin yang diaktifkan untuk uji

citationStyleLanguage, googleScholar, dublinCoreMeta, customBlockManager, staticPages, pdfJsViewer, htmlArticleGalley, credit, pflPlugin, plugin DOI. crossref/datacite hanya untuk verifikasi tautan lokal, **tanpa deposit eksternal**.

### 7.4 Fixture upgrade 3.3 → 3.5

1. Salin stack 3.3 (data sintetis + NusantaraReborn 1.0.2.0 aktif, dengan opsi multibahasa terisi `en_US`/`id_ID`).
2. Catat `plugin_settings` NR sebelum upgrade.
3. Pilih Default Theme, jalankan upgrade resmi OJS ke 3.5.0-6, ganti seluruh folder theme dengan paket 2.0.0.0, aktifkan kembali.
4. Bandingkan opsi sesudah upgrade: apakah kunci locale di dalam nilai JSON opsi multibahasa ikut berubah `en_US→en`, `id_ID→id`. Bila tidak, dokumentasikan pengisian ulang via UI; **tidak** menambah migrasi database (rencana induk §11.2).
5. Verifikasi tanggal editorial setelah renumbering konstanta keputusan.

---

## 8. Rencana pengujian 3.5

Menggunakan matriks §13 rencana induk, ditambah skenario wajib 3.5:

| Area | Skenario |
|---|---|
| Statis | PHP lint 8.2; validasi namespace/`version.xml`/locale; kontras; daftar isi tarball |
| Instalasi | Unggah Plugin Manager (PharData, cari `version.xml`), enable, pilih theme, simpan opsi; instalasi manual `installPluginVersion.php` |
| Penyimpanan | Nama `nusantararebornthemeplugin` sama dengan 3.3; opsi per jurnal tidak bocor |
| Halaman | Beranda (dengan/tanpa highlights), isu, arsip, artikel, masthead aktif/nonaktif, riwayat editorial, kebijakan, pengumuman, pencarian, login, halaman situs |
| Artikel | Afiliasi ganda ±ROR, ORCID terverifikasi/tidak, data availability, CSL aktif/nonaktif (tidak ganda), statistik `none/bar/line`, CRediT, PFL, hook fixture |
| Keamanan | Ulangi uji 3.3: opsi berbahaya, `javascript:` URL, skrip di masthead/Custom Page/license terms, naskah belum terbit di kartu dan JSON-LD |
| Visual | 5 preset × 375/768/1440; ornamen on/off; font sistem/Google; tanpa overflow; konsol bersih |
| Upgrade | §7.4 |
| Paket | Uji ulang dari tarball final, bukan dari folder sumber |

Laporan: `docs/TEST-REPORT-OJS35.md`. Audit ulang: `docs/SECURITY-AUDIT-OJS35.md` dengan metode yang sama seperti 3.3.

---

## 9. Dokumentasi yang diperbarui

- `docs/PANDUAN-PENGATURAN.md`: tambah bagian 3.5 (Highlights, `displayStats`, masthead berbasis peran, afiliasi/ROR, data availability). Lokasi menu UI 3.5 dicek ulang terhadap antarmuka.
- `README.md` proyek dan paket 3.5; `ojs35/nusantaraReborn/CHANGELOG.md`.
- Panduan upgrade 3.3 → 3.5 (rencana induk §11.2 + hasil fixture §7.4).
- Matriks kompatibilitas: upstream vs runtime yang benar-benar diuji.

---

## 10. Keputusan yang perlu disetujui pemilik produk

| ID | Pertanyaan | Usulan |
|---|---|---|
| D-35-1 | Tampilkan Highlights core di beranda NR? | Ya, di bawah hero, memakai komponen core |
| D-35-2 | Pengumuman beranda: desain NR 3.3 atau komponen core? | Desain NR 3.3 dengan data model 3.5 (konsisten antarversi) |
| D-35-3 | Angka "Dilihat/Unduhan" di kotak Informasi Artikel | Hitung dari layanan statistik 3.5; grafik `displayStats` tetap dari core. Alternatif: buang angka dan andalkan grafik saja |
| D-35-4 | Masthead: hanya styling, atau override template? | Hanya styling (rencana induk §8.3) |
| D-35-5 | Format tag rilis | `v2.0.0.0`, paket `dist/ojs35/`; rilis 3.3 tetap pada seri 1.x |
| D-35-6 | Jurnal berbahasa Indonesia sebagai *primary locale* di fixture | Ya untuk jurnal kedua, agar format tanggal dan label `id` teruji |

---

## 11. Tahapan dan kriteria selesai

| Tahap | Pekerjaan | Selesai apabila |
|---|---|---|
| **D0** Verifikasi | Cek API yang belum pasti: nama tipe pengumuman (Eloquent), bentuk `publication.galleys` (koleksi/array), perilaku `isActive()` tanpa sesi, akses `$activeTheme` saat child theme aktif, tabel `edit_decisions` pasca-upgrade | Semua baris "verifikasi" di §3 terjawab dengan bukti source |
| **D1** Kerangka | Loader, class, `version.xml`, locale, build `35`, lint | Theme bisa dipasang dan dipilih tanpa error, halaman memakai template parent |
| **D2** Data | Port akses data sesuai §3 | Tidak ada pemanggilan API 3.3 yang dihapus; PHP log bersih pada semua halaman |
| **D3** Template | Port/bangun ulang sesuai §4.2; CSS tambahan | Paritas fitur dengan 3.3 + fitur §5; hook tepat sekali |
| **D4** Stack uji | `tests/runtime35`, data sintetis, fixture hook, e2e | Stack terpasang ulang dari nol dengan satu skrip |
| **D5** Verifikasi | Matriks §8, audit keamanan ulang, fixture upgrade | Tidak ada kegagalan kritis; hasil tercatat |
| **D6** Rilis | Paket 2.0.0.0, checksum, dokumentasi, commit, push, release (setelah otorisasi) | Paket terpasang dari tarball final; dokumentasi sesuai hasil |

---

## 12. Risiko

| Risiko | Penanganan |
|---|---|
| Template artikel 3.5 jauh berbeda; override lama menghilangkan fitur baru | Bangun dari template 3.5, bukan dari override 3.3; checklist §5 |
| Renumbering konstanta keputusan menghasilkan tanggal salah | Konstanta kelas; uji pada fixture upgrade |
| Opsi multibahasa tidak ikut migrasi locale | Uji §7.4; dokumentasi pengisian ulang |
| Panel CSL ganda atau hilang | Tidak menyalin blok inline; uji CSL aktif/nonaktif |
| Swiper/Highlights rusak oleh CSS theme | Gaya terbatas pada wrapper NR; uji seluler |
| Penyaringan `richHtml()` terlalu ketat/longgar di 3.5 | Uji ulang dengan muatan audit; bandingkan `allowed_html` 3.5 |
| Permintaan tanpa sesi (API/CLI) memicu kode theme | Uji `isActive()` dan keluar lebih awal tanpa konteks |
| Campuran berkas 3.3 dan 3.5 saat upgrade | Instruksi ganti folder penuh; paket dengan root tunggal |

---

## 13. Batas otorisasi

- Dokumen ini tidak mengubah kode, template, skrip, atau data.
- Pembuatan kode jalur 3.5, stack Docker baru, commit, push, dan rilis dilakukan setelah persetujuan pemilik produk atas rencana ini dan keputusan §10.
- Arsip `ojs-3.5.0-6.tar.gz` tetap referensi lokal read-only, tidak dikemas, dan tidak di-commit.
