<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('page.delete_schedule')) ?></title>
    <link rel="stylesheet" href="../../assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <?php $activeNav = 'schedules'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('nav.schedules')) ?></p>
                <h1><?= $e(__('page.delete_schedule')) ?></h1>
            </div>
        </section>

        <section class="panel form-stack">
            <p>
                <?= $e(__('message.delete_schedule')) ?>
                <strong><?= $e(display_activity_title($schedule['title'])) ?></strong>
                <?= $e(__('message.schedule_for_activity', ['activity' => display_activity_title($schedule['activity_title'])])) ?>
            </p>

            <form method="post" action="/schedules/<?= $e($schedule['id']) ?>">
                <input type="hidden" name="_method" value="DELETE">
                <div class="form-actions">
                    <a class="button" href="/schedules"><?= $e(__('action.cancel')) ?></a>
                    <button class="button danger" type="submit"><?= $e(__('action.delete')) ?></button>
                </div>
            </form>
        </section>
    </main>
    <script src="../../assets/js/app.js"></script>
</body>
</html>
