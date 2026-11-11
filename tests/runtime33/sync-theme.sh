#!/usr/bin/env bash
# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
# LOCAL TEST ONLY: replace the installed theme with build/ojs33 and clear compiled templates.
set -euo pipefail
cd "$(dirname "$0")"
rm -rf .work/ojs/plugins/themes/nusantaraReborn
cp -R ../../build/ojs33/nusantaraReborn .work/ojs/plugins/themes/nusantaraReborn
docker compose exec -T web sh -c 'rm -rf /var/www/html/cache/t_compile/* /var/www/html/cache/fc-*; chown -R www-data:www-data /var/www/html/plugins/themes/nusantaraReborn'
