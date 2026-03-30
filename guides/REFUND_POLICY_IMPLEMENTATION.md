# SmartCare Cancellation and Refund Policy - Implementation Guide

## Overview

This document describes the complete implementation of the SmartCare cancellation and refund policy system. The system automatically calculates refunds based on cancellation timing, service usage, and payment status.

---

## Policy Rules Summary

### 1. Cancellation Before Service Start
- **Refund**: Advance Paid - Cancellation Fee
- **Cancellation Fee**: LKR 5,000 (or 5% of advance, whichever is higher)
- **Status**: Booking marked as 'Cancelled', future schedules removed

### 2. Cancellation After Service Start
- **Refund**: Total Paid - Used Service Value - Cancellation Fee
- **Used Service**: Calculated based on months/days elapsed since service start
- **Only unused prepaid portion refunded**

### 3. Cancellation During Recurring Payment Stage
- **Current Cycle**: Non-refundable if already started
- **Future Cycles**: Refundable if already paid but not yet used
- **Unpaid Cycles**: Simply cancelled, no charges

### 4. Daily Service Cancellation
- **Before Start**: Full refund minus cancellation fee
- **After Start**: Used days deducted, unused prepaid days refunded

### 5. Auto-Cancellation (Non-Payment)
- **Grace Period**: 3 days after due date
- **Refund**: None - current unpaid cycle is non-refundable
- **Notification**: All parties notified automatically

---

## Database Schema

### Refunds Table (`refunds`)
Tracks all refund calculations and processing status.

```sql
CREATE TABLE `refunds` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `booking_id` INT NOT NULL,
  `client_id` INT NOT NULL,
  `cancellation_type` ENUM(...),
  `total_paid` DECIMAL(10,2),
  `service_used_amount` DECIMAL(10,2),
  `cancellation_fee` DECIMAL(10,2),
  `refund_amount` DECIMAL(10,2),
  `refund_calculation` TEXT,
  `status` ENUM('pending', 'approved', 'declined', 'processed', 'completed'),
  -- Additional tracking fields
);
```

### Bookings Table Updates
Added columns:
- `refund_status`: Tracks refund processing status
- `advance_amount`: Stores advance payment received
- `service_days_used`: Days/months of service used

---

## Core Components

### 1. RefundCalculationService (`app/core/RefundCalculationService.php`)

Main service class that handles all refund calculations.

#### Key Methods:

**`calculateRefund($bookingId, $cancellationReason, $isAutoCancellation)`**
- Determines cancellation type
- Calculates appropriate refund amount
- Returns detailed calculation breakdown

**`createRefundRecord($refundData)`**
- Stores refund record in database
- Updates booking refund status
- Returns refund ID

**`updateRefundStatus($refundId, $status, $userId, $notes, $refundMethod, $refundReference)`**
- HR/Admin approves or declines refund
- Marks refund as processed/completed
- Updates booking accordingly

#### Cancellation Types:

1. **before_service_start**: Service not yet started
2. **after_service_start**: Service started, within advance period
3. **during_recurring**: Advance period over, in recurring stage
4. **daily_service**: Daily basis bookings
5. **auto_nonpayment**: Automatic cancellation due to payment failure

---

## Client-Side Implementation

### ClientController Updates

**`cancelBooking()` Method**
```php
1. Validate request
2. Calculate refund using RefundCalculationService
3. Cancel booking in database
4. Create refund record
5. Cancel future recurring payments
6. Send notifications to client, caretaker, HR
7. Display success message with refund amount
```

### Cancellation Flow

```
Client clicks "Cancel" →
Enter reason →
System calculates refund →
Booking cancelled →
Refund record created (status: pending) →
Notifications sent →
HR reviews and approves
```

---

## HR/Admin Interface

### Pages Created

#### 1. Refund Management (`hr/refunds`)
- Lists all refunds with filtering
- Shows statistics (pending, approved, total amount)
- Filter by status: All, Pending, Approved, Completed, Declined

#### 2. Refund Details (`hr/refundDetails`)
- Full refund calculation breakdown
- Booking and client information
- Approve/Decline actions (for pending refunds)
- Mark as completed (for approved refunds)

### HR Actions

**Pending Refunds:**
- Review calculation details
- Approve or decline with notes
- Client notified automatically

**Approved Refunds:**
- Enter refund method (Bank Transfer, Cash, etc.)
- Add transaction reference
- Mark as completed
- Client notified of completion

---

## Notification System

### Automatic Notifications Sent To:

**Client:**
- Booking cancelled confirmation
- Refund amount (if applicable)
- Refund status updates (approved/declined/completed)

**Caretaker:**
- Booking cancelled by client
- Schedule updated

**HR/Manager:**
- New cancellation requiring refund approval
- Action required alerts

---

## Recurring Payment Integration

### Auto-Cancellation Due to Non-Payment

