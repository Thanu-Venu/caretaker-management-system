<?php
require_once APPROOT . '/core/Database.php';
require_once APPROOT . '/core/PasswordPolicy.php';
require_once APPROOT . '/models/AccountModel.php';

class CaretakerModel
{
    private $conn;
    private $accountLinkChecked = false;
    private $accountLinkEnabled = false;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }

    private function hasAccountLinking(): bool
    {
        if ($this->accountLinkChecked) {
            return $this->accountLinkEnabled;
        }

        $tables = $this->conn->query("SHOW TABLES LIKE 'accounts'");
        if (!$tables || $tables->num_rows === 0) {
            $this->accountLinkChecked = true;
            $this->accountLinkEnabled = false;
            return false;
        }

        $cols = $this->conn->query("SHOW COLUMNS FROM caretakers LIKE 'account_id'");
        $this->accountLinkChecked = true;
        $this->accountLinkEnabled = (bool)($cols && $cols->num_rows > 0);
        return $this->accountLinkEnabled;
    }

    /**
     * HR/admin caregiver password policy. Returns a user-facing error message or null if valid.
     */
    public static function validateCaretakerPassword(string $password): ?string
    {
        return PasswordPolicy::validateStrong($password);
    }

    /** @return array */
    public function getCaretakers()
    {
        $result = $this->conn->query("SELECT * FROM caretakers");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Return only caretakers whose status is Active. Used by client-facing
     * listings so that inactive caregivers do not appear in search results.
     */
    public function getActiveCaretakers()
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM caretakers WHERE status = 'Active' ORDER BY name ASC"
        );
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        foreach ($rows as &$row) {
            $row = $this->mergeFeedbackAverageIntoCaretakerRow($row);
        }
        unset($row);
        $this->sortCaretakerRowsByRatingDescThenName($rows);
        return $rows;
    }

    public function getCaretakerById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM caretakers WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Return array of distinct non-empty locations from caretakers table
     * @return array
     */
    public function getDistinctLocations()
    {
        $res = $this->conn->query("SELECT DISTINCT location FROM caretakers WHERE location <> '' ORDER BY location ASC");
        return $res ? array_column($res->fetch_all(MYSQLI_ASSOC), 'location') : [];
    }

    /**
     * Return array of distinct locations for a given service type
     * @param string $service
     * @return array
     */
    public function getLocationsByService(string $service): array
    {
        $stmt = $this->conn->prepare("SELECT DISTINCT location FROM caretakers WHERE service_type = ? AND location <> '' ORDER BY location ASC");
        $stmt->bind_param("s", $service);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? array_column($res->fetch_all(MYSQLI_ASSOC), 'location') : [];
    }



    public function addCaretaker($data)
    {
        // Ensure all expected keys exist to avoid binding nulls for NOT NULL columns
        $expected = ['name', 'email', 'phone', 'service_type', 'status', 'experience', 'location', 'qualifications', 'profile_image', 'password'];
        foreach ($expected as $key) {
            if (!array_key_exists($key, $data) || $data[$key] === null) {
                $data[$key] = '';
            }
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        if (!$this->hasAccountLinking()) {
            $stmt = $this->conn->prepare(
                "INSERT INTO caretakers (name, email, phone, service_type, status, experience, location, qualifications, profile_image, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssssssssss",
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['service_type'],
                $data['status'],
                $data['experience'],
                $data['location'],
                $data['qualifications'],
                $data['profile_image'],
                $hashedPassword
            );

            return $stmt->execute();
        }

        $this->conn->begin_transaction();
        try {
            $role = 'caretaker';
            $status = $data['status'] === 'Inactive' ? 'Inactive' : 'Active';
            $stmt = $this->conn->prepare(
                "INSERT INTO accounts (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sssss", $data['name'], $data['email'], $hashedPassword, $role, $status);
            if (!$stmt->execute()) {
                throw new Exception('Failed to create caretaker account');
            }
            $accountId = (int)$this->conn->insert_id;
            $stmt->close();

            $stmt = $this->conn->prepare(
                "INSERT INTO caretakers (account_id, name, email, phone, service_type, status, experience, location, qualifications, profile_image, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "issssssssss",
                $accountId,
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['service_type'],
                $data['status'],
                $data['experience'],
                $data['location'],
                $data['qualifications'],
                $data['profile_image'],
                $hashedPassword
            );

            if (!$stmt->execute()) {
                throw new Exception('Failed to create caretaker profile');
            }
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (Throwable $e) {
            $this->conn->rollback();
            error_log('CaretakerModel::addCaretaker transaction failed: ' . $e->getMessage());
            return false;
        }
    }

    private $timeMap = [
        "Morning (8am - 12pm)" => ["08:00:00", "12:00:00"],
        "Evening (1pm - 5pm)"  => ["13:00:00", "17:00:00"],
        "Night (6pm - 10pm)"   => ["18:00:00", "22:00:00"],
        "Full Time (8am - 5pm)" => ["08:00:00", "17:00:00"]
    ];





    public function updateCaretaker($id, $data, $profileImage = null)
    {
        if ($profileImage) {
            // profile image update included
            $stmt = $this->conn->prepare(
                "UPDATE caretakers
             SET name=?, email=?, phone=?, experience=?, location=?, qualifications=?, service_type=?, status=?, profile_image=?
             WHERE id=?"
            );

            $stmt->bind_param(
                "sssssssssi",
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['experience'],
                $data['location'],
                $data['qualifications'],
                $data['service_type'],
                $data['status'],
                $profileImage,
                $id
            );
        } else {
            // without changing profile image
            $stmt = $this->conn->prepare(
                "UPDATE caretakers
             SET name=?, email=?, phone=?, experience=?, location=?, qualifications=?, service_type=?, status=?
             WHERE id=?"
            );

            $stmt->bind_param(
                "ssssssssi",
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['experience'],
                $data['location'],
                $data['qualifications'],
                $data['service_type'],
                $data['status'],
                $id
            );
        }

        return $stmt->execute();
    }


    public function updateCaretakerDetails($id, $data)
    {
        $stmt = $this->conn->prepare("UPDATE caretakers SET name=?,email=?,phone=? WHERE id=?");
        $stmt->bind_param("sssi", $data['name'], $data['email'], $data['phone'], $id);
        $ok = $stmt->execute();

        if ($ok && $this->hasAccountLinking()) {
            $ct = $this->getCaretakerById($id);
            $accountId = (int)($ct['account_id'] ?? 0);
            if ($accountId > 0) {
                $role = 'caretaker';
                $status = $ct['status'] === 'Inactive' ? 'Inactive' : 'Active';
                $sync = $this->conn->prepare("UPDATE accounts SET name=?, email=?, role=?, status=? WHERE id=?");
                $sync->bind_param("ssssi", $data['name'], $data['email'], $role, $status, $accountId);
                $sync->execute();
                $sync->close();
            }
        }

        return $ok;
    }

    public function deleteCaretaker($id)
    {
        if (!$this->hasAccountLinking()) {
            $stmt = $this->conn->prepare("DELETE FROM caretakers WHERE id=?");
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        }

        $ct = $this->getCaretakerById($id);
        if (!$ct) {
            return false;
        }

        $this->conn->begin_transaction();
        try {
            $stmt = $this->conn->prepare("DELETE FROM caretakers WHERE id=?");
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete caretaker profile');
            }
            $stmt->close();

            $accountId = (int)($ct['account_id'] ?? 0);
            if ($accountId > 0) {
                $stmt = $this->conn->prepare("DELETE FROM accounts WHERE id=?");
                $stmt->bind_param("i", $accountId);
                if (!$stmt->execute()) {
                    throw new Exception('Failed to delete caretaker account');
                }
                $stmt->close();
            }

            $this->conn->commit();
            return true;
        } catch (Throwable $e) {
            $this->conn->rollback();
            error_log('CaretakerModel::deleteCaretaker transaction failed: ' . $e->getMessage());
            return false;
        }
    }




    public function updateProfileCaretaker($id, $data)
    {

        $stmt = $this->conn->prepare(
            "UPDATE caretakers SET
                name = ?,
                email = ?,
                phone = ?,
                experience = ?,
                qualifications = ?,
                profile_image = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "ssssssi",
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['experience'],
            $data['qualifications'],
            $data['profile_image'],
            $id
        );

        return $stmt->execute();
    }

    public function updateCaretakerPassword($id, $hashedPassword)
    {

        $stmt = $this->conn->prepare(
            "UPDATE caretakers
             SET password = ?
             WHERE id = ?"
        );

        $stmt->bind_param("si", $hashedPassword, $id);

        $ok = $stmt->execute();

        if ($ok && $this->hasAccountLinking()) {
            $ct = $this->getCaretakerById($id);
            $accountId = (int)($ct['account_id'] ?? 0);
            if ($accountId > 0) {
                $sync = $this->conn->prepare("UPDATE accounts SET password=? WHERE id=?");
                $sync->bind_param("si", $hashedPassword, $accountId);
                $sync->execute();
                $sync->close();
            }
        }

        return $ok;
    }

    public function updateAvailabilityStatus($id, $status)
    {
        $stmt = $this->conn->prepare("UPDATE caretakers SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    public function getAvailableCaretakers($service, $date, $preferredTime, $basis, $duration, $location = '', $excludeBookingId = null)
    {
        $startDate = $date;
        $duration = max(1, (int)$duration);
        $normalizedBasis = strtolower(trim((string)$basis));

        if ($normalizedBasis === 'hourly') {
            $endDate = $date; // hourly bookings only block the same day
        } elseif ($normalizedBasis === 'monthly') {
            $endDate = date('Y-m-d', strtotime('+' . $duration . ' month -1 day', strtotime($date)));
        } elseif ($normalizedBasis === 'yearly') {
            $endDate = date('Y-m-d', strtotime('+' . $duration . ' year -1 day', strtotime($date)));
        } else {
            $endDate = date('Y-m-d', strtotime('+' . ($duration - 1) . ' days', strtotime($date)));
        }

        // base SQL
        $sql = "
SELECT c.*
FROM caretakers c
WHERE c.service_type = ?
  AND c.status = 'Active'
";
        // build type string and value list
        $types = "s"; // service
        $values = [$service];

        if ($location !== '') {
            $sql .= " AND c.location = ?";
            $types .= "s";
            $values[] = $location;
        }
        // only consider active caretakers
        // (status is usually 'Active' or 'Inactive' in the dump)
        // the earlier WHERE clause already includes this, but keep comments
        // for clarity if additional filters are added later.


        $sql .= "
AND NOT EXISTS (
    SELECT 1 FROM bookings b
    WHERE b.caretaker_id = c.id
      AND LOWER(b.status) IN (
        'requested','payment_requested','advance_paid',
        'accepted','approved','change_requested','reschedule_requested'
      )
      AND COALESCE(b.service_start_date, b.booking_date) <= ?
    AND (
        CASE
            WHEN LOWER(b.basis) = 'hourly' THEN COALESCE(b.service_start_date, b.booking_date)
            WHEN LOWER(b.basis) = 'monthly' THEN DATE_SUB(DATE_ADD(COALESCE(b.service_start_date, b.booking_date), INTERVAL GREATEST(b.duration, 1) MONTH), INTERVAL 1 DAY)
            WHEN LOWER(b.basis) = 'yearly' THEN DATE_SUB(DATE_ADD(COALESCE(b.service_start_date, b.booking_date), INTERVAL GREATEST(b.duration, 1) YEAR), INTERVAL 1 DAY)
            ELSE DATE_SUB(DATE_ADD(COALESCE(b.service_start_date, b.booking_date), INTERVAL GREATEST(b.duration, 1) DAY), INTERVAL 1 DAY)
        END
    ) >= ?";

        // Add exclusion for current booking if rescheduling
        if ($excludeBookingId !== null) {
            $sql .= " AND b.id != ?";
        }

        $sql .= ")";

        // Leave replacement cover: caregiver is busy as new_caretaker_id for overlapping dates/slots
        $sql .= "
AND NOT EXISTS (
    SELECT 1 FROM booking_reassignments br
    INNER JOIN bookings rb ON rb.id = br.booking_id
    WHERE br.new_caretaker_id = c.id
      AND LOWER(rb.status) IN (
        'requested','payment_requested','advance_paid',
        'accepted','approved','change_requested','reschedule_requested'
      )
      AND br.start_date <= ?
      AND br.end_date >= ?";

        if ($excludeBookingId !== null) {
            $sql .= "
      AND br.booking_id != ?";
        }

        $sql .= "
)";

        // prepare statement
        $stmt = $this->conn->prepare($sql);

        // Date overlap: existing_start <= requested_end AND existing_end >= requested_start.
        // Hourly bookings occupy the full calendar day (matches HH:MM preferred_time in DB).
        $types .= "ss";
        $values = array_merge($values, [$endDate, $startDate]);

        // Add exclusion param if rescheduling
        if ($excludeBookingId !== null) {
            $types .= "i";
            $values[] = $excludeBookingId;
        }

        $types .= "ss";
        $values = array_merge($values, [$endDate, $startDate]);

        if ($excludeBookingId !== null) {
            $types .= "i";
            $values[] = $excludeBookingId;
        }

        // bind all parameters (should match types length)
        $stmt->bind_param($types, ...$values);

        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        foreach ($rows as &$row) {
            $row = $this->mergeFeedbackAverageIntoCaretakerRow($row);
        }
        unset($row);
        return $rows;
    }

    /**
     * Client-facing listings use caretakers.rating, but feedback stars are stored in feedbacks.
     * When at least one client rating exists, expose its average as rating for browse/profile views.
     *
     * @param array $row Single caretaker row from the caretakers table
     * @return array
     */
    public function mergeFeedbackAverageIntoCaretakerRow(array $row): array
    {
        $id = (int) ($row['id'] ?? 0);
        if ($id <= 0) {
            return $row;
        }
        $avg = $this->getAverageRating($id);
        if ($avg > 0) {
            $row['rating'] = round($avg, 1);
        }
        return $row;
    }

    /** @param array<int, array<string, mixed>> $rows */
    private function sortCaretakerRowsByRatingDescThenName(array &$rows): void
    {
        usort($rows, static function (array $a, array $b): int {
            $ra = isset($a['rating']) && $a['rating'] !== '' && $a['rating'] !== null ? (float) $a['rating'] : null;
            $rb = isset($b['rating']) && $b['rating'] !== '' && $b['rating'] !== null ? (float) $b['rating'] : null;
            if ($ra === null && $rb === null) {
                return strcasecmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
            }
            if ($ra === null) {
                return 1;
            }
            if ($rb === null) {
                return -1;
            }
            if ((int) ($ra * 10) !== (int) ($rb * 10)) {
                return $rb <=> $ra;
            }

            return strcasecmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
        });
    }




    // Upcoming bookings
    // Get Upcoming Bookings for Caretaker
    public function getUpcomingBookings($caretakerId)
    {
        $updateSql = "
        UPDATE bookings
        SET status = 'Completed'
        WHERE caretaker_id = ?
          AND (
                CASE
                    WHEN LOWER(basis) = 'hourly' THEN booking_date
                    WHEN LOWER(basis) = 'monthly' THEN DATE_SUB(DATE_ADD(booking_date, INTERVAL duration MONTH), INTERVAL 1 DAY)
                    WHEN LOWER(basis) = 'yearly' THEN DATE_SUB(DATE_ADD(booking_date, INTERVAL duration YEAR), INTERVAL 1 DAY)
                    ELSE DATE_SUB(DATE_ADD(booking_date, INTERVAL duration DAY), INTERVAL 1 DAY)
                END
              ) < CURDATE()
          AND status = 'Accepted'
    ";
        $updateStmt = $this->conn->prepare($updateSql);
        $updateStmt->bind_param("i", $caretakerId);
        $updateStmt->execute();
        $updateStmt->close();

        $sql = "SELECT
                b.id AS booking_id,
                b.booking_date,
                b.preferred_time,
                b.basis,
                b.duration,
                b.service_type,
                b.status,
                CONCAT(
                    b.district, ', ',
                    b.street, ', ',
                    b.address_line1, ', ',
                    b.address_line2, ', ',
                    b.postal_code
                ) AS service_location,
                c.name AS client_name
            FROM bookings b
            JOIN clients c ON c.id = b.client_id
            WHERE b.caretaker_id = ? 
              AND b.status IN ('Accepted', 'Payment_Requested', 'Advance_Paid')
              AND b.booking_date > CURDATE()
            ORDER BY b.booking_date ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $caretakerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get Ongoing Bookings for Caretaker (active today)
    public function getOngoingBookings($caretakerId)
    {
        $sql = "SELECT
                b.id AS booking_id,
                b.booking_date,
                b.preferred_time,
                b.basis,
                b.duration,
                b.service_type,
                CONCAT(
                    b.district, ', ',
                    b.street, ', ',
                    b.address_line1, ', ',
                    b.address_line2, ', ',
                    b.postal_code
                ) AS service_location,
                c.name AS client_name
            FROM bookings b
            JOIN clients c ON c.id = b.client_id
            WHERE b.caretaker_id = ?
              AND b.status = 'Accepted'
              AND b.booking_date <= CURDATE()
              AND (
                    CASE
                        WHEN LOWER(b.basis) = 'hourly' THEN b.booking_date
                        WHEN LOWER(b.basis) = 'monthly' THEN DATE_SUB(DATE_ADD(b.booking_date, INTERVAL b.duration MONTH), INTERVAL 1 DAY)
                        WHEN LOWER(b.basis) = 'yearly' THEN DATE_SUB(DATE_ADD(b.booking_date, INTERVAL b.duration YEAR), INTERVAL 1 DAY)
                        ELSE DATE_SUB(DATE_ADD(b.booking_date, INTERVAL b.duration DAY), INTERVAL 1 DAY)
                    END
                  ) >= CURDATE()
            ORDER BY b.booking_date ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $caretakerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get All Active Bookings for Schedule Calendar
    public function getAllActiveBookings($caretakerId)
    {
        // Update old accepted bookings to completed first
        $updateSql = "
        UPDATE bookings
        SET status = 'Completed'
        WHERE caretaker_id = ?
                    AND (
                                CASE
                                        WHEN LOWER(basis) = 'hourly' THEN booking_date
                                        WHEN LOWER(basis) = 'monthly' THEN DATE_SUB(DATE_ADD(booking_date, INTERVAL duration MONTH), INTERVAL 1 DAY)
                                        WHEN LOWER(basis) = 'yearly' THEN DATE_SUB(DATE_ADD(booking_date, INTERVAL duration YEAR), INTERVAL 1 DAY)
                                        ELSE DATE_SUB(DATE_ADD(booking_date, INTERVAL duration DAY), INTERVAL 1 DAY)
                                END
                            ) < CURDATE()
          AND status = 'Accepted'
    ";
        $updateStmt = $this->conn->prepare($updateSql);
        $updateStmt->bind_param("i", $caretakerId);
        $updateStmt->execute();
        $updateStmt->close();

        // Get only bookings that caretaker should see in schedule (from assignment until completion)
        $sql = "SELECT
                b.id AS booking_id,
                b.booking_date,
                b.preferred_time,
                b.basis,
                b.duration,
                b.service_type,
                b.status,
                CONCAT(
                    b.district, ', ',
                    b.street, ', ',
                    b.address_line1, ', ',
                    b.address_line2, ', ',
                    b.postal_code
                ) AS service_location,
                c.name AS client_name
            FROM bookings b
            JOIN clients c ON c.id = b.client_id
            WHERE b.caretaker_id = ?
            AND b.status IN ('Payment_Requested', 'Advance_Paid', 'Accepted', 'Completed')
            ORDER BY b.booking_date ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $caretakerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get Past Bookings for Caretaker
    public function getPastBookings($caretakerId)
    {
        $sql = "SELECT
                b.id AS booking_id,
                b.booking_date,
                b.preferred_time,
                b.basis,
                b.duration,
                b.service_type,
                CONCAT(
                    b.district, ', ',
                    b.street, ', ',
                    b.address_line1, ', ',
                    b.address_line2, ', ',
                    b.postal_code
                ) AS service_location,
                c.name AS client_name
            FROM bookings b
            JOIN clients c ON c.id = b.client_id
            WHERE b.caretaker_id = ? AND b.status = 'Completed' AND b.booking_date < CURDATE()
            ORDER BY b.booking_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $caretakerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Bookings assigned to this caretaker that were cancelled */
    public function getCancelledBookingsForCaretaker(int $caretakerId): array
    {
        if ($caretakerId <= 0) {
            return [];
        }

        $sql = "SELECT
                b.id AS booking_id,
                b.booking_date,
                b.preferred_time,
                b.basis,
                b.duration,
                b.service_type,
                b.status,
                b.cancellation_reason,
                b.cancelled_at,
                CONCAT(
                    b.district, ', ',
                    b.street, ', ',
                    b.address_line1, ', ',
                    b.address_line2, ', ',
                    b.postal_code
                ) AS service_location,
                c.name AS client_name
            FROM bookings b
            JOIN clients c ON c.id = b.client_id
            WHERE b.caretaker_id = ?
              AND LOWER(TRIM(b.status)) = 'cancelled'
            ORDER BY COALESCE(b.cancelled_at, b.booking_date) DESC, b.booking_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $caretakerId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Leave-approved replacement windows: rows where this caregiver is new_caretaker_id.
     * Dates cover_start_date / cover_end_date are the reassignment overlap only (not full booking span).
     */
    public function getReplacementCoverAssignments(int $caretakerId): array
    {
        if ($caretakerId <= 0) {
            return [];
        }

        $sql = "SELECT
                b.id AS booking_id,
                br.id AS reassignment_id,
                br.start_date AS cover_start_date,
                br.end_date AS cover_end_date,
                br.start_date AS booking_date,
                b.preferred_time,
                b.basis,
                b.duration,
                b.service_type,
                b.status AS booking_status,
                CONCAT(
                    b.district, ', ',
                    b.street, ', ',
                    b.address_line1, ', ',
                    b.address_line2, ', ',
                    b.postal_code
                ) AS service_location,
                c.name AS client_name,
                oc.name AS covered_for_caretaker_name,
                1 AS is_replacement_cover
            FROM booking_reassignments br
            INNER JOIN bookings b ON b.id = br.booking_id
            INNER JOIN clients c ON c.id = b.client_id
            LEFT JOIN caretakers oc ON oc.id = br.old_caretaker_id
            WHERE br.new_caretaker_id = ?
              AND b.status NOT IN ('Rejected', 'Cancelled')
            ORDER BY br.start_date ASC, br.id ASC";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('i', $caretakerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Split replacement cover rows into ongoing / upcoming / past by cover dates vs today.
     *
     * @return array{ongoing: array, upcoming: array, past: array}
     */
    public function getReplacementCoverAssignmentsByTab(int $caretakerId): array
    {
        $today = date('Y-m-d');
        $ongoing = [];
        $upcoming = [];
        $past = [];

        foreach ($this->getReplacementCoverAssignments($caretakerId) as $row) {
            $s = (string) ($row['cover_start_date'] ?? '');
            $e = (string) ($row['cover_end_date'] ?? '');
            if ($s === '' || $e === '') {
                continue;
            }
            if ($e < $today) {
                $past[] = $row;
            } elseif ($s > $today) {
                $upcoming[] = $row;
            } else {
                $ongoing[] = $row;
            }
        }

        return ['ongoing' => $ongoing, 'upcoming' => $upcoming, 'past' => $past];
    }

    // Get approved bookings with client details
    public function getApprovedBookingsWithClientDetails($caretakerId)
    {
        $sql = "SELECT
                b.id AS booking_id,
                b.booking_date,
                b.preferred_time,
                b.basis,
                b.duration,
                b.service_type,
                b.status,
                b.district,
                b.street,
                b.address_line1,
                b.address_line2,
                b.postal_code,
                c.id AS client_id,
                c.name AS client_name,
                c.phone AS client_phone,
                c.email AS client_email
            FROM bookings b
            JOIN clients c ON b.client_id = c.id
            WHERE b.caretaker_id = ? AND b.status = 'Approved'
            ORDER BY b.booking_date ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $caretakerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }






    public function login($email, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM caretakers WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $caretaker = $stmt->get_result()->fetch_assoc();
        if ($caretaker && password_verify($password, $caretaker['password'])) {
            return $caretaker;
        }
        return false;
    }
public function getClients($caretaker_id)
{
    $stmt = $this->conn->prepare(
        "SELECT 
            clients.id AS client_id, 
            clients.name AS client_name,
            bookings.id AS booking_id,
            bookings.booking_date,
            bookings.preferred_time,
            bookings.service_type,
            bookings.basis,
            bookings.duration,
            CASE
                WHEN bookings.basis = 'Hourly' THEN bookings.booking_date
                WHEN bookings.basis = 'Daily' THEN DATE_ADD(bookings.booking_date, INTERVAL (GREATEST(bookings.duration, 1) - 1) DAY)
                WHEN bookings.basis = 'Monthly' THEN DATE_SUB(DATE_ADD(bookings.booking_date, INTERVAL GREATEST(bookings.duration, 1) MONTH), INTERVAL 1 DAY)
                WHEN bookings.basis = 'Yearly' THEN DATE_SUB(DATE_ADD(bookings.booking_date, INTERVAL GREATEST(bookings.duration, 1) YEAR), INTERVAL 1 DAY)
                ELSE bookings.booking_date
            END AS booking_end_date
         FROM bookings
         JOIN clients ON bookings.client_id = clients.id
         WHERE bookings.caretaker_id = ?
         AND bookings.status IN ('Accepted', 'Advance_Paid', 'Change_Requested', 'Reschedule_Requested')
         AND CURDATE() BETWEEN bookings.booking_date AND (
            CASE
                WHEN bookings.basis = 'Hourly' THEN bookings.booking_date
                WHEN bookings.basis = 'Daily' THEN DATE_ADD(bookings.booking_date, INTERVAL (GREATEST(bookings.duration, 1) - 1) DAY)
                WHEN bookings.basis = 'Monthly' THEN DATE_SUB(DATE_ADD(bookings.booking_date, INTERVAL GREATEST(bookings.duration, 1) MONTH), INTERVAL 1 DAY)
                WHEN bookings.basis = 'Yearly' THEN DATE_SUB(DATE_ADD(bookings.booking_date, INTERVAL GREATEST(bookings.duration, 1) YEAR), INTERVAL 1 DAY)
                ELSE bookings.booking_date
            END
         )"
    );

    $stmt->bind_param("i", $caretaker_id);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

    /**
     * Active booking for caregiver complaint (must match getClients eligibility rules).
     *
     * @return array<string, mixed>|null
     */
    public function getActiveBookingForCaretakerComplaint(int $caretakerId, int $bookingId): ?array
    {
        if ($caretakerId <= 0 || $bookingId <= 0) {
            return null;
        }

        $sql = "SELECT
            bookings.id AS booking_id,
            clients.id AS client_id,
            clients.name AS client_name,
            bookings.booking_date,
            bookings.basis,
            bookings.duration,
            CASE
                WHEN bookings.basis = 'Hourly' THEN bookings.booking_date
                WHEN bookings.basis = 'Daily' THEN DATE_ADD(bookings.booking_date, INTERVAL (GREATEST(bookings.duration, 1) - 1) DAY)
                WHEN bookings.basis = 'Monthly' THEN DATE_SUB(DATE_ADD(bookings.booking_date, INTERVAL GREATEST(bookings.duration, 1) MONTH), INTERVAL 1 DAY)
                WHEN bookings.basis = 'Yearly' THEN DATE_SUB(DATE_ADD(bookings.booking_date, INTERVAL GREATEST(bookings.duration, 1) YEAR), INTERVAL 1 DAY)
                ELSE bookings.booking_date
            END AS booking_end_date
         FROM bookings
         JOIN clients ON bookings.client_id = clients.id
         WHERE bookings.id = ?
           AND bookings.caretaker_id = ?
           AND bookings.status IN ('Accepted', 'Advance_Paid', 'Change_Requested', 'Reschedule_Requested')
           AND CURDATE() BETWEEN bookings.booking_date AND (
            CASE
                WHEN bookings.basis = 'Hourly' THEN bookings.booking_date
                WHEN bookings.basis = 'Daily' THEN DATE_ADD(bookings.booking_date, INTERVAL (GREATEST(bookings.duration, 1) - 1) DAY)
                WHEN bookings.basis = 'Monthly' THEN DATE_SUB(DATE_ADD(bookings.booking_date, INTERVAL GREATEST(bookings.duration, 1) MONTH), INTERVAL 1 DAY)
                WHEN bookings.basis = 'Yearly' THEN DATE_SUB(DATE_ADD(bookings.booking_date, INTERVAL GREATEST(bookings.duration, 1) YEAR), INTERVAL 1 DAY)
                ELSE bookings.booking_date
            END
         )";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param("ii", $bookingId, $caretakerId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }

    public static function complaintServiceDateInBookingRange(string $serviceDate, string $bookingStart, string $bookingEnd): bool
    {
        if ($serviceDate === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $serviceDate)) {
            return false;
        }
        if ($bookingStart === '' || $bookingEnd === '') {
            return false;
        }

        return $serviceDate >= $bookingStart && $serviceDate <= $bookingEnd;
    }

    public function addComplaint($data)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO ct_complaints (client_id, caretaker_id, service_type, service_date, description, status)
         VALUES (?, ?, ?, ?, ?, 'Open')"
        );

        $stmt->bind_param(
            "iisss",
            $data['client_id'],
            $data['caretaker_id'],
            $data['service_type'],
            $data['service_date'],
            $data['description']
        );

        return $stmt->execute();
    }
public function getComplaintsByCaretaker($caretaker_id)
{
    $stmt = $this->conn->prepare(
        "SELECT ct_complaints.*, clients.name AS client_name
         FROM ct_complaints
         JOIN clients ON ct_complaints.client_id = clients.id
         WHERE ct_complaints.caretaker_id = ?
         ORDER BY ct_complaints.complaint_id DESC, ct_complaints.service_date DESC"
    );

    if (!$stmt) {
        return [];
    }

    $stmt->bind_param("i", $caretaker_id);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

    public function getCaretakerFeedbacks($caretakerId)
    {
        $sql = "SELECT
                cl.name AS client_name,
                b.service_type AS service,
                f.rating,
                f.feedback AS comment,
                f.created_at
            FROM feedbacks f
            JOIN clients cl ON f.client_id = cl.id
            JOIN bookings b ON f.booking_id = b.id
            WHERE f.caretaker_id = ?
            ORDER BY f.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $caretakerId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAverageRating($caretakerId)
    {
        $stmt = $this->conn->prepare(
            "SELECT AVG(rating) AS avg_rating FROM feedbacks WHERE caretaker_id = ? AND rating > 0"
        );
        $stmt->bind_param("i", $caretakerId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res && $res['avg_rating'] !== null ? (float)$res['avg_rating'] : 0.0;
    }

    /**
     * Caregiver counts by status (admin dashboard).
     *
     * @return array{labels: string[], values: int[]}
     */
    public function getCaretakerStatusDistribution(): array
    {
        $res = $this->conn->query(
            "SELECT COALESCE(NULLIF(TRIM(status), ''), 'Unknown') AS st, COUNT(*) AS cnt FROM caretakers GROUP BY st ORDER BY cnt DESC"
        );
        if (!$res) {
            return ['labels' => [], 'values' => []];
        }
        $labels = [];
        $values = [];
        while ($row = $res->fetch_assoc()) {
            $labels[] = (string) $row['st'];
            $values[] = (int) $row['cnt'];
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function countCaretakers(string $search = ''): int
    {
        if ($search !== '') {
            $like = "%" . $search . "%";
            $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total
            FROM caretakers
            WHERE name LIKE ? OR service_type LIKE ? OR status LIKE ?
        ");
            $stmt->bind_param("sss", $like, $like, $like);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            return (int)($row['total'] ?? 0);
        }

        $res = $this->conn->query("SELECT COUNT(*) AS total FROM caretakers");
        $row = $res ? $res->fetch_assoc() : null;
        return (int)($row['total'] ?? 0);
    }

    public function getCaretakersPaginated(int $limit, int $offset, string $search = ''): array
    {
        if ($search !== '') {
            $like = "%" . $search . "%";
            $stmt = $this->conn->prepare("
            SELECT *
            FROM caretakers
            WHERE name LIKE ? OR service_type LIKE ? OR status LIKE ?
            ORDER BY id DESC
            LIMIT ? OFFSET ?
        ");
            $stmt->bind_param("sssii", $like, $like, $like, $limit, $offset);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        $stmt = $this->conn->prepare("
        SELECT *
        FROM caretakers
        ORDER BY id DESC
        LIMIT ? OFFSET ?
    ");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getCaretakersFiltered(array $filters, int $limit, int $offset): array
    {
        $sql = "SELECT id, name, service_type, status, location, email, phone, experience, qualifications, profile_image, created_at
            FROM caretakers
            WHERE 1=1";
        $types = "";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND status = ?";
            $types .= "s";
            $params[] = $filters['status'];
        }

        if (!empty($filters['service_type'])) {
            $sql .= " AND service_type = ?";
            $types .= "s";
            $params[] = $filters['service_type'];
        }

        if (!empty($filters['location'])) {
            $sql .= " AND LOWER(location) LIKE ?";
            $types .= "s";
            $params[] = "%" . strtolower($filters['location']) . "%";
        }

        if (!empty($filters['q'])) {
            $sql .= " AND LOWER(name) LIKE ?";
            $types .= "s";
            $params[] = "%" . strtolower($filters['q']) . "%";
        }

        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $types .= "ii";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function countCaretakersFiltered(array $filters): int
    {
        $sql = "SELECT COUNT(*) AS total
            FROM caretakers
            WHERE 1=1";
        $types = "";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND status = ?";
            $types .= "s";
            $params[] = $filters['status'];
        }

        if (!empty($filters['service_type'])) {
            $sql .= " AND service_type = ?";
            $types .= "s";
            $params[] = $filters['service_type'];
        }

        if (!empty($filters['location'])) {
            $sql .= " AND LOWER(location) LIKE ?";
            $types .= "s";
            $params[] = "%" . strtolower($filters['location']) . "%";
        }

        if (!empty($filters['q'])) {
            $sql .= " AND LOWER(name) LIKE ?";
            $types .= "s";
            $params[] = "%" . strtolower($filters['q']) . "%";
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    public function getServiceReport($caretakerId)
    {
        $sql = "SELECT
                b.id AS booking_id,
                c.name AS client_name,
                b.service_type,
                b.booking_date,
                b.duration
            FROM bookings b
            JOIN clients c ON b.client_id = c.id
            WHERE b.caretaker_id = ?
            ORDER BY b.booking_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $caretakerId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Fetch bookings for FullCalendar
    public function getScheduleByCaretaker($caretakerId)
    {
        // Select additional fields so the frontend modal can show payment/location/duration
        $sql = "SELECT
                    b.id AS booking_id,
                    c.name AS client_name,
                    b.service_type,
                    b.booking_date,
                    b.preferred_time,
                    b.duration,
                    b.service_location,
                    b.status
                FROM bookings b
                JOIN clients c ON b.client_id = c.id
                WHERE b.caretaker_id = ?
                ORDER BY b.booking_date ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $caretakerId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $events = [];
        foreach ($result as $row) {
            // Use date-only start so events with non-ISO preferred_time still show on the calendar
            $events[] = [
                'id' => $row['booking_id'],
                'title' => $row['client_name'] . ' - ' . $row['service_type'],
                'start' => $row['booking_date'],
                'allDay' => true,
                'extendedProps' => [
                    'client' => $row['client_name'],
                    'service' => $row['service_type'],
                    'time' => $row['preferred_time'],
                    'duration' => $row['duration'],
                    'location' => $row['service_location'],
                    'status' => $row['status']
                ]
            ];
        }

        return $events;
    }
}
