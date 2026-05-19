<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('page.edit_reminder')) ?></title>
    <?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'theme_boot.php'; ?>
    <link rel="stylesheet" href="../../assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <?php $activeNav = 'reminders'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('nav.reminders')) ?></p>
                <h1><?= $e(__('page.edit_reminder')) ?></h1>
            </div>
        </section>

        <form class="panel form-stack" method="post" action="/reminders/<?= $e($reminder['id']) ?>">
            <input type="hidden" name="_method" value="PUT">
            <?php require __DIR__ . DIRECTORY_SEPARATOR . '_form.php'; ?>
            <div class="form-actions">
                <a class="button" href="/reminders"><?= $e(__('action.cancel')) ?></a>
                <button class="button primary" type="submit"><?= $e(__('action.save')) ?></button>
            </div>
        </form>
    </main>
    <script src="../../assets/js/app.js"></script>
</body>
</html>
