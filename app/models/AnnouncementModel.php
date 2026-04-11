<?php
require_once APPROOT . '/core/Database.php';

class AnnouncementModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // Add announcement
    public function addAnnouncement($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO announcements (title, message, target_role, created_by, created_at) 
             VALUES (?, ?, ?, ?, NOW())"
        );
        $stmt->bind_param(
            "sssi",
            $data['title'],
            $data['message'],
            $data['target_role'],
            $data['created_by']
        );
        return $stmt->execute();
    }

    // Get all announcements
    public function getAllAnnouncements() {
        $result = $this->conn->query("SELECT a.*, u.username AS created_by_name FROM announcements a 
                                      LEFT JOIN users u ON a.created_by = u.id
                                      ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Delete announcement
    public function deleteAnnouncement($id) {
        $stmt = $this->conn->prepare("DELETE FROM announcements WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Get single announcement
    public function getAnnouncementById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM announcements WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Update announcement
    public function updateAnnouncement($data) {
        $stmt = $this->conn->prepare(
            "UPDATE announcements SET title=?, message=?, target_role=? WHERE id=?"
        );
        $stmt->bind_param(
            "sssi",
            $data['title'],
            $data['message'],
            $data['target_role'],
            $data['id']
        );
        return $stmt->execute();
    }
public function getCaretakerAnnouncements()
{
    $stmt = $this->conn->prepare(
        "SELECT * FROM announcements
         WHERE target_role IN ('caretaker', 'All')
         ORDER BY created_at DESC"
    );
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function getClientAnnouncements()
{
    $stmt = $this->conn->prepare(
        "SELECT * FROM announcements
         WHERE target_role IN ('client', 'All')
         ORDER BY created_at DESC"
    );
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

    public function getUserAnnouncements()
{
    $stmt = $this->conn->prepare(
        "SELECT * FROM announcements
         WHERE target_role IN ('users', 'All')
         ORDER BY created_at DESC"
    );
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

    /** Build WHERE fragments for filtered announcement queries. */
    private function buildAnnouncementFilterClause(array $filters)
    {
        $parts = ['1=1'];
        $types = '';
        $params = [];

        $target = trim((string)($filters['target_role'] ?? ''));
        if ($target !== '') {
            $parts[] = 'a.target_role = ?';
            $types .= 's';
            $params[] = $target;
        }

        $dateFrom = trim((string)($filters['date_from'] ?? ''));
        if ($dateFrom !== '') {
            $parts[] = 'DATE(a.created_at) >= ?';
            $types .= 's';
            $params[] = $dateFrom;
        }

        $dateTo = trim((string)($filters['date_to'] ?? ''));
        if ($dateTo !== '') {
            $parts[] = 'DATE(a.created_at) <= ?';
            $types .= 's';
            $params[] = $dateTo;
        }

        $q = trim((string)($filters['q'] ?? ''));
        if ($q !== '') {
            $parts[] = '(a.title LIKE ? OR a.message LIKE ? OR u.username LIKE ?)';
            $types .= 'sss';
            $like = '%' . $q . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        return [$parts, $types, $params];
    }

    public function countAnnouncementsFiltered(array $filters)
    {
        [$parts, $types, $params] = $this->buildAnnouncementFilterClause($filters);
        $sql = 'SELECT COUNT(*) AS cnt FROM announcements a '
            . 'LEFT JOIN users u ON a.created_by = u.id WHERE ' . implode(' AND ', $parts);
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return 0;
        }
        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        return (int)($row['cnt'] ?? 0);
    }

    public function getAnnouncementsFilteredPaged(array $filters, $limit, $offset)
    {
        [$parts, $types, $params] = $this->buildAnnouncementFilterClause($filters);
        $limit = max(1, min(100, (int)$limit));
        $offset = max(0, (int)$offset);

        $sql = 'SELECT a.*, u.username AS created_by_name FROM announcements a '
            . 'LEFT JOIN users u ON a.created_by = u.id WHERE ' . implode(' AND ', $parts)
            . ' ORDER BY a.created_at DESC LIMIT ' . $limit . ' OFFSET ' . $offset;

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return [];
        }
        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

}
?>