<?php

class LeaveCRUDController extends Controller
{


    private $leaveModel;
    private $notifModel;

    public function __construct()
    {
        $this->leaveModel = $this->model('LeaveModel');
        $this->notifModel = $this->model('NotificationModel');
    }

    private function requireCaretaker(): int
    {
        if (!AuthSession::hasRole('caretaker')) {
            die("Caretaker not logged in");
        }

        return (int)AuthSession::profileId();
    }

    private function baseAddViewData(array $overrides = []): array
    {
        $userId = (int)AuthSession::profileId();
        $summary = $this->leaveModel->getCurrentMonthLeaveSummary($userId, true);

        $defaults = [
            'today' => date('Y-m-d'),
            'minStartDate' => date('Y-m-d', strtotime('+' . LeaveModel::ADVANCE_NOTICE_DAYS . ' days')),
            'monthlySummary' => $summary,
            'policy' => [
                'advanceNoticeDays' => LeaveModel::ADVANCE_NOTICE_DAYS,
                'maxPerRequest' => LeaveModel::MAX_DAYS_PER_REQUEST,
                'monthlyLimit' => LeaveModel::MONTHLY_LEAVE_LIMIT
            ],
            'impactPreviewUrl' => URLROOT . '/LeaveCRUD/impactPreview',
            'errors' => [],
            'warnings' => [],
            'form' => [
                'leave_type' => '',
                'start_date' => '',
                'end_date' => '',
                'start_time' => '09:00',
                'end_time' => '17:00',
                'reason' => ''
            ]
        ];

        return array_replace_recursive($defaults, $overrides);
    }

    /** User-facing note when leave dates overlap assigned bookings */
    private function bookingOverlapClientMessage(int $count): string
    {
        if ($count <= 0) {
            return '';
        }
        if ($count === 1) {
            return 'You have 1 scheduled booking in this leave period. HR may arrange a replacement caregiver if your leave is approved.';
        }

        return "You have {$count} scheduled bookings in this leave period. HR may arrange replacement cover if your leave is approved.";
    }

    private function monthSequenceBetween(string $startDate, string $endDate): array
    {
        $months = [];
        $cursor = new DateTime(date('Y-m-01', strtotime($startDate)));
        $last = new DateTime(date('Y-m-01', strtotime($endDate)));

        while ($cursor <= $last) {
            $months[] = [
                'year' => (int)$cursor->format('Y'),
                'month' => (int)$cursor->format('m')
            ];
            $cursor->modify('+1 month');
        }

        return $months;
    }

