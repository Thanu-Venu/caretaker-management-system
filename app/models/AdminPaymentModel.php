<?php

class AdminPaymentModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }

    public function getPaymentSummary(array $filters = []): array
    {
        [$where, $types, $params] = $this->buildFilterClause($filters);

        $sql = "SELECT
                    COUNT(*) AS total_records,
                    COUNT(DISTINCT p.client_id) AS unique_clients,
                    SUM(CASE WHEN p.status = 'approved' THEN p.amount ELSE 0 END) AS total_collected,
                    SUM(CASE WHEN p.status = 'pending' THEN 1 ELSE 0 END) AS pending_count,
                    SUM(CASE WHEN p.status = 'rejected' THEN 1 ELSE 0 END) AS rejected_count,
                    SUM(CASE WHEN p.status IN ('pending', 'approved') THEN p.remaining_balance ELSE 0 END) AS outstanding_balance
                FROM payments p
                INNER JOIN bookings b ON b.id = p.booking_id
                INNER JOIN clients c ON c.id = p.client_id
                INNER JOIN caretakers ct ON ct.id = p.caretaker_id
                {$where}";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return $this->defaultSummary();
        }

        $this->bindParams($stmt, $types, $params);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc() ?: [];
        $stmt->close();

        return [
            'total_records' => (int)($result['total_records'] ?? 0),
            'unique_clients' => (int)($result['unique_clients'] ?? 0),
            'total_collected' => (float)($result['total_collected'] ?? 0),
            'pending_count' => (int)($result['pending_count'] ?? 0),
            'rejected_count' => (int)($result['rejected_count'] ?? 0),
            'outstanding_balance' => (float)($result['outstanding_balance'] ?? 0),
        ];
    }

    public function getPayments(array $filters = [], int $limit = 20, int $offset = 0): array
    {
        [$where, $types, $params] = $this->buildFilterClause($filters);

        $sql = "SELECT
                    p.id AS payment_id,
                    p.booking_id,
                    p.client_id,
                    p.caretaker_id,
                    p.total_booking_amount,
                    p.customization_price,
                    p.amount,
                    p.remaining_balance,
                    p.payment_method,
                    p.payment_type,
                    p.status,
                    p.due_date,
                    p.paid_date,
                    p.created_at,
                    p.approved_at,
                    c.name AS client_name,
                    c.email AS client_email,
                    c.phone AS client_phone,
                    ct.name AS caretaker_name,
                    b.service_type,
                    b.basis,
                    b.duration,
                    b.booking_date,
                    b.service_start_date,
                    b.preferred_time,
                    b.status AS booking_status,
                    b.district,
                    b.street,
                    b.address_line1,
                    b.address_line2,
                    b.postal_code,
                    b.customization,
                    b.total_payment AS booking_total_payment
                FROM payments p
                INNER JOIN bookings b ON b.id = p.booking_id
                INNER JOIN clients c ON c.id = p.client_id
                INNER JOIN caretakers ct ON ct.id = p.caretaker_id
                {$where}
                ORDER BY p.created_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $types .= 'ii';
        $params[] = $limit;
        $params[] = $offset;
        $this->bindParams($stmt, $types, $params);

        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }

    public function getPaymentsCount(array $filters = []): int
    {
        [$where, $types, $params] = $this->buildFilterClause($filters);

        $sql = "SELECT COUNT(*) AS total
                FROM payments p
                INNER JOIN bookings b ON b.id = p.booking_id
                INNER JOIN clients c ON c.id = p.client_id
                INNER JOIN caretakers ct ON ct.id = p.caretaker_id
                {$where}";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return 0;
        }

        $this->bindParams($stmt, $types, $params);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return (int)($row['total'] ?? 0);
    }

    public function getPaymentById(int $paymentId): ?array
    {
        $sql = "SELECT
                    p.id AS payment_id,
                    p.booking_id,
                    p.client_id,
                    p.caretaker_id,
                    p.total_booking_amount,
                    p.customization_price,
                    p.amount,
                    p.remaining_balance,
                    p.payment_method,
                    p.payment_type,
                    p.status,
                    p.due_date,
                    p.paid_date,
                    p.created_at,
                    p.approved_at,
                    c.name AS client_name,
                    c.email AS client_email,
                    c.phone AS client_phone,
                    ct.name AS caretaker_name,
                    b.service_type,
                    b.basis,
                    b.duration,
                    b.booking_date,
                    b.service_start_date,
                    b.preferred_time,
                    b.status AS booking_status,
                    b.district,
                    b.street,
                    b.address_line1,
                    b.address_line2,
                    b.postal_code,
                    b.customization,
                    b.total_payment AS booking_total_payment
                FROM payments p
                INNER JOIN bookings b ON b.id = p.booking_id
                INNER JOIN clients c ON c.id = p.client_id
                INNER JOIN caretakers ct ON ct.id = p.caretaker_id
                WHERE p.id = ?
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param('i', $paymentId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    public function getAllPaymentsForExport(array $filters = []): array
    {
        [$where, $types, $params] = $this->buildFilterClause($filters);

        $sql = "SELECT
                    p.id AS payment_id,
                    p.booking_id,
                    c.name AS client_name,
                    ct.name AS caretaker_name,
                    b.service_type,
                    b.basis,
                    p.payment_type,
                    p.payment_method,
                    p.amount,
                    p.remaining_balance,
                    p.status,
                    p.due_date,
                    p.paid_date,
                    p.created_at,
                    b.status AS booking_status
                FROM payments p
                INNER JOIN bookings b ON b.id = p.booking_id
                INNER JOIN clients c ON c.id = p.client_id
                INNER JOIN caretakers ct ON ct.id = p.caretaker_id
                {$where}
                ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $this->bindParams($stmt, $types, $params);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }

    public function getFilterOptions(): array
    {
        return [
            'statuses' => ['pending', 'approved', 'rejected'],
            'payment_types' => ['advance', 'reminder', 'final'],
            'payment_methods' => ['credit_card', 'debit_card', 'mobile_wallet', 'bank_transfer'],
            'booking_statuses' => [
                'Requested',
                'Payment_Requested',
                'Advance_Paid',
                'Accepted',
                'Change_Requested',
                'Rejected',
                'Cancelled',
                'Completed',
                'Reschedule_Requested',
            ],
        ];
    }

    private function buildFilterClause(array $filters): array
    {
        $conditions = ["1=1"];
        $types = '';
        $params = [];

        $search = trim((string)($filters['search'] ?? ''));
        if ($search !== '') {
            $like = '%' . $search . '%';
            $conditions[] = "(c.name LIKE ? OR ct.name LIKE ? OR CAST(p.id AS CHAR) LIKE ? OR CAST(p.booking_id AS CHAR) LIKE ?)";
            $types .= 'ssss';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $status = trim((string)($filters['status'] ?? ''));
        if ($status !== '' && in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $conditions[] = 'p.status = ?';
            $types .= 's';
            $params[] = $status;
        }

        $paymentType = trim((string)($filters['payment_type'] ?? ''));
        if ($paymentType !== '' && in_array($paymentType, ['advance', 'reminder', 'final'], true)) {
            $conditions[] = 'p.payment_type = ?';
            $types .= 's';
            $params[] = $paymentType;
        }

        $paymentMethod = trim((string)($filters['payment_method'] ?? ''));
        if ($paymentMethod !== '' && in_array($paymentMethod, ['credit_card', 'debit_card', 'mobile_wallet', 'bank_transfer'], true)) {
            $conditions[] = 'p.payment_method = ?';
            $types .= 's';
            $params[] = $paymentMethod;
        }

        $bookingStatus = trim((string)($filters['booking_status'] ?? ''));
        if ($bookingStatus !== '' && in_array($bookingStatus, [
            'Requested',
            'Payment_Requested',
            'Advance_Paid',
            'Accepted',
            'Change_Requested',
            'Rejected',
            'Cancelled',
            'Completed',
            'Reschedule_Requested',
        ], true)) {
            $conditions[] = 'b.status = ?';
            $types .= 's';
            $params[] = $bookingStatus;
        }

        $fromDate = trim((string)($filters['from'] ?? ''));
        if ($this->isValidDate($fromDate)) {
            $conditions[] = 'DATE(p.created_at) >= ?';
            $types .= 's';
            $params[] = $fromDate;
        }

        $toDate = trim((string)($filters['to'] ?? ''));
        if ($this->isValidDate($toDate)) {
            $conditions[] = 'DATE(p.created_at) <= ?';
            $types .= 's';
            $params[] = $toDate;
        }

        $where = 'WHERE ' . implode(' AND ', $conditions);

        return [$where, $types, $params];
    }

    private function bindParams(mysqli_stmt $stmt, string $types, array &$params): void
    {
        if ($types === '') {
            return;
        }

        $bindParams = [];
        $bindParams[] = &$types;

        foreach ($params as $index => $value) {
            $bindParams[] = &$params[$index];
        }

        call_user_func_array([$stmt, 'bind_param'], $bindParams);
    }

    private function isValidDate(string $value): bool
    {
        if ($value === '') {
            return false;
        }

        $date = DateTime::createFromFormat('Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value;
    }

    private function defaultSummary(): array
    {
        return [
            'total_records' => 0,
            'unique_clients' => 0,
            'total_collected' => 0.0,
            'pending_count' => 0,
            'rejected_count' => 0,
            'outstanding_balance' => 0.0,
        ];
    }
}
