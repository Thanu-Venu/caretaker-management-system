<?php
require_once APPROOT . '/core/Database.php';

class AdminLeaveModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // Get all leaves submitted by caretakers
    public function getAllLeaves() {
        $sql = "
    SELECT l.*, c.name AS caretaker_name
    FROM leaves l
    JOIN caretakers c ON l.user_id = c.id
    ORDER BY l.start_date DESC
";

        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    public function countPendingLeaves()
{
    $stmt = $this->conn->prepare("
        SELECT COUNT(*) AS total
        FROM leaves l
        INNER JOIN caretakers c ON l.user_id = c.id
        WHERE l.status = 'pending'
    ");
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc()['total'] ?? 0;
}

public function getLeavesPaginatedFiltered($limit, $offset, $type, $status)
{
    $sql = "
        SELECT 
            l.*,
            c.name AS caretaker_name
        FROM leaves l
        JOIN caretakers c ON l.user_id = c.id
        WHERE 1=1
    ";

    $types = "";
    $params = [];

    if ($type && $type !== "All") {
        $sql .= " AND l.leave_type = ?";
        $types .= "s";
        $params[] = $type;
    }

    if ($status && $status !== "All") {
        $sql .= " AND l.status = ?";
        $types .= "s";
        $params[] = $status;
    }

    $sql .= " ORDER BY l.start_date DESC LIMIT ? OFFSET ?";
    $types .= "ii";
    $params[] = $limit;
    $params[] = $offset;

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

public function getTotalLeavesFiltered($type, $status)
{
    $sql = "
        SELECT COUNT(*) AS total
        FROM leaves l
        JOIN caretakers c ON l.user_id = c.id
        WHERE 1=1
    ";

    $types = "";
    $params = [];

    if ($type && $type !== "All") {
        $sql .= " AND l.leave_type = ?";
        $types .= "s";
        $params[] = $type;
    }

    if ($status && $status !== "All") {
        $sql .= " AND l.status = ?";
        $types .= "s";
        $params[] = $status;
    }

    $stmt = $this->conn->prepare($sql);

    if ($types !== "") {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    return (int)$stmt->get_result()->fetch_assoc()['total'];
}


}
