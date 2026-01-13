<?php

class CaretakerCRUDController extends Controller {

    private $caretakerModel;

    public function __construct() {
        $this->caretakerModel = $this->model('CaretakerModel');
    }

    // Add caretaker
    public function add()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $data = $_POST;

        // default image
        $data['profile_image'] = 'default.png';

        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {

            $uploadDir = APPROOT . '/../public/uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . '_' . basename($_FILES['profile_image']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
                $data['profile_image'] = $fileName;
            }
        }

        $this->caretakerModel->addCaretaker($data);

        header("Location: " . URLROOT . "/admin/ad_caretakers");
        exit;
    }

    $this->view("admin/caretaker_add");
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
