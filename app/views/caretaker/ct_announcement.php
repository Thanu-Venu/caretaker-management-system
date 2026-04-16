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

        return rtrim(mb_substr($text, 0, $max - 1)) . '…';
    }
    if (strlen($text) <= $max) {
        return $text;
    }

    return rtrim(substr($text, 0, $max - 3)) . '…';
};

$ct_audience_label = static function (string $role): string {
    $r = strtolower($role);
    if ($r === 'caretaker') {
        return 'Caregivers';
    }
    if ($r === 'all') {
        return 'Everyone';
    }

    return $role !== '' ? $role : '---';
};

$buildAnnPageUrl = static function (int $page) use ($df, $dt, $fq): string {
    $q = ['url' => 'caretaker/ct_announcement'];
    if ($df !== '') {
        $q['date_from'] = $df;
    }
    if ($dt !== '') {
        $q['date_to'] = $dt;
    }
    if ($fq !== '') {
        $q['q'] = $fq;
    }
    if ($page > 1) {
        $q['page'] = $page;
    }

    return URLROOT . '/public?' . http_build_query($q);
};
?>

<?php
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
            <label for="ct-ann-from">From</label>
            <input type="date" id="ct-ann-from" name="date_from" class="form-input" value="<?= ct_ann_esc($df) ?>">
        </div>
        <div class="filter-group">
            <label for="ct-ann-to">To</label>
            <input type="date" id="ct-ann-to" name="date_to" class="form-input" value="<?= ct_ann_esc($dt) ?>">
        </div>
        <div class="filter-group filter-group--grow">
            <label for="ct-ann-q">Search</label>
            <input type="search" id="ct-ann-q" name="q" class="form-input" value="<?= ct_ann_esc($fq) ?>" placeholder="Title or message">
        </div>
        <div class="filter-group filter-group--actions">
            <button type="submit" class="btn primary">Apply</button>
            <a class="btn ghost" href="<?= ct_ann_esc(URLROOT . '/public?url=caretaker/ct_announcement') ?>">Reset</a>
        </div>
    </form>

    <?php if (empty($announcements)): ?>
        <p class="no-data"><?= $totalRecords > 0 ? 'No announcements match your filters.' : 'No announcements yet.' ?></p>
    <?php else: ?>
        <div class="table-container ct-announcements-table-wrap">
            <table class="table booking-table ct-announcements-table" data-table-collapse="off">
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
                        $by = (string) ($ann['created_by_name'] ?? '');
                        ?>
                        <tr>
                            <td class="ct-announcements-date"><?= ct_ann_esc($dateOut) ?></td>
                            <td class="ct-announcements-title">
                                <span class="ct-announcements-title-text"><?= ct_ann_esc($title) ?></span>
                                <?php if ($by !== ''): ?>
                                    <span class="ct-announcements-by">By <?= ct_ann_esc($by) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="ct-announcements-msg" title="<?= ct_ann_esc($msg) ?>"><?= ct_ann_esc($ann_preview($msg)) ?></td>
                            <td><span class="status-pill ct-announcements-audience"><?= ct_ann_esc($ct_audience_label($role)) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="ct-announcements-pagination" aria-label="Announcements pagination">
                <?php if ($currentPage > 1): ?>
                    <a class="btn secondary btn-sm" href="<?= ct_ann_esc($buildPageUrl($currentPage - 1)) ?>">Previous</a>
                <?php endif; ?>
                <span class="ct-announcements-page-meta">Page <?= $currentPage ?> of <?= $totalPages ?> (<?= $totalRecords ?> total)</span>
                <?php if ($currentPage < $totalPages): ?>
                    <a class="btn secondary btn-sm" href="<?= ct_ann_esc($buildPageUrl($currentPage + 1)) ?>">Next</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php require_once APPROOT . '/views/templates/caretaker/caretaker_layout_close.php'; ?>