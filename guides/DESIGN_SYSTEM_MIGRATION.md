# 🚀 SmartCare Frontend Refactor - Migration Guide

## 📋 Overview

This guide provides step-by-step instructions for migrating your SmartCare application to the new design system. Follow these steps carefully to ensure a smooth transition without breaking existing functionality.

---

## ✅ What Has Been Created

### CSS Files (System Foundation)
```
public/css/system/
├── variables.css       ← All design tokens (colors, spacing, typography)
├── reset.css           ← CSS normalization
├── global.css          ← Base body, html, element styles
├── typography.css      ← Text styles and heading scales
└── utilities.css       ← Utility classes (margin, padding, flex, etc.)
```

### CSS Files (Layout)
```
public/css/layout/
├── container.css       ← Page structure and content containers
├── grid.css            ← Grid systems for dashboards and forms
└── sidebar.css         ← Unified responsive sidebar (mobile-ready!)
```

### CSS Files (Components)
```
public/css/components/
├── buttons.css         ← Standardized button system
├── forms.css           ← Input, select, textarea styles
├── tables.css          ← Consistent table design
├── cards.css           ← Card/panel components
└── badges.css          ← Status badges and labels
```

### CSS Files (Responsive)
```
public/css/responsive/
└── breakpoints.css     ← Media queries and responsive utilities
```

### JavaScript
```
public/js/
└── sidebar-toggle.js   ← Mobile sidebar toggle functionality
```

### Documentation
```
Root directory:
├── DESIGN_SYSTEM_PLAN.md           ← Complete design system specifications
└── DESIGN_SYSTEM_MIGRATION.md      ← This file
```

---

## 🔧 Migration Steps

### STEP 1: Update Page Templates

#### 1.1 Update CSS Includes in Header Files

**Before (Example: hr_header.php):**
```php
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_header.css">
```

**After (Replace with new system):**
```php
<!-- System Foundation -->
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/system/variables.css">
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/system/reset.css">
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/system/global.css">
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/system/typography.css">
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/system/utilities.css">

<!-- Layout -->
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/layout/container.css">
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/layout/grid.css">
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/layout/sidebar.css">

<!-- Components -->
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/components/buttons.css">
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/components/forms.css">
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/components/tables.css">
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/components/cards.css">
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/components/badges.css">

<!-- Responsive -->
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/responsive/breakpoints.css">

<!-- Keep sidebar badges -->
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/common/sidebar-badges.css">

<!-- Page-specific CSS (if needed) -->
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/hr/hr_dashboard.css">
```

#### 1.2 Add JavaScript Before Closing Body Tag

**Add to all pages with sidebar (before `</body>`):**
```php
<!-- Sidebar Mobile Toggle -->
<script src="<?= URLROOT ?>/public/js/sidebar-toggle.js"></script>
```

---

### STEP 2: Update Sidebar Structure

#### 2.1 Admin Sidebar (ad_sidebar.php)

**Update the sidebar HTML structure:**

```php
<?php
// Keep existing badge helper
require_once APPROOT . '/core/SidebarBadgeHelper.php';
$badgeCounts = getSidebarBadgeCounts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartCare - Admin</title>

  <!-- Boxicons -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

  <!-- New CSS System -->
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/system/variables.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/system/reset.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/system/global.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/layout/sidebar.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/common/sidebar-badges.css">
</head>
<body>

  <!-- Sidebar -->
  <aside class="sidebar">
    <!-- Sidebar Brand -->
    <div class="sidebar-brand">
      <h2>SmartCare</h2>
    </div>

    <!-- Menu with Scroll -->
    <div class="menu-scroll">
      <ul class="sidebar-menu">
        <li>
          <a href="<?= URLROOT ?>/admin/dashboard">
            <i class='bx bx-home'></i>
            <span>Dashboard</span>
          </a>
        </li>

        <li>
          <a href="<?= URLROOT ?>/admin/caregivers">
            <i class='bx bx-group'></i>
            <span>Caregivers</span>
          </a>
        </li>

        <li>
          <a href="<?= URLROOT ?>/admin/bookings">
            <span class="menu-item-content">
              <span class="menu-left">
                <i class='bx bx-calendar'></i>
                <span>Bookings</span>
              </span>
              <?php echo renderBadge('bookings', $badgeCounts); ?>
            </span>
          </a>
        </li>

        <!-- Add more menu items -->
      </ul>
    </div>
  </aside>

  <!-- Sidebar Overlay (for mobile) -->
  <div class="sidebar-overlay"></div>

  <!-- Sidebar Toggle Button (for mobile) -->
  <button class="sidebar-toggle" aria-label="Toggle sidebar menu">
    <i class='bx bx-menu'></i>
  </button>

</body>
</html>
```

