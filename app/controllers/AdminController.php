<?php
require_once APPROOT . '/models/ComplaintModel.php';

class AdminController extends Controller
{

    private $caretakerModel;
    private $userModel;

    private $announcementModel;

    private $clientModel;
    private $adminLeaveModel;

    private $complaintModel;

    private $feedbackModel;

    private $historyModel;
    private $profileChangeRequestModel;
    private $adminPaymentModel;
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();

        if (!AuthSession::hasRole('admin')) {
            header("Location: index.php?url=auth/login");
            exit;
        }

        $this->caretakerModel = $this->model('CaretakerModel');

        $this->userModel = $this->model('UserModel');
        $this->announcementModel = $this->model('AnnouncementModel');
        $this->clientModel = $this->model('ClientModel');
        $this->adminLeaveModel = $this->model('AdminLeaveModel');
        $this->complaintModel = $this->model('ComplaintModel');
        $this->feedbackModel = $this->model('FeedbackModel');
        $this->historyModel = $this->model('HistoryModel');
        $this->profileChangeRequestModel = $this->model('ProfileChangeRequestModel');
        $this->adminPaymentModel = $this->model('AdminPaymentModel');
        // Revalidate caretaker from DB
        $user = $this->userModel->getUserById(AuthSession::profileId()); // lowercase usage
        if (!$user) {
            session_destroy();
            header("Location: index.php?url=auth/login");
            exit;
        }

