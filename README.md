# leek.cafe

Personal website built with Laravel.

## Development

### 1) Start MariaDB (Docker)

```sh
docker compose up -d db
```

### 2) Install PHP dependencies (first time)

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### 3) Frontend assets (Vite)

```sh
pnpm install
pnpm dev
```

Vite dev server runs on http://localhost:5173.

### Lint / format (Biome)

```sh
pnpm format
```

## Xdebug

If you run the app inside Docker, the dev PHP image includes Xdebug and is enabled by default.

It is configured with `xdebug.start_with_request=trigger`, so it will only start debugging when your browser extension sets the trigger.

Xdebug listens on port 9003 and connects to `host.docker.internal`.

## PHP Docker images

The PHP Dockerfile has two build targets:

- `dev`: includes Xdebug
- `prod`: no Xdebug

Example:

```sh
docker build -f docker/php/Dockerfile --target dev -t leekcafe-php-dev .
docker build -f docker/php/Dockerfile --target prod -t leekcafe-php-prod .
```
