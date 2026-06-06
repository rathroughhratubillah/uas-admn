# ============================================================
# Dockerfile — dynamic-app
# UAS Cloud Computing II | NIM: 2388010012
# Base: php:8.3-apache
# ============================================================

FROM php:8.3-apache

# ---- Install ekstensi PDO MySQL ----
RUN docker-php-ext-install pdo pdo_mysql mysqli

# ---- Aktifkan mod_rewrite Apache ----
RUN a2enmod rewrite

# ---- Set DocumentRoot ke /var/www/html/public ----
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' \
        /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|<Directory /var/www/html>|<Directory /var/www/html/public>|g' \
        /etc/apache2/apache2.conf \
    || true

# Izinkan .htaccess dan AllowOverride All
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# ---- Salin kode aplikasi ----
COPY . /var/www/html/

# ---- Salin entrypoint ----
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# ---- Izin folder ----
RUN chown -R www-data:www-data /var/www/html

# ---- Expose port ----
EXPOSE 80

# ---- Entrypoint: setup.php → Apache ----
ENTRYPOINT ["docker-entrypoint.sh"]
