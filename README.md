# Blog CMS

Laravel-based CMS with Filament Admin Panel and Livewire frontend.

## Requirements

- PHP 8.2+
- Composer
- Node.js + npm
- MySQL

## Installation

```bash
git clone git@github.com:Dembalua/blog-cms.git
cd blog-cms
composer install
cp .env.example .env
php artisan key:generate
```

Create a MySQL database, then edit `.env` (see below) and run:

```bash
php artisan migrate --seed
npm install
npm run build
php artisan storage:link
```

Admin login after seeding: `admin@example.com` / `Password123!`

> ⚠️ **Local demo credentials only.** Change the password (or create a new admin
> user and delete the seeded one) before deploying to any environment that is
> reachable from the internet.

Dev data (posts, categories etc.) is only seeded when `APP_ENV=local`.

## Environment variables

| Variable | Description |
|---|---|
| `APP_NAME` | Blog name |
| `APP_URL` | Local or production URL |
| `DB_DATABASE` | Database name |
| `DB_USERNAME` | Database user |
| `DB_PASSWORD` | Database password |
| `MFA_BRAND_NAME` | Brand name shown in 2FA prompts |
| `LOG_MAIL_TO` | Email address for error notifications |
| `MAIL_FROM_ADDRESS` | Sender address for outgoing emails |

## License

Released under the MIT License — see [LICENSE](LICENSE).
