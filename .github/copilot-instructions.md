# Copilot Instructions — Laravel API Boilerplate

## Tech Stack

- **PHP 8.4+**, **Laravel 13**, **Sanctum 4** (token-based auth)
- **Pest 4** (testing), **Larastan level 9** (static analysis), **Pint** (code style, Laravel preset)
- Database: SQLite (dev/test), no Eloquent ORM alternatives

---

## 1. Strict Typing

Every PHP file MUST start with:

```php
<?php

declare(strict_types=1);
```

- All methods MUST have explicit return types. Never use `mixed`.
- Use PHPDoc `@var`, `@return`, `@param` only when the type system cannot express it (e.g., generic arrays, template types).
- Annotate arrays precisely: `array<string, mixed>`, `list<string>`, `array{key: type}`.

---

## 2. Architecture Pattern

### Action Classes (`app/Actions/{Domain}/`)

All business logic lives in Action classes. Controllers MUST NOT contain business logic.

```
app/Actions/{Domain}/{Verb}{Entity}Action.php
```

Rules:
- Class MUST be `final`.
- Single public method: `execute(...)`.
- Accept a DTO or Model as input, return a Model, value object, or `void`.
- Declare thrown exceptions via `@throws` docblock.

Example: `App\Actions\Auth\LoginUserAction::execute(LoginUserData $data): User`

### DTOs (`app/DTOs/{Domain}/`)

Data Transfer Objects carry validated data from Request to Action.

```
app/DTOs/{Domain}/{Verb}{Entity}Data.php
```

Rules:
- Class MUST be `final readonly`.
- Use promoted constructor properties (`public string $name`).
- Include a static factory: `fromRequest(SomeRequest $request): self`.
- Inside `fromRequest`, call `$request->validated()` and cast with a PHPDoc `@var` array shape.

### Controllers (`app/Http/Controllers/{Domain}/`)

One action per controller (invokable `__invoke`) for single-purpose endpoints. Use a resource controller (`show`, `index`, `store`, `update`, `destroy`) only for full CRUD on a resource.

```
app/Http/Controllers/{Domain}/{Verb}Controller.php    // invokable
app/Http/Controllers/{Entity}Controller.php            // resource
```

Rules:
- Class MUST be `final` and extend `App\Http\Controllers\Controller`.
- Inject Action + FormRequest via method parameters (auto-resolved).
- Flow: `Request → DTO::fromRequest() → Action::execute() → Response`.
- Return `JsonResponse` using `$this->successResponse(...)` or `$this->errorResponse(...)`.
- Use `DB::transaction(fn () => ...)` when the operation involves multiple writes.
- Authorize via `Gate::authorize('ability', $model)` inside the controller.

### Form Requests (`app/Http/Requests/{Domain}/`)

```
app/Http/Requests/{Domain}/{Verb}Request.php
```

Rules:
- Class MUST be `final` and extend `Illuminate\Foundation\Http\FormRequest`.
- `authorize()` returns `bool`.
- `rules()` return type: `array<string, ValidationRule|array<mixed>|string>`.

### Resources (`app/Http/Resources/`)

```
app/Http/Resources/{Entity}Resource.php
```

Rules:
- Class MUST be `final` and extend `JsonResource`.
- Add `@mixin Model` docblock for IDE support.
- `toArray()` MUST return a typed array shape via `@return` docblock.
- Format dates with `->toIso8601String()`.
- Never expose sensitive fields (`password`, `remember_token`).

### Models (`app/Models/`)

Rules:
- Class MUST be `final`.
- Use `HasFactory` with `@use HasFactory<ModelFactory>` annotation.
- Define `$fillable`, `$hidden`, and `casts()`.
- Cast `password` to `hashed` for auto-hashing.

### Policies (`app/Policies/`)

```
app/Policies/{Entity}Policy.php
```

Rules:
- Class MUST be `final`.
- Methods accept `(User $authenticatedUser, Model $model)` and return `bool`.

### Enums (`app/Enums/`)

Rules:
- MUST be backed enums (`string` or `int`).
- Use `const` for static values (e.g., `TOKEN_NAME`).
- Include a `values(): list<string>` helper when the enum values need to be passed as an array.

### Exceptions (`app/Exceptions/{Domain}/`)

```
app/Exceptions/{Domain}/{Descriptive}Exception.php
```

