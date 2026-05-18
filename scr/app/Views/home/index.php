<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title ?? 'Personal Time Optimizer') ?></title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="page-shell">
        <section class="intro-panel">
            <p class="eyebrow">PHP MVC Skeleton</p>
            <h1><?= $e($title ?? 'Personal Time Optimizer') ?></h1>
            <p>
                Category and activity management are ready for the demo user.
                Schedules, time logs, gap analysis, and dashboard alerts are still upcoming.
            </p>
            <div class="home-actions">
                <a class="button primary" href="/categories">Manage Categories</a>
                <a class="button" href="/activities">Manage Activities</a>
            </div>
        </section>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
