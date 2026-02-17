<?php

class LeaveCRUDController extends Controller {


    private $leaveModel;
    private $notifModel;

    public function __construct() {
        $this->leaveModel = $this->model('LeaveModel');
        $this->notifModel = $this->model('NotificationModel');
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

     // 🔹 Add new leave
    public function add() {
    if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'caretaker') {
        die("Caretaker not logged in");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $data = [
            'user_id' => $_SESSION['user']['id'],
            'leave_type' => $_POST['leave_type'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'start_time' => $_POST['start_time'],
            'end_time' => $_POST['end_time'],
            'reason' => $_POST['reason'],
            'can_edit_until' => date('Y-m-d H:i:s', strtotime('+1 day'))
        ];

        // 1️⃣ Insert leave
        $this->leaveModel->addLeave($data);

        // 2️⃣ Notify ALL admins (THIS IS THE CORRECT PLACE)
        $this->notifModel->notifyAdmins(
            "New Leave Request",
            "New leave request submitted by caretaker " . ($_SESSION['user']['name'] ?? 'Caretaker'),
            URLROOT . "/admin/ad_leave"
        );

        // 3️⃣ Redirect caretaker
        header("Location: " . URLROOT . "/LeaveCRUD/index");
        exit;
    }

    $this->view('caretaker/leave_add');
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