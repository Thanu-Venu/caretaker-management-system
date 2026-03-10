# Reports View Enhancement - Quick Reference

## Admin Reports View (`ad_reports.php`) - Code Snippets

### 1. Enhanced Summary Cards Section

Replace existing summary cards with:

```php
<!-- Summary Statistics Cards -->
<div class="summary-cards">
    <div class="card">
        <div class="card-icon revenue"></div>
        <div class="card-content">
            <h3>Total Revenue</h3>
            <p class="card-value">LKR <?= number_format($data['summary']['total_revenue'] ?? 0, 2) ?></p>
        </div>
    </div>

    <div class="card">
        <div class="card-icon bookings"></div>
        <div class="card-content">
            <h3>Total Bookings</h3>
            <p class="card-value"><?= $data['summary']['total_bookings'] ?? 0 ?></p>
        </div>
    </div>

    <div class="card">
        <div class="card-icon caretakers"></div>
        <div class="card-content">
            <h3>Active Caretakers</h3>
            <p class="card-value"><?= $data['summary']['active_caretakers'] ?? 0 ?></p>
        </div>
    </div>

    <div class="card">
        <div class="card-icon clients"></div>
        <div class="card-content">
            <h3>Total Clients</h3>
            <p class="card-value"><?= $data['summary']['total_clients'] ?? 0 ?></p>
        </div>
    </div>

    <div class="card">
        <div class="card-icon payments"></div>
        <div class="card-content">
            <h3>Total Payments</h3>
            <p class="card-value"><?= $data['summary']['total_payments'] ?? 0 ?></p>
        </div>
    </div>

    <div class="card">
        <div class="card-icon rating"></div>
        <div class="card-content">
            <h3>Average Rating</h3>
            <p class="card-value"><?= number_format($data['summary']['average_rating'] ?? 0, 2) ?> ⭐</p>
        </div>
    </div>
</div>
```

### 2. Booking Status Breakdown Section

Add after summary cards:

```php
<!-- Booking Status Breakdown -->
<div class="report-section">
    <h2>Booking Status Overview</h2>
    <div class="section-grid">
        <div class="chart-container">
            <canvas id="bookingStatusChart"></canvas>
        </div>
        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Count</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = array_sum(array_column($data['bookingStatus'] ?? [], 'count'));
                    foreach ($data['bookingStatus'] ?? [] as $status):
                        $percentage = $total > 0 ? ($status['count'] / $total) * 100 : 0;
                    ?>
                    <tr>
                        <td><span class="status-badge status-<?= strtolower($status['status']) ?>"><?= htmlspecialchars($status['status']) ?></span></td>
                        <td><?= $status['count'] ?></td>
                        <td><?= number_format($percentage, 1) ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
```

### 3. Financial Reports Section

```php
<!-- Financial Reports -->
<div class="report-section">
    <h2>Financial Analytics</h2>

    <!-- Monthly Revenue Trend -->
    <div class="subsection">
        <h3>Monthly Revenue Trend</h3>
        <canvas id="revenueTrendChart"></canvas>
    </div>

    <!-- Revenue by Service Type -->
    <div class="subsection">
        <h3>Revenue by Service Type</h3>
        <div class="section-grid">
            <div class="chart-container">
                <canvas id="revenueByServiceChart"></canvas>
            </div>
            <div class="data-table">
                <table>
                    <thead>
                        <tr>
                            <th>Service Type</th>
                            <th>Revenue (LKR)</th>
                            <th>Bookings</th>
                            <th>Avg per Booking</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['revenueByService'] ?? [] as $service):
                            $avgPerBooking = $service['bookings'] > 0 ? $service['revenue'] / $service['bookings'] : 0;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($service['service_type']) ?></td>
                            <td>LKR <?= number_format($service['revenue'], 2) ?></td>
                            <td><?= $service['bookings'] ?></td>
                            <td>LKR <?= number_format($avgPerBooking, 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Status Breakdown -->
    <div class="subsection">
        <h3>Payment Status Breakdown</h3>
        <canvas id="paymentStatusChart"></canvas>
    </div>

    <!-- Refund Statistics -->
    <div class="subsection">
        <h3>Refund Statistics</h3>
        <table>
            <thead>
                <tr>
                    <th>Metric</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Refunds Processed</td>
                    <td><?= $data['refundStats']['total_refunds'] ?? 0 ?></td>
                </tr>
                <tr>
                    <td>Total Refund Amount</td>
                    <td>LKR <?= number_format($data['refundStats']['total_refund_amount'] ?? 0, 2) ?></td>
                </tr>
                <tr>
                    <td>Average Refund Amount</td>
                    <td>LKR <?= number_format($data['refundStats']['avg_refund_amount'] ?? 0, 2) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
```

