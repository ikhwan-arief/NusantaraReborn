#!/usr/bin/env bash
# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
#
# Build an installable NusantaraReborn package from an explicit allowlist.
#   scripts/build.sh 33     -> build/ojs33/nusantaraReborn (staging) + dist/ojs33/nusantaraReborn-<version>.tar.gz
#   scripts/build.sh 35     -> build/ojs35/nusantaraReborn (staging) + dist/ojs35/nusantaraReborn-<version>.tar.gz
# The staging folder is also what the local Docker test stack mounts.
# No dependency installation, no network access.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
TRACK="${1:-33}"
case "$TRACK" in
  33) PLUGIN_PHP="NusantaraRebornThemePlugin.inc.php" ;;
  35) PLUGIN_PHP="NusantaraRebornThemePlugin.php" ;;
  *) echo "Unknown track: $TRACK (use 33 or 35)" >&2; exit 2 ;;
esac
SRC="$ROOT/ojs$TRACK/nusantaraReborn"
STAGE="$ROOT/build/ojs$TRACK/nusantaraReborn"
VERSION="$(sed -n 's:.*<release>\(.*\)</release>.*:\1:p' "$SRC/version.xml")"
[ -n "$VERSION" ] || { echo "No <release> in version.xml" >&2; exit 1; }

python3 -I "$ROOT/scripts/check-contrast.py" >/dev/null || { echo "Contrast check failed" >&2; exit 1; }
python3 -I "$ROOT/scripts/make-presets-css.py" >/dev/null
python3 -I "$ROOT/scripts/make-locales.py" "$TRACK" >/dev/null

rm -rf "$STAGE"
mkdir -p "$STAGE"/{styles,js,assets/heroes,assets/covers,assets/ornaments}

# Version-specific runtime files.
cp "$SRC/index.php" "$SRC/$PLUGIN_PHP" "$SRC/version.xml" "$STAGE/"
cp -R "$SRC/templates" "$SRC/locale" "$STAGE/"
cp "$SRC/README.md" "$SRC/CHANGELOG.md" "$STAGE/"

# Shared, version-neutral files.
cp "$ROOT/shared/styles/nusantara.css" "$ROOT/shared/styles/presets.css" "$STAGE/styles/"
cp "$ROOT/shared/js/nusantara.js" "$STAGE/js/"
cp "$ROOT"/shared/assets/heroes/*.webp "$STAGE/assets/heroes/"
cp "$ROOT"/shared/assets/covers/*.svg "$STAGE/assets/covers/"
cp "$ROOT"/shared/assets/ornaments/*.svg "$STAGE/assets/ornaments/"
cp "$ROOT/shared/assets/ASSET-MANIFEST.txt" "$STAGE/assets/"
mkdir -p "$STAGE/docs" && cp "$ROOT/docs/PANDUAN-PENGATURAN.md" "$STAGE/docs/"
cp "$ROOT/shared/package/LICENSE" "$ROOT/shared/package/LICENSE-GPL-3.0.txt" "$ROOT/shared/package/NOTICE.md" "$STAGE/"

# Guard: nothing hidden, no legacy identity, no symlinks.
if find "$STAGE" -name '.*' -o -type l | grep -q .; then echo "Hidden file or symlink in staging" >&2; exit 1; fi
if grep -rIl -i -E 'universitas andalas|unandJournal|ujt_|unand-logo|unand-crest' "$STAGE" | grep -q .; then
  grep -rIn -i -E 'universitas andalas|unandJournal|ujt_|unand-logo|unand-crest' "$STAGE" >&2; echo "Legacy identity found in staging" >&2; exit 1
fi

OUT="$ROOT/dist/ojs$TRACK/nusantaraReborn-$VERSION.tar.gz"
mkdir -p "$(dirname "$OUT")"
rm -f "$OUT"
# One root folder, normalised owner, no macOS metadata.
COPYFILE_DISABLE=1 tar --uid 0 --gid 0 --uname root --gname root --no-xattrs -czf "$OUT" -C "$ROOT/build/ojs$TRACK" nusantaraReborn 2>/dev/null \
  || COPYFILE_DISABLE=1 tar -czf "$OUT" -C "$ROOT/build/ojs$TRACK" nusantaraReborn
(cd "$ROOT/dist" && find . -name '*.tar.gz' | sort | sed 's|^\./||' | xargs shasum -a 256 > SHA256SUMS)
echo "built $OUT"
cat "$ROOT/dist/SHA256SUMS"
