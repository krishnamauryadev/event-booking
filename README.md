
# Event Booking System - Laravel (Skeleton)

This repository is a **skeleton** Laravel backend for an Event Booking System.
It contains migrations, models, controllers, middleware, services, traits,
notifications, factories, seeders, tests and a Postman collection — ready to be
copied into a real Laravel project or used as a starting point.

## Quick setup (local)

1. Create a fresh Laravel project (Laravel 10+ recommended):
   ```bash
   composer create-project laravel/laravel event-booking
   cd event-booking
   ```

2. Copy the contents of this skeleton into your Laravel project's root (merge into repo). Alternatively, unzip and replace the matching files in your Laravel app:
   ```bash
   unzip event-booking-laravel.zip -d ./
   ```

3. Install dependencies and Sanctum:
   ```bash
   composer install
   ```

4. Configure `.env` (database, mail driver `log` for dev, QUEUE_CONNECTION=database or sync)

5. Create queue table and run migrations & seeders:
   ```bash

   php artisan migrate
   php artisan db:seed
   ```

7. Run tests:
   ```bash
   php artisan test
   ```


