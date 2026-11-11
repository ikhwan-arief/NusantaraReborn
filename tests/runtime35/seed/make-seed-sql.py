#!/usr/bin/env python3
# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
"""SYNTHETIC TEST FIXTURE SQL for the NusantaraReborn OJS 3.5.0-6 stack.

Journal 1 (path "nusantara", en + id): bilingual settings, custom "Policies" page in the primary
menu, static page, announcements, highlights, a Custom Block Manager block, bundled plugins, the
hook-fixture plugin, a data availability statement, synthetic editor decisions (OJS 3.5 decision
constants), masthead users, and usage metrics for the statistics chart.
Journal 2 (path "kosong", primary locale id) stays deliberately empty.
"""
import sys


def q(v):
    return "'" + str(v).replace("\\", "\\\\").replace("'", "''") + "'"


J = 1
out = ["SET NAMES utf8mb4;"]

# --- Journal settings (bilingual) ------------------------------------------------
settings = [
    ("description", "en", "<p>[Test] The Synthetic Journal of Nusantara Studies is an invented journal used only to test the NusantaraReborn theme. It publishes placeholder research on coastal, cultural and environmental topics.</p>"),
    ("description", "id", "<p>[Uji] Jurnal Sintetis Kajian Nusantara adalah jurnal rekaan yang hanya dipakai untuk menguji theme NusantaraReborn. Isinya penelitian contoh tentang pesisir, budaya, dan lingkungan.</p>"),
    ("searchDescription", "en", "[Test] Invented journal for theme testing: coastal, cultural and environmental studies."),
    ("searchDescription", "id", "[Uji] Jurnal rekaan untuk pengujian theme: kajian pesisir, budaya, dan lingkungan."),
    ("printIssn", "", "0000-0019"),
    ("onlineIssn", "", "0000-0027"),
    ("publisherInstitution", "", "Synthetic Publishing Cooperative (test data)"),
    ("mailingAddress", "", "Jl. Contoh No. 1\nKota Sintetis 00000\nIndonesia (test address)"),
    ("contactPhone", "", "+62 000 0000 000"),
    ("numAnnouncementsHomepage", "", "3"),
    ("enableAnnouncements", "", "1"),
    ("additionalHomeContent", "en", "<p>[Test] Additional homepage content entered in Settings &gt; Website.</p>"),
]
for name, locale, value in settings:
    out.append(f"DELETE FROM journal_settings WHERE journal_id={J} AND setting_name={q(name)} AND locale={q(locale)};")
    out.append(f"INSERT INTO journal_settings (journal_id, locale, setting_name, setting_value) VALUES ({J}, {q(locale)}, {q(name)}, {q(value)});")
out.append(f"UPDATE journal_settings SET setting_value='1' WHERE journal_id={J} AND setting_name='publishingMode';")

# --- Plugins ----------------------------------------------------------------------------
for plugin in ["citationstylelanguageplugin", "googlescholarplugin", "dublincoremetaplugin", "pdfjsviewerplugin",
               "htmlarticlegalleyplugin", "staticpagesplugin", "informationblockplugin", "languagetoggleblockplugin",
               "creditplugin", "pflplugin", "nrhookfixtureplugin"]:
    out.append(f"REPLACE INTO plugin_settings (plugin_name, context_id, setting_name, setting_value, setting_type) VALUES ({q(plugin)}, {J}, 'enabled', '1', 'bool');")

# --- Data availability on the first publication ---------------------------------------------
out.append("DELETE FROM publication_settings WHERE publication_id=1 AND setting_name='dataAvailability';")
out.append("INSERT INTO publication_settings (publication_id, locale, setting_name, setting_value) VALUES "
           "(1, 'en', 'dataAvailability', '<p>[Test] Synthetic data availability statement: the invented dataset is available on request.</p>'), "
           "(1, 'id', 'dataAvailability', '<p>[Uji] Pernyataan ketersediaan data sintetis.</p>');")

# --- Custom page in the primary menu --------------------------------------------------------
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
           f"({nid}, 'en', 'title', 'Policies', 'string'), ({nid}, 'en', 'content', {q(POLICY_EN)}, 'string'), "
           f"({nid}, 'id', 'title', 'Kebijakan', 'string'), ({nid}, 'id', 'content', {q(POLICY_ID)}, 'string');")
