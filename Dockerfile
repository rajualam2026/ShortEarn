FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    sqlite3 \
    libsqlite3-dev \
    pkg-config \
 && docker-php-ext-configure pdo_sqlite \
 && docker-php-ext-install zip pdo_sqlite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN cp .env.example .env

RUN mkdir -p database && touch database/database.sqlite

RUN composer install --no-dev --optimize-autoloader

EXPOSE 10000

CMD sh -c "php artisan key:generate --force && php artisan migrate --force || true && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"
