# Panduan Pengaturan NusantaraReborn

Untuk pengelola jurnal (Journal Manager) pada OJS 3.3. Developed by Ikhwan Arief (ikhwan[at]unand.ac.id). Copyright (c) 2026 Ikhwan Arief. Dokumentasi: CC BY-NC 4.0.

Panduan ini menjelaskan arti setiap isian di pengaturan theme dan pengaturan OJS lain yang memengaruhi tampilan. Prinsipnya: **isian yang kosong disembunyikan**. Theme tidak pernah mengisi teks contoh, klaim peer review, akses terbuka, indeksasi, atau identitas institusi. Jadi isi hanya hal yang benar untuk jurnal Anda.

Pengaturan theme ada di **Pengaturan > Situs Web > Tampilan > Theme** (Settings > Website > Appearance > Theme). Pilih *NusantaraReborn*, isi kolom, lalu klik **Simpan**. Kolom bertanda bahasa (misalnya *English* / *Bahasa Indonesia*) bisa diisi per bahasa. Bila satu bahasa kosong, theme memakai bahasa utama jurnal.

---

## 1. Peringatan utama: buat menu dari Navigation OJS

> ⚠ **Theme tampil sempurna bila menu dibuat dari Pengaturan > Situs Web > Pengaturan > Navigasi** (Settings > Website > Setup > Navigation).

Mengapa penting:

- Menu Navigasi otomatis ikut **menu seluler**, **penanda halaman aktif**, **breadcrumb** halaman kebijakan, dan **terjemahan** per bahasa.
- Menu yang dibuat sebagai HTML di footer, blok kustom, atau *Konten Tambahan* beranda tidak mendapat semua itu dan sering rusak di layar kecil.

Cara menyusun menu yang disarankan:

1. Buka **Navigasi**. Ada dua menu bawaan: **Primary Navigation Menu** (bar hijau di bawah header) dan **User Navigation Menu** (Daftar/Masuk di pojok kanan atas).
2. Di Primary Navigation Menu, susun item seperti berikut. Seret item untuk mengubah urutan, atau menjorokkannya menjadi submenu.
   - Terbitan Terkini (*Current*)
   - Arsip (*Archives*)
   - Pengumuman (*Announcements*), jika pengumuman diaktifkan
   - Tentang (*About*), dengan submenu: Tentang Jurnal, Dewan Redaksi, Penyerahan Naskah, Kontak
   - Kebijakan (*Custom Page*), dengan submenu halaman kebijakan: Fokus & Ruang Lingkup, Proses Telaah, Etika Publikasi, Akses Terbuka, Hak Cipta & Lisensi, dan seterusnya
3. **Jangan membuat item "Beranda".** Theme sudah menambahkannya otomatis di awal menu.
4. Halaman kebijakan sebaiknya dibuat sebagai **Custom Page** lewat tombol *Tambah Item* di Navigasi (tipe *Custom Page*), dengan judul dan isi untuk setiap bahasa.
5. Klik **Simpan** lalu periksa tampilan di layar ponsel.

Di atas pengaturan theme ada blok **Panduan NusantaraReborn** yang menampilkan peringatan ini beserta jumlah item Primary Navigation Menu. Jika tertulis menu masih kosong, lengkapi Navigasi terlebih dahulu.

---

## 2. Isian pengaturan theme

### Tampilan

| Isian | Arti | Cara mengisi |
|---|---|---|
| **Preset warna** | Paket warna, ornamen, latar hero bawaan, dan pasangan font Google. | Pilih salah satu: Zamrud Khatulistiwa (hijau-emas, bawaan), Bahari Nusantara (biru-turkuois), Batik Sogan (cokelat-tembaga), Merah Putih (merah-arang), Tenun Senja (nila-terakota). Semua preset sudah diuji kontras WCAG AA; struktur halaman tidak berubah. |
| **Ornamen Nusantara** | Motif dekoratif halus di hero, garis header, dan footer. | *Halus* untuk nuansa Indonesia; *Nonaktif* untuk tampilan polos. Area baca (abstrak, artikel, tabel) selalu tanpa ornamen. |
| **Sumber font** | Asal huruf. | *Font sistem* (disarankan): cepat, tanpa permintaan ke server luar. *Google Fonts*: memuat pasangan font preset dari fonts.googleapis.com. Jika font gagal dimuat, font sistem dipakai. |

### Identitas dan beranda

