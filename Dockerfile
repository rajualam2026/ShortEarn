FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    sqlite3 \
    libsqlite3-dev \
    libzip-dev \
    pkg-config \
    nodejs \
    npm \
 && docker-php-ext-install zip pdo pdo_sqlite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN cp .env.example .env || true

RUN mkdir -p database && touch database/database.sqlite

RUN composer install --no-dev --optimize-autoloader

RUN npm install
RUN npm run build

EXPOSE 10000

CMD sh -c "php artisan key:generate --force && php artisan migrate --force || true && php artisan serve --host=0.0.0.0 --host=0.0.0.0 --port=${PORT:-10000}"

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    sqlite3 \
    libsqlite3-dev \
    libzip-dev \
    pkg-config \
    nodejs \
    npm \
 && docker-php-ext-install zip pdo pdo_sqlite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN cp .env.example .env || true

RUN mkdir -p database && touch database/database.sqlite

RUN composer install --no-dev --optimize-autoloader

RUN npm install
RUN npm run build

EXPOSE 10000

CMD sh -c "php artisan key:generate --force && php artisan migrate --force || true && php artisan serve --host=0.0.0.0 --host=0.0.0.0 --port=${PORT:-10000}"
