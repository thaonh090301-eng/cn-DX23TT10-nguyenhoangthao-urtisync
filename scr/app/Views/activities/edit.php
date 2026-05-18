<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title ?? 'Edit Activity') ?></title>
    <link rel="stylesheet" href="../../assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <nav class="top-nav">
            <a href="/">Home</a>
            <a href="/categories">Categories</a>
            <a href="/activities" aria-current="page">Activities</a>
        </nav>

        <section class="page-header">
            <div>
                <p class="eyebrow">Activities</p>
                <h1>Edit Activity</h1>
            </div>
        </section>

        <form class="panel form-stack" method="post" action="/activities/<?= $e($activity['id']) ?>">
            <input type="hidden" name="_method" value="PUT">

            <label>
                <span>Category</span>
                <select name="category_id" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $e($category['id']) ?>" <?= ((int) ($activity['category_id'] ?? 0) === (int) $category['id']) ? 'selected' : '' ?>>
                            <?= $e($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['category_id'])): ?>
                    <small class="field-error"><?= $e($errors['category_id']) ?></small>
                <?php endif; ?>
            </label>

            <label>
                <span>Title</span>
                <input type="text" name="title" value="<?= $e($activity['title'] ?? '') ?>" required>
                <?php if (!empty($errors['title'])): ?>
                    <small class="field-error"><?= $e($errors['title']) ?></small>
                <?php endif; ?>
            </label>

            <label>
                <span>Description</span>
                <textarea name="description" rows="4"><?= $e($activity['description'] ?? '') ?></textarea>
            </label>

            <label>
                <span>Priority</span>
                <select name="priority" required>
                    <?php foreach ($priorities as $priority): ?>
                        <option value="<?= $e($priority) ?>" <?= (($activity['priority'] ?? 'medium') === $priority) ? 'selected' : '' ?>>
                            <?= $e(ucfirst($priority)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['priority'])): ?>
                    <small class="field-error"><?= $e($errors['priority']) ?></small>
                <?php endif; ?>
            </label>

            <label>
                <span>Estimated Minutes</span>
                <input type="number" name="estimated_minutes" value="<?= $e($activity['estimated_minutes'] ?? 30) ?>" min="1" required>
                <?php if (!empty($errors['estimated_minutes'])): ?>
                    <small class="field-error"><?= $e($errors['estimated_minutes']) ?></small>
                <?php endif; ?>
            </label>

            <label class="checkbox-row">
                <input type="checkbox" name="is_active" value="1" <?= ((int) ($activity['is_active'] ?? 1) === 1) ? 'checked' : '' ?>>
                <span>Active</span>
            </label>

            <div class="form-actions">
                <a class="button" href="/activities">Cancel</a>
                <button class="button primary" type="submit">Save</button>
            </div>
        </form>
    </main>
    <script src="../../assets/js/app.js"></script>
</body>
</html>
