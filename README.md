# Laravel Backend Boilerplate

Boilerplate untuk backend API menggunakan Laravel 13, Sanctum, dan tooling modern.

## Tech Stack

- **PHP** 8.3+
- **Laravel** 13.x
- **Auth** Laravel Sanctum 4 (token-based)
- **Static Analysis** Larastan (PHPStan level 9)
- **Code Style** Laravel Pint (Laravel preset)
- **Testing** Pest 4
- **Git Hooks** Husky + lint-staged + Commitlint

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

- **Pre-commit** (lint-staged): Pint pada semua staged PHP files, PHPStan pada staged files di `app/`
- **Commit-msg**: Commitlint memvalidasi format [Conventional Commits](https://www.conventionalcommits.org/)
- **Pre-push**: PHPStan full analyse + test suite

## Project Structure

```
app/
├── Actions/             # Business logic (Action classes)
├── DTOs/                # Data Transfer Objects
├── Enums/               # Backed enums
├── Exceptions/          # Custom HTTP exceptions
├── Http/
│   ├── Controllers/     # API Controllers (thin, invokable)
│   ├── Middleware/       # Custom middleware (ForceJsonResponse)
│   ├── Requests/        # Form Request validation
│   └── Resources/       # API Resources (JSON transformers)
├── Models/              # Eloquent Models
├── Policies/            # Authorization Policies
├── Providers/           # Service Providers
└── Support/             # Shared traits (ApiResponse)
bootstrap/
├── app.php              # Application bootstrap & routing
config/                  # Configuration files
database/
├── factories/           # Model Factories
├── migrations/          # Database Migrations
└── seeders/             # Database Seeders
routes/
├── api.php              # API Routes (versioned under /v1)
└── console.php          # Console Commands & Schedules
tests/
├── Arch/                # Architecture rules (Pest arch tests)
├── Feature/             # HTTP integration tests
└── Unit/                # Unit tests (Actions, DTOs, Policies, etc.)
```

## API Endpoints

Semua endpoint berada di bawah prefix `/api/v1`.

### Public (rate limited)

| Method | Path             | Deskripsi       |
| ------ | ---------------- | --------------- |
| POST   | `/auth/register` | Registrasi user |
| POST   | `/auth/login`    | Login user      |

### Authenticated (`auth:sanctum`)

| Method | Path            | Deskripsi             |
| ------ | --------------- | --------------------- |
| POST   | `/auth/logout`  | Logout (revoke token) |
| POST   | `/auth/refresh` | Refresh token         |
| GET    | `/user`         | Get current user      |

## Environment Variables

Lihat `.env.example` untuk daftar lengkap. Variable penting:

| Variable           | Deskripsi                         |
| ------------------ | --------------------------------- |
| `FRONTEND_URL`     | URL frontend untuk CORS           |
| `DB_CONNECTION`    | Database driver (default: sqlite) |
| `QUEUE_CONNECTION` | Queue driver (default: database)  |
| `BCRYPT_ROUNDS`    | Bcrypt cost factor (default: 12)  |

## API Authentication

Menggunakan Laravel Sanctum dengan **token-based authentication**:

- Client mengirim `POST /api/v1/auth/login` untuk mendapatkan Bearer token.
- Token disertakan di header `Authorization: Bearer {token}` pada setiap request.
- Token memiliki **abilities** (`api:access`, `profile:manage`) untuk membatasi scope akses.
- Endpoint `/auth/refresh` untuk merotasi token tanpa login ulang.
