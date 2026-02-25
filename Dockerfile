# ── Dockerfile ──────────────────────────────────────
# PHP 8.2 + Apache for Render Web Service
FROM php:8.2-apache

# ── Install PHP extensions ───────────────────────────
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ── Enable Apache mod_rewrite ────────────────────────
RUN a2enmod rewrite

# ── Set working directory ────────────────────────────
WORKDIR /var/www/html

# ── Copy all project files ───────────────────────────
COPY . .

# ── Set permissions ──────────────────────────────────
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# ── Apache config: allow .htaccess ──────────────────
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/allow-override.conf \
    && a2enconf allow-override

# ── Expose port 80 ──────────────────────────────────
EXPOSE 80

CMD ["apache2-foreground"]