| Isian | Arti | Cara mengisi |
|---|---|---|
| **Tagline jurnal** | Baris pendek di bawah nama jurnal pada header. | 5–12 kata, misalnya *Jurnal kajian pesisir dan kemaritiman*. Isi per bahasa. Kosongkan bila tidak perlu. |
| **Teks hero beranda** | Satu–dua kalimat di bawah judul besar beranda. | Ringkasan cakupan jurnal (maksimal sekitar 200 karakter). Jika kosong, *Deskripsi Mesin Pencari* dari pengaturan jurnal yang dipakai. |
| **Keterangan gambar hero** | Teks kecil di pojok gambar hero. | Isi kredit foto bila Anda mengunggah *Homepage Image*, misalnya *Foto: Nama Fotografer*. Kosongkan untuk latar bawaan. |
| **Baris penerbit** | Nama penerbit di footer. | Misalnya *Diterbitkan oleh Fakultas X, Universitas Y*. Jika kosong, kolom *Penerbit* di Pengaturan > Jurnal yang dipakai. |
| **URL penerbit** | Tautan baris penerbit dan data terstruktur. | Alamat lengkap diawali `https://`. Alamat lain (misalnya `javascript:`) otomatis diabaikan. |
| **Sorotan profil jurnal** | Hingga empat ikon-plus-teks di bawah deskripsi beranda. | Pisahkan dengan titik koma, misalnya `Akses terbuka; Telaah sejawat ganda; Terbit Juni dan Desember; Tanpa biaya`. Isi hanya fakta yang benar. |

### Baris Informasi Jurnal (kotak di beranda)

Setiap baris hanya tampil bila diisi. Kotak hilang bila semua baris kosong.

| Isian | Contoh isi yang baik |
|---|---|
| **Frekuensi terbit** | *Dua kali setahun (Juni dan Desember)* |
| **Proses telaah** | *Double-blind peer review, minimal dua penelaah* (sesuai kebijakan resmi jurnal) |
| **Biaya pemrosesan artikel (APC)** | *Tanpa biaya* atau *Rp500.000*. Jika kosong dan pembayaran OJS aktif, biaya publikasi OJS yang ditampilkan. |
| **Bahasa jurnal** | *Indonesia, Inggris*. Jika kosong, diambil dari bahasa naskah di OJS. |
| **Pengindeksan (ringkas)** | *DOAJ, SINTA 3, Garuda*. Isi hanya yang sudah resmi. |
| **Pengarsipan** | *PKP PN, LOCKSS* |
| **Bidang ilmu** | Tampil di halaman Dewan Redaksi, misalnya *Ilmu Kelautan* |

### Footer

| Isian | Arti | Cara mengisi |
|---|---|---|
| **Terindeks di (footer)** | Daftar layanan pengindeks berbentuk lencana teks. | Satu layanan per baris, opsional `|` dan tautan ke rekaman jurnal:<br>`DOAJ | https://doaj.org/toc/1234-5678`<br>`SINTA | https://sinta.kemdiktisaintek.go.id/journals/profile/0000`<br>Logo pengindeks tidak ditampilkan, agar tidak menimbulkan klaim yang keliru. |
| **Ikuti kami: situs web, Instagram, YouTube, LinkedIn, X** | Ikon media sosial di footer. | URL lengkap `https://`. Kosongkan yang tidak dipakai. |
| **URL ketentuan penggunaan** | Tautan *Ketentuan Penggunaan* di baris bawah footer. | URL `https://`. Pernyataan Privasi dan Kontak sudah otomatis dari OJS. |

### Editor-in-Chief (kartu di beranda)

| Isian | Cara mengisi |
|---|---|
| **Nama Editor-in-Chief** | Nama lengkap dengan gelar. **Kosongkan untuk menyembunyikan seluruh kartu.** |
| **Label jabatan** | Misalnya *Pemimpin Redaksi*. Jika kosong: *Editor-in-Chief*. |
| **Afiliasi** | Institusi editor. |
| **URL foto** | Unggah foto potret (rasio sekitar 4:5) melalui pustaka berkas OJS atau tempat lain yang aman, lalu tempel alamat `https://`. Alamat `http://` diabaikan. Jika kosong, tampil ikon. |
| **Biografi singkat** | 2–3 kalimat, tanpa format HTML. |

### Halaman artikel

| Isian | Arti |
|---|---|
| **Email penulis korespondensi** | Bawaan: *Sembunyikan*. Pilih *Tampilkan* hanya bila penulis telah menyetujui alamat emailnya dipublikasikan. |
| **Tanggal editorial** | Bawaan: *Tampilkan*. Menampilkan tanggal dikirim, direvisi, dan diterima pada artikel terbit (tanpa identitas editor/penelaah). Pilih *Sembunyikan* bila jurnal tidak ingin mempublikasikannya. |

---

## 3. Pengaturan OJS yang ikut membentuk tampilan

Theme mengambil data berikut langsung dari OJS. Lengkapi agar tampilan maksimal.

