<?php

class CaretakerController extends Controller
{
    private $leaveModel;
    private $caretakerModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();

        if (!isset($_SESSION['user'])) {
            header("Location: index.php?url=auth/login");
            exit;
        }

        $this->leaveModel = $this->model('LeaveModel');
        $this->caretakerModel = $this->model('CaretakerModel');

        // Revalidate caretaker from DB
        $user = $this->caretakerModel->getCaretakerById($_SESSION['user']['id']);
        if (!$user) {
            session_destroy();
            header("Location: index.php?url=auth/login");
            exit;
        }

        $_SESSION['user'] = $user;
    }

    /* ================= Dashboard ================= */
    public function ct_dashboard()
    {
        $this->view("caretaker/ct_dashboard");
    }

    public function ct_editprofile()
    {
        $this->view("caretaker/ct_editprofile");
    }

    /* ================= Leave ================= */
    public function ct_leave()
    {
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'caretaker') {
            die("Caretaker not logged in");
        }

        $userId = $_SESSION['user']['id'];
        $leaves = $this->leaveModel->getLeavesByUser($userId);

        $this->view('caretaker/ct_leave', ['leaves' => $leaves]);
    }

    public function ct_leaveHistory()
    {
        $this->view("caretaker/ct_leaveHistory");
    }

    /* ================= Booking ================= */
    public function ct_booking()
    {
        $caretakerId = $_SESSION['user']['id'];

        $upcoming = $this->caretakerModel->getUpcomingBookings($caretakerId);
        $past = $this->caretakerModel->getPastBookings($caretakerId);

        $this->view('caretaker/ct_booking', [
            'upcoming' => $upcoming,
            'past' => $past
        ]);
    }

    /* ================= Schedule ================= */
    public function ct_schedule()
    {
        $this->view("caretaker/ct_schedule");
    }

    /* ================= Complaints & Reports ================= */
    public function ct_complaints()
    {
        $this->view("caretaker/ct_complaints");
    }

    public function ct_reports()
    {
        $this->view("caretaker/ct_reports");
    }

    /* ================= Announcements ================= */
    public function ct_announcement()
    {
        if ($_SESSION['role'] === 'caretaker') {
            $user = $this->caretakerModel->getCaretakerById($_SESSION['user']['id']);
            if (!$user) {
                session_destroy();
                header("Location: index.php?url=auth/login");
                exit;
            }
            $_SESSION['user'] = $user;
        }

        $announcementModel = $this->model('AnnouncementModel');
        $announcements = $announcementModel->getCaretakerAnnouncements();

        $this->view("caretaker/ct_announcement", $announcements);
    }

    /* ================= Settings ================= */
    public function ct_settings()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?url=auth/login");
            exit;
        }

        $this->view("caretaker/ct_settings", [
            'user' => $_SESSION['user']
        ]);
    }

    /* ================= Reviews ================= */
    public function ct_reviews()
    {
        $this->view("caretaker/ct_reviews");
    }
}
