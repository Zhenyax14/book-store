# 🚀 BookShop — Installation Guide

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- Git

---

## 1. Clone the repository

```bash
git clone https://github.com/zhenyax14/book_shop.git
cd book_shop
```

---

## 2. Configure environment

```bash
cd book-store-docker
cp .env.example .env
```

Edit `book-store-docker/.env` with your values:

```env
# Database
DB_NAME=bookstore
DB_USER=evgenii
DB_PASSWORD=smx
DB_PORT=5432

# Meilisearch
MEILI_MASTER_KEY=masterkey123
MEILI_ENV=development

# MinIO
MINIO_ROOT_USER=minio
MINIO_ROOT_PASSWORD=minio123

# Stripe
STRIPE_PUBLIC_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...

# App (leave empty, generated in step 5)
APP_KEY=

# Docker user (avoids permission issues)
UID=1000
GID=1000
```

Edit `book-store-backend/.env` — make sure these values match:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=bookstore
DB_USERNAME=evgenii
DB_PASSWORD=smx

REDIS_HOST=redis
REDIS_PORT=6379

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=rabbitmq

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

---

## 3. Build and start containers

```bash
docker compose up -d
```

---

## 4. Install PHP dependencies

```bash
docker compose exec app composer install --no-interaction
```

---

## 5. Generate application key

```bash
chmod 666 ../book-store-backend/.env
docker compose exec app php artisan key:generate
```

---

## 6. Run migrations

```bash
docker compose exec app php artisan migrate
```

---

## 7. (Optional) Seed database

```bash
docker compose exec app php artisan db:seed
```

---

## 8. Verify everything works

```bash
curl http://localhost:8080/api/v1/books
```

---

## 🌐 Services

| Service       | URL                          | Credentials          |
|---------------|------------------------------|----------------------|
| App (API)     | http://localhost:8080        | —                    |
| Frontend      | http://localhost:5173        | —                    |
| RabbitMQ UI   | http://localhost:15672       | guest / guest        |
| MinIO UI      | http://localhost:9001        | minio / minio123     |
| Mailpit UI    | http://localhost:8025        | —                    |
| Meilisearch   | http://localhost:7700        | —                    |
| PostgreSQL    | localhost:5432               | evgenii / smx        |

---

## 🔄 Common commands

```bash
# Stop all containers
docker compose down

# Stop and delete all data (fresh start)
docker compose down -v

# View app logs
docker compose logs app -f

# Run artisan commands
docker compose exec app php artisan <command>

# Clear all caches
docker compose exec app php artisan optimize:clear
```

---

## ⚠️ Troubleshooting

**500 error on `/api/v1/books`**
→ Check `APP_DEBUG=true`, then run `docker compose exec app php artisan config:clear`

**`password authentication failed` for PostgreSQL**
→ The volume has stale data. Run `docker compose down -v && docker compose up -d`

**`Permission denied` on `.env`**
→ Run `chmod 666 ../book-store-backend/.env`

**`could not find driver (mysql)`**
→ Laravel cached old config. Run `docker compose exec app rm -f bootstrap/cache/*.php`