out.append(f"INSERT INTO navigation_menu_item_assignments (navigation_menu_id, navigation_menu_item_id, parent_id, seq) "
           f"SELECT navigation_menu_id, {nid}, NULL, 9 FROM navigation_menus WHERE context_id={J} AND area_name='primary';")

# --- Static page -----------------------------------------------------------------------------
out.append("DELETE FROM static_page_settings WHERE static_page_id=1; DELETE FROM static_pages WHERE static_page_id=1;")
out.append(f"INSERT INTO static_pages (static_page_id, path, context_id) VALUES (1, 'about-test', {J});")
out.append("INSERT INTO static_page_settings (static_page_id, locale, setting_name, setting_value, setting_type) VALUES "
           "(1, 'en', 'title', '[Test] Static Page', 'string'), (1, 'en', 'content', '<p>[Test] Static Pages plugin content.</p>', 'string');")

# --- Announcements (OJS 3.5: announcement_types.context_id) -------------------------------------
out.append("DELETE FROM announcement_type_settings WHERE type_id=1; DELETE FROM announcement_types WHERE type_id=1;")
out.append(f"INSERT INTO announcement_types (type_id, context_id) VALUES (1, {J});")
out.append("INSERT INTO announcement_type_settings (type_id, locale, setting_name, setting_value, setting_type) VALUES (1, 'en', 'name', 'Call for Papers', 'string'), (1, 'id', 'name', 'Panggilan Naskah', 'string');")
anns = [(1, 1, "2026-10-10 09:00:00", "2027-12-31", "[Test] Call for Papers: Synthetic Special Issue", "[Uji] Panggilan Naskah: Edisi Khusus Sintetis"),
        (2, None, "2026-09-22 09:00:00", None, "[Test] New Synthetic Board Members", "[Uji] Anggota Dewan Sintetis Baru"),
        (3, None, "2026-08-15 09:00:00", None, "[Test] Third Announcement With a Considerably Longer Title to Check Wrapping in the List", "[Uji] Pengumuman Ketiga Dengan Judul Panjang")]
for aid, typ, posted, expire, title_en, title_id in anns:
    out.append(f"DELETE FROM announcement_settings WHERE announcement_id={aid}; DELETE FROM announcements WHERE announcement_id={aid};")
    out.append(f"INSERT INTO announcements (announcement_id, assoc_type, assoc_id, type_id, date_expire, date_posted) VALUES ({aid}, 256, {J}, {typ if typ else 'NULL'}, {q(expire) if expire else 'NULL'}, {q(posted)});")
    out.append(f"INSERT INTO announcement_settings (announcement_id, locale, setting_name, setting_value) VALUES "
               f"({aid}, 'en', 'title', {q(title_en)}), ({aid}, 'en', 'descriptionShort', '<p>[Test] Synthetic summary.</p>'), ({aid}, 'en', 'description', '<p>[Test] Synthetic body.</p>'), "
               f"({aid}, 'id', 'title', {q(title_id)}), ({aid}, 'id', 'descriptionShort', '<p>[Uji] Ringkasan sintetis.</p>');")

# --- Highlights (OJS 3.5) ---------------------------------------------------------------------
out.append(f"DELETE FROM highlight_settings WHERE highlight_id IN (1,2); DELETE FROM highlights WHERE highlight_id IN (1,2);")
out.append(f"INSERT INTO highlights (highlight_id, context_id, sequence, url) VALUES (1, {J}, 1, 'https://example.test/highlight-one'), (2, {J}, 2, 'https://example.test/highlight-two');")
out.append("INSERT INTO highlight_settings (highlight_id, locale, setting_name, setting_value) VALUES "
           "(1, 'en', 'title', '[Test] Highlight One'), (1, 'en', 'description', '<p>[Test] Synthetic highlight description.</p>'), (1, 'en', 'urlText', 'Read more'), "
           "(2, 'en', 'title', '[Test] Highlight Two'), (2, 'en', 'description', '<p>[Test] Second synthetic highlight.</p>'), (2, 'en', 'urlText', 'Details');")

