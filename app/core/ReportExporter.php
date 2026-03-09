<?php

/**
 * ReportExporter
 * Utility class for exporting reports to CSV and PDF formats
 */
class ReportExporter
{
    /**
     * Export data to CSV format
     * @param array $data Report data
     * @param string $filename Filename without extension
     * @param string $reportType Type of report (admin/hr)
     */
    public static function exportToCSV($data, $filename, $reportType = 'admin')
    {
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add BOM for Excel UTF-8 support
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Write report header
        fputcsv($output, ['SmartCare ' . ucfirst($reportType) . ' Report']);
        fputcsv($output, ['Generated: ' . date('Y-m-d H:i:s')]);
        fputcsv($output, []); // Empty line

        // Write summary statistics if available
        if (isset($data['summary'])) {
            fputcsv($output, ['SUMMARY STATISTICS']);
            foreach ($data['summary'] as $key => $value) {
                fputcsv($output, [ucwords(str_replace('_', ' ', $key)), $value]);
            }
            fputcsv($output, []); // Empty line
        }

        // Export different sections based on report type
        if ($reportType === 'admin') {
            self::exportAdminData($output, $data);
        } else {
            self::exportHrData($output, $data);
        }

        fclose($output);
        exit;
    }

    /**
     * Export Admin-specific data sections
     */
    private static function exportAdminData($output, $data)
    {
        // Booking Status Breakdown
        if (isset($data['bookingStatus']) && !empty($data['bookingStatus'])) {
            fputcsv($output, ['BOOKING STATUS BREAKDOWN']);
            fputcsv($output, ['Status', 'Count']);
            foreach ($data['bookingStatus'] as $row) {
                fputcsv($output, [$row['status'], $row['count']]);
            }
            fputcsv($output, []);
        }

        // Revenue by Service Type
        if (isset($data['revenueByService']) && !empty($data['revenueByService'])) {
            fputcsv($output, ['REVENUE BY SERVICE TYPE']);
            fputcsv($output, ['Service Type', 'Revenue (LKR)', 'Bookings']);
            foreach ($data['revenueByService'] as $row) {
                fputcsv($output, [
                    $row['service_type'],
                    number_format($row['revenue'], 2),
                    $row['bookings']
                ]);
            }
            fputcsv($output, []);
        }

        // Top Caretakers by Bookings
        if (isset($data['topCaretakersByBookings']) && !empty($data['topCaretakersByBookings'])) {
            fputcsv($output, ['TOP CARETAKERS BY BOOKINGS']);
            fputcsv($output, ['Name', 'Service Type', 'Total Bookings', 'Avg Rating']);
            foreach ($data['topCaretakersByBookings'] as $row) {
                fputcsv($output, [
                    $row['name'],
                    $row['service_type'],
                    $row['total_bookings'],
                    number_format($row['avg_rating'], 2)
                ]);
            }
            fputcsv($output, []);
        }

        // Top Caretakers by Revenue
        if (isset($data['topCaretakersByRevenue']) && !empty($data['topCaretakersByRevenue'])) {
            fputcsv($output, ['TOP CARETAKERS BY REVENUE']);
            fputcsv($output, ['Name', 'Service Type', 'Total Revenue (LKR)', 'Total Bookings']);
            foreach ($data['topCaretakersByRevenue'] as $row) {
                fputcsv($output, [
                    $row['name'],
                    $row['service_type'],
                    number_format($row['total_revenue'], 2),
                    $row['total_bookings']
                ]);
            }
            fputcsv($output, []);
        }

        // Top Clients by Spending
        if (isset($data['topClientsBySpending']) && !empty($data['topClientsBySpending'])) {
            fputcsv($output, ['TOP CLIENTS BY SPENDING']);
            fputcsv($output, ['Name', 'Email', 'Total Bookings', 'Total Spent (LKR)']);
            foreach ($data['topClientsBySpending'] as $row) {
                fputcsv($output, [
                    $row['name'],
                    $row['email'],
                    $row['total_bookings'],
                    number_format($row['total_spent'], 2)
                ]);
            }
            fputcsv($output, []);
        }

        // Service-wise Ratings
        if (isset($data['serviceRatings']) && !empty($data['serviceRatings'])) {
            fputcsv($output, ['SERVICE-WISE AVERAGE RATINGS']);
            fputcsv($output, ['Service Type', 'Average Rating', 'Feedback Count']);
            foreach ($data['serviceRatings'] as $row) {
                fputcsv($output, [
                    $row['service_type'],
                    number_format($row['avg_rating'], 2),
                    $row['feedback_count']
                ]);
            }
            fputcsv($output, []);
        }

        // Low-rated Bookings
        if (isset($data['lowRatedBookings']) && !empty($data['lowRatedBookings'])) {
            fputcsv($output, ['LOW-RATED BOOKINGS (< 3.0)']);
            fputcsv($output, ['Booking ID', 'Caretaker', 'Client', 'Service', 'Rating', 'Date']);
            foreach ($data['lowRatedBookings'] as $row) {
                fputcsv($output, [
                    $row['booking_id'],
                    $row['caretaker_name'],
                    $row['client_name'],
                    $row['service_type'],
                    $row['rating'],
                    $row['booking_date']
                ]);
            }
            fputcsv($output, []);
        }
    }

