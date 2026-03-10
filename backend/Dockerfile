FROM justintime50/nginx-php:8.2-latest

COPY . /var/www/html

WORKDIR /var/www/html

# Permisos para Laravel
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Configuración de producción
ENV APP_ENV=production
ENV APP_DEBUG=false

CMD ["/start.sh"]