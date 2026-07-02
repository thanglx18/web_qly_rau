FROM php:8.1-apache
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN a2enmod rewrite

# Tự động redirect từ root (/) vào đúng thư mục dự án (/hi/public/)
RUN echo '<?php header("Location: /hi/public/"); exit;' > /var/www/html/index.php

WORKDIR /var/www/html/hi
