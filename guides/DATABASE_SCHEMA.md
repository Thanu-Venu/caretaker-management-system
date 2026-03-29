# Database Schema — Caretaker Management System

This document describes a **recommended/typical** database schema for the Caretaker Management System (PHP + MySQL/MariaDB). It is designed to support:

- Users and role-based access (Admin / HR / Client / Caretaker)
- Caretaker registration and verification
- Services (elder care, babysitting, household services)
- Booking lifecycle management
- Leave requests and conflict checks
- Payments and basic invoicing
- Audit/status history

> Important: This schema is provided as a **documentation blueprint**. If your project already has tables/columns, paste your current SQL dump (or table list) and I’ll rewrite this document to match your real schema exactly.

---

## 1. Conventions

### 1.1 General
- Use `InnoDB` for foreign keys and transactions.
- Store timestamps in UTC where possible.
- Use `created_at` and `updated_at` on key tables.

### 1.2 Enumerations (Recommended)
Use `ENUM` only if you’re comfortable with schema migrations; otherwise use `VARCHAR` with constraints at the application layer.

Common status values:
- Booking: `requested`, `confirmed`, `in_progress`, `completed`, `cancelled`
- Leave: `pending`, `approved`, `rejected`
- Payment: `pending`, `paid`, `failed`, `refunded`
- Caretaker verification: `pending`, `verified`, `rejected`

---

## 2. Core Tables

### 2.1 `users`
Stores authentication identity and global role.

**Purpose**
- Login credentials
- Role assignment
- Account status

**Suggested columns**
- `id` (PK)
- `full_name`
- `email` (unique)
- `phone` (optional)
- `password_hash`
- `role` (`admin`, `hr`, `client`, `caretaker`)
- `status` (`active`, `disabled`, `pending`)
- `last_login_at` (nullable)
- `created_at`
- `updated_at`

---

## 3. Profiles

### 3.1 `clients`
Client-specific profile info.

**Suggested columns**
- `id` (PK)
- `user_id` (FK → users.id, unique)
- `address_line1`, `address_line2`, `city`, `state`, `postal_code` (optional)
- `notes` (optional)
- `created_at`
- `updated_at`

### 3.2 `caretakers`
Caretaker profile and verification status.

**Suggested columns**
- `id` (PK)
- `user_id` (FK → users.id, unique)
- `bio`
- `gender` (optional)
- `date_of_birth` (optional)
- `hourly_rate` (DECIMAL)
- `verification_status` (`pending`, `verified`, `rejected`)
- `verified_by` (FK → users.id, nullable; HR/admin)
- `verified_at` (nullable)
- `profile_photo_path` (optional)
- `created_at`
- `updated_at`

---

## 4. Services & Skills

### 4.1 `services`
Defines service categories offered (elder care, babysitting, etc.).

**Suggested columns**
- `id` (PK)
- `name` (unique-ish)
- `description`
- `active` (boolean)
- `created_at`
- `updated_at`

### 4.2 `caretaker_services`
Many-to-many mapping between caretakers and services.

**Suggested columns**
- `caretaker_id` (FK → caretakers.id)
- `service_id` (FK → services.id)
- `created_at`

**Keys**
- Composite PK: (`caretaker_id`, `service_id`)

---

## 5. Booking

### 5.1 `bookings`
Primary booking record.

**Suggested columns**
- `id` (PK)
- `client_id` (FK → clients.id)
- `caretaker_id` (FK → caretakers.id, nullable if not assigned yet)
- `service_id` (FK → services.id)
- `start_datetime`
- `end_datetime`
- `address_line1`, `address_line2`, `city`, `state`, `postal_code` (optional)
- `status` (`requested`, `confirmed`, `in_progress`, `completed`, `cancelled`)
- `notes` (optional)
- `created_by` (FK → users.id) — typically client
- `created_at`
- `updated_at`

**Indexes (recommended)**
- `(client_id, created_at)`
- `(caretaker_id, start_datetime)`
- `(status, start_datetime)`

### 5.2 `booking_status_history` (Recommended)
Audit changes to booking status.

**Suggested columns**
- `id` (PK)
- `booking_id` (FK → bookings.id)
- `old_status`
- `new_status`
- `changed_by` (FK → users.id)
- `changed_at`
- `comment` (optional)

---

## 6. Leave Management

### 6.1 `leave_requests`
Caretaker leave requests.

**Suggested columns**
- `id` (PK)
- `caretaker_id` (FK → caretakers.id)
- `from_date`
- `to_date`
- `reason` (optional)
- `status` (`pending`, `approved`, `rejected`)
- `reviewed_by` (FK → users.id, nullable)
- `reviewed_at` (nullable)
- `created_at`
- `updated_at`

**Indexes (recommended)**
- `(caretaker_id, from_date, to_date)`
- `(status, created_at)`

