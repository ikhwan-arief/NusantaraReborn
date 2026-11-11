#!/usr/bin/env python3
# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
"""SYNTHETIC TEST FIXTURE SQL for the NusantaraReborn OJS 3.3.0-24 stack.

Journal 1 (path "nusantara") gets: bilingual settings, DOI plugin + stored DOIs, a custom
"Policies" page in the primary menu, announcements, a Custom Block Manager block, bundled
metadata/citation plugins, the hook-fixture plugin, synthetic editor decisions and metrics.
Journal 2 (path "kosong") stays deliberately empty: no issues, logo, description or options.
"""
import sys


def q(v):
    return "'" + str(v).replace("\\", "\\\\").replace("'", "''") + "'"


J = 1
out = ["SET NAMES utf8mb4;"]

# --- Journal settings (bilingual) ------------------------------------------------
settings = [
    ("description", "en_US", "<p>[Test] The Synthetic Journal of Nusantara Studies is an invented journal used only to test the NusantaraReborn theme. It publishes placeholder research on coastal, cultural and environmental topics.</p>"),
    ("description", "id_ID", "<p>[Uji] Jurnal Sintetis Kajian Nusantara adalah jurnal rekaan yang hanya dipakai untuk menguji theme NusantaraReborn. Isinya penelitian contoh tentang pesisir, budaya, dan lingkungan.</p>"),
    ("searchDescription", "en_US", "[Test] Invented journal for theme testing: coastal, cultural and environmental studies."),
    ("searchDescription", "id_ID", "[Uji] Jurnal rekaan untuk pengujian theme: kajian pesisir, budaya, dan lingkungan."),
    ("printIssn", "", "0000-0019"),
    ("onlineIssn", "", "0000-0027"),
    ("publisherInstitution", "", "Synthetic Publishing Cooperative (test data)"),
    ("mailingAddress", "", "Jl. Contoh No. 1\nKota Sintetis 00000\nIndonesia (test address)"),
    ("contactPhone", "", "+62 000 0000 000"),
    ("editorialTeam", "en_US", "<p>[Test] Synthetic editorial board used for layout checks.</p><h2>Editor-in-Chief</h2><ul><li><strong>Dr. Test Editor Utama</strong><br>Editor-in-Chief<br>Synthetic State University<br>Indonesia</li></ul><h2>Editorial Board</h2><ul><li><strong>Prof. Ayu Kartika Sari</strong> – Synthetic University – Indonesia</li><li><strong>Dr. Jean-Luc Ñúñez</strong> – Example Institute – Spain</li><li><strong>Dr. Hana Sato</strong> – Test University – Japan</li></ul>"),
    ("editorialTeam", "id_ID", "<p>[Uji] Dewan redaksi sintetis untuk pemeriksaan tata letak.</p><h2>Pemimpin Redaksi</h2><ul><li><strong>Dr. Test Editor Utama</strong><br>Pemimpin Redaksi<br>Universitas Negeri Sintetis<br>Indonesia</li></ul>"),
    ("numAnnouncementsHomepage", "", "3"),
    ("enableAnnouncements", "", "1"),
    ("enableAnnouncementsHomepage", "", "1"),
    ("additionalHomeContent", "en_US", "<p>[Test] Additional homepage content entered in Settings &gt; Website.</p>"),
]
for name, locale, value in settings:
    out.append(f"DELETE FROM journal_settings WHERE journal_id={J} AND setting_name={q(name)} AND locale={q(locale)};")
    out.append(f"INSERT INTO journal_settings (journal_id, locale, setting_name, setting_value) VALUES ({J}, {q(locale)}, {q(name)}, {q(value)});")
out.append(f"UPDATE journal_settings SET setting_value='1' WHERE journal_id={J} AND setting_name='publishingMode';")

# --- Plugins: DOI, metadata, citation, viewers, statistics ------------------------
plugins = [("doipubidplugin", [("enabled", "1", "bool"), ("doiPrefix", "10.99999", "string"), ("enablePublicationDoi", "1", "bool"),
                               ("enableIssueDoi", "0", "bool"), ("enableRepresentationDoi", "0", "bool"), ("doiSuffix", "default", "string")]),
           ("citationstylelanguageplugin", [("enabled", "1", "bool")]),
           ("googlescholarplugin", [("enabled", "1", "bool")]),
           ("dublincoremetaplugin", [("enabled", "1", "bool")]),
           ("pdfjsviewerplugin", [("enabled", "1", "bool")]),
           ("htmlarticlegalleyplugin", [("enabled", "1", "bool")]),
           ("usagestatsplugin", [("enabled", "1", "bool")]),
           ("staticpagesplugin", [("enabled", "1", "bool")]),
           ("informationblockplugin", [("enabled", "1", "bool")]),
           ("languagetoggleblockplugin", [("enabled", "1", "bool")]),
           ("nrhookfixtureplugin", [("enabled", "1", "bool")])]