**Apply the same structure to:**
- `app/views/templates/hr/hr_sidebar.php`
- `app/views/templates/client/c_sidebar.php`
- `app/views/templates/caretaker/ct_sidebar.php`

---

### STEP 3: Update Page Content Structure

#### 3.1 Dashboard Pages

**Before:**
```php
<div class="content">
  <h1>Dashboard</h1>
  <!-- content -->
</div>
```

**After:**
```php
<div class="main-content">
  <div class="page-header">
    <h1 class="page-title">Dashboard</h1>
  </div>

  <!-- Stats Cards -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-card-icon">
        <i class='bx bx-user'></i>
      </div>
      <div class="stat-card-label">Total Clients</div>
      <div class="stat-card-value">156</div>
    </div>
    <!-- More stat cards -->
  </div>

  <!-- Content sections -->
</div>
```

#### 3.2 Table Pages

**Before:**
```php
<div class="table-container">
  <table class="caretaker-table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <!-- rows -->
    </tbody>
  </table>
</div>
```

**After (add `.table` class and status badges):**
```php
<div class="table-container">
  <table class="table table-hover">
    <thead>
      <tr>
        <th>Name</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>John Doe</td>
        <td><span class="badge-success">Available</span></td>
        <td>
          <div class="table-actions">
            <a href="#" class="btn btn-sm btn-primary">View</a>
            <a href="#" class="btn btn-sm btn-outline">Edit</a>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</div>
```

#### 3.3 Form Pages

**Before:**
```php
<form action="..." method="POST">
  <label>Name</label>
  <input type="text" name="name">

  <button type="submit" class="submit-btn">Save</button>
</form>
```

**After:**
```php
<div class="main-content">
  <div class="form-section">
    <h2 class="form-section-title">Add Caregiver</h2>

    <form action="..." method="POST">
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label required">First Name</label>
          <input type="text" name="first_name" class="form-input" required>
        </div>

        <div class="form-group">
          <label class="form-label required">Last Name</label>
          <input type="text" name="last_name" class="form-input" required>
        </div>

        <div class="form-group full-width">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-textarea"></textarea>
          <span class="form-help">Brief description of the caregiver</span>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save Caregiver</button>
        <a href="..." class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</div>
```

---

### STEP 4: Update Button Classes

#### Replace old button classes:

| Old Class | New Class |
|-----------|-----------|
| `.submit-btn` | `.btn .btn-primary` |
| `.btn-cancel` | `.btn .btn-outline` |
| `.btn-add` | `.btn .btn-success` |
| Custom buttons | `.btn .btn-{variant}` |

**Example:**
```html
<!-- Before -->
<button class="submit-btn">Save</button>
<a href="#" class="btn-cancel">Cancel</a>

<!-- After -->
<button class="btn btn-primary">Save</button>
<a href="#" class="btn btn-outline">Cancel</a>
```

---

### STEP 5: Update Status Badges

#### Replace inline status styles with badge classes:

```php
<!-- Before -->
<span class="status available">Available</span>

<!-- After (still works - legacy support included) -->
<span class="status available">Available</span>

<!-- Or use new badge classes -->
<span class="badge-success">Available</span>
<span class="badge-warning">Pending</span>
<span class="badge-danger">Unavailable</span>
```

---

### STEP 6: Update Card Structures

