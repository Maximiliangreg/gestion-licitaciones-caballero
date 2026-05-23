### Multi-stage Dockerfile: build frontend assets with Node, copy into PHP image

FROM node:20 AS node_builder
WORKDIR /app

# copy package manifest and vite config
COPY package*.json ./
COPY vite.config.js ./
COPY resources ./resources

RUN npm ci --silent
RUN npm run build

FROM php:8.4-cli-alpine AS app
WORKDIR /var/www/html

# Install required PHP extensions and tools
RUN apk add --no-cache \
    curl \
    git \
    postgresql-client \
    libpq-dev \
    && docker-php-ext-configure pdo_pgsql \
    && docker-php-ext-install pdo pdo_pgsql bcmath ctype json tokenizer xml

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy full application
COPY . .

# Install PHP dependencies with Composer
RUN composer install --no-dev --optimize-autoloader

# Copy built frontend assets from node builder
COPY --from=node_builder /app/public/build ./public/build

# Set permissions for storage and bootstrap
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
