<?php
$announcements = $data['announcements'] ?? [];
$filters       = $data['filters'] ?? ['date_from' => '', 'date_to' => '', 'q' => ''];
$currentPage   = (int) ($data['currentPage'] ?? 1);
$totalPages    = (int) ($data['totalPages'] ?? 1);
$totalRecords  = (int) ($data['totalRecords'] ?? 0);
$perPage       = (int) ($data['perPage'] ?? 15);

$df = (string) ($filters['date_from'] ?? '');
$dt = (string) ($filters['date_to'] ?? '');
$fq = (string) ($filters['q'] ?? '');

function hr_ann_esc(string $s): string
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

$hr_audience_label = static function (string $role): string {
    $r = strtolower($role);
    if ($r === 'users') {
        return 'HR / Admin';
    }
    if ($r === 'all') {
        return 'Everyone';
    }

    return $role !== '' ? $role : '—';
};

$buildAnnPageUrl = static function (int $page) use ($df, $dt, $fq): string {
    $q = ['url' => 'hr/hr_announcement'];
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

$hrPageTitle = 'Announcements — HR';
$hrExtraCss  = ['hr/hr_announcement.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';
?>

<main class="main-content hr-announcements-page">
    <header class="page-header">
        <h1 class="page-title">Announcements</h1>
        <p class="page-subtitle">Messages published for HR and broadcast audiences.</p>
    </header>

    <form method="get" action="<?= hr_ann_esc(URLROOT . '/public') ?>" class="hr-announcement-filters filter-section filters-inline">
        <input type="hidden" name="url" value="hr/hr_announcement">
        <div class="filter-group">
            <label for="hr-ann-from">From</label>
            <input type="date" id="hr-ann-from" name="date_from" class="form-input" value="<?= hr_ann_esc($df) ?>">
        </div>
        <div class="filter-group">
            <label for="hr-ann-to">To</label>
            <input type="date" id="hr-ann-to" name="date_to" class="form-input" value="<?= hr_ann_esc($dt) ?>">
        </div>
        <div class="filter-group filter-group--grow">
            <label for="hr-ann-q">Search</label>
            <input type="search" id="hr-ann-q" name="q" class="form-input" value="<?= hr_ann_esc($fq) ?>" placeholder="Title or message">
        </div>
        <div class="filter-group filter-group--actions">
            <button type="submit" class="btn primary">Apply</button>
            <a class="btn ghost" href="<?= hr_ann_esc(URLROOT . '/public?url=hr/hr_announcement') ?>">Reset</a>
        </div>
    </form>

    <?php if (empty($announcements)): ?>
        <p class="no-data"><?= $totalRecords > 0 ? 'No announcements match your filters.' : 'No announcements yet.' ?></p>
    <?php else: ?>
        <div class="table-container hr-announcements-table-wrap">
            <table class="table booking-table hr-announcements-table" data-table-collapse="off">
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
                        $dateOut = $rawDate !== '' ? date('M j, Y', strtotime($rawDate)) : '—';
                        $by = (string) ($ann['created_by_name'] ?? '');
                        ?>
                        <tr>
                            <td class="hr-announcements-date"><?= hr_ann_esc($dateOut) ?></td>
                            <td class="hr-announcements-title">
                                <span class="hr-announcements-title-text"><?= hr_ann_esc($title) ?></span>
                                <?php if ($by !== ''): ?>
                                    <span class="hr-announcements-by">By <?= hr_ann_esc($by) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="hr-announcements-msg" title="<?= hr_ann_esc($msg) ?>"><?= hr_ann_esc($ann_preview($msg)) ?></td>
                            <td><span class="status-pill hr-announcements-audience"><?= hr_ann_esc($hr_audience_label($role)) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="hr-announcements-pagination" aria-label="Announcements pagination">
                <?php if ($currentPage > 1): ?>
                    <a class="btn secondary btn-sm" href="<?= hr_ann_esc($buildAnnPageUrl($currentPage - 1)) ?>">Previous</a>
                <?php endif; ?>
                <span class="hr-announcements-page-meta">Page <?= (int) $currentPage ?> of <?= (int) $totalPages ?> (<?= (int) $totalRecords ?> total)</span>
                <?php if ($currentPage < $totalPages): ?>
                    <a class="btn secondary btn-sm" href="<?= hr_ann_esc($buildAnnPageUrl($currentPage + 1)) ?>">Next</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</main>
<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