# --- Custom Block Manager block -------------------------------------------------------------------
out.append(f"REPLACE INTO plugin_settings (plugin_name, context_id, setting_name, setting_value, setting_type) VALUES ('customblockmanagerplugin', {J}, 'enabled', '1', 'bool'), ('customblockmanagerplugin', {J}, 'blocks', '[\"nrfixture1\"]', 'object');")
out.append(f"REPLACE INTO plugin_settings (plugin_name, context_id, setting_name, setting_value, setting_type) VALUES ('nrfixture1', {J}, 'enabled', '1', 'bool'), ('nrfixture1', {J}, 'context', '1', 'int'), ('nrfixture1', {J}, 'seq', '1', 'int'), ('nrfixture1', {J}, 'blockContent', '{{\"en\":\"<p>[Test] Custom Block Manager content (NR-BLOCK-MARKER).<\\\\/p>\"}}', 'object');")
out.append(f"DELETE FROM journal_settings WHERE journal_id={J} AND setting_name='sidebar';")
out.append(f"INSERT INTO journal_settings (journal_id, locale, setting_name, setting_value) VALUES ({J}, '', 'sidebar', '[\"nrfixture1\",\"informationblockplugin\",\"languagetoggleblockplugin\"]');")

# --- Synthetic editor decisions for submission 1 (OJS 3.5 constants: PENDING_REVISIONS=4, ACCEPT=2) --
out.append("DELETE FROM edit_decisions WHERE submission_id=1;")
out.append("INSERT INTO edit_decisions (submission_id, review_round_id, stage_id, round, editor_id, decision, date_decided) VALUES "
           "(1, NULL, 3, 1, 1, 4, '2026-03-02 10:00:00'), (1, NULL, 3, 2, 1, 2, '2026-04-14 10:00:00');")

# --- Masthead: two synthetic editors in the editor role --------------------------------------------
for uid, uname, given, family, aff in [(901, "nr_editor_one", "Ayu", "Kartika Sari", "Synthetic State University"),
                                       (902, "nr_editor_two", "Jean-Luc", "Ñúñez", "Example Institute (synthetic)")]:
    out.append(f"DELETE FROM user_user_groups WHERE user_id={uid}; DELETE FROM user_settings WHERE user_id={uid}; DELETE FROM users WHERE user_id={uid};")
    out.append(f"INSERT INTO users (user_id, username, password, email, country, locales, date_registered, disabled) VALUES "
               f"({uid}, {q(uname)}, '!disabled-synthetic', {q(uname + '@example.test')}, 'ID', '[]', '2025-01-01 00:00:00', 0);")
    out.append(f"INSERT INTO user_settings (user_id, locale, setting_name, setting_value) VALUES "
               f"({uid}, 'en', 'givenName', {q(given)}), ({uid}, 'en', 'familyName', {q(family)}), ({uid}, 'en', 'affiliation', {q(aff)});")
    out.append(f"INSERT INTO user_user_groups (user_group_id, user_id, date_start, masthead) "
               f"SELECT user_group_id, {uid}, '2025-01-01', 1 FROM user_groups WHERE context_id={J} AND role_id=17 AND masthead=1 ORDER BY user_group_id LIMIT 1;")

# --- Current issue -------------------------------------------------------------------------------
out.append(f"UPDATE journals SET current_issue_id = 1 WHERE journal_id = {J};")

# --- Usage metrics (OJS 3.4+ tables) -------------------------------------------------------------
out.append("DELETE FROM metrics_submission WHERE load_id LIKE 'nr_fixture_%';")
for month in ["2026-08-15", "2026-09-15", "2026-10-15"]:
    out.append(f"INSERT INTO metrics_submission (load_id, context_id, submission_id, representation_id, submission_file_id, file_type, assoc_type, date, metric) "
               f"SELECT 'nr_fixture_view', {J}, submission_id, NULL, NULL, NULL, 1048585, '{month}', submission_id*30+7 FROM submissions WHERE context_id={J} AND status=3;")
    out.append(f"INSERT INTO metrics_submission (load_id, context_id, submission_id, representation_id, submission_file_id, file_type, assoc_type, date, metric) "
               f"SELECT 'nr_fixture_pdf', {J}, sf.submission_id, pg.galley_id, sf.submission_file_id, 2, 515, '{month}', sf.submission_id*10+3 "
               f"FROM submission_files sf JOIN publication_galleys pg ON pg.submission_file_id=sf.submission_file_id JOIN submissions s ON s.submission_id=sf.submission_id WHERE s.context_id={J} AND s.status=3;")

sys.stdout.write("\n".join(out) + "\n")