### 4. Top Caretakers Section

```php
<!-- Top Caretakers Performance -->
<div class="report-section">
    <h2>Caretaker Performance</h2>

    <!-- Top by Bookings -->
    <div class="subsection">
        <h3>Top Caretakers by Bookings</h3>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Name</th>
                    <th>Service Type</th>
                    <th>Total Bookings</th>
                    <th>Avg Rating</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; foreach ($data['topCaretakersByBookings'] ?? [] as $caretaker): ?>
                <tr>
                    <td><?= $rank++ ?></td>
                    <td><?= htmlspecialchars($caretaker['name']) ?></td>
                    <td><?= htmlspecialchars($caretaker['service_type']) ?></td>
                    <td><?= $caretaker['total_bookings'] ?></td>
                    <td><?= number_format($caretaker['avg_rating'], 2) ?> ⭐</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Top by Revenue -->
    <div class="subsection">
        <h3>Top Caretakers by Revenue</h3>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Name</th>
                    <th>Service Type</th>
                    <th>Total Revenue (LKR)</th>
                    <th>Total Bookings</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; foreach ($data['topCaretakersByRevenue'] ?? [] as $caretaker): ?>
                <tr>
                    <td><?= $rank++ ?></td>
                    <td><?= htmlspecialchars($caretaker['name']) ?></td>
                    <td><?= htmlspecialchars($caretaker['service_type']) ?></td>
                    <td>LKR <?= number_format($caretaker['total_revenue'], 2) ?></td>
                    <td><?= $caretaker['total_bookings'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Highest Rated -->
    <div class="subsection">
        <h3>Highest Rated Caretakers (4.5+ rating)</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Service Type</th>
                    <th>Avg Rating</th>
                    <th>Total Feedbacks</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['highestRatedCaretakers'] ?? [] as $caretaker): ?>
                <tr>
                    <td><?= htmlspecialchars($caretaker['name']) ?></td>
                    <td><?= htmlspecialchars($caretaker['service_type']) ?></td>
                    <td><?= number_format($caretaker['avg_rating'], 2) ?> ⭐</td>
                    <td><?= $caretaker['total_feedbacks'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
```

### 5. Top Clients Section

```php
<!-- Top Clients Analytics -->
<div class="report-section">
    <h2>Client Analytics</h2>

    <!-- Top by Spending -->
    <div class="subsection">
        <h3>Top Clients by Spending</h3>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Total Bookings</th>
                    <th>Total Spent (LKR)</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; foreach ($data['topClientsBySpending'] ?? [] as $client): ?>
                <tr>
                    <td><?= $rank++ ?></td>
                    <td><?= htmlspecialchars($client['name']) ?></td>
                    <td><?= htmlspecialchars($client['email']) ?></td>
                    <td><?= $client['total_bookings'] ?></td>
                    <td>LKR <?= number_format($client['total_spent'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
```

### 6. Quality Metrics Section

```php
<!-- Quality & Feedback Metrics -->
<div class="report-section">
    <h2>Quality & Feedback</h2>

    <!-- Service-wise Ratings -->
    <div class="subsection">
        <h3>Service-wise Average Ratings</h3>
        <table>
            <thead>
                <tr>
                    <th>Service Type</th>
                    <th>Avg Rating</th>
                    <th>Feedback Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['serviceRatings'] ?? [] as $service): ?>
                <tr>
                    <td><?= htmlspecialchars($service['service_type']) ?></td>
                    <td><?= number_format($service['avg_rating'], 2) ?> ⭐</td>
                    <td><?= $service['feedback_count'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Low-rated Bookings -->
    <div class="subsection">
        <h3>Low-Rated Bookings (< 3.0 rating)</h3>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Caretaker</th>
                    <th>Client</th>
                    <th>Service</th>
                    <th>Rating</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['lowRatedBookings'] ?? [] as $booking): ?>
                <tr class="warning-row">
                    <td>#<?= $booking['booking_id'] ?></td>
                    <td><?= htmlspecialchars($booking['caretaker_name']) ?></td>
                    <td><?= htmlspecialchars($booking['client_name']) ?></td>
                    <td><?= htmlspecialchars($booking['service_type']) ?></td>
                    <td><?= number_format($booking['rating'], 1) ?> ⭐</td>
                    <td><?= date('M d, Y', strtotime($booking['booking_date'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
```

---

## HR Reports View (`hr_reports.php`) - Code Snippets

### 1. Enhanced Summary Cards Section

