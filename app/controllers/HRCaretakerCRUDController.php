<?php
session_start();
class HRCaretakerCRUDController extends Controller {

    private $caretakerModel;

    public function __construct() {
        $this->caretakerModel = $this->model('CaretakerModel');
    }

    // Add caretaker
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->caretakerModel->addCaretaker($_POST);
            header("Location: " . URLROOT . "/hr/hr_addct");
            exit;
        } else {
            $this->view("hr/caretaker_add");
        }
    }

    // Edit caretaker
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->caretakerModel->updateCaretaker($id, $_POST);
            header("Location: " . URLROOT . "/hr/hr_addct");
            exit;
        } else {
            $caretaker = $this->caretakerModel->getCaretakerById($id);
            $this->view("hr/caretaker_edit", ['caretaker' => $caretaker]);
        }
    }

    // Delete caretaker
    public function delete($id) {
        $this->caretakerModel->deleteCaretaker($id);
        header("Location: " . URLROOT . "/hr/hr_addct");
        exit;
    }

    // List all caretakers
    public function list() {
        $caretakers = $this->caretakerModel->getCaretakers();
        $this->view("hr/hr_addct", ['caretakers' => $caretakers]);
    }
}
?>