**Before:**
```html
<div class="card">
  <h3>Profile</h3>
  <p>Content here</p>
</div>
```

**After:**
```html
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Profile</h3>
  </div>
  <div class="card-body">
    <p>Content here</p>
  </div>
</div>
```

---

### STEP 7: Test on All Devices

#### 7.1 Desktop Testing (1920px)
- ✅ Sidebar visible and fixed
- ✅ Content has proper margin-left
- ✅ Tables fit properly
- ✅ Forms arranged in grid

#### 7.2 Tablet Testing (768px-1023px)
- ✅ Sidebar narrower but still visible
- ✅ Content margin adjusted
- ✅ Stats grid shows 2 columns

#### 7.3 Mobile Testing (<768px)
- ✅ Sidebar hidden by default
- ✅ Hamburger menu button visible
- ✅ Clicking hamburger opens sidebar
- ✅ Overlay visible when sidebar open
- ✅ Clicking overlay closes sidebar
- ✅ Content takes full width
- ✅ Tables scroll horizontally
- ✅ Forms stack vertically

---

## 📝 Component Usage Reference

### Buttons
```html
<button class="btn btn-primary">Primary</button>
<button class="btn btn-secondary">Secondary</button>
<button class="btn btn-success">Success</button>
<button class="btn btn-danger">Danger</button>
<button class="btn btn-outline">Outline</button>

<!-- Sizes -->
<button class="btn btn-sm btn-primary">Small</button>
<button class="btn btn-lg btn-primary">Large</button>

<!-- Icon button -->
<button class="btn btn-icon btn-primary"><i class='bx bx-plus'></i></button>
```

### Forms
```html
<div class="form-group">
  <label class="form-label required">Email</label>
  <input type="email" class="form-input" required>
  <span class="form-help">We'll never share your email</span>
</div>

<div class="form-group">
  <label class="form-label">Country</label>
  <select class="form-select">
    <option>Select country</option>
    <option>Sri Lanka</option>
  </select>
</div>

<div class="form-group">
  <label class="form-label">Message</label>
  <textarea class="form-textarea"></textarea>
</div>
```

### Tables
```html
<div class="table-container">
  <table class="table table-hover">
    <thead>
      <tr>
        <th>Column 1</th>
        <th>Column 2</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Data 1</td>
        <td>Data 2</td>
      </tr>
    </tbody>
  </table>
</div>
```

### Cards
```html
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Card Title</h3>
  </div>
  <div class="card-body">
    <p>Card content goes here</p>
  </div>
</div>

<!-- Stat Card -->
<div class="stat-card">
  <div class="stat-card-icon">
    <i class='bx bx-user'></i>
  </div>
  <div class="stat-card-label">Total Users</div>
  <div class="stat-card-value">1,234</div>
</div>
```

### Badges
```html
<span class="badge-primary">Primary</span>
<span class="badge-success">Success</span>
<span class="badge-warning">Warning</span>
<span class="badge-danger">Danger</span>

<!-- Status badges (legacy support) -->
<span class="status available">Available</span>
<span class="status pending">Pending</span>
```

### Grids
```html
<!-- Stats Grid (Dashboard) -->
<div class="stats-grid">
  <div class="stat-card">...</div>
  <div class="stat-card">...</div>
  <div class="stat-card">...</div>
</div>

<!-- Form Grid -->
<div class="form-grid">
  <div class="form-group">...</div>
  <div class="form-group">...</div>
  <div class="form-group full-width">...</div>
</div>
```

### Utility Classes
```html
<!-- Margin -->
<div class="mt-4 mb-6">Content</div>

<!-- Padding -->
<div class="p-4">Content</div>

<!-- Flexbox -->
<div class="flex items-center justify-between gap-4">
  <span>Left</span>
  <span>Right</span>
</div>

<!-- Typography -->
<p class="text-lg text-secondary">Large secondary text</p>
<p class="text-sm font-semibold">Small bold text</p>

<!-- Display -->
<div class="hidden-mobile">Desktop only</div>
<div class="mobile-only">Mobile only</div>
```

---