    /**
     * Export HR-specific data sections
     */
    private static function exportHrData($output, $data)
    {
        // Caretaker Status Breakdown
        if (isset($data['caretakerStatus']) && !empty($data['caretakerStatus'])) {
            fputcsv($output, ['CARETAKER STATUS BREAKDOWN']);
            fputcsv($output, ['Status', 'Count']);
            foreach ($data['caretakerStatus'] as $row) {
                fputcsv($output, [$row['status'], $row['count']]);
            }
            fputcsv($output, []);
        }

        // Newly Added Caretakers
        if (isset($data['newCaretakers']) && !empty($data['newCaretakers'])) {
            fputcsv($output, ['NEWLY ADDED CARETAKERS (Last 30 Days)']);
            fputcsv($output, ['ID', 'Name', 'Service Type', 'Status', 'Joined Date']);
            foreach ($data['newCaretakers'] as $row) {
                fputcsv($output, [
                    $row['caretaker_id'],
                    $row['name'],
                    $row['service_type'],
                    $row['status'],
                    $row['joined_date']
                ]);
            }
            fputcsv($output, []);
        }

        // Pending Leave Requests
        if (isset($data['pendingLeaves']) && !empty($data['pendingLeaves'])) {
            fputcsv($output, ['PENDING LEAVE REQUESTS']);
            fputcsv($output, ['ID', 'Caretaker', 'Service Type', 'Leave Type', 'From', 'To', 'Reason']);
            foreach ($data['pendingLeaves'] as $row) {
                fputcsv($output, [
                    $row['leave_id'],
                    $row['caretaker_name'],
                    $row['service_type'],
                    $row['leave_type'],
                    $row['from_date'],
                    $row['to_date'],
                    $row['reason']
                ]);
            }
            fputcsv($output, []);
        }

        // Caretakers On Leave
        if (isset($data['caretakersOnLeave']) && !empty($data['caretakersOnLeave'])) {
            fputcsv($output, ['CARETAKERS CURRENTLY ON LEAVE']);
            fputcsv($output, ['Name', 'Service Type', 'Leave Type', 'From', 'To', 'Days Remaining']);
            foreach ($data['caretakersOnLeave'] as $row) {
                fputcsv($output, [
                    $row['name'],
                    $row['service_type'],
                    $row['leave_type'],
                    $row['from_date'],
                    $row['to_date'],
                    $row['days_remaining']
                ]);
            }
            fputcsv($output, []);
        }

        // Unassigned Bookings
        if (isset($data['unassignedBookings']) && !empty($data['unassignedBookings'])) {
            fputcsv($output, ['UNASSIGNED BOOKINGS']);
            fputcsv($output, ['ID', 'Client', 'Service', 'Date', 'Time', 'Duration', 'Basis', 'Location']);
            foreach ($data['unassignedBookings'] as $row) {
                fputcsv($output, [
                    $row['booking_id'],
                    $row['client_name'],
                    $row['service_type'],
                    $row['booking_date'],
                    $row['preferred_time'],
                    $row['duration'],
                    $row['basis'],
                    $row['district']
                ]);
            }
            fputcsv($output, []);
        }

        // Upcoming Schedules
        if (isset($data['upcomingSchedules']) && !empty($data['upcomingSchedules'])) {
            fputcsv($output, ['UPCOMING SCHEDULES (Next 7 Days)']);
            fputcsv($output, ['Booking ID', 'Caretaker', 'Client', 'Service', 'Date', 'Time', 'Status']);
            foreach ($data['upcomingSchedules'] as $row) {
                fputcsv($output, [
                    $row['booking_id'],
                    $row['caretaker_name'],
                    $row['client_name'],
                    $row['service_type'],
                    $row['booking_date'],
                    $row['preferred_time'],
                    $row['status']
                ]);
            }
            fputcsv($output, []);
        }

        // Pending Reschedule Requests
        if (isset($data['pendingReschedules']) && !empty($data['pendingReschedules'])) {
            fputcsv($output, ['PENDING RESCHEDULE REQUESTS']);
            fputcsv($output, ['Request ID', 'Booking ID', 'Client', 'Caretaker', 'Current Date', 'Requested Date', 'Reason']);
            foreach ($data['pendingReschedules'] as $row) {
                fputcsv($output, [
                    $row['request_id'],
                    $row['booking_id'],
                    $row['client_name'],
                    $row['caretaker_name'] ?? 'Unassigned',
                    $row['current_date'],
                    $row['requested_date'],
                    $row['reason']
                ]);
            }
            fputcsv($output, []);
        }

        // Caretaker Feedback Summary
        if (isset($data['caretakerFeedback']) && !empty($data['caretakerFeedback'])) {
            fputcsv($output, ['CARETAKER FEEDBACK SUMMARY']);
            fputcsv($output, ['Name', 'Service Type', 'Avg Rating', 'Total Feedbacks']);
            foreach ($data['caretakerFeedback'] as $row) {
                fputcsv($output, [
                    $row['name'],
                    $row['service_type'],
                    number_format($row['avg_rating'], 2),
                    $row['total_feedbacks']
                ]);
            }
            fputcsv($output, []);
        }

        // Caretaker Complaints
        if (isset($data['caretakerComplaints']) && !empty($data['caretakerComplaints'])) {
            fputcsv($output, ['CARETAKER COMPLAINTS']);
            fputcsv($output, ['ID', 'Client', 'Caretaker', 'Service', 'Complaint', 'Status', 'Date']);
            foreach ($data['caretakerComplaints'] as $row) {
                fputcsv($output, [
                    $row['complaint_id'],
                    $row['client_name'],
                    $row['caretaker_name'],
                    $row['service_type'],
                    substr($row['complaint_text'], 0, 100), // Truncate for readability
                    $row['status'],
                    date('Y-m-d', strtotime($row['created_at']))
                ]);
            }
            fputcsv($output, []);
        }
    }