```php
<!-- Summary Statistics Cards -->
<div class="summary-cards">
    <div class="card">
        <div class="card-icon bookings"></div>
        <div class="card-content">
            <h3>Total Bookings</h3>
            <p class="card-value"><?= $data['summary']['total_bookings'] ?? 0 ?></p>
        </div>
    </div>

    <div class="card">
        <div class="card-icon caretakers"></div>
        <div class="card-content">
            <h3>Active Caretakers</h3>
            <p class="card-value"><?= $data['summary']['active_caretakers'] ?? 0 ?></p>
        </div>
    </div>

    <div class="card">
        <div class="card-icon leaves"></div>
        <div class="card-content">
            <h3>Pending Leaves</h3>
            <p class="card-value"><?= $data['summary']['pending_leave_requests'] ?? 0 ?></p>
        </div>
    </div>

    <div class="card">
        <div class="card-icon reschedules"></div>
        <div class="card-content">
            <h3>Pending Reschedules</h3>
            <p class="card-value"><?= $data['summary']['pending_reschedule_requests'] ?? 0 ?></p>
        </div>
    </div>

    <div class="card">
        <div class="card-icon unassigned"></div>
        <div class="card-content">
            <h3>Unassigned Bookings</h3>
            <p class="card-value"><?= $data['summary']['unassigned_bookings'] ?? 0 ?></p>
        </div>
    </div>

    <div class="card">
        <div class="card-icon rating"></div>
        <div class="card-content">
            <h3>Avg Caretaker Rating</h3>
            <p class="card-value"><?= number_format($data['summary']['average_caretaker_rating'] ?? 0, 2) ?> ⭐</p>
        </div>
    </div>
</div>
```

### 2. Caretaker Management Section

```php
<!-- Caretaker Management -->
<div class="report-section">
    <h2>Caretaker Management</h2>

    <!-- Status Breakdown -->
    <div class="subsection">
        <h3>Caretaker Status Breakdown</h3>
        <div class="section-grid">
            <div class="chart-container">
                <canvas id="caretakerStatusChart"></canvas>
            </div>
            <div class="data-table">
                <table>
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['caretakerStatus'] ?? [] as $status): ?>
                        <tr>
                            <td><span class="status-badge status-<?= strtolower($status['status']) ?>"><?= htmlspecialchars($status['status']) ?></span></td>
                            <td><?= $status['count'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Newly Added Caretakers -->
    <div class="subsection">
        <h3>Newly Added Caretakers (Last 30 Days)</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Service Type</th>
                    <th>Status</th>
                    <th>Joined Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['newCaretakers'] ?? [] as $caretaker): ?>
                <tr>
                    <td>#<?= $caretaker['caretaker_id'] ?></td>
                    <td><?= htmlspecialchars($caretaker['name']) ?></td>
                    <td><?= htmlspecialchars($caretaker['service_type']) ?></td>
                    <td><span class="status-badge status-<?= strtolower($caretaker['status']) ?>"><?= htmlspecialchars($caretaker['status']) ?></span></td>
                    <td><?= date('M d, Y', strtotime($caretaker['joined_date'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
```

### 3. Leave Management Section

```php
<!-- Leave Management -->
<div class="report-section">
    <h2>Leave Management</h2>

    <!-- Pending Leave Requests -->
    <div class="subsection highlight-section">
        <h3>⚠️ Pending Leave Requests (Action Required)</h3>
        <table>
            <thead>
                <tr>
                    <th>Leave ID</th>
                    <th>Caretaker</th>
                    <th>Service Type</th>
                    <th>Leave Type</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Days</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['pendingLeaves'] ?? [] as $leave):
                    $days = (strtotime($leave['to_date']) - strtotime($leave['from_date'])) / 86400 + 1;
                ?>
                <tr>
                    <td>#<?= $leave['leave_id'] ?></td>
                    <td><?= htmlspecialchars($leave['caretaker_name']) ?></td>
                    <td><?= htmlspecialchars($leave['service_type']) ?></td>
                    <td><?= htmlspecialchars($leave['leave_type']) ?></td>
                    <td><?= date('M d, Y', strtotime($leave['from_date'])) ?></td>
                    <td><?= date('M d, Y', strtotime($leave['to_date'])) ?></td>
                    <td><?= $days ?> days</td>
                    <td><?= htmlspecialchars($leave['reason']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Caretakers Currently on Leave -->
    <div class="subsection">
        <h3>Caretakers Currently on Leave</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Service Type</th>
                    <th>Leave Type</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Days Remaining</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['caretakersOnLeave'] ?? [] as $leave): ?>
                <tr>
                    <td><?= htmlspecialchars($leave['name']) ?></td>
                    <td><?= htmlspecialchars($leave['service_type']) ?></td>
                    <td><?= htmlspecialchars($leave['leave_type']) ?></td>
                    <td><?= date('M d, Y', strtotime($leave['from_date'])) ?></td>
                    <td><?= date('M d, Y', strtotime($leave['to_date'])) ?></td>
                    <td><?= $leave['days_remaining'] ?> days</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
```

