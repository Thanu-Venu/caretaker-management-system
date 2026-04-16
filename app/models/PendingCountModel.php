<?php

require_once APPROOT . '/core/Database.php';

/**
 * PendingCountModel
 *
 * Centralized model for fetching pending item counts for sidebar badges.
 * This model provides role-based badge counts for all dashboards.
 *
 * @package SmartCare
 * @subpackage Models
 */

class PendingCountModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->conn;
    }

    /**
     * Get all pending counts for Admin dashboard
     *
     * @return array Associative array of pending counts
     */
    public function getAdminPendingCounts()
    {
        $counts = [];

        // Pending/Requested Bookings
        $counts['bookings'] = $this->getPendingBookingsCount();

        // Pending Payments (awaiting HR approval)
        $counts['payments'] = $this->getPendingPaymentsCount();

        // Pending Leave Requests
        $counts['leave_requests'] = $this->getPendingLeaveCount();

        // Pending Profile Requests (caretaker profile changes)
        $counts['profile_requests'] = $this->getPendingProfileRequestsCount();

        // Unresolved Complaints
        $counts['complaints'] = $this->getUnresolvedComplaintsCount();

        // Pending Reschedule Requests
        $counts['reschedule_requests'] = $this->getPendingRescheduleCount();

        return $counts;
    }

    /**
     * Get all pending counts for HR dashboard
     *
     * @return array Associative array of pending counts
     */
    public function getHRPendingCounts()
    {
        $counts = [];

        // Pending Bookings
        $counts['bookings'] = $this->getPendingBookingsCount();

        // Pending Payments
        $counts['payments'] = $this->getPendingPaymentsCount();

        // Pending Leave Requests
        $counts['leave_requests'] = $this->getPendingLeaveCount();

        // Pending Caretaker Requests (if any verification pending)
        $counts['caretaker_requests'] = $this->getPendingCaretakerApprovalCount();

        // Unresolved Complaints
        $counts['complaints'] = $this->getUnresolvedComplaintsCount();

        // Pending Change Requests (caretaker change)
        $counts['change_requests'] = $this->getPendingChangeRequestsCount();

        // Pending Reschedule Requests
        $counts['reschedule_requests'] = $this->getPendingRescheduleCount();

        return $counts;
    }

    /**
     * Get all pending counts for Client dashboard
     *
     * @param int $clientId Client ID
     * @return array Associative array of pending counts
     */
    public function getClientPendingCounts($clientId)
    {
        $counts = [];

        // Bookings awaiting payment or confirmation
        $counts['bookings'] = $this->getClientPendingBookingsCount($clientId);

        // Pending Payments (payments user needs to make)
        $counts['payments'] = $this->getClientPendingPaymentsCount($clientId);

        // Pending Reschedule Requests
        $counts['reschedule_requests'] = $this->getClientPendingRescheduleCount($clientId);

        // Unread notifications
        $counts['notifications'] = $this->getUnreadNotificationsCount((int)$clientId, 'client');

        return $counts;
    }

    /**
     * Count unread notifications for a specific user role.
     *
     * @param int $userId User profile ID
     * @param string $role Notification role key
     * @return int Count of unread notifications
     */
    public function getUnreadNotificationsCount($userId, $role)
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as count
             FROM notifications
             WHERE user_id = ? AND user_role = ? AND is_read = 0"
        );

        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param('is', $userId, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return (int) ($row['count'] ?? 0);
    }

    /**
     * Get all pending counts for Caretaker dashboard
     *
     * @param int $caretakerId Caretaker ID
     * @return array Associative array of pending counts
     */
    public function getCaretakerPendingCounts($caretakerId)
    {
        $counts = [];

        // New bookings assigned to this caretaker (Accepted status waiting to start)
        $counts['bookings'] = $this->getCaretakerNewBookingsCount($caretakerId);

        // Pending Leave Requests
        $counts['leave_requests'] = $this->getCaretakerPendingLeaveCount($caretakerId);

        return $counts;
    }

    // ==================== Individual Count Methods ====================

    /**
     * Count bookings with status 'Requested' or 'Payment_Requested'
     *
     * @return int Count of pending bookings
     */
    public function getPendingBookingsCount()
    {
        $sql = "SELECT COUNT(*) as count FROM bookings WHERE status IN ('Requested')";
        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();
        return (int) ($row['count'] ?? 0);
    }

    /**
     * Count payments with status 'pending'
     *
     * @return int Count of pending payments
     */
    public function getPendingPaymentsCount()
    {
        $sql = "SELECT COUNT(*) as count FROM payments WHERE status = 'pending'";
        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();
        return (int) ($row['count'] ?? 0);
    }

    /**
     * Count leave requests with status 'Pending'
     *
     * @return int Count of pending leave requests
     */
    public function getPendingLeaveCount()
    {
        $sql = "SELECT COUNT(*) as count FROM leaves WHERE status = 'Pending'";
        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();
        return (int) ($row['count'] ?? 0);
    }

    /**
     * Count unresolved complaints (Open or In Progress)
     *
     * @return int Count of unresolved complaints
     */
    public function getUnresolvedComplaintsCount()
    {
        $sql = "SELECT COUNT(*) as count FROM complaints WHERE status IN ('Open', 'In Progress')";
        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();
        return (int) ($row['count'] ?? 0);
    }

    /**
     * Count pending reschedule requests
     *
     * @return int Count of pending reschedule requests
     */
    public function getPendingRescheduleCount()
    {
        $sql = "SELECT COUNT(*) as count FROM reschedule_requests WHERE status = 'pending'";
        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();
        return (int) ($row['count'] ?? 0);
    }

    /**
     * Count pending caretaker approvals (if using verification_status)
     * Note: Based on schema, caretakers don't have verification_status, so returns 0
     *
     * @return int Count of pending caretaker approvals
     */
    public function getPendingCaretakerApprovalCount()
    {
        // Based on schema, caretakers table doesn't have verification_status
        // If you add this field later, uncomment the query below:
        /*
        $sql = "SELECT COUNT(*) as count FROM caretakers WHERE verification_status = 'pending'";
        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();
        return (int) ($row['count'] ?? 0);
        */

        // For now, return 0 or check for recently added caretakers needing review
        // You could count caretakers added in last 7 days if needed
        return 0;
    }

    /**
     * Count pending profile change requests
     *
     * @return int Count of pending profile requests
     */
    public function getPendingProfileRequestsCount()
    {
        // Check if profile_change_requests table exists
        try {
            $sql = "SELECT COUNT(*) as count FROM profile_change_requests WHERE status = 'pending'";
            $result = $this->db->query($sql);

            if (!$result) {
                return 0;
            }

            $row = $result->fetch_assoc();
            return (int) ($row['count'] ?? 0);
        } catch (Exception $e) {
            // Table might not exist
            return 0;
        }
    }

    /**
     * Count pending change requests (caretaker change requests)
     *
     * @return int Count of pending change requests
     */
    public function getPendingChangeRequestsCount()
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM change_requests WHERE status = 'pending'";
            $result = $this->db->query($sql);

            if (!$result) {
                return 0;
            }

            $row = $result->fetch_assoc();
            return (int) ($row['count'] ?? 0);
        } catch (Exception $e) {
            return 0;
        }
    }

    // ==================== Client-Specific Methods ====================

    /**
     * Count pending bookings for a specific client
     *
     * @param int $clientId Client ID
     * @return int Count of pending bookings
     */
    public function getClientPendingBookingsCount($clientId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM bookings WHERE client_id = ? AND status IN ('Requested', 'Payment_Requested')");

        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param('i', $clientId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return (int) ($row['count'] ?? 0);
    }

    /**
     * Count pending payments for a specific client
     *
     * @param int $clientId Client ID
     * @return int Count of pending payments
     */
    public function getClientPendingPaymentsCount($clientId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM payments WHERE client_id = ? AND status = 'pending'");

        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param('i', $clientId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return (int) ($row['count'] ?? 0);
    }

    /**
     * Count pending reschedule requests for a specific client
     *
     * @param int $clientId Client ID
     * @return int Count of pending reschedule requests
     */
    public function getClientPendingRescheduleCount($clientId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM reschedule_requests WHERE client_id = ? AND status = 'pending'");

        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param('i', $clientId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return (int) ($row['count'] ?? 0);
    }

    // ==================== Caretaker-Specific Methods ====================

    /**
     * Count new bookings for a specific caretaker (recently accepted)
     *
     * @param int $caretakerId Caretaker ID
     * @return int Count of new bookings
     */
    public function getCaretakerNewBookingsCount($caretakerId)
    {
        // Count bookings that are Accepted but not yet started
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM bookings WHERE caretaker_id = ? AND status = 'Accepted' AND service_start_date >= CURDATE()");

        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param('i', $caretakerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return (int) ($row['count'] ?? 0);
    }

    /**
     * Count pending leave requests for a specific caretaker
     *
     * @param int $caretakerId Caretaker ID (user_id in leaves table)
     * @return int Count of pending leave requests
     */
    public function getCaretakerPendingLeaveCount($caretakerId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM leaves WHERE user_id = ? AND status = 'Pending'");

        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param('i', $caretakerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return (int) ($row['count'] ?? 0);
    }

    // ==================== Utility Methods ====================

    /**
     * Format count for display (shows 99+ for counts > 99)
     *
     * @param int $count The count to format
     * @return string Formatted count
     */
    public static function formatCount($count)
    {
        return ($count > 99) ? '99+' : (string) $count;
    }

    /**
     * Get total pending items across all categories
     *
     * @param array $counts Array of counts
     * @return int Total count
     */
    public static function getTotalCount($counts)
    {
        return array_sum($counts);
    }
}
