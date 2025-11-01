FROM php:8.2-apache

# Копируем проект в Apache
COPY . /var/www/html/

# Устанавливаем PHP-расширения
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Создаём каталог data (для CSV)
RUN mkdir -p /var/www/html/data

# Даём Apache полные права на запись в проект и data
RUN mkdir -p /var/data && \
    chown -R www-data:www-data /var/data && \
    chmod -R 775 /var/data && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html


# Включаем mod_rewrite (если понадобится позже)
RUN a2enmod rewrite

EXPOSE 80
