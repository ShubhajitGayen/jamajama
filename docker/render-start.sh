#!/bin/sh
set -eu

PORT="${PORT:-10000}"
case "$PORT" in
    ''|*[!0-9]*)
        echo "PORT must be a numeric TCP port." >&2
        exit 1
        ;;
esac

sed -ri "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \\*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

mkdir -p /var/www/html/uploads
cp -an /opt/jamajama/uploads/. /var/www/html/uploads/
mkdir -p /var/www/html/uploads/hero-banners \
    /var/www/html/uploads/hero-videos \
    /var/www/html/uploads/video-posters \
    /var/www/html/uploads/gallery \
    /var/www/html/uploads/packages
chown www-data:www-data /var/www/html/uploads \
    /var/www/html/uploads/hero-banners \
    /var/www/html/uploads/hero-videos \
    /var/www/html/uploads/video-posters \
    /var/www/html/uploads/gallery \
    /var/www/html/uploads/packages

exec apache2-foreground
