<?php

require_once APPROOT . '/core/Database.php';

/**
 * Read-only aggregates for the public landing page (no auth).
 */
class LandingModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }

    /**
     * @return array{
     *   care_hours:int,
     *   care_hours_display:string,
     *   families:int,
     *   families_display:string,
     *   avg_rating:float|null,
     *   rating_display:string,
     *   has_family_feedback:bool
     * }
     */
    public function getPublicMetrics(): array
    {
        $defaults = [
            'care_hours' => 0,
            'care_hours_display' => '0',
            'families' => 0,
            'families_display' => '0',
            'avg_rating' => null,
            'rating_display' => '—',
            'has_family_feedback' => false,
        ];

        if (!$this->conn) {
            return $defaults;
        }

        try {
            $hours = $this->fetchEstimatedCareHours();
            $families = $this->fetchFamiliesSupportedCount();
            $ratingRow = $this->fetchAverageRating();

            return [
                'care_hours' => $hours,
                'care_hours_display' => self::formatHoursHeadline($hours),
                'families' => $families,
                'families_display' => self::formatCountHeadline($families),
                'avg_rating' => $ratingRow['value'],
                'rating_display' => $ratingRow['display'],
                'has_family_feedback' => $ratingRow['from_feedback'],
            ];
        } catch (Throwable $e) {
            return $defaults;
        }
    }

    private function fetchEstimatedCareHours(): int
    {
        $sql = "SELECT COALESCE(SUM(
            CASE `basis`
                WHEN 'Hourly' THEN `duration` + COALESCE(`customization_hours`, 0)
                WHEN 'Daily' THEN `duration` * 9
                WHEN 'Monthly' THEN `duration` * 22 * 9
                WHEN 'Yearly' THEN `duration` * 250 * 9
                ELSE `duration` * 9
            END
        ), 0) AS h
        FROM `bookings`
        WHERE `status` NOT IN ('Cancelled', 'Rejected')";

        $res = $this->conn->query($sql);
        if (!$res || !($row = $res->fetch_assoc())) {
            return 0;
        }
        return max(0, (int) round((float) ($row['h'] ?? 0)));
    }

    /**
     * Families with at least one non-cancelled booking; otherwise registered clients.
     */
    private function fetchFamiliesSupportedCount(): int
    {
        $sql = "SELECT COUNT(DISTINCT `client_id`) AS c FROM `bookings`
                WHERE `status` NOT IN ('Cancelled', 'Rejected')";
        $res = $this->conn->query($sql);
        $n = 0;
        if ($res && ($row = $res->fetch_assoc())) {
            $n = max(0, (int) ($row['c'] ?? 0));
        }
        if ($n > 0) {
            return $n;
        }
        $res2 = $this->conn->query('SELECT COUNT(*) AS c FROM `clients`');
        if (!$res2 || !($row2 = $res2->fetch_assoc())) {
            return 0;
        }
        return max(0, (int) ($row2['c'] ?? 0));
    }

    /**
     * Prefer average from client feedbacks; fallback to active caretakers' stored ratings.
     *
     * @return array{value: float|null, display: string, from_feedback: bool}
     */
    private function fetchAverageRating(): array
    {
        $fb = $this->conn->query(
            'SELECT ROUND(AVG(`rating`), 1) AS a, COUNT(*) AS n FROM `feedbacks` WHERE `rating` > 0'
        );
        if ($fb && ($r = $fb->fetch_assoc()) && (int) ($r['n'] ?? 0) > 0 && $r['a'] !== null) {
            $v = (float) $r['a'];
            return ['value' => $v, 'display' => number_format($v, 1), 'from_feedback' => true];
        }

        $ct = $this->conn->query(
            "SELECT ROUND(AVG(`rating`), 1) AS a, COUNT(*) AS n FROM `caretakers`
             WHERE `rating` IS NOT NULL AND `status` = 'Active'"
        );
        if ($ct && ($r = $ct->fetch_assoc()) && (int) ($r['n'] ?? 0) > 0 && $r['a'] !== null) {
            $v = (float) $r['a'];
            return ['value' => $v, 'display' => number_format($v, 1), 'from_feedback' => false];
        }

        return ['value' => null, 'display' => '—', 'from_feedback' => false];
    }

    private static function formatHoursHeadline(int $n): string
    {
        if ($n >= 1000000) {
            return rtrim(rtrim(number_format($n / 1000000, 1), '0'), '.') . 'M+';
        }
        if ($n >= 10000) {
            return (string) (int) round($n / 1000) . 'k+';
        }
        if ($n >= 1000) {
            $k = $n / 1000;
            $s = number_format($k, $k == floor($k) ? 0 : 1);
            return $s . 'k+';
        }
        return (string) max(0, $n);
    }

    private static function formatCountHeadline(int $n): string
    {
        if ($n >= 10000) {
            return (string) (int) round($n / 1000) . 'k+';
        }
        if ($n >= 1000) {
            $k = $n / 1000;
            return number_format($k, $k == floor($k) ? 0 : 1) . 'k+';
        }
        return (string) max(0, $n);
    }
}
