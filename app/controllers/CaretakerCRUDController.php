<?php

class CaretakerCRUDController extends Controller
{

    private $caretakerModel;
    private $historyModel;
    public function __construct()
    {
        $this->caretakerModel = $this->model('CaretakerModel');
        $this->historyModel = $this->model('HistoryModel');
    }

    // Add caretaker
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = $_POST;
            $data['profile_image'] = 'default.png';

            // ✅ upload folder
            $uploadDir = APPROOT . '/../public/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // ✅ handle image upload
            if (!empty($_FILES['profile_image']['name'])) {

                $err = $_FILES['profile_image']['error'];

                // Handle upload errors clearly
                if ($err !== UPLOAD_ERR_OK) {
                    $_SESSION['error'] = "Image upload failed (error code: $err).";

                    // Common: too large (ini or form limit)
                    if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
                        $_SESSION['error'] = "Image too large. Please upload a smaller file (e.g., under 2MB).";
                    }

                    header("Location: " . URLROOT . "/admin/caretaker_add");
                    exit;
                }

                // ✅ Validate size (2MB)
                $maxSize = 2 * 1024 * 1024;
                if ($_FILES['profile_image']['size'] > $maxSize) {
                    $_SESSION['error'] = "Image too large. Max 2MB allowed.";
                    header("Location: " . URLROOT . "/admin/caretaker_add");
                    exit;
                }

                // ✅ Validate extension
                $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
                $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedExt)) {
                    $_SESSION['error'] = "Invalid image type. Use JPG/PNG/WEBP.";
                    header("Location: " . URLROOT . "/admin/caretaker_add");
                    exit;
                }

                // ✅ Safer unique name
                $fileName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $targetPath = $uploadDir . $fileName;

                if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
                    $_SESSION['error'] = "Failed to save image. Check public/uploads permission.";
                    header("Location: " . URLROOT . "/admin/caretaker_add");
                    exit;
                }

                $data['profile_image'] = $fileName;
            }

            // ✅ Insert caretaker
            $ok = $this->caretakerModel->addCaretaker($data);

            if (!$ok) {
                $_SESSION['error'] = "Failed to add caretaker. Please try again.";
                header("Location: " . URLROOT . "/admin/caretaker_add");
                exit;
            }

            // ✅ History log (only after successful insert)
            $this->historyModel->log([
                'user_id' => $_SESSION['user']['id'] ?? null,
                'username' => $_SESSION['user']['username'] ?? 'unknown',
                'role' => 'admin',
                'action' => "Added caretaker: " . ($data['name'] ?? 'Unknown'),
                'section' => "Caretakers"
            ]);

            header("Location: " . URLROOT . "/admin/ad_caretakers");
            exit;
        }

        $this->view("admin/caretaker_add");
    }

    // Edit caretaker
    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->caretakerModel->updateCaretaker($id, $_POST);
            $this->historyModel->log([
                'user_id' => $_SESSION['user']['id'],
                'username' => $_SESSION['user']['username'],
                'role' => 'admin',
                'action' => "Updated caretaker (ID: $id)",
                'section' => "Caretakers"
            ]);

            header("Location: " . URLROOT . "/admin/ad_caretakers");
            exit;
        } else {
            $caretaker = $this->caretakerModel->getCaretakerById($id);
            $this->view("admin/caretaker_edit", ['caretaker' => $caretaker]);
        }
    }

    // Delete caretaker
    public function delete($id)
    {
        $this->caretakerModel->deleteCaretaker($id);
        $this->historyModel->log([
            'user_id' => $_SESSION['user']['id'],
            'username' => $_SESSION['user']['username'],
            'role' => 'admin',
            'action' => "Deleted caretaker (ID: $id)",
            'section' => "Caretakers"
        ]);

        header("Location: " . URLROOT . "/admin/ad_caretakers");
        exit;
    }

    // List all caretakers
    public function list()
    {
        // Filters (GET)
        $filters = [
            'service_type' => trim($_GET['service_type'] ?? ''),
            'status' => trim($_GET['status'] ?? ''),
            'location' => trim($_GET['location'] ?? ''),
            'q' => trim($_GET['q'] ?? '') // optional name search
        ];

        // Pagination
        $perPage = 10;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $offset = ($page - 1) * $perPage;

        // Total + rows
        $total = $this->caretakerModel->countCaretakersFiltered($filters);
        $caretakers = $this->caretakerModel->getCaretakersFiltered($filters, $perPage, $offset);

        $totalPages = max(1, (int) ceil($total / $perPage));

        $this->view("admin/ad_caretakers", [
            'caretakers' => $caretakers,
            'filters' => $filters,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

}

