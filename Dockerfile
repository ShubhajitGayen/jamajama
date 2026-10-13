FROM php:8.2-apache

ENV PORT=10000

RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev \
    && docker-php-ext-install mbstring pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . /var/www/html/
COPY docker/apache-render.conf /etc/apache2/conf-available/jamajama.conf
COPY docker/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
COPY docker/render-start.sh /usr/local/bin/render-start

RUN a2enconf jamajama \
    && chmod 0755 /usr/local/bin/render-start \
    && mkdir -p /opt/jamajama/uploads \
    && cp -a /var/www/html/uploads/. /opt/jamajama/uploads/ \
    && mkdir -p /var/www/html/uploads/hero-banners \
        /var/www/html/uploads/hero-videos \
        /var/www/html/uploads/video-posters \
        /var/www/html/uploads/gallery \
        /var/www/html/uploads/packages \
    && chown www-data:www-data /var/www/html/uploads \
        /var/www/html/uploads/hero-banners \
        /var/www/html/uploads/hero-videos \
        /var/www/html/uploads/video-posters \
        /var/www/html/uploads/gallery \
        /var/www/html/uploads/packages

EXPOSE 10000
CMD ["/usr/local/bin/render-start"]
