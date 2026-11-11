#!/usr/bin/env python3
# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
"""Write the NusantaraReborn locale files from one table of Indonesian/English strings.

  python3 -I scripts/make-locales.py 33   -> ojs33/nusantaraReborn/locale/{id_ID,en_US}/locale.po
  python3 -I scripts/make-locales.py 35   -> ojs35/nusantaraReborn/locale/{id,en}/locale.po
"""
import os, sys

HERE = os.path.dirname(os.path.abspath(__file__))
ROOT = os.path.dirname(HERE)
P = "plugins.themes.nusantaraReborn."

# key suffix: (Indonesian, English)
S = {
    "name": ("NusantaraReborn", "NusantaraReborn"),
    "description": (
        "Theme jurnal ilmiah umum bernuansa Indonesia dengan lima preset warna, ornamen Nusantara opsional, dan pilihan font sistem atau Google Fonts. Identitas, kebijakan, dan metadata tetap berasal dari pengaturan jurnal di OJS.",
        "A general scholarly journal theme with an Indonesian character: five colour presets, optional Nusantara ornaments, and system or Google fonts. Identity, policies and metadata always come from the journal's OJS settings."),

    # Options: presentation
    "option.colourScheme.label": ("Preset warna", "Colour preset"),
    "option.colourScheme.description": (
        "Pilih satu dari lima preset bernuansa Indonesia. Preset mengubah palet, ornamen, latar hero bawaan, dan pasangan font Google (bila sumber font Google dipilih). Struktur dan fitur halaman tidak berubah.",
        "Choose one of five Indonesian-inspired presets. A preset changes the palette, ornaments, default hero background and the Google font pair (when Google fonts are selected). Page structure and features stay the same."),
    "option.colourScheme.zamrudKhatulistiwa": ("Zamrud Khatulistiwa — hijau, krem, emas", "Zamrud Khatulistiwa — green, cream, gold"),
    "option.colourScheme.bahariNusantara": ("Bahari Nusantara — biru, gading, turkuois", "Bahari Nusantara — blue, ivory, turquoise"),
    "option.colourScheme.batikSogan": ("Batik Sogan — cokelat sogan, gading, tembaga", "Batik Sogan — sogan brown, ivory, copper"),
    "option.colourScheme.merahPutih": ("Merah Putih — merah, putih, arang", "Merah Putih — red, white, charcoal"),
    "option.colourScheme.tenunSenja": ("Tenun Senja — nila, pasir, terakota", "Tenun Senja — indigo, sand, terracotta"),
    "option.nusantaraOrnaments.label": ("Ornamen Nusantara", "Nusantara ornaments"),
    "option.nusantaraOrnaments.description": (
        "Aksen dekoratif halus pada hero, garis header, dan footer. Area baca (abstrak, artikel, referensi, tabel, formulir) selalu berlatar bersih.",
        "Subtle decorative accents on the hero, header line and footer. Reading areas (abstract, article, references, tables, forms) always stay plain."),
    "option.nusantaraOrnaments.subtle": ("Halus", "Subtle"),
    "option.nusantaraOrnaments.off": ("Nonaktif", "Off"),
    "option.fontSource.label": ("Sumber font", "Font source"),
    "option.fontSource.description": (
        "Font sistem tidak memuat berkas dari luar server. Google Fonts memuat pasangan font preset dari fonts.googleapis.com dengan font sistem sebagai cadangan.",
        "System fonts load no external files. Google Fonts loads the preset font pair from fonts.googleapis.com, with system fonts as fallback."),
    "option.fontSource.system": ("Font sistem (tanpa permintaan eksternal)", "System fonts (no external requests)"),
    "option.fontSource.google": ("Google Fonts sesuai preset", "Google Fonts for the preset"),

    # Options: identity text
    "option.journalTagline.label": ("Tagline jurnal", "Journal tagline"),
    "option.journalTagline.description": ("Baris pendek di bawah nama jurnal pada header. Kosongkan untuk menyembunyikan.", "Short line under the journal name in the header. Leave empty to hide."),
    "option.heroDescription.label": ("Teks hero beranda", "Homepage hero text"),
    "option.heroDescription.description": ("Satu atau dua kalimat di bawah judul jurnal. Jika kosong, deskripsi mesin pencari jurnal yang dipakai; jika itu juga kosong, teks disembunyikan.", "One or two sentences under the journal title. If empty, the journal's search engine description is used; if that is empty too, nothing is shown."),
    "option.heroCaption.label": ("Keterangan gambar hero", "Hero image caption"),
    "option.heroCaption.description": ("Keterangan kecil di pojok gambar hero, misalnya sumber foto Homepage Image. Kosongkan untuk menyembunyikan.", "Small caption in the corner of the hero, e.g. the credit for the Homepage Image. Leave empty to hide."),
    "option.publisherLine.label": ("Baris penerbit", "Publisher line"),
    "option.publisherLine.description": ("Teks penerbit pada footer. Jika kosong, nama penerbit dari Pengaturan > Jurnal dipakai; jika itu juga kosong, baris disembunyikan.", "Publisher text in the footer. If empty, the publisher from Settings > Journal is used; if that is empty too, the line is hidden."),
    "option.publisherUrl.label": ("URL penerbit", "Publisher URL"),
    "option.publisherUrl.description": ("Alamat https:// penerbit untuk tautan footer dan data terstruktur. Kosongkan bila tidak ada.", "The publisher's https:// address for the footer link and structured data. Leave empty if none."),
    "option.featureHighlights.label": ("Sorotan profil jurnal", "Journal highlights"),
    "option.featureHighlights.description": ("Hingga empat sorotan singkat di bawah deskripsi beranda, dipisahkan titik koma. Isi hanya hal yang benar untuk jurnal ini. Kosongkan untuk menyembunyikan.", "Up to four short highlights under the homepage description, separated by semicolons. Only list what is true for this journal. Leave empty to hide."),

    # Options: journal information rows
    "option.publicationFrequencyLabel.label": ("Frekuensi terbit", "Publication frequency"),
    "option.publicationFrequencyLabel.description": ("Contoh: Dua kali setahun (Juni dan Desember). Kosongkan untuk menyembunyikan baris.", "Example: Twice a year (June and December). Leave empty to hide the row."),
    "option.peerReviewLabel.label": ("Proses telaah", "Review process"),
    "option.peerReviewLabel.description": ("Jelaskan proses telaah sesuai kebijakan jurnal, misalnya Double-blind peer review. Kosongkan untuk menyembunyikan baris.", "Describe the review process as stated in the journal policy, e.g. Double-blind peer review. Leave empty to hide the row."),
    "option.apcLabel.label": ("Biaya pemrosesan artikel (APC)", "Article processing charge (APC)"),
    "option.apcLabel.description": ("Contoh: Tanpa biaya. Jika kosong, biaya publikasi OJS ditampilkan saat pembayaran aktif; selain itu baris disembunyikan.", "Example: No charge. If empty, the OJS publication fee is shown when payments are enabled; otherwise the row is hidden."),
    "option.languageLabel.label": ("Bahasa jurnal", "Journal languages"),
    "option.languageLabel.description": ("Jika kosong, bahasa naskah yang diaktifkan di OJS dipakai.", "If empty, the submission languages enabled in OJS are used."),
    "option.indexingLabel.label": ("Pengindeksan (ringkas)", "Indexing (short)"),
    "option.indexingLabel.description": ("Satu baris ringkas tentang pengindeksan yang benar-benar berlaku. Kosongkan untuk menyembunyikan baris.", "One short line about indexing that actually applies. Leave empty to hide the row."),
    "option.archivingLabel.label": ("Pengarsipan", "Archiving"),
    "option.archivingLabel.description": ("Contoh: PKP PN, LOCKSS. Kosongkan untuk menyembunyikan baris.", "Example: PKP PN, LOCKSS. Leave empty to hide the row."),
    "option.subjectAreaLabel.label": ("Bidang ilmu", "Subject area"),
    "option.subjectAreaLabel.description": ("Ditampilkan di halaman Dewan Redaksi. Kosongkan untuk menyembunyikan.", "Shown on the Editorial Team page. Leave empty to hide."),
    "option.indexedIn.label": ("Terindeks di (footer)", "Indexed in (footer)"),
    "option.indexedIn.description": ("Satu layanan per baris, opsional diikuti | dan tautan ke rekaman jurnal. Contoh: DOAJ | https://doaj.org/toc/xxxx-xxxx. Isi hanya layanan yang benar-benar mengindeks jurnal. Kosongkan untuk menyembunyikan.", "One service per line, optionally followed by | and a link to the journal record. Example: DOAJ | https://doaj.org/toc/xxxx-xxxx. Only list services that actually index the journal. Leave empty to hide."),

    # Options: editor and links
    "option.editorInChiefName.label": ("Nama Editor-in-Chief (beranda)", "Editor-in-Chief name (homepage)"),
    "option.editorInChiefName.description": ("Kosongkan untuk menyembunyikan kartu Editor-in-Chief pada beranda.", "Leave empty to hide the Editor-in-Chief card on the homepage."),
    "option.editorInChiefRole.label": ("Label jabatan", "Role label"),
    "option.editorInChiefRole.description": ("Jika kosong: Editor-in-Chief.", "If empty: Editor-in-Chief."),
    "option.editorInChiefAffiliation.label": ("Afiliasi Editor-in-Chief", "Editor-in-Chief affiliation"),
    "option.editorInChiefPhoto.label": ("URL foto Editor-in-Chief", "Editor-in-Chief photo URL"),
    "option.editorInChiefPhoto.description": ("Alamat https:// foto. Kosongkan untuk menampilkan ikon.", "An https:// photo address. Leave empty to show an icon."),
    "option.editorInChiefBio.label": ("Biografi singkat Editor-in-Chief", "Short Editor-in-Chief biography"),
    "option.showCorrespondingEmail.label": ("Email penulis korespondensi di halaman artikel", "Corresponding author email on article pages"),
    "option.showCorrespondingEmail.description": ("Bawaan: disembunyikan. Tampilkan hanya bila penulis telah menyetujui publikasi alamat email.", "Default: hidden. Show only when authors agreed to publish their email address."),
    "option.show": ("Tampilkan", "Show"),
    "option.hide": ("Sembunyikan", "Hide"),
    "option.socialWebsite.label": ("Ikuti kami: URL situs web", "Follow us: website URL"),
    "option.socialInstagram.label": ("Ikuti kami: URL Instagram", "Follow us: Instagram URL"),
    "option.socialYoutube.label": ("Ikuti kami: URL YouTube", "Follow us: YouTube URL"),
    "option.socialLinkedin.label": ("Ikuti kami: URL LinkedIn", "Follow us: LinkedIn URL"),
    "option.socialX.label": ("Ikuti kami: URL X", "Follow us: X URL"),
    "option.termsUrl.label": ("URL ketentuan penggunaan (footer)", "Terms of use URL (footer)"),

    # Navigation
    "nav.home": ("Beranda", "Home"),
    "nav.openMenu": ("Menu", "Menu"),

    # Homepage
    "home.openAccess": ("Akses Terbuka", "Open Access"),
    "home.submitManuscript": ("Kirim Naskah", "Submit Manuscript"),
    "home.viewCurrentIssue": ("Lihat Terbitan Terkini", "View Current Issue"),
    "home.readMore": ("Selengkapnya tentang jurnal", "More about the journal"),
    "home.journalInformation": ("Informasi Jurnal", "Journal Information"),
    "home.frequency": ("Frekuensi Terbit", "Publication Frequency"),
    "home.peerReview": ("Proses Telaah", "Review Process"),
    "home.apc": ("Biaya Pemrosesan Artikel", "Article Processing Charge"),
    "home.language": ("Bahasa", "Language"),
    "home.indexing": ("Pengindeksan", "Indexing"),
    "home.archiving": ("Pengarsipan", "Archiving"),
    "home.viewAllIssues": ("Lihat Semua Terbitan", "View All Issues"),
    "home.inThisIssue": ("Dalam terbitan ini", "In this issue"),
    "home.latestArticles": ("Artikel Terbaru", "Latest Articles"),
    "home.viewAllArticles": ("Lihat Semua Artikel", "View All Articles"),
    "home.viewAllUpdates": ("Lihat Semua Pengumuman", "View All Announcements"),
    "home.editorInChief": ("Editor-in-Chief", "Editor-in-Chief"),
    "home.viewEditorialTeam": ("Lihat Dewan Redaksi", "View Editorial Team"),

    # Archive
    "archive.intro": ("Telusuri semua terbitan {$journal}.", "Browse every issue of {$journal}."),
    "archive.browseByYear": ("Telusuri per Tahun", "Browse by Year"),
    "archive.allYears": ("Semua Tahun", "All Years"),
    "archive.search": ("Cari Artikel", "Search Articles"),
    "archive.searchPlaceholder": ("Judul, penulis, atau kata kunci", "Title, author or keyword"),
    "archive.oneIssue": ("1 terbitan", "1 issue"),
    "archive.manyIssues": ("{$count} terbitan", "{$count} issues"),
    "archive.oneArticle": ("1 artikel", "1 article"),
    "archive.manyArticles": ("{$count} artikel", "{$count} articles"),
    "archive.viewIssue": ("Lihat Terbitan", "View Issue"),

    # Article
    "article.received": ("Dikirim", "Received"),
    "article.revised": ("Direvisi", "Revised"),
    "article.accepted": ("Diterima", "Accepted"),
    "article.keywords": ("Kata Kunci", "Keywords"),
    "article.referencesPreview": ("Referensi", "References"),
    "article.viewAllReferences": ("Lihat Semua Referensi", "View All References"),
    "article.downloadPdf": ("Unduh PDF", "Download PDF"),
    "article.readOnline": ("Baca daring", "Read online"),
    "article.share": ("Bagikan", "Share"),
    "article.copyLink": ("Salin tautan", "Copy link"),
    "article.copyCitation": ("Salin Sitasi", "Copy Citation"),
    "article.information": ("Informasi Artikel", "Article Information"),
    "article.type": ("Jenis Artikel", "Article Type"),
    "article.volumeIssuePages": ("Volume, Nomor, Halaman", "Volume, Issue, Pages"),
    "article.pagesShort": ("hlm. {$pages}", "pp. {$pages}"),
    "article.views": ("Dilihat", "Views"),
    "article.downloads": ("Unduhan", "Downloads"),
    "article.correspondingAuthor": ("Penulis Korespondensi", "Corresponding Author"),
    "article.licenseText": ("Artikel ini didistribusikan di bawah ketentuan {$license}.", "This article is distributed under the terms of the {$license}."),
    "article.viewLicense": ("Lihat Detail Lisensi", "View License Details"),
    "article.related": ("Artikel Terkait", "Related Articles"),
    "article.moreArticles": ("Lihat Arsip", "Browse the Archive"),

    # Editorial team
    "editorial.office": ("Kantor Redaksi", "Editorial Office"),
    "editorial.publishedBy": ("Diterbitkan oleh", "Published by"),
    "editorial.subjectArea": ("Bidang Ilmu", "Subject Area"),
    "editorial.openAccessText": ("Konten jurnal tersedia secara akses terbuka", "Journal content is available open access"),

    # Policy pages
    "policy.onThisPage": ("Di halaman ini", "On this page"),
    "policy.relatedInformation": ("Informasi Terkait", "Related Information"),
    "policy.journalContact": ("Kontak Jurnal", "Journal Contact"),

    # Footer
    "footer.indexedIn": ("Terindeks di", "Indexed in"),
    "footer.followUs": ("Ikuti Kami", "Follow Us"),
    "footer.website": ("Situs web", "Website"),
    "footer.links": ("Tautan jurnal", "Journal links"),
    "footer.privacy": ("Pernyataan Privasi", "Privacy Statement"),
    "footer.terms": ("Ketentuan Penggunaan", "Terms of Use"),
    "footer.platform": ("Open Journal Systems {$version}", "Open Journal Systems {$version}"),
    "footer.themeCredit": ("Theme NusantaraReborn", "NusantaraReborn theme"),
}

