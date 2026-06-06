#!/bin/sh
# ============================================================
# docker-entrypoint.sh
# 1. Jalankan setup.php (buat admin + seed data)
# 2. Start Apache di foreground
# ============================================================

set -e

echo "==> [entrypoint] Menjalankan setup awal aplikasi..."
php /var/www/html/setup.php

echo "==> [entrypoint] Memulai Apache2..."
exec apache2-foreground
