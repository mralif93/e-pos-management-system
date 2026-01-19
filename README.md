# e-POS (Point of Sale System)

This is a Point of Sale (POS) system built with [Laravel](https://laravel.com). This guide will help you set up the project on your local machine.

## Prerequisites

Before you begin, ensure you have the following installed on your machine:

-   [PHP](https://www.php.net/) (version 8.2 or higher)
-   [Composer](https://getcomposer.org/)
-   [Node.js](https://nodejs.org/) and NPM

## Installation

Follow these steps to initialize the project:

### 1. Clone the Repository

If you haven't already, ensure you have the project files locally.

### 2. Install PHP Dependencies

Install the project's backend dependencies using Composer:

```bash
composer install
```

### 3. Environment Configuration

Copy the example environment file to create your local `.env` file:

```bash
cp .env.example .env
```

Open the `.env` file and configure your environment variables, specifically the database connection. By default, it is configured to use SQLite:

```ini
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# ...
```

### 4. Generate Application Key

Generate the unique application key for your instance:

```bash
php artisan key:generate
```

### 5. Run Database Migrations

Create the necessary database tables (ensure your database file exists if using SQLite, usually `database/database.sqlite`):

```bash
# Create SQLite file if it doesn't exist (Mac/Linux)
touch database/database.sqlite

# Run migrations
php artisan migrate
```

### 6. Install Frontend Dependencies

Install the JavaScript dependencies:

```bash
npm install
```

### 7. Build Frontend Assets

Compile the assets (Vite + Tailwind CSS):

```bash
# For development (hot module replacement)
npm run dev

# OR for production build
npm run build
```

## Running the Application

To start the local development server:

```bash
php artisan serve
```

The application will be accessible at `http://127.0.0.1:8000`.

## Additional Commands

-   **Run Tests**:
    ```bash
    php artisan test
    ```
-   **Run Linter**:
    ```bash
    ./vendor/bin/pint
    ```
