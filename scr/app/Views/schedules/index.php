<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('nav.schedules')) ?></title>
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
            <?php if ($schedules === []): ?>
                <p class="empty-state"><?= $e(__('empty.schedules')) ?></p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th><?= $e(__('label.title')) ?></th>
                                <th><?= $e(__('label.activity')) ?></th>
                                <th><?= $e(__('label.category')) ?></th>
                                <th><?= $e(__('label.start')) ?></th>
                                <th><?= $e(__('label.end')) ?></th>
                                <th><?= $e(__('label.status')) ?></th>
                                <th><?= $e(__('label.actions')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $schedule): ?>
                                <tr>
                                    <td><?= $e(display_activity_title($schedule['title'])) ?></td>
                                    <td><?= $e(display_activity_title($schedule['activity_title'])) ?></td>
                                    <td>
                                        <span class="color-chip" style="--chip: <?= $e($schedule['category_color']) ?>"></span>
                                        <?= $e(display_category_name($schedule['category_name'])) ?>
                                    </td>
                                    <td><?= $e($schedule['start_at']) ?></td>
                                    <td><?= $e($schedule['end_at']) ?></td>
                                    <td><?= $e(__('schedule_status.' . $schedule['status'])) ?></td>
                                    <td class="actions">
                                        <a href="/schedules/<?= $e($schedule['id']) ?>/edit"><?= $e(__('action.edit')) ?></a>
                                        <a class="danger-link" href="/schedules/<?= $e($schedule['id']) ?>/delete"><?= $e(__('action.delete')) ?></a>
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
