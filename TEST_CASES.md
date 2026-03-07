# Test Cases — Caretaker Management System

This document provides a practical set of manual and semi-automatable test cases for the Caretaker Management System (PHP web app).

> Notes
> - IDs like **TC-AUTH-001** are stable references you can use in issues/bug reports.
> - Where exact URLs/pages differ in your repo, map the test case to the equivalent page (e.g., `login.php`, `auth/login.php`, etc.).
> - If you share your actual page routes and DB table names, I can tailor these test cases precisely.

---

## 1. Test Environment

### 1.1 Recommended Setup
- PHP: 8.x
- MySQL/MariaDB: running locally
- Web server: Apache (XAMPP/WAMP/LAMP)
- Browser: Chrome + Firefox (at minimum)

### 1.2 Test Data (Suggested)
Create at least:
- 1 Admin user
- 1 HR user
- 2 Client users
- 3 Caretaker users:
  - 1 verified
  - 1 pending verification
  - 1 rejected/disabled

Create:
- 3 services (elder care, babysitting, household services)
- 2 bookings (different statuses)
- 1 leave request (pending)

---

## 2. Authentication & Session Management

### TC-AUTH-001 — Login with valid credentials
**Preconditions**
- User exists with known email/password.

**Steps**
1. Open the Login page.
2. Enter valid email and password.
3. Submit.

**Expected Results**
- User is authenticated.
- Session is created.
- User is redirected to the correct dashboard based on role.

---

### TC-AUTH-002 — Login with invalid password
**Steps**
1. Open Login page.
2. Enter correct email + incorrect password.
3. Submit.

**Expected Results**
- Login fails with an error message.
- No session is created.

---

### TC-AUTH-003 — Access protected page without login
**Steps**
1. Log out (or clear cookies).
2. Directly open a protected URL (admin/hr/client/caretaker page).

**Expected Results**
- Redirect to login page (or show “Unauthorized” page).
- No protected data is displayed.

---

### TC-AUTH-004 — Logout ends session
**Steps**
1. Log in.
2. Click Logout.
3. Use browser back button to try to access a previously visited protected page.

**Expected Results**
- Session is terminated.
- Protected pages remain inaccessible (redirect to login).

---

### TC-AUTH-005 — Role guard enforcement
**Preconditions**
- Have an HR account and an Admin-only page.

**Steps**
1. Log in as HR.
2. Attempt to open an Admin-only page.

**Expected Results**
- Access denied or redirect to HR dashboard.
- Attempt is logged (optional).

---

## 3. User Registration (If Supported)

### TC-REG-001 — Caretaker registration creates pending verification profile
**Steps**
1. Open caretaker registration page.
2. Fill required fields.
3. Submit.

**Expected Results**
- User created with role `caretaker`.
- Caretaker profile created with `verification_status = pending`.
- Caretaker cannot be booked until verified.

---

### TC-REG-002 — Duplicate email blocked
**Steps**
1. Register with an email that already exists.
2. Submit.

**Expected Results**
- Registration is rejected.
- Clear “email already in use” error is shown.

---

## 4. Caretaker Verification (HR/Admin)

### TC-VERIFY-001 — HR approves a caretaker
**Preconditions**
- At least one caretaker is in pending state.

**Steps**
1. Log in as HR.
2. Open “Pending Caretakers” list.
3. Select a pending caretaker.
4. Click Approve/Verify.

**Expected Results**
- Verification status becomes `verified`.
- Caretaker appears in client browsing/booking selection.

---

### TC-VERIFY-002 — HR rejects a caretaker
**Steps**
1. Log in as HR.
2. Open pending caretaker.
3. Click Reject and provide a reason (if supported).

**Expected Results**
- Verification status becomes `rejected`.
- Caretaker is not shown as available for booking.

---

### TC-VERIFY-003 — Non-HR cannot verify caretakers
**Steps**
1. Log in as client or caretaker.
2. Attempt to access verification endpoints/pages.

**Expected Results**
- Access denied (role guard enforced).

---

## 5. Service Management

### TC-SVC-001 — Admin creates a new service
**Steps**
1. Log in as Admin.
2. Open Services management page.
3. Add a service (name + description).
4. Save.

**Expected Results**
- Service appears in service list.
- Service is selectable during booking.

---

### TC-SVC-002 — Service name validation
**Steps**
1. Add a service with empty name or invalid length.
2. Save.

**Expected Results**
- Validation error shown.
- No DB record is created.

---

## 6. Booking Management

### TC-BOOK-001 — Client creates booking request
**Preconditions**
- Client logged in
- At least one service exists
- At least one verified caretaker exists

**Steps**
1. Open “New Booking”.
2. Select service.
3. Select caretaker (or choose “any”, if supported).
4. Choose date/time range.
5. Submit.

**Expected Results**
- Booking is created with status `requested` (or `pending_payment`).
- Booking appears in client booking history.

---

