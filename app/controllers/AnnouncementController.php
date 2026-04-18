<?php
class AnnouncementController extends Controller {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE)
            session_start();

        if (!AuthSession::hasRole('caretaker')) {
            header("Location: index.php?url=auth/login");
            exit;
        }

        $this->announcementModel = $this->model('AnnouncementModel');
    }

    public function index() {
        // Get pagination parameters
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 15;

        // Get filter parameters
        $filters = [
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'q' => $_GET['q'] ?? ''
        ];

        // Get announcements data
        $announcements = $this->announcementModel->getCaretakerAnnouncements();
        
        // Apply filtering (basic implementation)
        if (!empty($filters['date_from']) || !empty($filters['date_to']) || !empty($filters['q'])) {
            $announcements = $this->filterAnnouncements($announcements, $filters);
        }
        
        // Apply pagination
        $totalRecords = count($announcements);
        $totalPages = ceil($totalRecords / $perPage);
        $offset = ($page - 1) * $perPage;
        $paginatedAnnouncements = array_slice($announcements, $offset, $perPage);
        
        $data = [
            'announcements' => $paginatedAnnouncements,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords,
            'perPage' => $perPage,
            'filters' => $filters
        ];

        $this->view('caretaker/ct_announcement', $data);
    }
    
    private function filterAnnouncements($announcements, $filters) {
        return array_filter($announcements, function($announcement) use ($filters) {
            // Date filtering
            if (!empty($filters['date_from']) && $announcement['created_at'] < $filters['date_from']) {
                return false;
            }
            if (!empty($filters['date_to']) && $announcement['created_at'] > $filters['date_to']) {
                return false;
            }
            
            // Text search
            if (!empty($filters['q'])) {
                $searchTerm = strtolower($filters['q']);
                $title = strtolower($announcement['title'] ?? '');
                $message = strtolower($announcement['message'] ?? '');
                if (strpos($title, $searchTerm) === false && strpos($message, $searchTerm) === false) {
                    return false;
                }
            }
            
            return true;
        });
    }
}
