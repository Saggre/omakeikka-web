#!/usr/bin/env bash
#
# Replace the local Docker database with a copy of production.
#
# On the server `wp db export` streams a compressed dump over SSH; nothing is
# written there. The dump is downloaded in full before the local database is
# dropped and recreated, so anything local-only is lost.
#
# The copy holds real personal data (help requests, form entries). wp-mail-smtp
# is deactivated so that production's SMTP credentials never send mail from
# here; without it wp_mail fails locally and WP Mail Catcher logs the attempt.
#
# Usage: bin/updatedb.sh

set -euo pipefail

cd "$(dirname "$0")/.."

REMOTE_HOST="omakeikka-web"
REMOTE_PATH="/home/omakeikkafi/sites/omakeikka-production/current"
REMOTE_URL="https://omakeikka.fi"
LOCAL_URL="$(sed -n "s/^WP_HOME=['\"]\{0,1\}\([^'\"]*\).*/\1/p" .env)"
LOCAL_ADMIN_EMAIL="admin@omakeikka.fi"
COMPOSE=(docker compose --env-file .docker/docker.env)
WP=("${COMPOSE[@]}" run --rm -T wpcli)
DUMP_FILE="$(mktemp)"

trap 'rm -f "$DUMP_FILE"' EXIT

printf "Downloading the production database...\n"
# --single-transaction avoids locking production tables during the dump.
ssh "$REMOTE_HOST" "set -o pipefail; cd $REMOTE_PATH && wp db export - --single-transaction | gzip" >"$DUMP_FILE"

printf "Loading the dump...\n"
"${COMPOSE[@]}" exec -T db mariadb -uwp -pwppass -e 'DROP DATABASE IF EXISTS wordpress; CREATE DATABASE wordpress'
gunzip -c "$DUMP_FILE" | "${COMPOSE[@]}" exec -T db mariadb -uwp -pwppass wordpress

# Elementor stores URLs in JSON with escaped slashes, which a plain replace misses.
printf "Replacing %s with %s...\n" "$REMOTE_URL" "$LOCAL_URL"
"${WP[@]}" search-replace "$REMOTE_URL" "$LOCAL_URL" --all-tables-with-prefix --skip-columns=guid --report-changed-only
"${WP[@]}" search-replace "${REMOTE_URL//'/'/'\/'}" "${LOCAL_URL//'/'/'\/'}" --all-tables-with-prefix --skip-columns=guid --report-changed-only

printf "Disabling outgoing mail and clearing caches...\n"
"${WP[@]}" plugin deactivate wp-mail-smtp
"${WP[@]}" transient delete --all
"${WP[@]}" cache flush

# The copy may already hold an admin with this address and a real password,
# so the account is upserted rather than only created when missing.
printf "Setting up the local admin...\n"
if ! "${WP[@]}" user get "$LOCAL_ADMIN_EMAIL" --field=ID >/dev/null 2>&1; then
    "${WP[@]}" user create localadmin "$LOCAL_ADMIN_EMAIL" --role=administrator --porcelain >/dev/null
fi
"${WP[@]}" user update "$LOCAL_ADMIN_EMAIL" --user_pass=root --role=administrator --skip-email

printf "Done. Log in at %s/wp/wp-admin/ as %s / root.\n" "$LOCAL_URL" "$LOCAL_ADMIN_EMAIL"
