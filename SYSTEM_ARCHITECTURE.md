# System Architecture — Caretaker Management System

## 1. Overview

The Caretaker Management System is a web-based platform that connects clients with caretakers for elder care, babysitting, and household services. It provides:

- **Caretaker onboarding & profile management**
- **Service discovery and booking** (clients)
- **Leave management** (caretakers / HR)
- **Payment processing** (clients / admin)
- **Role-based dashboards** for **Admin**, **HR**, **Client**, and **Caretaker**

This document describes the intended architecture, responsibilities of modules, key workflows, and non-functional requirements.

---

## 2. Architecture Style

### 2.1 Logical Architecture (Layered)

The system uses a typical PHP web architecture:

1. **Presentation Layer**
   - Server-rendered pages (PHP templates/views)
   - Static assets (CSS/JS/images)
   - Form validation (client-side JS + server-side PHP)

2. **Application Layer (Use-cases / Services)**
   - Business logic for booking, leave approvals, payments, user management
   - Authorization rules per role

3. **Data Access Layer**
   - Database connection handling
   - Queries and persistence logic
   - Transaction boundaries for multi-step operations (booking + payment, etc.)

4. **Infrastructure / Integration**
   - Email/SMS notifications (if configured)
   - Payment gateway integration (if configured)
   - File uploads (profile photos, documents)

### 2.2 Deployment Architecture (Typical)

- **Client browser** → **Web server (Apache/Nginx + PHP)** → **Database (MySQL/MariaDB)**
- Optional third-parties:
  - Payment provider (e.g., Stripe/PayPal)
  - SMTP provider for email

---

## 3. Roles & Access Model

### 3.1 Roles

- **Admin**
  - Full system access
  - User & role management
  - Reports & system configuration
- **HR**
  - Caretaker verification
  - Leave approval workflow
  - HR reporting
- **Client**
  - Browse caretakers/services
  - Create bookings
  - Make payments
  - View booking history
- **Caretaker**
  - Manage profile & availability
  - View assigned bookings
  - Request leave
  - View payment/earning info (if implemented)

### 3.2 Authorization (Recommended Pattern)

- Session-based authentication
- Role stored in session and validated on each protected route/page
- Pages organized by role area (recommended), with shared includes for guards:
  - `require_login()`
  - `require_role('admin')`, etc.

---

## 4. Major Domain Modules

### 4.1 User & Identity
**Responsibilities**
- Registration, login, logout, password reset (if implemented)
- Role assignment and profile management

**Data (typical)**
- `users`: id, name, email, password_hash, role, status, created_at
- `profiles_*`: role-specific profile tables (caretaker details, client details), or a unified profile table

### 4.2 Caretaker Management
**Responsibilities**
- Caretaker registration + HR verification
- Service categories and skills
- Availability and constraints

**Data (typical)**
- `caretakers`: user_id, bio, hourly_rate, verification_status, etc.
- `caretaker_skills`: caretaker_id, skill_id
- `services`: id, name, description, base_price (optional)

### 4.3 Booking Management
**Responsibilities**
- Creating a booking request (client)
- Matching/assigning caretaker (auto or HR/admin mediated)
- Booking lifecycle: requested → confirmed → in-progress → completed/cancelled

**Data (typical)**
- `bookings`: id, client_id, caretaker_id, service_id, start_time, end_time, status, notes
- `booking_status_history`: booking_id, status, changed_by, changed_at

### 4.4 Leave Management
**Responsibilities**
- Caretaker submits leave request
- HR reviews and approves/rejects
- Enforce “no double-booking” constraints and caretaker availability checks

**Data (typical)**
- `leave_requests`: id, caretaker_id, from_date, to_date, reason, status, reviewed_by, reviewed_at

### 4.5 Payment Processing
**Responsibilities**
- Generate invoice/amount for booking
- Accept payment (online gateway and/or offline “mark as paid”)
- Store transaction references and payment status

**Data (typical)**
- `payments`: id, booking_id, amount, currency, status, provider, provider_ref, paid_at
- `invoices` (optional): id, booking_id, subtotal, tax, total, created_at

