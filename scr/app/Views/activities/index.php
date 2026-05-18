<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title ?? 'Activities') ?></title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <nav class="top-nav">
            <a href="/">Home</a>
            <a href="/categories">Categories</a>
            <a href="/activities" aria-current="page">Activities</a>
        </nav>

        <section class="page-header">
            <div>
                <p class="eyebrow">Management</p>
                <h1>Activities</h1>
            </div>
            <a class="button primary" href="/activities/create">New Activity</a>
        </section>

        <?php if (!empty($flash['success'])): ?>
            <div class="alert success"><?= $e($flash['success']) ?></div>
        <?php endif; ?>

        <section class="panel">
            <?php if ($activities === []): ?>
                <p class="empty-state">No activities yet. Create categories first, then add activities.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Estimate</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activities as $activity): ?>
                                <tr>
                                    <td><?= $e($activity['title']) ?></td>
                                    <td>
                                        <span class="color-chip" style="--chip: <?= $e($activity['category_color']) ?>"></span>
                                        <?= $e($activity['category_name']) ?>
                                    </td>
                                    <td><?= $e(ucfirst($activity['priority'])) ?></td>
                                    <td><?= $e($activity['estimated_minutes']) ?> min</td>
                                    <td><?= ((int) $activity['is_active'] === 1) ? 'Active' : 'Inactive' ?></td>
                                    <td class="actions">
                                        <a href="/activities/<?= $e($activity['id']) ?>/edit">Edit</a>
                                        <a class="danger-link" href="/activities/<?= $e($activity['id']) ?>/delete">Delete</a>
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
