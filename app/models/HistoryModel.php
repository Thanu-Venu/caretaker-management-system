<?php
class HistoryModel {
  private $conn;

  public function __construct() {
    $db = new Database();
    $this->conn = $db->conn;
  }

  public function log($data) {
    $sql = "INSERT INTO history_logs (user_id, username, role, action, section)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param(
      "issss",
      $data['user_id'],
      $data['username'],
      $data['role'],
      $data['action'],
      $data['section']
    );
    return $stmt->execute();
  }

  // ✅ ONLY ADMIN logs
  public function getAdminLogs() {
    $sql = "SELECT created_at, username, role, action, section
            FROM history_logs
            WHERE role = 'admin'
            ORDER BY created_at DESC";
    $result = $this->conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getRecentLogs($limit = 8)
{
    $stmt = $this->conn->prepare("
        SELECT action, section, username, created_at
        FROM history_logs
        ORDER BY created_at DESC
        LIMIT ?
    ");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

public function getLogsPaginated($limit, $offset)
{
    $stmt = $this->conn->prepare("
        SELECT created_at, username, role, action, section
        FROM history_logs
        WHERE role = 'admin'
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


public function getTotalLogs()
{
    $res = $this->conn->query("
        SELECT COUNT(*) AS total
        FROM history_logs
        WHERE role = 'admin'
    ");
    return (int) $res->fetch_assoc()['total'];
}


}
