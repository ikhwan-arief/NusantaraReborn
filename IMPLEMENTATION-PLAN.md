# Rencana Implementasi NusantaraReborn

**Tanggal rencana:** 9 November 2026  
**Revisi:** 2 — berdasarkan inspeksi statis dua arsip OJS lokal  
**Status:** Rencana implementasi; belum merupakan bukti implementasi atau kelulusan pengujian  
**Nama produk:** NusantaraReborn  
**Pengembang dan pemegang hak cipta first-party:** Ikhwan Arief  
**Kontak pengembang:** ikhwan[at]unand.ac.id

## 1. Tujuan

Mengembangkan Unand Journal Theme menjadi **NusantaraReborn**, theme jurnal umum bernuansa Indonesia, tanpa keterikatan identitas Universitas Andalas. Theme harus mempertahankan fungsi OJS dan mendukung integrasi plugin melalui mekanisme ekstensi resmi OJS.

Hasil utama adalah dua paket instalasi berbeda: satu untuk OJS 3.3 terbaru dan satu untuk OJS 3.5 terbaru. Identitas Nusantara hadir melalui warna, ornamen, tipografi, serta penyajian halaman; nama jurnal, penerbit, kebijakan, dan metadata tetap berasal dari jurnal bersangkutan.

Dokumen ini menjadi acuan implementasi, pengujian, dokumentasi, dan packaging. Pembuatan dokumen ini tidak menginisialisasi Git, memindahkan sumber lama, atau menjalankan implementasi theme.

## 2. Keputusan yang telah disepakati

1. Nama produk dan nama tampilan adalah **NusantaraReborn**.
2. Theme ditujukan untuk jurnal umum, tidak terikat universitas, penerbit, bidang ilmu, atau wilayah tertentu.
3. Lima preset menggunakan nuansa Indonesia. Keputusan ini menggantikan rencana awal yang hanya menggunakan lima preset netral.
4. Instalasi merupakan plugin baru; tidak ada migrasi otomatis dari Unand Journal Theme.
5. Pengguna telah menegaskan bahwa hak atas materi first-party sepenuhnya milik atau telah dialihkan kepada Ikhwan Arief. Perubahan pemberitahuan kepemilikan mengikuti konfirmasi tersebut.
6. Lisensi kode tetap GPL-3.0-or-later; dokumentasi dan aset visual orisinal tetap CC BY-NC 4.0. Lisensi dan atribusi pihak ketiga tetap dipertahankan.
7. Alamat ikhwan[at]unand.ac.id tetap menjadi kontak pengembang pada atribusi/lisensi, bukan identitas penerbit jurnal.
8. Fitur OJS dan plugin tetap menggunakan implementasi OJS; theme tidak mengambil alih workflow atau mengganti core.
9. Hasil pekerjaan berupa paket lokal. Commit, push, publikasi, deployment, instalasi dependency, dan operasi lain yang dibatasi aturan proyek memerlukan otorisasi yang sesuai.

## 3. Kondisi awal dan batas pekerjaan

### 3.1 Sumber awal

- Direktori sumber awal: `../unandJournalTheme/work/unandJournalTheme/` relatif terhadap folder proyek baru ini.
- Versi sumber yang diperiksa: **1.2.0.0**.
- Sumber menggunakan child theme dari `defaultthemeplugin`.
- Dokumentasi lama menyebut pengujian OJS **3.3.0-23**, PHP 7.4, MariaDB 10.6. Klaim tersebut merupakan catatan historis dan tidak menjadi bukti pengujian NusantaraReborn.
- Direktori sumber yang diperiksa bukan repositori Git.
- Identitas institusi ditemukan pada class/plugin ID, locale, CSS/template, metadata penerbit, URL fallback, logo, favicon, gambar hero, dokumentasi, dan pemberitahuan lisensi.
- Kode menggunakan `import()`, `HookRegistry`, `DAORegistry`, serta `Services`; akses tersebut perlu dianalisis per versi saat porting.

### 3.2 Termasuk dalam pekerjaan

- Rebranding menyeluruh pada distribusi baru.
- Desain lima preset Indonesia dan pengaturan presentasi.
- Port kode dan template untuk dua jalur OJS.
- Pengamanan keluaran dan pelestarian integrasi plugin.
- Pengujian instalasi, fungsi, visual, aksesibilitas, metadata, serta packaging.
- Panduan instalasi, konfigurasi, perpindahan versi, dan laporan hasil uji.

### 3.3 Tidak termasuk

- Mengubah core OJS atau skema database.
- Membuat workflow editorial, sistem pembayaran, atau pengelolaan publikasi pengganti.
- Migrasi otomatis dari theme lama.
- Menyatakan dukungan OJS 3.4 atau semua patch lama tanpa pengujian.
- Mengubah data penerbit atau isi jurnal pengguna secara massal.
- Menjamin kompatibilitas seluruh plugin pihak ketiga atau menjamin penerimaan jurnal oleh pengindeks.

## 4. Target versi dan persyaratan

Target dibekukan berdasarkan pemeriksaan sumber resmi pada **9 November 2026**. Jika implementasi dimulai setelah ada patch baru, verifikasi kembali versi terbaru, perbarui matriks target dan catatan sumber sebelum pengujian final.

