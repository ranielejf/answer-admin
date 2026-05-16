# Database Connectivity

## Objective
Document where the database is configured in `answer-admin` and the supported connection methods.

## Current Configuration Location
- Environment variables: `/Users/ranielejf/Projetos/answer-admin/.env`
- Laravel DB connection map: `/Users/ranielejf/Projetos/answer-admin/config/database.php`
- Docker service network: `/Users/ranielejf/Projetos/answer-admin/docker-compose.yml`

## Active Local Connection (Snapshot: 2026-05-16)
- `DB_CONNECTION=mysql`
- `DB_HOST=mysql`
- `DB_PORT=3306`
- `DB_DATABASE=answer_local`
- `DB_USERNAME=answer`
- `DB_PASSWORD` is defined in `.env` (do not expose outside local/dev context)

## How to Connect

### 1) Through Laravel (recommended for app validation)
Use Artisan/Tinker to verify the app can open a DB connection:

```bash
php artisan migrate:status
php artisan tinker --execute="DB::connection()->getPdo(); echo 'ok';"
```

### 2) Through Docker network (inside containers)
When running with Compose, host `mysql` resolves inside the project network.

Example from the app container:

```bash
docker compose exec app php artisan migrate:status
```

### 3) Through external SQL client (DBeaver/TablePlus/MySQL CLI)
Use these parameters:
- Host: `127.0.0.1` (if port is published) or container hostname when inside Docker network
- Port: `3306`
- Database: `answer_local`
- Username: value from `.env` `DB_USERNAME`
- Password: value from `.env` `DB_PASSWORD`

If using local CLI:

```bash
mysql -h 127.0.0.1 -P 3306 -u answer -p answer_local
```

## Troubleshooting
- `Connection refused`: MySQL container/service is down, or port is not published.
- `Unknown host mysql`: command is running outside Docker network; use `127.0.0.1` instead.
- `Access denied`: invalid credentials or user permission mismatch.
- App connects to wrong database: check `.env` and clear config cache:

```bash
php artisan config:clear
```

## Security Notes
- Never commit real production credentials.
- Keep `.env` out of version control.
- Prefer different credentials per environment (local/staging/production).
