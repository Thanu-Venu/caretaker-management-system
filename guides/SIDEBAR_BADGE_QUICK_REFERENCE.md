# Sidebar Badge System - Quick Reference

## 🚀 Quick Start

### 1. How Badges Work
- Badges automatically appear on sidebar menu items when there are pending items
- Badge counts are fetched from database on page load
- Counts are cached to avoid multiple database queries
- Badges are hidden when count is 0

---

## 📋 File Locations

```
app/models/PendingCountModel.php          ← Database queries for counts
app/core/SidebarBadgeHelper.php           ← Helper functions for rendering
app/views/templates/admin/ad_sidebar.php  ← Admin sidebar (modified)
app/views/templates/hr/hr_sidebar.php     ← HR sidebar (modified)
app/views/templates/client/c_sidebar.php  ← Client sidebar (modified)
app/views/templates/caretaker/ct_sidebar.php ← Caretaker sidebar (modified)
public/css/common/sidebar-badges.css      ← Badge styling
```

---

## 🎯 Badge Keys by Role

### Admin
- `bookings` - Requested/Payment_Requested bookings
- `leave_requests` - Pending leave requests
- `profile_requests` - Pending profile change requests
- `payments` - Pending payments
- `complaints` - Open/In Progress complaints
- `reschedule_requests` - Pending reschedule requests

### HR (Manager)
- `bookings` - Pending bookings
- `payments` - Pending payments
- `leave_requests` - Pending leaves
- `complaints` - Unresolved complaints
- `change_requests` - Pending caretaker change requests
- `reschedule_requests` - Pending reschedule requests

### Client
- `bookings` - Client's pending bookings
- `payments` - Client's pending payments
- `reschedule_requests` - Client's pending reschedules

### Caretaker
- `bookings` - New accepted bookings
- `leave_requests` - Caretaker's pending leaves

---

## 💻 Code Snippets

### Add Badge to Menu Item

**Simple Menu Item (No Dropdown)**:
```php
<li>
  <a href="http://localhost/CMA/public?url=admin/ad_bookings">
    <span class="menu-item-content">
      <span class="menu-left">
        <i class='bx bx-calendar'></i> <span class="menu-text">Bookings</span>
      </span>
      <?php echo renderBadge('bookings', $badgeCounts); ?>
    </span>
  </a>
</li>
```

**Dropdown Menu Item**:
```php
<li class="submenu">
  <a href="#" class="dropdown-btn">
    <span class="menu-item-content">
      <span class="menu-left">
        <i class="bx bx-calendar"></i> My Bookings
      </span>
      <?php echo renderBadge('bookings', $badgeCounts); ?>
      <i class="bx bx-chevron-down arrow"></i>
    </span>
  </a>
  <ul class="dropdown-container">
    <!-- dropdown items -->
  </ul>
</li>
```

---

## ➕ Add New Badge Type

### Step 1: Add Method to PendingCountModel.php
```php
public function getPendingFeedbackCount() {
    $this->db->query("
        SELECT COUNT(*) as count
        FROM feedback
        WHERE status = 'pending'
    ");
    $result = $this->db->single();
    return (int) ($result->count ?? 0);
}
```

### Step 2: Add to Role Method
```php
public function getAdminPendingCounts() {
    $counts = [];
    $counts['bookings'] = $this->getPendingBookingsCount();
    $counts['payments'] = $this->getPendingPaymentsCount();
    // ... existing counts
    $counts['feedback'] = $this->getPendingFeedbackCount(); // ← Add this
    return $counts;
}
```

### Step 3: Use in Sidebar
```php
<?php echo renderBadge('feedback', $badgeCounts); ?>
```

---

## 🎨 Styling Options

### Change Badge Color
Edit `public/css/common/sidebar-badges.css`:

```css
/* Current: Red */
.sidebar-badge {
  background: linear-gradient(135deg, #ff4757 0%, #e84118 100%);
}

/* Option: Blue */
.sidebar-badge {
  background: linear-gradient(135deg, #3867d6 0%, #2f54c7 100%);
}

/* Option: Green */
.sidebar-badge {
  background: linear-gradient(135deg, #26de81 0%, #20bf6b 100%);
}
```