### TC-BOOK-002 — Prevent booking overlaps for caretaker
**Preconditions**
- A verified caretaker has a confirmed booking for a time range.

**Steps**
1. Attempt to create another booking for the same caretaker overlapping that time.

**Expected Results**
- Booking is blocked or caretaker is shown unavailable.
- Clear message indicates conflict.

---

### TC-BOOK-003 — Booking status transitions
**Steps**
1. Create a booking.
2. Confirm it (admin/HR/caretaker flow depending on system).
3. Mark as in-progress.
4. Mark as completed.

**Expected Results**
- Each transition is allowed only in the correct order.
- Status stored correctly.
- Status history recorded (if implemented).

---

### TC-BOOK-004 — Cancel booking
**Steps**
1. Create/confirm booking.
2. Cancel booking as allowed actor (client/admin).
3. Verify listing.

**Expected Results**
- Status becomes `cancelled`.
- Payment rules respected (refund/fees if implemented).

---

## 7. Leave Management

### TC-LEAVE-001 — Caretaker submits leave request
**Steps**
1. Log in as caretaker.
2. Open Leave Request page.
3. Enter from/to dates.
4. Submit.

**Expected Results**
- Leave request created with status `pending`.
- Visible in caretaker leave history.
- Visible in HR pending leave list.

---

### TC-LEAVE-002 — HR approves leave
**Preconditions**
- Pending leave request exists.

**Steps**
1. Log in as HR.
2. Open leave request.
3. Approve.

**Expected Results**
- Status becomes `approved`.
- Caretaker is not bookable during leave window.
- Conflicting bookings are detected/flagged (policy-dependent).

---

### TC-LEAVE-003 — Leave overlap validation
**Preconditions**
- Existing approved leave exists for a caretaker.

**Steps**
1. Submit another leave request overlapping the same date range.

**Expected Results**
- Request is blocked or merged depending on rules.
- Clear error message.

---

## 8. Payments (If Implemented)

### TC-PAY-001 — Payment success updates booking
**Preconditions**
- Booking in `pending_payment`.

**Steps**
1. Proceed to payment.
2. Complete payment successfully (gateway sandbox or manual paid).
3. Return to app.

**Expected Results**
- Payment record created/updated: `paid`.
- Booking status becomes `confirmed` (or equivalent).
- Receipt/invoice available if supported.

---

### TC-PAY-002 — Payment failure
**Steps**
1. Attempt payment.
2. Fail/cancel payment.

**Expected Results**
- Payment status becomes `failed` (or remains pending).
- Booking remains unconfirmed.
- Clear UI message provided.

---

### TC-PAY-003 — Prevent double payment
**Preconditions**
- Booking already paid.

**Steps**
1. Attempt to pay again for same booking.

**Expected Results**
- System prevents duplicate payment.
- User sees message: already paid / receipt shown.

---

## 9. Input Validation & Security Tests

### TC-SEC-001 — SQL injection attempt on login
**Steps**
1. In email field, enter a typical injection string (e.g., `' OR '1'='1`).
2. Submit.

**Expected Results**
- Login fails.
- No SQL error shown.
- App remains stable (prepared statements prevent injection).

---

### TC-SEC-002 — XSS attempt in profile fields
**Steps**
1. Enter `<script>alert(1)</script>` in a text field like bio/notes.
2. Save.
3. Reload profile view page.

**Expected Results**
- Script does not execute.
- Output is escaped/sanitized.

---

### TC-SEC-003 — CSRF protection on critical forms (Recommended)
**Steps**
1. Attempt to submit a booking/leave/payment request without a valid CSRF token (if implemented).

**Expected Results**
- Request is rejected with a CSRF error.
- No DB changes occur.

---

## 10. UI/UX & Cross-Browser

### TC-UI-001 — Responsive layout
**Steps**
1. Open dashboards on mobile width (e.g., 375px).
2. Navigate bookings, profile, leave pages.

**Expected Results**
- No horizontal scrolling for main content.
- Menus remain usable.

---

### TC-UI-002 — Form usability
**Steps**
1. Try submitting required forms with missing fields.

**Expected Results**
- Fields are clearly highlighted.
- Error messages appear near the relevant inputs.

---

## 11. Performance & Reliability (Basic)

### TC-PERF-001 — Dashboard loads with 500+ bookings
**Preconditions**
- Seed DB with many bookings (or duplicate records for testing).

**Steps**
1. Open admin dashboard / booking list.

**Expected Results**
- Page loads within acceptable time (target < 2 seconds locally; depends on machine).
- Pagination/filtering available if implemented.

---

## 12. Regression Checklist (Quick)

Run after any change related to booking/leave/payment/auth:

- [ ] Login/logout works
- [ ] Role-based access works (admin/hr/client/caretaker)
- [ ] Caretaker verification still works
- [ ] Booking creation works
- [ ] Booking conflict rules still enforced
- [ ] Leave request and approval works
- [ ] Payment success/failure handled correctly (if enabled)
- [ ] No new PHP errors/warnings in logs

---
