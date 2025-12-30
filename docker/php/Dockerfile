ARG PHP_IMAGE=php:8.5.1-fpm-alpine3.23
ARG NODE_IMAGE=node:24-alpine

FROM ${NODE_IMAGE} AS assets-builder

WORKDIR /app

COPY package.json pnpm-lock.yaml pnpm-workspace.yaml ./

RUN corepack enable \
    && pnpm install --frozen-lockfile

COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN pnpm run build

FROM ${PHP_IMAGE} AS php-ext-builder

RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        exif \
        gd \
        intl \
        pdo_mysql \
        zip

FROM php-ext-builder AS php-ext-builder-dev

RUN apk add --no-cache linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

FROM ${PHP_IMAGE} AS base

WORKDIR /app

RUN apk add --no-cache \
        git \
        unzip \
        nginx \
        supervisor \
        jpegoptim \
        optipng \
        pngquant \
        gifsicle \
        icu-libs \
        libzip \
        freetype \
        libjpeg-turbo \
        libpng

COPY --from=php-ext-builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=php-ext-builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY docker/php/php.base.ini /usr/local/etc/php/conf.d/zz-app.ini

COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 8000

FROM base AS dev

COPY --from=php-ext-builder-dev /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=php-ext-builder-dev /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/zz-xdebug.ini
COPY docker/php/php.dev.ini /usr/local/etc/php/conf.d/zz-env.ini

COPY composer.json composer.lock /app

RUN composer install --no-interaction --no-progress --prefer-dist --no-scripts

COPY . /app

COPY --from=assets-builder /app/public/build /app/public/build
RUN mkdir -p \
        /app/bootstrap/cache \
        /app/storage/app \
        /app/storage/framework/cache \
        /app/storage/framework/sessions \
        /app/storage/framework/views \
        /app/storage/logs \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache

ENTRYPOINT ["/usr/local/bin/entrypoint"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]

FROM base AS prod

COPY docker/php/php.prod.ini /usr/local/etc/php/conf.d/zz-env.ini

COPY composer.json composer.lock /app/
RUN composer install --no-interaction --no-progress --prefer-dist --optimize-autoloader --no-scripts --no-dev

COPY . /app

COPY --from=assets-builder /app/public/build /app/public/build

RUN mkdir -p \
        /app/bootstrap/cache \
        /app/storage/app \
        /app/storage/framework/cache \
        /app/storage/framework/sessions \
        /app/storage/framework/views \
        /app/storage/logs \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache

ENTRYPOINT ["/usr/local/bin/entrypoint"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
