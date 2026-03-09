# SmartCare Refund System - Testing Guide

## 🧪 Complete Testing Checklist

Follow this guide to test all aspects of the cancellation and refund system.

---

## Prerequisites

1. ✅ Database migration completed (refunds table created)
2. ✅ WAMP server running
3. ✅ SmartCare application accessible
4. ✅ Test accounts available (Client, HR, Caretaker)

---

## Test Suite 1: Before Service Start Cancellation

### Objective
Test refund calculation when client cancels before service begins.

### Steps

1. **Create Booking**
   - Login as Client
   - Navigate to Book Service
   - Create a booking for future date (e.g., 7 days from today)
   - Service: Elder Care
   - Basis: Monthly
   - Duration: 3 months
   - Total: LKR 120,000

2. **Make Advance Payment**
   - Wait for HR to request advance payment
   - Navigate to payment page
   - Enter payment details
   - Confirm payment

3. **Cancel Booking**
   - Go to Ongoing Bookings or Upcoming Bookings
   - Click "Cancel" button
   - Enter reason: "Personal emergency"
   - Confirm cancellation

### Expected Results

✅ Booking status changes to "Cancelled"
✅ Success message shows: "A refund of LKR 115,000 will be processed after HR approval"
✅ Client receives notification
✅ Refund record created in database
✅ Refund amount = 120,000 - 5,000 = LKR 115,000

### Verify in Database

```sql
-- Check booking cancelled
SELECT id, status, cancellation_reason, cancelled_at
FROM bookings
WHERE id = [BOOKING_ID];

-- Check refund created
SELECT * FROM refunds WHERE booking_id = [BOOKING_ID];

-- Should show:
-- cancellation_type: before_service_start
-- total_paid: 120000.00
-- service_used_amount: 0.00
-- cancellation_fee: 6000.00 (5% of 120,000)
-- refund_amount: 114000.00
-- status: pending
```

---

## Test Suite 2: After Service Start Cancellation

### Objective
Test refund calculation when service has already started.

### Steps

1. **Create Booking with Past Start Date**
   - Create booking with service_start_date 1 month ago
   - Or manually update database:
   ```sql
   UPDATE bookings
   SET service_start_date = DATE_SUB(CURDATE(), INTERVAL 30 DAY)
   WHERE id = [BOOKING_ID];
   ```

2. **Cancel Booking**
   - Login as Client
   - Cancel the booking
   - Enter reason: "Service no longer needed"

### Expected Results

✅ Refund calculation deducts 1 month of service
✅ If 3-month advance (LKR 135,000):
   - Monthly rate: LKR 45,000
   - Used: LKR 45,000 (1 month)
   - Fee: LKR 6,750
   - Refund: LKR 83,250

### Verify in Database

```sql
SELECT * FROM refunds WHERE booking_id = [BOOKING_ID];

-- Should show:
-- cancellation_type: after_service_start
-- service_used_amount: 45000.00
-- refund_amount: 83250.00
```

---

## Test Suite 3: Daily Service Cancellation

### Objective
Test refund for daily basis bookings.

### Steps

1. **Create Daily Booking**
   - Service: Maid
   - Basis: Daily
   - Duration: 20 days
   - Total: LKR 60,000

2. **Make Advance Payment**
   - 15 days advance = LKR 45,000

3. **Update Start Date (Simulate 8 Days Usage)**
   ```sql
   UPDATE bookings
   SET service_start_date = DATE_SUB(CURDATE(), INTERVAL 8 DAY)
   WHERE id = [BOOKING_ID];
   ```

4. **Cancel Booking**
   - Login as Client
   - Cancel booking

### Expected Results

✅ Daily rate: LKR 3,000
✅ Days used: 8
✅ Used amount: LKR 24,000
✅ Cancellation fee: LKR 5,000
✅ Refund: 45,000 - 24,000 - 5,000 = LKR 16,000

---

## Test Suite 4: HR Approval Workflow

### Objective
Test HR refund approval process.

### Steps

1. **Login as HR**
   - Navigate to HR dashboard
   - Click "Refunds" in menu (or go to /hr/refunds)

2. **View Refunds List**
   - Should see pending refunds dashboard
   - Statistics showing pending count
   - List of all refunds

3. **Filter Refunds**
   - Click "Pending" tab
   - Should show only pending refunds
   - Click "All" to see all statuses

4. **View Refund Details**
   - Click "View Details" on a pending refund
   - Should show:
     - Complete booking information
     - Client details
     - Calculation breakdown
     - Refund amount highlighted

