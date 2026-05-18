<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('nav.activities')) ?></title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'activities'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('section.management')) ?></p>
                <h1><?= $e(__('nav.activities')) ?></h1>
            </div>
            <a class="button primary" href="/activities/create"><?= $e(__('action.new_activity')) ?></a>
        </section>

        <?php if (!empty($flash['success'])): ?>
            <div class="alert success"><?= $e($flash['success']) ?></div>
        <?php endif; ?>

        <section class="panel">
            <?php if ($activities === []): ?>
                <p class="empty-state"><?= $e(__('empty.activities')) ?></p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th><?= $e(__('label.title')) ?></th>
                                <th><?= $e(__('label.category')) ?></th>
                                <th><?= $e(__('label.priority')) ?></th>
                                <th><?= $e(__('label.estimate')) ?></th>
                                <th><?= $e(__('label.status')) ?></th>
                                <th><?= $e(__('label.actions')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activities as $activity): ?>
                                <tr>
                                    <td><?= $e(display_activity_title($activity['title'])) ?></td>
                                    <td>
                                        <span class="color-chip" style="--chip: <?= $e($activity['category_color']) ?>"></span>
                                        <?= $e(display_category_name($activity['category_name'])) ?>
                                    </td>
                                    <td><?= $e(__('priority.' . $activity['priority'])) ?></td>
                                    <td><?= $e($activity['estimated_minutes']) ?> <?= $e(__('unit.min')) ?></td>
                                    <td><?= ((int) $activity['is_active'] === 1) ? $e(__('status.active')) : $e(__('status.inactive')) ?></td>
                                    <td class="actions">
                                        <a href="/activities/<?= $e($activity['id']) ?>/edit"><?= $e(__('action.edit')) ?></a>
                                        <a class="danger-link" href="/activities/<?= $e($activity['id']) ?>/delete"><?= $e(__('action.delete')) ?></a>
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
