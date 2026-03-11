# Bug Report Template — Caretaker Management System

Use this template to report bugs consistently. Copy/paste the sections below into a GitHub Issue.

---

## Title

**[Module] Short description of the issue**

Examples:
- `[Auth] Login fails with valid credentials on PHP 8.2`
- `[Booking] Overlapping booking allowed for same caretaker`
- `[Payments] Payment marked as paid but booking stays pending`
- `[Admin] HR can access admin-only user management page`

---

## Summary

Describe what is wrong in 1–3 sentences.

---

## Environment

- Date (YYYY-MM-DD):
- App version / branch / commit:
- Deployment:
  - [ ] Local (XAMPP/WAMP/LAMP/MAMP)
  - [ ] Staging
  - [ ] Production
- OS:
- Browser (if UI issue):
- PHP version:
- MySQL/MariaDB version:

---

## Affected Module(s)

Select all that apply:
- [ ] User Authentication
- [ ] Admin Panel
- [ ] HR Panel
- [ ] Caretaker Management
- [ ] Client Dashboard
- [ ] Booking System
- [ ] Leave Management
- [ ] Payments
- [ ] Notifications
- [ ] Reports
- [ ] UI/CSS
- [ ] Database

---

## Preconditions / Test Data

What must be true before reproducing the issue?
- Example: “Caretaker A is verified”
- Example: “Client account exists and is logged in”
- Example: “Booking exists with status confirmed”
- Example: “Leave request exists with status approved”
- Example: “Service ‘Babysitting’ exists”

If applicable, include IDs:
- User ID(s):
- Caretaker ID:
- Client ID:
- Booking ID:
- Leave Request ID:
- Payment ID:

---

## Steps to Reproduce

1.
2.
3.
4.

---

## Actual Result

What actually happened?

---

## Expected Result

What should have happened?

---

## Severity / Impact

Choose one:
- [ ] Critical (system unusable / security issue / data loss)
- [ ] High (major feature broken)
- [ ] Medium (workaround exists)
- [ ] Low (minor issue / cosmetic)

Explain impact briefly:
- Who is affected?
- How often does it occur?

---

## Evidence

Attach any relevant evidence:
- Screenshots / screen recordings
- Console output (browser devtools)
- PHP error output
- Server logs (Apache/Nginx/PHP)
- MySQL errors

Include snippets if possible:

```text
Paste logs here
```

---

## Suspected Cause (Optional)

If you have an idea where the issue might be:
- suspected file(s):
- suspected function(s):
- suspected query/table:

---

## Workaround (Optional)

Is there a temporary workaround?

---

## Additional Context

Anything else useful for debugging (recent changes, related issues, etc.).

---

## Regression?

- [ ] This worked before (regression)
- If yes, last known working version/commit/date:

---
