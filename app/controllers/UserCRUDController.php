<?php


class UserCRUDController extends Controller {

    private $userModel;
    private $historyModel;
    public function __construct() {
        $this->userModel = $this->model('UserModel');
        $this->historyModel = $this->model('HistoryModel');
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
            $this->historyModel->log([
                'user_id' => $_SESSION['user']['id'],
                'username' => $_SESSION['user']['username'],
                'role' => 'admin',
                'action' => "Added user: " . ($_POST['username'] ?? 'Unknown'),
                'section' => "Staffs"
            ]);
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
        $this->historyModel->log([
            'user_id' => $_SESSION['user']['id'],
            'username' => $_SESSION['user']['username'],
            'role' => 'admin',
            'action' => "Updated user (ID: $id)",
            'section' => "Staffs"
        ]);
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
        $this->historyModel->log([
            'user_id' => $_SESSION['user']['id'],
            'username' => $_SESSION['user']['username'],
            'role' => 'admin',
            'action' => "Deleted user (ID: $id)",
            'section' => "Staffs"
        ]);
        header("Location: " . URLROOT . "/admin/ad_users");
        exit;
    }
}
?>
