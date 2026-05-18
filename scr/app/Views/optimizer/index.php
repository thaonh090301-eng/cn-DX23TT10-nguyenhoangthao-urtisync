<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('nav.optimizer')) ?></title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'optimizer'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('section.gap_analysis')) ?></p>
                <h1><?= $e(__('nav.optimizer')) ?></h1>
            </div>
        </section>

        <?php if (!empty($flash['success'])): ?>
            <div class="alert success"><?= $e($flash['success']) ?></div>
        <?php endif; ?>

        <?php if (!empty($flash['danger'])): ?>
            <div class="alert danger"><?= $e($flash['danger']) ?></div>
        <?php endif; ?>

        <section class="panel dashboard-section" id="optimizer-form">
            <?php if ($activities === []): ?>
                <div class="alert danger"><?= $e(__('message.create_activity_before_suggestions')) ?></div>
            <?php endif; ?>

            <form class="form-stack" method="post" action="/optimizer">
                <label>
                    <span><?= $e(__('label.activity')) ?></span>
                    <select name="activity_id" required>
                        <option value=""><?= $e(__('option.choose_activity')) ?></option>
                        <?php foreach ($activities as $activity): ?>
                            <option value="<?= $e($activity['id']) ?>" <?= ((int) ($input['activity_id'] ?? 0) === (int) $activity['id']) ? 'selected' : '' ?>>
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
                        <span><?= $e(__('label.range_start')) ?></span>
                        <input type="date" name="range_start" value="<?= $e($input['range_start'] ?? '') ?>" required>
                        <?php if (!empty($errors['range_start'])): ?>
                            <small class="field-error"><?= $e($errors['range_start']) ?></small>
                        <?php endif; ?>
                    </label>

                    <label>
                        <span><?= $e(__('label.range_end')) ?></span>
                        <input type="date" name="range_end" value="<?= $e($input['range_end'] ?? '') ?>" required>
                        <?php if (!empty($errors['range_end'])): ?>
                            <small class="field-error"><?= $e($errors['range_end']) ?></small>
                        <?php endif; ?>
                    </label>
                </div>

                <div class="form-grid">
                    <label>
                        <span><?= $e(__('label.required_duration')) ?></span>
                        <input type="number" name="required_minutes" value="<?= $e($input['required_minutes'] ?? 30) ?>" min="1" required>
                        <?php if (!empty($errors['required_minutes'])): ?>
                            <small class="field-error"><?= $e($errors['required_minutes']) ?></small>
                        <?php endif; ?>
                    </label>

                    <label>
                        <span><?= $e(__('label.earliest_allowed_time')) ?></span>
                        <input type="time" name="earliest_time" value="<?= $e($input['earliest_time'] ?? '08:00') ?>" required>
                        <?php if (!empty($errors['earliest_time'])): ?>
                            <small class="field-error"><?= $e($errors['earliest_time']) ?></small>
                        <?php endif; ?>
                    </label>
                </div>

                <label>
                    <span><?= $e(__('label.latest_allowed_time')) ?></span>
                    <input type="time" name="latest_time" value="<?= $e($input['latest_time'] ?? '18:00') ?>" required>
                    <?php if (!empty($errors['latest_time'])): ?>
                        <small class="field-error"><?= $e($errors['latest_time']) ?></small>
                    <?php endif; ?>
                </label>

                <p class="help-text">
                    <?= $e(__('optimizer.help_range')) ?>
                </p>

                <div class="form-actions">
                    <button class="button primary" type="submit"><?= $e(__('action.find_suggestions')) ?></button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="section-heading">
                <div>
                    <p class="eyebrow"><?= $e(__('section.suggestions')) ?></p>
                    <h2><?= $e(__('optimizer.available_slots')) ?></h2>
                </div>
            </div>

            <?php if ($suggestions === []): ?>
                <p class="empty-state"><?= $e(__('optimizer.empty')) ?></p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th><?= $e(__('label.suggested_start')) ?></th>
                                <th><?= $e(__('label.suggested_end')) ?></th>
                                <th><?= $e(__('label.gap')) ?></th>
                                <th><?= $e(__('label.activity')) ?></th>
                                <th><?= $e(__('label.category')) ?></th>
                                <th><?= $e(__('label.score')) ?></th>
                                <th><?= $e(__('label.reason')) ?></th>
                                <th><?= $e(__('label.action')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($suggestions as $index => $suggestion): ?>
                                <tr class="<?= $index === 0 ? 'best-suggestion' : '' ?>">
                                    <td><?= $e($suggestion['start_at']) ?></td>
                                    <td><?= $e($suggestion['end_at']) ?></td>
                                    <td><?= $e($suggestion['gap_minutes']) ?> <?= $e(__('unit.min')) ?></td>
                                    <td><?= $e(display_activity_title($suggestion['activity_title'])) ?></td>
                                    <td>
                                        <span class="color-chip" style="--chip: <?= $e($suggestion['category_color']) ?>"></span>
                                        <?= $e(display_category_name($suggestion['category_name'])) ?>
                                    </td>
                                    <td>
                                        <span class="score-pill"><?= $e($suggestion['score']) ?></span>
                                        <?php if ($index === 0): ?>
                                            <span class="best-pill"><?= $e(__('ui.best')) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $e($suggestion['reason']) ?></td>
                                    <td class="suggestion-action">
                                        <form method="post" action="/optimizer/schedule">
                                            <input type="hidden" name="activity_id" value="<?= $e($suggestion['activity_id']) ?>">
                                            <input type="hidden" name="start_at" value="<?= $e($suggestion['start_at']) ?>">
                                            <input type="hidden" name="end_at" value="<?= $e($suggestion['end_at']) ?>">
                                            <button class="button primary compact" type="submit"><?= $e(__('action.create_schedule')) ?></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
