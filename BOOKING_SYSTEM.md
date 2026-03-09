# Booking System — Caretaker Management System

This document describes how the booking system should work end-to-end: data model, booking lifecycle, validation rules, and common workflows for clients, caretakers, HR, and admins.

> Note: This is written for a typical PHP + MySQL/MariaDB implementation. If you share your actual pages/routes and table names, I can tailor this to match your repository exactly.

---

## 1. Overview

The booking system enables clients to request and manage caretaker services such as:
- Elder care
- Babysitting
- Household support

A booking typically includes:
- Client (who requests the service)
- Caretaker (assigned to provide the service; may be selected by the client or assigned later)
- Service type/category
- Date/time window
- Location (optional)
- Status / lifecycle state
- Payment state (if implemented)

---

## 2. Roles & Responsibilities

### 2.1 Client
- Browse services/caretakers
- Create booking requests
- Pay for bookings (if applicable)
- View booking history, upcoming bookings
- Cancel or reschedule bookings (policy-dependent)

### 2.2 Caretaker
- View assigned bookings
- Accept/confirm bookings (optional flow)
- Mark progress (in-progress/completed) if allowed
- Request leave (may affect bookings)

### 2.3 HR
- Verify caretakers
- Review leave and manage booking conflicts caused by leave
- Optionally assign caretakers to unassigned bookings

### 2.4 Admin
- Full access to bookings and payments
- Override cancellations/reschedules
- Reporting and audit access

---

## 3. Booking Lifecycle (State Machine)

Below is a recommended booking status model. Your code can implement a subset, but the transitions should remain consistent.

### 3.1 Booking Statuses
- `requested`
  - Client submitted booking; awaiting assignment/confirmation
- `pending_payment` (optional)
  - Booking created but not confirmed until payment succeeds
- `confirmed`
  - Caretaker assigned and booking approved (and paid if required)
- `in_progress`
  - Caretaker started the job
- `completed`
  - Service completed successfully
- `cancelled`
  - Booking cancelled by client/admin (or system policy)

### 3.2 Allowed Transitions (Recommended)
- `requested` → `pending_payment` (if pay-first)
- `requested` → `confirmed` (if pay-later or admin confirms)
- `pending_payment` → `confirmed` (payment success)
- `confirmed` → `in_progress`
- `in_progress` → `completed`
- `requested` → `cancelled`
- `pending_payment` → `cancelled`
- `confirmed` → `cancelled` (policy-dependent; may trigger refund)
- `requested` → `confirmed` via caretaker assignment/approval (if not pay-first)

> Tip: For auditability, store a status history record whenever a transition occurs.

---

## 4. Booking Creation (Client Flow)

### 4.1 Inputs
Minimum recommended inputs:
- Service type (service/category)
- Date/time range:
  - `start_datetime`
  - `end_datetime`
- Location (optional):
  - address fields or a single location note
- Notes (optional)
- Caretaker selection:
  - Specific caretaker chosen, OR
  - “Any available caretaker” (if implemented)

### 4.2 Server-Side Validations (Required)
- Start must be before end
- Booking duration must be within reasonable limits (e.g., 1–12 hours) — configurable
- Client must be logged in and have role `client`
- Service must exist and be active
- If caretaker selected:
  - caretaker must be `verified`
  - caretaker must not have:
    - overlapping confirmed/in-progress bookings
    - overlapping approved leave
- If no caretaker selected:
  - booking may remain unassigned until HR/admin assigns one

### 4.3 Result
- Create `booking` record with status `requested` or `pending_payment`.
- Record creation metadata:
  - `created_by` (user id)
  - `created_at`

---

## 5. Caretaker Assignment

Caretaker assignment can be implemented in one of these ways:

### 5.1 Client-Selected Caretaker
- Client chooses caretaker during booking request.
- System validates caretaker availability.
- Booking can be immediately confirmed or remain requested depending on policy.

### 5.2 HR/Admin Assignment
- Booking created without caretaker.
- HR/admin selects a caretaker from a filtered list:
  - verified
  - available for the time window
  - offers requested service

### 5.3 Auto-Matching (Future Enhancement)
- System suggests top caretakers using ranking (availability, skills, rating, distance).
- HR/admin can approve suggestion (recommended for early versions).

---

## 6. Reschedule & Cancellation

### 6.1 Rescheduling (Optional)
Reschedule can be implemented as:
- Update `start/end` on the booking with a new conflict check, OR
- Create a new booking and cancel the old one (better for audit trails)

Required validations:
- same overlap checks as new bookings
- policy rules (e.g., can’t reschedule within 24h)

### 6.2 Cancellation
Cancellation rules typically depend on status:
- If `requested` / `pending_payment`: cancel anytime
- If `confirmed`: cancellation may require admin approval or apply fees
- If `in_progress`: cancellation should be restricted (or treated as dispute)

---

## 7. Booking Conflicts (Core Logic)

### 7.1 Overlap Check (Time Window)
Two intervals overlap if:
- `A.start < B.end` AND `B.start < A.end`

### 7.2 Conflicts to Check
When assigning or confirming a booking:
1. Overlapping **confirmed/in-progress** bookings for caretaker
2. Overlapping **approved leave** for caretaker
3. (Optional) caretaker availability schedule conflicts
4. (Optional) booking maximum per day/week constraints

---

## 8. Payment Integration (If Applicable)

### 8.1 Payment Models
**A) Pay-first**
- Booking created as `pending_payment`
- Only confirm after payment succeeds

**B) Pay-later**
- Booking can be `confirmed` without payment
- Payment collected later (manual or online)

### 8.2 Data to Store
In a `payments` table (recommended):
- booking_id
- amount, currency
- status (`pending`, `paid`, `failed`, `refunded`)
- provider (stripe/paypal/manual)
- provider_reference
- paid_at

### 8.3 Refund Handling (Optional)
Refunds can be:
- full refunds for early cancellations
- partial refunds for partial service
- admin-only action (recommended)

---

## 9. Reporting & KPIs (Optional)

Useful booking KPIs:
- bookings created per day/week/month
- completion rate
- cancellation rate
- average hours per booking
- revenue per service type
- caretaker utilization rate

---

## 10. Recommended Tables (Documentation)

Typical tables involved in booking:
- `services`
- `clients`
- `caretakers`
- `bookings`
- `booking_status_history` (recommended)
- `leave_requests`
- `payments` (if implemented)

For an example schema, see `DATABASE_SCHEMA.md`.

---

## 11. UI Pages (Suggested)

Client:
- New booking page
- Booking list (upcoming / past)
- Booking details page
- Payment page (if pay-first)

Caretaker:
- Upcoming bookings
- Booking details
- Mark in-progress / completed (if allowed)

HR/Admin:
- Booking management list
- Assign caretaker page
- Booking detail with status change controls
- Conflict resolution view (leave impacts)

---

## 12. Testing Checklist (Booking)

Minimum tests to run after changes:
- Client can create booking
- Booking rejects invalid time ranges
- Booking rejects unverified caretaker
- Booking rejects caretaker conflicts (leave + overlapping booking)
- Payment success confirms booking (if pay-first)
- Cancellation works and respects policy
- Role guards prevent unauthorized actions

See `TEST_CASES.md` for detailed cases.

---
