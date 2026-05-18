<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('page.create_activity')) ?></title>
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <?php $activeNav = 'activities'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('nav.activities')) ?></p>
                <h1><?= $e(__('page.create_activity')) ?></h1>
            </div>
        </section>

        <form class="panel form-stack" method="post" action="/activities">
            <label>
                <span><?= $e(__('label.category')) ?></span>
                <select name="category_id" required>
                    <option value=""><?= $e(__('option.choose_category')) ?></option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $e($category['id']) ?>" <?= ((int) ($activity['category_id'] ?? 0) === (int) $category['id']) ? 'selected' : '' ?>>
                            <?= $e(display_category_name($category['name'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['category_id'])): ?>
                    <small class="field-error"><?= $e($errors['category_id']) ?></small>
                <?php endif; ?>
            </label>

            <label>
                <span><?= $e(__('label.title')) ?></span>
                <input type="text" name="title" value="<?= $e($activity['title'] ?? '') ?>" required>
                <?php if (!empty($errors['title'])): ?>
                    <small class="field-error"><?= $e($errors['title']) ?></small>
                <?php endif; ?>
            </label>

            <label>
                <span><?= $e(__('label.description')) ?></span>
                <textarea name="description" rows="4"><?= $e($activity['description'] ?? '') ?></textarea>
            </label>

            <label>
                <span><?= $e(__('label.priority')) ?></span>
                <select name="priority" required>
                    <?php foreach ($priorities as $priority): ?>
                        <option value="<?= $e($priority) ?>" <?= (($activity['priority'] ?? 'medium') === $priority) ? 'selected' : '' ?>>
                            <?= $e(__('priority.' . $priority)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['priority'])): ?>
                    <small class="field-error"><?= $e($errors['priority']) ?></small>
                <?php endif; ?>
            </label>

            <label>
                <span><?= $e(__('label.estimated_minutes')) ?></span>
                <input type="number" name="estimated_minutes" value="<?= $e($activity['estimated_minutes'] ?? 30) ?>" min="1" required>
                <?php if (!empty($errors['estimated_minutes'])): ?>
                    <small class="field-error"><?= $e($errors['estimated_minutes']) ?></small>
                <?php endif; ?>
            </label>

            <label class="checkbox-row">
                <input type="checkbox" name="is_active" value="1" <?= ((int) ($activity['is_active'] ?? 1) === 1) ? 'checked' : '' ?>>
                <span><?= $e(__('status.active')) ?></span>
            </label>

            <div class="form-actions">
                <a class="button" href="/activities"><?= $e(__('action.cancel')) ?></a>
                <button class="button primary" type="submit"><?= $e(__('action.create')) ?></button>
            </div>
        </form>
    </main>
    <script src="../assets/js/app.js"></script>
</body>
</html>
