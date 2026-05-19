<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('nav.reminders')) ?></title>
    <?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'theme_boot.php'; ?>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'reminders'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('reminder.eyebrow')) ?></p>
                <h1><?= $e(__('nav.reminders')) ?></h1>
            </div>
            <div class="header-actions">
                <button class="button" type="button" data-notification-permission><?= $e(__('reminder.allow_notifications')) ?></button>
                <a class="button primary" href="/reminders/create"><?= $e(__('action.new_reminder')) ?></a>
            </div>
        </section>

        <?php foreach (['success', 'warning', 'danger'] as $flashType): ?>
            <?php if (!empty($flash[$flashType])): ?>
                <div class="alert <?= $e($flashType) ?>"><?= $e($flash[$flashType]) ?></div>
            <?php endif; ?>
        <?php endforeach; ?>

        <section class="panel">
            <?php if ($reminders === []): ?>
                <div class="empty-state">
                    <p><?= $e(__('empty.reminders')) ?></p>
                    <a class="button primary" href="/reminders/create"><?= $e(__('action.new_reminder')) ?></a>
                </div>
            <?php else: ?>
                <div class="reminder-grid">
                    <?php foreach ($reminders as $reminder): ?>
                        <article class="reminder-card <?= (int) $reminder['is_active'] === 1 ? 'active' : 'inactive' ?>">
                            <div class="reminder-card-head">
                                <span class="status-pill <?= (int) $reminder['is_active'] === 1 ? 'success' : 'warning' ?>">
                                    <?= $e((int) $reminder['is_active'] === 1 ? __('status.active') : __('status.inactive')) ?>
                                </span>
                                <strong><?= $e(format_app_time($reminder['remind_time'])) ?></strong>
                            </div>
                            <h2><?= $e($reminder['title']) ?></h2>
                            <p><?= $e($reminder['note'] ?? '') ?></p>
                            <p class="help-text">
                                <?= $e(__('reminder.repeat.' . $reminder['repeat_type'])) ?>
                                <?php if ($reminder['repeat_type'] === 'weekly' && $reminder['day_of_week'] !== null): ?>
                                    &middot; <?= $e(__('day.' . $reminder['day_of_week'])) ?>
                                <?php endif; ?>
                            </p>
                            <div class="actions">
                                <a href="/reminders/<?= $e($reminder['id']) ?>/edit"><?= $e(__('action.edit')) ?></a>
                                <form method="post" action="/reminders/<?= $e($reminder['id']) ?>/toggle">
                                    <button class="button compact" type="submit">
                                        <?= $e((int) $reminder['is_active'] === 1 ? __('reminder.deactivate') : __('reminder.activate')) ?>
                                    </button>
                                </form>
                                <a class="danger-link" href="/reminders/<?= $e($reminder['id']) ?>/delete"><?= $e(__('action.delete')) ?></a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
