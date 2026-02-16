# syntax=docker/dockerfile:1.7
ARG PHP_IMAGE=php:8.5.1-fpm-alpine3.23
ARG NODE_IMAGE=node:24-alpine

FROM ${NODE_IMAGE} AS assets-builder

WORKDIR /app

COPY package.json pnpm-lock.yaml pnpm-workspace.yaml ./

RUN corepack enable \
    && pnpm config set store-dir /root/.pnpm-store

RUN --mount=type=cache,target=/root/.pnpm-store \
    pnpm install --frozen-lockfile

COPY css css
COPY js js
COPY build.mjs .

RUN pnpm run build

FROM ${PHP_IMAGE} AS php-ext-builder

RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        freetype-dev

RUN docker-php-ext-install -j"$(nproc)" \
        pdo_mysql

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
        supervisor

RUN addgroup -S app && adduser -S -G app app
RUN chown -R app:app /app \
    && chown -R app:app /var/lib/nginx \
    && chown -R app:app /var/log/nginx \
    && chown -R app:app /run

COPY --from=php-ext-builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=php-ext-builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY docker/php.base.ini /usr/local/etc/php/conf.d/zz-app.ini

# Remove default users. We run as non-root
RUN sed -i 's/user nginx;//' /etc/nginx/nginx.conf
RUN sed -i 's/user =/;user =/' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i 's/group =/;group =/' /usr/local/etc/php-fpm.d/www.conf

# Disable access logs
RUN echo "access.log = /dev/null" >> /usr/local/etc/php-fpm.d/www.conf

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 8000

COPY public public
COPY src src
COPY composer.json composer.lock .

COPY --from=assets-builder /app/public/js /app/public/css ./public

ENTRYPOINT ["/usr/local/bin/entrypoint"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]

FROM base AS dev

COPY --from=php-ext-builder-dev /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=php-ext-builder-dev /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

COPY docker/xdebug.ini /usr/local/etc/php/conf.d/zz-xdebug.ini
COPY docker/php.dev.ini /usr/local/etc/php/conf.d/zz-env.ini

RUN --mount=type=cache,target=/root/.composer \
    composer install \
      --no-interaction \
      --no-progress \
      --prefer-dist \
      --no-scripts

USER app

FROM base AS prod

COPY docker/php.prod.ini /usr/local/etc/php/conf.d/zz-env.ini

RUN --mount=type=cache,target=/root/.composer \
    composer install \
      --no-interaction \
      --no-progress \
      --prefer-dist \
      --optimize-autoloader \
      --no-scripts \
      --no-dev

USER app