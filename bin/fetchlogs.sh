#!/usr/bin/env bash
#
# Read-only helper for inspecting the production LiteSpeed logs.
#
# The site runs on its own cchosting account (ssh host alias `omakeikka-web`)
# that serves only omakeikka.fi, so no domain filtering is needed. Rotation is
# daily:
#   access_log, error_log                  current day
#   access_log.YYYY-MM-DD                  previous day, plain
#   access_log.YYYY-MM-DD.gz               older days, compressed
# and the same for error_log. Roughly three months are kept. Access log lines
# are JSON objects, one per line.
#
# This script never writes to the remote host or to disk. It streams the
# (transparently decompressed) log content to stdout so it can be piped into
# grep/tail/less/jq etc, e.g.:
#   bin/fetchlogs.sh | grep '"status":403'
#   bin/fetchlogs.sh 'access_log.2026-10-*' | tail -n 100
#   bin/fetchlogs.sh error_log

# The path and glob are expanded on the client on purpose, the remote shell
# then expands the glob against the log directory.
# shellcheck disable=SC2029

set -euo pipefail

REMOTE_HOST="omakeikka-web"
REMOTE_PATH="/home/omakeikkafi/logs"

if [ "${1:-}" = "-h" ] || [ "${1:-}" = "--help" ]; then
    printf "Usage: bin/fetchlogs.sh [glob]\n"
    printf "  glob defaults to 'access_log' (current day). Pass e.g. 'access_log*' for the full history.\n"
    exit 0
fi

GLOB="${1:-access_log}"

ssh "$REMOTE_HOST" "zcat -f $REMOTE_PATH/$GLOB"
