<?php
class HrSettingsController extends Controller
{
    private $userModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Validate session
        if (!AuthSession::isLoggedIn()) {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        $this->userModel = $this->model('UserModel');

        // Re-fetch user from DB
        $user = $this->userModel->getUserById(AuthSession::profileId());
        if (!$user) {
            session_destroy();
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        $_SESSION['user'] = $user;

        // Restrict access to manager role.
        if (!AuthSession::hasRole('manager')) {
            die("Access denied. Only HR can access this page.");
        }
    }

    // Display settings page
    public function home()
    {
        $user = $_SESSION['user'];
        $this->view('hr/hr_settings', ['user' => $user]);
    }

    // Update profile
    public function update_profile()
    {
        // Make sure session is started
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Validate user session
        if (!AuthSession::isLoggedIn()) {
            header("Location: index.php?url=auth/login");
            exit;
        }

        $userId = AuthSession::profileId();
        $userModel = $this->model('UserModel');

        // Prepare data array
        $data = [
            'username' => $_POST['username'] ?? '',
            'phone'    => $_POST['phone'] ?? ''
        ];

        // Handle profile picture upload
        if (isset($_FILES['profileFile']) && $_FILES['profileFile']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = dirname(APPROOT) . '/public/images/profiles/';

            // Create folder if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $tmpName = $_FILES['profileFile']['tmp_name'];
            $ext = pathinfo($_FILES['profileFile']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            $destination = $uploadDir . $filename;

            if (move_uploaded_file($tmpName, $destination)) {
                $data['profile_pic'] = $filename;
            } else {
                die("Failed to upload profile picture.");
            }
        }

        // Update user in DB
        $userModel->updateUserProfile($userId, $data);

        // Re-fetch updated user data
        $user = $userModel->getUserById($userId);
        $_SESSION['user'] = $user;

        // Redirect back to settings page
        header('Location: ' . URLROOT . '/public?url=hr/hr_settings');
        exit;
    }

    // Change password
    public function change_password()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = AuthSession::profileId();
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];

            $user = $this->userModel->getUserById($userId);

            if (!password_verify($currentPassword, $user['password'])) {
                $_SESSION['flash_error'] = "Current password is incorrect!";
                header('Location: ' . URLROOT . '/public?url=hr/hr_settings');
                exit();
            }

            if ($newPassword !== $confirmPassword) {
                $_SESSION['flash_error'] = "New passwords do not match!";
                header('Location: ' . URLROOT . '/public?url=hr/hr_settings');
                exit();
            }

            $pwErr = UserModel::validatePasswordPolicy($newPassword);
            if ($pwErr !== null) {
                $_SESSION['flash_error'] = $pwErr;
                header('Location: ' . URLROOT . '/public?url=hr/hr_settings');
                exit();
            }

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->userModel->updatePassword($userId, $hashedPassword);

            $_SESSION['flash_success'] = "Password updated successfully!";
            header('Location: ' . URLROOT . '/public?url=hr/hr_settings');
            exit();
        }
    }
}