### 4.6 Dashboards & Reporting
**Responsibilities**
- Aggregated views per role (KPIs, pending approvals, upcoming bookings)
- Reports: bookings by month, revenue, caretaker utilization, leave stats

---

## 5. Key Workflows (Sequence Summaries)

### 5.1 Caretaker Registration & Verification
1. Caretaker signs up and fills profile.
2. System marks caretaker as `pending_verification`.
3. HR reviews submitted details/documents.
4. HR sets status: `verified` or `rejected`.
5. Verified caretakers become visible/selectable for booking.

### 5.2 Booking & Payment
1. Client selects service and caretaker (or requests matching).
2. Client submits booking request (validated server-side).
3. System creates `booking` with status `requested` or `pending_payment`.
4. Payment step:
   - If online: redirect to provider and record transaction result.
   - If offline: admin/HR marks paid (if supported).
5. Booking is confirmed once payment succeeds (or policy allows pay-later).

### 5.3 Leave Request & Conflict Handling
1. Caretaker submits leave period.
2. System checks:
   - Existing leave overlaps
   - Existing confirmed bookings overlap
3. HR approves/rejects.
4. If approved:
   - Caretaker becomes unavailable for booking in that window.
   - Existing bookings may be flagged for reassignment (policy-dependent).

---

## 6. Data & Storage Design Notes

### 6.1 Referential Integrity
Use foreign keys where possible (if enabled) and consistent IDs.

### 6.2 Transactions
Use DB transactions for multi-step operations such as:
- Creating a booking + creating a payment record
- Approving leave + updating availability + marking impacted bookings

### 6.3 Auditability
Recommended:
- `created_at`, `updated_at` on all key tables
- status history tables for bookings and leave approvals

---

## 7. Security Considerations

- **Password hashing**: `password_hash()` / `password_verify()`
- **SQL Injection protection**: prepared statements (PDO or MySQLi prepared queries)
- **CSRF protection** for form posts (recommended)
- **Access control**: deny-by-default for routes/pages; enforce role checks
- **Input validation**:
  - Server-side validation is mandatory
  - Sanitize outputs with escaping to prevent XSS
- **File uploads**:
  - Validate MIME type and extension
  - Store outside web root if possible
  - Randomize filenames

---

## 8. Observability & Operations (Recommended)

- Error logging to file (different levels for dev vs prod)
- Basic request tracing via correlation ID (optional)
- Admin page for viewing recent errors (optional, protected)

---

## 9. Folder/Module Structure (To Be Aligned With Repo)

> Update this section to match the actual repository structure once confirmed.

Recommended structure for a PHP app:

- `public/` (web root)
  - `index.php`
  - `assets/` (css/js/images)
- `app/`
  - `Controllers/`
  - `Services/`
  - `Models/`
  - `Views/`
- `config/`
  - `database.php`
  - `app.php`
- `includes/` (legacy shared PHP includes, if used)
  - `auth.php`, `db.php`, `helpers.php`
- `sql/` (schema, seed files)
- `docs/`
  - `SYSTEM_ARCHITECTURE.md` (this file)

---

## 10. Integration Points

- **Database**: MySQL/MariaDB
- **Email**: SMTP (optional)
- **Payment provider**: depends on implementation (optional)

Define configuration via environment variables or a single `config` file:
- DB host, port, name, user, password
- Payment keys
- SMTP credentials

---

## 11. Non-Functional Requirements

- **Availability**: 99%+ for hosted environment (target)
- **Performance**:
  - Typical page loads < 1–2s on standard hosting
  - Indexed queries for booking searches and dashboard aggregates
- **Scalability**:
  - Start as monolith; can split modules later if needed
- **Maintainability**:
  - Consistent coding standards
  - Clear separation: controllers/services/models
- **Privacy**:
  - Limit exposure of caretaker/client personal information
  - Protect documents and PII

---

## 12. Future Enhancements (Optional)

- Automated caretaker matching based on:
  - availability, location, rating, skills
- Notifications:
  - email/SMS for booking confirmations and leave status updates
- Role-based API endpoints for a mobile app
- Admin analytics dashboard (charts)

---
