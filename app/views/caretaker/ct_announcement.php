<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_announcement.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/admin/admin-ui.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_header.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_sidebar.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/common/sidebar-badges.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>

<?php
$announcements  = isset($announcements) && is_array($announcements) ? $announcements : [];
$filters        = isset($filters) && is_array($filters) ? $filters : ['date_from' => '', 'date_to' => '', 'q' => ''];
$currentPage    = (int) ($currentPage ?? 1);
$totalPages     = (int) ($totalPages ?? 1);
$totalRecords   = (int) ($totalRecords ?? 0);
$perPage        = (int) ($perPage ?? 15);

function ct_ann_esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$ann_preview = static function (string $text, int $max = 200): string {
    $text = trim(preg_replace('/\s+/', ' ', $text));
    if ($text === '') {
        return '';
    }
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        return mb_strlen($text) <= $max ? $text : rtrim(mb_substr($text, 0, $max - 1)) . '…';
    }
    return strlen($text) <= $max ? $text : rtrim(substr($text, 0, $max - 3)) . '…';
};

$audience_label = static function (string $role): string {
    if ($role === '') {
        return 'Caregiver';
    }
    $role = strtolower($role);
    return $role === 'all' ? 'Everyone' : ucfirst($role);
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

    return URLROOT . '/public?' . http_build_query($query);
};
?>

<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>
<main class="main-content announcement-container caretaker-announcements-page">
    <header class="page-header">
        <h1 class="page-title">Announcements</h1>
        <p class="page-subtitle">Updates published for caretakers and general audiences.</p>
    </header>

    <form method="get" action="<?= ct_ann_esc(URLROOT . '/public') ?>" class="caretaker-announcement-filters filter-section filters-inline">
        <input type="hidden" name="url" value="caretaker/ct_announcement">

        <div class="filter-group">
            <label for="caretaker-ann-from">From</label>
            <input id="caretaker-ann-from" type="date" name="date_from" class="form-input" value="<?= ct_ann_esc($filters['date_from']) ?>">
        </div>

        <div class="filter-group">
            <label for="caretaker-ann-to">To</label>
            <input id="caretaker-ann-to" type="date" name="date_to" class="form-input" value="<?= ct_ann_esc($filters['date_to']) ?>">
        </div>

        <div class="filter-group filter-group--grow">
            <label for="caretaker-ann-q">Search</label>
            <input id="caretaker-ann-q" type="search" name="q" class="form-input" value="<?= ct_ann_esc($filters['q']) ?>" placeholder="Title or message">
        </div>

        <div class="filter-group filter-group--actions">
            <button type="submit" class="btn primary">Apply</button>
            <a class="btn ghost" href="<?= ct_ann_esc(URLROOT . '/public?url=caretaker/ct_announcement') ?>">Reset</a>
        </div>
    </form>

    <?php if (empty($announcements)): ?>
        <p class="no-data"><?= $totalRecords > 0 ? 'No announcements match your filters.' : 'No announcements yet.' ?></p>
    <?php else: ?>
        <div class="card">
            <div class="table-container">
                <table class="table booking-table caretaker-announcements-table" data-table-collapse="off">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Audience</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($announcements as $announcement): ?>
                            <?php
                            $title = (string) ($announcement['title'] ?? '');
                            $msg = (string) ($announcement['message'] ?? '');
                            $role = (string) ($announcement['target_role'] ?? '');
                            $rawDate = (string) ($announcement['created_at'] ?? '');
                            $dateOut = $rawDate !== '' ? date('M j, Y', strtotime($rawDate)) : '—';
                            $createdBy = (string) ($announcement['created_by_name'] ?? '');
                            ?>
                            <tr>
                                <td class="announcement-date"><?= ct_ann_esc($dateOut) ?></td>
                                <td class="announcement-title">
                                    <span class="announcement-title-text"><?= ct_ann_esc($title) ?></span>
                                    <?php if ($createdBy !== ''): ?>
                                        <span class="announcement-by">By <?= ct_ann_esc($createdBy) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="announcement-msg" title="<?= ct_ann_esc($msg) ?>"><?= ct_ann_esc($ann_preview($msg)) ?></td>
                                <td><span class="role-tag"><?= ct_ann_esc($audience_label($role)) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav class="caretaker-announcements-pagination" aria-label="Announcements pagination">
                    <?php if ($currentPage > 1): ?>
                        <a class="btn secondary btn-sm" href="<?= ct_ann_esc($buildPageUrl($currentPage - 1)) ?>">Previous</a>
                    <?php endif; ?>
                    <span class="announcement-page-meta">Page <?= $currentPage ?> of <?= $totalPages ?> (<?= $totalRecords ?> total)</span>
                    <?php if ($currentPage < $totalPages): ?>
                        <a class="btn secondary btn-sm" href="<?= ct_ann_esc($buildPageUrl($currentPage + 1)) ?>">Next</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</main>

</body>
</html>