Rules:
- Class MUST be `final` and extend `Symfony\Component\HttpKernel\Exception\HttpException`.
- Set `statusCode` and `message` in the constructor.
- Override `report(): bool` and return `false` if the exception should not be logged.

### Support Traits (`app/Support/`)

- `ApiResponse` trait provides `successResponse()` and `errorResponse()`.
- All controllers inherit these via the base `Controller` class.
- MUST be traits — arch tests enforce `App\Support` contains only traits.

### Middleware (`app/Http/Middleware/`)

- Class MUST be `final`.
- `ForceJsonResponse` is prepended to all API routes — all responses are JSON.

---

## 3. API Response Format

### Success

```json
{
    "message": "Human-readable message.",
    "data": { ... }
}
```

Use `$this->successResponse(data: ..., message: '...', status: 200)`.

### Error

```json
{
    "message": "Error description.",
    "errors": { "field": ["detail"] }
}
```

- Validation errors (422) are handled automatically by Laravel.
- `HttpException` subclasses are rendered as `{ "message": "..." }` with the appropriate status code (configured in `bootstrap/app.php`).

---

## 4. Routing

All routes are defined in `routes/api.php` without API version prefix:

```php
Route::prefix('auth')->as('auth.')->group(function () { ... });
```

Rules:
- Use invokable controller syntax: `Route::post('/path', ActionController::class)`.
- Use resource controller syntax: `Route::get('/entity', [EntityController::class, 'method'])`.
- Apply `auth:sanctum` middleware to protected routes.
- Apply `ability:{ability}` middleware for token-scoped access.
- Apply `throttle:n,m` for rate limiting on public endpoints (e.g., login, register).
- Name all routes: `->name('resource.action')`.

---

## 5. Database & Migrations

Migration file naming: `YYYY_MM_DD_HHMMSS_create_{table}_table.php` (Laravel default).

Rules:
- Every migration MUST use `declare(strict_types=1)`.
- Always include `$table->timestamps()`.
- Add `->index()` on columns used for frequent lookups or filtering.
- Add `->unique()` on columns that require uniqueness (e.g., `email`).
- Use `$table->softDeletes()` when records should not be permanently deleted; add `SoftDeletes` trait to the corresponding Model.
- Use anonymous migration classes (`return new class extends Migration`).
- Every migration MUST have both `up()` and `down()` methods.

---

## 6. Naming Conventions

| Artifact                | Pattern                              | Example                          |
|-------------------------|--------------------------------------|----------------------------------|
| Action                  | `{Verb}{Entity}Action`               | `CreateOrderAction`              |
| DTO                     | `{Verb}{Entity}Data`                 | `CreateOrderData`                |
| Controller (invokable)  | `{Verb}Controller`                   | `LoginController`                |
| Controller (resource)   | `{Entity}Controller`                 | `UserController`                 |
| FormRequest             | `{Verb}Request`                      | `LoginRequest`                   |
| Resource                | `{Entity}Resource`                   | `UserResource`                   |
| Policy                  | `{Entity}Policy`                     | `UserPolicy`                     |
| Exception               | `{Descriptive}Exception`             | `InvalidCredentialsException`    |
| Enum                    | `{PascalCase}`                       | `TokenAbility`                   |
| Migration               | `create_{table}_table`               | `create_orders_table`            |
| Factory                 | `{Entity}Factory`                    | `UserFactory`                    |
| Feature Test            | `{Entity}Test` or `{Verb}Test`       | `LoginTest`, `UserTest`          |
| Unit Test               | `{Class}Test`                        | `LoginUserActionTest`            |

---

## 7. Testing (Pest)

Every new feature MUST include tests. No exceptions.

### Test Structure

```
tests/
  Arch/ArchTest.php          ← architecture rules (auto-run)
  Feature/{Domain}/          ← HTTP integration tests
  Feature/{Entity}Test.php   ← resource endpoint tests
  Unit/Actions/              ← action unit tests
  Unit/DTOs/                 ← DTO unit tests
  Unit/Exceptions/           ← exception unit tests
  Unit/Policies/             ← policy unit tests
  Unit/Resources/            ← resource unit tests
  Unit/Support/              ← trait/utility unit tests
```

### Rules

