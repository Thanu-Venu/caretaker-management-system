# SmartCare Payment System - Testing Guide

## Pre-Testing Setup

### Step 1: Database Migration
```bash
# Connect to MySQL
mysql -u root -p

# Run migrations
mysql> use smartcare;
mysql> source C:/wamp64/www/CMA/database/migrations/01_add_payment_fields_to_bookings.sql;
mysql> source C:/wamp64/www/CMA/database/migrations/02_create_recurring_payments_table.sql;

# Verify
mysql> DESCRIBE bookings;
mysql> DESCRIBE recurring_payments;
```

**Expected Result**: New columns in bookings, new recurring_payments table exists

### Step 2: Verify Files
```bash
cd C:\wamp64\www\CMA

# Check new files exist
dir app\core\PaymentCalculationService.php
dir app\core\RecurringPaymentService.php
dir app\core\PaymentHelper.php
dir app\cron\process_recurring_payments.php
```

### Step 3: Create Logs Directory
```bash
mkdir logs
# Ensure writable permissions
```

---

## Test Suite 1: Daily Booking Validation

### Test 1.1: Daily Booking > 30 Days (Should Fail)
**Steps**:
1. Login as a client
2. Go to "Find Caretaker" page
3. Select a caretaker
4. Choose service: Maid
5. Choose basis: Daily
6. Enter duration: 31 days
7. Fill other required fields
8. Click "Book Now"

**Expected Result**:
- ❌ Error message: "Daily bookings are limited to 30 days. Please choose Monthly Service."
- Booking NOT created

**Actual Result**: _____________

---

### Test 1.2: Daily Booking 10 Days (100% Advance)
**Steps**:
1. Create Daily booking for 10 days
2. Check booking details

**Expected Result**:
- ✓ Booking created
- Advance required: 100% of total
- No recurring payments
- Status: "Requested"

**SQL Check**:
```sql
SELECT id, basis, duration, total_payment, advance_months, total_months, advance_balance
FROM bookings WHERE id = [BOOKING_ID];
```

**Expected Values**:
- advance_months: 0
- total_months: 0
- advance_balance: 0.00

**Actual Result**: _____________

---

### Test 1.3: Daily Booking 25 Days (15 Days Advance)
**Steps**:
1. Create Daily booking for 25 days with Rs. 2,000/day rate
2. Total should be Rs. 50,000

**Expected Result**:
- ✓ Booking created
- Advance: Rs. 30,000 (15 days)
- Remaining: Rs. 20,000 (10 days)
- advance_balance: Rs. 30,000

**SQL Check**:
```sql
SELECT * FROM bookings WHERE id = [BOOKING_ID];
```

**Actual Result**: _____________

---

## Test Suite 2: Monthly Booking Calculations

### Test 2.1: Monthly < 6 Months (1 Month Advance)
**Steps**:
1. Create Monthly booking for 4 months
2. Rate: Rs. 30,000/month
3. Total: Rs. 120,000

**Expected Result**:
- Advance: Rs. 30,000 (1 month)
- advance_months: 1
- total_months: 4
- advance_balance: Rs. 30,000
- Remaining: Rs. 90,000

**SQL Check**:
```sql
SELECT advance_months, total_months, advance_balance, total_payment
FROM bookings WHERE id = [BOOKING_ID];
```

**Actual Result**: _____________

---

### Test 2.2: Monthly ≥ 6 Months (3 Months Advance)
**Steps**:
1. Create Monthly booking for 8 months
2. Rate: Rs. 30,000/month
3. Total: Rs. 240,000

**Expected Result**:
- Advance: Rs. 90,000 (3 months)
- advance_months: 3
- total_months: 8
- advance_balance: Rs. 90,000
- Remaining: Rs. 150,000

**Actual Result**: _____________

---

## Test Suite 3: Yearly Booking Calculations

### Test 3.1: Yearly = 1 Year (4 Months Advance)
**Steps**:
1. Create Yearly booking for 1 year
2. Rate: Rs. 30,000/month
3. Total: Rs. 360,000

**Expected Result**:
- advance_months: 4
- total_months: 12
- Advance: Rs. 120,000
- Remaining: Rs. 240,000

**SQL Check**:
```sql
SELECT advance_months, total_months, advance_balance, total_payment
FROM bookings WHERE id = [BOOKING_ID];
```

**Actual Result**: _____________

---

### Test 3.2: Yearly > 1 Year (6 Months Advance)
**Steps**:
1. Create Yearly booking for 2 years
2. Rate: Rs. 30,000/month
3. Total: Rs. 720,000

