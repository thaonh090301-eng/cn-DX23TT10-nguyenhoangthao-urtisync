<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title ?? 'Personal Time Optimizer') ?></title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <?php $activeNav = 'home'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="intro-panel">
            <p class="eyebrow">PHP MVC Skeleton</p>
            <h1><?= $e($title ?? 'Personal Time Optimizer') ?></h1>
            <p>
                Category and activity management are ready for the demo user.
                Schedule management, calendar viewing, and time logging are ready.
                Dashboard statistics, basic alerts, and gap suggestions are ready.
            </p>
        </section>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
