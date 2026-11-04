FROM php:8.2-apache
# Instalar extensiones PHP necesarias
RUN docker-php-ext-install pdo pdo_mysql mysqli
# Habilitar mod_rewrite
RUN a2enmod rewrite
# Copiar archivos
COPY . /var/www/html/
# Permisos
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html
# Configurar Apache para usar 10000 (puerto de Render)
RUN sed -i 's/80/10000/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf
EXPOSE 10000
