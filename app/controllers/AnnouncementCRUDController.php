<?php


class AnnouncementCRUDController extends Controller {
    private $announcementModel;

    public function __construct() {
        $this->announcementModel = $this->model('AnnouncementModel');
    }

    // List announcements
   public function index()
{
    $perPage = 10;
    $filters = [
        'target_role' => trim((string)($_GET['target_role'] ?? '')),
        'date_from' => trim((string)($_GET['date_from'] ?? '')),
        'date_to' => trim((string)($_GET['date_to'] ?? '')),
        'q' => trim((string)($_GET['q'] ?? '')),
    ];
    $page = max(1, (int)($_GET['page'] ?? 1));
    $total = $this->announcementModel->countAnnouncementsFiltered($filters);
    $totalPages = max(1, (int)ceil($total / $perPage));
    if ($page > $totalPages) {
        $page = $totalPages;
    }
    $offset = ($page - 1) * $perPage;
    $announcements = $this->announcementModel->getAnnouncementsFilteredPaged($filters, $perPage, $offset);
    $listUrl = URLROOT . '/AnnouncementCRUD/index';

    $openModal = trim((string)($_GET['open'] ?? ''));
    if ($openModal !== 'add' && $openModal !== 'edit') {
        $openModal = '';
    }
    $editId = (int)($_GET['edit_id'] ?? 0);
    $editAnnouncement = null;
    if ($openModal === 'edit' && $editId > 0) {
        $editAnnouncement = $this->announcementModel->getAnnouncementById($editId);
        if (!is_array($editAnnouncement) || empty($editAnnouncement['id'])) {
            $editAnnouncement = null;
            $openModal = '';
        }
    }

    $data = [
        'announcements' => $announcements,
        'filters' => $filters,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'totalRecords' => $total,
        'perPage' => $perPage,
        'listUrl' => $listUrl,
        'filterFormAction' => URLROOT . '/AnnouncementCRUD/index',
        'filterFormHidden' => [],
        'openModal' => $openModal,
        'editAnnouncement' => $editAnnouncement,
    ];
    $this->view('admin/ad_announcement', $data);
}

public function create()
{
    header('Location: ' . URLROOT . '/public?url=admin/ad_announcement&open=add');
    exit;
}


    // Add announcement
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => trim($_POST['title']),
                'message' => trim($_POST['message']),
                'target_role' => trim($_POST['target_role']),
                'created_by' => $_SESSION['admin_id'] ?? 1  // replace with actual admin session
            ];

            if ($this->announcementModel->addAnnouncement($data)) {
                $_SESSION['flash_message'] = 'Announcement added successfully!';
                header('Location: ' . URLROOT . '/public?url=admin/ad_announcement');
                exit;
            } else {
                die("Something went wrong!");
            }
        }
    }

    // Edit announcement
    public function edit($id) {
        $id = (int) $id;
        if ($id < 1) {
            header('Location: ' . URLROOT . '/public?url=admin/ad_announcement');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id' => $id,
                'title' => trim($_POST['title']),
                'message' => trim($_POST['message']),
                'target_role' => trim($_POST['target_role'])
            ];

            if ($this->announcementModel->updateAnnouncement($data)) {
                $_SESSION['flash_message'] = 'Announcement updated!';
                header('Location: ' . URLROOT . '/public?url=admin/ad_announcement');
                exit;
            }
        } else {
            header('Location: ' . URLROOT . '/public?url=admin/ad_announcement&open=edit&edit_id=' . $id);
            exit;
        }
    }

    // Delete announcement
    public function delete($id) {
        $this->announcementModel->deleteAnnouncement($id);
        $_SESSION['flash_message'] = 'Announcement deleted!';
        header('Location: ' . URLROOT . '/public?url=admin/ad_announcement');
        exit;
    }


}


?>