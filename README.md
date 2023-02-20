# EAFC Web Development Exam Project Backend

## Context 

This repository is related to my Web Development Exam from my bachelor degree in Business Computing, 2nd year.
The archived repository for the exam can be found [here](https://github.com/PinsonJulien/EAFC-Web-Development-Exam-Project-Backend-Archive)

You can consult the *scope statement* (in French) in the `scope.pdf` file at root.

I chose to use **Laravel 9**, scaffolded as a **REST api**.
The usage of session being in the scope statement, the api routes are located in the `routes/web.php`.
This means that you're required to refresh a CSRF cookie and include its value in the header parameter `X-XSRF-TOKEN` for each POST/PUT/PATCH/DELETE requests.
CSRF cookies can be obtained using the route : `/sanctum/csrf-cookie`

## Minimal requirements

- PHP 8.0
- MySQL 5.7.36
- Composer 2.3.10

### Database configuration

Make sure your mysql database:
- Is using `InnoDB` as the engine.
- Has `default-row-format` set to `dynamic`.

## Initial setup

- Clone this repository
- In `cd EAFC-Web-Development-Exam-Project-Backend`
- Run `composer install`
- Copy the `.env.example` file to `.env` and replace environment variables that do not fit your database configuration.
- Make the sure the .env has `FILESYSTEM_DISK=public` this will ensure the files are properly saved.
- Make sure the `FRONTEND_URL` .env variable matches `http://127.0.0.1:5000/`
- Run `php artisan key:generate` (this will fill the **APP_KEY** .env variable).
- Run `php artisan storage:link` to create a **symbolic link** between *public/storage* and *storage/app/public*
- Run `php artisan migrate` command to generate all the tables.

## Prepare for the demo

- Run `php artisan db:seed --class=DemoSeeder` to populate the database.
- Each SiteRole has an initial User, you can log yourself using these credentials (email ; password)
  - Guest: **guest@site.com** ; **guest**
  - User: **user@site.com** ; **user**
  - Secretary: **secretary@site.com** ; **secretary**
  - Administrator: **administrator@site.com** ; **administrator**
  - Banned: **banned@site.com** ; **banned**

## How to serve the application

Run `php artisan serve`

## Contribution tools

### PHP Documentor

Available in `/docs/app`

To regenerate the document, run `php phpDocumentor.phar -d app -t docs/app`

### Postman collection

Developers can import the `postman_collection.json` at the root of the project if they want to test this application without a frontend.
The CSRF token is automatically set in pre-request.

### Seeding for all the environments

- Demo : `php artisan db:seed --class=DemoSeeder`
- Development : `php artisan db:seed --class=DevelopmentSeeder`
- Production : `php artisan db:seed --class=ProductionSeeder`

### Useful commands to work with

- generate a new model and related classes : `php artisan make:model Name -mfsc`
- refresh migrations : `php artisan migrate:refresh`
- reset migrations : `php artisan migrate:reset`
- generate resource : `php artisan make:resource V1\Name\NameResource`
- generate collection : `php artisan make:resource V1\Name\NameCollection`
- generate request : `php artisan make:request V1\Name\StoreNameRequest`
- generate policy : `php artisan make:policy NamePolicy --model=Name`
- generate middleware : `php artisan make:middleware NameMiddleware`

## Project evolutions

### Ideas

- Calendar to deal with courses and formations being available multiple times (instead of updating the formation start / end dates)

### Possible improvements

Improvements to polish the application quality.
This list contains thoughts of improvement that came across when working on the project.

- Routes
  - Dynamic api routing
    - get folders
    - path is name of the folder with a first lowercase
    - include routes from inside the folder
  - Make the /api routes. Laravel Passport will however not be able to work with sessions. They should be replaced with tokens
- Seeders
  - Improve seeders with relational generations.
- Controllers
  - Reduce code redundancy
  - Remove waterfall actions to model observers
- Models
  - Write observers
    - Some could return errors whenever they're locked for example. These errors will be handled by the controller.
- Roles
  - The authorization should be more dynamic and allow granular field by field limitations.
- Authorization
  - roles
    - Make a more dynamic and allow granular field by field limitation. Policies will consult the database to check the user capabilities.
  - token
    - If they're implemented, they must have a 24h limit of validity.
- Documentation
  - Provide a clean OpenAPI.yaml document.
- Tests
  - Write unit tests for every route.
