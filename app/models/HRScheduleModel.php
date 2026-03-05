<?php
require_once APPROOT . '/core/Database.php';

class HRScheduleModel
{
    private $conn;
    private $columnCache = [];

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }

    private function hasColumn($table, $column)
    {
        $key = $table . '.' . $column;
        if (isset($this->columnCache[$key])) {
            return $this->columnCache[$key];
        }

        $sql = "SELECT COUNT(*) AS cnt
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ss', $table, $column);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        $this->columnCache[$key] = ((int)($row['cnt'] ?? 0)) > 0;
        return $this->columnCache[$key];
    }

    private function bookingRangeExpressions()
    {
        // Calculate booking end date based on basis and duration
        // Hourly: same day, Daily: date + (duration-1) days
        // Monthly: add duration months then -1 day, Yearly: add duration years then -1 day
        $endExpr = "CASE
                      WHEN b.basis = 'Hourly' THEN b.booking_date
                      WHEN b.basis = 'Daily' THEN DATE_ADD(b.booking_date, INTERVAL (b.duration - 1) DAY)
                      WHEN b.basis = 'Monthly' THEN DATE_ADD(DATE_ADD(b.booking_date, INTERVAL b.duration MONTH), INTERVAL -1 DAY)
                      WHEN b.basis = 'Yearly' THEN DATE_ADD(DATE_ADD(b.booking_date, INTERVAL b.duration YEAR), INTERVAL -1 DAY)
                      ELSE b.booking_date
                    END";

        return [
            'start' => 'b.booking_date',
            'end'   => $endExpr
        ];
    }

    private function normalizeDate($date)
    {
        $dt = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dt || $dt->format('Y-m-d') !== $date) {
            return null;
        }
        return $dt->format('Y-m-d');
    }

    private function dateRange($startDate, $endDate)
    {
        $start = new DateTimeImmutable($startDate);
        $end = new DateTimeImmutable($endDate);

        if ($start > $end) {
            $tmp = $start;
            $start = $end;
            $end = $tmp;
        }

        $dates = [];
        $cursor = $start;
        while ($cursor <= $end) {
            $dates[] = $cursor->format('Y-m-d');
            $cursor = $cursor->modify('+1 day');
        }

        return $dates;
    }

    private function clampRange($startDate, $endDate, $visibleStart, $visibleEnd)
    {
        $start = max($startDate, $visibleStart);
        $end = min($endDate, $visibleEnd);

        if ($start > $end) {
            return null;
        }

        return [$start, $end];
    }

    private function activeStatuses()
    {
        // Accepted = confirmed & ready/executing. Advance_Paid = payment received, booking confirmed.
        return ['Accepted', 'Advance_Paid'];
    }

    private function pendingStatuses()
    {
        // Requested = new booking request. Payment_Requested = awaiting advance payment.
        // Change_Requested/Reschedule_Requested = awaiting approval but linked to existing booking.
        return ['Requested', 'Payment_Requested', 'Change_Requested', 'Reschedule_Requested'];
    }

    private function placeholders($items)
    {
        return implode(',', array_fill(0, count($items), '?'));
    }

    public function getCalendarMonthAggregates($startDate, $endDate)
    {
        $startDate = $this->normalizeDate($startDate);
        $endDate = $this->normalizeDate($endDate);

        if (!$startDate || !$endDate) {
            return [
                'start' => null,
                'end' => null,
                'dates' => [],
                'events' => []
            ];
        }

        $allDates = $this->dateRange($startDate, $endDate);
        $dates = [];
        $leaveSet = [];
        $busySet = [];

        foreach ($allDates as $d) {
            $dates[$d] = [
                'date' => $d,
                'active' => 0,
                'pending' => 0,
                'leave' => 0,
                'busy' => 0,
                'available' => 0
            ];
        }

        $totalActiveCaretakers = 0;
        $caretakerStmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM caretakers WHERE status = 'Active'");
        $caretakerStmt->execute();
        $caretakerRow = $caretakerStmt->get_result()->fetch_assoc();
        $totalActiveCaretakers = (int)($caretakerRow['total'] ?? 0);

        $rangeExpr = $this->bookingRangeExpressions();
        $startExpr = $rangeExpr['start'];
        $endExpr = $rangeExpr['end'];

        $activeStatuses = $this->activeStatuses();
        $activeSql = "SELECT
                        b.id,
                        b.caretaker_id,
                        {$startExpr} AS effective_start,
                        {$endExpr} AS effective_end
                      FROM bookings b
                      WHERE b.status IN (" . $this->placeholders($activeStatuses) . ")
                        AND {$startExpr} <= ?
                        AND {$endExpr} >= ?";
        $activeStmt = $this->conn->prepare($activeSql);
        $activeTypes = str_repeat('s', count($activeStatuses)) . 'ss';
        $activeParams = array_merge($activeStatuses, [$endDate, $startDate]);
        $activeStmt->bind_param($activeTypes, ...$activeParams);
        $activeStmt->execute();
        $activeRows = $activeStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($activeRows as $row) {
            $effectiveStart = $row['effective_start'] ?: $startDate;
            $effectiveEnd = $row['effective_end'] ?: $effectiveStart;
            $clamped = $this->clampRange($effectiveStart, $effectiveEnd, $startDate, $endDate);
            if (!$clamped) continue;

            [$from, $to] = $clamped;
            foreach ($this->dateRange($from, $to) as $d) {
                $dates[$d]['active']++;
            }
        }

        $leaveSql = "SELECT l.user_id, l.start_date, l.end_date
                     FROM leaves l
                     WHERE LOWER(TRIM(l.status)) = 'approved'
                       AND l.start_date <= ?
                       AND l.end_date >= ?";
        $leaveStmt = $this->conn->prepare($leaveSql);
        $leaveStmt->bind_param('ss', $endDate, $startDate);
        $leaveStmt->execute();
        $leaveRows = $leaveStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($leaveRows as $row) {
            $leaveStart = $row['start_date'];
            $leaveEnd = $row['end_date'];
            $clamped = $this->clampRange($leaveStart, $leaveEnd, $startDate, $endDate);
            if (!$clamped) continue;

            [$from, $to] = $clamped;
            $caretakerId = (int)$row['user_id'];

            foreach ($this->dateRange($from, $to) as $d) {
                if (!isset($leaveSet[$d])) $leaveSet[$d] = [];
                $leaveSet[$d][$caretakerId] = true;
            }
        }

        foreach ($activeRows as $row) {
            $effectiveStart = $row['effective_start'] ?: $startDate;
            $effectiveEnd = $row['effective_end'] ?: $effectiveStart;
            $clamped = $this->clampRange($effectiveStart, $effectiveEnd, $startDate, $endDate);
            if (!$clamped) continue;

            [$from, $to] = $clamped;
            $caretakerId = (int)$row['caretaker_id'];

            foreach ($this->dateRange($from, $to) as $d) {
                if (isset($leaveSet[$d][$caretakerId])) {
                    continue;
                }
                if (!isset($busySet[$d])) $busySet[$d] = [];
                $busySet[$d][$caretakerId] = true;
            }
        }

        $pendingStatuses = $this->pendingStatuses();
        // For pending requests: show from TODAY until booking_date so HR can take action before the date
        $today = date('Y-m-d');
        $pendingSql = "SELECT b.booking_date
                       FROM bookings b
                       WHERE b.status IN (" . $this->placeholders($pendingStatuses) . ")
                         AND b.booking_date >= ?
                         AND b.booking_date <= ?";
        $pendingStmt = $this->conn->prepare($pendingSql);
        $pendingTypes = str_repeat('s', count($pendingStatuses)) . 'ss';
        $pendingParams = array_merge($pendingStatuses, [$startDate, $endDate]);
        $pendingStmt->bind_param($pendingTypes, ...$pendingParams);
        $pendingStmt->execute();
        $pendingRows = $pendingStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($pendingRows as $row) {
            // Show pending from TODAY until booking_date (inclusive)
            $bookingDate = $row['booking_date'];
            $pendingStart = max($today, $startDate);  // Start from today or month start
            $pendingEnd = min($bookingDate, $endDate); // End on booking date or month end

            if ($pendingStart <= $pendingEnd) {
                foreach ($this->dateRange($pendingStart, $pendingEnd) as $d) {
                    if (isset($dates[$d])) {
                        $dates[$d]['pending']++;
                    }
                }
            }
        }

        $events = [];
        foreach ($dates as $dayKey => &$dayData) {
            $leaveCount = count($leaveSet[$dayKey] ?? []);
            $busyCount = count($busySet[$dayKey] ?? []);

            $dayData['leave'] = $leaveCount;
            $dayData['busy'] = $busyCount;
            $dayData['available'] = max($totalActiveCaretakers - $leaveCount - $busyCount, 0);

            if (($dayData['active'] + $dayData['pending'] + $dayData['leave'] + $dayData['available']) > 0) {
                $events[] = [
                    'title' => "A:{$dayData['active']} P:{$dayData['pending']} L:{$dayData['leave']} Av:{$dayData['available']}",
                    'start' => $dayKey,
                    'allDay' => true,
                    'display' => 'background',
                    'extendedProps' => [
                        'active' => $dayData['active'],
                        'pending' => $dayData['pending'],
                        'leave' => $dayData['leave'],
                        'busy' => $dayData['busy'],
                        'available' => $dayData['available']
                    ]
                ];
            }
        }

        return [
            'start' => $startDate,
            'end' => $endDate,
            'dates' => $dates,
            'events' => $events,
            'meta' => [
                'total_active_caregivers' => $totalActiveCaretakers,
                'busy_rule' => 'Busy excludes caregivers on approved leave for the selected date.'
            ]
        ];
    }

    public function getDayDetails($date)
    {
        $date = $this->normalizeDate($date);
        if (!$date) {
            return null;
        }

        $rangeExpr = $this->bookingRangeExpressions();
        $startExpr = $rangeExpr['start'];
        $endExpr = $rangeExpr['end'];

        $leaveSql = "SELECT
                        l.id,
                        l.user_id AS caretaker_id,
                        c.name AS caretaker_name,
                        c.service_type,
                        l.leave_type,
                        l.start_date,
                        l.end_date,
                        l.reason,
                        l.status
                    FROM leaves l
                    INNER JOIN caretakers c ON c.id = l.user_id
                    WHERE LOWER(TRIM(l.status)) = 'approved'
                      AND ? BETWEEN l.start_date AND l.end_date
                    ORDER BY c.name ASC";
        $leaveStmt = $this->conn->prepare($leaveSql);
        $leaveStmt->bind_param('s', $date);
        $leaveStmt->execute();
        $leaveRows = $leaveStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $leaveIds = [];
        foreach ($leaveRows as $leave) {
            $leaveIds[(int)$leave['caretaker_id']] = true;
        }

        $activeStatuses = $this->activeStatuses();
        $activeSql = "SELECT
                        b.id AS booking_id,
                        b.client_id,
                        b.caretaker_id,
                        c.name AS client_name,
                        ct.name AS caretaker_name,
                        ct.service_type,
                        ct.location,
                        b.service_type AS booking_service_type,
                        b.preferred_time,
                        b.status,
                        b.basis,
                        b.duration,
                        b.booking_date,
                        {$startExpr} AS effective_start,
                        {$endExpr} AS effective_end
                      FROM bookings b
                      INNER JOIN clients c ON c.id = b.client_id
                      INNER JOIN caretakers ct ON ct.id = b.caretaker_id
                      WHERE b.status IN (" . $this->placeholders($activeStatuses) . ")
                        AND ? BETWEEN {$startExpr} AND {$endExpr}
                      ORDER BY b.preferred_time ASC, ct.name ASC";
        $activeStmt = $this->conn->prepare($activeSql);
        $activeTypes = str_repeat('s', count($activeStatuses)) . 's';
        $activeParams = array_merge($activeStatuses, [$date]);
        $activeStmt->bind_param($activeTypes, ...$activeParams);
        $activeStmt->execute();
        $activeRows = $activeStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $busyMap = [];
        foreach ($activeRows as $booking) {
            $caretakerId = (int)$booking['caretaker_id'];
            if (isset($leaveIds[$caretakerId])) {
                continue;
            }

            if (!isset($busyMap[$caretakerId])) {
                $busyMap[$caretakerId] = [
                    'caretaker_id' => $caretakerId,
                    'caretaker_name' => $booking['caretaker_name'],
                    'service_type' => $booking['service_type'],
                    'location' => $booking['location'],
                    'booking_count' => 0
                ];
            }

            $busyMap[$caretakerId]['booking_count']++;
        }

        $pendingStatuses = $this->pendingStatuses();
        // Pending requests visible from TODAY until booking_date (so HR can take action)
        $pendingSql = "SELECT
                        b.id AS booking_id,
                        b.client_id,
                        b.caretaker_id,
                        c.name AS client_name,
                        ct.name AS caretaker_name,
                        b.service_type,
                        b.preferred_time,
                        b.booking_date,
                        b.basis,
                        b.duration,
                        b.status,
                        {$startExpr} AS effective_start,
                        {$endExpr} AS effective_end
                      FROM bookings b
                      INNER JOIN clients c ON c.id = b.client_id
                      INNER JOIN caretakers ct ON ct.id = b.caretaker_id
                      WHERE b.status IN (" . $this->placeholders($pendingStatuses) . ")
                        AND b.booking_date >= ?
                        AND b.booking_date >= DATE(NOW())
                      ORDER BY b.booking_date ASC";
        $pendingStmt = $this->conn->prepare($pendingSql);
        $pendingTypes = str_repeat('s', count($pendingStatuses)) . 's';
        $pendingParams = array_merge($pendingStatuses, [$date]);
        $pendingStmt->bind_param($pendingTypes, ...$pendingParams);
        $pendingStmt->execute();
        $pendingRows = $pendingStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $caretakerSql = "SELECT id, name, service_type, location
                         FROM caretakers
                         WHERE status = 'Active'
                         ORDER BY name ASC";
        $caretakerRows = $this->conn->query($caretakerSql)->fetch_all(MYSQLI_ASSOC);

        $busyIds = array_map('intval', array_keys($busyMap));
        $busyLookup = array_fill_keys($busyIds, true);

        $availableRows = [];
        foreach ($caretakerRows as $caretaker) {
            $caretakerId = (int)$caretaker['id'];
            if (isset($leaveIds[$caretakerId])) continue;
            if (isset($busyLookup[$caretakerId])) continue;
            $availableRows[] = [
                'caretaker_id' => $caretakerId,
                'caretaker_name' => $caretaker['name'],
                'service_type' => $caretaker['service_type'],
                'location' => $caretaker['location']
            ];
        }

        return [
            'date' => $date,
            'summary' => [
                'active_bookings' => count($activeRows),
                'pending_requests' => count($pendingRows),
                'caregiver_leaves' => count($leaveRows),
                'busy_caregivers' => count($busyMap),
                'available_caregivers' => count($availableRows),
                'busy_rule' => 'Busy excludes caregivers on approved leave for the selected date.'
            ],
            'lists' => [
                'active_bookings' => $activeRows,
                'pending_requests' => $pendingRows,
                'leave_list' => $leaveRows,
                'busy_caregivers' => array_values($busyMap),
                'available_caregivers' => $availableRows
            ]
        ];
    }

    public function getSchemaAssumptions()
    {
        return [
            'tables' => [
                'bookings(id, client_id, caretaker_id, status, booking_date, basis, duration)',
                'caretakers(id, name, service_type, location, status)',
                'leaves(id, user_id, start_date, end_date, status)'
            ],
            'booking_period_calculation' => [
                'Hourly' => 'booking_date (same day)',
                'Daily' => 'booking_date + (duration - 1) days',
                'Monthly' => 'booking_date + duration months - 1 day',
                'Yearly' => 'booking_date + duration years - 1 day'
            ],
            'index_sql' => [
                'CREATE INDEX idx_bookings_status_date_caretaker ON bookings(status, booking_date, caretaker_id);',
                'CREATE INDEX idx_bookings_basis_duration ON bookings(basis, duration);'
            ],
            'notes' => [
                'Booking end date is calculated dynamically from booking_date + basis + duration',
                'Active bookings will show across entire booking period in calendar and day views'
            ]
        ];
    }
}
