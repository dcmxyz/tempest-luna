# *Luna*
> A starter kit for [Tempest PHP](https://tempestphp.com) using Inertia and Svelte.

> [!WARNING]
> Work in progress. A lot of things are still missing, including tests.

---

## Stack

| Layer | Technology |
|---|---|
| Framework | [Tempest PHP](https://tempestphp.com) 3.x+ |
| Bridge | [inertia-tempest](https://github.com/Maarten-Dekker/inertia-tempest) 4.x-dev |
| Frontend | [Svelte 5](https://svelte.dev) + [Inertia.js](https://inertiajs.com) |
| Language | PHP 8.5+ |
| Build | Vite + Tailwind CSS v4 + vite-plugin-tempest |
| Database | PostgreSQL |

---

## What's included

- **Authentication** register, login, logout
- **Session management** custom database-backed session manager with encrypted payload, IP address and user agent tracking
- **Middleware** `MustBeAuthenticated`, `RedirectIfAuthenticated`, `HandleInertiaRequests`
- **Validation** typed request classes for auth + custom `Unique` rule
- **Layouts** `App` and `Auth` Svelte layout components
- **Migrations** users, sessions, password resets
- **Typed route helper** lightweight `uri()` inspired by Ziggy. Builds type-safe URLs from a Tempest route manifest with param substitution, query string support and `uriIs()` for active path matching

---

## Requirements

- PHP 8.5+
- Composer
- PostgreSQL
- Node.js + Bun (or npm)

---

## Getting started

```bash
git clone https://github.com/dcmxyz/tempest-luna my-project

cd my-project

cp .env.example .env
```

```bash
composer install

bun install
```

```bash
php tempest key:generate

php tempest migrate:up
```

```bash
bun run dev
# or
npm run dev
```

---

## Architecture

```
app/
  CommandBus/         Command + Handler pairs
  Config/             Config DTOs and their .config.php return files
  Controllers/        HTTP controllers
  Database/
    Migrations/       Schema migrations
  Middleware/         HTTP middleware
  Models/             Database models
    Traits/
  Requests/           Typed request classes with validation
  Resources/
    css/              CSS entrypoint
    js/
      plugins/        Vite plugins (tempest-routes)
      routes/         uri() helper and generated route manifest
      Types/          Shared TypeScript types
      utils.ts
    pages/            Svelte components
      Authentication/
      Components/
      Layouts/
  Session/            Custom session manager and initializer
  Validation/         Custom validation rules
```

---

## Notes

- Session data is encrypted at rest via Tempest's `Encrypter`
- Auth operations go through the command bus (`RegisterUser`, `LoginUser`)
- No database-level foreign key constraints, relationships are managed in application code
- The route manifest (`.tempest/typescript/routes.ts`) is auto-generated on dev start and on every controller file change via the `tempestRoutes` Vite plugin