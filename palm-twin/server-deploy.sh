#!/usr/bin/env bash
#
# Runs ON the server (invoked by ./deploy.sh over SSH, or manually).
# Pulls latest code, rebuilds the Docker image, and restarts the container.
#
#   bash server-deploy.sh
#
# Requirements on the server: git, docker, and a .env file (DATABASE_URL, PORT=5003).
# nginx terminates the domain and proxies to 127.0.0.1:$HOST_PORT (see nginx/ + DEPLOY.md).

set -euo pipefail
cd "$(dirname "$0")"

APP_NAME="palm-twin"
HOST_PORT="${HOST_PORT:-5003}"   # host port nginx proxies to; container always listens on 5003

echo "▶ Pulling latest from GitHub…"
git pull --ff-only

if [ ! -f .env ]; then
  echo "✖ .env not found on the server. Create it (see .env.example) with DATABASE_URL and PORT=5003."
  exit 1
fi

echo "▶ Building Docker image (${APP_NAME})…"
docker build -t "${APP_NAME}:latest" .

echo "▶ Restarting container…"
docker rm -f "${APP_NAME}" 2>/dev/null || true
docker run -d --name "${APP_NAME}" --restart unless-stopped \
  -p "127.0.0.1:${HOST_PORT}:5003" \
  --env-file .env \
  "${APP_NAME}:latest"

echo "▶ Pruning dangling images…"
docker image prune -f >/dev/null 2>&1 || true

echo "✅ ${APP_NAME} running on 127.0.0.1:${HOST_PORT}"