        $_SESSION['user'] = $user;
    }

    public function ad_dashboard()
    {
        // Top stats
        $totalCaregivers = $this->caretakerModel->countCaretakers();
        $totalClients    = $this->clientModel->countClients();
        $upcomingBookings = $this->clientModel->countUpcomingBookings();
        $pendingLeaves   = $this->adminLeaveModel->countPendingLeaves();

        $paymentSummary = $this->adminPaymentModel->getPaymentSummary([]);
        $pendingProfileRequests = $this->profileChangeRequestModel->countPending();

        $complaints = $this->complaintModel->getAllComplaints();
        $openComplaints = 0;
        foreach ($complaints as $c) {
            if (strtolower((string) ($c['status'] ?? '')) === 'open') {
                $openComplaints++;
            }
        }

        $staffCount = count($this->userModel->getAllUsers());
        $feedbackEntries = $this->feedbackModel->getAll();
        $feedbackCount = is_array($feedbackEntries) ? count($feedbackEntries) : 0;

        // Charts: line/bar (bottom) + doughnut overview (replaces recent activity)
        $bookingStats = $this->clientModel->getBookingsLast4Weeks();
        $engagement   = $this->clientModel->getClientEngagementLast6Months();
        $bookingStatusDist = $this->clientModel->getBookingStatusDistribution();
        $caretakerStatusDist = $this->caretakerModel->getCaretakerStatusDistribution();

        $payTotal = (int) ($paymentSummary['total_records'] ?? 0);
        $payPending = (int) ($paymentSummary['pending_count'] ?? 0);
        $payRejected = (int) ($paymentSummary['rejected_count'] ?? 0);
        $payApproved = max(0, $payTotal - $payPending - $payRejected);

        $this->view("admin/ad_dashboard", [
            'stats' => [
                'totalCaregivers' => $totalCaregivers,
                'totalClients' => $totalClients,
                'upcomingBookings' => $upcomingBookings,
                'pendingLeaves' => $pendingLeaves,
                'totalCollected' => (float) ($paymentSummary['total_collected'] ?? 0),
            ],
            'review' => [
                'pendingPayments' => (int) ($paymentSummary['pending_count'] ?? 0),
                'rejectedPayments' => (int) ($paymentSummary['rejected_count'] ?? 0),
                'pendingLeaves' => $pendingLeaves,
                'pendingProfileRequests' => $pendingProfileRequests,
                'openComplaints' => $openComplaints,
                'staffCount' => $staffCount,
                'feedbackCount' => $feedbackCount,
            ],
            'bookingStats' => $bookingStats,
            'engagement' => $engagement,
            'chartPaymentStatus' => [
                'labels' => ['Pending', 'Approved', 'Rejected'],
                'values' => [$payPending, $payApproved, $payRejected],
            ],
            'chartBookingStatus' => $bookingStatusDist,
            'chartCaretakerStatus' => $caretakerStatusDist,
        ]);
    }


    public function ad_leave()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $type   = $_GET['type'] ?? 'All';
        $status = $_GET['status'] ?? 'All';

        $leaveModel = $this->model('AdminLeaveModel');

        $leaves = $leaveModel->getLeavesPaginatedFiltered($limit, $offset, $type, $status);
        $totalLeaves = $leaveModel->getTotalLeavesFiltered($type, $status);

        $totalPages = (int) ceil($totalLeaves / $limit);
        if ($totalPages < 1) $totalPages = 1;

        $data = [
            'leaves'       => $leaves,
            'currentPage'  => $page,
            'totalPages'   => $totalPages,
            'selectedType' => $type,
            'selectedStatus' => $status
        ];

        $this->view('admin/ad_leave', $data);
    }


    public function update_leave_status($id, $status)
    {
        // 1️⃣ Update DB
        $this->adminLeaveModel->updateLeaveStatus($id, $status);

        // 2️⃣ Log admin action
        $this->historyModel->log([
            'user_id' => AuthSession::profileId(),
            'username' => $_SESSION['user']['username'],   // adjust if needed
            'role' => $_SESSION['user']['role'],
            'action' => "Updated leave status to $status (Leave ID: $id)",
            'section' => "Leaves"
        ]);

        // 3️⃣ Redirect
        header('Location: ' . URLROOT . '/admin/ad_leave');
        exit();
    }


    public function ad_history()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        $limit  = 10; // logs per page
        $offset = ($page - 1) * $limit;

        $historyModel = $this->model('HistoryModel'); // change to your model name

        $logs = $historyModel->getLogsPaginated($limit, $offset);
        $totalLogs = $historyModel->getTotalLogs();

        $totalPages = (int) ceil($totalLogs / $limit);
        if ($totalPages < 1) $totalPages = 1;

        $data = [
            'logs' => $logs,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ];

        $this->view('admin/ad_history', $data);
    }


    public function ad_caretakers()
    {
        header("Location: " . URLROOT . "/CaretakerCRUD/list?page=1");
        exit;
    }

    public function caretaker_add()
    {
        // Redirect to CaretakerCRUD controller
        header("Location: " . URLROOT . "/CaretakerCRUD/add");
        exit;
    }


    public function ad_announcement()
    {
        $announcements = $this->announcementModel->getAllAnnouncements();
        $this->view("admin/ad_announcement", [
            'announcements' => $announcements
        ]);
    }
    public function ad_clients()
    {
        $filters = [
            'q' => trim((string)($_GET['q'] ?? '')),
            'date_from' => trim((string)($_GET['date_from'] ?? '')),
            'date_to' => trim((string)($_GET['date_to'] ?? '')),
        ];
        $clients = $this->clientModel->getAllClientsFiltered($filters);
        $this->view('admin/ad_clients', [
            'clients' => $clients,
            'filters' => $filters,
        ]);
    }

    public function ad_users()
    {
        header('Location: ' . URLROOT . '/userCRUD/list');
        exit;
    }

    public function ad_feedback()
    {
        $complaints = $this->complaintModel->getAllComplaints();
        $feedbacks = $this->feedbackModel->getAll();

        $this->view("admin/ad_feedback", [
            'complaints' => $complaints,
            'feedbacks' => $feedbacks
        ]);
    }

    public function ad_settings()
    {
        // Session already started in constructor
        if (!AuthSession::isLoggedIn()) {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        // Optional: allow only admin role
        if (!AuthSession::hasRole('admin')) {
            die("Access denied. Only admin can access this page.");
        }

        // Use session user directly
        $user = $_SESSION['user'];

        $this->view('admin/ad_settings', ['user' => $user]);
    }

    public function ad_reports()
    {
        // Check for export request
        if (isset($_GET['export'])) {
            $this->exportAdminReport();
            return;
        }

        $reportsModel = $this->model('AdminReportsModel');

        $fromDate = $_GET['from'] ?? null;
        $toDate = $_GET['to'] ?? null;

        // Get comprehensive report data using the specialized admin model
        $data = $reportsModel->getCompleteReportData($fromDate, $toDate);
        $data['fromDate'] = $fromDate;
        $data['toDate'] = $toDate;

        $this->view("admin/ad_reports", $data);
    }

    /**
     * AJAX endpoint to fetch filtered report data
     */
    public function getReportData()
    {
        header('Content-Type: application/json');

        $reportsModel = $this->model('AdminReportsModel');

        $fromDate = $_GET['from'] ?? null;
        $toDate = $_GET['to'] ?? null;

        $data = $reportsModel->getCompleteReportData($fromDate, $toDate);

        echo json_encode($data);
        exit;
    }

    /**
     * Export admin report to CSV or PDF
     */
    private function exportAdminReport()
    {
        require_once APPROOT . '/core/ReportExporter.php';

        $reportsModel = $this->model('AdminReportsModel');

        $fromDate = $_GET['from'] ?? null;
        $toDate = $_GET['to'] ?? null;
        $format = $_GET['format'] ?? 'csv'; // csv or pdf

        // Get all report data
        $data = $reportsModel->getCompleteReportData($fromDate, $toDate);

        // Generate filename
        $dateRange = ($fromDate && $toDate) ? "_" . $fromDate . "_to_" . $toDate : "_all_time";
        $filename = "admin_report" . $dateRange;

        // Export based on format
        if ($format === 'pdf') {
            ReportExporter::exportToPDF($data, $filename, 'admin');
        } else {
            ReportExporter::exportToCSV($data, $filename, 'admin');
        }
    }

    public function ad_payments()
    {
        $filters = $this->getPaymentFilters();

        // Export request keeps existing filters and bypasses page render.
        if (isset($_GET['export'])) {
            $format = strtolower((string)($_GET['format'] ?? 'csv'));
            if ($format === 'pdf') {
                $this->exportPaymentReportPDF($filters);
            } else {
                $this->exportPaymentReportCSV($filters);
            }
            return;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }

        $limit = 20;
        $offset = ($page - 1) * $limit;

        $payments = $this->adminPaymentModel->getPayments($filters, $limit, $offset);
        $summary = $this->adminPaymentModel->getPaymentSummary($filters);
        $totalRecords = $this->adminPaymentModel->getPaymentsCount($filters);
        $totalPages = (int)ceil($totalRecords / $limit);
        if ($totalPages < 1) {
            $totalPages = 1;
        }

        $this->view("admin/ad_payments", [
            'summary' => $summary,
            'payments' => $payments,
            'filters' => $filters,
            'filterOptions' => $this->adminPaymentModel->getFilterOptions(),
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords,
        ]);
    }

    public function ad_payment_detail($paymentId = null)
    {
        $id = (int)$paymentId;
        if ($id <= 0) {
            $_SESSION['error'] = 'Invalid payment ID.';
            header('Location: ' . URLROOT . '/admin/ad_payments');
            exit;
        }

        $payment = $this->adminPaymentModel->getPaymentById($id);
        if (!$payment) {
            $_SESSION['error'] = 'Payment not found.';
            header('Location: ' . URLROOT . '/admin/ad_payments');
            exit;
        }

        $this->view('admin/ad_payment_detail', [
            'payment' => $payment,
        ]);
    }

    private function getPaymentFilters(): array
    {
        return [
            'search' => trim((string)($_GET['search'] ?? '')),
            'status' => trim((string)($_GET['status'] ?? '')),
            'payment_type' => trim((string)($_GET['payment_type'] ?? '')),
            'payment_method' => trim((string)($_GET['payment_method'] ?? '')),
            'booking_status' => trim((string)($_GET['booking_status'] ?? '')),
            'from' => trim((string)($_GET['from'] ?? '')),
            'to' => trim((string)($_GET['to'] ?? '')),
        ];
    }

    private function exportPaymentReportCSV(array $filters): void
    {
        $rows = $this->adminPaymentModel->getAllPaymentsForExport($filters);

        $filename = 'payment_summary_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, ['SmartCare Admin Payment Summary']);
        fputcsv($output, ['Generated at', date('Y-m-d H:i:s')]);
        fputcsv($output, []);
        fputcsv($output, [
            'Payment ID',
            'Booking ID',
            'Client',
            'Caretaker',
            'Service',
            'Basis',
            'Payment Type',
            'Payment Method',
            'Amount',
            'Remaining Balance',
            'Payment Status',
            'Due Date',
            'Paid Date',
            'Created At',
            'Booking Status',
        ]);

        foreach ($rows as $row) {
            fputcsv($output, [
                $row['payment_id'],
                $row['booking_id'],
                $row['client_name'],
                $row['caretaker_name'],
                $row['service_type'],
                $row['basis'],
                $row['payment_type'],
                $row['payment_method'],
                number_format((float)$row['amount'], 2, '.', ''),
                number_format((float)$row['remaining_balance'], 2, '.', ''),
                $row['status'],
                $row['due_date'],
                $row['paid_date'],
                $row['created_at'],
                $row['booking_status'],
            ]);
        }

        fclose($output);
        exit;
    }

    private function exportPaymentReportPDF(array $filters): void
    {
        $rows = $this->adminPaymentModel->getAllPaymentsForExport($filters);
        $summary = $this->adminPaymentModel->getPaymentSummary($filters);

        header('Content-Type: text/html; charset=utf-8');

        echo '<!DOCTYPE html><html><head><meta charset="UTF-8">';
        echo '<title>Payment Summary Report</title>';
        echo '<style>';
        echo 'body{font-family:Arial,sans-serif;margin:24px;color:#111827;}';
        echo 'h1{margin:0 0 8px;font-size:24px;}';
        echo '.meta{margin:0 0 16px;color:#4b5563;}';
        echo '.summary{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:18px;}';
        echo '.card{border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;min-width:180px;background:#f8fafc;}';
        echo '.card b{display:block;color:#374151;font-size:12px;margin-bottom:4px;text-transform:uppercase;}';
        echo 'table{width:100%;border-collapse:collapse;font-size:12px;}';
        echo 'th,td{border:1px solid #d1d5db;padding:6px 8px;text-align:left;}';
        echo 'th{background:#0f172a;color:#fff;}';
        echo '@media print{button{display:none;}}';
        echo '</style></head><body>';
        echo '<button onclick="window.print()">Print / Save as PDF</button>';
        echo '<h1>SmartCare Admin Payment Summary</h1>';
        echo '<p class="meta">Generated at: ' . htmlspecialchars(date('Y-m-d H:i:s'), ENT_QUOTES, 'UTF-8') . '</p>';

        echo '<div class="summary">';
        echo '<div class="card"><b>Total Records</b>' . (int)$summary['total_records'] . '</div>';
        echo '<div class="card"><b>Unique Clients</b>' . (int)$summary['unique_clients'] . '</div>';
        echo '<div class="card"><b>Total Collected</b>LKR ' . number_format((float)$summary['total_collected'], 2) . '</div>';
        echo '<div class="card"><b>Pending</b>' . (int)$summary['pending_count'] . '</div>';
        echo '<div class="card"><b>Rejected</b>' . (int)$summary['rejected_count'] . '</div>';
        echo '<div class="card"><b>Outstanding</b>LKR ' . number_format((float)$summary['outstanding_balance'], 2) . '</div>';
        echo '</div>';

        echo '<table><thead><tr>';
        echo '<th>Payment ID</th><th>Booking ID</th><th>Client</th><th>Caretaker</th><th>Service</th><th>Type</th><th>Method</th><th>Amount</th><th>Balance</th><th>Status</th><th>Created</th>';
        echo '</tr></thead><tbody>';

        if (empty($rows)) {
            echo '<tr><td colspan="11">No payment records found for selected filters.</td></tr>';
        } else {
            foreach ($rows as $row) {
                echo '<tr>';
                echo '<td>' . (int)$row['payment_id'] . '</td>';
                echo '<td>' . (int)$row['booking_id'] . '</td>';
                echo '<td>' . htmlspecialchars($row['client_name'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row['caretaker_name'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row['service_type'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row['payment_type'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row['payment_method'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>LKR ' . number_format((float)$row['amount'], 2) . '</td>';
                echo '<td>LKR ' . number_format((float)$row['remaining_balance'], 2) . '</td>';
                echo '<td>' . htmlspecialchars($row['status'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row['created_at'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
                echo '</tr>';
            }
        }

        echo '</tbody></table>';
        echo '</body></html>';
        exit;
    }

    public function ad_profile_requests()
    {
        $status = $_GET['status'] ?? 'All';
        $allowed = ['All', 'Pending', 'Approved', 'Rejected'];
        if (!in_array($status, $allowed, true)) {
            $status = 'All';
        }

        $requests = $this->profileChangeRequestModel->getRequests($status);
        $this->view('admin/ad_profile_requests', [
            'requests' => $requests,
            'selectedStatus' => $status
        ]);
    }

    public function approve_profile_request()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/admin/ad_profile_requests');
            exit;
        }

        $requestId = (int)($_POST['request_id'] ?? 0);
        $adminNote = trim($_POST['admin_note'] ?? '');

        if ($requestId <= 0) {
            $_SESSION['error'] = 'Invalid request.';
            header('Location: ' . URLROOT . '/admin/ad_profile_requests');
            exit;
        }

        $request = $this->profileChangeRequestModel->getById($requestId);
        $ok = $this->profileChangeRequestModel->approveRequest($requestId, (int)AuthSession::profileId(), $adminNote);

        if ($ok && $request) {
            require_once APPROOT . '/models/NotificationModel.php';
            $notif = new NotificationModel();
            $msg = 'Your profile update request has been approved.';
            if ($adminNote !== '') {
                $msg .= "\nAdmin note: " . $adminNote;
            }
            $notif->addNotification((int)$request['caretaker_id'], 'caretaker', 'Profile Update Approved', $msg, URLROOT . '/caretaker/ct_settings');

            $this->historyModel->log([
                'user_id' => AuthSession::profileId(),
                'username' => $_SESSION['user']['username'],
                'role' => $_SESSION['user']['role'],
                'action' => "Approved caretaker profile update request (Request ID: {$requestId})",
                'section' => 'Profile Requests'
            ]);

            $_SESSION['success'] = 'Profile update request approved.';
        } else {
            $_SESSION['error'] = 'Failed to approve request.';
        }

        header('Location: ' . URLROOT . '/admin/ad_profile_requests');
        exit;
    }

    public function reject_profile_request()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/admin/ad_profile_requests');
            exit;
        }

        $requestId = (int)($_POST['request_id'] ?? 0);
        $adminNote = trim($_POST['admin_note'] ?? '');

        if ($requestId <= 0) {
            $_SESSION['error'] = 'Invalid request.';
            header('Location: ' . URLROOT . '/admin/ad_profile_requests');
            exit;
        }

        $request = $this->profileChangeRequestModel->getById($requestId);
        $ok = $this->profileChangeRequestModel->rejectRequest($requestId, (int)AuthSession::profileId(), $adminNote);

        if ($ok && $request) {
            require_once APPROOT . '/models/NotificationModel.php';
            $notif = new NotificationModel();
            $msg = 'Your profile update request has been rejected.';
            if ($adminNote !== '') {
                $msg .= "\nAdmin note: " . $adminNote;
            }
            $notif->addNotification((int)$request['caretaker_id'], 'caretaker', 'Profile Update Rejected', $msg, URLROOT . '/caretaker/ct_settings');

            $this->historyModel->log([
                'user_id' => AuthSession::profileId(),
                'username' => $_SESSION['user']['username'],
                'role' => $_SESSION['user']['role'],
                'action' => "Rejected caretaker profile update request (Request ID: {$requestId})",
                'section' => 'Profile Requests'
            ]);

            $_SESSION['success'] = 'Profile update request rejected.';
        } else {
            $_SESSION['error'] = 'Failed to reject request.';
        }

        header('Location: ' . URLROOT . '/admin/ad_profile_requests');
        exit;
    }

    public function ad_bookings()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $clientModel = $this->model('ClientModel'); // or new ClientModel();

        $bookings = $clientModel->getBookingsPaginated($limit, $offset);
        $totalBookings = $clientModel->getTotalBookings();

        $totalPages = (int) ceil($totalBookings / $limit);
        if ($totalPages < 1) $totalPages = 1;

        $data = [
            'bookings'     => $bookings,
            'currentPage'  => $page,
            'totalPages'   => $totalPages,
            'totalRecords' => $totalBookings
        ];

        $this->view('admin/ad_bookings', $data);
    }
}
