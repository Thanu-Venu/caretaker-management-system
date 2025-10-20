<?php
class LeaveCRUDController extends Controller {

    private $leaveModel;

    public function __construct() {
        $this->leaveModel = $this->model('LeaveModel');
    }

    // 🔹 Display all leaves for logged-in caretaker
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
     

        if (!isset($_SESSION['caretaker_id'])) {
            header("Location: " . URLROOT . "/login");
            exit;
        }
        

        $caretakerId = $_SESSION['caretaker_id'];
        $leaves = $this->leaveModel->getLeavesByCaretaker($caretakerId);
        $this->view('caretaker/ct_leave', ['leaves' => $leaves]);
    
    }
    // 🔹 Add new leave
    public function add() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
               
                'leave_type'   => $_POST['leave_type'],
                'start_date'   => $_POST['start_date'],
                'end_date'     => $_POST['end_date'],
                'start_time'   => $_POST['start_time'],
                'end_time'     => $_POST['end_time'],
                'reason'       => $_POST['reason']
            ];

            $this->leaveModel->addLeave($data);
            header("Location: " . URLROOT . "/leaveCRUD/index");
            exit;
        } else {
            $this->view('caretaker/leave_add');
        }
    }

    // 🔹 Edit leave
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
              
                'id' =>$id,
                'leave_type'   => $_POST['leave_type'],
                'start_date'   => $_POST['start_date'],
                'end_date'     => $_POST['end_date'],
                'start_time'   => $_POST['start_time'],
                'end_time'     => $_POST['end_time'],
                'reason'       => $_POST['reason']
            ];
            $this->leaveModel->updateLeave($data);
            header("Location: " . URLROOT . "/leaveCRUD/index");
            exit;
        } else {
            $leave = $this->leaveModel->getLeaveById($id);
            $this->view('caretaker/leave_edit', ['leave' => $leave]);
        }
    }

    // 🔹 Delete leave
    public function delete($id) {
        $this->leaveModel->deleteLeave($id);
        header("Location: " . URLROOT . "/leaveCRUD/index");
        exit;
    }
}
?>
