# ── Base image: PHP 8.2 with Apache ──────────────────────────
FROM php:8.2-apache

# ── Install system dependencies & PHP extensions ─────────────
RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev pkg-config \
    && docker-php-ext-install mbstring pdo pdo_mysql \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ── Copy Apache virtual-host config ─────────────────────────
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# ── Copy application source into the container ───────────────
COPY autoexpert-php/ /var/www/html/

# ── Ensure the data directory is writable (for users.json) ───
RUN mkdir -p /var/www/html/includes/data \
    && chown -R www-data:www-data /var/www/html/includes/data

# ── Expose port 80 ──────────────────────────────────────────
EXPOSE 80