### 4. Schedule & Assignment Section

```php
<!-- Schedule & Assignment Management -->
<div class="report-section">
    <h2>Schedule & Assignment Management</h2>

    <!-- Unassigned Bookings -->
    <div class="subsection highlight-section">
        <h3>⚠️ Unassigned Bookings (Urgent Action Required)</h3>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Client</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Duration</th>
                    <th>Basis</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['unassignedBookings'] ?? [] as $booking): ?>
                <tr>
                    <td>#<?= $booking['booking_id'] ?></td>
                    <td><?= htmlspecialchars($booking['client_name']) ?></td>
                    <td><?= htmlspecialchars($booking['service_type']) ?></td>
                    <td><?= date('M d, Y', strtotime($booking['booking_date'])) ?></td>
                    <td><?= htmlspecialchars($booking['preferred_time']) ?></td>
                    <td><?= $booking['duration'] ?> hrs</td>
                    <td><?= htmlspecialchars($booking['basis']) ?></td>
                    <td><?= htmlspecialchars($booking['district']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Upcoming Schedules -->
    <div class="subsection">
        <h3>Upcoming Schedules (Next 7 Days)</h3>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Caretaker</th>
                    <th>Client</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['upcomingSchedules'] ?? [] as $schedule): ?>
                <tr>
                    <td>#<?= $schedule['booking_id'] ?></td>
                    <td><?= htmlspecialchars($schedule['caretaker_name']) ?></td>
                    <td><?= htmlspecialchars($schedule['client_name']) ?></td>
                    <td><?= htmlspecialchars($schedule['service_type']) ?></td>
                    <td><?= date('M d, Y', strtotime($schedule['booking_date'])) ?></td>
                    <td><?= htmlspecialchars($schedule['preferred_time']) ?></td>
                    <td><span class="status-badge status-<?= strtolower($schedule['status']) ?>"><?= htmlspecialchars($schedule['status']) ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
```

### 5. Reschedule Management Section

```php
<!-- Reschedule Management -->
<div class="report-section">
    <h2>Reschedule Management</h2>

    <!-- Pending Reschedule Requests -->
    <div class="subsection highlight-section">
        <h3>⚠️ Pending Reschedule Requests (Action Required)</h3>
        <table>
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Booking ID</th>
                    <th>Client</th>
                    <th>Caretaker</th>
                    <th>Current Date</th>
                    <th>Requested Date</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['pendingReschedules'] ?? [] as $request): ?>
                <tr>
                    <td>#<?= $request['request_id'] ?></td>
                    <td>#<?= $request['booking_id'] ?></td>
                    <td><?= htmlspecialchars($request['client_name']) ?></td>
                    <td><?= htmlspecialchars($request['caretaker_name'] ?? 'Unassigned') ?></td>
                    <td><?= date('M d, Y', strtotime($request['current_date'])) ?></td>
                    <td><?= date('M d, Y', strtotime($request['requested_date'])) ?></td>
                    <td><?= htmlspecialchars($request['reason']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
```

### 6. Performance Monitoring Section

```php
<!-- Performance Monitoring -->
<div class="report-section">
    <h2>Performance Monitoring</h2>

    <!-- Caretaker Feedback Summary -->
    <div class="subsection">
        <h3>Caretaker Feedback Summary</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Service Type</th>
                    <th>Avg Rating</th>
                    <th>Total Feedbacks</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['caretakerFeedback'] ?? [] as $feedback): ?>
                <tr>
                    <td><?= htmlspecialchars($feedback['name']) ?></td>
                    <td><?= htmlspecialchars($feedback['service_type']) ?></td>
                    <td><?= number_format($feedback['avg_rating'], 2) ?> ⭐</td>
                    <td><?= $feedback['total_feedbacks'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Caretaker Complaints -->
    <div class="subsection">
        <h3>Caretaker Complaints</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Caretaker</th>
                    <th>Service</th>
                    <th>Complaint</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['caretakerComplaints'] ?? [] as $complaint): ?>
                <tr>
                    <td>#<?= $complaint['complaint_id'] ?></td>
                    <td><?= htmlspecialchars($complaint['client_name']) ?></td>
                    <td><?= htmlspecialchars($complaint['caretaker_name']) ?></td>
                    <td><?= htmlspecialchars($complaint['service_type']) ?></td>
                    <td><?= htmlspecialchars(substr($complaint['complaint_text'], 0, 100)) ?>...</td>
                    <td><span class="status-badge status-<?= strtolower($complaint['status']) ?>"><?= htmlspecialchars($complaint['status']) ?></span></td>
                    <td><?= date('M d, Y', strtotime($complaint['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
```

