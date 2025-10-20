<?php
class LeaveCRUDController extends Controller {
    private $leaveModel;

    public function __construct() {
        $this->leaveModel = $this->model('LeaveModel');
    }

    // Apply leave
    public function apply() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'caretaker_id' => $_POST['caretaker_id'],
                'leave_type' => $_POST['leave_type'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'start_time' => $_POST['start_time'],
                'end_time' => $_POST['end_time'],
                'reason' => $_POST['reason']
            ];
            $this->leaveModel->addLeave($data);
            header("Location: " . URLROOT . "/caretaker/leave");
            exit;
        }
    }

    // View caretaker leaves
    public function myLeaves($caretaker_id) {
        $leaves = $this->leaveModel->getLeavesByCaretaker($caretaker_id);
        $this->view("caretaker/leave_list", ['leaves' => $leaves]);
    }

    // Admin update status
    public function updateStatus($leave_id, $status) {
        $this->leaveModel->updateLeaveStatus($leave_id, $status);
        header("Location: " . URLROOT . "/admin/leaves");
        exit;
    }

    // Delete leave
    public function delete($leave_id) {
        $this->leaveModel->deleteLeave($leave_id);
        header("Location: " . URLROOT . "/caretaker/leave");
        exit;
    }
}
?>
