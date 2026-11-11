#!/usr/bin/env python3
# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
"""SYNTHETIC TEST FIXTURE: OJS 3.5 Native XML for the NusantaraReborn test stack.

Every name, title and text is invented and marked "[Uji]" / "[Test]". It exercises:
two locales (en + id), several affiliations per author with and without ROR, non-ASCII names, several affiliations and countries, a long title,
missing DOI/ORCID/affiliation, PDF and no-galley articles, an issue with and without cover,
several archive years, and one queued (unpublished) submission that must never appear publicly.

  python3 -I make-native-xml.py issues   > issues.xml
  python3 -I make-native-xml.py queued   > queued.xml
"""
import base64, struct, sys, zlib
from xml.sax.saxutils import escape as e

LOCALES = ("en", "id")


def pdf_bytes(title):
    text = ("Synthetic test PDF: " + title)[:90].replace("(", "[").replace(")", "]").encode("ascii", "replace").decode()
    content = f"BT /F1 12 Tf 50 750 Td ({text}) Tj ET".encode()
    objs = [b"<< /Type /Catalog /Pages 2 0 R >>",
            b"<< /Type /Pages /Kids [3 0 R] /Count 1 >>",
            b"<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>",
            b"<< /Length " + str(len(content)).encode() + b" >>\nstream\n" + content + b"\nendstream",
            b"<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>"]
    out = b"%PDF-1.4\n"
    offs = []
    for i, o in enumerate(objs, 1):
        offs.append(len(out))
        out += f"{i} 0 obj\n".encode() + o + b"\nendobj\n"
    x = len(out)
    out += f"xref\n0 {len(objs)+1}\n0000000000 65535 f \n".encode() + b"".join(f"{o:010d} 00000 n \n".encode() for o in offs)
    out += f"trailer\n<< /Size {len(objs)+1} /Root 1 0 R >>\nstartxref\n{x}\n%%EOF\n".encode()
    return out


