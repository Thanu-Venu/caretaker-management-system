<?php

class HrLeaveController extends Controller {

    private $leaveModel;

    public function __construct() {
        $this->leaveModel = $this->model('LeaveModel');
    }

    // ✅ Central role check (Manager)
    private function requireManager() {
        if (!isset($_SESSION['user'])) {
            die("Not logged in");
        }

        // Your system uses 'Manager'
        $role = $_SESSION['role'] ?? ($_SESSION['user']['role'] ?? '');
        $role = trim($role);

        if ($role !== 'Manager') {
            die("HR not logged in");
        }
    }

    // HR leave list
    public function index() {
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
    public function approve_form($leaveId) {
    $this->requireManager(); // or your Manager check

    $leaveId = (int)$leaveId;
    $leave = $this->leaveModel->getLeaveById($leaveId);
    if (!$leave) die("Leave not found");

    $result = $this->leaveModel->getEligibleReplacementCaretakers($leaveId);

    $this->view('hr/hr_leave_approve', [
        'leave' => $leave,
        'affected' => $result['affected'] ?? [],
        'caretakers' => $result['caretakers'] ?? [],
        'error' => ($result['ok'] ? '' : ($result['message'] ?? '')),
        'criteria' => $result['criteria'] ?? null
    ]);
}

    // POST: approve + reassign
    public function approve_submit() {
        $this->requireManager();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/HrLeave/index");
            exit;
        }

        $leaveId = (int)($_POST['leave_id'] ?? 0);
        $replacementId = (int)($_POST['replacement_caretaker_id'] ?? 0); // can be 0
        $hrNote = trim($_POST['hr_note'] ?? '');

        // HR ID (Manager ID) - store who approved
        $hrId = (int)($_SESSION['user']['id'] ?? 0);

        if (!$leaveId || !$hrId) {
            die("Leave ID missing or session missing");
        }

        // ✅ Model decides if replacement is required (based on affected bookings)
        $res = $this->leaveModel->approveLeaveWithReassign($leaveId, $replacementId, $hrId, $hrNote);

        if (!$res['ok']) {
            die("Error: " . htmlspecialchars($res['message']));
        }

        header("Location: " . URLROOT . "/HrLeave/index");
        exit;
    }

    // Reject
    public function reject($leaveId) {
        $this->requireManager();

        $this->leaveModel->updateLeaveStatus((int)$leaveId, 'Rejected');
        header("Location: " . URLROOT . "/HrLeave/index");
        exit;
    }
}
