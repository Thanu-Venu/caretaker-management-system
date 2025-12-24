<?php
class LeaveApproval extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->leaveModel = $this->model('Leave');
    }

    public function index() {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['hr','admin'])) {
            die('Forbidden');
        }

        $status = $_GET['status'] ?? '';

        $leaves = empty($status)
            ? $this->leaveModel->getAllLeaves()
            : $this->leaveModel->getLeavesByStatus($status);

        $this->view('hr/hr_leave', ['leaves' => $leaves]);
    }

    public function approve($id) {
        if ($_SESSION['role'] !== 'hr') die('Forbidden');
        $this->leaveModel->updateLeaveStatus($id, 'Approved');
        header("Location: " . URLROOT . "/LeaveApproval/index");
        exit;
    }

    public function reject($id) {
        if ($_SESSION['role'] !== 'hr') die('Forbidden');
        $this->leaveModel->updateLeaveStatus($id, 'Rejected');
        header("Location: " . URLROOT . "/LeaveApproval/index");
        exit;
    }
}