5. **Approve Refund**
   - Enter notes: "Approved after verification"
   - Click "Approve Refund" button
   - Should redirect to refunds list
   - Success message displayed

6. **Mark as Completed**
   - Click "View Details" on approved refund
   - Enter:
     - Refund Method: Bank Transfer
     - Reference: TXN123456789
     - Notes: "Transferred to client account"
   - Click "Mark as Completed"

### Expected Results

✅ Refund status changes: pending → approved → completed
✅ Client receives notifications at each step
✅ HR actions logged in hr_logs table
✅ Refund details show approval information

### Verify in Database

```sql
-- Check refund status updated
SELECT id, status, approved_by, approved_at, processed_at, refund_method
FROM refunds
WHERE booking_id = [BOOKING_ID];

-- Check HR log
SELECT * FROM hr_logs
WHERE description LIKE '%Refund%'
ORDER BY created_at DESC
LIMIT 5;

-- Check notifications sent
SELECT * FROM notifications
WHERE user_id = [CLIENT_ID]
  AND title LIKE '%Refund%'
ORDER BY created_at DESC;
```

---

## Test Suite 5: HR Decline Refund

### Objective
Test refund decline workflow.

### Steps

1. **Create Test Cancellation**
   - Create and cancel a booking as client

2. **Login as HR**
   - Navigate to Refunds
   - View pending refund details

3. **Decline Refund**
   - Enter notes: "Cancellation outside policy terms"
   - Click "Decline Refund"

### Expected Results

✅ Refund status changes to "declined"
✅ Client receives decline notification with reason
✅ Refund amount not processed
✅ Booking remains cancelled

---

## Test Suite 6: Auto-Cancellation Due to Non-Payment

### Objective
Test automatic cancellation when recurring payment is overdue.

### Steps

1. **Create Long-Term Booking**
   - 6-month booking with 3-month advance
   - This creates recurring payments for months 4, 5, 6

2. **Simulate Overdue Payment**
   ```sql
   -- Find recurring payment
   SELECT * FROM recurring_payments
   WHERE booking_id = [BOOKING_ID]
     AND status = 'pending'
   LIMIT 1;

   -- Update to overdue with past grace period
   UPDATE recurring_payments
   SET status = 'overdue',
       due_date = DATE_SUB(CURDATE(), INTERVAL 10 DAY),
       grace_period_end = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
   WHERE id = [PAYMENT_ID];
   ```

3. **Run Auto-Cancel Script**
   ```bash
   # Navigate to cron folder
   cd c:\wamp64\www\CMA\app\cron

   # Run the script
   php process_recurring_payments.php
   ```

### Expected Results

✅ Booking status changes to "Cancelled"
✅ Cancellation reason: "Auto-cancelled due to non-payment"
✅ All pending recurring payments cancelled
✅ Refund record created with amount = 0
✅ Client, HR, and Caretaker notified

### Verify

```sql
-- Check booking cancelled
SELECT id, status, cancellation_reason
FROM bookings
WHERE id = [BOOKING_ID];

-- Check refund created with 0 amount
SELECT * FROM refunds
WHERE booking_id = [BOOKING_ID];

-- Should show:
-- cancellation_type: auto_nonpayment
-- refund_amount: 0.00
```

---

## Test Suite 7: Notification System

### Objective
Verify all notifications are sent correctly.

### Cancellation Notifications

1. **Client Notification**
   - Check notifications table for client
   - Should include:
     - Cancellation confirmation
     - Refund amount (if applicable)
     - Message about HR approval pending

2. **HR Notification**
   - Check notifications for HR user
   - Should include:
     - Booking cancellation alert
     - Refund amount
     - Action required message

3. **Caretaker Notification**
   - Check notifications for caretaker
   - Should include:
     - Booking cancelled by client
     - Service no longer active

### Refund Status Notifications

1. **Approval Notification**
   - Client receives approval confirmation
   - Refund amount confirmed
   - Processing timeline mentioned

2. **Completion Notification**
   - Client receives completion notice
   - Refund method and reference included
   - Timeline for funds to arrive

### Verify in Database

```sql
-- Client notifications
SELECT * FROM notifications
WHERE user_id = [CLIENT_ID]
  AND role = 'client'
ORDER BY created_at DESC;

-- HR notifications
SELECT * FROM notifications
WHERE role = 'Manager'
  AND title LIKE '%Refund%' OR title LIKE '%Cancel%'
ORDER BY created_at DESC;

-- Caretaker notifications
SELECT * FROM notifications
WHERE user_id = [CARETAKER_ID]
  AND role = 'caretaker'
ORDER BY created_at DESC;
```

