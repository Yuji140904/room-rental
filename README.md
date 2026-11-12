# Room Rental

A Laravel 12 app for managing rooms, tenants and monthly bills for a small
rental business. The admin creates tenant accounts; tenants log in with their
phone number and see only their own bills.

## Requirements

- PHP 8.2 or newer, with the `pdo_pgsql`, `intl` and `sqlite3` extensions
- Composer
- Node.js
- PostgreSQL 16 or newer

## Setup

```
composer install
npm install
npm run build
copy .env.example .env
php artisan key:generate
```

Create the database and a user in PostgreSQL:

```sql
CREATE DATABASE room_rental;
CREATE USER rental_user WITH PASSWORD 'choose-a-password';
ALTER DATABASE room_rental OWNER TO rental_user;
```

Put the same password in `.env` as `DB_PASSWORD`, then:

```
php artisan migrate --seed
php artisan serve
```

The seeder creates an admin account and six sample rooms. Change the admin
password before using the app for real.

## Roles

- **admin**: manages rooms and tenants
- **tenant**: logs in with a phone number and sees only their own bills

Tenants cannot register themselves; the admin creates their accounts.

## Tests

```
php artisan test
```

## Progress

- Week 1: login with phone number, roles, rooms and tenants screens, bill policy
- Week 2: bills, payments, tenant dashboard, deployment (in progress)