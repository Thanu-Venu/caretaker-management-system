# Installation Guide — Caretaker Management System

This guide explains how to set up and run the Caretaker Management System locally (recommended for development/testing) and includes notes for deploying to a hosted server.

> Note: Because repository structures differ (and I can’t read your repo files from here), some paths and filenames (like the DB config file) may need minor adjustments. If you paste your repo root tree and the DB config file name, I can tailor this guide exactly to your project.

---

## 1. Requirements

### 1.1 Software
- **PHP:** 8.x recommended (7.4+ usually works for many projects)
- **Web server:** Apache (XAMPP/WAMP/LAMP) or Nginx + PHP-FPM
- **Database:** MySQL or MariaDB
- **Git** (optional but recommended)

### 1.2 PHP Extensions (commonly needed)
Ensure these are enabled:
- `mysqli` or `pdo_mysql`
- `mbstring`
- `openssl`
- `curl`
- `json` (usually enabled by default)

---

## 2. Local Installation (XAMPP / WAMP / MAMP)

### 2.1 Clone / Download the Project
Option A — Git clone:
```bash
git clone https://github.com/Thanu-Venu/caretaker-management-system.git
cd caretaker-management-system
```

Option B — Download ZIP:
1. Download the ZIP from GitHub
2. Extract it

### 2.2 Move Project into Web Root

#### XAMPP (Windows)
Move the folder into:
- `C:\xampp\htdocs\caretaker-management-system`

#### XAMPP (macOS)
Move the folder into:
- `/Applications/XAMPP/htdocs/caretaker-management-system`

#### Linux (Apache default)
Move the folder into:
- `/var/www/html/caretaker-management-system`

---

## 3. Database Setup

### 3.1 Start Database
Start **MySQL/MariaDB** from your stack manager:
- XAMPP Control Panel → Start **MySQL**
- WAMP → Start services
- MAMP → Start servers

### 3.2 Create Database
Using phpMyAdmin (common):
1. Open:
   - `http://localhost/phpmyadmin`
2. Create a database, for example:
   - `caretaker_management` (name can vary)

### 3.3 Import SQL Schema (If Provided)
Check your repository for any of these:
- `sql/`
- `database/`
- `db/`
- `schema.sql`
- `*.sql` dump files

If a SQL file exists:
1. phpMyAdmin → select your DB
2. **Import** → select the `.sql` file → **Go**

### 3.4 If No SQL File Exists
If your repo does not include a schema export, you must obtain one from:
- a teammate’s local DB export, or
- the hosting server DB, or
- your project documentation.

Minimum tables typically required:
- `users` (auth + roles)
- `caretakers` / caretaker profile data
- `clients` (if separate profile table)
- `services` (categories/types of care)
- `bookings`
- `leave_requests`
- `payments` (if payment is implemented)

---

## 4. Configure Database Connection (Critical Step)

Locate your DB config file. Common filenames include:
- `config.php`
- `db.php`
- `db_connect.php`
- `database.php`
- `includes/db.php`
- `config/database.php`

Update values such as:
- `DB_HOST` (often `localhost`)
- `DB_USER` (often `root`)
- `DB_PASS` (often empty in XAMPP)
- `DB_NAME` (the database you created)

Example snippet (for reference only):
```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "caretaker_management";
```

> If you paste your existing DB config file contents here, I can tell you exactly what to change and where.

---

## 5. Run the Application

1. Start **Apache** and **MySQL**
2. Open your browser:
   - `http://localhost/caretaker-management-system/`

If your app has a specific entry point (e.g., `/public/index.php`), try:
- `http://localhost/caretaker-management-system/public/`

---

## 6. Default Accounts (If Seeded)

Some projects include seed data with test accounts (admin/hr/client).
Check:
- SQL seed files
- a `README.md` section
- hardcoded credentials in a setup page

If you have default credentials, document them here:

- Admin:
  - Email:
  - Password:
- HR:
  - Email:
  - Password:
- Client:
  - Email:
  - Password:
- Caretaker:
  - Email:
  - Password:

---

## 7. Troubleshooting

### 7.1 “Page not found” / 404
- Confirm the folder is inside your web root (`htdocs` or equivalent)
- Confirm the URL matches the folder name exactly
- Check whether the app uses a `public/` directory as web root

### 7.2 Database Connection Errors
- Verify DB name, username, password
- Ensure MySQL service is running
- Confirm the DB host is correct (`localhost` vs `127.0.0.1`)
- Verify the required tables exist (imported successfully)

### 7.3 Login Not Working
- Confirm seeded users exist in the DB
- Verify password hashing method matches stored data
- Check session configuration and PHP errors

### 7.4 Blank Page / 500 Error
Enable error reporting (development only):

```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

Check logs:
- XAMPP: `apache/logs/error.log`
- Linux Apache: `/var/log/apache2/error.log`
- Nginx: `/var/log/nginx/error.log`

---

## 8. Deployment Notes (Hosting / Production)

### 8.1 Recommended Steps
- Use a dedicated DB user (not `root`)
- Set strong DB passwords
- Disable `display_errors` in production
- Enforce HTTPS (TLS)
- Restrict access to admin panels

### 8.2 File Permissions
- Ensure the web server can read the project files
- For uploads, ensure write permissions only for the upload directory

### 8.3 Environment Configuration
If the project supports environment variables, prefer them over hardcoding secrets.

---

## 9. Next Improvements (Optional)
- Add `.env.example` and use environment variables
- Add a single `docs/` section describing:
  - DB schema
  - roles/permissions
  - main workflows
- Add a setup script to validate config and DB connectivity

---

## 10. Support

If you share:
- your repo directory tree (root + main folders),
- the DB config file path/name,
- and your SQL schema file name (if any),

I can update this guide with exact, copy-pasteable steps specific to your codebase.
