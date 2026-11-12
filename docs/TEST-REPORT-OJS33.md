# Laporan Uji NusantaraReborn 1.0.2.0 — OJS 3.3

Developed by Ikhwan Arief (ikhwan[at]unand.ac.id). Copyright (c) 2026 Ikhwan Arief. Dokumentasi: CC BY-NC 4.0.

**Tanggal:** 11 November 2026 · **Paket:** `dist/ojs33/nusantaraReborn-1.0.2.0.tar.gz`
**SHA-256:** lihat `dist/SHA256SUMS` · **Data:** seluruhnya sintetis (`tests/runtime33/seed/`)

## Lingkungan

| Komponen | Versi |
|---|---|
| OJS | 3.3.0-24 (arsip lokal, SHA-256 terverifikasi) |
| PHP | 8.2.35 (php:8.2-apache) |
| Database | MariaDB 10.11.19 |
| Browser | Google Chrome 157 (Playwright, headless) |
| Plugin bundled aktif | citationStyleLanguage 0.1.0.0, googleScholar 1.1.0.0, dublinCoreMeta 1.0.0.0, customBlockManager 1.2.0.0, staticPages 1.2.0.0, pdfJsViewer 1.0.1.0, usageStats 1.0.0.0, DOI pubId |
| Fixture hook | `tests/fixtures/ojs33/nrHookFixture` (hanya uji, tidak dikemas) |

## Hasil

| Skenario | Status | Bukti / catatan |
|---|---|---|
| PHP lint (PHP 8.2) | Lulus | `php -l` pada semua `.php` paket |
| Kontras WCAG AA lima preset | Lulus | `scripts/check-contrast.py` (teks ≥4.5:1, non-teks ≥3:1) |
| Unggah lewat Plugin Manager, enable, pilih theme via UI | Lulus | `tests/e2e/install_theme.py`, screenshot `install-*` |
| Nama penyimpanan plugin | Lulus | `plugin_settings.plugin_name = nusantararebornthemeplugin` |
| Opsi tersimpan per jurnal, tidak bocor antarjurnal | Lulus | Jurnal 1 Zamrud, jurnal 2 Tenun Senja; data jurnal 1 tidak muncul di jurnal 2 |
| Halaman: beranda, terbitan, arsip, artikel, about, dewan redaksi, kebijakan, pengumuman, pencarian, login, halaman statis | Lulus (HTTP 200) | `issue/current` 302 ke terbitan (perilaku core) |
| Hook: Index::journal, Article::Main/Details/Footer, Common::Footer::PageFooter, header | Lulus | Tiap penanda tampil tepat satu kali |
| Blok sidebar (Custom Block, Information, Language) | Lulus | Tampil sebagai pita di halaman lebar penuh |
| Sitasi CSL, Google Scholar, Dublin Core | Lulus | Panel sitasi tampil sekali; meta `citation_*`/`DC.*` tetap ada |
| Naskah belum terbit tidak tampil | Lulus | Tidak muncul di kartu, artikel terkait, JSON-LD |
| Lokal id_ID | Lulus | `lang="id-ID"`, label theme dan opsi multibahasa berbahasa Indonesia |
| Halaman situs tanpa konteks jurnal (daftar jurnal di root) | Lulus setelah perbaikan | Nama situs sebelumnya kosong di header/footer; kini memakai judul situs OJS. Footer menempel di bawah layar pada halaman pendek |
| Blok panduan + peringatan Navigation di pengaturan theme | Lulus | Tampil di atas opsi; status jumlah item menu benar (10/9); pesan menu kosong muncul saat menu dikosongkan; form tetap tersimpan; tidak ada nilai tersimpan untuk blok ini |
| Perbaikan audit keamanan T-1/T-2/T-3 (1.0.2.0) | Lulus | Lihat `docs/SECURITY-AUDIT-OJS33.md` bagian 6; tanpa overflow/error konsol pada beranda, artikel, arsip, dewan redaksi, kebijakan (375/1440 px) |
| Jurnal kosong (tanpa terbitan/logo/deskripsi) | Lulus | Tanpa error; bagian kosong disembunyikan |
| Escaping opsi (HTML, kutip, Unicode, `javascript:` URL) | Lulus | Skrip ter-escape; URL tak aman dibuang |
| Lima preset × 375/1440 px (beranda, artikel, arsip) | Lulus | Tanpa overflow horizontal, tanpa error konsol |
| Zamrud × 375/768/1440 px (6 halaman) | Lulus | Tanpa overflow |
| Font sistem tidak memuat font eksternal | Lulus | Permintaan eksternal hanya badge CC dari core OJS |
| Mode Google Fonts + ornamen nonaktif | Lulus | Memuat fonts.googleapis.com hanya pada mode ini |
| Paket: satu root, tanpa file tersembunyi/symlink/identitas lama | Lulus | Penjaga di `scripts/build.sh`; isi tarball = salinan terpasang |

## Belum dijalankan / keterbatasan

- Firefox dan Safari belum diuji (hanya Chrome).
- Instalasi manual via `installPluginVersion.php` belum diuji (hanya unggah UI).
- Galley HTML, orcidProfile, dan usageStats pada data riil belum diuji secara visual; metrik memakai data sintetis.
- Navigasi keyboard dan pembaca layar baru diperiksa lewat markup (skip link, `aria-*`), belum uji manual.
- Uji upgrade 3.3 → 3.5 menunggu jalur 3.5.
- Menghapus cache kompilasi Smarty saat ada request berjalan dapat memicu error 500 sesaat ("Invalid compiled template"); ini race cache OJS di stack uji, hilang pada request berikutnya.
- Log PHP berisi *deprecation* dari core OJS 3.3 pada PHP 8.2 (bukan dari theme).
