FROM php:8.2-fpm

RUN set -eux; \
    docker-php-ext-install pdo_mysql mysqli; \
    if grep -q '^clear_env' /usr/local/etc/php-fpm.d/www.conf; then \
        sed -i 's/^clear_env = .*/clear_env = no/' /usr/local/etc/php-fpm.d/www.conf; \
    else \
        echo 'clear_env = no' >> /usr/local/etc/php-fpm.d/www.conf; \
    fi

WORKDIR /var/www/html
