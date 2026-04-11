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
        $perPage = 10;
        $filters = $this->normalizeStaffListFilters($_GET);
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $total = $this->userModel->countUsersFiltered($filters);
        $totalPages = max(1, (int) ceil($total / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;
        $users = $this->userModel->getUsersFilteredPaged($filters, $perPage, $offset);

        $openModal = trim((string) ($_GET['open'] ?? ''));
        $editId = (int) ($_GET['id'] ?? 0);
        $editUser = null;
        if ($openModal === 'edit' && $editId > 0) {
            $editUser = $this->userModel->getUserById($editId);
            if (!$editUser) {
                $openModal = '';
            }
        }

        $listQueryForHidden = ltrim($this->staffListQueryString($filters, $page, []), '?');

        $this->view('admin/ad_users', [
            'users' => $users,
            'openModal' => $openModal,
            'editUser' => $editUser,
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalRecords' => $total,
            'perPage' => $perPage,
            'listUrl' => URLROOT . '/userCRUD/list',
            'listQueryForHidden' => $listQueryForHidden,
        ]);
    }

    /**
     * @param array<string, mixed> $input
     * @return array{status: string, role: string}
     */
    private function normalizeStaffListFilters(array $input): array
    {
        $status = trim((string) ($input['status'] ?? ''));
        if (!in_array($status, ['Active', 'Inactive'], true)) {
            $status = '';
        }
        $role = trim((string) ($input['role'] ?? ''));
        if (!in_array($role, ['Admin', 'Manager'], true)) {
            $role = '';
        }

        return ['status' => $status, 'role' => $role];
    }

    /**
     * Query string for list URLs (leading ? or empty).
     *
     * @param array{status: string, role: string} $filters
     * @param array<string, scalar>             $extra merged into query (e.g. open, id)
     */
    private function staffListQueryString(array $filters, int $page, array $extra = []): string
    {
        $q = $extra;
        if ($filters['status'] !== '') {
            $q['status'] = $filters['status'];
        }
        if ($filters['role'] !== '') {
            $q['role'] = $filters['role'];
        }
        if ($page > 1) {
            $q['page'] = $page;
        }
        $built = http_build_query($q);

        return $built === '' ? '' : '?' . $built;
    }

    /**
     * Rebuild safe list query from POSTed `_staff_list_qs` (no leading ?).
     */
    private function staffListQueryFromPostedReturn(?string $raw): string
    {
        if ($raw === null || $raw === '') {
            return '';
        }
        parse_str($raw, $parts);
        if (!is_array($parts)) {
            return '';
        }
        $filters = $this->normalizeStaffListFilters($parts);
        $page = max(1, (int) ($parts['page'] ?? 1));

        return $this->staffListQueryString($filters, $page, []);
    }

    /** @param array<string, mixed> $get */
    private function staffListQueryFromGet(array $get): string
    {
        $filters = $this->normalizeStaffListFilters($get);
        $page = max(1, (int) ($get['page'] ?? 1));

        return $this->staffListQueryString($filters, $page, []);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $returnQs = $this->staffListQueryFromPostedReturn($_POST['_staff_list_qs'] ?? null);
            $ok = $this->userModel->addUser($_POST);
            if (!$ok) {
                $_SESSION['error'] = 'Could not add staff member. Check the details and try again.';
                $sep = $returnQs === '' ? '?' : '&';
                header('Location: ' . URLROOT . '/userCRUD/list' . $returnQs . $sep . 'open=add');
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
            header('Location: ' . URLROOT . '/userCRUD/list' . $returnQs);
            exit;
        }

        $getQs = $this->staffListQueryFromGet($_GET);
        header('Location: ' . URLROOT . '/userCRUD/list' . $getQs . ($getQs === '' ? '?' : '&') . 'open=add');
        exit;
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $returnQs = $this->staffListQueryFromPostedReturn($_POST['_staff_list_qs'] ?? null);
            $ok = $this->userModel->updateUser($id, $_POST);
            if (!$ok) {
                $_SESSION['error'] = 'Could not update staff member. Please try again.';
                $sep = $returnQs === '' ? '?' : '&';
                header('Location: ' . URLROOT . '/userCRUD/list' . $returnQs . $sep . 'open=edit&id=' . (int) $id);
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
            header('Location: ' . URLROOT . '/userCRUD/list' . $returnQs);
            exit;
        }

        $getQs = $this->staffListQueryFromGet($_GET);
        $sep = $getQs === '' ? '?' : '&';
        header('Location: ' . URLROOT . '/userCRUD/list' . $getQs . $sep . 'open=edit&id=' . (int) $id);
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

        $returnQs = $this->staffListQueryFromGet($_GET);
        header('Location: ' . URLROOT . '/userCRUD/list' . $returnQs);
        exit;
    }
}
