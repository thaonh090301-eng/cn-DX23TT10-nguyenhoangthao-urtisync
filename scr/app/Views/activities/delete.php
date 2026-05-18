<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('page.delete_activity')) ?></title>
    <link rel="stylesheet" href="../../assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <?php $activeNav = 'activities'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('nav.activities')) ?></p>
                <h1><?= $e(__('page.delete_activity')) ?></h1>
            </div>
        </section>

        <section class="panel form-stack">
            <?php if (!empty($errors['activity'])): ?>
                <div class="alert danger"><?= $e($errors['activity']) ?></div>
            <?php endif; ?>

            <p>
                <?= $e(__('message.delete_activity')) ?>
                <strong><?= $e(display_activity_title($activity['title'])) ?></strong>
                <?= $e(display_category_name($activity['category_name'])) ?>
                <?= $e(__('message.activity_usage', ['schedules' => $activity['schedules_count'] ?? 0, 'time_logs' => $activity['time_logs_count'] ?? 0])) ?>
            </p>

            <form method="post" action="/activities/<?= $e($activity['id']) ?>">
                <input type="hidden" name="_method" value="DELETE">
                <div class="form-actions">
                    <a class="button" href="/activities"><?= $e(__('action.cancel')) ?></a>
                    <button class="button danger" type="submit"><?= $e(__('action.delete')) ?></button>
                </div>
            </form>
        </section>
    </main>
    <script src="../../assets/js/app.js"></script>
</body>
</html>
