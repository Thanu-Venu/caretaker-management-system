# Future Features (Enhanced) — Caretaker Management System

This document is an expanded roadmap of potential enhancements for the Caretaker Management System. Items are grouped by theme and include suggested scope and implementation notes where helpful.

> Tip: If you want, I can convert the **Top Priority Roadmap** section into GitHub Issues (with clear acceptance criteria) once you confirm which features you want first.

---

## 0. Top Priority Roadmap (Suggested)

### Phase 1 — Stability & Trust (High value, low risk)
1. **Availability + conflict prevention**
   - Prevent double-booking and booking during approved leave.
2. **Email notifications**
   - Booking created/confirmed/cancelled, leave approved/rejected, payment receipt.
3. **Audit logs for privileged actions**
   - Verification decisions, role changes, booking status changes, payment state changes.
4. **Improved validation + consistent error handling**
   - Standardize server-side validation and user-facing messages.

### Phase 2 — Product Experience
5. **Reviews & ratings**
6. **Reporting dashboard + CSV exports**
7. **In-app messaging (client ↔ caretaker)**

### Phase 3 — Scale & Automation
8. **Smart matching**
9. **Caretaker payout workflow**
10. **Public API + mobile app**

---

## 1. Booking & Scheduling Enhancements

### 1.1 Availability Calendar (Caretaker)
**What**
- Calendar UI to manage availability with recurring rules (weekly schedule) + exceptions.
- “Unavailable” blocks (appointments, personal time).

**Implementation ideas**
- Tables: `availability_rules`, `availability_exceptions`
- Compute availability at booking time (and cache results for common searches).

**Acceptance ideas**
- Caretaker cannot be assigned to bookings outside available times.

---

### 1.2 Smart Matching (Client → Caretaker)
**What**
- Suggest caretakers based on:
  - service/skills match
  - availability window
  - price range
  - rating (if reviews exist)
  - distance (if location is captured)

**Implementation ideas**
- Ranking score = weighted factors
- Provide “why recommended” explanation

---

### 1.3 Recurring Bookings
**What**
- Weekly/monthly recurring bookings for regular clients.

**Implementation ideas**
- Store recurrence rules (RRULE-like) and generate instances.
- Support pausing/cancelling a single occurrence.

---

### 1.4 Reschedule Flow
**What**
- Rescheduling with conflict checks and policy rules (e.g., 24h notice).

**Implementation ideas**
- Prefer “create new booking + cancel old booking” for audit clarity.

---

### 1.5 Booking Chat / Timeline
**What**
- Booking-specific conversation thread and timeline:
  - status changes
  - notes
  - attachments

---

## 2. Payments, Billing & Finance

### 2.1 Online Payments + Webhooks
**What**
- Stripe/PayPal checkout integration
- Webhook verification to prevent fake “success” redirects

**Implementation ideas**
- Store `provider_reference`, `provider_event_id`
- Idempotency keys for webhook processing

---

### 2.2 Invoices & Receipts (PDF)
**What**
- PDF invoice + receipt generation
- Email delivery to client
- Invoice numbering system

---

### 2.3 Refunds & Disputes
**What**
- Admin-driven refund flow
- Dispute workflow with evidence attachments and notes

**Implementation ideas**
- Tables: `refunds`, `disputes`
- Restrict refund permissions, log actor and reason

---

### 2.4 Caretaker Payouts (If Applicable)
**What**
- Earnings dashboard and payout cycles (weekly/monthly)

**Implementation ideas**
- Tables: `payouts`, `payout_items`
- Export payouts (CSV) for finance ops
- Optional bank transfer integration later

---

## 3. HR, Verification & Compliance

### 3.1 Verification Checklist + Notes
**What**
- HR verification page with structured checklist items:
  - identity verified
  - certifications checked
  - interview completed (optional)
- Add internal notes

---

### 3.2 Document Management + Expiration Reminders
**What**
- Upload caretaker documents (IDs/certificates)
- Track expiry dates and notify caretaker/HR before expiry

**Implementation ideas**
- Table: `caretaker_documents` with `expires_at`
- Cron job (or scheduled task) to send reminders

---

### 3.3 Background Checks Integration (Optional)
**What**
- Integrate with a third-party screening provider
- Store check status + results summary

