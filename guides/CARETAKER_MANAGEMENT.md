# Caretaker Management — Caretaker Management System

This document describes caretaker-related functionality: registration, profile management, verification, availability, leave, and admin/HR workflows.

> Note: This is written to be compatible with a typical PHP + MySQL implementation. If you share your actual folders/pages/table names, I can tailor this to your exact codebase.

---

## 1. Overview

Caretaker management covers the full lifecycle of a caretaker account:

1. **Sign up / registration**
2. **Profile completion** (skills/services, rate, bio, documents)
3. **Verification** by HR/admin
4. **Booking assignment & execution**
5. **Leave requests**
6. **Ongoing performance tracking** (optional: ratings, attendance)

---

## 2. Roles Involved

### 2.1 Caretaker
- Creates account and caretaker profile
- Updates availability and profile details
- Views bookings and schedule
- Requests leave
- Views payment/earning status (if supported)

### 2.2 HR
- Reviews caretaker registration submissions
- Verifies/rejects caretakers
- Reviews caretaker leave requests
- Handles reassignment when leave impacts bookings (policy-dependent)

### 2.3 Admin
- Full access
- Can manage users, roles, and override verification decisions
- Generates reports and audits caretaker-related activity

---

## 3. Caretaker Registration Workflow

### 3.1 Registration Inputs (Suggested)
Typical caretaker registration collects:

- Identity:
  - Full name
  - Email (unique)
  - Phone number
- Profile:
  - Address / location (optional)
  - Bio / introduction
  - Services offered (elder care, babysitting, household services)
  - Skills / qualifications (optional)
  - Experience (years)
  - Hourly rate / pricing
- Documents (optional):
  - ID document
  - Certificates

### 3.2 System Behavior
- Create a `users` record with role `caretaker`
- Create a `caretakers` profile record
- Set `verification_status = pending`
- Caretaker is **not bookable/visible** until verified (recommended)

### 3.3 Validation Rules (Recommended)
- Email must be unique
- Strong password requirements
- Hourly rate must be numeric and within allowed bounds
- Required services/skills must be selected

---

## 4. Caretaker Verification (HR/Admin)

### 4.1 Purpose
Verification ensures the caretaker is legitimate and qualified to offer services.

### 4.2 Verification States
- `pending` — awaiting HR review
- `verified` — caretaker can receive bookings
- `rejected` — caretaker cannot be booked; caretaker should see reason (optional)

### 4.3 HR Review Checklist (Suggested)
- Profile completeness
- Contact details provided
- Documents present and readable (if used)
- Certifications valid (if used)
- Manual interview/reference check (optional)

### 4.4 Actions
HR/Admin can:
- Approve (set verified)
- Reject (set rejected)
- Request edits (optional intermediate state, e.g., `needs_changes`)

---

## 5. Caretaker Profile Management

### 5.1 Editable Fields (Common)
- Bio, skills/services offered
- Hourly rate
- Profile photo
- Contact info (if allowed)
- Availability preferences (if supported)

### 5.2 Security Requirements
- Caretaker can edit **only their own** profile
- HR/Admin can edit any caretaker profile
- Sanitize all text fields to prevent XSS
- Validate file uploads for photos/documents

---

## 6. Availability & Scheduling (Recommended)

### 6.1 Availability Models
You can implement either model:

**A) Simple model**
- Single boolean “available/unavailable”
- Manual “block dates” (using leave)

**B) Calendar model (recommended for scale)**
- Weekly recurring availability slots (e.g., Mon–Fri 9–17)
- Exceptions / blocked dates
- Compute availability at booking time

### 6.2 Conflict Rules
A caretaker should not be assigned to a booking if:
- the booking overlaps an **approved leave request**
- the booking overlaps another **confirmed/in-progress booking**

---

## 7. Booking Assignment & Caretaker Operations

### 7.1 Booking Assignment
Assignment can be implemented as:
- Client selects a caretaker directly, or
- HR/admin assigns a caretaker after request, or
- Auto-match (future enhancement)

### 7.2 Caretaker Booking View
Caretaker should be able to:
- View assigned bookings
- Confirm acceptance (optional)
- Update status: in progress → completed (policy dependent)
- See client details required to perform service (privacy aware)

---

## 8. Leave Management (Caretaker → HR)

### 8.1 Leave Request Submission
Caretaker submits:
- From date
- To date
- Reason (optional)
- Leave type (optional: sick/vacation/unpaid)

System:
- Creates `leave_requests` record with status `pending`

### 8.2 HR Review
HR can:
- Approve (status `approved`)
- Reject (status `rejected`)
- Add comments/reason (recommended)

### 8.3 Booking Impacts
When leave is approved:
- Identify overlapping future bookings
- Depending on policy:
  - Prevent approval until conflicts are resolved, or
  - Approve and flag impacted bookings for reassignment, or
  - Auto-suggest replacement caretakers

---

## 9. Data Model (Suggested Tables)

Caretaker management usually involves:

- `users`
- `caretakers`
- `services`
- `caretaker_services` (many-to-many)
- `bookings`
- `leave_requests`
- `uploads` (optional)
- `audit_logs` or status history (recommended)

For a full recommended schema, see `DATABASE_SCHEMA.md`.

---

## 10. UI Pages (Suggested)

Caretaker area:
- Registration page
- Profile page
- Booking list (upcoming/past)
- Leave request page
- Leave history page

HR/Admin area:
- Pending caretaker verification list
- Caretaker profile review page
- Leave request review page
- Reports page

---

## 11. Security & Compliance Notes

- Use session-based authentication.
- Enforce role guards on every protected page.
- Store passwords with `password_hash()` / `password_verify()`.
- Use prepared statements for all DB queries.
- For caretaker documents:
  - restrict who can view/download
  - store outside web root if possible
  - log access (optional)

---

## 12. Future Enhancements (Caretaker-Focused)

- Ratings & reviews
- Certifications expiry reminders
- Background check integration
- Caretaker training modules
- Earnings dashboard and payout tracking
- In-app chat (client ↔ caretaker)

---
