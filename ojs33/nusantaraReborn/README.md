# NusantaraReborn 1.0.1.0 — OJS 3.3

Theme jurnal ilmiah umum bernuansa Indonesia untuk **OJS 3.3** (diuji pada 3.3.0-24). Paket ini **bukan** untuk OJS 3.4/3.5; gunakan paket seri 2.x untuk OJS 3.5.

## Instalasi

1. Masuk sebagai administrator atau pengelola jurnal: **Pengaturan > Situs Web > Plugin > Unggah Plugin Baru**.
2. Unggah `nusantaraReborn-1.0.1.0.tar.gz` tanpa mengganti nama berkas (OJS 3.3 membaca nama plugin dari awal nama arsip).
3. Aktifkan **NusantaraReborn** pada daftar plugin, lalu pilih di **Pengaturan > Situs Web > Tampilan > Theme**.

Instalasi manual: ekstrak sehingga folder menjadi `plugins/themes/nusantaraReborn/`, lalu jalankan `php lib/pkp/tools/installPluginVersion.php plugins/themes/nusantaraReborn/version.xml` dari root OJS.

## Opsi utama

| Opsi | Nilai | Bawaan |
|---|---|---|
| Preset warna | Zamrud Khatulistiwa, Bahari Nusantara, Batik Sogan, Merah Putih, Tenun Senja | Zamrud Khatulistiwa |
| Ornamen Nusantara | Halus / Nonaktif | Halus |
| Sumber font | Font sistem / Google Fonts | Font sistem |

Nama, logo, favicon, gambar beranda, penerbit, dan kebijakan jurnal diambil dari pengaturan OJS. Baris informasi (frekuensi, proses telaah, APC, pengindeksan, dll.) hanya tampil jika diisi.

## English summary

General scholarly theme with an Indonesian character for **OJS 3.3** (tested on 3.3.0-24). Upload the archive unchanged through *Settings > Website > Plugins*, enable it, then select it under *Appearance > Theme*. Five presets, optional ornaments, system or Google fonts. Journal identity always comes from OJS.

Developed by Ikhwan Arief (ikhwan[at]unand.ac.id). Copyright (c) 2026 Ikhwan Arief. Code: GPL-3.0-or-later; original docs and visual assets: CC BY-NC 4.0. See LICENSE and NOTICE.md.
