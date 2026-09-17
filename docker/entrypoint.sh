#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

mkdir -p \
  storage/app/public \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/testing \
  storage/framework/views \
  storage/logs \
  bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache

if [ -z "${APP_KEY:-}" ]; then
  export APP_KEY
  APP_KEY="$(php artisan key:generate --show --no-interaction)"
  echo "Generated ephemeral APP_KEY for this container. Set APP_KEY in docker/compose.env for persistent sessions."
fi

if [ "${DB_CONNECTION:-mysql}" = "mysql" ] && [ -n "${DB_HOST:-}" ]; then
  echo "Waiting for MySQL at ${DB_HOST}:${DB_PORT:-3306}..."
  php -r '
    $host = getenv("DB_HOST") ?: "db";
    $port = getenv("DB_PORT") ?: "3306";
    $db = getenv("DB_DATABASE") ?: "fixtora";
    $user = getenv("DB_USERNAME") ?: "fixtora";
    $pass = getenv("DB_PASSWORD") ?: "";
    $deadline = time() + 90;
    do {
        try {
            new PDO("mysql:host={$host};port={$port};dbname={$db}", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 3,
            ]);
            exit(0);
        } catch (Throwable $e) {
            usleep(500000);
        }
    } while (time() < $deadline);
    fwrite(STDERR, "Timed out waiting for MySQL\n");
    exit(1);
  '
fi

php artisan storage:link --force || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  php artisan migrate --force
fi

php artisan optimize:clear
php artisan config:cache
php artisan route:cache || true
php artisan view:cache || true

exec "$@"
