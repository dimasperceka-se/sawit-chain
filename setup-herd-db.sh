#!/usr/bin/env bash
# Create the local (native MySQL) database + user for running PalmOilTrace under
# Herd, then import palmoiltrace_demo.sql with the two fixes the dump needs:
#   * non-strict sql_mode  -> MySQL 8 would otherwise abort on truncated values
#   * strip SRID 4326      -> MySQL 8 reads 4326 latitude-first and rejects every
#                             POINT(lon lat) row; SRID 0 stores them as-is
#
# Usage:
#   ./setup-herd-db.sh '<local-mysql-root-password>'
#
# Target (matches application/config/database.php fallbacks):
#   host 127.0.0.1:3306   db palmoiltrace_demo   user palmoiltrace / palmoiltrace
set -euo pipefail

ROOT_PW="${1:-${MYSQL_ROOT_PW:-}}"
[ -n "$ROOT_PW" ] || { echo "Usage: $0 '<mysql-root-password>'"; exit 1; }

MYSQL=/usr/local/mysql/bin/mysql
DB=palmoiltrace_demo
APP_USER=palmoiltrace
APP_PW=palmoiltrace
DUMP="$(cd "$(dirname "$0")" && pwd)/palmoiltrace_demo.sql"

echo "==> Creating database '$DB' and user '$APP_USER'"
"$MYSQL" -h127.0.0.1 -uroot -p"$ROOT_PW" <<SQL
CREATE DATABASE IF NOT EXISTS \`$DB\` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER IF NOT EXISTS '$APP_USER'@'localhost' IDENTIFIED WITH mysql_native_password BY '$APP_PW';
CREATE USER IF NOT EXISTS '$APP_USER'@'127.0.0.1' IDENTIFIED WITH mysql_native_password BY '$APP_PW';
ALTER USER '$APP_USER'@'localhost'  IDENTIFIED WITH mysql_native_password BY '$APP_PW';
ALTER USER '$APP_USER'@'127.0.0.1'  IDENTIFIED WITH mysql_native_password BY '$APP_PW';
GRANT ALL PRIVILEGES ON \`$DB\`.* TO '$APP_USER'@'localhost';
GRANT ALL PRIVILEGES ON \`$DB\`.* TO '$APP_USER'@'127.0.0.1';
FLUSH PRIVILEGES;
-- The dump defines non-deterministic stored functions; with binary logging on
-- (MySQL 8 default) they're rejected unless this is allowed.
SET GLOBAL log_bin_trust_function_creators = 1;
SQL

echo "==> Importing $DUMP (~1.5GB, several minutes)..."
sed "s/', 4326)/')/g" "$DUMP" | "$MYSQL" -h127.0.0.1 -uroot -p"$ROOT_PW" \
    --force --max_allowed_packet=1G \
    --init-command="SET SESSION sql_mode='NO_ENGINE_SUBSTITUTION'" "$DB"

COUNT=$("$MYSQL" -h127.0.0.1 -uroot -p"$ROOT_PW" -N -e \
  "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB';")
echo "==> Done. '$DB' now has $COUNT tables."
