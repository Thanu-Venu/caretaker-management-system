<?php
class UserCRUDController extends Controller {

    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('UserModel');
    }

    // Add User
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $result = $this->userModel->addUser($_POST);
            header("Location: " . URLROOT . "/admin/ad_users");
            exit;
        } else {
            $this->view("admin/user_add");
        }
    }

    // Edit User
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->userModel->updateUser($id, $_POST);
            header("Location: " . URLROOT . "/admin/ad_users");
            exit;
        } else {
            $user = $this->userModel->getUserById($id);
            $this->view("admin/user_edit", ['user' => $user]);
        }
    }

    // Delete User
    public function delete($id) {
        $this->userModel->deleteUser($id);
        header("Location: " . URLROOT . "/admin/ad_users");
        exit;
    }
}
?>