| Aspek | Jalur OJS 3.3 | Jalur OJS 3.5 |
|---|---|---|
| Target OJS | 3.3.0-24 LTS | 3.5.0-6 LTS |
| Versi awal theme | 1.0.0.0 | 2.0.0.0 |
| Runtime uji utama | PHP 8.2, MariaDB 10.11 | PHP 8.2, MariaDB 10.11 |
| Parent | Default Theme target | Default Theme target |
| Class plugin | Legacy `.inc.php` | Namespaced `.php` |
| Locale | `id_ID`, `en_US` | `id`, `en` |
| Paket | `nusantaraReborn-1.0.0.0.tar.gz` | `nusantaraReborn-2.0.0.0.tar.gz` |

Halaman unduhan PKP mencantumkan kedua target sebagai rilis terbaru pada jalurnya, tersedia sejak 1 November 2026. README tag 3.3.0-24 mencantumkan tanggal internal 30 Oktober 2026; gunakan tanggal ketersediaan dari halaman unduhan jika menjelaskan rilis publik.

Dokumentasi OJS 3.3.0-24 mencantumkan PHP 7.3–8.2 dan MySQL/PostgreSQL; dokumentasi OJS 3.5 mensyaratkan PHP minimal 8.2. Matriks di atas merupakan lingkungan uji yang dipilih, bukan klaim bahwa seluruh kombinasi upstream telah diuji. Paket lokal 3.5 menyatakan PHP `^8.2` serta ekstensi langsung `bcmath`, `gd`, `intl`, `mbstring`, `xml`, dan `zip` dalam `lib/pkp/composer.json`. Daftar ini bukan seluruh kebutuhan transitif; periksa platform requirements paket/vendor dan driver database sebelum menjalankan fixture. Theme tidak menambah dependency server baru di luar kebutuhan OJS.

Sumber primer:

