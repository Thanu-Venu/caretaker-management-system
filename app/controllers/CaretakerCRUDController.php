<?php
class CaretakerCRUDController extends Controller {

    private $caretakerModel;

    public function __construct() {
        $this->caretakerModel = $this->model('CaretakerModel');
    }

    // Add caretaker
    public function add() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $result = $this->caretakerModel->addCaretaker($_POST);

            // Redirect to admin caretakers page
            header("Location: " . URLROOT . "/admin/ad_caretakers");
            exit;
        } else {
            $this->view("admin/caretaker_add");
        }
    }

    // Edit caretaker
   public function edit($id) {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Update caretaker in DB
        $this->caretakerModel->updateCaretaker($id, $_POST);
        header("Location: " . URLROOT . "/admin/ad_caretakers");
        exit;
    } else {
        // Load caretaker data and open edit form
        $caretaker = $this->caretakerModel->getCaretakerById($id);
        $this->view("admin/caretaker_edit", ['caretaker' => $caretaker]);
    }
}


    public function delete($id) {
    $this->caretakerModel->deleteCaretaker($id);
    header("Location: " . URLROOT . "?url=admin/ad_caretakers");
    exit;
}
}
?>
