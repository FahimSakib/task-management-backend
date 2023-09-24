# Task Management Backend
**Technology used:**
- Laravel
- Laravel Sanctum
- Laravel Notification
- Database: MySQL

## Install project
*Clone project from git*

`git clone https://github.com/FahimSakib/task-management-backend.git`

*Install composer*

`composer install`

*Copy .env.example and make .env file*

`cp .env.example .env`

*Generate app key*

`php artisan key:generate`

*Run migrations*

`php artisan migrate`

*Setup mail config*

*Setup your mail config in .env file. **Note** you can use my mail config that already added to .env.example file*

*Run project*

`php artisan serve`

**Note: Consider running this project by `php artisan serve` because the backend and frontend should be in the same top-level-domain, and for local, this will be `localhost` for both with different port.**

*Start jobs*

`php artisan queue:work`
