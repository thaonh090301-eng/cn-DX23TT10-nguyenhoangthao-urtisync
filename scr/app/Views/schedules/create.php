<?php
$dateTimeLocal = static fn (mixed $value): string => str_replace(' ', 'T', substr((string) $value, 0, 16));
?>
<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('page.create_schedule')) ?></title>
    <?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'theme_boot.php'; ?>
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <?php $activeNav = 'schedules'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('nav.schedules')) ?></p>
                <h1><?= $e(__('page.create_schedule')) ?></h1>
            </div>
        </section>

        <?php if ($activities === []): ?>
            <div class="alert danger"><?= $e(__('message.create_activity_before_schedules')) ?></div>
        <?php endif; ?>

        <form class="panel form-stack" method="post" action="/schedules">
            <label>
                <span><?= $e(__('label.activity')) ?></span>
                <select name="activity_id" required>
                    <option value=""><?= $e(__('option.choose_activity')) ?></option>
                    <?php foreach ($activities as $activity): ?>
                        <option value="<?= $e($activity['id']) ?>" <?= ((int) ($schedule['activity_id'] ?? 0) === (int) $activity['id']) ? 'selected' : '' ?>>
                            <?= $e(display_activity_title($activity['title'])) ?> - <?= $e(display_category_name($activity['category_name'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['activity_id'])): ?>
                    <small class="field-error"><?= $e($errors['activity_id']) ?></small>
                <?php endif; ?>
            </label>

            <label>
                <span><?= $e(__('label.title')) ?></span>
                <input type="text" name="title" value="<?= $e($schedule['title'] ?? '') ?>" required>
                <?php if (!empty($errors['title'])): ?>
                    <small class="field-error"><?= $e($errors['title']) ?></small>
                <?php endif; ?>
            </label>

            <div class="form-grid">
                <label>
                    <span><?= $e(__('label.start_time')) ?></span>
                    <input type="datetime-local" name="start_at" value="<?= $e($dateTimeLocal($schedule['start_at'] ?? '')) ?>" required>
                    <?php if (!empty($errors['start_at'])): ?>
                        <small class="field-error"><?= $e($errors['start_at']) ?></small>
                    <?php endif; ?>
                </label>

                <label>
                    <span><?= $e(__('label.end_time')) ?></span>
                    <input type="datetime-local" name="end_at" value="<?= $e($dateTimeLocal($schedule['end_at'] ?? '')) ?>" required>
                    <?php if (!empty($errors['end_at'])): ?>
                        <small class="field-error"><?= $e($errors['end_at']) ?></small>
                    <?php endif; ?>
                </label>
            </div>

            <label>
                <span><?= $e(__('label.notes')) ?></span>
                <textarea name="notes" rows="4"><?= $e($schedule['notes'] ?? '') ?></textarea>
            </label>

            <div class="form-actions">
                <a class="button" href="/schedules"><?= $e(__('action.cancel')) ?></a>
                <button class="button primary" type="submit"><?= $e(__('action.create')) ?></button>
            </div>
        </form>
    </main>
    <script src="../assets/js/app.js"></script>
</body>
</html>
