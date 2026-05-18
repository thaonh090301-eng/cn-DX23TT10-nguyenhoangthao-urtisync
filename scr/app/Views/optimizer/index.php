<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title ?? 'Optimizer') ?></title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'optimizer'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow">Gap Analysis</p>
                <h1>Optimizer</h1>
            </div>
        </section>

        <?php if (!empty($flash['success'])): ?>
            <div class="alert success"><?= $e($flash['success']) ?></div>
        <?php endif; ?>

        <?php if (!empty($flash['danger'])): ?>
            <div class="alert danger"><?= $e($flash['danger']) ?></div>
        <?php endif; ?>

        <section class="panel dashboard-section">
            <?php if ($activities === []): ?>
                <div class="alert danger">Create at least one activity before requesting suggestions.</div>
            <?php endif; ?>

            <form class="form-stack" method="post" action="/optimizer">
                <label>
                    <span>Activity</span>
                    <select name="activity_id" required>
                        <option value="">Choose activity</option>
                        <?php foreach ($activities as $activity): ?>
                            <option value="<?= $e($activity['id']) ?>" <?= ((int) ($input['activity_id'] ?? 0) === (int) $activity['id']) ? 'selected' : '' ?>>
                                <?= $e($activity['title']) ?> - <?= $e($activity['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['activity_id'])): ?>
                        <small class="field-error"><?= $e($errors['activity_id']) ?></small>
                    <?php endif; ?>
                </label>

                <div class="form-grid">
                    <label>
                        <span>Range Start</span>
                        <input type="date" name="range_start" value="<?= $e($input['range_start'] ?? '') ?>" required>
                        <?php if (!empty($errors['range_start'])): ?>
                            <small class="field-error"><?= $e($errors['range_start']) ?></small>
                        <?php endif; ?>
                    </label>

                    <label>
                        <span>Range End</span>
                        <input type="date" name="range_end" value="<?= $e($input['range_end'] ?? '') ?>" required>
                        <?php if (!empty($errors['range_end'])): ?>
                            <small class="field-error"><?= $e($errors['range_end']) ?></small>
                        <?php endif; ?>
                    </label>
                </div>

                <div class="form-grid">
                    <label>
                        <span>Required Duration</span>
                        <input type="number" name="required_minutes" value="<?= $e($input['required_minutes'] ?? 30) ?>" min="1" required>
                        <?php if (!empty($errors['required_minutes'])): ?>
                            <small class="field-error"><?= $e($errors['required_minutes']) ?></small>
                        <?php endif; ?>
                    </label>

                    <label>
                        <span>Earliest Allowed Time</span>
                        <input type="time" name="earliest_time" value="<?= $e($input['earliest_time'] ?? '08:00') ?>" required>
                        <?php if (!empty($errors['earliest_time'])): ?>
                            <small class="field-error"><?= $e($errors['earliest_time']) ?></small>
                        <?php endif; ?>
                    </label>
                </div>

                <label>
                    <span>Latest Allowed Time</span>
                    <input type="time" name="latest_time" value="<?= $e($input['latest_time'] ?? '18:00') ?>" required>
                    <?php if (!empty($errors['latest_time'])): ?>
                        <small class="field-error"><?= $e($errors['latest_time']) ?></small>
                    <?php endif; ?>
                </label>

                <p class="help-text">
                    Range end is exclusive. For a single-day search, use tomorrow as the range end.
                </p>

                <div class="form-actions">
                    <button class="button primary" type="submit">Find Suggestions</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Suggestions</p>
                    <h2>Available Slots</h2>
                </div>
            </div>

            <?php if ($suggestions === []): ?>
                <p class="empty-state">No suggestions to show yet, or no free gap matched the selected duration.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Suggested Start</th>
                                <th>Suggested End</th>
                                <th>Gap</th>
                                <th>Activity</th>
                                <th>Category</th>
                                <th>Score</th>
                                <th>Reason</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($suggestions as $suggestion): ?>
                                <tr>
                                    <td><?= $e($suggestion['start_at']) ?></td>
                                    <td><?= $e($suggestion['end_at']) ?></td>
                                    <td><?= $e($suggestion['gap_minutes']) ?> min</td>
                                    <td><?= $e($suggestion['activity_title']) ?></td>
                                    <td>
                                        <span class="color-chip" style="--chip: <?= $e($suggestion['category_color']) ?>"></span>
                                        <?= $e($suggestion['category_name']) ?>
                                    </td>
                                    <td><?= $e($suggestion['score']) ?></td>
                                    <td><?= $e($suggestion['reason']) ?></td>
                                    <td>
                                        <form method="post" action="/optimizer/schedule">
                                            <input type="hidden" name="activity_id" value="<?= $e($suggestion['activity_id']) ?>">
                                            <input type="hidden" name="start_at" value="<?= $e($suggestion['start_at']) ?>">
                                            <input type="hidden" name="end_at" value="<?= $e($suggestion['end_at']) ?>">
                                            <button class="button primary compact" type="submit">Create</button>
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