---

## Export Button Integration

### Add to both views (modify existing export buttons):

```php
<!-- Export Buttons -->
<div class="export-buttons">
    <button onclick="exportReport('csv')" class="btn btn-export">
        📄 Export CSV
    </button>
    <button onclick="exportReport('pdf')" class="btn btn-export">
        📑 Export PDF
    </button>
</div>
```

### Update JavaScript export function:

```javascript
function exportReport(format) {
    const fromDate = document.getElementById('from-date').value;
    const toDate = document.getElementById('to-date').value;

    let url = window.location.pathname + '?export=1&format=' + format;

    if (fromDate) url += '&from=' + fromDate;
    if (toDate) url += '&to=' + toDate;

    window.location.href = url;
}
```

---

## Additional CSS Styles

Add these styles to support the new sections:

```css
/* Report Sections */
.report-section {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.report-section h2 {
    color: #2c3e50;
    border-bottom: 2px solid #3498db;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.subsection {
    margin-bottom: 30px;
}

.subsection h3 {
    color: #34495e;
    margin-bottom: 15px;
}

.highlight-section {
    background-color: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 15px;
}

.section-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.chart-container {
    position: relative;
    height: 300px;
}

/* Status Badges */
.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending { background: #ffc107; color: #000; }
.status-completed { background: #28a745; color: #fff; }
.status-approved { background: #28a745; color: #fff; }
.status-confirmed { background: #17a2b8; color: #fff; }
.status-cancelled { background: #dc3545; color: #fff; }
.status-rejected { background: #dc3545; color: #fff; }
.status-active { background: #28a745; color: #fff; }
.status-inactive { background: #6c757d; color: #fff; }

/* Warning Row */
.warning-row {
    background-color: #fff3cd;
}

/* Export Buttons */
.export-buttons {
    display: flex;
    gap: 10px;
    margin: 20px 0;
}

.btn-export {
    padding: 10px 20px;
    background: #3498db;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

.btn-export:hover {
    background: #2980b9;
}

/* Responsive */
@media (max-width: 768px) {
    .section-grid {
        grid-template-columns: 1fr;
    }

    .summary-cards {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 480px) {
    .summary-cards {
        grid-template-columns: 1fr;
    }
}
```

---

## Chart.js Integration Examples

### Booking Status Chart (Admin):

```javascript
// Add after existing chart code
const bookingStatusCtx = document.getElementById('bookingStatusChart');
if (bookingStatusCtx) {
    new Chart(bookingStatusCtx, {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_column($data['bookingStatus'] ?? [], 'status')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($data['bookingStatus'] ?? [], 'count')) ?>,
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#17a2b8',
                    '#dc3545',
                    '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' }
            }
        }
    });
}
```

### Revenue by Service Chart (Admin):

```javascript
const revenueByServiceCtx = document.getElementById('revenueByServiceChart');
if (revenueByServiceCtx) {
    new Chart(revenueByServiceCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($data['revenueByService'] ?? [], 'service_type')) ?>,
            datasets: [{
                label: 'Revenue (LKR)',
                data: <?= json_encode(array_column($data['revenueByService'] ?? [], 'revenue')) ?>,
                backgroundColor: '#3498db'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}
```

---

## Quick Copy-Paste Checklist

### For admin/ad_reports.php:
- [ ] Replace summary cards section
- [ ] Add booking status section
- [ ] Add financial reports section
- [ ] Add top caretakers section
- [ ] Add top clients section
- [ ] Add quality metrics section
- [ ] Update export buttons
- [ ] Update JavaScript charts

### For hr/hr_reports.php:
- [ ] Replace summary cards section
- [ ] Add caretaker management section
- [ ] Add leave management section
- [ ] Add schedule & assignment section
- [ ] Add reschedule management section
- [ ] Add performance monitoring section
- [ ] Update export buttons
- [ ] Update JavaScript charts

### General:
- [ ] Add new CSS styles
- [ ] Test all sections display correctly
- [ ] Test export functionality
- [ ] Verify responsive design

---

**Note:** All data structures used in these snippets are provided by the `getCompleteReportData()` method in the respective models (AdminReportsModel or HrReportsModel). Make sure to check for empty arrays using `?? []` to avoid errors when no data is available.
