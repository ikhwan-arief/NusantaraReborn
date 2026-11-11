#!/usr/bin/env bash
# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
#
# LOCAL TEST STACK ONLY. Builds a clean OJS 3.4.0-12 on http://localhost:8102 from the local
# archive, runs the web installer (en + id), creates two synthetic journals and imports
# synthetic fixtures. The theme is NOT installed here (tests/e2e installs the built tarball).
#   ./setup.sh            refuse if an instance exists
#   ./setup.sh --reset    destroy this stack's database/files and reinstall
set -euo pipefail
HERE="$(cd "$(dirname "$0")" && pwd)"
ROOT="$(cd "$HERE/../.." && pwd)"
cd "$HERE"
BASE="http://localhost:8102"
TARBALL="$ROOT/ojs-3.4.0-12.tar.gz"
EXPECTED="830ce886745dbda7cb711407f826eeb885d8d1137b0383bf4d180c1086ba02a0"
[ "$(shasum -a 256 "$TARBALL" | cut -d' ' -f1)" = "$EXPECTED" ] || { echo "OJS archive hash mismatch" >&2; exit 1; }

if [ "${1:-}" = "--reset" ]; then
  docker compose down -v --remove-orphans >/dev/null 2>&1 || true
  rm -rf .work/ojs .work/files
elif [ -f .work/ojs/config.inc.php ] && grep -q 'installed = On' .work/ojs/config.inc.php; then
  echo "Instance exists. Use ./setup.sh --reset" >&2; exit 1
fi

gen() { LC_ALL=C tr -dc 'A-Za-z0-9' </dev/urandom | head -c "$1"; }
[ -f .env ] || { printf 'DB_PASSWORD=%s\nDB_ROOT_PASSWORD=%s\n' "$(gen 24)" "$(gen 24)" > .env; chmod 600 .env; }
[ -f credentials.txt ] || { printf 'admin %s\n' "$(gen 16)" > credentials.txt; chmod 600 credentials.txt; }
set -a; . ./.env; set +a
ADMIN_PASS="$(awk '$1=="admin"{print $2}' credentials.txt)"

mkdir -p .work/files .work/logs
rm -rf .work/ojs .work/ojs-3.4.0-12
tar -xzf "$TARBALL" -C .work && mv .work/ojs-3.4.0-12 .work/ojs
cp .work/ojs/config.TEMPLATE.inc.php .work/ojs/config.inc.php
cp -R "$ROOT/tests/fixtures/ojs34/nrHookFixture" .work/ojs/plugins/generic/nrHookFixture
cp seed/nr-import.php .work/ojs/tools/nrImport.php
: > .work/logs/php_errors.log

docker compose up -d --build
docker compose exec -T web chown -R www-data:www-data /var/www/html /var/www/files
for i in $(seq 1 60); do docker compose exec -T db mariadb -uojs -p"$DB_PASSWORD" -e 'select 1' ojs >/dev/null 2>&1 && break; sleep 2; done
until curl -s -o /dev/null "$BASE/index.php"; do sleep 1; done

echo "== web installer"
JAR="$(mktemp)"; trap 'rm -f "$JAR"' EXIT
curl -s -c "$JAR" -b "$JAR" "$BASE/index.php/index/install" -o /dev/null
curl -s -c "$JAR" -b "$JAR" -o .work/logs/install-response.html \
  --data-urlencode "installing=0" --data-urlencode "adminUsername=admin" \
  --data-urlencode "adminPassword=$ADMIN_PASS" --data-urlencode "adminPassword2=$ADMIN_PASS" \
  --data-urlencode "adminEmail=admin@example.test" --data-urlencode "locale=en" \
  --data-urlencode "additionalLocales[]=id" --data-urlencode "timeZone=Asia/Jakarta" \
  --data-urlencode "filesDir=/var/www/files" \
  --data-urlencode "databaseDriver=mysqli" --data-urlencode "databaseHost=db" \
  --data-urlencode "databaseUsername=ojs" --data-urlencode "databasePassword=$DB_PASSWORD" \
  --data-urlencode "databaseName=ojs" --data-urlencode "oaiRepositoryId=nr34.localhost" \
  --data-urlencode "enableBeacon=0" "$BASE/index.php/index/install/install"
