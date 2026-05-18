<?php
$summaryCards = [
    ['label' => __('dashboard.planned_today'), 'value' => $summary['planned_today_minutes'], 'suffix' => __('unit.min')],
    ['label' => __('dashboard.actual_today'), 'value' => $summary['actual_today_minutes'], 'suffix' => __('unit.min')],
    ['label' => __('dashboard.planned_week'), 'value' => $summary['planned_week_minutes'], 'suffix' => __('unit.min')],
    ['label' => __('dashboard.actual_week'), 'value' => $summary['actual_week_minutes'], 'suffix' => __('unit.min')],
    ['label' => __('dashboard.active_activities'), 'value' => $summary['active_activities_count'], 'suffix' => ''],
    ['label' => __('dashboard.scheduled_items'), 'value' => $summary['scheduled_items_count'], 'suffix' => ''],
];

$plannedMax = max(1, ...array_map(static fn (array $row): int => (int) $row['minutes'], $plannedByCategory));
$actualMax = max(1, ...array_map(static fn (array $row): int => (int) $row['minutes'], $actualByCategory));
?>
<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('nav.dashboard')) ?></title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'dashboard'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('section.overview')) ?></p>
                <h1><?= $e(__('nav.dashboard')) ?></h1>
            </div>
        </section>

        <section class="dashboard-grid">
            <?php foreach ($summaryCards as $card): ?>
                <article class="stat-card">
                    <span><?= $e($card['label']) ?></span>
                    <strong><?= $e($card['value']) ?><?= $card['suffix'] !== '' ? ' ' . $e($card['suffix']) : '' ?></strong>
                </article>
            <?php endforeach; ?>
        </section>

        <section class="panel dashboard-section">
            <div class="section-heading">
                <div>
                    <p class="eyebrow"><?= $e(__('section.alerts')) ?></p>
                    <h2><?= $e(__('section.today')) ?></h2>
                </div>
                <span class="threshold-note">
                    <?= $e(__('dashboard.personal_threshold', ['actual' => $personalActualMinutes, 'threshold' => $personalThresholdMinutes])) ?>
                </span>
            </div>

            <?php if ($alerts === []): ?>
                <div class="alert success"><?= $e(__('dashboard.no_alerts')) ?></div>
            <?php else: ?>
                <div class="alert-list">
                    <?php foreach ($alerts as $alert): ?>
                        <div class="alert <?= $e($alert['type']) ?>"><?= $e($alert['message']) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="split-grid">
            <article class="panel">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow"><?= $e(__('section.this_week')) ?></p>
                        <h2><?= $e(__('dashboard.planned_by_category')) ?></h2>
                    </div>
                </div>

                <div class="category-stats">
                    <?php foreach ($plannedByCategory as $category): ?>
                        <?php $width = ((int) $category['minutes'] / $plannedMax) * 100; ?>
                        <div class="category-row">
                            <div class="category-meta">
                                <span>
                                    <i class="color-chip" style="--chip: <?= $e($category['color']) ?>"></i>
                                    <?= $e(display_category_name($category['name'])) ?>
                                </span>
                                <strong><?= $e($category['minutes']) ?> <?= $e(__('unit.min')) ?></strong>
                            </div>
                            <div class="category-bar">
                                <span style="--bar: <?= $e(number_format($width, 2, '.', '')) ?>%; --chip: <?= $e($category['color']) ?>"></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </article>

            <article class="panel">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow"><?= $e(__('section.this_week')) ?></p>
                        <h2><?= $e(__('dashboard.actual_by_category')) ?></h2>
                    </div>
                </div>

                <div class="category-stats">
                    <?php foreach ($actualByCategory as $category): ?>
                        <?php $width = ((int) $category['minutes'] / $actualMax) * 100; ?>
                        <div class="category-row">
                            <div class="category-meta">
                                <span>
                                    <i class="color-chip" style="--chip: <?= $e($category['color']) ?>"></i>
                                    <?= $e(display_category_name($category['name'])) ?>
                                </span>
                                <strong><?= $e($category['minutes']) ?> <?= $e(__('unit.min')) ?></strong>
                            </div>
                            <div class="category-bar">
                                <span style="--bar: <?= $e(number_format($width, 2, '.', '')) ?>%; --chip: <?= $e($category['color']) ?>"></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </article>
        </section>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
