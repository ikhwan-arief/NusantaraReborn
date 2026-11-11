# Laporan Audit Keamanan Statis (SAST) — NusantaraReborn 2.0.0.0 untuk OJS 3.5

Developed by Ikhwan Arief (ikhwan[at]unand.ac.id). Copyright (c) 2026 Ikhwan Arief. Dokumentasi: CC BY-NC 4.0.

| Item | Keterangan |
|---|---|
| Objek audit | `ojs35/nusantaraReborn/` (NusantaraReborn 2.0.0.0), aset bersama `shared/` |
| Tanggal | 11 November 2026 |
| Metode | Sama dengan `docs/SECURITY-AUDIT-OJS33.md`: pencarian sistematis keluaran tanpa *escape*, akses SQL/berkas/request, pembandingan dengan template core OJS 3.5.0-6, ditambah uji dinamis (`tests/runtime35/verify.sh`) |
| Acuan | OWASP Top 10 (2021); praktik plugin PKP 3.5 (`strip_unsafe_html`, repository, `Hook::add`) |

## 1. Ringkasan

Tidak ada temuan High atau Medium. Semua kontrol 1.0.2.0 (T-1, T-2, T-3) dibawa ke jalur 3.5 dan diuji ulang. Port ke 3.5 tidak menambah endpoint, form, handler, SQL mentah, akses berkas, maupun pembacaan input request.

| Temuan 3.3 | Status di 2.0.0.0 | Bukti |
|---|---|---|
| T-1 HTML pengelola jurnal | Dipertahankan: `richHtml()` (HTMLPurifier bawaan OJS, `allowed_html` + heading/tabel/blockquote) untuk Custom Page dan license terms | Skrip, iframe, `onerror` dibuang; struktur utuh |
| T-2 Tanggal editorial | Dipertahankan: opsi `showEditorialDates`; data dibaca lewat `Repo::decision()` hanya tipe dan tanggal | Tanpa identitas editor/penelaah |
| T-3 URL eksternal | Dipertahankan: `safeUrl()` http/https; foto Editor-in-Chief hanya `https` + `referrerpolicy="no-referrer"`; ROR hanya `https` | `javascript:` dibuang; foto `http://` diabaikan |
| T-4 Google Fonts tanpa SRI | Tetap informasi; bawaan font sistem | Nol permintaan font eksternal pada mode sistem |
| T-5 `innerHTML` JS | Tidak relevan di 3.5: dewan redaksi kini masthead core berbasis peran, tidak diparsing JS | — |

## 2. Keluaran tanpa *escape* di template 3.5

| Variabel | Sumber | Penilaian |
|---|---|---|
| `$homeUrl`, `$nrCardUrl`, `$nrIssueUrl`, `$publicFilesDir`, `$primaryMenu`, `$nrBannerCrumbs` | Helper `{url}`/menu core | Aman (dibangun core) |
| `$orcidIcon`, `$orcidUnauthenticatedIcon`, `$rorIdIcon`, `$ccLicenseBadge` | SVG/HTML statis dari core | Aman (sama dengan template core) |
| `$nrPageContent`, `$nrLicenseTerms` | HTML pengelola jurnal setelah `richHtml()` | Disaring |
| `$additionalHomeContent`, `$pageFooter`, `$sidebarCode` | HTML pengelola jurnal / keluaran plugin blok | Setara core (T-1, Low) |
| `$nrFacts`, `$name` | *Capture* template dengan nilai ter-*escape* | Aman |
| `$nrAff.index`, `$nrAff.flag`, `$nrAuthor.flag`, `$nrYear`, `getId()` | Angka/emoji yang dibangkitkan theme | Aman |
| `$activeTheme->displayUsageStatsGraph()` | Metode core (mengembalikan `void`, menyisipkan data grafik ter-*encode* JSON) | Aman |

Judul artikel 3.5 dirender dengan format `html` + `strip_unsafe_html` (pola core); meta tag dan JSON-LD memakai format `text`. JSON-LD tetap memakai `JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT`.

## 3. Akses data dan otorisasi

- Repository OJS 3.5 (`Repo::submission/issue/section/decision`, `app()->get('publicationStats')`, `app()->get('file')`, `AnnouncementTypeDAO`, `NavigationMenu*DAO`). Tidak ada SQL mentah.
- Kartu beranda hanya untuk `STATUS_PUBLISHED` (TOC 3.5 dapat memuat item terjadwal untuk editor); artikel terkait dan arsip difilter `STATUS_PUBLISHED` + terbitan yang sudah terbit; JSON-LD hanya untuk publikasi terbit.
- Ukuran berkas galley dibaca dari galley yang sudah diotorisasi `ArticleHandler`; hanya ukuran, bukan isi.
- `getOptionValues()` hanya memetakan kunci locale lama saat membaca; tidak menulis database.

## 4. Kesimpulan

Profil keamanan 2.0.0.0 setara dengan 1.0.2.0. Rekomendasi tetap: pertahankan font sistem sebagai bawaan, dan perlakukan akun Journal Manager sebagai akun berhak tinggi (konten HTML-nya dirender seperti di core).
