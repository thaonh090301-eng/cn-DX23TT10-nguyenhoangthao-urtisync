<?php

use App\Core\Lang;

$activeNav = $activeNav ?? '';
$navItems = [
    'home' => ['label' => __('nav.home'), 'href' => '/'],
    'dashboard' => ['label' => __('nav.dashboard'), 'href' => '/dashboard'],
    'assistant' => ['label' => __('nav.assistant'), 'href' => '/assistant'],
    'timetable' => ['label' => __('nav.timetable'), 'href' => '/timetable'],
    'reminders' => ['label' => __('nav.reminders'), 'href' => '/reminders'],
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
    'assistant' => [
        'title' => __('nav.assistant'),
        'subtitle' => __('assistant.eyebrow'),
        'quickLabel' => '',
        'quickHref' => '',
    ],
    'timetable' => [
        'title' => __('nav.timetable'),
        'subtitle' => __('timetable.eyebrow'),
        'quickLabel' => __('timetable.action.add_schedule'),
        'quickHref' => '/schedules/create',
    ],
    'reminders' => [
        'title' => __('nav.reminders'),
        'subtitle' => __('reminder.eyebrow'),
        'quickLabel' => __('action.new_reminder'),
        'quickHref' => '/reminders/create',
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
        'quickLabel' => __('time_report.action.unscheduled'),
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
        <?php if ($workspacePage['quickHref'] !== ''): ?>
            <a class="button primary" href="<?= $e($workspacePage['quickHref']) ?>">
                <?= $e($workspacePage['quickLabel']) ?>
            </a>
        <?php endif; ?>
        <div class="quick-add">
            <button
                class="button quick-add-trigger"
                type="button"
                data-quick-add-toggle
                aria-expanded="false"
                aria-controls="quick-add-panel"
            >
                <?= $e(__('quick_add.label')) ?>
            </button>
            <div class="quick-add-panel" id="quick-add-panel" data-quick-add-panel hidden>
                <a href="/activities/create"><?= $e(__('quick_add.activity')) ?></a>
                <a href="/schedules/create"><?= $e(__('quick_add.schedule')) ?></a>
                <a href="/time-logs/create"><?= $e(__('quick_add.time_log')) ?></a>
                <a href="/reminders/create"><?= $e(__('quick_add.reminder')) ?></a>
                <a href="/assistant"><?= $e(__('quick_add.assistant')) ?></a>
                <a href="/optimizer"><?= $e(__('quick_add.optimizer')) ?></a>
            </div>
        </div>
        <div class="personalization">
            <button
                class="button ghost personalization-trigger"
                type="button"
                data-personalization-toggle
                aria-expanded="false"
                aria-controls="personalization-panel"
            >
                <?= $e(__('ui.theme')) ?>
            </button>
            <div class="personalization-panel" id="personalization-panel" data-personalization-panel hidden>
                <div class="preference-group">
                    <span><?= $e(__('ui.theme_mode')) ?></span>
                    <div class="preference-options" role="group" aria-label="<?= $e(__('ui.theme_mode')) ?>">
                        <button class="preference-option" type="button" data-preference="theme" data-value="light" aria-pressed="false">
                            <?= $e(__('theme.light')) ?>
                        </button>
                        <button class="preference-option" type="button" data-preference="theme" data-value="dark" aria-pressed="false">
                            <?= $e(__('theme.dark')) ?>
                        </button>
                    </div>
                </div>

                <div class="preference-group">
                    <span><?= $e(__('ui.accent_color')) ?></span>
                    <div class="preference-options swatch-options" role="group" aria-label="<?= $e(__('ui.accent_color')) ?>">
                        <?php foreach (['blue', 'purple', 'green', 'orange'] as $accent): ?>
                            <button class="preference-option swatch-option" type="button" data-preference="accent" data-value="<?= $e($accent) ?>" aria-pressed="false">
                                <i class="accent-swatch accent-<?= $e($accent) ?>" aria-hidden="true"></i>
                                <?= $e(__('accent.' . $accent)) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="preference-group">
                    <span><?= $e(__('ui.density')) ?></span>
                    <div class="preference-options" role="group" aria-label="<?= $e(__('ui.density')) ?>">
                        <button class="preference-option" type="button" data-preference="density" data-value="comfortable" aria-pressed="false">
                            <?= $e(__('density.comfortable')) ?>
                        </button>
                        <button class="preference-option" type="button" data-preference="density" data-value="compact" aria-pressed="false">
                            <?= $e(__('density.compact')) ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
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

<div
    class="toast-config"
    data-toast-validation="<?= $e(__('toast.validation_error')) ?>"
    data-toast-dismiss="<?= $e(__('action.dismiss')) ?>"
    data-reminder-template="<?= $e(__('reminder.toast_template')) ?>"
    data-notification-granted="<?= $e(__('reminder.notification_granted')) ?>"
    data-notification-denied="<?= $e(__('reminder.notification_denied')) ?>"
    hidden
></div>
<div class="toast-region" data-toast-region aria-live="polite" aria-atomic="false"></div>

<div class="modal-backdrop" data-delete-modal hidden>
    <section
        class="confirm-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="delete-modal-title"
        aria-describedby="delete-modal-message"
    >
        <p class="eyebrow"><?= $e(__('modal.confirmation')) ?></p>
        <h2 id="delete-modal-title"><?= $e(__('modal.delete_title')) ?></h2>
        <p id="delete-modal-message" data-delete-modal-message><?= $e(__('modal.delete_message')) ?></p>
        <div class="form-actions">
            <button class="button" type="button" data-delete-modal-cancel><?= $e(__('action.cancel')) ?></button>
            <button class="button danger" type="button" data-delete-modal-confirm><?= $e(__('action.confirm_delete')) ?></button>
        </div>
    </section>
</div>
