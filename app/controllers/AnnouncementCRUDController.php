<?php


class AnnouncementCRUDController extends Controller {
    private $announcementModel;

    public function __construct() {
        $this->announcementModel = $this->model('AnnouncementModel');
    }

    // List announcements
   public function index()
{
    $announcements = $this->announcementModel->getAllAnnouncements(); // your existing method
    $data = ['announcements' => $announcements];
    $this->view('admin/ad_announcement', $data);
}

public function create()
{
    $this->view('admin/ad_addAnnouncement');
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
                $_SESSION['flash_message'] = "Announcement added successfully!";
                header("Location: " . URLROOT . "/AnnouncementCRUD/index");
                exit;
            } else {
                die("Something went wrong!");
            }
        }
    }

    // Edit announcement
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id' => $id,
                'title' => trim($_POST['title']),
                'message' => trim($_POST['message']),
                'target_role' => trim($_POST['target_role'])
            ];

            if ($this->announcementModel->updateAnnouncement($data)) {
                $_SESSION['flash_message'] = "Announcement updated!";
                header("Location: " . URLROOT . "/AnnouncementCRUD/index");
                exit;
            }
        } else {
            $announcement = $this->announcementModel->getAnnouncementById($id);
            $this->view('admin/edit_announcement', ['announcement' => $announcement]);
        }
    }

    // Delete announcement
    public function delete($id) {
        $this->announcementModel->deleteAnnouncement($id);
        $_SESSION['flash_message'] = "Announcement deleted!";
        header("Location: " . URLROOT . "/AnnouncementCRUD/index");
        exit;
    }


}


?>