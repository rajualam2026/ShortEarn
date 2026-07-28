FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libsqlite3-dev \
    libzip-dev \
    pkg-config \
    nodejs \
    npm \
 && docker-php-ext-install zip pdo_sqlite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN mkdir -p database && touch database/database.sqlite

RUN composer install --no-dev --optimize-autoloader

RUN npm install
RUN npm run build
RUN cp .env.example .env || true
EXPOSE 10000

CMD sh -c "php artisan key:generate --force && php artisan migrate --force || true && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"
