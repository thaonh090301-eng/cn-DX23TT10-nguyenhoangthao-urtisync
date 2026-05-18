<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title ?? 'Delete Activity') ?></title>
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
                <h1>Delete Activity</h1>
            </div>
        </section>

        <section class="panel form-stack">
            <p>
                Delete <strong><?= $e($activity['title']) ?></strong>
                from <?= $e($activity['category_name']) ?>?
            </p>

            <form method="post" action="/activities/<?= $e($activity['id']) ?>">
                <input type="hidden" name="_method" value="DELETE">
                <div class="form-actions">
                    <a class="button" href="/activities">Cancel</a>
                    <button class="button danger" type="submit">Delete</button>
                </div>
            </form>
        </section>
    </main>
    <script src="../../assets/js/app.js"></script>
</body>
</html>
