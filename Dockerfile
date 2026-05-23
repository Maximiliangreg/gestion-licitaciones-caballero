### Multi-stage Dockerfile: build frontend assets with Node, copy into PHP image

FROM node:20 AS node_builder
WORKDIR /app

# copy package manifest and vite config
COPY package*.json ./
COPY vite.config.js ./
COPY resources ./resources

RUN npm ci --silent
RUN npm run build

FROM openswoole/swoole:22.0.0-php8.4-alpine AS app
WORKDIR /var/www/html

# Copy full application (composer/vendor should be handled outside or earlier)
COPY . .

# Copy built frontend assets from node builder
COPY --from=node_builder /app/public/build ./public/build

EXPOSE 80

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