**Process:**
1. Recurring payment overdue
2. 3-day grace period starts
3. If not paid within grace period:
   - Booking status → 'Cancelled'
   - All pending/overdue payments → 'cancelled'
   - Refund calculation → No refund (auto_nonpayment type)
   - Notifications sent to all parties

**Implementation:**
- Located in `RecurringPaymentService::autoCancelUnpaidBookings()`
- Runs via cron job (daily)
- No refund issued for non-payment cancellations

---

## Installation Instructions

### Step 1: Run Database Migration

```bash
# Navigate to database migrations folder
cd database/migrations

# Run the refunds table migration
mysql -u root -p smartcare < 05_create_refunds_table.sql
```

### Step 2: Verify Tables Created

```sql
USE smartcare;
SHOW TABLES LIKE 'refunds';
DESCRIBE refunds;
DESCRIBE bookings; -- Check for new columns
```

### Step 3: Test Cancellation Flow

1. Create a test booking
2. Make advance payment
3. Cancel booking from client dashboard
4. Check refund record created
5. Login as HR and approve refund
6. Verify notifications sent

---

## Configuration

### Cancellation Fee Settings

Located in `RefundCalculationService.php`:

```php
private const CANCELLATION_FEE_FIXED = 5000; // LKR 5,000
private const CANCELLATION_FEE_PERCENTAGE = 0.05; // 5%
private const GRACE_PERIOD_DAYS = 3;
```

To change cancellation fee:
1. Update constants in RefundCalculationService
2. System will use whichever is higher: fixed or percentage

---

## Refund Calculation Examples

### Example 1: Before Service Start
```
Advance Paid: LKR 90,000
Cancellation Fee: LKR 5,000
Refund: 90,000 - 5,000 = LKR 85,000
```

### Example 2: After Service Start (Monthly)
```
Advance Paid: LKR 90,000 (3 months)
Monthly Rate: LKR 30,000
Service Used: 1 month = LKR 30,000
Cancellation Fee: LKR 5,000
Refund: 90,000 - 30,000 - 5,000 = LKR 55,000
```

### Example 3: Daily Service
```
Total Paid: LKR 30,000 (for 15 days)
Daily Rate: LKR 2,000
Days Used: 8 days = LKR 16,000
Cancellation Fee: LKR 5,000
Refund: 30,000 - 16,000 - 5,000 = LKR 9,000
```

### Example 4: Auto-Cancellation (Non-Payment)
```
Refund: LKR 0
Reason: Cancelled due to non-payment after grace period
Current unpaid cycle is non-refundable
```

---

## API Endpoints

### Client Endpoints

- `POST /client/cancelBooking` - Cancel booking and create refund

### HR Endpoints

- `GET /hr/refunds` - List all refunds (with status filter)
- `GET /hr/refundDetails?refund_id={id}` - View refund details
- `POST /hr/processRefund` - Approve or decline refund
- `POST /hr/completeRefund` - Mark refund as completed

---

## Testing Checklist

- [ ] Cancellation before service start
- [ ] Cancellation after service start (monthly)
- [ ] Cancellation after service start (daily)
- [ ] Cancellation during recurring payment stage
- [ ] Auto-cancellation due to non-payment
- [ ] Hourly service cancellation
- [ ] HR approval workflow
- [ ] HR decline workflow
- [ ] Refund completion workflow
- [ ] Client notifications
- [ ] HR notifications
- [ ] Caretaker notifications

---

## Troubleshooting

### Issue: Refund amount showing 0

**Check:**
- Is `advance_balance` or `advance_amount` populated in bookings table?
- Has advance payment been made?
- Is service status 'Advance_Paid' or 'Accepted'?

### Issue: Refund record not created

**Check:**
- Database connection working?
- `refunds` table exists?
- Foreign key constraints satisfied?

### Issue: HR cannot see refunds

**Check:**
- HR logged in with correct role ('Manager')?
- Refunds exist in database?
- Check session and permissions

---

## Future Enhancements

1. **Email Notifications**: Send email in addition to in-app notifications
2. **SMS Alerts**: Alert clients via SMS for refund updates
3. **Bank Integration**: Automatic bank transfer processing
4. **Refund Reports**: Generate monthly refund reports
5. **Policy Customization**: Allow admin to configure cancellation fees
6. **Partial Refunds**: Support manual partial refund adjustments

---

## Support

For issues or questions regarding the refund system:
- Check system logs in `logs/` directory
- Review `hr_logs` table for HR actions
- Contact system administrator

---

## Policy Compliance

This implementation ensures:
- ✅ Clients not charged for unused services
- ✅ Caregivers compensated for completed work
- ✅ Service provider protected against sudden cancellations
- ✅ Transparent and fair refund calculations
- ✅ Full audit trail of all refund decisions

---

**Implementation Date:** March 7, 2026
**Version:** 1.0
**System:** SmartCare Caretaker Management System