    /**
     * Export data to PDF format (basic HTML-to-PDF approach)
     * For production, consider using libraries like TCPDF or mPDF
     * @param array $data Report data
     * @param string $filename Filename without extension
     * @param string $reportType Type of report (admin/hr)
     */
    public static function exportToPDF($data, $filename, $reportType = 'admin')
    {
        // For a simple PDF, we can use HTML-to-PDF conversion
        // This is a placeholder - you would integrate a PDF library here

        header('Content-Type: text/html; charset=utf-8');

        echo '<!DOCTYPE html>';
        echo '<html><head>';
        echo '<meta charset="UTF-8">';
        echo '<title>' . $filename . '</title>';
        echo '<style>';
        echo 'body { font-family: Arial, sans-serif; margin: 20px; }';
        echo 'h1 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }';
        echo 'h2 { color: #34495e; margin-top: 30px; border-bottom: 1px solid #bdc3c7; padding-bottom: 5px; }';
        echo 'table { border-collapse: collapse; width: 100%; margin: 20px 0; }';
        echo 'th { background-color: #3498db; color: white; padding: 10px; text-align: left; }';
        echo 'td { border: 1px solid #ddd; padding: 8px; }';
        echo 'tr:nth-child(even) { background-color: #f2f2f2; }';
        echo '.summary { background-color: #ecf0f1; padding: 15px; border-radius: 5px; margin: 20px 0; }';
        echo '.summary-item { margin: 10px 0; }';
        echo '@media print { button { display: none; } }';
        echo '</style>';
        echo '</head><body>';

        echo '<button onclick="window.print()">Print / Save as PDF</button>';

        echo '<h1>SmartCare ' . ucfirst($reportType) . ' Report</h1>';
        echo '<p><strong>Generated:</strong> ' . date('Y-m-d H:i:s') . '</p>';

        // Summary statistics
        if (isset($data['summary'])) {
            echo '<div class="summary">';
            echo '<h2>Summary Statistics</h2>';
            foreach ($data['summary'] as $key => $value) {
                echo '<div class="summary-item"><strong>' . ucwords(str_replace('_', ' ', $key)) . ':</strong> ' . $value . '</div>';
            }
            echo '</div>';
        }

        // Generate tables based on report type
        if ($reportType === 'admin') {
            self::renderAdminPDFSections($data);
        } else {
            self::renderHrPDFSections($data);
        }

        echo '</body></html>';
        exit;
    }

