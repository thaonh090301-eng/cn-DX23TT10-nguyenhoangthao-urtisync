<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title ?? 'Categories') ?></title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <nav class="top-nav">
            <a href="/">Home</a>
            <a href="/categories" aria-current="page">Categories</a>
            <a href="/activities">Activities</a>
        </nav>

        <section class="page-header">
            <div>
                <p class="eyebrow">Management</p>
                <h1>Categories</h1>
            </div>
            <a class="button primary" href="/categories/create">New Category</a>
        </section>

        <?php if (!empty($flash['success'])): ?>
            <div class="alert success"><?= $e($flash['success']) ?></div>
        <?php endif; ?>

        <section class="panel">
            <?php if ($categories === []): ?>
                <p class="empty-state">No categories yet. Create the first category before adding activities.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Color</th>
                                <th>Sort</th>
                                <th>Activities</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?= $e($category['name']) ?></td>
                                    <td>
                                        <span class="color-chip" style="--chip: <?= $e($category['color']) ?>"></span>
                                        <?= $e($category['color']) ?>
                                    </td>
                                    <td><?= $e($category['sort_order']) ?></td>
                                    <td><?= $e($category['activities_count']) ?></td>
                                    <td class="actions">
                                        <a href="/categories/<?= $e($category['id']) ?>/edit">Edit</a>
                                        <a class="danger-link" href="/categories/<?= $e($category['id']) ?>/delete">Delete</a>
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
