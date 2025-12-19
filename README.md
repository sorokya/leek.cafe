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
docker compose run --rm app composer install
docker compose run --rm app cp .env.example .env
docker compose run --rm app php artisan key:generate
docker compose run --rm app php artisan migrate
```

### 3) Frontend assets (Vite)

```sh
docker compose run --rm node sh -lc "corepack enable && pnpm install"
docker compose up node
```

Vite dev server runs on http://localhost:5173.

### Lint / format (Biome)

```sh
docker compose run --rm node sh -lc "corepack enable && pnpm lint"
docker compose run --rm node sh -lc "corepack enable && pnpm format"
```

## Xdebug

Xdebug is installed in the PHP image but disabled by default.

To enable it for the current session:

```sh
XDEBUG_MODE=debug,develop docker compose up --build
```

It listens on port 9003 and connects to `host.docker.internal`.
