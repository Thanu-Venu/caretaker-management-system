<?php
/** @var array $announcements @var array $filters @var int $currentPage @var int $totalPages @var int $totalRecords @var int $perPage */
$announcements = isset($announcements) && is_array($announcements) ? $announcements : [];
$filters       = isset($filters) && is_array($filters) ? $filters : ['date_from' => '', 'date_to' => '', 'q' => ''];
$currentPage   = (int) ($currentPage ?? 1);
$totalPages    = (int) ($totalPages ?? 1);
$totalRecords  = (int) ($totalRecords ?? 0);
$perPage       = (int) ($perPage ?? 15);

$df = (string) ($filters['date_from'] ?? '');
$dt = (string) ($filters['date_to'] ?? '');
$fq = (string) ($filters['q'] ?? '');

function ct_ann_esc(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/** @return string */
$ann_preview = static function (string $text, int $max = 200): string {
    $text = trim(preg_replace('/\s+/', ' ', $text));
    if ($text === '') {
        return '';
    }
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text) <= $max) {
            return $text;
        }
        return rtrim(mb_substr($text, 0, $max - 1)) . '...';
    }
    if (strlen($text) <= $max) {
        return $text;
    }
    return rtrim(substr($text, 0, $max - 1)) . '...';
};

$ct_audience_label = static function (string $role): string {
    $r = strtolower($role);
    if ($r === 'caretaker') {
        return 'Caregivers';
    }
    if ($r === 'all') {
        return 'Everyone';
    }
    return $role !== '' ? $role : '...';
};

$buildPageUrl = static function (int $page) use ($filters): string {
    $query = ['url' => 'caretaker/ct_announcement'];
    if ($filters['date_from'] !== '') {
        $query['date_from'] = $filters['date_from'];
    }
    if ($filters['date_to'] !== '') {
        $query['date_to'] = $filters['date_to'];
    }
    if ($filters['q'] !== '') {
        $query['q'] = $filters['q'];
    }
    if ($page > 1) {
        $query['page'] = $page;
    }
    return URLROOT . '/public?' . http_build_query($q);
};

$caretakerPageTitle = 'Announcements - SmartCare';
$caretakerExtraCss = ['caretaker/ct_announcement.css'];
require_once APPROOT . '/views/templates/caretaker/caretaker_layout_head.php';
include_once APPROOT . '/views/templates/caretaker/ct_header.php';
include_once APPROOT . '/views/templates/caretaker/ct_sidebar.php';
?>

<main class="content ct-announcements-page">
    <header class="page-header">
        <h1 class="page-title">Announcements</h1>
        <p class="page-subtitle">Updates published for caregivers and general audiences.</p>
    </header>

    <form method="get" action="<?= ct_ann_esc(URLROOT . '/public') ?>" class="ct-announcement-filters filter-section filters-inline">
        <input type="hidden" name="url" value="caretaker/ct_announcement">
        <div class="filter-group">
            <label for="dateFrom">Date From</label>
            <input type="date" id="dateFrom" name="date_from" value="<?= ct_ann_esc($df) ?>">
        </div>
        <div class="filter-group">
            <label for="dateTo">Date To</label>
            <input type="date" id="dateTo" name="date_to" value="<?= ct_ann_esc($dt) ?>">
        </div>
        <div class="filter-group">
            <label for="searchQ">Search</label>
            <input type="search" id="searchQ" name="q" value="<?= ct_ann_esc($fq) ?>" placeholder="Search announcements...">
        </div>
        <div class="filter-group filter-group--actions">
            <button type="submit" class="btn primary">Filter</button>
            <a href="<?= ct_ann_esc(URLROOT . '/public?url=caretaker/ct_announcement') ?>" class="btn secondary">Clear</a>
        </div>
    </form>

    <?php if (!empty($announcements)): ?>
        <div class="ct-announcements-table-wrap">
            <table class="data-table ct-announcements-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Audience</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $ann): ?>
                        <?php
                        $title   = (string) ($ann['title'] ?? '');
                        $msg     = (string) ($ann['message'] ?? '');
                        $role    = (string) ($ann['target_role'] ?? '');
                        $rawDate = (string) ($ann['created_at'] ?? '');
                        $dateOut = $rawDate !== '' ? date('M j, Y', strtotime($rawDate)) : '---';
                        ?>
                        <tr>
                            <td class="ct-announcements-date"><?= ct_ann_esc($dateOut) ?></td>
                            <td class="ct-announcements-title">
                                <span class="ct-announcements-title-text"><?= ct_ann_esc($title) ?></span>
                            </td>
                            <td class="ct-announcements-msg" title="<?= ct_ann_esc($msg) ?>"><?= ct_ann_esc($ann_preview($msg)) ?></td>
                            <td><span class="status-pill ct-announcements-audience"><?= ct_ann_esc($ct_audience_label($role)) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($totalPages > 1): ?>
                <nav class="ct-announcements-pagination" aria-label="Announcements pagination">
                    <?php if ($currentPage > 1): ?>
                        <a class="btn secondary btn-sm" href="<?= ct_ann_esc($buildPageUrl($currentPage - 1)) ?>">Previous</a>
                    <?php endif; ?>
                    <span class="ct-announcements-page-meta">Page <?= (int) $currentPage ?> of <?= (int) $totalPages ?> (<?= (int) $totalRecords ?> total)</span>
                    <?php if ($currentPage < $totalPages): ?>
                        <a class="btn secondary btn-sm" href="<?= ct_ann_esc($buildPageUrl($currentPage + 1)) ?>">Next</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="no-data">
            <p>No announcements found matching your criteria.</p>
        </div>
    <?php endif; ?>
</main>

<?php require_once APPROOT . '/views/templates/caretaker/caretaker_layout_close.php'; ?>
