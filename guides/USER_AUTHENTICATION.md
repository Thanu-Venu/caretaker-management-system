# User Authentication — Caretaker Management System

This document describes a recommended authentication and authorization approach for the Caretaker Management System (PHP web application), including session handling, role-based access control, and security best practices.

> Note: If you share your current login/register PHP files (or the file tree), I can tailor this document to match your exact implementation (file names, functions, session keys, roles, redirects, etc.).

---

## 1. Overview

Authentication answers: **“Who is the user?”**  
Authorization answers: **“What is the user allowed to do?”**

The system supports role-based dashboards and pages for:
- **Admin**
- **HR**
- **Client**
- **Caretaker**

The recommended approach for a PHP app is:
- **Session-based authentication**
- **Password hashing** with `password_hash()` / `password_verify()`
- **Role-based access guards** per page/route

---

## 2. Terminology

- **Identity:** The user’s login record (email, password hash, role)
- **Session:** Server-side state that persists authentication between requests
- **RBAC:** Role-Based Access Control (admin/hr/client/caretaker)

---

## 3. Data Model (Recommended)

### 3.1 `users` table (typical)
- `id` (PK)
- `full_name`
- `email` (unique)
- `password_hash`
- `role` (`admin`, `hr`, `client`, `caretaker`)
- `status` (`active`, `disabled`, `pending`)
- `created_at`, `updated_at`

> See `DATABASE_SCHEMA.md` for a complete recommended schema.

---

## 4. Authentication Flows

### 4.1 Login
**Inputs**
- Email
- Password

**Server-side logic**
1. Validate email format and password presence.
2. Look up user by email.
3. Ensure account status is `active`.
4. Verify password:
   - `password_verify($password, $user['password_hash'])`
5. Start session and store minimal identity in session.
6. Redirect user to role dashboard.

**Session keys (recommended)**
- `user_id`
- `user_role`
- `user_email` (optional)
- `user_name` (optional)
- `csrf_token` (recommended)

### 4.2 Logout
1. Unset session values.
2. Destroy session.
3. Redirect to login page.

### 4.3 Registration (Optional)
If the app supports self-registration:
- Client and caretaker can register.
- HR and admin accounts are usually created by admin only (recommended).

After registration:
- Client accounts can become active immediately.
- Caretaker accounts should default to `pending` verification and may require HR approval.

---

## 5. Authorization (RBAC)

### 5.1 Page Guards (Recommended)
Every protected page should:
1. Require login (session exists)
2. Require correct role(s)

**Examples**
- `/admin/*` requires role `admin`
- `/hr/*` requires role `hr` or `admin`
- `/client/*` requires role `client`
- `/caretaker/*` requires role `caretaker`

### 5.2 Guard Helpers (Recommended)
Implement reusable helper functions, for example in `includes/auth.php`:

- `require_login()`
- `require_role($role)`
- `require_any_role(array $roles)`

This avoids duplicating logic across files and reduces security gaps.

---

## 6. Password Security

### 6.1 Hashing
Always store passwords using:
- `password_hash($plainPassword, PASSWORD_DEFAULT)`

Verify using:
- `password_verify($plainPassword, $storedHash)`

### 6.2 Password Policies (Recommended)
- Minimum length: 8+
- Encourage passphrases
- Block very common passwords (optional)

---

## 7. Session Security

### 7.1 Session Best Practices
- Call `session_start()` before any output
- Regenerate session ID after login:
  - `session_regenerate_id(true)`
- Store only needed values in session (avoid storing sensitive details)

### 7.2 Cookie Settings (Recommended)
If you can configure PHP session cookies:
- `session.cookie_httponly = 1`
- `session.cookie_secure = 1` (requires HTTPS)
- `session.cookie_samesite = Lax` (or `Strict` when feasible)

---

## 8. CSRF Protection (Strongly Recommended)

### 8.1 What to Protect
Protect all state-changing POST actions:
- booking creation
- leave requests
- verification approvals
- payment actions
- profile updates

### 8.2 Implementation Pattern
1. Generate a token on session creation:
   - `$_SESSION['csrf_token'] = bin2hex(random_bytes(32));`
2. Add hidden field in forms:
   - `<input type="hidden" name="csrf_token" value="...">`
3. Validate on POST:
   - `hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])`

---

## 9. Input Validation & Output Escaping

### 9.1 Prevent SQL Injection
- Use prepared statements (PDO or MySQLi prepared)
- Never concatenate user input directly into SQL queries

### 9.2 Prevent XSS
- Escape output when rendering user-entered fields:
  - `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')`

---

## 10. Recommended Redirects After Login

Example mapping:
- `admin` → Admin dashboard
- `hr` → HR dashboard
- `client` → Client dashboard
- `caretaker` → Caretaker dashboard

If a user tries to access a protected page without access:
- redirect to login, or
- show “403 Unauthorized”

---

## 11. Error Handling

Guidelines:
- Avoid revealing whether an email exists (“Invalid credentials” is better than “Email not found”)
- Log errors server-side
- In production: disable `display_errors`

---

## 12. Test Cases (Quick)

Minimum authentication tests:
- Valid login works for each role
- Invalid password fails
- Disabled user cannot log in
- Accessing protected pages redirects to login
- Role-based restriction works (HR cannot open admin-only page)
- Logout destroys session

See `TEST_CASES.md` for expanded test coverage.

---
