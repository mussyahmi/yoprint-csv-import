# Laravel CSV Importer with Queue Processing

This project is a Laravel-based application that allows users to upload CSV files through a drag-and-drop UI. The files are queued for background processing via Laravel Jobs and then inserted into a database. It supports real-time upload history with status and time tracking.

---

## 🚀 Features

- Drag & Drop CSV file upload with real-time feedback
- Upload history table (sortable and humanized time display)
- Laravel Queue with Horizon for async processing
- Auto-creates or updates products from CSV
- Supports UTF-8 cleanup during import
- Error handling and logging per file

---

## 🛠️ Tech Stack

- Laravel 10+
- TailwindCSS (CDN)
- Axios
- MySQL / SQLite
- Laravel Horizon (optional)

---

## 📦 Setup Instructions

```bash
git clone https://github.com/mussyahmi/yoprint-csv-import.git
cd yoprint-csv-import
composer install
cp .env.example .env
php artisan key:generate
```

Set your DB credentials in `.env`.

```bash
php artisan migrate
```

(Optional: queue with Horizon)
```bash
php artisan horizon
```

---

## 📁 Uploading Files

- Accepts `.csv` or `.txt`
- Uses drag-and-drop or manual selection
- Files are stored in `storage/app/uploads`

---

## 🧠 CSV Format

| UNIQUE_KEY | PRODUCT_TITLE | PRODUCT_DESCRIPTION | STYLE# | SANMAR_MAINFRAME_COLOR | SIZE | COLOR_NAME | PIECE_PRICE |
|------------|----------------|---------------------|--------|--------------------------|------|-------------|-------------|
| ...        | ...            | ...                 | ...    | ...                      | ...  | ...         | ...         |

---

## 🖼 Example

The `upload.blade.php` provides a simple UI with:
- Dropzone file input
- File preview
- Upload table with time, file name, and status

---

## 🧪 Commands

Run the queue worker:

```bash
php artisan queue:work
```

Run Horizon (optional):

```bash
php artisan horizon
```



---

## 📜 License

MIT — feel free to use, fork, or contribute.

## Queue & Horizon Setup

This project uses Laravel Horizon for queue management. Horizon requires Redis to be running.

### Requirements

- Redis installed on your machine

### Start Redis

#### macOS (Homebrew)
```bash
brew services start redis
```

#### Ubuntu/Debian
```bash
sudo service redis-server start
```

#### Windows
Use [WSL](https://learn.microsoft.com/en-us/windows/wsl/) or install Redis manually from [https://redis.io](https://redis.io)

### Start the Laravel Queue Worker

```bash
php artisan horizon
```

Or monitor via browser:

```
http://localhost:8000/horizon
```

> ⚠️ Redis must be running **before uploading CSV files** or your jobs will fail silently.
