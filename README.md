# Caretaker Management System

A web-based caretaker management system that connects clients with caretakers for elder care, babysitting, and household services.

Core features include caretaker registration, service booking, leave management, payment processing, and role-based dashboards for **Admin**, **HR**, and **Clients** (and caretakers where applicable).

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Roles](#project-roles)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Local Setup](#local-setup)
  - [Database Setup](#database-setup)
  - [Run the App](#run-the-app)
- [Configuration](#configuration)
- [Suggested Folder Structure](#suggested-folder-structure)
- [Security Notes](#security-notes)
- [Contributing](#contributing)
- [License](#license)

---

## Features

### Client
- Browse available caretakers/services
- Create and manage bookings
- Payment processing for bookings (implementation-dependent)
- View booking history and status

### Caretaker
- Register and manage caretaker profile
- View assigned bookings
- Request leave

### HR
- Review caretaker registrations (verify/reject)
- Manage caretaker leave requests (approve/reject)
- HR dashboard for pending actions

### Admin
- Manage users and roles
- System overview dashboard
- Reporting (implementation-dependent)

---

## Tech Stack

- **Backend:** PHP
- **Frontend:** HTML, CSS, JavaScript
- **Database:** MySQL / MariaDB (recommended)
- **Server:** Apache (XAMPP/LAMP/WAMP) or Nginx + PHP-FPM

Language composition (GitHub): PHP (68.2%), CSS (23.5%), JavaScript (8.2%)

---

## Project Roles

Typical roles in the system:
- `admin`
- `hr`
- `client`
- `caretaker`

> Note: Exact role names and permissions may vary depending on the implementation in this repository.

---

## Getting Started

### Prerequisites

- PHP 8.x (or the version your hosting environment supports)
- MySQL/MariaDB
- Web server (Apache via XAMPP/WAMP/LAMP recommended for local development)
- Git

### Local Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/Thanu-Venu/caretaker-management-system.git
   cd caretaker-management-system
   ```

2. Place the project in your web server root:
   - XAMPP (Windows): `C:\xampp\htdocs\caretaker-management-system`
   - XAMPP (macOS): `/Applications/XAMPP/htdocs/caretaker-management-system`
   - Linux (Apache): `/var/www/html/caretaker-management-system`

### Database Setup

Because repository structures vary, follow one of these options:

#### Option A: SQL file provided in repo
- Look for a folder such as `sql/`, `database/`, or a file like `schema.sql`.
- Import it into MySQL using phpMyAdmin or the MySQL CLI.

#### Option B: No SQL file found
- Create a database (example name: `caretaker_db`).
- Add the required tables for:
  - users & roles
  - caretaker profiles
  - services
  - bookings
  - leave requests
  - payments

> Tip: If you paste your current table schema (or upload a SQL dump), we can document it cleanly in `/docs` and standardize setup.

### Run the App

- Start Apache + MySQL (XAMPP/WAMP/LAMP)
- Open in browser:
  - `http://localhost/caretaker-management-system/`

---

## Configuration

The project usually needs:
- DB host, username, password, database name
- Optional email (SMTP) settings
- Optional payment provider keys

Search for a config file such as:
- `config.php`
- `db.php`
- `database.php`
- `.env` (if used)

> If you share your repo’s config file name and location, we can update this README with exact steps.

---

## Suggested Folder Structure

This is a recommended structure (your repo may differ):

- `public/` — web root (index.php, assets)
- `assets/` — CSS/JS/images
- `includes/` — shared PHP includes (db/auth/helpers)
- `admin/` — admin dashboard pages
- `hr/` — HR dashboard pages
- `client/` — client pages
- `caretaker/` — caretaker pages
- `sql/` — DB schema and seed scripts
- `docs/` — documentation
  - `SYSTEM_ARCHITECTURE.md`
  - `FUTURE_FEATURES.md`

---

## Security Notes

Recommended best practices (especially for production):
- Use `password_hash()` and `password_verify()`
- Use prepared statements (PDO or MySQLi prepared queries)
- Add CSRF protection for forms
- Validate all inputs server-side
- Escape outputs to prevent XSS
- Restrict access to role-based pages via session checks

---

## Contributing

1. Fork the repository
2. Create a feature branch:
   ```bash
   git checkout -b feature/my-change
   ```
3. Commit your changes:
   ```bash
   git commit -m "Add my change"
   ```
4. Push to your fork and open a Pull Request

---

## License

Add a license file (e.g., `LICENSE`) to define usage and contribution terms.
If this is an academic or internal project, state the intended use here.