    private function validateLeaveInput(int $userId, array $input, ?int $excludeLeaveId = null): array
    {
        $errors = [];
        $warnings = [];

        $leaveType = $input['leave_type'] ?? '';
        $startDate = $input['start_date'] ?? '';
        $endDate = $input['end_date'] ?? '';

        if ($startDate === '' || $endDate === '') {
            $errors[] = 'Start date and end date are required.';
            return ['errors' => $errors, 'warnings' => $warnings];
        }

        $today = date('Y-m-d');

        if ($startDate < $today || $endDate < $today) {
            $errors[] = 'Leave cannot be requested for past dates.';
        }

        if ($leaveType === 'Sick Leave') {
            // Sick leave allows immediate start (no advance notice needed)
            $duration = $this->leaveModel->calculateInclusiveDays($startDate, $endDate);
            if ($duration > LeaveModel::MAX_DAYS_PER_REQUEST) {
                $errors[] = 'Sick leave cannot exceed ' . LeaveModel::MAX_DAYS_PER_REQUEST . ' days.';
            }
        } else {
            // Other leaves require advance notice
            $minimumStart = date('Y-m-d', strtotime('+' . LeaveModel::ADVANCE_NOTICE_DAYS . ' days'));
            if ($startDate < $minimumStart) {
                $errors[] = 'Leave must be requested at least 3 days in advance.';
            }

            $duration = $this->leaveModel->calculateInclusiveDays($startDate, $endDate);
            if ($duration > LeaveModel::MAX_DAYS_PER_REQUEST) {
                $errors[] = 'A single leave request cannot exceed ' . LeaveModel::MAX_DAYS_PER_REQUEST . ' days.';
            }
        }

        if ($this->leaveModel->hasOverlappingLeave($userId, $startDate, $endDate, ['Approved', 'Pending'], $excludeLeaveId)) {
            $errors[] = 'This leave request overlaps with an existing approved or pending leave.';
        }

        foreach ($this->monthSequenceBetween($startDate, $endDate) as $monthInfo) {
            $requestDaysInMonth = $this->leaveModel->getLeaveDaysWithinMonth(
                $startDate,
                $endDate,
                $monthInfo['year'],
                $monthInfo['month']
            );

            if ($requestDaysInMonth <= 0) {
                continue;
            }

            $usedInMonth = $this->leaveModel->getMonthlyLeaveUsage(
                $userId,
                $monthInfo['year'],
                $monthInfo['month'],
                true,
                $excludeLeaveId
            );

            if (($usedInMonth + $requestDaysInMonth) > LeaveModel::MONTHLY_LEAVE_LIMIT) {
                $errors[] = 'Leave request exceeds the monthly leave limit of 5 days.';
                break;
            }
        }

        $impact = $this->leaveModel->getActiveBookingImpactSummary($userId, $startDate, $endDate);
        if (($impact['count'] ?? 0) > 0) {
            $warnings[] = $this->bookingOverlapClientMessage((int) $impact['count']);
        }

        return [
            'errors' => array_values(array_unique($errors)),
            'warnings' => $warnings,
            'impact' => $impact
        ];
    }

    private function computeMaxRequestableEndDate(int $userId, string $startDate, ?int $excludeLeaveId = null): ?string
    {
        if ($startDate === '') {
            return null;
        }

        $today = date('Y-m-d');
        if ($startDate < $today) {
            return null;
        }

        $maxDays = LeaveModel::MAX_DAYS_PER_REQUEST;
        $bestEnd = null;

        for ($offset = 0; $offset < $maxDays; $offset++) {
            $endDate = date('Y-m-d', strtotime($startDate . ' +' . $offset . ' day'));
            $isValid = true;

            foreach ($this->monthSequenceBetween($startDate, $endDate) as $monthInfo) {
                $requestDaysInMonth = $this->leaveModel->getLeaveDaysWithinMonth(
                    $startDate,
                    $endDate,
                    $monthInfo['year'],
                    $monthInfo['month']
                );

                $usedInMonth = $this->leaveModel->getMonthlyLeaveUsage(
                    $userId,
                    $monthInfo['year'],
                    $monthInfo['month'],
                    true,
                    $excludeLeaveId
                );

                if (($usedInMonth + $requestDaysInMonth) > LeaveModel::MONTHLY_LEAVE_LIMIT) {
                    $isValid = false;
                    break;
                }
            }

            if (!$isValid) {
                break;
            }

            $bestEnd = $endDate;
        }

        return $bestEnd;
    }

    public function impactPreview()
    {
        $userId = $this->requireCaretaker();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
            return;
        }

        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');
        $excludeLeaveId = isset($_POST['leave_id']) ? (int) $_POST['leave_id'] : null;

        if ($startDate === '') {
            echo json_encode([
                'ok' => true,
                'hasImpact' => false,
                'count' => 0,
                'booking_ids' => [],
                'service_dates' => [],
                'range_is_provisional' => false,
                'max_end_date' => null,
                'start_month_used' => 0,
                'start_month_remaining' => LeaveModel::MONTHLY_LEAVE_LIMIT,
                'can_request' => false
            ]);
            return;
        }

        $startYear = (int) date('Y', strtotime($startDate));
        $startMonth = (int) date('m', strtotime($startDate));
        $usedInStartMonth = $this->leaveModel->getMonthlyLeaveUsage(
            $userId,
            $startYear,
            $startMonth,
            true,
            $excludeLeaveId
        );
        $remainingInStartMonth = max(0, LeaveModel::MONTHLY_LEAVE_LIMIT - $usedInStartMonth);
        $maxEndDate = $this->computeMaxRequestableEndDate($userId, $startDate, $excludeLeaveId);

