# FAQ — Caretaker Management System

Frequently asked questions for users, admins/HR, and developers working on the Caretaker Management System.

---

## General

### 1) What is this system?
A web-based caretaker management system that connects clients with caretakers for elder care, babysitting, and household services. It includes caretaker registration, service booking, leave management, payment processing, and role-based dashboards (Admin, HR, Client, and Caretaker where applicable).

---

### 2) Who can use the system?
Typical roles are:
- **Admin** — manages the whole system
- **HR** — verifies caretakers and manages leave
- **Client** — books services and makes payments
- **Caretaker** — provides services, manages profile and leave

---

### 3) Do clients pick a caretaker directly?
This depends on how your project is configured:
- **Client-selected**: the client chooses a caretaker while booking.
- **HR/Admin-assigned**: the client requests a service, and HR/Admin assigns a caretaker later.
- **Auto-matching** (future): system suggests a caretaker automatically.

---

### 4) Can a caretaker be booked before HR verifies them?
Recommended: **No**. A caretaker should be visible/bookable only after their verification status is `verified`.

---

## Booking & Scheduling

### 5) What happens if a caretaker is already booked at that time?
The system should prevent double-booking:
- If a caretaker has an overlapping **confirmed** or **in-progress** booking, the new booking should be blocked (or require reassignment).

---

### 6) What happens if a caretaker is on approved leave?
The system should treat approved leave as unavailability:
- Bookings overlapping the leave window should be blocked or require assigning a different caretaker.

---

### 7) Can bookings be cancelled or rescheduled?
Usually yes, but it depends on your policy and implementation:
- Cancelling a `requested` booking is typically allowed.
- Cancelling a `confirmed` booking may have restrictions or fees.
- Rescheduling may require availability checks and admin/HR approval depending on rules.

---

## Payments

### 8) Are online payments supported?
It depends on the implementation:
- Some versions support online gateways (e.g., Stripe/PayPal).
- Others support manual payment tracking (mark as paid).

If online payments are enabled, payment status should be verified securely (preferably via provider APIs/webhooks).

---

### 9) Can the system handle refunds?
If refunds are implemented:
- Refund actions should be **admin-only**
- Refunds should be logged (who, when, why)
- Partial refunds may be supported depending on policy

---

## Leave Management

### 10) How does leave approval work?
Typical flow:
1. Caretaker submits a leave request (status `pending`).
2. HR reviews and approves (`approved`) or rejects (`rejected`).
3. Approved leave blocks booking assignments in that window.

---

### 11) What if an approved leave conflicts with future bookings?
Recommended handling:
- Detect conflicts during leave approval.
- Flag impacted bookings for reassignment.
- Optionally block approval until conflicts are resolved.

---

## Admin & HR

### 12) What is the difference between Admin and HR?
- **HR** focuses on caretaker verification and leave management.
- **Admin** has full control (users/roles, settings, payments, reports) and can override HR decisions when necessary.

---

### 13) How do we control who can see what?
Use role-based access control (RBAC):
- Require login for protected pages.
- Enforce role guards on every page/action.
- Prevent “IDOR” issues by checking record ownership (clients can’t access other clients’ bookings, etc.).

See `USER_AUTHENTICATION.md` and `SECURITY_GUIDELINES.md`.

---

## Developer / Setup

### 14) How do I run the project locally?
See:
- `INSTALLATION_GUIDE.md`

Typical steps:
- Install XAMPP/WAMP/LAMP
- Put project in web root (`htdocs`)
- Create/import the database
- Configure DB connection settings
- Open `http://localhost/<project-folder>/`

---

### 15) Where is the database schema documented?
See:
- `DATABASE_SCHEMA.md`

If your project already has a real schema, export it and update the doc so it matches your actual tables/columns.

---

### 16) What should I do if I find a bug?
Create an issue using:
- `BUG_REPORT_TEMPLATE.md`

Include:
- steps to reproduce
- expected vs actual result
- screenshots/logs
- environment details

---

### 17) How can we improve security quickly?
Suggested quick wins:
1. Use prepared statements everywhere
2. Add CSRF tokens to all POST forms
3. Ensure role checks on every protected page
4. Escape output to prevent XSS
5. Validate file uploads (if any)

See:
- `SECURITY_GUIDELINES.md`

---

### 18) How do we add a new feature safely?
Recommended process:
1. Create a small design note (what, who, flow)
2. Add/adjust DB tables (migration or SQL script)
3. Implement backend logic + validations
4. Add UI pages
5. Add test cases in `TEST_CASES.md`
6. Manually test core flows (auth, booking, leave, payments)

---

## Data & Privacy

### 19) What personal data does the system store?
Typical stored data may include:
- names, emails, phone numbers
- addresses (optional)
- booking details
- caretaker profile info (skills, rate, bio)
- leave requests

If documents are uploaded (IDs/certificates), store and protect them carefully.

---

### 20) Can we delete user data?
Depends on policy. Recommended approach:
- use **soft-delete** or account disable for operational integrity
- allow admin to anonymize data if required
- keep audit logs for admin actions (without exposing sensitive data)

---

## Contact / Support

### 21) Who should I contact for help?
- For setup issues: share logs + your environment details (PHP/MySQL versions).
- For app bugs: open a GitHub issue using the bug report template.
- For security issues: report privately to the project maintainers (avoid posting secrets in issues).

---
