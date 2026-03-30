# Reschedule Feature - Comprehensive Validation Implementation

## Overview
This implementation adds complete server-side validation and UI restrictions for the reschedule booking feature, enforcing strict business rules to ensure data integrity and proper workflow.

---

## 🔑 Key Business Rules Enforced

1. **Status Restriction**: Only bookings with status = `'Requested'` can be rescheduled
2. **One-Time Limit**: Each booking can only be rescheduled ONCE (checks for existing pending/approved requests)
3. **Ownership Validation**: Only the booking owner can submit reschedule requests
4. **Date Restrictions**:
   - New date cannot be in the past
   - Must provide at least 24 hours advance notice
5. **Atomic Transactions**: Booking updates and request status changes happen together (all or nothing)
6. **HR Approval Required**: All reschedule requests go through HR review with notes

---

## 📋 Implementation Summary

### 1. Database Migration
**File**: `database/migrations/add_hr_note_to_reschedule_requests.sql`
- Adds `hr_note TEXT` column to `reschedule_requests` table
- **Required**: Run this SQL before deploying code changes

**How to Apply**:
```bash
# Option 1: Through phpMyAdmin
# - Open phpMyAdmin → Select 'smartcare' database
# - Click "SQL" tab → Paste contents of migration file → Click "Go"

# Option 2: Command line (if MySQL CLI configured)
mysql -u root -p smartcare < database/migrations/add_hr_note_to_reschedule_requests.sql
```

---

### 2. Model Enhancements
**File**: `app/models/RescheduleRequestModel.php`

**New Methods Added**:
- `getRescheduleCountForBooking($bookingId)` - Counts pending/approved requests for a booking
- `hasRescheduleRequest($bookingId)` - Boolean check for existing request
- `canReschedule($bookingId, $clientId, $newDate)` - Comprehensive validation with detailed error messages

**Updated Methods**:
- `approveRequest($requestId, $hrNote)` - Now uses transactions for atomic operations with rollback on error

**Validation Order** (in `canReschedule()`):
1. Booking exists
2. Ownership check (client_id match)
3. Status must be 'Requested'
4. No prior reschedule request
5. New date not in past
6. Minimum 24h advance notice

---

### 3. Controller Updates

#### ClientController (`app/controllers/ClientController.php`)
**Method**: `rescheduleBooking()`

**Changes**:
- Now uses `canReschedule()` for ordered validation
- Provides specific error messages for each failure case
- Validates caretaker availability for new date
- Checks caretaker leave status
- Cleaner code structure with comments

**Error Messages**:
- "Booking not found."
- "You do not have permission to reschedule this booking."
- "This booking cannot be rescheduled (current status: X). Only bookings with 'Requested' status can be rescheduled."
- "A reschedule request has already been submitted for this booking. Only one reschedule is allowed per booking."
- "The new date cannot be in the past."
- "Reschedule requests must be made at least 24 hours in advance."
- "The assigned caregiver is not available on the requested new date and time."
- "The assigned caregiver is on leave during the requested date."

#### HrController (`app/controllers/HrController.php`)
**Method**: `approveReschedule()` and `rejectReschedule()`

**Changes**:
- Added validation before approval (request exists + status is pending)
- Better error handling with specific messages
- Improved notification messages
- Uses model's transaction-based approval
- **CRITICAL FIX**: After approval/rejection, booking status returns to `'Requested'` (NOT `'Accepted'`)
  - This allows the client to continue the normal workflow (advance payment, final payment, etc.)
  - Prevents skipping required payment steps
  - Maintains proper booking lifecycle: Requested → [Payment Steps] → Accepted → Completed

**Status Flow**:
```
Original: Requested
   ↓
Client requests reschedule → Reschedule_Requested
   ↓
HR approves/rejects
   ↓
Back to: Requested (client must complete payment workflow)
```

---

### 4. View Updates
**File**: `app/views/client/c_upcomingBookings.php`

**UI Improvements**:
- **Conditional Button Logic**:
  - Shows reschedule button ONLY for status = 'Requested' AND no existing request
  - Shows disabled button with tooltip for 'Requested' bookings that already have a request
  - Hides button completely for other statuses

- **Enhanced Modal**:
  - Warning box explaining reschedule rules
  - Date input with `min` attribute (24h from now)
  - Clear labels and help text
  - Professional styling with icons

- **Business Rule Display**:
  ```
  ⚠️ Important:
  • Only the date can be changed through reschedule
  • Service type, duration, and caregiver remain the same
  • You can only reschedule once per booking
  • Requests must be made at least 24 hours in advance
  • Status must be 'Requested' to allow reschedule
  ```

