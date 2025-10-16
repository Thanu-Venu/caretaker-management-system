<?php
class UserCRUDController extends Controller {

    private $userModel;

    public function __construct() {
        // ✅ Model பெயர் சரியாக பொருத்தி இணைக்கப்பட்டுள்ளது
        $this->userModel = $this->model('UserModel');
    }

    // 🔹 Display All Users
    public function index() {
        $users = $this->userModel->getAllUsers();
        $this->view("admin/ad_users", ['users' => $users]);
    }

    // 🔹 Add New User
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // password hashing
            $_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $this->userModel->addUser($_POST);
            header("Location: " . URLROOT . "/UserCRUDController/index");
            exit;

        } else {
            $this->view("admin/user_add");
        }
    }

    // 🔹 Edit User
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->userModel->updateUser($id, $_POST);
            header("Location: " . URLROOT . "/UserCRUDController/index");
            exit;
        } else {
            $user = $this->userModel->getUserById($id);
            $this->view("admin/user_edit", ['user' => $user]);
        }
    }

    // 🔹 Delete User
    public function delete($id) {
        $this->userModel->deleteUser($id);
        header("Location: " . URLROOT . "/UserCRUDController/index");
        exit;
    }
}
?>
