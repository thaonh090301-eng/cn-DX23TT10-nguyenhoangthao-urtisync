<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('nav.time_logs')) ?></title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'time_logs'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('section.tracking')) ?></p>
                <h1><?= $e(__('nav.time_logs')) ?></h1>
            </div>
            <a class="button primary" href="/time-logs/create"><?= $e(__('action.new_time_log')) ?></a>
        </section>

        <?php if (!empty($flash['success'])): ?>
            <div class="alert success"><?= $e($flash['success']) ?></div>
        <?php endif; ?>

        <section class="panel">
            <?php if ($timeLogs === []): ?>
                <p class="empty-state"><?= $e(__('empty.time_logs')) ?></p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th><?= $e(__('label.activity')) ?></th>
                                <th><?= $e(__('label.category')) ?></th>
                                <th><?= $e(__('label.actual_start')) ?></th>
                                <th><?= $e(__('label.actual_end')) ?></th>
                                <th><?= $e(__('label.duration')) ?></th>
                                <th><?= $e(__('label.note')) ?></th>
                                <th><?= $e(__('label.actions')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($timeLogs as $timeLog): ?>
                                <tr>
                                    <td><?= $e(display_activity_title($timeLog['activity_title'])) ?></td>
                                    <td>
                                        <span class="color-chip" style="--chip: <?= $e($timeLog['category_color']) ?>"></span>
                                        <?= $e(display_category_name($timeLog['category_name'])) ?>
                                    </td>
                                    <td><?= $e($timeLog['started_at']) ?></td>
                                    <td><?= $e($timeLog['ended_at']) ?></td>
                                    <td><?= $e($timeLog['duration_minutes']) ?> <?= $e(__('unit.min')) ?></td>
                                    <td><?= $e($timeLog['note'] ?? '') ?></td>
                                    <td class="actions">
                                        <a href="/time-logs/<?= $e($timeLog['id']) ?>/edit"><?= $e(__('action.edit')) ?></a>
                                        <a class="danger-link" href="/time-logs/<?= $e($timeLog['id']) ?>/delete"><?= $e(__('action.delete')) ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
