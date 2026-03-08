# Admin Panel Guide — Caretaker Management System

This guide describes the responsibilities, key pages, workflows, and recommended security controls for the **Admin Panel** in the Caretaker Management System.

> Note: Page/file names vary by repository. If you share your admin folder/file list, I can tailor this guide to match your exact pages (e.g., `admin/dashboard.php`, `admin/users.php`, etc.).

---

## 1. Admin Role Overview

The **Admin** role is responsible for system-wide configuration and oversight. Admin capabilities typically include:

- Manage users and roles (Admin, HR, Client, Caretaker)
- Monitor and manage bookings and service categories
- Oversee payments and refunds (if payment module exists)
- View reports and audit logs
- Configure global system settings

**Admin users should have the highest permissions** and should be protected with strict security rules.

---

## 2. Access & Security Requirements

### 2.1 Authentication
- Admin must be logged in via session-based auth.
- The system must enforce **deny-by-default** access control.

Recommended guard per admin page:
- `require_login()`
- `require_role('admin')`

### 2.2 Session Security (Recommended)
- Regenerate session ID after login: `session_regenerate_id(true)`
- HTTPOnly cookies enabled
- Secure cookies enabled in production (HTTPS)
- Reasonable session timeout (optional)

### 2.3 CSRF Protection (Recommended)
All admin actions that modify data should require a CSRF token:
- creating/editing users
- changing roles
- verifying caretakers
- approving/rejecting leave
- modifying bookings
- marking payments as paid/refunded

### 2.4 Input Validation
- Use server-side validation for all fields
- Use prepared statements for DB access
- Escape output to prevent XSS

---

## 3. Admin Panel Navigation (Suggested)

A typical Admin Panel menu:

1. **Dashboard**
2. **Users**
3. **Caretakers**
4. **Clients**
5. **Services**
6. **Bookings**
7. **Leave Requests**
8. **Payments**
9. **Reports**
10. **System Settings**
11. **Audit Logs**

---

## 4. Admin Dashboard

### 4.1 Purpose
The dashboard provides a quick overview of system health and activity.

### 4.2 Recommended Widgets / KPIs
- Total users (by role)
- Pending caretaker verifications
- Pending leave requests
- Upcoming bookings (next 7 days)
- Bookings by status (requested/confirmed/completed/cancelled)
- Revenue summary (today/this month) if payments are enabled

### 4.3 Common Admin Actions
- Quickly open pending verification queues
- Jump to bookings management
- Access reports

---

## 5. User Management

### 5.1 Create User
Admin can create:
- HR accounts
- Admin accounts (use caution)
- Client accounts (optional)
- Caretaker accounts (optional; usually caretakers self-register)

**Required fields (typical)**
- name
- email (unique)
- role
- password

**Recommended behavior**
- send login credentials via email (optional)
- force password reset on first login (optional)

### 5.2 Edit User
Admin can:
- change role
- update contact info
- reset password (or trigger reset)
- disable/enable account

### 5.3 Disable User
Disabling should prevent:
- login
- viewing protected pages
- creating bookings/leave requests

### 5.4 Validation Rules
- prevent duplicate email
- prevent demoting/removing the last admin account (recommended)
- log role changes (recommended)

---

## 6. Caretaker Management (Admin View)

Admin can:
- view caretaker profiles
- override verification status (verified/rejected)
- deactivate caretaker accounts
- review caretaker bookings and leave history

Recommended filters:
- verification status (pending/verified/rejected)
- service type
- rate range
- active/inactive

---

## 7. Services Management

Admin can:
- create/edit/disable services (elder care, babysitting, household support)
- manage service descriptions and pricing policy (if used)

Recommended:
- disallow deleting services that are used in existing bookings (soft-disable instead)

---

## 8. Bookings Management (Admin)

### 8.1 Admin Capabilities
- view all bookings
- filter by status/date/service/caretaker/client
- assign caretakers to unassigned bookings
- change booking status (policy-driven)
- cancel bookings (with refund logic if applicable)

### 8.2 Booking Conflict Handling
When assigning or confirming, the admin panel should validate:
- caretaker is verified
- caretaker has no overlapping confirmed/in-progress bookings
- caretaker has no overlapping approved leave

---

## 9. Leave Requests (Admin)

Admin can:
- view leave requests
- approve/reject (if HR not available)
- override decisions (recommended to log overrides)

When approving leave, system should:
- detect conflicting bookings
- flag impacted bookings for reassignment
- optionally prevent approval until conflicts resolved (policy)

---

## 10. Payments (Admin)

If payments are enabled, admin can:
- view payment transactions
- mark manual payments as paid (offline payments)
- issue refunds (if supported)
- resolve disputes (optional)

Recommended payment fields to show:
- booking id
- client
- amount/currency
- status
- provider reference
- timestamps

---

## 11. Reports

Recommended report types:
- bookings per month/week/day
- revenue per month/service type
- caretaker utilization
- leave statistics
- top caretakers by number of completed bookings (optional)

Recommended export:
- CSV export for users/bookings/payments

---

## 12. System Settings (Optional)

If you implement a settings page, consider:
- service pricing rules
- cancellation policies
- booking limits (max hours/day, max future booking window)
- payment configuration (provider keys)
- email/notification settings

> Security: settings should be admin-only and require CSRF for updates.

---

## 13. Audit Logs (Strongly Recommended)

Admin actions to audit:
- role changes
- disabling/enabling accounts
- caretaker verification overrides
- booking status changes/cancellations
- leave approvals/rejections
- payment status updates/refunds

Suggested audit fields:
- actor (admin user id)
- action type
- entity type + entity id
- before/after values (optional)
- timestamp
- IP address (optional)

---

## 14. Admin Panel UI/UX Recommendations

- Use clear confirmation dialogs for destructive actions:
  - disable user, cancel booking, refund payment
- Show validation errors inline
- Provide filters, pagination, and search
- Provide consistent status badges (requested/confirmed/etc.)
- Make sure admin pages are responsive (at least basic)

---

## 15. Admin Panel Test Checklist

- [ ] Admin can log in and access dashboard
- [ ] Non-admin cannot access admin pages
- [ ] Admin can create and disable users
- [ ] Admin can manage services
- [ ] Admin can view and manage bookings
- [ ] Conflict checks work when assigning caretaker
- [ ] Admin can approve/reject leave (or override)
- [ ] Payment views are correct (if enabled)
- [ ] CSRF tokens required for admin mutations
- [ ] No SQL errors / no XSS on user-entered content

---
