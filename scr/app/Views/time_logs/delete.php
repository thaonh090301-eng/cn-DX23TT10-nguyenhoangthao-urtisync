<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('page.delete_time_log')) ?></title>
    <link rel="stylesheet" href="../../assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <?php $activeNav = 'time_logs'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('nav.time_logs')) ?></p>
                <h1><?= $e(__('page.delete_time_log')) ?></h1>
            </div>
        </section>

        <section class="panel form-stack">
            <p>
                <?= $e(__('message.delete_time_log')) ?>
                <strong><?= $e(display_activity_title($timeLog['activity_title'])) ?></strong>
                <?= $e(__('message.logged_from_to', ['start' => $timeLog['started_at'], 'end' => $timeLog['ended_at']])) ?>
            </p>

            <form method="post" action="/time-logs/<?= $e($timeLog['id']) ?>">
                <input type="hidden" name="_method" value="DELETE">
                <div class="form-actions">
                    <a class="button" href="/time-logs"><?= $e(__('action.cancel')) ?></a>
                    <button class="button danger" type="submit"><?= $e(__('action.delete')) ?></button>
                </div>
            </form>
        </section>
    </main>
    <script src="../../assets/js/app.js"></script>
</body>
</html>
