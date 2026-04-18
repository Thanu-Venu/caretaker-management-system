# Caretaker Page URLs and Setup

## Complete Caretaker URL Structure

### Base URL
```
http://localhost/CMA/public/?url=caretaker/
```

### Main Pages
1. **Dashboard**: `http://localhost/CMA/public/?url=caretaker/ct_dashboard`
2. **Schedule**: `http://localhost/CMA/public/?url=caretaker/ct_schedule`
3. **Bookings**: `http://localhost/CMA/public/?url=caretaker/ct_booking`
4. **Leave Request**: `http://localhost/CMA/public/?url=caretaker/ct_leave`
5. **Leave History**: `http://localhost/CMA/public/?url=caretaker/ct_leaveHistory`
6. **Complaints**: `http://localhost/CMA/public/?url=caretaker/ct_complaints`
7. **Reviews**: `http://localhost/CMA/public/?url=caretaker/ct_reviews`
8. **Reports**: `http://localhost/CMA/public/?url=caretaker/ct_reports`
9. **Settings**: `http://localhost/CMA/public/?url=caretaker/ct_settings`
10. **Profile**: `http://localhost/CMA/public/?url=caretaker/ct_profile`
11. **Edit Profile**: `http://localhost/CMA/public/?url=caretaker/ct_editprofile`
12. **Announcements**: `http://localhost/CMA/public/?url=caretaker/ct_announcement`

### Booking Management
- **My Bookings**: `http://localhost/CMA/public/?url=caretaker/ct_myBookings`
- **Upcoming Bookings**: `http://localhost/CMA/public/?url=caretaker/ct_upcomingBookings`
- **Past Bookings**: `http://localhost/CMA/public/?url=caretaker/ct_pastBookings`
- **Ongoing Bookings**: `http://localhost/CMA/public/?url=caretaker/ct_ongoingBookings`

### Complaint Management
- **Register Complaint**: `http://localhost/CMA/public/?url=caretaker/ct_complaintReg`
- **Submit Complaint**: `http://localhost/CMA/public/?url=caretaker/submitComplaint`

### Feedback
- **Feedback**: `http://localhost/CMA/public/?url=caretaker/ct_feedback`

## Authentication Flow
1. **Login**: `http://localhost/CMA/public/?url=auth/login`
2. **After Login**: Redirects to appropriate dashboard based on role
3. **Logout**: `http://localhost/CMA/public/?url=auth/logout`

## Navigation Structure
The caretaker sidebar provides access to:
- Dashboard (Main overview)
- My Schedule (Calendar view)
- Bookings (All booking management)
- Leave Request (Request time off)
- Complaints (View and manage complaints)
- Reviews (View client feedback)
- Reports (Generate reports)
- Settings (Account settings)

## Controller Methods
All URLs are handled by `CaretakerController.php` with corresponding methods:
- `ct_dashboard()` - Main dashboard
- `ct_schedule()` - Schedule view
- `ct_booking()` - Bookings management
- `ct_leave()` - Leave request form
- `ct_complaints()` - Complaints list
- `ct_reviews()` - Reviews page
- `ct_reports()` - Reports page
- `ct_settings()` - Settings page

## Access Requirements
- User must be logged in with 'caretaker' role
- Sessions managed by `AuthSession` class
- Automatic redirect to login if not authenticated

## Testing
1. Login as caretaker through: `http://localhost/CMA/public/?url=auth/login`
2. Should automatically redirect to: `http://localhost/CMA/public/?url=caretaker/ct_dashboard`
3. Navigate using sidebar menu items
