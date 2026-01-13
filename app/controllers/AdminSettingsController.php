<?php
class AdminSettingsController extends Controller
{
    private $userModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Validate session
        if (!isset($_SESSION['user'])) {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        $this->userModel = $this->model('UserModel');

        // Re-fetch user from DB
        $user = $this->userModel->getUserById($_SESSION['user']['id']);
        if (!$user) {
            session_destroy();
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        $_SESSION['user'] = $user;

        // Optional: restrict access to admins only
        if ($_SESSION['user']['role'] !== 'admin') {
            die("Access denied. Only admin can access this page.");
        }
    }

    // Display settings page
    public function home()
    {
        $user = $_SESSION['user'];
        $this->view('admin/ad_settings', ['user' => $user]);
    }

    // Update profile
    public function update_profile()
{
    // Make sure session is started
    if (session_status() === PHP_SESSION_NONE) session_start();

    // Validate user session
    if (!isset($_SESSION['user'])) {
        header("Location: index.php?url=auth/login");
        exit;
    }

    $userId = $_SESSION['user']['id'];
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
    header("Location: " . URLROOT . "/admin/ad_settings");
    exit;
}

    // Change password
    public function change_password()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];

            $user = $this->userModel->getUserById($userId);

            if (!password_verify($currentPassword, $user['password'])) {
                $_SESSION['flash_error'] = "Current password is incorrect!";
                header('Location: ' . URLROOT . '/adminsettings');
                exit();
            }

            if ($newPassword !== $confirmPassword) {
                $_SESSION['flash_error'] = "New passwords do not match!";
                header('Location: ' . URLROOT . '/adminsettings');
                exit();
            }

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->userModel->updatePassword($userId, $hashedPassword);

            $_SESSION['flash_success'] = "Password updated successfully!";
            header('Location: ' . URLROOT . '/adminsettings');
            exit();
        }
    }
}
