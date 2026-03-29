# Security Guidelines — Caretaker Management System

This document outlines security best practices and recommended controls for the Caretaker Management System (PHP web application). It focuses on protecting user accounts, personal data, bookings, payments, and admin/HR operations.

> Note: This is a security **guideline** document. If you share your current auth/db code patterns, I can map these guidelines into concrete tasks and point out exact gaps in your repository.

---

## 1. Security Objectives

1. **Prevent unauthorized access** to admin/HR/client/caretaker dashboards
2. **Protect sensitive data** (PII, credentials, payment references)
3. **Prevent common web vulnerabilities** (SQLi, XSS, CSRF, file upload abuse)
4. **Ensure integrity** of bookings, leave approvals, and payment state
5. **Provide auditability** for privileged actions

---

## 2. Authentication

### 2.1 Password Storage
- Use `password_hash()` for storing passwords.
- Verify with `password_verify()`.
- Never store plaintext or reversible encrypted passwords.

### 2.2 Account Status
- Enforce an account `status` flag (`active`, `disabled`, `pending`).
- Block login for `disabled` and optionally for `pending`.

### 2.3 Brute Force Protection (Recommended)
- Rate-limit login attempts per IP and per account.
- Introduce increasing delays after repeated failures.
- Optional: CAPTCHA after N failed attempts.

### 2.4 Password Reset (If Implemented)
- Use time-limited, single-use tokens.
- Store token hashes (not plaintext tokens).
- Invalidate token after use.

---

## 3. Authorization (RBAC)

### 3.1 Deny-by-Default
- Every protected page should require login.
- Every protected action should require role validation.

### 3.2 Role Guards
Implement consistent guards:
- `require_login()`
- `require_role('admin')`
- `require_any_role(['hr', 'admin'])`

### 3.3 Privilege Separation
- HR should not have admin-only permissions unless explicitly intended.
- Admin actions (role changes, refunds) should require extra confirmation.

---

## 4. Session & Cookie Security

### 4.1 Session Handling
- Call `session_start()` before any output.
- Regenerate session ID on login:
  - `session_regenerate_id(true)`
- Destroy sessions on logout.

### 4.2 Cookie Flags (Recommended)
- `HttpOnly` to block JavaScript access to session cookies
- `Secure` in production (HTTPS only)
- `SameSite=Lax` (or `Strict` if compatible)

### 4.3 Session Timeout (Recommended)
- Idle timeout (e.g., 30–60 minutes)
- Absolute session lifetime (optional)

---

## 5. Input Validation & Output Encoding

### 5.1 SQL Injection Prevention (Critical)
- Use prepared statements (PDO or MySQLi prepared).
- Never build SQL by concatenating untrusted input.

### 5.2 XSS Prevention (Critical)
- Escape user-provided data on output:
  - `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')`
- Validate and sanitize rich text fields carefully (prefer plain text).

### 5.3 File Upload Validation
For uploads (photos/documents):
- Validate file type using server-side checks
- Restrict allowed extensions (e.g., jpg/png/pdf)
- Enforce maximum file size
- Store outside web root if possible
- Use randomized filenames
- Strip executable metadata where possible

---

## 6. CSRF Protection (Critical)

### 6.1 Protect All State-Changing Requests
Use CSRF tokens on:
- login (optional but good)
- registration
- booking creation/cancellation/reschedule
- leave request submission/approval/rejection
- caretaker verification
- payment actions
- role/user updates in admin panel

### 6.2 Token Pattern (Recommended)
- Token stored in session: `$_SESSION['csrf_token']`
- Token embedded in every form
- Validate via `hash_equals()`
- Rotate token periodically (optional)

---

## 7. Access Control for Data (IDOR Protection)

### 7.1 Prevent Insecure Direct Object References
Always verify ownership/permissions for any record accessed by ID:
- Client can only view their own bookings/payments
- Caretaker can only view their own bookings/leave
- HR/Admin can view and manage broader sets

**Example**
If URL has `booking_id=123`, validate:
- does the current user have permission to access booking 123?

---

## 8. Payments Security (If Enabled)

### 8.1 Trust the Provider, Not the Browser
- Never mark payments as `paid` based on a GET parameter like `?status=success`.
- Prefer webhook/instant verification with the provider API.

### 8.2 Store Minimal Payment Data
- Store provider reference IDs and status
- Do not store raw card data (ever)
- If using Stripe/PayPal: use hosted checkout pages or official SDK flows

### 8.3 Refund Controls
- Restrict refunds to admin only
- Record refund reason and actor
- Keep immutable payment history (avoid deleting transactions)

---

## 9. Error Handling & Logging

### 9.1 Error Messages
- Do not expose stack traces or SQL errors to users.
- Use generic user-facing messages.
- Log detailed errors server-side.

### 9.2 Logging Recommendations
Log:
- failed login attempts
- role changes
- caretaker verification actions
- leave approvals/rejections
- booking status changes
- payment state changes

Avoid logging:
- plaintext passwords
- session IDs
- sensitive PII unless necessary

---

## 10. Secure Configuration

### 10.1 Secrets Management
- Do not commit secrets to GitHub.
- Use environment variables or separate config ignored by git.
- Provide a `.env.example` or `config.sample.php`.

### 10.2 Production Settings
- Disable `display_errors`
- Enable HTTPS
- Set correct permissions on upload directories

---

## 11. Database Security

- Use a dedicated DB user with minimum privileges (not `root`).
- Regular backups (encrypted if possible).
- Ensure DB is not publicly accessible from the internet unless needed.

---

## 12. Security Testing Checklist

Minimum tests to run:
- [ ] SQL injection attempts fail safely (login, booking, admin forms)
- [ ] XSS payloads render as text, not executable scripts
- [ ] CSRF token required on all POST mutations
- [ ] Role restrictions enforced on every protected page
- [ ] ID-based resources enforce ownership/permissions
- [ ] File upload rejects invalid file types
- [ ] Payment status cannot be spoofed via URL parameters

See `TEST_CASES.md` for additional test coverage.

---

## 13. Security Roadmap (Suggested)

Near-term:
1. Add CSRF tokens everywhere
2. Ensure prepared statements everywhere
3. Add consistent role guards on every page
4. Add upload validation (if uploads exist)
5. Add audit logging for privileged actions

Mid-term:
- Rate limiting for login
- Password reset flow with secure tokens
- Stronger permission model (granular permissions)

Long-term:
- Centralized logging/monitoring
- Security headers (CSP, HSTS, etc.) if using a modern deployment
- Optional 2FA for admin accounts

---
