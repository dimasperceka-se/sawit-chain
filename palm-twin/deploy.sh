#!/usr/bin/env bash
#
# 🚀 Happy deploy command (run from your machine — works in Git Bash on Windows).
#
#   ./deploy.sh                 # commit all changes, push, redeploy on the server
#   ./deploy.sh "my message"    # custom commit message
#
# It does, in order:
#   1. commit + push your code to GitHub (dimasperceka-se/palm-twin)
#   2. SSH into the server, `git pull`, rebuild the Docker image, restart the container
#
# First time? See DEPLOY.md for the one-time server setup (clone, .env, nginx, HTTPS).

set -euo pipefail
cd "$(dirname "$0")"

if [ ! -f deploy.config ]; then
  echo "✖ Missing deploy.config — copy deploy.config.example → deploy.config and fill in your server details."
  exit 1
fi
# shellcheck disable=SC1091
source deploy.config

BRANCH="${BRANCH:-main}"
MSG="${1:-deploy $(date -u +%Y-%m-%dT%H:%M:%SZ)}"

echo "▶ 1/2  Commit & push to GitHub ($BRANCH)…"
git add -A
git commit -m "$MSG" || echo "   (nothing new to commit — pushing current $BRANCH)"
git push origin "$BRANCH"

echo "▶ 2/2  Pull & redeploy on ${SERVER_HOST}…"
ssh "${SERVER_USER}@${SERVER_HOST}" \
  "cd '${APP_DIR}' && HOST_PORT='${HOST_PORT:-5003}' bash server-deploy.sh"

echo "✅ Done — live at https://${DOMAIN}"
