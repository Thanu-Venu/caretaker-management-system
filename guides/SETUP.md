# SmartCare / Caretaker Management System — Setup Guide

## 1. Requirements
- PHP 8.x (works best on 8.0+)
- Apache (XAMPP/WAMP/LAMP) or Nginx
- MySQL or MariaDB
- PHP extensions commonly needed:
  - mysqli or pdo_mysql
  - mbstring
  - openssl
  - json

## 2. Get the Project
1. Download/clone the repository
2. Place it in your server root:
   - XAMPP: `htdocs/`
   - WAMP: `www/`

Example:
- `C:\xampp\htdocs\caretaker-management-system`

## 3. Create Database
1. Open phpMyAdmin
2. Create a database (example name):
   - `caretaker_db`
3. Import schema:
   - Import `database/schema.sql`
4. (Optional) Import demo data:
   - Import `database/seed.sql`

## 4. Configure Environment
This project uses environment variables.

1. Copy `.env.example` to `.env`
2. Update values for your local machine:
   - DB_HOST, DB_NAME, DB_USER, DB_PASS
   - APP_URL

## 5. Run the Project
- Start Apache + MySQL in XAMPP/WAMP
- Open:
  - `http://localhost/caretaker-management-system/`

## 6. Default Accounts (Optional)
If you import `database/seed.sql`, you can log in with:

- Admin: `admin@example.com` / `Admin@123`
- HR: `hr@example.com` / `Hr@123`
- Client: `client@example.com` / `Client@123`

(If you don’t use seed data, create accounts from your registration or admin panel.)

## 7. Recurring Jobs (If enabled)
If you use recurring payments or reminders, set a cron job to run:

- `php check_recurring.php`

See `CRON_JOBS.md` (recommended) for production scheduling and logging.

## 8. Common Errors
### DB connection failed
- Check `.env` values
- Confirm MySQL is running
- Confirm database name exists and schema is imported

### 404 / routing issues
- Ensure Apache `mod_rewrite` is enabled
- Confirm `.htaccess` is allowed:
  - `AllowOverride All`

### Permission denied (Linux)
- Ensure `logs/` and `uploads/` are writable (if used)
