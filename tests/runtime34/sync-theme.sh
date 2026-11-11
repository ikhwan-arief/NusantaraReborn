#!/usr/bin/env bash
# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
# LOCAL TEST ONLY: replace the installed theme with build/ojs34 and clear compiled templates.
set -euo pipefail
cd "$(dirname "$0")"
rm -rf .work/ojs/plugins/themes/nusantaraReborn
cp -R ../../build/ojs34/nusantaraReborn .work/ojs/plugins/themes/nusantaraReborn
# OJS 3.4 keeps data caches in subfolders (_db, t_cache, ...): clear every cached file.
docker compose exec -T web find /var/www/html/cache -mindepth 1 -type f -delete
docker compose exec -T web chown -R www-data:www-data /var/www/html/plugins/themes/nusantaraReborn
