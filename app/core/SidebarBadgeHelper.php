<?php

/**
 * Sidebar Badge Helper
 *
 * Helper functions for rendering sidebar badges with pending counts
 *
 * @package SmartCare
 * @subpackage Helpers
 */

// Require PendingCountModel
require_once APPROOT . '/models/PendingCountModel.php';

/**
 * Get pending counts for the current user role
 *
 * @return array Associative array of badge counts
 */
function getSidebarBadgeCounts()
{
    static $counts = null;

    // Cache counts to avoid multiple DB queries
    if ($counts !== null) {
        return $counts;
    }

    $pendingCountModel = new PendingCountModel();

    // Determine role from session
    $role = $_SESSION['role'] ?? null;

    switch ($role) {
        case 'admin':
            $counts = $pendingCountModel->getAdminPendingCounts();
            break;

        case 'manager': // HR role
            $counts = $pendingCountModel->getHRPendingCounts();
            break;

        case 'client':
            $clientId = $_SESSION['profile_id'] ?? null;
            $counts = $clientId ? $pendingCountModel->getClientPendingCounts($clientId) : [];
            break;

        case 'caretaker':
            $caretakerId = $_SESSION['profile_id'] ?? null;
            $counts = $caretakerId ? $pendingCountModel->getCaretakerPendingCounts($caretakerId) : [];
            break;

        default:
            $counts = [];
    }

    return $counts;
}

/**
 * Render a badge HTML element if count > 0
 *
 * @param string $key The badge key (e.g., 'bookings', 'payments')
 * @param array $counts Array of counts (optional, will fetch if not provided)
 * @return string HTML badge or empty string
 */
function renderBadge($key, $counts = null)
{
    if ($counts === null) {
        $counts = getSidebarBadgeCounts();
    }

    $count = $counts[$key] ?? 0;

    if ($count <= 0) {
        return '';
    }

    $displayCount = ($count > 99) ? '99+' : $count;

    return '<span class="sidebar-badge">' . htmlspecialchars($displayCount) . '</span>';
}

/**
 * Get formatted count for display
 *
 * @param int $count The count to format
 * @return string Formatted count (99+ for counts > 99)
 */
function formatBadgeCount($count)
{
    return ($count > 99) ? '99+' : (string) $count;
}

/**
 * Check if a badge should be shown
 *
 * @param string $key The badge key
 * @param array $counts Array of counts (optional)
 * @return bool True if badge should be shown
 */
function shouldShowBadge($key, $counts = null)
{
    if ($counts === null) {
        $counts = getSidebarBadgeCounts();
    }

    $count = $counts[$key] ?? 0;
    return $count > 0;
}
