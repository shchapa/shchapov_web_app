# Используем официальный образ PHP 8.1 с Apache
FROM php:8.1-apache

# Обновляем список пакетов и устанавливаем необходимые утилиты и расширения PHP
# default-mysql-client - для возможности подключения к MySQL из командной строки контейнера (для отладки)
# mysqli, pdo, pdo_mysql - расширения PHP для работы с MySQL
RUN apt-get update && \
    apt-get install -y default-mysql-client libzip-dev unzip && \
    docker-php-ext-install mysqli pdo pdo_mysql zip

# Включаем модуль Apache rewrite для ЧПУ (если понадобится в будущем, сейчас не используется)
RUN a2enmod rewrite

# Устанавливаем рабочую директорию
WORKDIR /var/www/html