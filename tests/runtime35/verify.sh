#!/usr/bin/env bash
# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
#
# LOCAL TEST STACK ONLY. Server-side acceptance checks for NusantaraReborn on the OJS 3.5 stack:
# HTTP status and hook markers per page, OJS 3.5 article features, output escaping with hostile
# option values, multi-journal isolation. Temporary test values are restored at the end.
# Output: tests/reports/ojs35/verify.txt
set -uo pipefail
cd "$(dirname "$0")"
set -a; . ./.env; set +a
B=http://localhost:8100/index.php
OUT=../reports/ojs35/verify.txt; mkdir -p ../reports/ojs35; : > "$OUT"
log() { echo "$*" | tee -a "$OUT"; }
q() { docker compose exec -T db mariadb -uojs -p"$DB_PASSWORD" ojs -e "$1"; docker compose exec -T web find /var/www/html/cache -mindepth 1 -type f -delete; }
PS="REPLACE INTO plugin_settings (plugin_name, context_id, setting_name, setting_value, setting_type) VALUES"
: > .work/logs/php_errors.log

log "== Pages (HTTP, hook markers: each marker appears twice per rendering = attribute + text)"
q "$PS ('defaultthemeplugin', 1, 'displayStats', 'bar', 'string');" >/dev/null
for u in index issue/current issue/archive article/view/1 article/view/2 article/view/4 issue/view/1 about about/editorialMasthead about/editorialHistory announcement policies about-test search login; do
  H=$(curl -s -L "$B/nusantara/en/$u")
  log "$(printf '%-24s %s nr_theme=%s %s' "$u" "$(curl -s -L -o /dev/null -w '%{http_code}' "$B/nusantara/en/$u")" "$(echo "$H" | grep -c 'nr_theme')" "$(echo "$H" | grep -o 'NR-HOOK-[A-Z-]*' | sort | uniq -c | awk '{printf "%s:%s ",$2,$1}')")"
done
log "site index: $(curl -s -L -o /dev/null -w '%{http_code}' "$B/index/en")"

log "== Article (OJS 3.5 features)"
A=$(curl -s "$B/nusantara/en/article/view/1")
for k in 'class="nr_affiliations"' 'class="nr_ror"' 'nr_orcid_link' 'data-availability-statement' 'canvas class="usageStatsGraph"' 'class="nr_dates"' 'id="citationOutput"' 'nr_doi_line' 'application/ld+json' 'nr_related' 'NR-BLOCK-MARKER' 'UNPUBLISHED'; do
  log "$(printf '  %-34s %s' "$k" "$(echo "$A" | grep -c "$k")")"
done
log "  affiliation numbers: $(echo "$A" | grep -o '<sup>[^<]*</sup>' | sed 's/<[^>]*>//g' | tr '\n' ' ')"
log "  unpublished on homepage/archive: $(curl -s "$B/nusantara/en/index" | grep -c UNPUBLISHED)/$(curl -s "$B/nusantara/en/issue/archive" | grep -c UNPUBLISHED)"

log "== Locale id"
H=$(curl -s "$B/nusantara/id/index"); log "  $(echo "$H" | grep -o '<html lang="[^"]*"') $(echo "$H" | grep -o 'Kirim Naskah\|Informasi Jurnal\|Artikel Terbaru' | sort -u | tr '\n' ',')"

log "== Escaping and URL filtering (hostile option values)"
q "$PS ('nusantararebornthemeplugin',1,'publisherUrl','javascript:alert(1)','string'),('nusantararebornthemeplugin',1,'indexedIn','Good Index | https://example.test/x\nEvil <b>x</b> | javascript:alert(2)','string'),('nusantararebornthemeplugin',1,'editorInChiefName','Dr. Test <script>alert(3)</script>','string'),('nusantararebornthemeplugin',1,'editorInChiefPhoto','http://example.test/p.jpg','string');" >/dev/null
I=$(curl -s "$B/nusantara/en/index")
log "  javascript: urls=$(echo "$I" | grep -c 'javascript:')  raw <script>alert(3)=$(echo "$I" | grep -c '<script>alert(3)')  escaped=$(echo "$I" | grep -c '&lt;script&gt;alert(3)')  http photo=$(echo "$I" | grep -c 'example.test/p.jpg')"
q "DELETE FROM plugin_settings WHERE plugin_name='nusantararebornthemeplugin' AND context_id=1 AND setting_name IN ('publisherUrl','indexedIn','editorInChiefName','editorInChiefPhoto');" >/dev/null
q "CREATE TABLE nr_tmp_nm AS SELECT * FROM navigation_menu_item_settings WHERE navigation_menu_item_id=200 AND setting_name='content' AND locale='en';
UPDATE navigation_menu_item_settings SET setting_value=CONCAT(setting_value,'<p>XSS-B<script>alert(21)</script><iframe src=\"https://example.test\"></iframe><img src=x onerror=alert(22)></p>') WHERE navigation_menu_item_id=200 AND setting_name='content' AND locale='en';" >/dev/null
N=$(curl -s "$B/nusantara/en/policies")
log "  custom page: script=$(echo "$N" | grep -c 'alert(2[12])') iframe=$(echo "$N" | grep -c '<iframe') h2=$(echo "$N" | grep -c '<h2') table=$(echo "$N" | grep -c '<table')"
q "DELETE FROM navigation_menu_item_settings WHERE navigation_menu_item_id=200 AND setting_name='content' AND locale='en'; INSERT INTO navigation_menu_item_settings SELECT * FROM nr_tmp_nm; DROP TABLE nr_tmp_nm;" >/dev/null

log "== Multi-journal isolation (journal 2: primary locale id, preset batikSogan)"
q "UPDATE journal_settings SET setting_value='nusantaraReborn' WHERE journal_id=2 AND setting_name='themePluginPath'; $PS ('nusantararebornthemeplugin',2,'enabled','1','bool'),('nusantararebornthemeplugin',2,'colourScheme','batikSogan','string');" >/dev/null
K=$(curl -s "$B/kosong/id/index")
log "  kosong: $(echo "$K" | grep -o 'nr_scheme_[a-zA-Z]*' | head -1) $(echo "$K" | grep -o '<html lang="[^"]*"') leak-from-journal-1=$(echo "$K" | grep -c 'Synthetic Publishing\|Coastal Futures')  journal-1: $(curl -s "$B/nusantara/en/index" | grep -o 'nr_scheme_[a-zA-Z]*' | head -1)"
log "  kosong archive/masthead: $(curl -s -o /dev/null -w '%{http_code}' "$B/kosong/id/issue/archive")/$(curl -s -o /dev/null -w '%{http_code}' "$B/kosong/id/about/editorialMasthead")"

log "== PHP log (theme only; core pflPlugin TypeError and indexation notices excluded)"
log "$(grep -v 'Deprecated\|Skipped indexation\|pflPlugin\|PflPlugin\|IPGeoDB' .work/logs/php_errors.log | grep -v '^#\|^Stack\|^  thrown' | sed 's/^\[[^]]*\] //' | sort | uniq -c | head -10)"
log "== core pflPlugin errors (not theme): $(grep -c 'PflPlugin failed to handle' .work/logs/php_errors.log)"
