<?php

class HRCaretakerCRUDController extends Controller
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
            
            // ✅ SERVER-SIDE VALIDATION
            $errors = [];

            // Check required fields
            $requiredFields = ['name', 'email', 'password', 'phone', 'experience', 'location', 'qualifications', 'service_type', 'status'];
            foreach ($requiredFields as $field) {
                if (empty(trim($data[$field] ?? ''))) {
                    $errors[] = "Field '$field' is required.";
                }
            }

            // Email validation
            if (!empty($data['email'])) {
                if (!filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Invalid email format. Use format like abc@gmail.com";
                } elseif (!strpos(trim($data['email']), '@gmail.com')) {
                    $errors[] = "Email must end with @gmail.com (e.g., abc@gmail.com)";
                }
            }

            // Phone validation
            if (!empty($data['phone'])) {
                $phone = trim($data['phone']);
                // Remove all non-digit characters and check if exactly 10 digits remain
                $phoneDigits = preg_replace('/\D/', '', $phone);
                if (strlen($phoneDigits) !== 10) {
                    $errors[] = "Phone number must be exactly 10 digits (e.g., 0771234567)";
                }
            }

            // Password validation
            if (!empty($data['password'])) {
                $pwErr = CaretakerModel::validateCaretakerPassword(trim($data['password']));
                if ($pwErr !== null) {
                    $errors[] = $pwErr;
                }
            }

            // If there are validation errors, store and redirect
            if (!empty($errors)) {
                $_SESSION['error'] = implode("; ", $errors);
                header("Location: " . URLROOT . "/HRCaretakerCRUD/list?open=add");
                exit;
            }

            $data['profile_image'] = 'default.jpg';

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

                    header("Location: " . URLROOT . "/HRCaretakerCRUD/list?open=add");
                    exit;
                }

                // ✅ Validate size (2MB)
                $maxSize = 2 * 1024 * 1024;
                if ($_FILES['profile_image']['size'] > $maxSize) {
                    $_SESSION['error'] = "Image too large. Max 2MB allowed.";
                    header("Location: " . URLROOT . "/HRCaretakerCRUD/list?open=add");
                    exit;
                }

                // ✅ Validate extension
                $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
                $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedExt)) {
                    $_SESSION['error'] = "Invalid image type. Use JPG/PNG/WEBP.";
                    header("Location: " . URLROOT . "/HRCaretakerCRUD/list?open=add");
                    exit;
                }

                // ✅ Safer unique name
                $fileName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $targetPath = $uploadDir . $fileName;

                if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
                    $_SESSION['error'] = "Failed to save image. Check public/uploads permission.";
                    header("Location: " . URLROOT . "/HRCaretakerCRUD/list?open=add");
                    exit;
                }

                $data['profile_image'] = $fileName;
            }

            // ✅ Insert caretaker
            $ok = $this->caretakerModel->addCaretaker($data);

            if (!$ok) {
                $_SESSION['error'] = "Failed to add caretaker. Please try again.";
                header("Location: " . URLROOT . "/HRCaretakerCRUD/list?open=add");
                exit;
            }

            // ✅ History log (only after successful insert)
            $this->historyModel->log([
                'user_id' => AuthSession::profileId() ?: null,
                'username' => $_SESSION['user']['username'] ?? 'unknown',
                'role' => 'admin',
                'action' => "Added caretaker: " . ($data['name'] ?? 'Unknown'),
                'section' => "Caretakers"
            ]);

            $_SESSION['success'] = 'Caretaker added successfully!';
            header("Location: " . URLROOT . "/HRCaretakerCRUD/list");
            exit;
        }

        header("Location: " . URLROOT . "/HRCaretakerCRUD/list?open=add");
        exit;
    }

    // Edit caretaker
    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            
            // ✅ SERVER-SIDE VALIDATION
            $errors = [];

            // Check required fields
            $requiredFields = ['name', 'email', 'phone', 'experience', 'location', 'qualifications', 'service_type', 'status'];
            foreach ($requiredFields as $field) {
                if (empty(trim($data[$field] ?? ''))) {
                    $errors[] = "Field '$field' is required.";
                }
            }

            // Email validation
            if (!empty($data['email'])) {
                if (!filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Invalid email format. Use format like abc@gmail.com";
                } elseif (!strpos(trim($data['email']), '@gmail.com')) {
                    $errors[] = "Email must end with @gmail.com (e.g., abc@gmail.com)";
                }
            }

            // Phone validation
            if (!empty($data['phone'])) {
                $phone = trim($data['phone']);
                // Remove all non-digit characters and check if exactly 10 digits remain
                $phoneDigits = preg_replace('/\D/', '', $phone);
                if (strlen($phoneDigits) !== 10) {
                    $errors[] = "Phone number must be exactly 10 digits (e.g., 0771234567)";
                }
            }

            $newPassword = trim($data['new_password'] ?? '');
            if ($newPassword !== '') {
                $pwErr = CaretakerModel::validateCaretakerPassword($newPassword);
                if ($pwErr !== null) {
                    $errors[] = $pwErr;
                }
            }

            // If there are validation errors, store and redirect
            if (!empty($errors)) {
                $_SESSION['error'] = implode("; ", $errors);
                header("Location: " . URLROOT . "/HRCaretakerCRUD/list?open=edit&id=" . (int) $id);
                exit;
            }

            // ✅ Handle profile image upload during edit
            $profileImage = null;
            if (!empty($_FILES['profile_image']['name']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = APPROOT . '/../public/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
                $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
                
                if (in_array($ext, $allowedExt) && $_FILES['profile_image']['size'] <= 2 * 1024 * 1024) {
                    $fileName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $targetPath = $uploadDir . $fileName;
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
                        $profileImage = $fileName; // Successfully saved
                    }
                }
            }

            $this->caretakerModel->updateCaretaker($id, $data, $profileImage);
            if ($newPassword !== '') {
                $this->caretakerModel->updateCaretakerPassword($id, password_hash($newPassword, PASSWORD_DEFAULT));
            }
            $this->historyModel->log([
                'user_id' => AuthSession::profileId(),
                'username' => $_SESSION['user']['username'],
                'role' => 'admin',
                'action' => "Updated caretaker (ID: $id)",
                'section' => "Caretakers"
            ]);

            $_SESSION['success'] = 'Caregiver updated successfully.';
            header("Location: " . URLROOT . "/HRCaretakerCRUD/list");
            exit;
        }

        header("Location: " . URLROOT . "/HRCaretakerCRUD/list?open=edit&id=" . (int) $id);
        exit;
    }

    // View caretaker details
    public function viewCaretaker($id)
    {
        $caretaker = $this->caretakerModel->getCaretakerById($id);
        if (!$caretaker) {
            $_SESSION['error'] = "Caretaker not found.";
            header("Location: " . URLROOT . "/HRCaretakerCRUD/list");
            exit;
        }
        $this->view("hr/caretaker_view", ['caretaker' => $caretaker]);
    }

    // Delete caretaker
    public function delete($id)
    {
        $this->caretakerModel->deleteCaretaker($id);
        $this->historyModel->log([
            'user_id' => AuthSession::profileId(),
            'username' => $_SESSION['user']['username'],
            'role' => 'admin',
            'action' => "Deleted caretaker (ID: $id)",
            'section' => "Caretakers"
        ]);

        header("Location: " . URLROOT . "/HRCaretakerCRUD/list");
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

        $openModal = trim((string) ($_GET['open'] ?? ''));
        $editId = (int) ($_GET['id'] ?? 0);
        $editCaretaker = null;
        if ($openModal === 'edit' && $editId > 0) {
            $editCaretaker = $this->caretakerModel->getCaretakerById($editId);
            if (!$editCaretaker) {
                $openModal = '';
            }
        }

        $this->view("hr/hr_addct", [
            'caretakers' => $caretakers,
            'filters' => $filters,
            'page' => $page,
            'totalPages' => $totalPages,
            'openModal' => $openModal,
            'editCaretaker' => $editCaretaker,
        ]);
    }

}