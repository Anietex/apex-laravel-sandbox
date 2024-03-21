## Getting Started

These instructions will get your copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

What things you need to install the software and how to install them:

- PHP >= 8.1
- Composer
- Laravel >= 10.x
- A database system (e.g., MySQL, PostgreSQL)

### Installation

A step-by-step series of examples that tell you how to get a development environment running.

Clone the repository:

```bash
git clone https://github.com/Anietex/apex-laravel-sandbox.git
cd apex-laravel-sandbox
```

Install the dependencies:

```bash
composer install
```

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```
Update the `.env` file with your database credentials.


Generate the application key:

```bash
php artisan key:generate
```

Run the database migrations and seed the database with the default data:

```bash
php artisan migrate --seed
```


Setup passport:

```bash
php artisan passport:keys
```

Start the development server:

```bash
php artisan serve
```

You can now access the server at http://localhost:8000


## API Documentation:

[Postman Documentation](https://documenter.getpostman.com/view/6340830/2sA35A6jVX)


### Admin User Credentials

Email: `admin@apex.test`

password: `password`



## Running the tests
To run the tests, run:

```bash
php artisan test
```


