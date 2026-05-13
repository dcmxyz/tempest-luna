# Luna
A starter kit for [Tempest PHP](https://tempestphp.com) using Inertia and Svelte.

---

> [!NOTE]
> This project is not production-ready (and tempest itself is not yet stable), but you can use it as a starting point for your own projects.

> [!NOTE]
> This is a work in progress. It already works, but there are still some things missing.

---

![README_SCREEN.png](README_SCREEN.png)

---

## Stack

| Layer     | Technology                                                                   |
|-----------|------------------------------------------------------------------------------|
| Framework | [Tempest PHP](https://tempestphp.com) 3.x+                                   |
| Bridge    | [inertia-tempest](https://github.com/Maarten-Dekker/inertia-tempest) 4.x-dev |
| Frontend  | [Svelte 5](https://svelte.dev) + [Inertia.js](https://inertiajs.com)         |
| Language  | PHP 8.5+                                                                     |
| Build     | Vite + Tailwind CSS v4 + vite-plugin-tempest                                 |
| Database  | PostgreSQL, MySQL, or SQLite                                                 |

---

## Included

- **Welcome Page**
- **Dashboard Skeleton Page**
- **Authentication**
  - Create an account
  - Sign in
  - Remember me
  - Reset password via email
  - Remember me
- **Profile Page**
  - Change name
    - Avatar auto-generated from name
  - Change email
  - Email verification
  - Update password
  - Delete account
  - Recent sessions with `Log out of all other devices` functionality
- **Session management**
  - Custom database-backed session manager with encrypted payload, IP address and user agent tracking
- **Useful Middlewares**
  - `MustBeAuthenticated`
  - `RedirectIfAuthenticated`
  - `MustHaveVerifiedEmail`
- **Extra Validation Rules**
  - `Unique` Rule
  - `FailWithMessage` Generic Rule (works partially, call via function `fail_validation`)
- **View layouts**
  - `Base`
  - `App`
  - `Auth`
- **Routes**
  - Lightweight `uri()` helper inspired by Ziggy. Builds type-safe URLs from a Tempest route manifest with param substitution, query string support and `uriIs()` for active path matching
- **Svelte components**
  - Alert
  - Badge
  - Button
  - Link
  - Navbar
  - NotificationBanner
  - TextInput
  - UserAvatar

---

## Requirements

- PHP 8.5+
- Composer
- PostgreSQL, MySQL or SQLite
- Node.js + Bun (or NPM, or PNPM, or Yarn, or whatever you like to use)

---

## Getting started

```bash
git clone https://github.com/dcmxyz/tempest-luna my-project

cd my-project
```

```bash
rm -rf .git

git init
```

```bash
cp .env.example .env
```

```bash
composer install

bun install
# or
npm install
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

## Notes

- No database-level foreign key constraints
- The route manifest (`.tempest/generated/routes.ts`) is auto-generated on dev start and on every controller file change via the `tempestRoutes` Vite plugin