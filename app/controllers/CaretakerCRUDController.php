<?php

class CaretakerCRUDController extends Controller {

    private $caretakerModel;

    public function __construct() {
        $this->caretakerModel = $this->model('CaretakerModel');
    }

    // Add caretaker
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->caretakerModel->addCaretaker($_POST);
            header("Location: " . URLROOT . "/admin/ad_caretakers");
            exit;
        } else {
            $this->view("admin/caretaker_add");
        }
    }

    // Edit caretaker
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->caretakerModel->updateCaretaker($id, $_POST);
            header("Location: " . URLROOT . "/admin/ad_caretakers");
            exit;
        } else {
            $caretaker = $this->caretakerModel->getCaretakerById($id);
            $this->view("admin/caretaker_edit", ['caretaker' => $caretaker]);
        }
    }

    // Delete caretaker
    public function delete($id) {
        $this->caretakerModel->deleteCaretaker($id);
        header("Location: " . URLROOT . "/admin/ad_caretakers");
        exit;
    }

    // List all caretakers
    public function list() {
        $caretakers = $this->caretakerModel->getCaretakers();
        $this->view("admin/ad_caretakers", ['caretakers' => $caretakers]);
    }
}
?>