**Expected Result**:
- advance_months: 6
- total_months: 24
- Advance: Rs. 180,000
- Remaining: Rs. 540,000

**Actual Result**: _____________

---

## Test Suite 4: Payment Flow & Recurring Payments

### Test 4.1: Advance Payment Approval
**Steps**:
1. Client creates Monthly booking (6 months)
2. Client submits advance payment
3. HR approves payment

**Expected Result**:
- Booking status: "Requested" → "Payment_Requested" → "Advance_Paid" → "Accepted"
- advance_paid_date: Set to current timestamp

**SQL Check**:
```sql
SELECT id, status, advance_paid_date
FROM bookings WHERE id = [BOOKING_ID];
```

**Actual Result**: _____________

---

### Test 4.2: Recurring Payments Creation
**After HR approves advance payment**:

**SQL Check**:
```sql
SELECT * FROM recurring_payments
WHERE booking_id = [BOOKING_ID]
ORDER BY cycle_number;
```

**Expected Result** (for 6 month booking with 3 month advance):
| cycle_number | due_date | amount | status |
|--------------|----------|--------|--------|
| 1 | [Month 4] | 30000 | pending |
| 2 | [Month 5] | 30000 | pending |
| 3 | [Month 6] | 30000 | pending |

**Actual Result**: _____________

---

## Test Suite 5: Cron Job Testing

### Test 5.1: Manual Cron Execution
**Steps**:
```bash
cd C:\wamp64\www\CMA\app\cron
php process_recurring_payments.php
```

**Expected Result**:
- Script runs without errors
- Log file created: `logs/payment_cron_[DATE].log`
- Console output shows processing steps

**Check Log**:
```bash
type C:\wamp64\www\CMA\logs\payment_cron_[DATE].log
```

**Actual Result**: _____________

---

### Test 5.2: Payment Reminders (7 Days)
**Setup**:
1. Create a recurring payment manually:
```sql
INSERT INTO recurring_payments
(booking_id, client_id, caretaker_id, cycle_number, cycle_type, due_date, amount, status, grace_period_end)
VALUES
([BOOKING_ID], [CLIENT_ID], [CARETAKER_ID], 1, 'monthly', DATE_ADD(CURDATE(), INTERVAL 7 DAY), 30000, 'pending', DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 7 DAY), INTERVAL 3 DAY));
```

2. Run cron job:
```bash
php app/cron/process_recurring_payments.php
```

**Expected Result**:
- Notification sent to client
- Notification sent to HR
- reminder_7_days_sent flag set to 1

**SQL Check**:
```sql
SELECT reminder_7_days_sent FROM recurring_payments WHERE id = [PAYMENT_ID];
```

**Check Notifications**:
```sql
SELECT * FROM notifications
WHERE user_id = [CLIENT_ID]
ORDER BY created_at DESC LIMIT 1;
```

**Actual Result**: _____________

---

### Test 5.3: Overdue Payment Marking
**Setup**:
1. Create recurring payment with past due date:
```sql
INSERT INTO recurring_payments
(booking_id, client_id, caretaker_id, cycle_number, cycle_type, due_date, amount, status, grace_period_end)
VALUES
([BOOKING_ID], [CLIENT_ID], [CARETAKER_ID], 1, 'monthly', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 30000, 'pending', DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 1 DAY), INTERVAL 3 DAY));
```

2. Run cron:
```bash
php app/cron/process_recurring_payments.php
```

**Expected Result**:
- Payment status changed to 'overdue'
- Booking still active

**SQL Check**:
```sql
SELECT status FROM recurring_payments WHERE id = [PAYMENT_ID];
-- Expected: overdue

SELECT status FROM bookings WHERE id = [BOOKING_ID];
-- Expected: Accepted (still active during grace period)
```

**Actual Result**: _____________

---

### Test 5.4: Auto-Cancellation
**Setup**:
1. Create overdue payment past grace period:
```sql
INSERT INTO recurring_payments
(booking_id, client_id, caretaker_id, cycle_number, cycle_type, due_date, amount, status, grace_period_end)
VALUES
([BOOKING_ID], [CLIENT_ID], [CARETAKER_ID], 1, 'monthly', DATE_SUB(CURDATE(), INTERVAL 5 DAY), 30000, 'overdue', DATE_SUB(CURDATE(), INTERVAL 2 DAY));

-- Ensure booking is active
UPDATE bookings SET status = 'Accepted' WHERE id = [BOOKING_ID];
```

2. Run cron:
```bash
php app/cron/process_recurring_payments.php
```

