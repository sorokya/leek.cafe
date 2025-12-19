# leek.cafe

Personal website built with Laravel.

## Development (Docker)

This repo is set up to develop entirely inside Docker:

### 1) Start the containers

```sh
docker compose up --build
```

The app will be available at http://localhost:8000.

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

Xdebug is installed in the PHP image but disabled by default.

To enable it for the current session:

```sh
XDEBUG_MODE=debug,develop docker compose up --build
```

It listens on port 9003 and connects to `host.docker.internal`.