    /**
     * Render Admin PDF sections
     */
    private static function renderAdminPDFSections($data)
    {
        // Booking Status
        if (isset($data['bookingStatus']) && !empty($data['bookingStatus'])) {
            echo '<h2>Booking Status Breakdown</h2>';
            echo '<table>';
            echo '<tr><th>Status</th><th>Count</th></tr>';
            foreach ($data['bookingStatus'] as $row) {
                echo '<tr><td>' . htmlspecialchars($row['status']) . '</td><td>' . $row['count'] . '</td></tr>';
            }
            echo '</table>';
        }

        // Top Caretakers by Revenue
        if (isset($data['topCaretakersByRevenue']) && !empty($data['topCaretakersByRevenue'])) {
            echo '<h2>Top Caretakers by Revenue</h2>';
            echo '<table>';
            echo '<tr><th>Name</th><th>Service Type</th><th>Revenue (LKR)</th><th>Bookings</th></tr>';
            foreach ($data['topCaretakersByRevenue'] as $row) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['service_type']) . '</td>';
                echo '<td>LKR ' . number_format($row['total_revenue'], 2) . '</td>';
                echo '<td>' . $row['total_bookings'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    }

    /**
     * Render HR PDF sections
     */
    private static function renderHrPDFSections($data)
    {
        // Caretaker Status
        if (isset($data['caretakerStatus']) && !empty($data['caretakerStatus'])) {
            echo '<h2>Caretaker Status Breakdown</h2>';
            echo '<table>';
            echo '<tr><th>Status</th><th>Count</th></tr>';
            foreach ($data['caretakerStatus'] as $row) {
                echo '<tr><td>' . htmlspecialchars($row['status']) . '</td><td>' . $row['count'] . '</td></tr>';
            }
            echo '</table>';
        }

        // Pending Leaves
        if (isset($data['pendingLeaves']) && !empty($data['pendingLeaves'])) {
            echo '<h2>Pending Leave Requests</h2>';
            echo '<table>';
            echo '<tr><th>Caretaker</th><th>Service Type</th><th>Leave Type</th><th>From</th><th>To</th></tr>';
            foreach ($data['pendingLeaves'] as $row) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['caretaker_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['service_type']) . '</td>';
                echo '<td>' . htmlspecialchars($row['leave_type']) . '</td>';
                echo '<td>' . $row['from_date'] . '</td>';
                echo '<td>' . $row['to_date'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    }
}