- All test files MUST start with `declare(strict_types=1)`.
- Use `describe('ClassName or Endpoint', fn () => ...)` to group tests.
- Use `it('does something specific', fn () => ...)` for individual test cases.
- Separate Happy Path & Edge Cases with comment banners:

```php
// ── Happy Path ──────────────────────────────────────────────────────
// ── Validation Errors (422) ─────────────────────────────────────────
// ── Unauthorized (401) ──────────────────────────────────────────────
```

### Feature Tests

- Test the full HTTP lifecycle: `$this->postJson('/api/...')`.
- Assert status codes, JSON structure (`assertJsonStructure`), and specific values (`assertJsonPath`).
- Assert database state (`assertDatabaseHas`, `assertDatabaseMissing`).
- Use `Sanctum::actingAs($user, abilities)` for authenticated requests.
- Cover: success, authentication failure, authorization failure, validation errors, rate limiting.

### Unit Tests

- Test Action classes by instantiating directly and calling `execute(...)`.
- Test DTOs for constructor instantiation, `fromRequest()`, and readonly enforcement.
- Test Exceptions for status code, message, and `report()` return.
- Test Policies for allow/deny per user relationship.
- Test Resources by calling `toArray()` and asserting keys/values.
- Use `->throws(ExceptionClass::class)` for expected exception tests.

### Pest Config (`tests/Pest.php`)

- `Feature` tests use `RefreshDatabase`.
- `Unit/Actions`, `Unit/Resources`, `Unit/Policies`, `Unit/DTOs` use `RefreshDatabase`.
- `Unit/Exceptions`, `Unit/Support` use base `TestCase` without `RefreshDatabase`.
- When adding a new Unit subdirectory that needs database access, register it in `Pest.php`.

---

## 8. Architecture Tests

Architecture rules are enforced in `tests/Arch/ArchTest.php`. When adding new classes, ensure they comply:

- Models → `final`, extend `Model` (or `Authenticatable` for User).
- Controllers → `final`, extend `App\Http\Controllers\Controller`.
- Actions → `final`.
- DTOs → `final readonly`.
- FormRequests → `final`, extend `FormRequest`.
- Resources → `final`.
- Exceptions → `final`, extend `Exception`.
- Policies → `final`.
- Middleware → `final`.
- Enums → must be enums.
- Support → must be traits.
- No `dd`, `dump`, `ray`, `var_dump`, `print_r` in app code.
- App code must not depend on `Tests` namespace.

---

## 9. Static Analysis (Larastan)

- Level: **9** (strictest).
- Config: `phpstan.neon`.
- All code MUST pass `./vendor/bin/phpstan analyse` with zero errors.
- Use `@phpstan-ignore` comments only when strictly necessary (e.g., framework type limitations).

---

## 10. Code Style (Pint)

- Preset: `laravel`.
- Enforced: `declare_strict_types`, `ordered_imports` (alpha), `no_unused_imports`.
- Run `./vendor/bin/pint` before committing.

---

## 11. New Feature Checklist

When creating a new feature, generate ALL of the following:

1. **Migration** — if new tables/columns are needed.
2. **Model** — with `$fillable`, `$hidden`, `casts()`, factory.
3. **Factory** — for the new model.
4. **Policy** — for authorization rules.
5. **DTO** — `final readonly`, with `fromRequest()` factory.
6. **Action** — `final`, single `execute()` method.
7. **FormRequest** — `final`, with validation rules.
8. **Controller** — `final`, thin, delegates to Action.
9. **Resource** — `final`, typed `toArray()`.
10. **Routes** — non-versioned under `/api`, named, with appropriate middleware.
11. **Feature Tests** — happy path, auth, validation, edge cases.
12. **Unit Tests** — for Action, DTO, Policy, Resource, Exception.
13. **Arch Test Update** — add rules if introducing new architectural layers.

---

## 12. Security Practices

- Passwords are auto-hashed via `'password' => 'hashed'` cast — never hash manually in Actions.
- Prevent timing-based user enumeration: always run `Hash::make()` even when user is not found.
- Rate limit public endpoints (`throttle:5,1` on login/register).
- Token abilities restrict access scope — use `ability` middleware.
- Use `Gate::authorize()` for resource-level authorization.
- Never expose `password` or `remember_token` in API responses.
- `ForceJsonResponse` middleware ensures all API responses are JSON (prevents HTML error leaks).
