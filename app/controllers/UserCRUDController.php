<?php

class UserCRUDController extends Controller
{
    private $userModel;
    private $historyModel;

    public function __construct()
    {
        $this->userModel = $this->model('UserModel');
        $this->historyModel = $this->model('HistoryModel');
    }

    public function list()
    {
        $users = $this->userModel->getAllUsers();

        $openModal = trim((string) ($_GET['open'] ?? ''));
        $editId = (int) ($_GET['id'] ?? 0);
        $editUser = null;
        if ($openModal === 'edit' && $editId > 0) {
            $editUser = $this->userModel->getUserById($editId);
            if (!$editUser) {
                $openModal = '';
            }
        }

        $this->view('admin/ad_users', [
            'users' => $users,
            'openModal' => $openModal,
            'editUser' => $editUser,
        ]);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ok = $this->userModel->addUser($_POST);
            if (!$ok) {
                $_SESSION['error'] = 'Could not add staff member. Check the details and try again.';
                header('Location: ' . URLROOT . '/userCRUD/list?open=add');
                exit;
            }

            $this->historyModel->log([
                'user_id' => AuthSession::profileId(),
                'username' => $_SESSION['user']['username'],
                'role' => 'admin',
                'action' => 'Added user: ' . ($_POST['username'] ?? 'Unknown'),
                'section' => 'Staffs',
            ]);
            $_SESSION['success'] = 'Staff member added successfully.';
            header('Location: ' . URLROOT . '/userCRUD/list');
            exit;
        }

        header('Location: ' . URLROOT . '/userCRUD/list?open=add');
        exit;
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ok = $this->userModel->updateUser($id, $_POST);
            if (!$ok) {
                $_SESSION['error'] = 'Could not update staff member. Please try again.';
                header('Location: ' . URLROOT . '/userCRUD/list?open=edit&id=' . (int) $id);
                exit;
            }

            $this->historyModel->log([
                'user_id' => AuthSession::profileId(),
                'username' => $_SESSION['user']['username'],
                'role' => 'admin',
                'action' => "Updated user (ID: $id)",
                'section' => 'Staffs',
            ]);
            $_SESSION['success'] = 'Staff member updated successfully.';
            header('Location: ' . URLROOT . '/userCRUD/list');
            exit;
        }

        header('Location: ' . URLROOT . '/userCRUD/list?open=edit&id=' . (int) $id);
        exit;
    }

    public function delete($id)
    {
        $this->userModel->deleteUser($id);
        $this->historyModel->log([
            'user_id' => AuthSession::profileId(),
            'username' => $_SESSION['user']['username'],
            'role' => 'admin',
            'action' => "Deleted user (ID: $id)",
            'section' => 'Staffs',
        ]);

        header('Location: ' . URLROOT . '/userCRUD/list');
        exit;
    }
}