---

## 🔒 Security Improvements

1. **Type Casting**: All IDs cast to integers
2. **Input Validation**: Date format and validity checked
3. **Ownership Checks**: Client can only reschedule their own bookings
4. **Status Guards**: Multiple status checks prevent unauthorized actions
5. **Transaction Rollback**: Database consistency maintained even on partial failures

---

## 🧪 Testing Checklist

### Client-Side Tests:
- [ ] Try to reschedule a booking with status 'Accepted' → Should show no button
- [ ] Try to reschedule a booking with status 'Requested' → Should show button
- [ ] Submit reschedule request → Should appear in HR queue
- [ ] Try to reschedule same booking again → Should show disabled button with tooltip
- [ ] Try to reschedule with past date → Should get validation error
- [ ] Try to reschedule with date <24h away → Should get validation error

### HR-Side Tests:
- [ ] View pending reschedule requests → Should show all pending
- [ ] Approve request with hr_note → Should update booking date and status atomically
- [ ] Reject request with hr_note → Should update request status only
- [ ] Check notifications sent to client and caretaker after approval

### Database Tests:
- [ ] Verify hr_note column exists after migration
- [ ] Check transaction rollback if booking update fails
- [ ] Verify only one reschedule per booking is allowed

---

## 📊 Database Schema Update

**Before**:
```sql
CREATE TABLE `reschedule_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `client_id` int NOT NULL,
  `old_date` date NOT NULL,
  `new_date` date NOT NULL,
  `reason` text,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
)
```

**After**:
```sql
CREATE TABLE `reschedule_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `client_id` int NOT NULL,
  `old_date` date NOT NULL,
  `new_date` date NOT NULL,
  `reason` text,
  `hr_note` text,  -- ✨ NEW COLUMN
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
)
```

---

## 🚀 Deployment Steps

1. **Backup Database** (critical!):
   ```bash
   mysqldump -u root -p smartcare > backup_before_reschedule_update.sql
   ```

2. **Run Migration**:
   - Execute `database/migrations/add_hr_note_to_reschedule_requests.sql`
   - Verify column added: `DESCRIBE reschedule_requests;`

3. **Deploy Code**:
   - Copy updated files to production
   - Clear any caches if applicable

4. **Test**:
   - Follow testing checklist above
   - Monitor error logs for any issues

---

## 📝 Code Quality Improvements

- ✅ Follows DRY principle (validation logic centralized in model)
- ✅ Consistent error handling
- ✅ Clear separation of concerns (validation in model, business logic in controller, display in view)
- ✅ Comprehensive inline documentation
- ✅ Transaction safety for database operations
- ✅ User-friendly error messages
- ✅ Defensive programming (type checks, null checks)

---

## 🔍 Files Modified

1. ✅ `database/migrations/add_hr_note_to_reschedule_requests.sql` (NEW)
2. ✅ `app/models/RescheduleRequestModel.php` (ENHANCED)
3. ✅ `app/controllers/ClientController.php` (UPDATED)
4. ✅ `app/controllers/HrController.php` (UPDATED)
5. ✅ `app/views/client/c_upcomingBookings.php` (UPDATED)

---

## 💡 Future Enhancements (Optional)

- Add email notifications for reschedule status changes
- Allow rescheduling for other statuses (with adjusted business rules)
- Add reschedule request history view for clients
- Implement automatic caretaker reassignment if original is unavailable
- Add bulk reschedule operations for HR
- Create reschedule analytics dashboard

---

## ❓ FAQ

**Q: What happens if the caretaker is unavailable on the new date?**
A: The system checks availability before allowing the request. If unavailable, client gets an error message.

**Q: Can a booking be rescheduled after it's approved?**
A: No, current business rule only allows reschedule when status = 'Requested'.

**Q: What if client submits multiple requests rapidly?**
A: The database check runs on each submission. The first request will succeed, subsequent ones will be blocked.

**Q: Can HR force a reschedule even if the limit is reached?**
A: No, but HR can manually update the booking through other means if needed.

**Q: Does the transaction rollback work across multiple tables?**
A: Yes, the transaction covers both bookings and reschedule_requests tables atomically.

---

## 📞 Support

If you encounter issues:
1. Check error logs: `error_log()` statements added for debugging
2. Verify database migration completed successfully
3. Ensure all modified files deployed correctly
4. Test with a fresh browser session (clear cache/cookies)

---

**Implementation Date**: <?= date('Y-m-d') ?>
**Version**: 1.0.0
**Status**: ✅ Ready for Testing