for plugin, rows in plugins:
    for name, val, typ in rows:
        out.append(f"REPLACE INTO plugin_settings (plugin_name, context_id, setting_name, setting_value, setting_type) VALUES ({q(plugin)}, {J}, {q(name)}, {q(val)}, {q(typ)});")
out.append("REPLACE INTO plugin_settings (plugin_name, context_id, setting_name, setting_value, setting_type) VALUES ('nrhookfixtureplugin', 2, 'enabled', '1', 'bool');")

# Stored DOIs for two publications (the third DOI-bearing fixture is set by the import).
for pub, doi in [(1, "10.99999/nr.2026.0201"), (3, "10.99999/nr.2026.0203"), (5, "10.99999/nr.2026.0101")]:
    out.append(f"DELETE FROM publication_settings WHERE publication_id={pub} AND setting_name='pub-id::doi';")
    out.append(f"INSERT INTO publication_settings (publication_id, locale, setting_name, setting_value) VALUES ({pub}, '', 'pub-id::doi', {q(doi)});")

# --- Custom page in the primary menu ---------------------------------------------
POLICY_EN = """<p>[Test] Synthetic policy page. Not the policy of any real journal.</p>
<h2>Focus and Scope</h2><p>Placeholder scope paragraph.</p>
<ul><li>Coastal studies</li><li>Cultural heritage</li><li>Environmental change</li><li>Spatial planning</li><li>Maritime history</li><li>Community science</li></ul>
<h2>Peer Review Process</h2><p>Placeholder paragraph about the review workflow.</p><blockquote><p>Placeholder callout for a highlighted note.</p></blockquote>
<h2>Open Access Policy</h2><p>Placeholder paragraph.</p>
<h2>Archiving</h2><p>Placeholder paragraph.</p>
<table><thead><tr><th>Item</th><th>Value</th><th>Notes with a long cell to test horizontal overflow on narrow screens</th></tr></thead><tbody><tr><td>Example</td><td>Placeholder</td><td>Synthetic value only</td></tr></tbody></table>"""
POLICY_ID = POLICY_EN.replace("[Test] Synthetic policy page. Not the policy of any real journal.", "[Uji] Halaman kebijakan sintetis, bukan kebijakan jurnal nyata.").replace("Focus and Scope", "Fokus dan Ruang Lingkup").replace("Peer Review Process", "Proses Telaah Sejawat")
nid = 200
out.append(f"DELETE FROM navigation_menu_item_settings WHERE navigation_menu_item_id={nid};")
out.append(f"DELETE FROM navigation_menu_item_assignments WHERE navigation_menu_item_id={nid};")
out.append(f"REPLACE INTO navigation_menu_items (navigation_menu_item_id, context_id, path, type) VALUES ({nid}, {J}, 'policies', 'NMI_TYPE_CUSTOM');")
out.append(f"INSERT INTO navigation_menu_item_settings (navigation_menu_item_id, locale, setting_name, setting_value, setting_type) VALUES "
           f"({nid}, 'en_US', 'title', 'Policies', 'string'), ({nid}, 'en_US', 'content', {q(POLICY_EN)}, 'string'), "
           f"({nid}, 'id_ID', 'title', 'Kebijakan', 'string'), ({nid}, 'id_ID', 'content', {q(POLICY_ID)}, 'string');")
out.append(f"INSERT INTO navigation_menu_item_assignments (navigation_menu_id, navigation_menu_item_id, parent_id, seq) "
           f"SELECT navigation_menu_id, {nid}, 0, 9 FROM navigation_menus WHERE context_id={J} AND area_name='primary';")

# --- Static page (staticPages plugin) ---------------------------------------------
out.append("DELETE FROM static_page_settings WHERE static_page_id=1; DELETE FROM static_pages WHERE static_page_id=1;")
out.append(f"INSERT INTO static_pages (static_page_id, path, context_id) VALUES (1, 'about-test', {J});")
out.append("INSERT INTO static_page_settings (static_page_id, locale, setting_name, setting_value, setting_type) VALUES "
           "(1, 'en_US', 'title', '[Test] Static Page', 'string'), (1, 'en_US', 'content', '<p>[Test] Static Pages plugin content.</p>', 'string');")

