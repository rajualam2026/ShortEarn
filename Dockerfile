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

# Create SQLite database
RUN mkdir -p database && touch database/database.sqlite

# Create .env if missing
RUN cp .env.example .env || true

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
RUN npm install
RUN npm run build

# Laravel permissions
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# Optimize Laravel (ignore errors if APP_KEY isn't available yet)
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

EXPOSE 10000

CMD sh -c "php artisan key:generate --force && php artisan migrate --force || true && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"