---

### 3.4 Training & Onboarding Modules
**What**
- Required training modules per service type
- Completion tracking and gating (can’t accept certain bookings without training)

---

## 4. Leave Management Improvements

### 4.1 Leave Types & Policies
**What**
- Leave type: sick/vacation/unpaid
- Policy rules: max days, blackout dates, documentation requirements

---

### 4.2 Leave/Booking Conflict Resolution Tools
**What**
- When approving leave:
  - show impacted future bookings
  - suggest replacement caretakers
  - optionally block approval until reassignment is planned

---

## 5. Communication & Notifications

### 5.1 Notification Preferences
**What**
- Let users choose which events trigger notifications and channels.

**Implementation ideas**
- Table: `notification_preferences`

---

### 5.2 SMS / WhatsApp Notifications (Optional)
**What**
- Critical booking updates via SMS/WhatsApp

**Implementation ideas**
- Use a provider (Twilio, etc.)
- Rate limit and opt-in/opt-out controls

---

### 5.3 In-App Messaging
**What**
- Secure client ↔ caretaker messages
- Attachments
- Admin/HR visibility rules

**Implementation ideas**
- Tables: `conversations`, `messages`, `message_attachments`

---

## 6. Reviews, Quality & Trust

### 6.1 Reviews & Ratings
**What**
- Client can rate caretaker after completed booking
- Optional review moderation by admin

**Implementation ideas**
- Only allow reviews for `completed` bookings.
- Table: `reviews` linked to `booking_id`.

---

### 6.2 Attendance / Check-in / Check-out (Optional)
**What**
- Caretaker check-in/out timestamps to confirm actual service duration.

**Implementation ideas**
- Store geo-location optionally (privacy careful)
- Use timestamps for accurate billing and dispute resolution

---

## 7. Administration, Reporting & Data Export

### 7.1 Advanced Reporting Dashboard
**What**
- Bookings by status, revenue trends, caretaker utilization

**Implementation ideas**
- Add indexes for heavy queries
- Cache summary stats (optional)

---

### 7.2 CSV Export & Import Tools
**What**
- Export: bookings, payments, users, caretakers, leave requests
- Import: services, caretaker list (for migration)

---

### 7.3 Audit Logs (Critical for Admin/HR Actions)
**What**
- Track actor/action/entity/before/after/time

**Implementation ideas**
- Table: `audit_logs`
- Log high-risk actions:
  - role changes, verification, refunds, cancellations

---

## 8. Security & Platform Hardening

### 8.1 CSRF Protection Everywhere
- Add CSRF tokens to all state-changing forms.

### 8.2 Rate Limiting
- Login attempt throttling
- Rate limit booking creation to prevent abuse

### 8.3 Stronger Permission Model
- Beyond roles: permissions/groups (finance admin, HR assistant, etc.)

### 8.4 Security Headers (If applicable)
- Content Security Policy (CSP)
- HSTS (HTTPS only)
- X-Frame-Options / frame-ancestors

---

## 9. Developer Experience & Quality

### 9.1 Standardize Architecture (MVC-ish)
**What**
- Consistent structure for controllers/services/models/views
- Centralize DB access (PDO recommended)
- Centralize auth guards

---

### 9.2 Testing Strategy (Optional but recommended)
- Unit tests for service rules (booking overlap, leave conflict)
- Integration tests for booking + payment lifecycle
- Smoke tests for dashboards

---

## 10. Localization & Accessibility

### 10.1 Multi-language Support
- Translation files
- Language switcher
- Admin-managed text (optional)

### 10.2 Accessibility Improvements
- Keyboard navigation
- Proper labels and ARIA attributes
- Contrast improvements

---

## 11. Reliability & Operations

### 11.1 Backups & Restore Procedure
- Automated DB backups
- Documented restore steps
- Periodic restore test

### 11.2 Maintenance Mode
- Admin can enable maintenance mode for upgrades
- Show a friendly downtime page to users

---

## 12. Ideas Parking Lot (Optional)

- Multi-branch support (multiple service locations/regions)
- Promotions / discount codes
- Membership/subscription plans
- Public caretaker profiles with SEO pages (if intended)
- Internal task assignments for HR/admin

---
