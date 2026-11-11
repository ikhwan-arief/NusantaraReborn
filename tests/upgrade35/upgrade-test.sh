#!/usr/bin/env bash
# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
#
# LOCAL TEST ONLY. Upgrade fixture NusantaraReborn 1.x (OJS 3.3.0-24) -> 2.0.0.0 (OJS 3.5.0-6):
#  1. copy the 3.3 test stack database and files (read-only use of the 3.3 stack),
#  2. on the copy, set known multilingual theme options (en_US / id_ID) and record them,
#  3. replace the theme folder with the 3.5 package, run the official OJS upgrade tool,
#  4. record the theme options again for comparison.
set -euo pipefail
HERE="$(cd "$(dirname "$0")" && pwd)"; ROOT="$(cd "$HERE/../.." && pwd)"; cd "$HERE"
R33="$ROOT/tests/runtime33"
gen() { LC_ALL=C tr -dc 'A-Za-z0-9' </dev/urandom | head -c "$1"; }
[ -f .env ] || { printf 'DB_PASSWORD=%s\nDB_ROOT_PASSWORD=%s\n' "$(gen 24)" "$(gen 24)" > .env; chmod 600 .env; }
set -a; . ./.env; set +a
DB33="$(sed -n 's/^DB_PASSWORD=//p' "$R33/.env")"

docker compose down -v --remove-orphans >/dev/null 2>&1 || true
rm -rf .work && mkdir -p .work/files .work/logs
echo "== copy 3.3 database and files"
(cd "$R33" && docker compose exec -T db mariadb-dump --single-transaction -uojs -p"$DB33" ojs) > .work/ojs33.sql
cp -R "$R33/.work/files/." .work/files/
tar -xzf "$ROOT/ojs-3.5.0-6.tar.gz" -C .work && mv .work/ojs-3.5.0-6 .work/ojs
cp -R "$R33/.work/ojs/public/." .work/ojs/public/
# Merge configuration as the upgrade guide requires: start from the 3.5 template and carry the
# installation values over from the 3.3 configuration (salt and API secret keep sessions/keys valid).
old() { sed -n "s/^$1 = //p" "$R33/.work/ojs/config.inc.php" | head -1; }
sed -e "s#^installed = Off#installed = On#" \
    -e "s#^base_url = .*#base_url = $(old base_url)#" \
    -e "s#^host = localhost#host = db#" \
    -e "s#^password = ojs#password = $DB_PASSWORD#" \
    -e "s#^files_dir = files#files_dir = /var/www/files#" \
    -e "s#^salt = .*#salt = $(old salt)#" \
    -e "s#^api_key_secret = .*#api_key_secret = $(old api_key_secret)#" \
    -e "s#^default = sendmail#default = log#" \
    .work/ojs/config.TEMPLATE.inc.php > .work/ojs/config.inc.php
docker compose up -d --build >/dev/null 2>&1
for i in $(seq 1 60); do docker compose exec -T db mariadb -uojs -p"$DB_PASSWORD" -e 'select 1' ojs >/dev/null 2>&1 && break; sleep 2; done
docker compose exec -T db mariadb -uojs -p"$DB_PASSWORD" ojs < .work/ojs33.sql

echo "== known options on the copy (OJS 3.3 format)"
docker compose exec -T db mariadb -uojs -p"$DB_PASSWORD" ojs -e "
REPLACE INTO plugin_settings (plugin_name, context_id, setting_name, setting_value, setting_type) VALUES
('nusantararebornthemeplugin', 1, 'journalTagline', '{\"en_US\":\"UPG-TAGLINE-EN\",\"id_ID\":\"UPG-TAGLINE-ID\"}', 'object'),
('nusantararebornthemeplugin', 1, 'colourScheme', 'tenunSenja', 'string');"
docker compose exec -T db mariadb -uojs -p"$DB_PASSWORD" ojs -e "SELECT setting_name, setting_value FROM plugin_settings WHERE plugin_name='nusantararebornthemeplugin' AND context_id=1 AND setting_name IN ('journalTagline','colourScheme','enabled')" > .work/logs/options-before.txt

echo "== replace theme with the 3.5 package and upgrade OJS"
rm -rf .work/ojs/plugins/themes/nusantaraReborn
tar -xzf "$ROOT/dist/ojs35/nusantaraReborn-2.0.0.0.tar.gz" -C .work/ojs/plugins/themes/
docker compose exec -T web chown -R www-data:www-data /var/www/html /var/www/files
docker compose exec -T -u www-data web php lib/pkp/tools/appKey.php generate >/dev/null
docker compose exec -T -u www-data web php tools/upgrade.php check | tail -5
docker compose exec -T -u www-data web php tools/upgrade.php upgrade > .work/logs/upgrade.log 2>&1 || { tail -30 .work/logs/upgrade.log; exit 1; }
tail -3 .work/logs/upgrade.log
# The pre-flight check needs the 3.3 base_url (usage-stats logs); switch to this stack afterwards.
sed -i.bak "s#^base_url = .*#base_url = \"http://localhost:8101\"#" .work/ojs/config.inc.php && rm -f .work/ojs/config.inc.php.bak
docker compose exec -T web find /var/www/html/cache -mindepth 1 -type f -delete
docker compose exec -T db mariadb -uojs -p"$DB_PASSWORD" ojs -e "SELECT setting_name, setting_value FROM plugin_settings WHERE plugin_name='nusantararebornthemeplugin' AND context_id=1 AND setting_name IN ('journalTagline','colourScheme','enabled')" > .work/logs/options-after.txt
echo "== before"; cat .work/logs/options-before.txt; echo "== after"; cat .work/logs/options-after.txt