        // Until an end date is chosen, check overlap for the start day only so the caregiver sees an early note.
        if ($endDate !== '' && $endDate < $startDate) {
            $impact = ['count' => 0, 'booking_ids' => [], 'service_dates' => []];
            $rangeIsProvisional = false;
            $count = 0;
            $message = '';
        } else {
            $rangeEnd = ($endDate !== '' && $endDate >= $startDate) ? $endDate : $startDate;
            $rangeIsProvisional = ($endDate === '');
            $impact = $this->leaveModel->getActiveBookingImpactSummary($userId, $startDate, $rangeEnd);
            $count = (int) ($impact['count'] ?? 0);
            $message = $count > 0 ? $this->bookingOverlapClientMessage($count) : '';
            if ($rangeIsProvisional && $count > 0) {
                $message .= ' Add your end date to see overlap across the full leave range.';
            }
        }

        echo json_encode([
            'ok' => true,
            'hasImpact' => ($count > 0),
            'count' => $count,
            'booking_ids' => $impact['booking_ids'] ?? [],
            'service_dates' => $impact['service_dates'] ?? [],
            'range_is_provisional' => $rangeIsProvisional,
            'message' => $message,
            'max_end_date' => $maxEndDate,
            'start_month_used' => $usedInStartMonth,
            'start_month_remaining' => $remainingInStartMonth,
            'can_request' => ($maxEndDate !== null)
        ]);
    }

    // 🔹 Display all leaves for logged-in caretaker
    public function index()
    {
        $userId = $this->requireCaretaker();
        $leaves = $this->leaveModel->getLeavesByUser($userId);
        $this->view('caretaker/ct_leave', [
            'leaves' => $leaves,
            'monthlySummary' => $this->leaveModel->getCurrentMonthLeaveSummary($userId, true),
            'success' => $_SESSION['leave_success'] ?? '',
            'warning' => $_SESSION['leave_warning'] ?? ''
        ]);

        unset($_SESSION['leave_success'], $_SESSION['leave_warning']);
    }

    // 🔹 Add new leave
    public function add()
    {
        $userId = $this->requireCaretaker();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = [
                'leave_type' => trim($_POST['leave_type'] ?? ''),
                'start_date' => trim($_POST['start_date'] ?? ''),
                'end_date' => trim($_POST['end_date'] ?? ''),
                'start_time' => trim($_POST['start_time'] ?? '09:00'),
                'end_time' => trim($_POST['end_time'] ?? '17:00'),
                'reason' => trim($_POST['reason'] ?? '')
            ];

            $validation = $this->validateLeaveInput($userId, $input);

            if ($input['leave_type'] === '' || $input['reason'] === '') {
                $validation['errors'][] = 'Leave type and reason are required.';
            }

            if (!empty($validation['errors'])) {
                $viewData = $this->baseAddViewData([
                    'errors' => array_values(array_unique($validation['errors'])),
                    'warnings' => $validation['warnings'] ?? [],
                    'form' => $input,
                    'impact' => $validation['impact'] ?? []
                ]);
                $this->view('caretaker/leave_add', $viewData);
                return;
            }

            $insertData = [
                'user_id' => $userId,
                'leave_type' => $input['leave_type'],
                'start_date' => $input['start_date'],
                'end_date' => $input['end_date'],
                'start_time' => $input['start_time'],
                'end_time' => $input['end_time'],
                'reason' => $input['reason'],
                'can_edit_until' => date('Y-m-d H:i:s', strtotime('+1 day'))
            ];

            $created = $this->leaveModel->addLeave($insertData);
            if (!$created) {
                $viewData = $this->baseAddViewData([
                    'errors' => ['Unable to submit leave request right now. Please try again.'],
                    'form' => $input
                ]);
                $this->view('caretaker/leave_add', $viewData);
                return;
            }

            $_SESSION['leave_success'] = 'Leave request submitted successfully and sent to HR for approval.';

            if (($validation['impact']['count'] ?? 0) > 0) {
                $n = (int) $validation['impact']['count'];
                $idParts = array_map(static function ($id) {
                    return '#' . (int) $id;
                }, $validation['impact']['booking_ids'] ?? []);
                $_SESSION['leave_warning'] = $this->bookingOverlapClientMessage($n)
                    . (!empty($idParts) ? ' Ref: ' . implode(', ', $idParts) : '');
            }

            header("Location: " . URLROOT . "/LeaveCRUD/index");
            exit;
        }

        $this->view('caretaker/leave_add', $this->baseAddViewData());
    }


    // 🔹 Edit leave
    public function edit($id)
    {
        $userId = $this->requireCaretaker();
        $leave = $this->leaveModel->getLeaveById($id);
        if (!$leave) die("Leave not found");

        if ($leave->user_id != $userId || strtotime($leave->can_edit_until) < time()) {
            die("You cannot edit this leave.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = [
                'leave_type' => trim($_POST['leave_type'] ?? ''),
                'start_date' => trim($_POST['start_date'] ?? ''),
                'end_date' => trim($_POST['end_date'] ?? ''),
                'start_time' => trim($_POST['start_time'] ?? '09:00'),
                'end_time' => trim($_POST['end_time'] ?? '17:00'),
                'reason' => trim($_POST['reason'] ?? '')
            ];

            $validation = $this->validateLeaveInput($userId, $input, (int)$id);
            if ($input['leave_type'] === '' || $input['reason'] === '') {
                $validation['errors'][] = 'Leave type and reason are required.';
            }

            if (!empty($validation['errors'])) {
                // Update local leaf object with new input for preview (to keep state in form)
                $leave->leave_type = $input['leave_type'];
                $leave->start_date = $input['start_date'];
                $leave->end_date = $input['end_date'];
                $leave->start_time = $input['start_time'];
                $leave->end_time = $input['end_time'];
                $leave->reason = $input['reason'];

                $viewData = $this->baseAddViewData([
                    'leave' => $leave,
                    'errors' => array_values(array_unique($validation['errors'])),
                    'warnings' => $validation['warnings'] ?? [],
                    'impact' => $validation['impact'] ?? []
                ]);
                $this->view('caretaker/leave_edit', $viewData);
                return;
            }

            $data = [
                'id' => $id,
                'leave_type' => $input['leave_type'],
                'start_date' => $input['start_date'],
                'end_date' => $input['end_date'],
                'start_time' => $input['start_time'],
                'end_time' => $input['end_time'],
                'reason' => $input['reason']
            ];

            $this->leaveModel->updateLeave($data);
            header("Location: " . URLROOT . "/LeaveCRUD/index");
            exit;
        } else {
            $this->view('caretaker/leave_edit', $this->baseAddViewData(['leave' => $leave]));
        }
    }

    // 🔹 Delete leave
    public function delete($id)
    {
        $userId = $this->requireCaretaker();
        $leave = $this->leaveModel->getLeaveById($id);
        if (!$leave) die("Leave not found");

        if ($leave->user_id != $userId || strtotime($leave->can_edit_until) < time()) {
            die("You cannot delete this leave.");
        }

        if (strtolower((string)$leave->status) !== 'pending') {
            die('Only pending leaves can be cancelled.');
        }

        $this->leaveModel->updateLeaveStatus((int)$id, 'Cancelled');
        header("Location: " . URLROOT . "/LeaveCRUD/index");
        exit;
    }

    public function ct_dashboard()
    {

        if (!AuthSession::hasRole('caretaker')) {
            die("Caretaker not logged in");
        }

        $userId = AuthSession::profileId();

        // LOAD LEAVES
        $leaveModel = $this->model('LeaveModel');
        $leaves = $leaveModel->getLeavesByUser($userId);

        $this->view('caretaker/ct_dashboard', [
            'leaves' => $leaves
        ]);
    }
}
