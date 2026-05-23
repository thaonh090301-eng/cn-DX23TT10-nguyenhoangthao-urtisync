<?php
$wellnessIcons = [
    'droplet' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3.5s-6 6.5-6 10.5a6 6 0 0 0 12 0c0-4-6-10.5-6-10.5Z"></path><path d="M9.5 15.5a2.7 2.7 0 0 0 3 2.2"></path></svg>',
    'dumbbell' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 8v8"></path><path d="M9 7v10"></path><path d="M15 7v10"></path><path d="M18 8v8"></path><path d="M9 12h6"></path><path d="M3.5 10v4"></path><path d="M20.5 10v4"></path></svg>',
    'leaf' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 19c9 0 14-6 14-14v-1h-1C10 4 4 9 4 17v2h1Z"></path><path d="M4 19c4-5 8-8 14-10"></path></svg>',
    'book' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5.5A3 3 0 0 1 8 3h11v16H8a3 3 0 0 0-3 3Z"></path><path d="M5 5.5V22"></path><path d="M9 7h6"></path><path d="M9 11h6"></path></svg>',
    'heart' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20s-7-4.6-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.4-7 10-7 10Z"></path><path d="M19 3l.6 1.6L21 5l-1.4.4L19 7l-.6-1.6L17 5l1.4-.4Z"></path></svg>',
];
$wellnessIcon = static fn (string $icon): string => $wellnessIcons[$icon] ?? $wellnessIcons['heart'];
?>
<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('app.title')) ?></title>
    <?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'theme_boot.php'; ?>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="home-page">
    <main class="app-shell narrow home-shell">
        <?php $activeNav = 'home'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="intro-panel">
            <p class="eyebrow"><?= $e(__('home.eyebrow')) ?></p>
            <h1><?= $e(__('app.title')) ?></h1>
            <p><?= $e(__('home.description')) ?></p>
        </section>

        <section class="wellness-section" aria-labelledby="wellness-heading">
            <div class="section-heading wellness-heading">
                <div>
                    <p class="eyebrow"><?= $e(__('home.wellness.eyebrow')) ?></p>
                    <h2 id="wellness-heading"><?= $e(__('home.wellness.title')) ?></h2>
                    <p><?= $e(__('home.wellness.description')) ?></p>
                </div>
            </div>

            <div class="wellness-grid">
                <?php foreach (($wellnessPosts ?? []) as $post): ?>
                    <article class="wellness-card wellness-card-<?= $e($post['tone']) ?>">
                        <div class="wellness-card-header">
                            <span class="wellness-marker" aria-hidden="true"><?= $wellnessIcon((string) ($post['icon'] ?? 'heart')) ?></span>
                            <span class="wellness-tag"><?= $e($post['tag']) ?></span>
                        </div>
                        <h3><?= $e($post['title']) ?></h3>
                        <p><?= $e($post['description']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
