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
    <?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'theme_boot.php'; ?>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="dashboard-page">
    <main class="app-shell dashboard-shell">
        <?php $activeNav = 'dashboard'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header dashboard-hero">
            <div class="dashboard-hero-copy">
                <p class="eyebrow"><?= $e(__('section.overview')) ?></p>
                <h1><?= $e(__('nav.dashboard')) ?></h1>
            </div>
            <div class="dashboard-hero-metrics" aria-label="<?= $e(__('nav.dashboard')) ?>">
                <article>
                    <span><?= $e(__('dashboard.productivity_score')) ?></span>
                    <strong><?= $e((int) ($productivityScore['value'] ?? 0)) ?>/100</strong>
                </article>
                <article>
                    <span><?= $e(__('nav.focus')) ?></span>
                    <strong><?= $e((int) ($summary['focus_logs_today_count'] ?? 0)) ?></strong>
                </article>
                <article>
                    <span><?= $e(__('dashboard.scheduled_items')) ?></span>
                    <strong><?= $e((int) ($summary['scheduled_items_count'] ?? 0)) ?></strong>
                </article>
            </div>
        </section>

        <section class="dashboard-grid dashboard-stat-grid">
            <?php foreach ($summaryCards as $card): ?>
                <article class="stat-card dashboard-stat-card">
                    <span><?= $e($card['label']) ?></span>
                    <strong><?= $e($card['value']) ?><?= $card['suffix'] !== '' ? ' ' . $e($card['suffix']) : '' ?></strong>
                </article>
            <?php endforeach; ?>
        </section>

        <section class="panel dashboard-section productivity-panel dashboard-glass-panel">
            <div class="productivity-score-card">
                <div>
                    <p class="eyebrow"><?= $e(__('dashboard.gamification_eyebrow')) ?></p>
                    <h2><?= $e(__('dashboard.productivity_score')) ?></h2>
                    <p><?= $e(__('dashboard.productivity_score_help')) ?></p>
                </div>
                <div class="productivity-score-meter" style="--score: <?= $e((int) ($productivityScore['value'] ?? 0)) ?>%">
                    <strong><?= $e((int) ($productivityScore['value'] ?? 0)) ?></strong>
                    <span>/100</span>
                </div>
            </div>

            <div class="productivity-badges" aria-label="<?= $e(__('dashboard.badges_label')) ?>">
                <?php if (($productivityBadges ?? []) === []): ?>
                    <span class="productivity-badge muted"><?= $e(__('dashboard.badge.empty')) ?></span>
                <?php else: ?>
                    <?php foreach ($productivityBadges as $badge): ?>
                        <span class="productivity-badge <?= $e($badge['type']) ?>"><?= $e($badge['label']) ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="score-rules">
                <?php foreach (($productivityScore['rules'] ?? []) as $rule): ?>
                    <?php $points = (int) $rule['points']; ?>
                    <div class="score-rule <?= $e($rule['type']) ?>">
                        <span><?= $e($rule['label']) ?></span>
                        <strong><?= $points > 0 ? '+' : '' ?><?= $e($points) ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="panel dashboard-section dashboard-glass-panel dashboard-alert-panel">
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

        <section class="panel dashboard-section dashboard-glass-panel dashboard-assistant-panel">
            <div class="section-heading">
                <div>
                    <p class="eyebrow"><?= $e(__('assistant.eyebrow')) ?></p>
                    <h2><?= $e(__('assistant.dashboard_title')) ?></h2>
                </div>
                <a class="button primary compact" href="/assistant"><?= $e(__('assistant.open')) ?></a>
            </div>
            <p class="assistant-dashboard-copy"><?= $e(__('assistant.description')) ?></p>

            <div class="empty-state assistant-empty compact">
                <p><?= $e(__('assistant.dashboard_prompt')) ?></p>
            </div>
        </section>

        <section class="panel dashboard-section dashboard-glass-panel dashboard-reminders-panel">
            <div class="section-heading">
                <div>
                    <p class="eyebrow"><?= $e(__('nav.reminders')) ?></p>
                    <h2><?= $e(__('dashboard.upcoming_reminders')) ?></h2>
                </div>
                <a class="button compact" href="/reminders"><?= $e(__('nav.reminders')) ?></a>
            </div>

            <?php if (($upcomingReminders ?? []) === []): ?>
                <div class="empty-state compact">
                    <p><?= $e(__('dashboard.no_upcoming_reminders')) ?></p>
                </div>
            <?php else: ?>
                <div class="reminder-list compact">
                    <?php foreach ($upcomingReminders as $reminder): ?>
                        <article class="reminder-row">
                            <strong><?= $e(format_app_time($reminder['remind_time'])) ?></strong>
                            <span><?= $e($reminder['title']) ?></span>
                            <?php if (!empty($reminder['note'])): ?>
                                <small><?= $e($reminder['note']) ?></small>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="panel dashboard-section dashboard-glass-panel dashboard-important-panel">
            <div class="section-heading">
                <div>
                    <p class="eyebrow"><?= $e(__('nav.important_dates')) ?></p>
                    <h2><?= $e(__('dashboard.upcoming_important_dates')) ?></h2>
                </div>
                <a class="button compact" href="/important-dates"><?= $e(__('nav.important_dates')) ?></a>
            </div>

            <?php if (($upcomingImportantDates ?? []) === []): ?>
                <div class="empty-state compact">
                    <p><?= $e(__('dashboard.no_upcoming_important_dates')) ?></p>
                </div>
            <?php else: ?>
                <div class="countdown-card-grid">
                    <?php foreach ($upcomingImportantDates as $importantDate): ?>
                        <?php
                            $countdownDays = (int) $importantDate['countdown_days'];
                            $countdownLabel = $countdownDays === 0
                                ? __('important_date.countdown_today')
                                : __('important_date.countdown_days', ['days' => $countdownDays]);
                        ?>
                        <article class="countdown-card">
                            <span class="status-pill info"><?= $e(__('important_date.type.' . $importantDate['type'])) ?></span>
                            <strong><?= $e($countdownLabel) ?></strong>
                            <h3><?= $e($importantDate['title']) ?></h3>
                            <p><?= $e(format_app_date($importantDate['next_event_date'])) ?></p>
                            <small><?= $e(__($importantDate['reminder_status_key'], ['days' => $importantDate['remind_before_days']])) ?></small>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="split-grid dashboard-category-compare">
            <article class="panel dashboard-glass-panel">
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

            <article class="panel dashboard-glass-panel">
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