TARGETS = {"33": (("id_ID", 0), ("en_US", 1)), "35": (("id", 0), ("en", 1))}


def esc(value):
    return value.replace("\\", "\\\\").replace('"', '\\"')


def main():
    track = sys.argv[1] if len(sys.argv) > 1 else "33"
    base = os.path.join(ROOT, "ojs" + track, "nusantaraReborn", "locale")
    for locale, col in TARGETS[track]:
        lines = [
            "# NusantaraReborn theme — " + locale,
            "# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).",
            "# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.",
            "# Generated by scripts/make-locales.py; edit that file instead.",
            'msgid ""',
            'msgstr ""',
            '"Project-Id-Version: NusantaraReborn\\n"',
            f'"Language: {locale}\\n"',
            '"MIME-Version: 1.0\\n"',
            '"Content-Type: text/plain; charset=UTF-8\\n"',
            '"Content-Transfer-Encoding: 8bit\\n"',
            "",
        ]
        for key in sorted(S):
            lines += [f'msgid "{P}{key}"', f'msgstr "{esc(S[key][col])}"', ""]
        os.makedirs(os.path.join(base, locale), exist_ok=True)
        with open(os.path.join(base, locale, "locale.po"), "w", encoding="utf-8") as f:
            f.write("\n".join(lines))
        print("wrote", locale, len(S), "strings")


if __name__ == "__main__":
    main()
