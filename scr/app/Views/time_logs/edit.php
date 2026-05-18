<?php
$dateTimeLocal = static fn (mixed $value): string => str_replace(' ', 'T', substr((string) $value, 0, 16));
?>
<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('page.edit_time_log')) ?></title>
    <link rel="stylesheet" href="../../assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <?php $activeNav = 'time_logs'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('nav.time_logs')) ?></p>
                <h1><?= $e(__('page.edit_time_log')) ?></h1>
            </div>
        </section>

        <form class="panel form-stack" method="post" action="/time-logs/<?= $e($timeLog['id']) ?>">
            <input type="hidden" name="_method" value="PUT">

            <label>
                <span><?= $e(__('label.activity')) ?></span>
                <select name="activity_id" required>
                    <?php foreach ($activities as $activity): ?>
                        <option value="<?= $e($activity['id']) ?>" <?= ((int) ($timeLog['activity_id'] ?? 0) === (int) $activity['id']) ? 'selected' : '' ?>>
                            <?= $e(display_activity_title($activity['title'])) ?> - <?= $e(display_category_name($activity['category_name'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['activity_id'])): ?>
                    <small class="field-error"><?= $e($errors['activity_id']) ?></small>
                <?php endif; ?>
            </label>

            <div class="form-grid">
                <label>
                    <span><?= $e(__('label.actual_start')) ?></span>
                    <input type="datetime-local" name="started_at" value="<?= $e($dateTimeLocal($timeLog['started_at'] ?? '')) ?>" required>
                    <?php if (!empty($errors['started_at'])): ?>
                        <small class="field-error"><?= $e($errors['started_at']) ?></small>
                    <?php endif; ?>
                </label>

                <label>
                    <span><?= $e(__('label.actual_end')) ?></span>
                    <input type="datetime-local" name="ended_at" value="<?= $e($dateTimeLocal($timeLog['ended_at'] ?? '')) ?>" required>
                    <?php if (!empty($errors['ended_at'])): ?>
                        <small class="field-error"><?= $e($errors['ended_at']) ?></small>
                    <?php endif; ?>
                </label>
            </div>

            <p class="help-text">
                <?= $e(__('message.duration_recalculated')) ?>
            </p>

            <label>
                <span><?= $e(__('label.note')) ?></span>
                <textarea name="note" rows="4"><?= $e($timeLog['note'] ?? '') ?></textarea>
            </label>

            <div class="form-actions">
                <a class="button" href="/time-logs"><?= $e(__('action.cancel')) ?></a>
                <button class="button primary" type="submit"><?= $e(__('action.save')) ?></button>
            </div>
        </form>
    </main>
    <script src="../../assets/js/app.js"></script>
</body>
</html>