### 6.2 Conflict Rule (App-Level)
When creating:
- a booking, or
- a leave request

Check overlap:
- Approved leave overlaps requested booking window
- Confirmed/in-progress bookings overlap requested leave window

---

## 7. Payments & Invoices

### 7.1 `payments`
Tracks payment lifecycle per booking.

**Suggested columns**
- `id` (PK)
- `booking_id` (FK → bookings.id, unique if 1 payment per booking)
- `amount` (DECIMAL)
- `currency` (e.g., `LKR`, `USD`)
- `status` (`pending`, `paid`, `failed`, `refunded`)
- `provider` (e.g., `stripe`, `paypal`, `manual`)
- `provider_reference` (nullable)
- `paid_at` (nullable)
- `created_at`
- `updated_at`

### 7.2 `invoices` (Optional)
If you want invoice numbers and PDF generation.

**Suggested columns**
- `id` (PK)
- `booking_id` (FK → bookings.id, unique)
- `invoice_number` (unique)
- `subtotal`
- `tax`
- `total`
- `issued_at`
- `created_at`

---

## 8. Notifications (Optional)

### 8.1 `notifications`
If implementing in-app notifications.

**Suggested columns**
- `id` (PK)
- `user_id` (FK → users.id)
- `type` (e.g., `booking_confirmed`)
- `title`
- `message`
- `link` (optional)
- `is_read` (boolean)
- `created_at`

---

## 9. Files / Uploads (Optional)

### 9.1 `uploads`
Track uploaded caretaker documents and profile images.

**Suggested columns**
- `id` (PK)
- `user_id` (FK → users.id)
- `category` (e.g., `profile_photo`, `id_document`, `certificate`)
- `path`
- `original_filename`
- `mime_type`
- `size_bytes`
- `uploaded_at`

---

## 10. Minimal SQL (Illustrative Only)

> This is a **minimal** sample to illustrate structure. Adjust types/lengths to match your implementation.

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  phone VARCHAR(30),
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(30) NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'active',
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE clients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  address_line1 VARCHAR(190),
  address_line2 VARCHAR(190),
  city VARCHAR(100),
  state VARCHAR(100),
  postal_code VARCHAR(20),
  notes TEXT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_clients_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE caretakers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  bio TEXT,
  hourly_rate DECIMAL(10,2) NOT NULL DEFAULT 0,
  verification_status VARCHAR(30) NOT NULL DEFAULT 'pending',
  verified_by INT NULL,
  verified_at DATETIME NULL,
  profile_photo_path VARCHAR(255),
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_caretakers_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_caretakers_verified_by FOREIGN KEY (verified_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  description TEXT,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE caretaker_services (
  caretaker_id INT NOT NULL,
  service_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (caretaker_id, service_id),
  CONSTRAINT fk_cs_caretaker FOREIGN KEY (caretaker_id) REFERENCES caretakers(id),
  CONSTRAINT fk_cs_service FOREIGN KEY (service_id) REFERENCES services(id)
) ENGINE=InnoDB;

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  caretaker_id INT NULL,
  service_id INT NOT NULL,
  start_datetime DATETIME NOT NULL,
  end_datetime DATETIME NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'requested',
  notes TEXT,
  created_by INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_bookings_client FOREIGN KEY (client_id) REFERENCES clients(id),
  CONSTRAINT fk_bookings_caretaker FOREIGN KEY (caretaker_id) REFERENCES caretakers(id),
  CONSTRAINT fk_bookings_service FOREIGN KEY (service_id) REFERENCES services(id),
  CONSTRAINT fk_bookings_created_by FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE leave_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  caretaker_id INT NOT NULL,
  from_date DATE NOT NULL,
  to_date DATE NOT NULL,
  reason TEXT,
  status VARCHAR(30) NOT NULL DEFAULT 'pending',
  reviewed_by INT NULL,
  reviewed_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_leave_caretaker FOREIGN KEY (caretaker_id) REFERENCES caretakers(id),
  CONSTRAINT fk_leave_reviewed_by FOREIGN KEY (reviewed_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL UNIQUE,
  amount DECIMAL(10,2) NOT NULL,
  currency VARCHAR(10) NOT NULL DEFAULT 'LKR',
  status VARCHAR(30) NOT NULL DEFAULT 'pending',
  provider VARCHAR(30) NOT NULL DEFAULT 'manual',
  provider_reference VARCHAR(190),
  paid_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_payments_booking FOREIGN KEY (booking_id) REFERENCES bookings(id)
) ENGINE=InnoDB;
```

---

## 11. What I Need to Make This 100% Accurate

If you want this doc to match your **actual** database, paste either:
- your `*.sql` export file, or
- the output of `SHOW TABLES;` plus `SHOW CREATE TABLE <table>;` for each table.

Then I’ll rewrite this file to reflect the real table/column names and relationships used in your codebase.