### Use Color Variations
Add class to badge:
```php
<span class="sidebar-badge badge-warning">5</span>  <!-- Orange -->
<span class="sidebar-badge badge-info">3</span>     <!-- Blue -->
<span class="sidebar-badge badge-success">10</span> <!-- Green -->
```

### Adjust Badge Size
```css
/* Make badges smaller */
.sidebar-badge {
  min-width: 18px;
  height: 18px;
  font-size: 10px;
}

/* Make badges larger */
.sidebar-badge {
  min-width: 24px;
  height: 24px;
  font-size: 12px;
}
```

---

## 🔧 Common Issues

### Badge Not Showing
1. Check if count > 0 in database
2. Verify `$_SESSION['role']` is set correctly
3. Check browser console for errors
4. Verify CSS file is loading

### Wrong Count
1. Check status values in database match SQL query
2. Verify user ID in `$_SESSION['profile_id']`
3. Check query logic in PendingCountModel.php

### Styling Broken
1. Clear browser cache
2. Check CSS file path is correct
3. Verify no CSS conflicts with existing styles

---

## 📊 Database Queries

### Bookings (Requested/Payment_Requested)
```sql
SELECT COUNT(*) as count
FROM bookings
WHERE status IN ('Requested', 'Payment_Requested')
```

### Payments (Pending)
```sql
SELECT COUNT(*) as count
FROM payments
WHERE status = 'pending'
```

### Leave Requests (Pending)
```sql
SELECT COUNT(*) as count
FROM leaves
WHERE status = 'Pending'
```

### Complaints (Open/In Progress)
```sql
SELECT COUNT(*) as count
FROM complaints
WHERE status IN ('Open', 'In Progress')
```

### Client-Specific Bookings
```sql
SELECT COUNT(*) as count
FROM bookings
WHERE client_id = :client_id
AND status IN ('Requested', 'Payment_Requested')
```

---

## 🧪 Testing Commands

### Check Badge Counts Manually
```php
// Add to any controller method temporarily
$pendingCountModel = new PendingCountModel();
$counts = $pendingCountModel->getAdminPendingCounts();
var_dump($counts);
exit;
```

### Test SQL Query
```sql
-- Run in phpMyAdmin or MySQL client
SELECT COUNT(*) as count
FROM bookings
WHERE status IN ('Requested', 'Payment_Requested');
```

### Check Session Variables
```php
// Add to sidebar temporarily
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
```

---

## ⚡ Performance Tips

### Add Database Indexes
```sql
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_payments_status ON payments(status);
CREATE INDEX idx_leaves_status ON leaves(status);
CREATE INDEX idx_complaints_status ON complaints(status);
```

### Monitor Query Performance
```php
// Add to PendingCountModel methods temporarily
$start = microtime(true);
// ... query code ...
$end = microtime(true);
error_log("Query time: " . ($end - $start) . " seconds");
```

---

## 🔄 AJAX Live Updates (Optional)

### Simple Implementation
Add to sidebar file:

```javascript
<script>
setInterval(() => {
  fetch('<?php echo URLROOT; ?>/badge/getCounts')
    .then(r => r.json())
    .then(data => {
      // Update badges with new counts
      Object.entries(data.counts).forEach(([key, count]) => {
        const badge = document.querySelector(`[data-badge-key="${key}"]`);
        if (badge) {
          badge.textContent = count > 99 ? '99+' : count;
          badge.style.display = count > 0 ? 'inline-flex' : 'none';
        }
      });
    });
}, 30000); // Update every 30 seconds
</script>
```

---

## 📝 Checklist for New Developer

- [ ] Read SIDEBAR_BADGE_IMPLEMENTATION.md
- [ ] Understand file structure
- [ ] Check database table statuses match queries
- [ ] Verify session variables are set on login
- [ ] Test badges in all 4 roles
- [ ] Verify responsive design on mobile
- [ ] Check browser console for errors
- [ ] Confirm CSS is loading
- [ ] Test with 0 pending items
- [ ] Test with 100+ pending items (99+ display)

---

## 🆘 Getting Help

1. Full documentation: `SIDEBAR_BADGE_IMPLEMENTATION.md`
2. Check troubleshooting section
3. Review code comments in:
   - `PendingCountModel.php`
   - `SidebarBadgeHelper.php`
4. Test individual query methods
5. Check PHP error logs
6. Verify database indexes exist

---

**Last Updated**: March 10, 2026
**Version**: 1.0
