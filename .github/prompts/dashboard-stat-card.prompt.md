---
description: "Generate dashboard stat cards matching the summary-card pattern (white card, title, large value, optional styling). Use when: creating new stat cards for dashboards, converting other dashboard components to use consistent styling, or generating card layouts for metrics display."
argument-hint: "Stat card title, value, and optional styling variant"
agent: "agent"
---

# Generate Dashboard Stat Card

Generate a dashboard stat card (or grid of cards) matching the summary-card pattern used in the payments dashboard. Each card displays a metric title and value in a consistent, clean layout.

## Card Pattern Reference

The standard card styling (from `c_payments.css`):
- White background with light gray border (`#ddd`)
- Border-radius: `10px`, padding: `15px`
- **Title (`h3`)**: 12px, gray color, descriptive label
- **Value (`p`)**: 18px, bold, primary metric

## Task

Generate HTML markup for new stat cards with these specifications:

**Input what you want:**
- Card title (e.g., "Total Revenue", "Pending Approvals")
- Value display (number, currency, text, or count)
- Optional: Icon, color accent, or special styling variant

**Output you'll get:**
- Clean HTML markup following the existing pattern
- CSS classes matching the design system
- Ready to integrate into your view file
- Grid layout if creating multiple cards together

## Example

If you request: *"Create a stat card showing 'Active Bookings' with count of 12"*

Result:
```html
<div class="summary-card">
    <h3>Active Bookings</h3>
    <p>12</p>
</div>
```

For multiple cards, output as a `summary-grid` wrapper with cards inside.
