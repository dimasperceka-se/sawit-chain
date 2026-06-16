#!/usr/bin/env bash
# ============================================================
# Deploy SawitChain (app + palm-twin) on a server with Docker.
# Reads secrets from .env / palm-twin/.env (NOT committed).
#
#   ./deploy-server.sh
# ============================================================
set -euo pipefail
cd "$(dirname "$0")"

[ -f .env ]            || { echo "ERROR: missing .env (cp .env.example .env and fill it)"; exit 1; }
[ -f palm-twin/.env ]  || { echo "ERROR: missing palm-twin/.env (cp palm-twin/.env.example palm-twin/.env and fill it)"; exit 1; }

# Runtime upload dirs (kept out of git)
mkdir -p api/files api/uploads

echo "==> Building & starting containers ..."
docker compose -f docker-compose.remote.yml up -d --build

echo "==> Installing PHP dependencies (api/vendor) inside the container ..."
docker exec -e COMPOSER_PROCESS_TIMEOUT=0 -w /var/www/html/api palmoiltrace-app \
    composer install --no-dev --no-interaction --no-progress

# shellcheck disable=SC1091
set -a; . ./.env; set +a
echo "==> DONE."
echo "    App:        http://<server-ip>:${APP_PORT:-8080}"
echo "    Palm-twin:  http://<server-ip>:${PALM_TWIN_PORT:-5003}"
echo "    Make sure those ports are open in the firewall / security group,"
echo "    and that PALM_TWIN_URL in .env points to the server's public address."
