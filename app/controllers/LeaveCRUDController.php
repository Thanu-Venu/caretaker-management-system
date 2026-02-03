<?php

class LeaveCRUDController extends Controller {


    private $leaveModel;

    public function __construct() {
        $this->leaveModel = $this->model('LeaveModel');
    }

    // 🔹 Display all leaves for logged-in caretaker
    public function index() {
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'caretaker') {
            die("Caretaker not logged in");
        }

        $userId = $_SESSION['user']['id'];
        $leaves = $this->leaveModel->getLeavesByUser($userId);
        $this->view('caretaker/ct_leave', ['leaves' => $leaves]);
    }

  public function add() {
    if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'caretaker') {
        die("Caretaker not logged in");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $startDate = $_POST['start_date'];
        $endDate   = $_POST['end_date'];

        $today = new DateTime('today');
        $start = new DateTime($startDate);
        $end   = new DateTime($endDate);

        // 🔹 Rule 1: Start date must be at least 5 days after today
        $minStartDate = (clone $today)->modify('+1 days');

        if ($start < $minStartDate) {
            die("Start date must be at least 1 days after today");
        }

        // 🔹 Rule 2: End date must not be before start date
        if ($end < $start) {
            die("End date cannot be before start date");
        }

        // 🔹 Rule 3: Max leave = 28 days (inclusive)
        $maxEndDate = (clone $start)->modify('+27 days'); // 28 days total

        if ($end > $maxEndDate) {
            die("Leave cannot exceed 28 days from the start date");
        }

        // ✅ Save
        $data = [
            'user_id' => $_SESSION['user']['id'],
            'leave_type' => $_POST['leave_type'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => $_POST['start_time'],
            'end_time' => $_POST['end_time'],
            'reason' => $_POST['reason'],
            'can_edit_until' => date('Y-m-d H:i:s', strtotime('+1 day'))
        ];

        $this->leaveModel->addLeave($data);
        header("Location: " . URLROOT . "/LeaveCRUD/index");
        exit;
    } 
    else {
        // Pass min start date to view
        $minStartDate = (new DateTime('today'))->modify('+1 days')->format('Y-m-d');
        $this->view('caretaker/leave_add', ['minStartDate' => $minStartDate]);
    }
}




    // 🔹 Edit leave
    public function edit($id) {
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'caretaker') {
            die("Caretaker not logged in");
        }

        $userId = $_SESSION['user']['id'];
        $leave = $this->leaveModel->getLeaveById($id);
        if (!$leave) die("Leave not found");

        if ($leave->user_id != $userId || strtotime($leave->can_edit_until) < time()) {
            die("You cannot edit this leave.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id' => $id,
                'leave_type' => $_POST['leave_type'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'start_time' => $_POST['start_time'],
                'end_time' => $_POST['end_time'],
                'reason' => $_POST['reason']
            ];

            $this->leaveModel->updateLeave($data);
            header("Location: " . URLROOT . "/LeaveCRUD/index");
            exit;
        } else {
            $this->view('caretaker/leave_edit', ['leave' => $leave]);
        }
    }

    // 🔹 Delete leave
    public function delete($id) {
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'caretaker') {
            die("Caretaker not logged in");
        }

        $userId = $_SESSION['user']['id'];
        $leave = $this->leaveModel->getLeaveById($id);
        if (!$leave) die("Leave not found");

        if ($leave->user_id != $userId || strtotime($leave->can_edit_until) < time()) {
            die("You cannot delete this leave.");
        }

        $this->leaveModel->deleteLeave($id);
        header("Location: " . URLROOT . "/LeaveCRUD/index");
        exit;
    }

    public function ct_dashboard() {

    if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'caretaker') {
        die("Caretaker not logged in");
    }

    $userId = $_SESSION['user']['id'];

    // LOAD LEAVES
    $leaveModel = $this->model('LeaveModel');
    $leaves = $leaveModel->getLeavesByUser($userId);

    $this->view('caretaker/ct_dashboard', [
        'leaves' => $leaves
    ]);
}

}