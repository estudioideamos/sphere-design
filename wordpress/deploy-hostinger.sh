#!/usr/bin/env bash
set -euo pipefail
revision="$1"
[[ "$revision" =~ ^[a-f0-9]{40}$ ]] || exit 2
root=/home/u256301447/domains/studiospheredesign.com/public_html
release="/home/u256301447/sphere-releases/$revision"
[[ -f "$root/wp-config.php" && -f "$release/release.tar.gz" ]] || exit 3
mkdir -p "$release/unpacked"
tar -xzf "$release/release.tar.gz" -C "$release/unpacked"
find "$release/unpacked" -name '*.php' -print0 | xargs -0 -n1 php -l
cd "$root"
tar -czf "$release/before-code.tar.gz" wp-content/themes/sphere wp-content/mu-plugins
rollback() {
  tar -xzf "$release/before-code.tar.gz" -C "$root"
  wp litespeed-purge all || true
}
trap rollback ERR
# Deliberately no --delete: uploads, database, seed data, and existing media are never replaced.
rsync -a "$release/unpacked/theme/" "$root/wp-content/themes/sphere/"
rsync -a "$release/unpacked/mu-plugins/" "$root/wp-content/mu-plugins/"
wp eval 'if (!function_exists("sphere_markup") || !function_exists("sphere_site_info")) { throw new Exception("Sphere modules missing"); }'
wp option update sphere_deploy_revision "$revision"
wp litespeed-purge all
trap - ERR
printf 'Deployed %s\n' "$revision"