**Expected Result**:
- Booking status: 'Cancelled'
- cancellation_reason: 'Auto-cancelled due to non-payment'
- cancelled_at: Current timestamp
- All recurring payments for booking: 'cancelled'
- Notifications sent to client, HR, caretaker

**SQL Check**:
```sql
SELECT status, cancellation_reason, cancelled_at
FROM bookings WHERE id = [BOOKING_ID];
-- Expected: Cancelled, reason filled, timestamp set

SELECT COUNT(*) as cancelled_payments
FROM recurring_payments
WHERE booking_id = [BOOKING_ID] AND status = 'cancelled';
-- Expected: All payments cancelled

SELECT COUNT(*) as notifications_sent
FROM notifications
WHERE message LIKE '%auto%cancel%'
  AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR);
-- Expected: 3 (client, HR, caretaker)
```

**Actual Result**: _____________

---

## Test Suite 6: Edge Cases

### Test 6.1: Hourly Service
**Steps**:
1. Create Hourly booking for 5 hours

**Expected Result**:
- advance_months: 0
- total_months: 0
- advance_balance: 0.00
- Advance payment: 100% of total
- No recurring payments created

**Actual Result**: _____________

---

### Test 6.2: Exact 30 Days Daily Booking
**Steps**:
1. Create Daily booking for exactly 30 days

**Expected Result**:
- ✓ Booking created (not rejected)
- Advance: 15 days
- Recurring payment for remaining 15 days

**Actual Result**: _____________

---

### Test 6.3: Exactly 6 Months Monthly Booking
**Steps**:
1. Create Monthly booking for exactly 6 months

**Expected Result**:
- advance_months: 3 (not 1)
- Follows ≥ 6 months rule

**Actual Result**: _____________

---

## Test Suite 7: User Interface (Optional)

### Test 7.1: Payment Description Display
**Add to booking details page**:
```php
require_once APPROOT . '/core/PaymentHelper.php';
echo PaymentHelper::getPaymentDescription($booking);
```

**Expected Result**:
- Clear payment breakdown shown
- Advance amount displayed
- Recurring payment info shown

**Actual Result**: _____________

---

### Test 7.2: Payment Schedule Display
**Add to booking details**:
```php
$recurringService = new RecurringPaymentService();
$payments = $recurringService->getPendingPayments($bookingId);
echo PaymentHelper::displayPaymentSchedule($payments);
```

**Expected Result**:
- Table showing all payment cycles
- Due dates clearly visible
- "Pay Now" buttons for pending

**Actual Result**: _____________

---

## Summary Checklist

### Database
- [ ] Migration 01 executed successfully
- [ ] Migration 02 executed successfully
- [ ] New columns exist in bookings table
- [ ] recurring_payments table created
- [ ] Foreign keys working

### Validation
- [ ] Daily > 30 days rejected
- [ ] Error message displayed correctly

### Payment Calculations
- [ ] Hourly: 100% advance
- [ ] Daily < 15 days: 100% advance
- [ ] Daily 15-30 days: 15 days advance
- [ ] Monthly < 6 months: 1 month advance
- [ ] Monthly ≥ 6 months: 3 months advance
- [ ] Yearly 1 year: 4 months advance
- [ ] Yearly > 1 year: 6 months advance

### Recurring Payments
- [ ] Created when advance approved
- [ ] Correct cycle numbers
- [ ] Correct due dates
- [ ] Correct amounts

### Cron Job
- [ ] Runs without errors
- [ ] Logs created
- [ ] 7-day reminders sent
- [ ] 3-day reminders sent
- [ ] Due date reminders sent
- [ ] Overdue payments marked
- [ ] Auto-cancellation works
- [ ] Notifications sent

---

## Performance Testing

### Test Load
1. Create 100 bookings with recurring payments
2. Run cron job
3. Measure execution time

**Acceptable**: Under 60 seconds

**Actual**: _________ seconds

---

## Regression Testing

### Existing Features Still Work
- [ ] Normal booking creation
- [ ] Payment submission
- [ ] HR payment approval
- [ ] Booking cancellation
- [ ] Status updates
- [ ] Notifications

---

## Sign-Off

| Role | Name | Date | Signature |
|------|------|------|-----------|
| QA Tester | | | |
| Developer | | | |
| Database Admin | | | |
| Project Manager | | | |

---

**Test Date**: _______________
**Tester**: _______________
**Environment**: Development / Staging / Production
**Version**: 1.0

---

## Issue Tracking

| Test # | Issue Description | Severity | Status | Fixed By |
|--------|------------------|----------|--------|----------|
| | | | | |
| | | | | |
| | | | | |

---

End of Testing Guide
