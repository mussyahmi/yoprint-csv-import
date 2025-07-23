# YoPrint CSV Import

A Laravel-based application that allows CSV file uploads, processes them via queued jobs, and displays the upload history and imported product data.

## Features

- Drag & drop CSV upload UI (TailwindCSS)
- Asynchronous CSV processing via Laravel Queues
- Product import from CSV using `updateOrInsert`
- Status tracking: pending, processing, completed, failed
- Laravel Horizon integration for queue monitoring

## Getting Started

### Clone the repository

```bash
git clone https://github.com/mussyahmi/yoprint-csv-import.git
cd yoprint-csv-import
```

### Install dependencies

```bash
composer install
```

### Environment setup

Copy `.env.example` and update your database and Redis credentials.

```bash
cp .env.example .env
php artisan key:generate
```

### Migrate the database

```bash
php artisan migrate
```

### Start the Laravel server

```bash
php artisan serve
```

This will start the app at `http://127.0.0.1:8000`.

### Queue & Redis setup

Ensure you have Redis installed and running:

```bash
redis-server
```

Start Laravel Horizon:

```bash
php artisan horizon
```

## CSV Format

Your CSV file must contain the following headers:

```
UNIQUE_KEY, PRODUCT_TITLE, PRODUCT_DESCRIPTION, STYLE#, SANMAR_MAINFRAME_COLOR, SIZE, COLOR_NAME, PIECE_PRICE
```

## License

MIT
