<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('page.delete_reminder')) ?></title>
    <?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'theme_boot.php'; ?>
    <link rel="stylesheet" href="../../assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <?php $activeNav = 'reminders'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('nav.reminders')) ?></p>
                <h1><?= $e(__('page.delete_reminder')) ?></h1>
            </div>
        </section>

        <section class="panel form-stack">
            <p><?= $e(__('message.delete_reminder')) ?></p>
            <div class="delete-summary">
                <strong><?= $e($reminder['title']) ?></strong>
                <span><?= $e(format_app_time($reminder['remind_time'])) ?></span>
                <?php if (!empty($reminder['note'])): ?>
                    <p><?= $e($reminder['note']) ?></p>
                <?php endif; ?>
            </div>

            <form class="form-actions" method="post" action="/reminders/<?= $e($reminder['id']) ?>">
                <input type="hidden" name="_method" value="DELETE">
                <a class="button" href="/reminders"><?= $e(__('action.cancel')) ?></a>
                <button class="button danger" type="submit"><?= $e(__('action.delete')) ?></button>
            </form>
        </section>
    </main>
    <script src="../../assets/js/app.js"></script>
</body>
</html>