grep -q 'installed = On' .work/ojs/config.inc.php || { echo "Install failed: .work/logs/install-response.html" >&2; exit 1; }
sed -i.bak "s#^base_url = .*#base_url = \"$BASE\"#" .work/ojs/config.inc.php && rm -f .work/ojs/config.inc.php.bak

echo "== journals"
TOKEN=$(curl -s -c "$JAR" -b "$JAR" "$BASE/index.php/index/login" | grep -o 'name="csrfToken" value="[^"]*"' | sed 's/.*value="//; s/"$//' || true)
curl -s -c "$JAR" -b "$JAR" -o /dev/null --data-urlencode "csrfToken=$TOKEN" --data-urlencode "username=admin" \
  --data-urlencode "password=$ADMIN_PASS" "$BASE/index.php/index/login/signIn"
CSRF=$(curl -s -c "$JAR" -b "$JAR" "$BASE/index.php/index/admin/contexts" | grep -o '"csrfToken":"[^"]*"' | head -1 | sed 's/.*:"//; s/"$//' || true)
api() { curl -s -b "$JAR" -X POST -H "X-Csrf-Token: $CSRF" -H 'Content-Type: application/json' --data "$1" "$BASE/index.php/index/api/v1/contexts"; }
# The first sign-in after a fresh install can fail its CSRF check; sign in once more if needed.
if [ -z "$CSRF" ]; then
  TOKEN=$(curl -s -c "$JAR" -b "$JAR" "$BASE/index.php/index/login" | grep -o 'name="csrfToken" value="[^"]*"' | sed 's/.*value="//; s/"$//' || true)
  curl -s -c "$JAR" -b "$JAR" -o /dev/null --data-urlencode "csrfToken=$TOKEN" --data-urlencode "username=admin" --data-urlencode "password=$ADMIN_PASS" "$BASE/index.php/index/login/signIn"
  CSRF=$(curl -s -c "$JAR" -b "$JAR" "$BASE/index.php/index/admin/contexts" | grep -o '"csrfToken":"[^"]*"' | head -1 | sed 's/.*:"//; s/"$//' || true)
fi
api '{"urlPath":"nusantara","enabled":true,"primaryLocale":"en","supportedLocales":["en","id"],"supportedFormLocales":["en","id"],"supportedSubmissionLocales":["en","id"],"name":{"en":"Synthetic Journal of Nusantara Studies","id":"Jurnal Sintetis Kajian Nusantara"},"acronym":{"en":"SJNS","id":"JSKN"},"contactName":"Editorial Office (synthetic)","contactEmail":"editor@example.test","country":"ID"}' > .work/logs/journal1.json; grep -q '"id":1' .work/logs/journal1.json || { echo "journal 1 failed:"; head -c 400 .work/logs/journal1.json; exit 1; }
api '{"urlPath":"kosong","enabled":true,"primaryLocale":"id","supportedLocales":["id","en"],"supportedFormLocales":["id","en"],"supportedSubmissionLocales":["id"],"name":{"id":"Jurnal Uji Kosong","en":"Empty Test Journal"},"acronym":{"id":"JUK","en":"ETJ"},"contactName":"Empty (synthetic)","contactEmail":"empty@example.test","country":"ID"}' > .work/logs/journal2.json; grep -q '"id":2' .work/logs/journal2.json || { echo "journal 2 failed:"; head -c 400 .work/logs/journal2.json; exit 1; }

echo "== fixture plugin, issues, queued submission"
docker compose exec -T web php lib/pkp/tools/installPluginVersion.php plugins/generic/nrHookFixture/version.xml
python3 -I seed/make-native-xml.py > .work/files/fixture-issues.xml
python3 -I seed/make-native-xml.py queued > .work/files/fixture-queued.xml
docker compose exec -T -u www-data web php tools/nrImport.php /var/www/files/fixture-issues.xml nusantara admin
docker compose exec -T -u www-data web php tools/nrImport.php /var/www/files/fixture-queued.xml nusantara admin
python3 -I seed/make-seed-sql.py | docker compose exec -T db mariadb -uojs -p"$DB_PASSWORD" ojs
docker compose exec -T web find /var/www/html/cache -mindepth 1 -type f -delete
docker compose exec -T web chown -R www-data:www-data /var/www/html /var/www/files
echo "== done: $BASE/index.php/nusantara (admin password in credentials.txt)"
