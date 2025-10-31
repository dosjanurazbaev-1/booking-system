FROM php:8.2-apache

# Копируем проект в Apache
COPY . /var/www/html/

# Устанавливаем PHP-расширения
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Даём Apache полные права на запись (CSV-файлы)
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html

EXPOSE 80
