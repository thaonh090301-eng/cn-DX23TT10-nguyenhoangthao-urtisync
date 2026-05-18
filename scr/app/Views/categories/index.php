<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('nav.categories')) ?></title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'categories'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('section.management')) ?></p>
                <h1><?= $e(__('nav.categories')) ?></h1>
            </div>
            <a class="button primary" href="/categories/create"><?= $e(__('action.new_category')) ?></a>
        </section>

        <?php if (!empty($flash['success'])): ?>
            <div class="alert success"><?= $e($flash['success']) ?></div>
        <?php endif; ?>

        <section class="panel">
            <?php if ($categories === []): ?>
                <p class="empty-state"><?= $e(__('empty.categories')) ?></p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th><?= $e(__('label.name')) ?></th>
                                <th><?= $e(__('label.color')) ?></th>
                                <th><?= $e(__('label.sort')) ?></th>
                                <th><?= $e(__('label.activities')) ?></th>
                                <th><?= $e(__('label.actions')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?= $e(display_category_name($category['name'])) ?></td>
                                    <td>
                                        <span class="color-chip" style="--chip: <?= $e($category['color']) ?>"></span>
                                        <?= $e($category['color']) ?>
                                    </td>
                                    <td><?= $e($category['sort_order']) ?></td>
                                    <td><?= $e($category['activities_count']) ?></td>
                                    <td class="actions">
                                        <a href="/categories/<?= $e($category['id']) ?>/edit"><?= $e(__('action.edit')) ?></a>
                                        <a class="danger-link" href="/categories/<?= $e($category['id']) ?>/delete"><?= $e(__('action.delete')) ?></a>
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
