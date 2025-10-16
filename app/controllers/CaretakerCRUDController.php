<?php
class UserController extends Controller {

    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    // 🔹 View all users
    public function index() {
        $users = $this->userModel->getAllUsers();
        $this->view('admin/ad_users', ['users' => $users]);
    }

    // 🔹 Add new user
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'role' => $_POST['role']
            ];

            if ($this->userModel->addUser($data)) {
                header("Location: " . URLROOT . "/UserController/index");
                exit;
            } else {
                die("Error adding user.");
            }
        } else {
            $this->view('admin/ad_user_add');
        }
    }

    // 🔹 Edit user
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id' => $id,
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'role' => $_POST['role']
            ];

            if ($this->userModel->updateUser($data)) {
                header("Location: " . URLROOT . "/UserController/index");
                exit;
            } else {
                die("Error updating user.");
            }
        } else {
            $user = $this->userModel->getUserById($id);
            $this->view('admin/ad_user_edit', ['user' => $user]);
        }
    }

    // 🔹 Delete user
    public function delete($id) {
        if ($this->userModel->deleteUser($id)) {
            header("Location: " . URLROOT . "/UserController/index");
            exit;
        } else {
            die("Error deleting user.");
        }
    }
}