---

## Test Suite 8: Edge Cases

### Test 8.1: Hourly Service Cancellation

```sql
-- Create hourly booking
-- Total paid upfront: LKR 2,000 (4 hours)
-- Cancel before service
-- Expected refund: 2,000 - 5,000 = LKR 0 (cancellation fee too high)
-- OR 2,000 - 100 = 1,900 if using 5% rule
```

### Test 8.2: Same Day Cancellation

- Create booking for today
- Make payment
- Cancel immediately
- Should count as "before service start"

### Test 8.3: Multiple Cancellations by Same Client

- Create multiple bookings
- Cancel all of them
- Each should have separate refund record
- HR can process independently

### Test 8.4: Cancellation with Custom Hours

- Book service with customization
- Verify refund includes customization cost
- Check calculation is correct

---

## Test Suite 9: UI/UX Testing

### Client Interface

1. ✅ Cancel button visible on eligible bookings
2. ✅ Modal opens smoothly
3. ✅ Required fields validated
4. ✅ Success message clear and informative
5. ✅ Refund amount displayed prominently

### HR Interface

1. ✅ Refunds menu accessible from dashboard
2. ✅ Dashboard statistics accurate
3. ✅ Filters work correctly
4. ✅ Table displays all necessary information
5. ✅ Details page shows complete breakdown
6. ✅ Forms validate inputs
7. ✅ Actions logged properly

---

## Test Suite 10: Security Testing

### Authorization

1. ✅ Client cannot access HR refunds page
2. ✅ Client cannot approve own refunds
3. ✅ Only HR role can access refund management
4. ✅ Caretaker cannot modify refunds

### Data Validation

1. ✅ SQL injection prevention (prepared statements)
2. ✅ XSS prevention (htmlspecialchars in views)
3. ✅ CSRF protection (POST methods only)
4. ✅ Input validation on all forms

---

## Performance Testing

### Load Testing

1. Create 50 test bookings
2. Cancel all bookings
3. Check:
   - Response time acceptable
   - No memory errors
   - Database queries optimized

### Database Query Performance

```sql
-- Check query execution time
EXPLAIN SELECT * FROM refunds
WHERE status = 'pending'
ORDER BY created_at DESC;

-- Should use indexes
```

---

## Regression Testing

After implementation, verify existing features still work:

1. ✅ Normal booking flow
2. ✅ Payment processing
3. ✅ Recurring payments
4. ✅ Leave requests
5. ✅ Reschedule requests
6. ✅ Caretaker change requests

---

## Testing Checklist Summary

| Test Suite | Status | Notes |
|------------|--------|-------|
| Before Service Start | ⬜ | Test ID: _____ |
| After Service Start | ⬜ | Test ID: _____ |
| Daily Service | ⬜ | Test ID: _____ |
| HR Approval | ⬜ | Test ID: _____ |
| HR Decline | ⬜ | Test ID: _____ |
| Auto-Cancellation | ⬜ | Test ID: _____ |
| Notifications | ⬜ | All parties notified |
| Edge Cases | ⬜ | See section 8 |
| UI/UX | ⬜ | All interfaces working |
| Security | ⬜ | No vulnerabilities found |

---

## Bug Reporting

If you find any issues during testing:

1. **Note the Test ID**
2. **Record Steps to Reproduce**
3. **Capture Error Messages**
4. **Check Logs:**
   - `C:\wamp64\logs\php_error.log`
   - `C:\wamp64\logs\mysql.log`
5. **Database State:**
   ```sql
   SELECT * FROM bookings WHERE id = [ID];
   SELECT * FROM refunds WHERE booking_id = [ID];
   ```

---

## Success Criteria

The refund system implementation is considered successful when:

✅ All test suites pass
✅ No critical bugs found
✅ Performance acceptable
✅ Security validated
✅ All notifications working
✅ HR workflow smooth
✅ Client experience positive
✅ Database integrity maintained

---

## Post-Testing Tasks

After successful testing:

1. ✅ Update user documentation
2. ✅ Train HR staff on new features
3. ✅ Inform clients about refund policy
4. ✅ Monitor system for first week
5. ✅ Gather user feedback
6. ✅ Make adjustments if needed

---

**Happy Testing! 🎉**

For any questions or issues, refer to:
- `REFUND_POLICY_IMPLEMENTATION.md`
- `REFUND_QUICK_REFERENCE.md`
- `REFUND_SYSTEM_SUMMARY.md`
