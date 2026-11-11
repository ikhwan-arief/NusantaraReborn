# NusantaraReborn 3.0.0.0 — OJS 3.4

Theme jurnal ilmiah umum bernuansa Indonesia untuk **OJS 3.4** (diuji pada 3.4.0-12). Paket ini **bukan** untuk OJS 3.3 (gunakan seri 1.x) atau OJS 3.5 (gunakan seri 2.x).

## Instalasi

1. Masuk sebagai administrator atau pengelola jurnal: **Pengaturan > Situs Web > Plugin > Unggah Plugin Baru**.
2. Unggah `nusantaraReborn-3.0.0.0.tar.gz`.
3. Aktifkan **NusantaraReborn**, lalu pilih di **Pengaturan > Situs Web > Tampilan > Theme**.

Instalasi manual: ekstrak sehingga folder menjadi `plugins/themes/nusantaraReborn/`, lalu jalankan `php lib/pkp/tools/installPluginVersion.php plugins/themes/nusantaraReborn/version.xml` dari root OJS.

Upgrade dari NusantaraReborn 1.x (OJS 3.3): ikuti upgrade resmi OJS, lalu **ganti seluruh folder** theme dengan paket ini. Teks multibahasa lama tetap terbaca; simpan form pengaturan theme sekali setelah upgrade.

## Opsi utama

| Opsi | Nilai | Bawaan |
|---|---|---|
| Preset warna | Zamrud Khatulistiwa, Bahari Nusantara, Batik Sogan, Merah Putih, Tenun Senja | Zamrud Khatulistiwa |
| Ornamen Nusantara | Halus / Nonaktif | Halus |
| Sumber font | Font sistem / Google Fonts | Font sistem |
| Statistik penggunaan (dari Default Theme) | Nonaktif / Batang / Garis | Nonaktif |

Nama, logo, favicon, gambar beranda, penerbit, dewan redaksi, dan kebijakan jurnal diambil dari pengaturan OJS. Baris informasi hanya tampil jika diisi.

## English summary

General scholarly theme with an Indonesian character for **OJS 3.4** (tested on 3.4.0-12). Upload through *Settings > Website > Plugins*, enable it, then select it under *Appearance > Theme*. Keeps OJS 3.4 features: data availability, usage statistics chart, CSL citations through its plugin hook, ORCID from the orcidProfile plugin; the Editorial Team text is shown as profile cards and tables.

Developed by Ikhwan Arief (ikhwan[at]unand.ac.id). Copyright (c) 2026 Ikhwan Arief. Code: GPL-3.0-or-later; original docs and visual assets: CC BY-NC 4.0. See LICENSE and NOTICE.md.