# --- Announcements -----------------------------------------------------------------
out.append(f"DELETE FROM announcement_type_settings WHERE type_id=1; DELETE FROM announcement_types WHERE type_id=1;")
out.append(f"INSERT INTO announcement_types (type_id, assoc_type, assoc_id) VALUES (1, 256, {J});")
out.append("INSERT INTO announcement_type_settings (type_id, locale, setting_name, setting_value, setting_type) VALUES (1, 'en_US', 'name', 'Call for Papers', 'string'), (1, 'id_ID', 'name', 'Panggilan Naskah', 'string');")
anns = [(1, 1, "2026-10-10 09:00:00", "2027-12-31", "[Test] Call for Papers: Synthetic Special Issue", "[Uji] Panggilan Naskah: Edisi Khusus Sintetis"),
        (2, None, "2026-09-22 09:00:00", None, "[Test] New Synthetic Board Members", "[Uji] Anggota Dewan Sintetis Baru"),
        (3, None, "2026-08-15 09:00:00", None, "[Test] Third Announcement With a Considerably Longer Title to Check Wrapping in the List", "[Uji] Pengumuman Ketiga Dengan Judul Panjang")]
for aid, typ, posted, expire, title_en, title_id in anns:
    out.append(f"DELETE FROM announcement_settings WHERE announcement_id={aid}; DELETE FROM announcements WHERE announcement_id={aid};")
    out.append(f"INSERT INTO announcements (announcement_id, assoc_type, assoc_id, type_id, date_expire, date_posted) VALUES ({aid}, 256, {J}, {typ if typ else 'NULL'}, {q(expire) if expire else 'NULL'}, {q(posted)});")
    out.append(f"INSERT INTO announcement_settings (announcement_id, locale, setting_name, setting_value, setting_type) VALUES "
               f"({aid}, 'en_US', 'title', {q(title_en)}, 'string'), ({aid}, 'en_US', 'descriptionShort', '<p>[Test] Synthetic summary.</p>', 'string'), ({aid}, 'en_US', 'description', '<p>[Test] Synthetic body.</p>', 'string'), "
               f"({aid}, 'id_ID', 'title', {q(title_id)}, 'string'), ({aid}, 'id_ID', 'descriptionShort', '<p>[Uji] Ringkasan sintetis.</p>', 'string');")

# --- Custom Block Manager block -------------------------------------------------------
out.append(f"REPLACE INTO plugin_settings (plugin_name, context_id, setting_name, setting_value, setting_type) VALUES ('customblockmanagerplugin', {J}, 'enabled', '1', 'bool'), ('customblockmanagerplugin', {J}, 'blocks', 'a:1:{{i:0;s:10:\"nrfixture1\";}}', 'object');")
block = '<p>[Test] Custom Block Manager content (NR-BLOCK-MARKER).</p>'
ser = 'a:1:{s:5:"en_US";s:%d:"%s";}' % (len(block.encode()), block)
out.append(f"REPLACE INTO plugin_settings (plugin_name, context_id, setting_name, setting_value, setting_type) VALUES ('nrfixture1', {J}, 'enabled', '1', 'bool'), ('nrfixture1', {J}, 'context', '1', 'int'), ('nrfixture1', {J}, 'seq', '1', 'int'), ('nrfixture1', {J}, 'blockContent', {q(ser)}, 'object');")
out.append(f"DELETE FROM journal_settings WHERE journal_id={J} AND setting_name='sidebar';")
out.append(f"INSERT INTO journal_settings (journal_id, locale, setting_name, setting_value) VALUES ({J}, '', 'sidebar', '[\"nrfixture1\",\"informationblockplugin\",\"languagetoggleblockplugin\"]');")

# --- Synthetic editor decisions for the first article (dates only are shown) -----------
out.append("DELETE FROM edit_decisions WHERE submission_id=1;")
out.append("INSERT INTO edit_decisions (submission_id, review_round_id, stage_id, round, editor_id, decision, date_decided) VALUES "
           "(1, 0, 3, 1, 1, 2, '2026-03-02 10:00:00'), (1, 0, 3, 2, 1, 1, '2026-04-14 10:00:00');")

# --- Current issue ---------------------------------------------------------------------
out.append(f"UPDATE issues SET current = (issue_id = 1) WHERE journal_id = {J};")

# --- Deterministic usage metrics ----------------------------------------------------------
out.append("DELETE FROM metrics WHERE load_id LIKE 'nr_fixture_%';")
out.append(f"INSERT INTO metrics (load_id,context_id,submission_id,assoc_type,assoc_id,day,month,metric_type,metric) SELECT CONCAT('nr_fixture_view_',submission_id),{J},submission_id,1048585,submission_id,'20261101','202611','ojs::counter',submission_id*100+7 FROM submissions WHERE context_id={J} AND status=3;")
out.append(f"INSERT INTO metrics (load_id,context_id,submission_id,representation_id,assoc_type,assoc_id,day,month,file_type,metric_type,metric) SELECT CONCAT('nr_fixture_pdf_',sf.submission_file_id),{J},sf.submission_id,pg.galley_id,515,sf.submission_file_id,'20261101','202611',2,'ojs::counter',sf.submission_id*10+3 FROM submission_files sf JOIN publication_galleys pg ON pg.submission_file_id=sf.submission_file_id;")

sys.stdout.write("\n".join(out) + "\n")
