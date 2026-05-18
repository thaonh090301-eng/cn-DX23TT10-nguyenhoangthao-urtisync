<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title ?? 'Create Category') ?></title>
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <nav class="top-nav">
            <a href="/">Home</a>
            <a href="/categories" aria-current="page">Categories</a>
            <a href="/activities">Activities</a>
        </nav>

        <section class="page-header">
            <div>
                <p class="eyebrow">Categories</p>
                <h1>Create Category</h1>
            </div>
        </section>

        <form class="panel form-stack" method="post" action="/categories">
            <label>
                <span>Name</span>
                <input type="text" name="name" value="<?= $e($category['name'] ?? '') ?>" required>
                <?php if (!empty($errors['name'])): ?>
                    <small class="field-error"><?= $e($errors['name']) ?></small>
                <?php endif; ?>
            </label>

            <label>
                <span>Color</span>
                <input type="color" name="color" value="<?= $e($category['color'] ?? '#2563eb') ?>" required>
                <?php if (!empty($errors['color'])): ?>
                    <small class="field-error"><?= $e($errors['color']) ?></small>
                <?php endif; ?>
            </label>

            <label>
                <span>Sort Order</span>
                <input type="number" name="sort_order" value="<?= $e($category['sort_order'] ?? 0) ?>" min="0">
            </label>

            <div class="form-actions">
                <a class="button" href="/categories">Cancel</a>
                <button class="button primary" type="submit">Create</button>
            </div>
        </form>
    </main>
    <script src="../assets/js/app.js"></script>
</body>
</html>
