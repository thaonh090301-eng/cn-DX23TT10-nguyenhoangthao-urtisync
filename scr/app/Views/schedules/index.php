<?php
$selectedStatus = $selectedStatus ?? 'all';
$selectedDate = $selectedDate ?? date('Y-m-d');
$dateMode = $dateMode ?? 'day';
$buildSchedulesUrl = static function (string $date, string $status): string {
    $params = ['date' => $date];

    if ($status !== 'all') {
        $params['status'] = $status;
    }

    return '/schedules?' . http_build_query($params);
};
?>
<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('nav.schedules')) ?></title>
    <?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'theme_boot.php'; ?>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'schedules'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('section.management')) ?></p>
                <h1><?= $e(__('nav.schedules')) ?></h1>
            </div>
            <div class="header-actions">
                <a class="button" href="/calendar"><?= $e(__('nav.calendar')) ?></a>
                <a class="button primary" href="/schedules/create"><?= $e(__('action.new_schedule')) ?></a>
            </div>
        </section>

        <?php if (!empty($flash['success'])): ?>
            <div class="alert success"><?= $e($flash['success']) ?></div>
        <?php endif; ?>

        <section class="panel">
            <?php if (($hasSchedules ?? true) === false): ?>
                <div class="empty-state">
                    <p><?= $e(__('empty.schedules')) ?></p>
                </div>
            <?php else: ?>
                <form class="filter-bar schedule-filter-bar" method="get" action="/schedules" data-filter-controls data-filter-target="schedules-table">
                    <label class="filter-field">
                        <span><?= $e(__('timetable.date')) ?></span>
                        <input type="date" name="date" value="<?= $dateMode === 'all' ? '' : $e($selectedDate) ?>">
                    </label>
                    <div class="filter-field schedule-day-actions" aria-label="<?= $e(__('timetable.date')) ?>">
                        <button class="button primary" type="submit"><?= $e(__('timetable.action.view_day')) ?></button>
                        <a class="button compact <?= $dateMode === 'all' ? 'primary' : '' ?>" href="<?= $e($buildSchedulesUrl('all', $selectedStatus)) ?>">
                            <?= $e(__('schedule_filter.all_days')) ?>
                        </a>
                    </div>
                    <label class="filter-field">
                        <span><?= $e(__('filter.search')) ?></span>
                        <input type="search" data-filter-search placeholder="<?= $e(__('filter.search_placeholder')) ?>">
                    </label>
                    <label class="filter-field">
                        <span><?= $e(__('label.activity')) ?></span>
                        <select data-filter-select="activity">
                            <option value=""><?= $e(__('filter.all_activities')) ?></option>
                        </select>
                    </label>
                    <label class="filter-field">
                        <span><?= $e(__('label.status')) ?></span>
                        <select name="status" onchange="this.form.submit()">
                            <option value="all" <?= $selectedStatus === 'all' ? 'selected' : '' ?>><?= $e(__('filter.all_statuses')) ?></option>
                            <?php foreach (($statusOptions ?? []) as $status): ?>
                                <option value="<?= $e($status) ?>" <?= $selectedStatus === $status ? 'selected' : '' ?>>
                                    <?= $e(__('schedule_status_display.' . $status)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </form>
                <?php if ($schedules === []): ?>
                    <p class="empty-state"><?= $e(__('filter.no_results')) ?></p>
                <?php else: ?>
                <div class="table-wrap">
                    <table id="schedules-table">
                        <thead>
                            <tr>
                                <th><?= $e(__('label.title')) ?></th>
                                <th><?= $e(__('label.activity')) ?></th>
                                <th><?= $e(__('label.category')) ?></th>
                                <th><?= $e(__('label.start')) ?></th>
                                <th><?= $e(__('label.end')) ?></th>
                                <th><?= $e(__('label.notes')) ?></th>
                                <th><?= $e(__('label.status')) ?></th>
                                <th><?= $e(__('label.actions')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $schedule): ?>
                                <?php
                                    $scheduleTitle = display_activity_title($schedule['title']);
                                    $activityTitle = display_activity_title($schedule['activity_title']);
                                    $categoryName = display_category_name($schedule['category_name']);
                                    $statusLabel = (string) ($schedule['display_status_label'] ?? __('schedule_status_display.scheduled'));
                                    $statusType = (string) ($schedule['display_status_type'] ?? 'info');
                                    $notes = display_note($schedule['notes'] ?? '');
                                    $searchText = implode(' ', [$scheduleTitle, $activityTitle, $categoryName, $statusLabel, $notes, format_app_datetime($schedule['start_at']), format_app_datetime($schedule['end_at'])]);
                                ?>
                                <tr data-filter-row data-search="<?= $e($searchText) ?>" data-activity="<?= $e($activityTitle) ?>" data-status="<?= $e($statusLabel) ?>">
                                    <td><?= $e($scheduleTitle) ?></td>
                                    <td><?= $e($activityTitle) ?></td>
                                    <td>
                                        <span class="color-chip" style="--chip: <?= $e($schedule['category_color']) ?>"></span>
                                        <?= $e($categoryName) ?>
                                    </td>
                                    <td><?= $e(format_app_datetime($schedule['start_at'])) ?></td>
                                    <td><?= $e(format_app_datetime($schedule['end_at'])) ?></td>
                                    <td><?= $e($notes) ?></td>
                                    <td>
                                        <span class="status-pill <?= $e($statusType) ?>"><?= $e($statusLabel) ?></span>
                                    </td>
                                    <td class="actions">
                                        <a href="/schedules/<?= $e($schedule['id']) ?>/edit"><?= $e(__('action.edit')) ?></a>
                                        <a class="danger-link" href="/schedules/<?= $e($schedule['id']) ?>/delete"><?= $e(__('action.delete')) ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p class="empty-state filter-empty" data-filter-empty="schedules-table" hidden><?= $e(__('filter.no_results')) ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
