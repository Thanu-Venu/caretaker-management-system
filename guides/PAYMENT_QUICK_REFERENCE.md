# SmartCare Payment System - Quick Reference

## Payment Rules at a Glance

| Service Type | Duration | Advance Required | Recurring Payments |
|-------------|----------|------------------|-------------------|
| **Hourly** | Any | 100% upfront | None |
| **Daily** | 1-14 days | 100% upfront | None |
| **Daily** | 15-30 days | 15 days advance | Day 16 payment |
| **Daily** | > 30 days | ❌ NOT ALLOWED | Use Monthly instead |
| **Monthly** | < 6 months | 1 month advance | Monthly after advance |
| **Monthly** | ≥ 6 months | 3 months advance | Monthly after advance |
| **Yearly** | 1 year | 4 months advance | Monthly (12 total) |
| **Yearly** | > 1 year | 6 months advance | Monthly (duration × 12) |

---

## Key Features

### ✅ Automated Payment Reminders
- **7 days** before due date
- **3 days** before due date
- **On due date**

### ✅ Grace Period
- **3 days** after due date
- Service continues during grace period
- Payment shows as "Overdue"

### ✅ Auto-Cancellation
- Triggers after grace period expires
- Booking cancelled automatically
- Notifications sent to all parties

---

## Database Tables

### bookings (Updated)
New columns for payment tracking:
```
service_start_date    - When service begins
advance_paid_date     - When advance was paid
advance_months        - Months covered by advance
total_months          - Total billing months
advance_balance       - Value of prepaid period
```

### recurring_payments (New)
Tracks all future payments:
```
cycle_number          - Payment cycle (1, 2, 3...)
due_date              - When payment is due
amount                - Payment amount
status                - pending/paid/overdue/cancelled
grace_period_end      - 3 days after due date
reminder flags        - 7d, 3d, 0d sent status
```

---

## Code Usage Examples

### Validate Booking
```php
$validation = PaymentController::validateBooking([
    'basis' => 'Daily',
    'duration' => 25
]);

if (!$validation['valid']) {
    echo $validation['message'];
}
```

### Calculate Payment
```php
$details = PaymentController::calculatePaymentDetails([
    'basis' => 'Monthly',
    'duration' => 6,
    'total_payment' => 180000,
    'service_start_date' => '2026-04-01'
]);

echo "Advance: Rs. {$details['advance_amount']}";
echo "Remaining: Rs. {$details['remaining_balance']}";
echo "Next Due: {$details['next_payment_due']}";
```

### Create Recurring Payments
```php
// Called automatically when payment is approved
PaymentController::createRecurringPayments($bookingId, $bookingData);
```

### Display Payment Info (View)
```php
require_once APPROOT . '/core/PaymentHelper.php';

// Show payment description
echo PaymentHelper::getPaymentDescription($booking);

// Show payment schedule
$payments = $recurringService->getPendingPayments($bookingId);
echo PaymentHelper::displayPaymentSchedule($payments);

// Show next payment alert
$nextPayment = $recurringService->getNextDuePayment($bookingId);
echo PaymentHelper::getNextPaymentAlert($nextPayment);
```

---

## Cron Job Setup

### Windows Task Scheduler
```
Program: C:\wamp64\bin\php\php8.x.x\php.exe
Arguments: C:\wamp64\www\CMA\app\cron\process_recurring_payments.php
Trigger: Daily at 12:00 AM
```

### Linux Cron
```bash
0 0 * * * /usr/bin/php /path/to/CMA/app/cron/process_recurring_payments.php
```

### Manual Test
```bash
php app/cron/process_recurring_payments.php
```

Check logs: `logs/payment_cron_YYYY-MM-DD.log`

---

## Status Flow

### Booking Status
```
Requested
    ↓
Payment_Requested (advance payment link sent)
    ↓
Advance_Paid (client paid, awaiting HR approval)
    ↓
Accepted (HR approved, service active)
    ↓
Completed (service finished)

OR

Accepted → Cancelled (auto-cancelled after grace period)
```

### Payment Status
```
pending → overdue → (grace period) → auto-cancel booking

OR

pending → paid (client pays on time)
```

---

## Common Scenarios

### Scenario 1: Daily Booking (25 days)
```
Duration: 25 days
Rate: Rs. 2,000/day
Total: Rs. 50,000

Advance: 15 days = Rs. 30,000
Remaining: 10 days = Rs. 20,000

Timeline:
Day 1: Service starts
Day 15: Reminder sent
Day 16: Payment due
Day 16-18: Grace period
Day 19: Auto-cancel if not paid
```

### Scenario 2: Monthly Booking (6 months)
```
Duration: 6 months
Rate: Rs. 30,000/month
Total: Rs. 180,000

Advance: 3 months = Rs. 90,000
Covers: Month 1, 2, 3

Recurring payments:
Month 4: Rs. 30,000
Month 5: Rs. 30,000
Month 6: Rs. 30,000

Reminders: 7, 3, 0 days before each due date
Grace: 3 days after due date
```

### Scenario 3: Yearly Booking (1 year)
```
Duration: 1 year = 12 months
Rate: Rs. 30,000/month
Total: Rs. 360,000

Advance: 4 months = Rs. 120,000
Covers: Month 1-4

Recurring: 8 monthly payments of Rs. 30,000
Starting: Month 5

Total payments: 1 advance + 8 recurring
```

---

## Troubleshooting

### Issue: Daily booking > 30 days accepted
**Fix**: Check validation in ClientController::bookCaretaker()

### Issue: Recurring payments not created
**Fix**: Verify approvePayment() calls createRecurringPayments()

### Issue: Reminders not sent
**Fix**: Check cron job is running, verify logs

### Issue: Auto-cancel not working
**Fix**: Confirm grace_period_end is set, check cron logs

### Issue: Wrong advance amount calculated
**Fix**: Check PaymentCalculationService rules match requirements

---

## Files Modified/Created

### Created (9 files)
- `app/core/PaymentCalculationService.php`
- `app/core/RecurringPaymentService.php`
- `app/core/PaymentHelper.php`
- `app/cron/process_recurring_payments.php`
- `app/cron/README.md`
- `database/migrations/01_add_payment_fields_to_bookings.sql`
- `database/migrations/02_create_recurring_payments_table.sql`
- `PAYMENT_SYSTEM_IMPLEMENTATION.md`
- `IMPLEMENTATION_SUMMARY.md`

### Modified (4 files)
- `app/controllers/PaymentController.php`
- `app/controllers/ClientController.php`
- `app/controllers/HrController.php`
- `app/models/ClientModel.php`

---

## Deployment Checklist

- [ ] Run database migrations
- [ ] Test daily booking validation
- [ ] Test payment calculations
- [ ] Setup cron job
- [ ] Test cron job manually
- [ ] Verify logs directory writable
- [ ] Test advance payment flow
- [ ] Test recurring payment creation
- [ ] Test reminder notifications
- [ ] Test auto-cancellation

---

## Support

**Documentation**: See PAYMENT_SYSTEM_IMPLEMENTATION.md
**Implementation Details**: See IMPLEMENTATION_SUMMARY.md
**Cron Setup**: See app/cron/README.md

---

**Last Updated**: March 7, 2026
**Version**: 1.0
**Status**: ✅ Ready for Testing