## 🎨 Customization Guide

### Changing Colors

Edit `public/css/system/variables.css`:

```css
:root {
  /* Change primary color */
  --primary-500: #3b82f6;  /* Your brand color */

  /* Change success color */
  --success-500: #10b981;

  /* Change danger color */
  --danger-500: #ef4444;
}
```

### Changing Spacing

```css
:root {
  /* Increase card padding */
  --card-padding: 2rem;  /* was 1.5rem */

  /* Increase page padding */
  --page-padding: 3rem;  /* was 2rem */
}
```

### Changing Typography

```css
:root {
  /* Change font family */
  --font-primary: 'Inter', -apple-system, sans-serif;

  /* Change base font size */
  --text-base: 1.125rem;  /* 18px instead of 16px */
}
```

---

## 🐛 Troubleshooting

### Sidebar Not Showing on Mobile

**Check:**
1. Is `sidebar-toggle.js` included before `</body>`?
2. Does sidebar have `.sidebar` class?
3. Is Boxicons loaded for the menu icon?

**Solution:**
```html
<!-- Before </body> -->
<script src="<?= URLROOT ?>/public/js/sidebar-toggle.js"></script>
```

### Tables Overflowing

**Solution:**
Wrap table in `.table-container`:
```html
<div class="table-container">
  <table class="table">...</table>
</div>
```

### Content Under Sidebar on Mobile

**Check:**
1. Is `.main-content` class on content wrapper?
2. Are all CSS files loaded in correct order?

**Solution:**
```html
<div class="main-content">
  <!-- Your page content -->
</div>
```

### Styles Not Applying

**Check Load Order:**
1. Variables.css must load FIRST
2. Reset.css second
3. Global.css third
4. Then typography, utilities, layout, components

---

## 📱 Mobile Sidebar Behavior

### How It Works

1. **Desktop (≥1024px):** Sidebar always visible, 260px width
2. **Tablet (768-1023px):** Sidebar always visible, 220px width
3. **Mobile (<768px):** Sidebar off-canvas, slides in from left

### User Flow on Mobile

1. User lands on page → Hamburger button visible
2. Click hamburger → Sidebar slides in, overlay appears
3. Click overlay or menu link → Sidebar closes
4. Press ESC key → Sidebar closes

---

## ✅ Checklist for Each Page

- [ ] Update CSS includes to new system
- [ ] Add `.main-content` wrapper
- [ ] Update sidebar structure with toggle button
- [ ] Add sidebar JavaScript
- [ ] Replace old button classes
- [ ] Update form classes
- [ ] Update table classes
- [ ] Update card structures
- [ ] Test on desktop (1920px)
- [ ] Test on tablet (768px)
- [ ] Test on mobile (375px)
- [ ] Verify hamburger menu works
- [ ] Check all dropdowns work
- [ ] Verify no horizontal scrolling

---

## 🎯 Priority Order

### HIGH PRIORITY (Do First)
1. Update main dashboard pages
2. Implement sidebar mobile toggle
3. Fix admin panel

### MEDIUM PRIORITY
1. Update all form pages
2. Update table listing pages
3. Update profile pages

### LOW PRIORITY
1. Landing page styling
2. Login/register pages
3. Report pages

---

## 📚 Additional Resources

- **Design System Plan:** `DESIGN_SYSTEM_PLAN.md`
- **CSS Variables Reference:** `public/css/system/variables.css` (lines 1-200)
- **Component Examples:** This guide (Component Usage Reference section)

---

## 🆘 Need Help?

### Common Issues

**Issue:** Sidebar overlaps content on mobile
**Fix:** Ensure `.main-content` class is applied to content wrapper

**Issue:** Dropdown menus not working
**Fix:** Include `sidebar-toggle.js` and verify dropdown structure

**Issue:** Buttons look different across pages
**Fix:** Use consistent `.btn .btn-{variant}` class structure

---

**Migration Status:** ✅ Complete
**Version:** 1.0
**Last Updated:** March 10, 2026
**Estimated Migration Time:** 2-4 hours for complete project