- [Unduhan resmi OJS](https://pkp.sfu.ca/software/ojs/download/).
- [Persyaratan OJS 3.3.0-24](https://github.com/pkp/ojs/blob/3_3_0-24/docs/README.md).
- [Persyaratan OJS 3.5.0-6](https://github.com/pkp/ojs/blob/3_5_0-6/README.md).
- [Perubahan pengembang OJS 3.4](https://github.com/pkp/pkp-docs/blob/main/dev/release-notebooks/en/3.4-release-notebook.md).
- [Perubahan pengembang OJS 3.5](https://github.com/pkp/pkp-docs/blob/main/dev/release-notebooks/en/3.5-release-notebook.md).
- [Default Theme OJS 3.5.0-6](https://github.com/pkp/ojs/blob/3_5_0-6/plugins/themes/default/DefaultThemePlugin.php).

### 4.1 Baseline arsip lokal yang telah diperiksa

Kedua arsip dibaca langsung tanpa ekstraksi ke proyek, tanpa menjalankan PHP OJS, tanpa instalasi dependency, dan tanpa mengubah isi arsip. Versi diverifikasi dari `dbscripts/xml/version.xml`, bukan hanya nama file.

| Arsip | Release internal / tag | Tanggal internal | SHA-256 lokal |
|---|---|---|---|
| `ojs-3.3.0-24.tar.gz` | `3.3.0.24` / `3_3_0-24` | 2026-10-30 | `57cd4331f8884a6bd183d1bf3e469395c59690a343eff9034aeb5bb37d7ab26f` |
| `ojs-3.5.0-6.tar.gz` | `3.5.0.6` / `3_5_0-6` | 2026-10-31 | `f5b85703b06e45cc82e56ef72b0a8f42a2f75237e5204dbd0146d9730a627c97` |

Hash tersebut mengidentifikasi salinan yang diperiksa; belum merupakan verifikasi autentisitas terhadap checksum penerbit. Simpan arsip sebagai referensi lokal read-only dan keluarkan dari paket theme serta calon commit repo. Tidak perlu mengunduh ulang paket yang sama untuk implementasi.

### 4.2 Dependency dan bootstrap yang teramati

| Komponen | Paket 3.3 lokal | Paket 3.5 lokal | Konsekuensi |
|---|---|---|---|
| Smarty pada `composer.lock` | 4.3.1 | 4.5.5 | Keduanya Smarty 4; jangan mengasumsikan migrasi ke Smarty 5 |
| Laravel pada `composer.lock` | 7.30.4 | 12.65.0 | Akses layanan/data harus sesuai jalur |
| Flysystem pada `composer.lock` | 1.1.3 | 3.34.0 | Jangan menyalin akses `Services::get('file')->fs` lama ke 3.5 |
| Default Theme `index.php` | `require_once` class `.inc.php` | Mengembalikan instance class bernamespace langsung | Ikuti bootstrap paket target |
| Default Theme `version.xml` | Class pendek `DefaultThemePlugin` | Class pendek `DefaultThemePlugin` | Namespace class PHP tidak berarti field XML harus berisi FQCN |
| Locale 3.5 | — | `locale/id/locale.po`, `locale/en/locale.po` tersedia | Gunakan `id` dan `en` untuk jalur 3.5 |

Paket 3.5 sudah berisi `lib/pkp/lib/vendor/autoload.php` dan aset build jQuery. Utamakan paket distribusi beserta dependency bawaan untuk fixture, tanpa menjalankan `composer update`, `npm install`, atau build frontend otomatis. Ketersediaan sebagian file bukan bukti bahwa instalasi runtime telah lulus; validasi platform tetap diperlukan saat implementasi.

## 5. Sistem desain Nusantara

### 5.1 Prinsip visual

Nuansa Indonesia harus terasa tanpa mengganggu pembacaan ilmiah. Ornamen menjadi aksen dekoratif pada hero, tepian section, dan footer. Abstrak, artikel, referensi, tabel, formulir, serta navigasi memiliki latar bersih.

Tidak menggunakan logo universitas, lambang negara, klaim dukungan pemerintah, atau motif sakral. Pola dibuat orisinal sebagai interpretasi abstrak alam, maritim, dan tekstil; jangan memberi klaim asal tradisi tertentu tanpa dasar.

### 5.2 Preset

| ID preset | Nama | Palet awal | Dekorasi | Font opsional |
|---|---|---|---|---|
| `zamrudKhatulistiwa` | Zamrud Khatulistiwa | Hijau `#145A43`, krem `#FAF7EF`, emas `#B88A38` | Dedaunan abstrak | Source Serif 4 + Source Sans 3 |
| `bahariNusantara` | Bahari Nusantara | Biru `#174A73`, gading `#F8FAF7`, turkuois `#167C80` | Gelombang ringan | Noto Serif + Noto Sans |
| `batikSogan` | Batik Sogan | Sogan `#6B432D`, gading `#FBF6EC`, tembaga `#A7653F` | Geometri berulang orisinal | Source Serif 4 + Source Sans 3 |
| `merahPutih` | Merah Putih | Merah `#A52834`, putih `#FFFFFF`, arang `#252A32` | Bidang dan garis sederhana | Noto Serif + Noto Sans |
| `tenunSenja` | Tenun Senja | Nila `#373D70`, pasir `#FBF4EA`, terakota `#A94F37` | Garis bersilang | Source Serif 4 + Source Sans 3 |

Palet tersebut adalah token awal implementasi. Aksen dekoratif tidak otomatis boleh digunakan sebagai teks. Sediakan token teks, tautan, hover, border, fokus, serta tombol dan sesuaikan nilai hanya jika diperlukan untuk memenuhi kontras WCAG AA; catat nilai akhir dalam panduan desain.

Default adalah **Zamrud Khatulistiwa**. Struktur, informasi, serta fitur tidak berubah ketika preset diganti.

### 5.3 Opsi presentasi

| Opsi | Nilai/default | Perilaku |
|---|---|---|
| Preset warna (`colourScheme`) | Lima ID preset; default `zamrudKhatulistiwa` | Mengubah token palet, ornamen, dan pasangan font opsional |
| Ornamen Nusantara (`nusantaraOrnaments`) | `subtle` / `off`; default `subtle` | Mengaktifkan aksen dekoratif halus atau menghilangkannya |
| Sumber font (`fontSource`) | `system` / `google`; default `system` | Memakai fallback sistem atau memuat font preset dari Google Fonts |

Font sistem menggunakan serif untuk judul dan sans-serif untuk antarmuka/teks pendukung. Mode Google Fonts menggunakan `display=swap` dan fallback yang sama. Jangan memuat font eksternal ketika mode sistem aktif, termasuk stylesheet font yang diwarisi dari opsi parent.

Ornamen memakai CSS/SVG lokal, tidak menerima fokus, dan disembunyikan dari teknologi bantu. Tidak ada animasi dekoratif wajib. Gambar homepage unggahan menjadi visual utama; tanpa gambar tampilkan latar abstrak. Ornamen tidak boleh menutupi gambar/logo atau teks.

### 5.4 Identitas dan konten jurnal

- Nama, singkatan, logo, favicon, deskripsi, penerbit, serta kontak berasal dari OJS.
- Tanpa logo, tampilkan nama jurnal sebagai teks; jangan memasang logo NusantaraReborn sebagai pengganti identitas jurnal.
- Tanpa favicon, gunakan perilaku bawaan OJS.
- Publisher line dan URL opsional menggunakan konfigurasi jurnal atau opsi eksplisit; bila kosong, hilangkan elemen terkait.
- Jangan menghasilkan klaim akses terbuka, peer review, indeksasi, APC, atau asal negara yang tidak didukung konfigurasi.
- Pertahankan kredit theme secara ringkas dan pisahkan dari hak cipta artikel/jurnal.

## 6. Bahasa, metadata, dan pengalaman pengguna

- Bahasa antarmuka mengikuti locale aktif OJS, bukan dipaksa bahasa Indonesia.
- Sediakan terjemahan lengkap theme Indonesia dan Inggris, termasuk pengaturan administrator, teks kosong, label aksesibilitas, dan navigasi tambahan.
- Gunakan kunci terjemahan core jika tersedia; jangan mengganti istilah core secara global.
- Format tanggal, bahasa metadata, nama penulis, afiliasi, dan urutan informasi mengikuti API OJS versi terkait.
- Metadata yang tidak tersedia dihilangkan secara aman, tidak diisi informasi contoh.
- Pertahankan dukungan judul panjang, beberapa afiliasi, karakter non-ASCII, serta metadata yang berbeda bahasa dari antarmuka.
- Tidak menyisipkan logo SINTA/Garuda atau klaim indeksasi secara default. Daftar indeksasi hanya muncul dari konfigurasi yang diisi pengelola.

## 7. Arsitektur dan kontrak ekstensi OJS

### 7.1 Identitas teknis

| Elemen | Nilai |
|---|---|
| Nama tampilan | `NusantaraReborn` |
| Folder dan application ID | `nusantaraReborn` |
| Class | `NusantaraRebornThemePlugin` |
| Namespace 3.5 | `APP\plugins\themes\nusantaraReborn` |
| Nama penyimpanan plugin | `nusantararebornthemeplugin`, harus sama pada dua jalur dan dibuktikan melalui `getName()` |
| Prefix locale | `plugins.themes.nusantaraReborn` |
| Prefix CSS/JS/template internal | `nr` |
| Parent theme | `defaultthemeplugin` |

Gunakan ID dan nama opsi yang setara pada kedua jalur agar pengaturan theme dapat dipertahankan saat upgrade OJS. Tidak ada API publik baru, endpoint baru, atau perubahan skema database.

### 7.2 Batas tanggung jawab

Theme mengatur tampilan frontend dan data presentasi yang diperlukan. OJS tetap bertanggung jawab atas autentikasi, workflow editorial, submission, publikasi, akses galley, statistik, pembayaran, dan pengelolaan metadata.

- Jangan mengedit file core.
- Jangan membangun form pengganti ketika form native dapat digunakan.
- Jangan memuat aset theme pada dashboard editorial tanpa kebutuhan yang terbukti.
- Gunakan helper URL OJS, bukan menyusun route secara manual.
- Konten pokok harus dapat diakses tanpa JavaScript tambahan theme.

### 7.3 Template dan plugin

Inventaris override lama meliputi header/head/footer, homepage, artikel/detail artikel, arsip/ringkasan issue, editorial team, halaman menu konten, dan partial kartu artikel. Setiap override harus dibandingkan dengan template target sebelum dibawa ke jalur baru.

- Pertahankan pemanggilan loader header, stylesheet, script, serta hook core pada lokasi semestinya.
- Pertahankan primary/user navigation, sidebar, custom block, additional homepage content, dan footer content.
- Pertahankan hook artikel, referensi, homepage, dan footer yang tersedia pada target.
- Hindari menyembunyikan konten plugin menggunakan selector luas atau overflow container.
- Namespace selector dan event JavaScript theme; jangan menghapus handler core atau memuat jQuery/library yang sama dua kali.
- Halaman tanpa kebutuhan desain khusus tetap memakai template core/parent.
- Jurnal multi-tenant menggunakan opsi per konteks; halaman tingkat situs tanpa konteks tidak boleh memicu error.

Kompatibilitas plugin mendatang diupayakan dengan menjaga kontrak hook, struktur native, dan isolasi CSS/JS. Dokumentasi hanya menyatakan kompatibilitas spesifik untuk plugin dan versi yang benar-benar diuji.

## 8. Implementasi khusus versi

### 8.1 OJS 3.3

1. Rename loader, class, `version.xml`, locale key, dan referensi internal secara konsisten.
2. Pertahankan `.inc.php`, `import()`, dan `HookRegistry` sesuai mekanisme target.
3. Periksa akses DAO/Services terhadap versi 3.3.0-24, terutama daftar submission, issue, keputusan editorial, keyword, dan file galley.
4. Pertahankan locale `id_ID` dan `en_US`.
5. Bandingkan template override dengan core target; bawa perbaikan struktur/hook yang diperlukan.
6. Pastikan opsi parent yang dinonaktifkan tidak tetap memuat font atau background yang bertentangan.

### 8.2 OJS 3.5

1. Gunakan class `.php` bernamespace dan bootstrap sesuai pola plugin target.
2. Ganti `import()` dengan import namespace yang tepat; gunakan `PKP\plugins\Hook` untuk registrasi hook.
3. Port akses data ke repository/model/API yang tersedia pada target, tanpa mempertahankan akses legacy melalui fallback yang menyembunyikan kegagalan.
4. Audit submission, publication, issue, section, author/affiliation, keputusan editorial, galley/file, keyword, dan announcement.
5. Gunakan locale `id` dan `en`; sesuaikan pembacaan opsi multibahasa dan URL yang memuat locale.
6. Bangun override dari template 3.5, lalu terapkan komponen desain NusantaraReborn.
7. Pertahankan `dataAvailability`, beberapa afiliasi/ROR, serta elemen publikasi native dan keluaran plugin pada halaman artikel. Untuk sitasi 3.5, gunakan hook yang memasok markup dari plugin CSL; jangan menyalin blok sitasi inline 3.3.
8. Sesuaikan jalur aset dan signature metode terhadap source target, bukan asumsi kompatibilitas 3.3.

### 8.3 Kontrak port yang dibuktikan dari paket lokal

| Area | Bukti source dalam arsip | Keputusan implementasi |
|---|---|---|
| Sitasi 3.3 | `templates/frontend/objects/article_details.tpl` memuat blok `$citation`, `citationOutput`, dan daftar format | Pertahankan blok dan atribut interaksinya sesuai template 3.3 |
| Sitasi 3.5 | `plugins/generic/citationStyleLanguage/CitationStyleLanguagePlugin.php` mendaftarkan `addCitationMarkup` pada `Templates::Article::Details` | Pertahankan hook tepat satu kali di lokasi detail; jangan menghasilkan panel CSL duplikat |
| Afiliasi penulis 3.5 | Template memanggil `getAffiliations()`, `getLocalizedName()`, `getRor()`, dan `getLocalizedAffiliationNamesAsString()` | Render seluruh afiliasi dan ROR; gunakan model yang sama untuk JSON-LD, bukan field string legacy |
| Masthead 3.5 | `lib/pkp/templates/frontend/pages/editorialMasthead.tpl` dan `editorialMastheadDisabled.tpl`; tidak ada template `editorialTeam.tpl` lama | Warisi template masthead native dan beri styling; jangan membawa override editorialTeam 3.3 ke 3.5 |
| Riwayat editorial | Masthead memakai `$mastheadRoles`, `$mastheadUsers`, tanggal mulai, dan tautan `about/editorialHistory` | Pertahankan role, masa tugas, tautan riwayat, serta perilaku halaman nonaktif |
| Pengakuan reviewer publik | Masthead native memakai `$reviewers` dan `$previousYear` | Hanya tampilkan data publik yang sudah dipasok core; jangan membuat query daftar reviewer sendiri |
| Data availability | Template artikel 3.5 memuat `data-availability-statement` | Pertahankan isi multibahasa dan sanitasi native |
| Keputusan editorial 3.5 | `classes/facades/Repo.php` menyediakan `decision()`; collector memiliki `filterBySubmissionIds()` dan `filterByDecisionTypes()` | Port pembacaan tanggal yang benar-benar boleh dipublikasikan melalui API ini; tidak menyalin EditDecisionDAO legacy |
| Opsi theme | Kedua `ThemePlugin` membaca settings memakai `$this->getName()`; theme aktif dibandingkan dengan basename folder | Folder/application ID saja tidak cukup: nama penyimpanan plugin dan opsi harus stabil antarjalur |
| Statistik 3.5 | Default Theme menyediakan `displayStats`: `none`, `bar`, `line` | Pertahankan opsi dan render native, jangan mengganti dengan angka statis |
| Aset parent 3.5 | Default Theme memuat Swiper dan aset jQuery dari `js/build/` | Pertahankan lifecycle parent; jangan memakai path library 3.3 atau memuat duplikat |

Afiliasi **author** 3.5 berbeda dari afiliasi **user** pada masthead: template masthead masih membaca `getLocalizedData('affiliation')`. Jangan mengganti seluruh akses afiliasi dengan satu pola secara global.

Hook artikel `Templates::Article::Main`, `Templates::Article::Details`, `Templates::Article::Details::Reference`, hook homepage `Templates::Index::journal`, serta hook footer/sidebar tetap ditemukan pada kedua paket. Bentuk argumen dan lokasi pemanggilan harus dipertahankan per template target.

### 8.4 Metadata tambahan

- Pertahankan metadata Scholar/Dublin Core yang diproduksi plugin.
- Canonical, Open Graph, dan JSON-LD hanya ditambahkan apabila dibutuhkan dan tidak menggandakan keluaran setara.
- Jangan menonaktifkan hook metadata untuk menghindari duplikasi; atur keluaran milik theme sendiri.
- Gunakan data publication aktif yang tepat, termasuk DOI, lisensi artikel, tanggal, author, afiliasi, dan issue.
- Sanitasi teks/URL dan encode JSON secara aman. Metadata kosong tidak boleh menjadi identitas institusi fallback.
- Kartu artikel/related articles hanya memuat publikasi yang boleh ditampilkan kepada pengunjung tersebut.

## 9. Rebranding, hak cipta, dan arsip

Folder proyek baru telah dipilih sebagai tujuan pengembangan. Sumber lama tetap utuh selama tahap perencanaan. Pada implementasi, buat snapshot legacy lokal sebelum perubahan; jangan menghapus folder lama sebagai bagian rebranding.

Paket baru harus bebas dari:

- Nama Universitas Andalas dan nama produk lama pada branding/konten bawaan.
- URL penerbit kampus dan caption kampus.
- Logo, wordmark, crest, favicon, dan foto hero institusi.
- Nama class, prefix, nama aset, translation key, serta contoh konfigurasi yang masih memakai identitas lama.
- Panduan lama dan checksum lama yang tidak cocok dengan paket baru.

Pengecualian terkontrol adalah kontak pengembang `ikhwan[at]unand.ac.id` di atribusi/lisensi dan referensi historis dalam panduan migrasi/arsip. Referensi historis tidak boleh menjadi tampilan default jurnal.

Untuk source first-party baru, gunakan komentar sesuai bahasa:

```text
Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
```

Jangan menambahkan header tersebut ke file pihak ketiga, data, binary, generated, lock, atau format-sensitive. Pertahankan atribusi valid pada template/aset turunan pihak ketiga. Pemberitahuan lisensi harus membedakan kode, dokumentasi/aset orisinal, dan komponen pihak ketiga; jangan memperluas pembatasan nonkomersial ke kode GPL.

## 10. Struktur sumber dan artefak

Struktur yang dituju setelah implementasi:

```text
NusantaraReborn/
  IMPLEMENTATION-PLAN.md
  README.md
  shared/
    styles/
    js/
    assets/
  ojs33/
    nusantaraReborn/
  ojs35/
    nusantaraReborn/
  scripts/
  tests/
  docs/
  legacy/
  dist/
    ojs33/nusantaraReborn-1.0.0.0.tar.gz
    ojs35/nusantaraReborn-2.0.0.0.tar.gz
    SHA256SUMS
```

- `shared/` menjadi sumber tunggal aset netral versi; hasil salinannya disertakan dalam paket saat build.
- PHP, locale, dan template khusus runtime berada pada jalur masing-masing.
- `legacy/` berisi snapshot sumber/paket historis yang tidak ikut distribusi. Jangan memasukkan data jurnal atau informasi rahasia.
- Packaging menggunakan daftar file yang diizinkan dan staging terpisah; tidak mengemas seluruh folder proyek secara rekursif.
- Setiap tarball memiliki satu root `nusantaraReborn/`; nama arsip dipertahankan sesuai panduan instalasi.
- Pada 3.3, `PluginHelper.inc.php` memperoleh nama pendek plugin dari awalan nama arsip; `nusantaraReborn-1.0.0.0.tar.gz` harus cocok dengan folder dan application ID. Pada 3.5, `PluginHelper.php::extractPlugin()` menggunakan `PharData` dan mencari subdirektori langsung yang berisi `version.xml`. Tetap gunakan satu root folder untuk kedua paket; jangan menyatakan aturan parsing nama 3.3 sebagai aturan internal 3.5.
- Versi pada nama paket dan `version.xml` harus konsisten. Tanggal versi menggunakan tanggal build rilis aktual.
- Jangan menginisialisasi Git, membuat branch, atau membuat commit hanya karena folder ini disiapkan untuk repo; lakukan hanya setelah ada otorisasi eksplisit.

## 11. Instalasi dan perpindahan versi

### 11.1 Dari Unand Journal Theme

Instal NusantaraReborn sebagai plugin baru. Pengelola mencatat opsi lama, memasang paket yang sesuai OJS, mengaktifkan theme, lalu mengisi opsi yang diperlukan secara manual. Data jurnal native tetap berasal dari OJS. Jangan menyalin identitas institusi lama sebagai nilai default baru.

### 11.2 Dari NusantaraReborn OJS 3.3 ke 3.5

- Gunakan prosedur upgrade resmi OJS dan backup yang sesuai.
- Nonaktifkan atau pilih Default Theme sebelum proses upgrade jika dibutuhkan untuk menghindari loader plugin lama pada runtime baru.
- Ganti seluruh paket theme dengan paket jalur 3.5; jangan menumpuk file baru di atas file legacy yang sudah tidak berlaku.
- Pertahankan application ID, hasil `getName()`, dan nama opsi setara; verifikasi opsi multibahasa setelah upgrade locale OJS. Snapshot nilai sebelum/sesudah untuk `id_ID → id` dan `en_US → en`. Jangan menganggap kesamaan folder otomatis memindahkan locale pada plugin settings. Jika upgrade core tidak mengonversinya, dokumentasikan pengisian ulang melalui UI; jangan menambahkan migrasi database otomatis di luar lingkup.
- Aktifkan NusantaraReborn kembali dan jalankan pemeriksaan penerimaan.
- Jika ditemukan masalah, gunakan Default Theme sementara; jangan mengubah database untuk memaksa plugin aktif.

Prosedur ini harus dibuktikan pada fixture upgrade sebelum diberi label teruji.

## 12. Tahapan pelaksanaan

### Tahap A — Baseline dan inventaris

- Periksa instruksi proyek serta status Git jika tersedia.
- Catat inventaris sumber dan hash snapshot awal.
- Inventaris seluruh identitas lama, override template, hook, API data, aset, dan opsi.
- Verifikasi paket target resmi beserta kebutuhan runtime.

**Selesai apabila:** sumber baseline terlindungi dan daftar perubahan memiliki cakupan yang jelas.

### Tahap B — Desain bersama dan debranding

- Implementasikan token lima preset, ornamen lokal, opsi font, dan fallback logo/gambar.
- Perbarui terjemahan, atribusi, lisensi, serta identitas teknis.
- Hapus fallback institusi dan klaim jurnal yang tidak bersumber.

**Selesai apabila:** tampilan dan aset baru bersifat umum bernuansa Indonesia, tanpa identitas kampus.

### Tahap C — Jalur 3.3

- Terapkan desain ke kode 3.3, audit API dan template target.
- Uji instalasi dan fitur utama, lalu selesaikan regresi.

**Selesai apabila:** jalur 3.3 memenuhi pengujian inti pada 3.3.0-24.

### Tahap D — Port 3.5

- Port loader, namespace, hook, akses data, locale, dan template.
- Uji integrasi native/plugin dan parity fitur dengan jalur 3.3.

**Selesai apabila:** jalur 3.5 memenuhi pengujian inti pada 3.5.0-6 tanpa mengandalkan API legacy yang dihapus.

### Tahap E — Verifikasi menyeluruh

- Jalankan matriks visual, aksesibilitas, metadata, plugin, dan upgrade.
- Perbaiki kegagalan dalam lingkup dan catat keterbatasan aktual.

**Selesai apabila:** bukti hasil uji tersedia dan tidak ada kegagalan kritis yang belum ditangani.

### Tahap F — Packaging dan dokumentasi

- Bangun kedua tarball, periksa isinya, hitung checksum.
- Uji instalasi ulang dari tarball hasil build, bukan hanya direktori sumber.
- Finalisasi panduan dan laporan rilis lokal.

**Selesai apabila:** kedua paket dapat dipasang sesuai instruksi dan dokumentasi mencerminkan hasil aktual.

## 13. Rencana pengujian

### 13.1 Pemeriksaan statis dan paket

- PHP lint pada runtime target.
- Validasi `version.xml`, class loader, namespace, dan locale.
- Pemeriksaan referensi template/aset serta tidak adanya file institusi tersisa.
- Pemeriksaan root tarball, versi, checksum, path traversal, symlink yang tidak diperlukan, dan file tersembunyi pengembangan.
- Pemeriksaan isi paket terhadap kredensial, data pribadi, log, cache, arsip lama, dan file konfigurasi lingkungan.

### 13.2 Instalasi dan opsi

Pada kedua versi: upload Plugin Manager, enable, pilih theme, simpan opsi, ganti preset, ubah locale, nonaktifkan/aktifkan kembali, dan instalasi manual. Uji bahwa opsi tetap tersimpan dan tidak bocor antarjurnal.

### 13.3 Halaman dan data

Uji homepage, issue, arsip, artikel, galley PDF/HTML yang tersedia, pencarian, editorial team, kebijakan, about, pengumuman, login, register, dan akses submission.

Fixture mencakup jurnal kosong, tanpa issue, tanpa logo/gambar, tanpa DOI/ORCID, metadata tidak lengkap, beberapa afiliasi, judul panjang, karakter non-ASCII, artikel belum terbit, dan dua jurnal dalam satu instalasi. Gunakan data sintetis, bukan data editorial rahasia.

### 13.4 Visual dan aksesibilitas

- Lima preset pada viewport 375, 768, dan 1440 piksel.
- Ornamen aktif/nonaktif, font sistem/Google, gambar tersedia/kosong.
- Desktop Chromium, Firefox, dan Safari bila tersedia; catat browser yang tidak dapat diuji.
- Kontras teks normal minimal 4.5:1 dan teks besar 3:1; fokus keyboard, skip link, label formulir, serta urutan heading diperiksa.
- Tidak ada overflow horizontal pada halaman utama, tabel, atau referensi panjang.
- Konten tetap terbaca saat font eksternal gagal dan saat JavaScript tambahan tidak berjalan.

### 13.5 Plugin dan hook

Pilih versi plugin yang dinyatakan kompatibel dengan masing-masing OJS dan catat versi tepatnya. Matriks minimal mencakup:

| Integrasi | Pemeriksaan |
|---|---|
| Scholar/Dublin Core | Meta tag tetap muncul dan tidak rusak |
| Citation Style Language | Keluaran sitasi dan interaksinya tetap berfungsi |
| PDF viewer / galley | Tampilan serta tautan unduh tetap mengikuti hak akses |
| Custom block/sidebar | Block dapat muncul di lokasi yang dikonfigurasi |
| Statistik penggunaan | Keluaran plugin tidak tertutup atau rusak |
| DOI/ORCID | Identifier dan tautan berasal dari metadata yang benar |
| Fixture hook | Sisipan header, artikel, sidebar, dan footer tetap ditampilkan |

Jangan menyatakan plugin lulus jika tidak tersedia dalam lingkungan uji. Fixture hook tidak menggantikan pengujian plugin nyata, tetapi membantu menguji kontrak ekstensi.

### 13.6 Matriks plugin berdasarkan isi paket lokal

| Integrasi | Paket 3.3 | Paket 3.5 | Strategi pengujian |
|---|---|---|---|
| `citationStyleLanguage`, `googleScholar`, `dublinCoreMeta` | Ada | Ada | Gunakan salinan bundled; uji sitasi sekali tampil, perubahan format, unduh sitasi, dan metadata |
| `customBlockManager`, `staticPages` | Ada | Ada | Uji posisi block serta halaman tambahan melalui navigasi native |
| `pdfJsViewer`, `htmlArticleGalley` | Ada | Ada | Uji viewer dan hak akses; jangan membuat URL file langsung |
| `usageStats` | Plugin generic tersedia | Tidak ada folder generic tersebut | 3.3 uji integrasi bundled; 3.5 uji statistik native dan opsi `displayStats` |
| `orcidProfile` | Ada | Tidak ada folder generic tersebut | 3.5 verifikasi keluaran ORCID native; jangan mengasumsikan plugin eksternal terpasang |
| `credit`, `pflPlugin` | Tidak ada folder generic tersebut | Ada | 3.5 uji CRediT dan Publication Facts Label bila diaktifkan, termasuk konten yang disisipkan melalui hook |
| `crossref`, `datacite` | Tidak ada folder generic tersebut | Ada | Verifikasi metadata/tautan lokal; tidak mengirim deposit eksternal selama pengujian theme |

“Ada” berarti folder plugin ditemukan di arsip, bukan bukti plugin aktif atau sudah diuji. “Tidak ada folder generic” bukan klaim bahwa kemampuan terkait tidak ada pada core atau kategori plugin lain. Catat versi plugin dari `version.xml` saat fixture diaktifkan. Gunakan data sintetis; jangan mengirim DOI deposit, email, analytics, atau permintaan kredensial ORCID nyata untuk smoke test.

Tambahkan skenario wajib 3.5: masthead aktif/nonaktif, riwayat editorial, afiliasi ganda dengan/tanpa ROR, data availability, CSL aktif/nonaktif, statistik `none/bar/line`, serta hook plugin yang menambahkan konten artikel. Tidak boleh ada panel ganda atau hilangnya fitur akibat override.

### 13.7 Metadata dan keamanan keluaran

- Periksa canonical, Open Graph, JSON-LD, dan metadata core/plugin terhadap duplikasi atau identitas fallback yang salah.
- Pastikan sitemap dan OAI-PMH tetap dapat diakses sesuai konfigurasi core.
- Uji teks yang mengandung HTML khusus, kutip, Unicode, dan URL tidak aman pada opsi theme.
- Pastikan isi belum dipublikasikan tidak muncul pada kartu/related articles atau JSON-LD publik.
- Jangan mengungkap keputusan editorial internal, komentar reviewer, atau data privat ketika membangun informasi tanggal publikasi.

### 13.8 Bukti pengujian

Catat untuk setiap skenario: versi OJS, PHP, database, plugin, browser, langkah, hasil yang diharapkan, hasil aktual, status lulus/gagal/terblokir/belum dijalankan, serta lokasi bukti. Screenshot dan log harus memakai data sintetis dan tidak mengandung rahasia.

## 14. Kriteria penerimaan rilis

- [ ] Kedua paket dapat dipasang melalui metode yang didokumentasikan.
- [ ] Nama produk, ID, class, locale, dan metadata versi konsisten.
- [ ] Tidak ada branding/aset Universitas Andalas dalam paket baru di luar pengecualian atribusi dan catatan historis yang jelas.
- [ ] Lima preset bernuansa Indonesia berfungsi dan mudah dibaca.
- [ ] Opsi ornamen dan font bekerja tanpa memuat aset yang tidak dipilih.
- [ ] Identitas serta metadata jurnal berasal dari OJS atau opsi eksplisit.
- [ ] Fungsi native, hook, navigasi, sidebar, dan integrasi plugin yang diuji tetap berfungsi.
- [ ] Masthead dan riwayat editorial 3.5, afiliasi/ROR, data availability, dan CSL berbasis hook tetap berfungsi.
- [ ] Nama penyimpanan plugin dan perpindahan opsi locale telah diuji, bukan diasumsikan dari kesamaan folder.
- [ ] Jalur 3.5 tidak bergantung pada class/plugin API legacy yang telah dihapus.
- [ ] Tidak ada kebocoran data unpublished atau informasi editorial privat.
- [ ] Pengujian runtime dilakukan dari paket final pada kedua target.
- [ ] Checksum, panduan, lisensi, serta laporan kompatibilitas tersedia.
- [ ] Semua kegagalan material diselesaikan atau dinyatakan sebagai penghalang rilis, bukan disembunyikan.

Label **teruji** hanya digunakan setelah pemeriksaan benar-benar dijalankan. Jika lingkungan belum tersedia atau instalasi dependency belum diotorisasi, catat hambatan dan jangan menyatakan paket siap produksi.

## 15. Dokumentasi yang harus dihasilkan

1. README produk dan cara memilih paket OJS yang benar.
2. Panduan instalasi dan konfigurasi bahasa Indonesia dengan ringkasan Inggris.
3. Panduan preset, ornamen, font, serta penggantian logo/gambar melalui OJS.
4. Matriks persyaratan upstream versus runtime yang diuji.
5. Matriks plugin, kontrak hook, dan batas kompatibilitas.
6. Panduan perpindahan manual dari theme lama serta upgrade NusantaraReborn 3.3 ke 3.5.
7. Catatan SEO/metadata yang menjelaskan pembagian tanggung jawab theme, OJS, dan plugin.
8. CHANGELOG, pemberitahuan hak cipta/lisensi, dan laporan pengujian per rilis.

## 16. Risiko dan penanganan

| Risiko | Penanganan |
|---|---|
| Template lama menghilangkan fitur atau hook baru | Bandingkan dengan source target dan minimalkan override |
| API data 3.5 berbeda | Port berdasarkan repository/model target dan uji dengan fixture nyata |
| CSS merusak plugin lain | Prefix selector, hindari aturan global, uji plugin dan fixture hook |
| Ornamen mengurangi keterbacaan | Default halus, area isi bersih, opsi nonaktif, pengujian kontras |
| Font eksternal tidak tersedia | Default font sistem dan fallback yang selalu berfungsi |
| Metadata mengarang klaim jurnal | Tidak ada fallback institusi/kebijakan; hilangkan nilai kosong |
| Sisa identitas lama pada binary | Audit inventaris aset dan pemeriksaan visual, bukan pencarian teks saja |
| Campuran file antarjalur saat upgrade | Instruksi penggantian paket lengkap dan uji fixture upgrade |
| Klaim kompatibilitas berlebihan | Batasi pada target dan plugin yang dibuktikan dalam laporan |

## 17. Catatan inspeksi lokal dan batas kepastian

Revisi 2 memeriksa langsung versi internal, hash arsip, composer manifest/lock, bootstrap Default Theme, ThemePlugin, PluginHelper, template homepage/artikel/header/footer, masthead 3.5, registrasi hook CSL, API afiliasi/keputusan, serta inventaris plugin generic. Pemeriksaan ini bersifat statis dan terarah, bukan audit seluruh source OJS.

Yang belum dilakukan: ekstraksi/install OJS, koneksi database, aktivasi plugin, render browser, pengujian upgrade, pengujian performa, dan pemeriksaan platform runtime. Tidak ada kode theme yang dibuat atau diubah dalam tahap penyempurnaan rencana ini. Hash paket belum dicocokkan dengan checksum resmi penerbit.

Perubahan utama dari revisi 1: kontrak CSL per versi dibuat eksplisit; editorialTeam 3.5 diganti dengan pewarisan masthead native; API afiliasi/ROR dan keputusan dipetakan; statistik/plugin bundled dibedakan; aturan packaging serta identitas penyimpanan opsi diperjelas; dependency paket aktual dan fingerprint baseline dicatat.

## 18. Batas otorisasi dan status akhir dokumen

Dokumen ini menyimpan rencana yang telah disepakati dan detail pelaksanaannya. Sumber theme lama tetap menjadi baseline sampai implementasi dimulai. Tidak ada klaim bahwa port 3.5, paket rilis, atau pengujian runtime telah selesai.

Implementasi harus mempertahankan perubahan pengguna, tidak menghapus arsip lama, dan tidak mengirim materi proyek ke layanan eksternal tanpa otorisasi. Publikasi paket, deployment, commit/push, serta instalasi dependency mengikuti batas otorisasi proyek.

Copyright (c) 2026 Ikhwan Arief. Dokumentasi ini mengikuti rencana lisensi dokumentasi proyek: CC BY-NC 4.0.
