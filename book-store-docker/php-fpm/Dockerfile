# book-store-docker/php/Dockerfile

FROM php:8.4-fpm-alpine

# ── System dependencies ───────────────────────────────────
RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    linux-headers \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    freetype \
    $PHPIZE_DEPS

# ── GD configuration ──────────────────────────────────────
RUN docker-php-ext-configure gd \
    --with-jpeg \
    --with-webp \
    --with-freetype

# ── PHP Extensions ────────────────────────────────────────
RUN docker-php-ext-install \
    pdo_pgsql \
    zip \
    pcntl \
    bcmath \
    opcache \
    sockets \
    gd

# ── PECL Extensions ───────────────────────────────────────
RUN pecl install redis \
    && docker-php-ext-enable redis

# ── Composer ──────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── PHP config ────────────────────────────────────────────
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

RUN addgroup -g 1000 appgroup \
    && adduser -u 1000 -G appgroup -s /bin/sh -D appuser

RUN mkdir -p /var/log/php \
    && chown -R appuser:appgroup /var/log/php

WORKDIR /var/www/html

USER appuser

EXPOSE 9000

CMD ["php-fpm"]