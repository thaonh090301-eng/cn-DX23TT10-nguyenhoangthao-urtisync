<?php

use App\Core\Lang;

$activeNav = $activeNav ?? '';
$navItems = [
    'home' => ['label' => __('nav.home'), 'href' => '/'],
    'dashboard' => ['label' => __('nav.dashboard'), 'href' => '/dashboard'],
    'optimizer' => ['label' => __('nav.optimizer'), 'href' => '/optimizer'],
    'calendar' => ['label' => __('nav.calendar'), 'href' => '/calendar'],
    'schedules' => ['label' => __('nav.schedules'), 'href' => '/schedules'],
    'activities' => ['label' => __('nav.activities'), 'href' => '/activities'],
    'categories' => ['label' => __('nav.categories'), 'href' => '/categories'],
    'time_logs' => ['label' => __('nav.time_logs'), 'href' => '/time-logs'],
];

$workspacePages = [
    'home' => [
        'title' => __('app.title'),
        'subtitle' => __('ui.focus_planning'),
        'quickLabel' => __('nav.dashboard'),
        'quickHref' => '/dashboard',
    ],
    'dashboard' => [
        'title' => __('nav.dashboard'),
        'subtitle' => __('section.overview'),
        'quickLabel' => __('action.new_schedule'),
        'quickHref' => '/schedules/create',
    ],
    'optimizer' => [
        'title' => __('nav.optimizer'),
        'subtitle' => __('section.gap_analysis'),
        'quickLabel' => __('action.find_suggestions'),
        'quickHref' => '#optimizer-form',
    ],
    'calendar' => [
        'title' => __('nav.calendar'),
        'subtitle' => __('page.schedule_calendar'),
        'quickLabel' => __('action.new_schedule'),
        'quickHref' => '/schedules/create',
    ],
    'schedules' => [
        'title' => __('nav.schedules'),
        'subtitle' => __('section.management'),
        'quickLabel' => __('action.new_schedule'),
        'quickHref' => '/schedules/create',
    ],
    'activities' => [
        'title' => __('nav.activities'),
        'subtitle' => __('section.management'),
        'quickLabel' => __('action.new_activity'),
        'quickHref' => '/activities/create',
    ],
    'categories' => [
        'title' => __('nav.categories'),
        'subtitle' => __('section.management'),
        'quickLabel' => __('action.new_category'),
        'quickHref' => '/categories/create',
    ],
    'time_logs' => [
        'title' => __('nav.time_logs'),
        'subtitle' => __('section.tracking'),
        'quickLabel' => __('action.new_time_log'),
        'quickHref' => '/time-logs/create',
    ],
];

$workspacePage = $workspacePages[$activeNav] ?? $workspacePages['home'];
$currentLocale = Lang::locale();
?>
<aside class="workspace-sidebar">
    <a class="workspace-brand" href="/">
        <span class="workspace-mark">PTO</span>
        <span>
            <strong><?= $e(__('app.sidebar_title')) ?></strong>
            <small><?= $e(__('app.sidebar_subtitle')) ?></small>
        </span>
    </a>

    <nav class="workspace-nav" aria-label="<?= $e(__('ui.workspace')) ?>">
        <?php foreach ($navItems as $key => $item): ?>
            <a href="<?= $e($item['href']) ?>" <?= $activeNav === $key ? 'aria-current="page"' : '' ?>>
                <span class="nav-glyph" aria-hidden="true"></span>
                <?= $e($item['label']) ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="workspace-sidebar-footer">
        <span><?= $e(__('ui.focus_planning')) ?></span>
    </div>
</aside>

<header class="workspace-topbar">
    <div class="topbar-title">
        <p class="eyebrow"><?= $e($workspacePage['subtitle']) ?></p>
        <h1><?= $e($workspacePage['title']) ?></h1>
    </div>

    <div class="topbar-actions">
        <a class="button primary" href="<?= $e($workspacePage['quickHref']) ?>">
            <?= $e($workspacePage['quickLabel']) ?>
        </a>
        <button class="button ghost theme-toggle" type="button" data-theme-toggle aria-label="<?= $e(__('ui.theme')) ?>">
            <?= $e(__('ui.theme')) ?>
        </button>
        <div class="language-switch" aria-label="<?= $e(__('language.switcher')) ?>">
            <a href="/lang?locale=vi" <?= $currentLocale === 'vi' ? 'aria-current="true"' : '' ?>>
                <?= $e(__('language.vi')) ?>
            </a>
            <a href="/lang?locale=en" <?= $currentLocale === 'en' ? 'aria-current="true"' : '' ?>>
                <?= $e(__('language.en')) ?>
            </a>
        </div>
    </div>
</header>
