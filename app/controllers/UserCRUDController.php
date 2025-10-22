<?php
session_start();

class UserCRUDController extends Controller {

    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('UserModel');
    }

    // 🔹 Display All Users
    public function list() {
        $users = $this->userModel->getAllUsers();
        $this->view("admin/ad_users", ['users' => $users]);
    }

    // 🔹 Add New User
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->userModel->addUser($_POST);
            header("Location: " . URLROOT . "/admin/ad_users");
            exit;
        } else {
            $this->view("admin/user_add");
        }
    }
    
    // 🔹 Edit User
   public function edit($id) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $this->userModel->updateUser($id, $_POST);
        header("Location: " . URLROOT . "/userCRUD/list");
        exit;
    } else {
        $userArray = $this->userModel->getUserById($id);
        $user = (object) $userArray; // array → object
        $this->view("admin/user_edit", ['user' => $user]);
    }
}

    // 🔹 Delete User
    public function delete($id) {
        $this->userModel->deleteUser($id);
        header("Location: " . URLROOT . "/admin/ad_users");
        exit;
    }
}
?>