| Lokasi di OJS | Yang dipakai theme | Saran |
|---|---|---|
| Pengaturan > Jurnal > Masthead: **Nama, Singkatan, Deskripsi** | Judul header/hero, kotak *Tentang Jurnal* di beranda (sekitar 560 karakter pertama) | Tulis paragraf pertama deskripsi sebagai ringkasan yang utuh. |
| Pengaturan > Jurnal > Masthead: **ISSN cetak/online, Penerbit** | Baris ISSN di header, hero, footer; data terstruktur | Isi ISSN resmi saja. |
| Pengaturan > Jurnal > Kontak | Kontak di footer | Isi alamat, email, dan telepon resmi. |
| Pengaturan > Situs Web > Tampilan: **Logo** | Logo header, ditampilkan di samping nama jurnal | PNG/SVG latar transparan, tinggi minimal 160 px. Tanpa logo, nama jurnal tampil sebagai teks. |
| Pengaturan > Situs Web > Tampilan: **Homepage Image** | Mengganti latar hero dan banner halaman dalam | Gambar landscape minimal 1600×900 px, area kanan tidak terlalu ramai. Isi *teks alternatif*. |
| Pengaturan > Situs Web > Tampilan: **Favicon** | Ikon tab browser | Theme tidak memasang favicon sendiri. |
| Pengaturan > Situs Web > Tampilan: **Sidebar** | Blok (Informasi, Bahasa, blok kustom) | Di beranda, artikel, dan arsip, blok tampil sebagai pita di bawah konten. |
| Pengaturan > Situs Web > Tampilan: **Footer halaman** | Kotak teks tambahan di footer | Untuk lisensi situs atau catatan singkat, bukan untuk menu (lihat bagian 1). |
| Pengaturan > Situs Web > Pengaturan > **Pengumuman** | Bagian *Pengumuman* beranda (jumlah item mengikuti pengaturan) | Aktifkan dan tentukan jumlah pengumuman yang tampil di beranda. |
| Pengaturan > Situs Web > Pengaturan > **Mesin pencari: Deskripsi** | Teks hero bawaan dan meta description | 1–2 kalimat, maksimal 160 karakter. |
| Terbitan > **Sampul terbitan** | Sampul di beranda dan arsip | Rasio sekitar 3:4. Tanpa sampul, tampil sampul abstrak sesuai preset. |
| Publikasi artikel > **Gambar sampul** | Gambar kartu artikel | Opsional, rasio sekitar 16:7. |
| Plugin **Citation Style Language** | Kotak *Cara Mengutip* di artikel | Aktifkan agar sitasi dan unduhan sitasi tampil. |
| Plugin **DOI** | DOI di kartu, artikel, dan data terstruktur | Aktifkan dan konfigurasikan prefiks DOI resmi. |

### Format isi Dewan Redaksi (Pengaturan > Jurnal > Masthead > Dewan Redaksi)

Theme mengubah daftar menjadi kartu (Editor-in-Chief/Managing Editor) atau tabel (anggota lain) bila format berikut dipakai. Gunakan **Heading 2** untuk nama kelompok dan **daftar berpoin** untuk orang:

```html
<p>Kalimat pengantar (opsional).</p>
<h2>Editor-in-Chief</h2>
<ul>
  <li><strong>Dr. Nama Lengkap</strong><br>Editor-in-Chief<br>Universitas Contoh<br>Indonesia</li>
</ul>
<h2>Dewan Redaksi</h2>
<ul>
  <li><strong>Prof. Nama Anggota</strong> – Universitas Lain – Malaysia</li>
</ul>
```

Baris terakhir yang berisi nama negara otomatis diberi bendera. Tanpa JavaScript, isi tetap tampil seperti aslinya.

### Format Custom Page kebijakan

Gunakan **Heading 2** untuk setiap bagian (minimal dua). Theme membuat daftar *Di halaman ini*, menomori bagian, dan memberi ikon. Kutipan (*blockquote*) ditampilkan sebagai kotak catatan, dan daftar berpoin dengan 6 butir atau lebih dibagi menjadi dua kolom.

Catatan keamanan: isi Dewan Redaksi, Custom Page, dan ketentuan lisensi disaring dengan HTMLPurifier bawaan OJS. Yang diizinkan: daftar `allowed_html` di `config.inc.php` ditambah heading, tabel, dan kutipan. `<script>`, `<iframe>`, atribut `style`, dan atribut seperti `onclick` selalu dibuang, jadi video atau widget tidak dapat disematkan di halaman-halaman ini; gunakan tautan biasa.

---

## 4. Daftar periksa sebelum situs dipublikasikan

- [ ] Menu dibuat lengkap di **Navigasi**, tanpa item "Beranda" ganda.
- [ ] Logo, favicon, dan Homepage Image (opsional) terunggah dengan teks alternatif.
- [ ] Deskripsi jurnal, deskripsi mesin pencari, ISSN, penerbit, dan kontak terisi.
- [ ] Preset, ornamen, dan sumber font dipilih.
- [ ] Baris Informasi Jurnal hanya berisi fakta yang sesuai kebijakan resmi.
- [ ] "Terindeks di" hanya berisi pengindeks yang sudah resmi.
- [ ] Plugin Citation Style Language dan DOI aktif bila dipakai.
- [ ] Tampilan diperiksa di ponsel, tablet, dan desktop, dalam setiap bahasa jurnal.

---

## English summary

Every theme field may be left empty; empty elements are hidden and never replaced by sample text or claims. **Build all menus in Settings > Website > Setup > Navigation** (Primary and User Navigation Menus); the theme adds the Home link itself. Journal identity (name, logo, favicon, homepage image, description, ISSN, publisher, contact) comes from OJS settings. Use Heading 2 + bulleted lists in the Editorial Team text and Heading 2 sections in policy Custom Pages to get the enhanced layouts.