def png(width, height, seed):
    """Deterministic gradient PNG (test cover / graphical abstract)."""
    rows = []
    for y in range(height):
        row = bytearray([0])
        for x in range(width):
            row.extend(((20 + seed * 40 + x * 60 // width) % 256, (70 + y * 90 // height) % 256, (90 + seed * 30 + (x + y) * 70 // (width + height)) % 256))
        rows.append(bytes(row))

    def chunk(kind, data):
        return struct.pack(">I", len(data)) + kind + data + struct.pack(">I", zlib.crc32(kind + data) & 0xffffffff)
    return (b"\x89PNG\r\n\x1a\n" + chunk(b"IHDR", struct.pack(">IIBBBBB", width, height, 8, 2, 0, 0, 0))
            + chunk(b"IDAT", zlib.compress(b"".join(rows), 9)) + chunk(b"IEND", b""))


FILE_ID = [100]


def loc(tag, values, attrs=""):
    """values: str (en only) or dict locale->text."""
    if isinstance(values, str):
        values = {"en": values}
    return "".join(f'<{tag} locale="{l}"{attrs}>{e(v)}</{tag}>' for l, v in values.items() if v)


def submission_file(title):
    FILE_ID[0] += 1
    fid = FILE_ID[0]
    data = pdf_bytes(title)
    xml = f'''      <submission_file id="{fid}" file_id="{fid}" stage="proof" genre="Article Text" uploader="admin" viewable="false" created_at="2026-01-10" updated_at="2026-01-10">
        <name locale="en">{fid}-article.pdf</name>
        <file id="{fid}" filesize="{len(data)}" extension="pdf"><embed encoding="base64" mime_type="application/pdf">{base64.b64encode(data).decode()}</embed></file>
      </submission_file>'''
    return fid, xml


def article(n, a, status=3, in_issue=True):
    files, galleys = [], []
    for seq, kind in enumerate(a.get("galleys", ["pdf"])):
        fid, fx = submission_file(a["title"]["en"] if isinstance(a["title"], dict) else a["title"])
        files.append(fx)
        galleys.append(f'''        <article_galley locale="en" approved="true">
          <id type="internal" advice="ignore">{n*10+seq}</id>
          <name locale="en">PDF</name>
          <seq>{seq}</seq>
          <submission_file_ref id="{fid}"/>
        </article_galley>''')
    authors = []
    for i, au in enumerate(a["authors"]):
        aid = n * 100 + i
        parts = [loc("givenname", au["given"]), loc("familyname", au.get("family", ""))]
        affs = au.get("affs") or ([{"name": au["aff"]}] if au.get("aff") else [])
        for aff in affs:
            names = aff["name"] if isinstance(aff["name"], dict) else {"en": aff["name"]}
            inner = "".join(f'<name locale="{l}">{e(v)}</name>' for l, v in names.items())
            if aff.get("ror"):
                parts.append(f'<rorAffiliation><ror>{aff["ror"]}</ror>{inner}</rorAffiliation>')
            else:
                parts.append(f'<affiliation>{inner}</affiliation>')
        if au.get("country"):
            parts.append(f'<country>{au["country"]}</country>')
        parts.append(f'<email>author{aid}@example.test</email>')
        if au.get("orcid"):
            parts.append(f'<orcid>{au["orcid"]}</orcid>')
        if au.get("bio"):
            parts.append(loc("biography", "&lt;p&gt;" + au["bio"] + "&lt;/p&gt;").replace("&amp;lt;", "&lt;").replace("&amp;gt;", "&gt;"))
        authors.append(f'''          <author include_in_browse="true" user_group_ref="Author" seq="{i}" id="{aid}">
            {''.join(parts)}
          </author>''')
    ids = f'\n        <id type="doi" advice="update">{a["doi"]}</id>' if a.get("doi") else ""
    kw = ""
    for l, words in a.get("keywords", {}).items():
        kw += f'\n        <keywords locale="{l}">' + "".join(f"<keyword><name>{e(k)}</name></keyword>" for k in words) + "</keywords>"
    cites = "".join(f"<citation>{e(c)}</citation>" for c in a.get("citations", []))
    cites = f"\n        <citations>{cites}</citations>" if cites else ""
    pages = f'\n        <pages>{a["pages"]}</pages>' if a.get("pages") else ""
    cover = ""
    if a.get("cover"):
        cover = (f'\n        <covers><cover locale="en"><cover_image>synthetic-abstract-{n}.png</cover_image>'
                 f'<cover_image_alt_text>Synthetic graphical abstract</cover_image_alt_text>'
                 f'<embed encoding="base64" mime_type="image/png">{base64.b64encode(png(640, 360, n)).decode()}</embed></cover></covers>')
    titles = a["title"] if isinstance(a["title"], dict) else {"en": a["title"]}
    abstracts = a.get("abstract", {"en": "[Test] Synthetic abstract placeholder."})
    abstract_xml = "".join(f'<abstract locale="{l}">&lt;p&gt;{e(v)}&lt;/p&gt;</abstract>' for l, v in abstracts.items())
    date_pub = f' date_published="{a["published"]}"' if status == 3 else ""
    section = a.get("section", "ART")
    return f'''    <article xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" locale="{a.get('locale', 'en')}" date_submitted="{a["submitted"]}" status="{status}" submission_progress="0" current_publication_id="{n}" stage="{'production' if status == 3 else 'submission'}">
      <id type="internal" advice="ignore">{n}</id>
{chr(10).join(files)}
      <publication version="1" status="{status}" primary_contact_id="{n*100 + a.get('primary', 0)}" url_path="" seq="{a.get('seq', 0)}"{date_pub} section_ref="{section}" access_status="{a.get('access_status', 0)}">
        <id type="internal" advice="ignore">{n}</id>{ids}
        {"".join(f'<title locale="{l}">{e(v)}</title>' for l, v in titles.items())}
        {abstract_xml}
        <licenseUrl>https://creativecommons.org/licenses/by/4.0/</licenseUrl>
        <copyrightHolder locale="en">{e(a.get("holder", "The Authors (synthetic test data)"))}</copyrightHolder>
        <copyrightYear>2026</copyrightYear>{kw}
        <authors xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
{chr(10).join(authors)}
        </authors>
{chr(10).join(galleys)}{cites}{pages}{cover}
      </publication>
    </article>'''


ABS_EN = ("[Test] This synthetic abstract exists only to exercise the article page. It describes an invented "
          "study of coastal settlement change using multi-temporal imagery and community mapping, with enough "
          "length to inspect line length, paragraph spacing and wrapping on small screens.")
ABS_ID = ("[Uji] Abstrak sintetis ini hanya untuk menguji halaman artikel. Isinya menggambarkan studi rekaan tentang "
          "perubahan permukiman pesisir menggunakan citra multi-waktu dan pemetaan partisipatif, dengan panjang yang "
          "cukup untuk memeriksa panjang baris dan pemenggalan pada layar kecil.")
CITES = [f"Penulis Uji {i}, A. (202{i % 6}). Synthetic reference number {i} with a long title for wrapping checks. Jurnal Rujukan Sintetis, {i}({i}), {i*10}–{i*10+9}. https://doi.org/10.99999/synthetic.{i}" for i in range(1, 9)]
CITES.append("Ñúñez, J., & Østergaard, K. (2025). Unicode reference ‘quoted’ & <tagged> text: a synthetic edge case. https://example.test/reference?with=query&and=amp")

ISSUES = [
    dict(vol=3, num="2", year=2026, published="2026-06-30", current=1, cover=False, sections=True,
         title={"en": "Coastal Futures", "id": "Masa Depan Pesisir"},
         articles=[
             dict(title={"en": "[Test] Community Mapping of Coastal Settlement Change in a Synthetic Archipelago",
                         "id": "[Uji] Pemetaan Partisipatif Perubahan Permukiman Pesisir di Kepulauan Sintetis"},
                  abstract={"en": ABS_EN, "id": ABS_ID}, submitted="2026-01-12", published="2026-06-30", pages="101–118",
                  doi="10.99999/nr.2026.0201", cover=True, citations=CITES,
                  keywords={"en": ["Coastal change", "Community mapping", "Remote sensing"], "id": ["Perubahan pesisir", "Pemetaan partisipatif", "Penginderaan jauh"]},
                  authors=[dict(given={"en": "Ni Luh Putu", "id": "Ni Luh Putu"}, family={"en": "Sri Wahyuni", "id": "Sri Wahyuni"},
                                affs=[{"name": {"en": "Faculty of Marine Science, Synthetic State University", "id": "Fakultas Ilmu Kelautan, Universitas Negeri Sintetis"}, "ror": "https://ror.org/04brc3a25"},
                                      {"name": "Centre for Coastal Studies (synthetic)"}],
                                country="ID", orcid="https://orcid.org/0000-0002-1825-0097", bio="Synthetic biography used for layout testing."),
                           dict(given={"en": "Đặng Thị"}, family={"en": "Minh Anh"}, aff={"en": "Institute of Test Geography, Example University"}, country="VN"),
                           dict(given={"en": "Jürgen"}, family={"en": "Müller-Østergaard"}, aff={"en": "Faculty of Marine Science, Synthetic State University", "id": "Fakultas Ilmu Kelautan, Universitas Negeri Sintetis"}, country="DE",
                                orcid="https://orcid.org/0000-0001-5109-3700")]),
             dict(title="[Test] A Deliberately Very Long Article Title Used to Verify the Wrapping of Headings, Cards, Breadcrumbs, Citation Panels and Metadata Rows Across Desktop, Tablet and Narrow Mobile Viewports Without Horizontal Overflow",
                  seq=1, submitted="2026-02-01", published="2026-06-30", pages="119–140", section="REV",
                  authors=[dict(given="Test", family="Single Author Without Affiliation")]),
             dict(title={"en": "[Test] Multi-author Collaboration Fixture", "id": "[Uji] Kolaborasi Banyak Penulis"}, seq=2,
                  submitted="2026-01-20", published="2026-06-30", pages="141–160", doi="10.99999/nr.2026.0203",
                  authors=[dict(given="Test", family=f"Coauthor {i}", aff=f"Synthetic Affiliation {i % 3}", country=["ID", "JP", "BR"][i % 3]) for i in range(6)]),
             dict(title={"en": "[Test] Abstract-only Article Without Galley", "id": "[Uji] Artikel Tanpa Berkas"}, seq=3, galleys=[],
                  submitted="2026-02-15", published="2026-06-30", locale="id",
                  authors=[dict(given={"en": "Siti", "id": "Siti"}, family={"en": "Rahmawati", "id": "Rahmawati"})]),
         ]),
    dict(vol=3, num="1", year=2026, published="2026-01-15", current=0, cover=True,
         articles=[dict(title="[Test] Tidal Energy Fixture Article", submitted="2025-09-01", published="2026-01-15", pages="1–20", doi="10.99999/nr.2026.0101",
                        authors=[dict(given="Test", family="Author Seven", aff="Synthetic Polytechnic", country="NZ")]),
                   dict(title="[Test] Mangrove Fixture Article", seq=1, submitted="2025-08-11", published="2026-01-15", pages="21–40",
                        authors=[dict(given="Test", family="Author Eight", aff="Synthetic Research Centre", country="PH")])]),
    dict(vol=2, num="1", year=2025, published="2025-06-20", current=0, cover=False,
         articles=[dict(title="[Test] Archive Year 2025 Fixture", submitted="2025-01-05", published="2025-06-20", pages="1–12",
                        authors=[dict(given="Test", family="Author Nine", aff="Synthetic College", country="MY")])]),
]

QUEUED = dict(title="[UNPUBLISHED] Confidential Draft Must Never Appear Publicly", submitted="2026-05-01",
              authors=[dict(given="Hidden", family="Unpublished Author", aff="Private Affiliation", country="ID")], galleys=[])

SECTIONS = '''    <sections>
      <section ref="ART" seq="0" editor_restricted="0" meta_indexed="1" meta_reviewed="1" abstracts_not_required="0" hide_title="0" hide_author="0" abstract_word_count="0"><abbrev locale="en">ART</abbrev><abbrev locale="id">ART</abbrev><title locale="en">Research Article</title><title locale="id">Artikel Penelitian</title></section>
      <section ref="REV" seq="1" editor_restricted="0" meta_indexed="1" meta_reviewed="1" abstracts_not_required="0" hide_title="0" hide_author="0" abstract_word_count="0"><abbrev locale="en">REV</abbrev><abbrev locale="id">TIN</abbrev><title locale="en">Review Article</title><title locale="id">Artikel Tinjauan</title></section>
    </sections>'''


def issues_xml():
    out = ['<?xml version="1.0" encoding="UTF-8"?>',
           '<issues xmlns="http://pkp.sfu.ca" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://pkp.sfu.ca native.xsd">']
    n = 0
    for k, iss in enumerate(ISSUES):
        arts = []
        for a in iss["articles"]:
            n += 1
            arts.append(article(n, a))
        title = ""
        if iss.get("title"):
            title = "\n    " + "".join(f'<title locale="{l}">{e(v)}</title>' for l, v in iss["title"].items())
        cover = ""
        if iss.get("cover"):
            cover = (f'\n    <covers><cover locale="en"><cover_image>synthetic-issue-{k}.png</cover_image><cover_image_alt_text>Synthetic issue cover</cover_image_alt_text>'
                     f'<embed encoding="base64" mime_type="image/png">{base64.b64encode(png(360, 500, k + 3)).decode()}</embed></cover></covers>')
        out.append(f'''  <issue xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" published="1" current="{iss["current"]}" access_status="1">
    <id type="internal" advice="ignore">{k + 1}</id>
    <description locale="en">&lt;p&gt;[Test] Synthetic issue description.&lt;/p&gt;</description>
    <description locale="id">&lt;p&gt;[Uji] Deskripsi terbitan sintetis.&lt;/p&gt;</description>
    <issue_identification><volume>{iss["vol"]}</volume><number>{iss["num"]}</number><year>{iss["year"]}</year>{title}</issue_identification>
    <date_published>{iss["published"]}</date_published>
    <last_modified>{iss["published"]}</last_modified>
{SECTIONS if iss.get("sections") else ""}{cover}
    <articles>
{chr(10).join(arts)}
    </articles>
  </issue>''')
    out.append("</issues>")
    return "\n".join(out) + "\n"


def queued_xml():
    return ('<?xml version="1.0" encoding="UTF-8"?>\n'
            '<articles xmlns="http://pkp.sfu.ca" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://pkp.sfu.ca native.xsd">\n'
            + article(99, QUEUED, status=1, in_issue=False) + "\n</articles>\n")


if __name__ == "__main__":
    sys.stdout.write(queued_xml() if (len(sys.argv) > 1 and sys.argv[1] == "queued") else issues_xml())
