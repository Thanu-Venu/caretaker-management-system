# SmartCare Refund Policy - Quick Reference

## Cancellation Fee
- **Fixed**: LKR 5,000
- **Percentage**: 5% of advance payment
- **Applied**: Whichever is higher

---

## Refund Formulas

### Before Service Start
```
Refund = Advance Paid - Cancellation Fee
```

### After Service Start
```
Refund = Total Paid - Used Service Value - Cancellation Fee

Used Service = (Months/Days Elapsed) × (Monthly/Daily Rate)
```

### During Recurring Stage
```
Refund = Total Paid - (Current Cycle + Completed Cycles) - Cancellation Fee

Note: Current cycle is non-refundable if started
```

### Daily Service
```
Refund = Total Paid - (Days Used × Daily Rate) - Cancellation Fee
```

### Auto-Cancellation (Non-Payment)
```
Refund = 0 (No refund applicable)
```

---

## Refund Status Flow

```
pending → approved → processed → completed
         ↓
         declined (no refund)
```

---

## Key Database Tables

### `refunds`
- Stores all refund calculations
- Tracks approval workflow
- Records payment details

### `bookings`
- New column: `refund_status`
- New column: `advance_amount`
- New column: `service_days_used`

---

## Quick Actions

### Client Cancels Booking
1. Navigate to bookings page
2. Click "Cancel" button
3. Enter cancellation reason
4. System calculates refund automatically
5. Wait for HR approval

### HR Processes Refund
1. Go to HR → Refunds
2. View pending refunds
3. Click "View Details"
4. Review calculation
5. Approve or Decline
6. If approved, mark as completed later

---

## Cancellation Types

| Type | When | Refund |
|------|------|--------|
| **before_service_start** | Before service start date | Full - Fee |
| **after_service_start** | Service started, within advance | Partial |
| **during_recurring** | In recurring payment stage | Future cycles only |
| **daily_service** | Daily bookings | Unused days |
| **auto_nonpayment** | Payment not made (3 days after due) | None |

---

## Grace Period
- **Duration**: 3 days after payment due date
- **Action**: Auto-cancel if payment not received
- **Refund**: None for auto-cancellation

---

## Notifications Sent

**Client receives:**
- Cancellation confirmation
- Refund amount
- Approval/decline status
- Completion notification

**HR receives:**
- New cancellation alert
- Refund approval required

**Caretaker receives:**
- Booking cancelled notification

---

## URLs

- Client Bookings: `/client/c_upcomingBookings`
- Client Cancelled: `/client/c_cancelledBookings`
- HR Refunds: `/hr/refunds`
- HR Refund Details: `/hr/refundDetails?refund_id={id}`

---

## Configuration File

`app/core/RefundCalculationService.php`

```php
CANCELLATION_FEE_FIXED = 5000
CANCELLATION_FEE_PERCENTAGE = 0.05
GRACE_PERIOD_DAYS = 3
```

Change these constants to adjust policy settings.

---

## Example Scenarios

### Scenario 1: Client cancels 3-month booking after 1 month
- **Total Paid**: LKR 90,000
- **Used**: 1 month = LKR 30,000
- **Fee**: LKR 5,000
- **Refund**: LKR 55,000

### Scenario 2: Client cancels 20-day booking after 8 days
- **Total Paid**: LKR 60,000 (15 days advance)
- **Daily Rate**: LKR 3,000
- **Used**: 8 days = LKR 24,000
- **Fee**: LKR 5,000
- **Refund**: LKR 31,000

### Scenario 3: Client cancels before service starts
- **Advance Paid**: LKR 45,000
- **Used**: LKR 0
- **Fee**: LKR 5,000
- **Refund**: LKR 40,000

---

## Common Questions

**Q: When will I get my refund?**
A: After HR approval (typically 1-3 business days) and processing (3-5 business days).

**Q: Can I cancel during the service?**
A: Yes, but you'll be charged for service already used plus cancellation fee.

**Q: What if payment is late?**
A: You have a 3-day grace period. After that, booking is auto-cancelled with no refund.

**Q: Is the cancellation fee always the same?**
A: No, it's either LKR 5,000 or 5% of advance payment, whichever is higher.

---

## Support

For refund inquiries:
- Contact HR department
- Check notifications for updates
- View refund status in cancelled bookings section
