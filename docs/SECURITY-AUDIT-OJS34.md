# Laporan Audit Keamanan Statis (SAST) — NusantaraReborn 3.0.0.0 untuk OJS 3.4

Developed by Ikhwan Arief (ikhwan[at]unand.ac.id). Copyright (c) 2026 Ikhwan Arief. Dokumentasi: CC BY-NC 4.0.

**Objek:** `ojs34/nusantaraReborn/` · **Tanggal:** 11 November 2026 · **Metode:** sama dengan `docs/SECURITY-AUDIT-OJS35.md`, ditambah perbandingan keluaran template 3.4 terhadap 3.5 dan uji dinamis `tests/runtime34/verify.sh`.

## Ringkasan

Tidak ada temuan High atau Medium. Kode 3.0.0.0 diturunkan dari 2.0.0.0 dan membawa seluruh kontrol T-1, T-2, T-3. Tidak ada endpoint, form, handler, SQL mentah, akses berkas, atau pembacaan input request.

## Perbedaan terhadap 2.0.0.0

| Area | Penilaian |
|---|---|
| `editorialTeam.tpl` (`$nrEditorialBody`) | HTML Dewan Redaksi dari pengelola jurnal **disaring `richHtml()`**: lebih ketat daripada core dan jalur 1.x (yang merender mentah). Pengantar tetap `strip_unsafe_html`. |
| Peta negara (`$nrCountryMapJson nofilter`) | `json_encode` dengan `JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT`; tidak bisa keluar dari blok `<script type="application/json">`. |
| JS kartu Dewan Redaksi | Membaca DOM yang sudah disaring; teks ditulis ulang via `escapeHtml()`/`textContent` (T-5 jalur 3.3, kini sumbernya disaring). |
| ROR / ikon ORCID core | Tidak dipakai di 3.4; lencana "iD" adalah markup statis theme. |
| Data | `Services::get('publicationStats'/'file')`, repository 3.4; filter `STATUS_PUBLISHED` sama dengan 2.0.0.0. |

## Uji dinamis

Opsi berbahaya (`<script>`, `javascript:`, foto `http://`) dan muatan Custom Page (`<script>`, `<iframe>`, `onerror`) diuji ulang di OJS 3.4.0-12: skrip ter-*escape* atau dibuang, URL tak aman tidak dirender, tidak ada dialog `alert`, struktur heading/tabel tetap.
