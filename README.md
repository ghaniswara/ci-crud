# A CodeIgniter 4 CRUD

## Features 
* Pages : 
    * View on `articles/create.php`
    * View on `articles/edit.php`
    * View on `articles/index.php`
    * View on `articles/show.php`
* REST API : 
    * GET `/api/articles` --> Return all articles
    * GET `/api/articles/:id` --> Return a single article
* MinIO for file storage (storing article JSON)
* Redis for caching (caching articles)

## Installation

1. Clone the repository
2. Run `composer install`
3. Run `docker compose up -d` to start MinIO
4. Install DBngin to setup MySQL & Redis
5. configure the `.env` file with the correct credentials for MySQL, Redis & MinIO (based on Docker Compose file)
6. Run `php spark migrate` to create the database tables
7. Run `php spark db:seed` to seed the database with some data
    1. This will create articles in database and JSON files in MinIO
8. Run `php spark serve`
9. Open `http://localhost:8080/articles` in your browser