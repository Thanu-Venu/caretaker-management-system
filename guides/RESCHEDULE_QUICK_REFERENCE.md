## Reschedule Feature - Quick Reference

### Validation Flow (Client Request)
```
Client clicks "Reschedule"
  ↓
canReschedule() model method
  ↓
✓ Booking exists?
✓ Belongs to client?
✓ Status = 'Requested'?
✓ No prior reschedule?
✓ Date not in past?
✓ 24h advance notice?
  ↓
✓ Caretaker available?
✓ Caretaker not on leave?
  ↓
Create request + Update status
  ↓
Notify HR
```

### HR Approval Flow
```
HR reviews request
  ↓
Click "Approve" with hr_note
  ↓
BEGIN TRANSACTION
  ↓
1. Update bookings.booking_date
2. Update reschedule_requests.status = 'approved'
  ↓
COMMIT (or ROLLBACK on error)
  ↓
Revert booking status to 'Requested'
  ↓
Notify client + caretaker
  ↓
Client continues normal workflow (payment, etc.)
```

### Business Rules Summary
| Rule | Validation Point | Error Message |
|------|-----------------|---------------|
| Only 'Requested' status | Model + View | "Only bookings with 'Requested' status can be rescheduled" |
| One reschedule per booking | Model | "Only one reschedule is allowed per booking" |
| Must own booking | Model | "You do not have permission" |
| Not in past | Model | "The new date cannot be in the past" |
| 24h advance | Model | "Must be made at least 24 hours in advance" |

### Key Methods
```php
// Model (RescheduleRequestModel)
$validation = $model->canReschedule($bookingId, $clientId, $newDate);
if (!$validation['valid']) {
    echo $validation['error'];  // User-friendly message
}

$hasRequest = $model->hasRescheduleRequest($bookingId);  // bool

$count = $model->getRescheduleCountForBooking($bookingId);  // int

$bookingId = $model->approveRequest($requestId, $hrNote);  // Uses transaction
```

### Database Migration (REQUIRED FIRST!)
```sql
ALTER TABLE `reschedule_requests`
ADD COLUMN `hr_note` TEXT NULL AFTER `reason`;
```

### View Logic (c_upcomingBookings.php)
```php
// Only show reschedule button if:
if ($booking['status'] === 'Requested' && !$hasRescheduleRequest) {
    // Show active button
} elseif ($booking['status'] === 'Requested') {
    // Show disabled button with tooltip
} else {
    // Hide button
}
```

### Files Changed
1. `database/migrations/add_hr_note_to_reschedule_requests.sql` ← RUN THIS FIRST
2. `app/models/RescheduleRequestModel.php` (+100 lines, 3 new methods)
3. `app/controllers/ClientController.php` (rescheduleBooking refactored)
4. `app/controllers/HrController.php` (approveReschedule enhanced)
5. `app/views/client/c_upcomingBookings.php` (conditional rendering + modal)
