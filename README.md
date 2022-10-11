
## BVG API backend

this project is mid-end that serves flutter as frontend from SAP as backend

## Installation
- setup server to point to /public directory
- ENV setup
  - setup App URL / IP
  - set APP_ENV to production
  - set APP_DEBUG to false
- copy .env.example to .env
- add database variables
- Queue Setup
  - Windows -> run `php artisan queue:work`
  - linux -> run same command but with supervisor
- Database Setup
  - Run `php artisan db:init`
    - this will drop all tables
    - seed role,permission and superadmin users

## Features / Modules
- Multi-Tenancy Project with Seperate DB
- Laravel Auth Sanctum for SPA Stateful Authentication
- Custom Database Helpers
- Config Undo
  - Every new project creation changes affects config, a backup config file is simultaneosly being maintained in case config files get corrupted.