<?php

class HrLeaveController extends Controller
{

    private $leaveModel;
    private $hrLogsModel;
    private $notifModel;

    public function __construct()
    {
        $this->leaveModel = $this->model('LeaveModel');
        $this->hrLogsModel = $this->model('HRLogsModel');
        $this->notifModel = $this->model('NotificationModel');
    }

    private function logManagerAction($action, $section = 'Leaves')
    {
        $userId = (int)AuthSession::profileId();
        if ($userId <= 0) {
            return;
        }

        $username = $_SESSION['user']['username'] ?? ($_SESSION['user']['name'] ?? 'unknown');

        $this->hrLogsModel->log([
            'user_id' => $userId,
            'username' => $username,
            'role' => 'Manager',
            'action' => $action,
            'section' => $section
        ]);
    }

    // ✅ Central role check (Manager)
    private function requireManager()
    {
        if (!AuthSession::isLoggedIn()) {
            die("Not logged in");
        }

        $role = AuthSession::role();
        if (AuthSession::normalizeRole($role) !== 'manager') {
            die("HR not logged in");
        }
    }

    // HR leave list
    public function index()
    {
        $this->requireManager();

        $perPage = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        $total = $this->leaveModel->countAllLeaves();
        $totalPages = max(1, (int)ceil($total / $perPage));

        if ($page > $totalPages) $page = $totalPages;

        $offset = ($page - 1) * $perPage;
        $rows = $this->leaveModel->getLeavesPage($perPage, $offset);

        $this->view('hr/hr_leave', [
            'leaves' => $rows,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => $totalPages
        ]);
    }



    // Show approve screen (choose replacement + see affected bookings)
    public function approve_form($leaveId)
    {
        $this->requireManager(); // or your Manager check

        $leaveId = (int)$leaveId;
        $leave = $this->leaveModel->getLeaveById($leaveId);
        if (!$leave) die("Leave not found");

        $leaveDetails = $this->leaveModel->getLeaveByIdWithCaretaker($leaveId);
        $impact = $this->leaveModel->getActiveBookingImpactSummary((int)$leave->user_id, $leave->start_date, $leave->end_date);

        $year = (int)date('Y', strtotime($leave->start_date));
        $month = (int)date('m', strtotime($leave->start_date));
        $requestDays = $this->leaveModel->getLeaveDaysWithinMonth($leave->start_date, $leave->end_date, $year, $month);
        $monthlyUsed = $this->leaveModel->getMonthlyLeaveUsage((int)$leave->user_id, $year, $month, true, (int)$leave->id);

        $result = $this->leaveModel->getEligibleReplacementCaretakers($leaveId);

        $this->view('hr/hr_leave_approve', [
            'leave' => $leave,
            'leaveDetails' => $leaveDetails,
            'impact' => $impact,
            'affected' => $result['affected'] ?? [],
            'caretakers' => $result['caretakers'] ?? [],
            'error' => ($result['ok'] ? '' : ($result['message'] ?? '')),
            'criteria' => $result['criteria'] ?? null,
            'monthlyUsage' => [
                'used_before' => $monthlyUsed,
                'request_days' => $requestDays,
                'used_after' => $monthlyUsed + $requestDays,
                'limit' => LeaveModel::MONTHLY_LEAVE_LIMIT
            ]
        ]);
    }

    // POST: approve + reassign
    public function approve_submit()
    {
        $this->requireManager();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/HrLeave/index");
            exit;
        }

        $leaveId = (int)($_POST['leave_id'] ?? 0);
        $replacementId = (int)($_POST['replacement_caretaker_id'] ?? 0); // can be 0
        $hrNote = trim($_POST['hr_note'] ?? '');

        // HR ID (Manager ID) - store who approved
        $hrId = (int)AuthSession::profileId();

        if (!$leaveId || !$hrId) {
            die("Leave ID missing or session missing");
        }

        // ✅ Model decides if replacement is required (based on affected bookings)
        $res = $this->leaveModel->approveLeaveWithReassign($leaveId, $replacementId, $hrId, $hrNote);

        if (!$res['ok']) {
            die("Error: " . htmlspecialchars($res['message']));
        }

        $this->logManagerAction("Approved leave request (Leave ID: {$leaveId})", 'Leaves');

        header("Location: " . URLROOT . "/HrLeave/index");
        exit;
    }

    // Reject
    public function reject($leaveId)
    {
        $this->requireManager();
        $leave = $this->leaveModel->getLeaveById((int)$leaveId);

        $this->leaveModel->updateLeaveStatus((int)$leaveId, 'Rejected');

        if ($leave) {
            $this->notifModel->addNotification(
                (int)$leave->user_id,
                'caretaker',
                'Leave Request Rejected',
                'Your leave request for ' . $leave->start_date . ' to ' . $leave->end_date . ' was rejected by HR.',
                URLROOT . '/LeaveCRUD/index'
            );
        }

        $this->logManagerAction("Rejected leave request (Leave ID: " . (int)$leaveId . ")", 'Leaves');
        header("Location: " . URLROOT . "/HrLeave/index");
        exit;
    }
}
