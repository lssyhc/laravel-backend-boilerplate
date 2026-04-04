# Laravel Backend Boilerplate

Boilerplate untuk backend API menggunakan Laravel 13, Sanctum, dan tooling modern.

## Tech Stack

- **PHP** 8.3+
- **Laravel** 13.x
- **Auth** Laravel Sanctum (token + SPA)
- **Static Analysis** Larastan (PHPStan level 5)
- **Code Style** Laravel Pint
- **Testing** PHPUnit 12
- **Git Hooks** Husky + lint-staged

## Setup

```bash
# Clone & install
composer setup
```

Script `composer setup` akan menjalankan:

1. `composer install`
2. Copy `.env.example` → `.env` (jika belum ada)
3. Generate app key
4. Run migrations

## Development

```bash
# Start server + queue + log watcher
composer dev
```

Ini menjalankan secara paralel:

- `php artisan serve` — HTTP server
- `php artisan queue:listen` — Queue worker
- `php artisan pail` — Real-time log viewer

## Testing

```bash
composer test
```

## Code Quality

```bash
# Format code
vendor/bin/pint

# Static analysis
vendor/bin/phpstan analyse --memory-limit=2G
```

**Git hooks** (via Husky) otomatis menjalankan:

- **Pre-commit**: Pint + PHPStan pada staged files
- **Pre-push**: PHPStan + tests

## Project Structure

```
app/
├── Http/Controllers/    # API Controllers
├── Models/              # Eloquent Models
└── Providers/           # Service Providers
bootstrap/
├── app.php              # Application bootstrap & routing
config/                  # Configuration files
database/
├── factories/           # Model Factories
├── migrations/          # Database Migrations
└── seeders/             # Database Seeders
routes/
├── api.php              # API Routes
└── console.php          # Console Commands & Schedules
tests/
├── Feature/             # Feature/Integration Tests
└── Unit/                # Unit Tests
```

## Environment Variables

Lihat `.env.example` untuk daftar lengkap. Variable penting:

| Variable                   | Deskripsi                              |
| -------------------------- | -------------------------------------- |
| `FRONTEND_URL`             | URL frontend SPA untuk CORS            |
| `SANCTUM_STATEFUL_DOMAINS` | Domain yang mendapat cookie-based auth |
| `DB_CONNECTION`            | Database driver (default: sqlite)      |
| `QUEUE_CONNECTION`         | Queue driver (default: database)       |

## API Authentication

Menggunakan Laravel Sanctum dengan dua mode:

1. **Token-based** — untuk mobile/3rd-party (Bearer token via `Authorization` header)
2. **Cookie-based (SPA)** — untuk first-party SPA (session + CSRF)

## License

MIT
