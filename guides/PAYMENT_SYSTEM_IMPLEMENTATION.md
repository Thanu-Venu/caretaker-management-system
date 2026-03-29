# SmartCare Payment System Implementation Guide

## Overview

This document describes the complete payment and billing system for the SmartCare Caretaker Management System, supporting four service bases: Hourly, Daily, Monthly, and Yearly.

## Table of Contents

1. [Payment Rules by Service Basis](#payment-rules-by-service-basis)
2. [Database Structure](#database-structure)
3. [Implementation Components](#implementation-components)
4. [Booking Flow](#booking-flow)
5. [Recurring Payment Flow](#recurring-payment-flow)
6. [Reminder System](#reminder-system)
7. [Grace Period & Auto-Cancellation](#grace-period--auto-cancellation)
8. [Migration & Setup](#migration--setup)

---

## Payment Rules by Service Basis

### Hourly Service
- **Advance Payment**: 100% of total amount
- **Recurring Payments**: None
- **Rules**:
  - Client must pay full amount before service begins
  - No recurring payments required
  - Service begins only after payment confirmation

### Daily Service
- **Duration Limit**: Minimum 1 day, maximum 30 days
- **Validation**: Bookings > 30 days must use Monthly Service
- **Advance Payment Rules**:
  - **< 15 days**: 100% advance payment
  - **15-30 days**: 15 days advance payment, remaining due before day 16

**Example (25 days booking at Rs. 2,000/day)**:
- Total: 25 × 2,000 = Rs. 50,000
- Advance (first 15 days): 15 × 2,000 = Rs. 30,000
- Remaining (10 days): 10 × 2,000 = Rs. 20,000
- Payment due: Day 16

### Monthly Service
- **Used when**: Service duration > 30 days
- **Advance Payment Rules**:
  - **< 6 months**: 1 month advance
  - **≥ 6 months**: 3 months advance
- **Recurring Payments**: After advance period ends, pay one month at a time

**Example (6 months at Rs. 30,000/month)**:
- Total: 6 × 30,000 = Rs. 180,000
- Advance: 3 × 30,000 = Rs. 90,000 (covers Month 1-3)
- Recurring: Rs. 30,000 due each month starting Month 4

### Yearly Service
- **Conversion**: Internally converted to monthly billing
  - `total_months = duration × 12`
- **Advance Payment Rules**:
  - **1 year**: 4 months advance
  - **> 1 year**: 6 months advance
- **Recurring Payments**: Monthly after advance period

**Example (1 year at Rs. 30,000/month)**:
- Total months: 12
- Advance: 4 months = Rs. 120,000 (covers Month 1-4)
- Recurring: Rs. 30,000 due monthly starting Month 5

---

## Database Structure

### Bookings Table (Updated)

New columns added:
```sql
service_start_date DATE          -- Agreed service start date
advance_paid_date DATETIME        -- When advance payment was completed
advance_months INT                -- Number of months covered by advance
total_months INT                  -- Total billing months (for yearly conversions)
advance_balance DECIMAL(10,2)     -- Monetary value of prepaid service period
```

### Recurring Payments Table (New)

```sql
CREATE TABLE recurring_payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  client_id INT NOT NULL,
  caretaker_id INT NOT NULL,
  cycle_number INT NOT NULL,
  cycle_type ENUM('monthly', '15_day', 'daily') NOT NULL,
  due_date DATE NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  status ENUM('pending', 'paid', 'overdue', 'cancelled') NOT NULL,
  paid_at DATETIME NULL,
  payment_id INT NULL,
  reminder_7_days_sent TINYINT(1) DEFAULT 0,
  reminder_3_days_sent TINYINT(1) DEFAULT 0,
  reminder_due_date_sent TINYINT(1) DEFAULT 0,
  grace_period_end DATE NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);
```

---

## Implementation Components

### 1. PaymentCalculationService
**Location**: `app/core/PaymentCalculationService.php`

**Key Methods**:
- `validateDailyBooking($basis, $duration)` - Validates daily booking ≤ 30 days
- `calculateAdvancePayment($booking)` - Calculates advance amount, balance, and recurring needs
- `generateRecurringPaymentSchedule($bookingId, $bookingData)` - Creates payment schedule

### 2. RecurringPaymentService
**Location**: `app/core/RecurringPaymentService.php`

**Key Methods**:
- `createRecurringPayments($bookingId, $bookingData, $schedule)` - Creates payment records
- `sendPaymentReminders()` - Sends 7, 3, 0 day reminders
- `markOverduePayments()` - Marks payments past due date as overdue
- `autoCancelUnpaidBookings()` - Cancels bookings past grace period

### 3. PaymentController
**Location**: `app/controllers/PaymentController.php`

**Updated Methods**:
- `validateBooking($bookingData)` - Validates booking before creation
- `calculatePaymentDetails($booking)` - Returns complete payment calculation
- `createRecurringPayments($bookingId, $bookingData)` - Creates recurring payment schedule

### 4. ClientModel
**Location**: `app/models/ClientModel.php`

**Updated**: `createBooking()` now includes new payment fields

**Added**: `updateBookingAdvancePaidDate()` sets timestamp when advance is paid

---

## Booking Flow

### Step 1: Booking Creation
```php
// ClientController::bookCaretaker()

// 1. Validate daily booking limit
$validation = PaymentController::validateBooking([
    'basis' => $basis,
    'duration' => $duration
]);

// 2. Calculate payment details
$paymentDetails = PaymentController::calculatePaymentDetails([
    'basis' => $basis,
    'duration' => $duration,
    'total_payment' => $total_payment,
    'service_start_date' => $booking_date
]);

// 3. Create booking with payment fields
$bookingData = [
    // ... other fields
    'advance_months' => $paymentDetails['advance_months'],
    'total_months' => $paymentDetails['total_months'],
    'advance_balance' => $paymentDetails['advance_balance']
];
```

### Step 2: Payment Submission
Client submits advance payment through payment form.
Status: `Requested` → `Payment_Requested` → `Advance_Paid`

### Step 3: Payment Approval
```php
// HrController::approvePayment()

// 1. Approve payment
$clientModel->updatePaymentStatus($paymentId, 'approved');

// 2. Update booking status
$clientModel->updateBookingStatus($bookingId, 'Accepted');

// 3. Set advance_paid_date
$clientModel->updateBookingAdvancePaidDate($bookingId);

// 4. Create recurring payments
PaymentController::createRecurringPayments($bookingId, $bookingDetails);
```

---

## Recurring Payment Flow

### Payment Schedule Creation

When advance payment is approved, recurring payment records are created:

**Monthly/Yearly Example**:
```
Service Start: 2026-04-01
Advance: 3 months (Apr, May, Jun)
Total: 6 months

Recurring Payments Created:
- Cycle 1: Due 2026-07-01, Amount: Rs. 30,000
- Cycle 2: Due 2026-08-01, Amount: Rs. 30,000
- Cycle 3: Due 2026-09-01, Amount: Rs. 30,000
```

**Daily (15-30 days) Example**:
```
Service Start: 2026-04-01
Duration: 25 days
Advance: 15 days

Recurring Payments Created:
- Cycle 1: Due 2026-04-16, Amount: Rs. 20,000 (remaining 10 days)
```

---

## Reminder System

### Automatic Reminders

Reminders are sent automatically via cron job:

1. **7 Days Before Due Date**
   - Notification to client
   - Notification to HR for monitoring

2. **3 Days Before Due Date**
   - Reminder to client
   - HR monitoring notification

3. **On Due Date**
   - Final reminder to client
   - HR alert

### Reminder Implementation

```php
// Runs daily via cron
$recurringService->sendPaymentReminders();
```

**Notification Example**:
```
Title: Payment Reminder - 7 Days
Message: Your payment of Rs. 30,000 is due in 7 days (July 1, 2026).
         Booking #12 | Elder Care
         Caretaker: Sunil Fernando

         Please ensure timely payment to continue the service.
```

---

## Grace Period & Auto-Cancellation

### Grace Period Rules

- **Duration**: 3 days after due date
- **During Grace Period**:
  - Service continues
  - Payment marked as "overdue"
  - Booking remains active

### Auto-Cancellation

If payment not completed after grace period:

1. **Booking Status**: Set to `Cancelled`
2. **Cancellation Reason**: "Auto-cancelled due to non-payment"
3. **Cancelled At**: Current timestamp
4. **Recurring Payments**: All pending payments marked as `cancelled`

### Notifications Sent

- **Client**: Service cancelled, rebook instructions
- **HR**: Booking cancelled, client details
- **Caretaker**: Booking cancelled, now available

**Example Timeline**:
```
Due Date: July 1, 2026
Grace Period: July 1-3, 2026
Auto-Cancel: July 4, 2026 (if still unpaid)
```

---

## Migration & Setup

### Step 1: Run Database Migrations

Execute in order:

1. **Add Payment Fields to Bookings**
   ```bash
   mysql -u root -p smartcare < database/migrations/01_add_payment_fields_to_bookings.sql
   ```

2. **Create Recurring Payments Table**
   ```bash
   mysql -u root -p smartcare < database/migrations/02_create_recurring_payments_table.sql
   ```

### Step 2: Setup Cron Job

Configure daily payment processing:

**Windows Task Scheduler**:
- Program: `C:\wamp64\bin\php\php8.x.x\php.exe`
- Arguments: `C:\wamp64\www\CMA\app\cron\process_recurring_payments.php`
- Trigger: Daily at 12:00 AM

**Linux Cron**:
```bash
0 0 * * * /usr/bin/php /path/to/CMA/app/cron/process_recurring_payments.php
```

### Step 3: Test the System

1. **Test Daily Validation**:
   - Try booking Daily service > 30 days (should fail)
   - Book Daily service for 20 days (should require 15 days advance)

2. **Test Payment Calculation**:
   - Create Monthly booking (6 months): Should calculate 3 months advance
   - Create Yearly booking (1 year): Should calculate 4 months advance

3. **Test Cron Job**:
   ```bash
   php app/cron/process_recurring_payments.php
   ```
   Check logs in `logs/payment_cron_YYYY-MM-DD.log`

---

## Summary of Business Rules

| Service Basis | Advance Rule | Max Duration | Recurring |
|--------------|--------------|--------------|-----------|
| Hourly | 100% advance | - | No |
| Daily (< 15 days) | 100% advance | 30 days | No |
| Daily (15-30 days) | 15 days advance | 30 days | Yes (1 payment) |
| Monthly (< 6 months) | 1 month advance | - | Yes |
| Monthly (≥ 6 months) | 3 months advance | - | Yes |
| Yearly (1 year) | 4 months advance | - | Yes (monthly) |
| Yearly (> 1 year) | 6 months advance | - | Yes (monthly) |

---

## Important Notes

1. **advance_balance** represents prepaid service value only, NOT a wallet
2. **Daily bookings > 30 days** must be rejected
3. **Grace period** is always 3 days after due date
4. **Reminders** sent at 7, 3, and 0 days before due date
5. **Yearly services** are internally converted to monthly billing
6. **Auto-cancellation** happens only after grace period expires

---

## File Structure

```
CMA/
├── app/
│   ├── controllers/
│   │   ├── PaymentController.php (Updated)
│   │   ├── ClientController.php (Updated)
│   │   └── HrController.php (Updated)
│   ├── models/
│   │   └── ClientModel.php (Updated)
│   ├── core/
│   │   ├── PaymentCalculationService.php (New)
│   │   └── RecurringPaymentService.php (New)
│   └── cron/
│       ├── process_recurring_payments.php (New)
│       └── README.md (New)
├── database/
│   └── migrations/
│       ├── 01_add_payment_fields_to_bookings.sql (New)
│       └── 02_create_recurring_payments_table.sql (New)
└── logs/
    └── payment_cron_YYYY-MM-DD.log (Auto-generated)
```

---

## Support & Troubleshooting

### Common Issues

**Issue**: Daily booking > 30 days not being rejected
- **Solution**: Ensure validation is called in `bookCaretaker()` method

**Issue**: Recurring payments not created
- **Solution**: Check that `createRecurringPayments()` is called in `approvePayment()`

**Issue**: Reminders not sent
- **Solution**: Verify cron job is running, check logs in `logs/` directory

**Issue**: Auto-cancellation not happening
- **Solution**: Confirm grace_period_end is being set correctly, check cron logs

---

For questions or issues, contact the development team or refer to the SmartCare technical documentation.
