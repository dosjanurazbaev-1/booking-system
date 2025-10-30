# Используем официальный PHP образ с Apache
FROM php:8.2-apache

# Копируем все файлы в контейнер
COPY . /var/www/html/

# Включаем нужные модули (например, mysqli, pdo и т.д.)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Открываем порт
EXPOSE 80
