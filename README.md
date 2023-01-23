# School Website - Backend Laravel

## WORK IN PROGRESS

This repository is part of the web development project of my bachelor degree in Business Computing.

## How to setup the project
### Database requirements
- Make sure your mysql database:
    - Is using `InnoDB` as the engine.
    - Has `default-row-format` set to `dynamic`.

### First launch
- run `composer install`
- Copy the `.env.config` file to `.env`.
- Fill the environment variables.
- Run `php artisan key:generate` (this will fill the `APP_KEY` environment variable).
- Run `php artisan migrate` command.
- For demo purpose you can run `php artisan db:seed --class=DemoSeeder` to populate the database.
- If you store files locally, run : `php artisan storage:link` to create a symbolic link between "public/storage" and "storage/app/public"

## How to serve the project
Run `php artisan serve`

## Ideas
- Calendar to deal with courses and formations being available multiple times (instead of updating each entry)
- Tokens must have 24h limit of validity

## Possible improvements
- Dynamic api routing
  - get folders
  - path is name of the folder with a first lowercase
  - include routes from inside the folder
- Improve seeders

## Todo
- Export: Should deleted_at be included ? If yes, should export method ask the model to include deleted ?
- User: Should delete user check the foreign keys ? Should remove the picture from storage ?
- Todo's in the source code.
- Grades:
  - Cannot update grades with a score.
  - Cannot be deleted when there's a score.

- Users : Allow to update the picture, delete the previous one.

- App access
  - Enrollment
    - Delete : Can only be deleted by its own user and when it's in PENDING status.
- 

- groups
  - Name, timestamps (to determine school year 20XX - 20YY) 
  - user_group (user_id, group_id)
  - controller allows to add new user to a group, remove, (un)subscribe the whole group to a formation or course.
  - On delete, remove all entries in user_group, maybe unsubscribe the linked users from any formation / course.
- don't allow adding the same person to the same cohort twice (via the respective custom Request)

- Courses
  - when assigning a group to a course, only create grades for students.
    
# Useful commands to generate new content
- generate a new model and related classes : `php artisan make:model Name -mfsc`
- refresh migrations : `php artisan migrate:refresh`
- reset migrations : `php artisan migrate:reset`
- generate resource : `php artisan make:resource V1\NameResource`
- generate collection : `php artisan make:resource V1\NameCollection`
- generate request : `php artisan make:request V1\Name\StoreNameRequest`
