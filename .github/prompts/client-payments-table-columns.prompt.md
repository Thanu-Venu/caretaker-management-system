---
description: "Update the client payments table to show both the full payment and amount paid columns"
name: "Client Payments Table Columns"
argument-hint: "Use the current workspace or selected files to update the client past bookings/payment table"
agent: "agent"
---
Inspect the current client payments UI and update the past bookings table so it clearly shows both:

- the full payment amount
- the amount paid by the client

Use the current workspace context and any selected files to:

- find where the client past bookings table is rendered
- identify the data fields for total/full payment and paid amount
- keep the existing booking/payment filters and tab behavior unchanged
- make the two payment values easy to compare at a glance
- preserve the existing layout conventions used on client pages

Use examples like a booking with full payment 34500 and amount paid 4600 to guide the display logic and labels.

Return the result as:

1. The files that should change
2. The table columns or labels that should appear
3. Any missing data fields or assumptions
4. A brief implementation plan

If the codebase does not currently store both values separately, say so explicitly and suggest the smallest safe fallback.