<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('page.delete_category')) ?></title>
    <link rel="stylesheet" href="../../assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <?php $activeNav = 'categories'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('nav.categories')) ?></p>
                <h1><?= $e(__('page.delete_category')) ?></h1>
            </div>
        </section>

        <section class="panel form-stack">
            <?php if (!empty($errors['category'])): ?>
                <div class="alert danger"><?= $e($errors['category']) ?></div>
            <?php endif; ?>

            <p>
                <?= $e(__('message.delete_category')) ?>
                <strong><?= $e(display_category_name($category['name'])) ?></strong>
                <?= $e(__('message.category_has_activities', ['count' => $category['activities_count']])) ?>
            </p>

            <form method="post" action="/categories/<?= $e($category['id']) ?>">
                <input type="hidden" name="_method" value="DELETE">
                <div class="form-actions">
                    <a class="button" href="/categories"><?= $e(__('action.cancel')) ?></a>
                    <button class="button danger" type="submit"><?= $e(__('action.delete')) ?></button>
                </div>
            </form>
        </section>
    </main>
    <script src="../../assets/js/app.js"></script>
</body>
</html>
