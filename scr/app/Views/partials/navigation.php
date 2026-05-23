<?php

use App\Core\Lang;

$activeNav = $activeNav ?? '';
$navItems = [
    'home' => ['label' => __('nav.home'), 'href' => '/'],
    'dashboard' => ['label' => __('nav.dashboard'), 'href' => '/dashboard'],
    'assistant' => ['label' => __('nav.assistant'), 'href' => '/assistant'],
    'focus' => ['label' => __('nav.focus'), 'href' => '/focus'],
    'timetable' => ['label' => __('nav.timetable'), 'href' => '/timetable'],
    'reminders' => ['label' => __('nav.reminders'), 'href' => '/reminders'],
    'important_dates' => ['label' => __('nav.important_dates'), 'href' => '/important-dates'],
    'optimizer' => ['label' => __('nav.optimizer'), 'href' => '/optimizer'],
    'calendar' => ['label' => __('nav.calendar'), 'href' => '/calendar'],
    'schedules' => ['label' => __('nav.schedules'), 'href' => '/schedules'],
    'activities' => ['label' => __('nav.activities'), 'href' => '/activities'],
    'categories' => ['label' => __('nav.categories'), 'href' => '/categories'],
    'time_logs' => ['label' => __('nav.time_logs'), 'href' => '/time-logs'],
    'account' => ['label' => __('nav.account'), 'href' => '/account'],
];

$navIcons = [
    'home' => '<svg viewBox="0 0 24 24"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/></svg>',
    'dashboard' => '<svg viewBox="0 0 24 24"><path d="M4 19V5"/><path d="M4 19h16"/><path d="M8 16v-5"/><path d="M12 16V8"/><path d="M16 16v-3"/></svg>',
    'assistant' => '<svg viewBox="0 0 24 24"><path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8z"/><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z"/></svg>',
    'focus' => '<svg viewBox="0 0 24 24"><path d="M9 2h6"/><path d="M12 8v5l3 2"/><circle cx="12" cy="13" r="8"/></svg>',
    'timetable' => '<svg viewBox="0 0 24 24"><path d="M7 3v4"/><path d="M17 3v4"/><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 10h16"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/></svg>',
    'reminders' => '<svg viewBox="0 0 24 24"><path d="M6 17h12"/><path d="M8 17V10a4 4 0 0 1 8 0v7"/><path d="M10 19a2 2 0 0 0 4 0"/></svg>',
    'important_dates' => '<svg viewBox="0 0 24 24"><path d="M12 3l2.6 5.3 5.9.9-4.2 4.1 1 5.8L12 16.3 6.7 19.1l1-5.8-4.2-4.1 5.9-.9z"/></svg>',
    'optimizer' => '<svg viewBox="0 0 24 24"><path d="M9 18h6"/><path d="M10 22h4"/><path d="M8 14a6 6 0 1 1 8 0c-.8.7-1 1.5-1 2H9c0-.5-.2-1.3-1-2z"/></svg>',
    'calendar' => '<svg viewBox="0 0 24 24"><path d="M7 3v4"/><path d="M17 3v4"/><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 10h16"/><path d="M8 14h3"/><path d="M13 14h3"/><path d="M8 17h3"/></svg>',
    'schedules' => '<svg viewBox="0 0 24 24"><path d="M7 3v4"/><path d="M17 3v4"/><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 10h16"/><path d="M8 15l2 2 5-5"/></svg>',
    'activities' => '<svg viewBox="0 0 24 24"><path d="M8 6h12"/><path d="M8 12h12"/><path d="M8 18h12"/><path d="M4 6l1 1 2-2"/><path d="M4 12l1 1 2-2"/><path d="M4 18l1 1 2-2"/></svg>',
    'categories' => '<svg viewBox="0 0 24 24"><path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>',
    'time_logs' => '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"/><path d="M12 8v5l3 2"/><path d="M4 4l3 3"/></svg>',
    'account' => '<svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>',
];

$workspacePages = [
    'home' => [
        'title' => __('app.title'),
        'subtitle' => __('ui.focus_planning'),
        'quickLabel' => '',
        'quickHref' => '',
    ],
    'dashboard' => [
        'title' => __('nav.dashboard'),
        'subtitle' => __('section.overview'),
        'quickLabel' => '',
        'quickHref' => '',
    ],
    'assistant' => [
        'title' => __('nav.assistant'),
        'subtitle' => __('assistant.eyebrow'),
        'quickLabel' => '',
        'quickHref' => '',
    ],
    'focus' => [
        'title' => __('nav.focus'),
        'subtitle' => __('focus.eyebrow'),
        'quickLabel' => '',
        'quickHref' => '',
    ],
    'timetable' => [
        'title' => __('nav.timetable'),
        'subtitle' => __('timetable.eyebrow'),
        'quickLabel' => '',
        'quickHref' => '',
    ],
    'reminders' => [
        'title' => __('nav.reminders'),
        'subtitle' => __('reminder.eyebrow'),
        'quickLabel' => '',
        'quickHref' => '',
    ],
    'important_dates' => [
        'title' => __('nav.important_dates'),
        'subtitle' => __('important_date.eyebrow'),
        'quickLabel' => '',
        'quickHref' => '',
    ],
    'optimizer' => [
        'title' => __('nav.optimizer'),
        'subtitle' => __('section.gap_analysis'),
        'quickLabel' => '',
        'quickHref' => '',
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
        'quickLabel' => '',
        'quickHref' => '',
    ],
    'activities' => [
        'title' => __('nav.activities'),
        'subtitle' => __('section.management'),
        'quickLabel' => '',
        'quickHref' => '',
    ],
    'categories' => [
        'title' => __('nav.categories'),
        'subtitle' => __('section.management'),
        'quickLabel' => '',
        'quickHref' => '',
    ],
    'time_logs' => [
        'title' => __('nav.time_logs'),
        'subtitle' => __('section.tracking'),
        'quickLabel' => '',
        'quickHref' => '',
    ],
    'account' => [
        'title' => __('nav.account'),
        'subtitle' => __('account.eyebrow'),
        'quickLabel' => '',
        'quickHref' => '',
    ],
];

$workspacePage = $workspacePages[$activeNav] ?? $workspacePages['home'];
$currentLocale = Lang::locale();
?>
<aside class="workspace-sidebar">
    <div class="workspace-brand-row">
        <a class="workspace-brand" href="/">
            <span class="workspace-mark">PTO</span>
            <span>
                <strong><?= $e(__('app.sidebar_title')) ?></strong>
                <small><?= $e(__('app.sidebar_subtitle')) ?></small>
            </span>
        </a>
        <div class="settings-menu">
            <button
                class="icon-button brand-settings-trigger"
                type="button"
                data-settings-toggle
                aria-expanded="false"
                aria-controls="settings-panel"
                aria-label="<?= $e(__('settings.open')) ?>"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"></path>
                    <path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1A2 2 0 1 1 4.2 17l.1-.1A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.6-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.3 7A2 2 0 1 1 7.1 4.2l.1.1a1.7 1.7 0 0 0 1.9.3 1.7 1.7 0 0 0 1-1.6V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1A2 2 0 1 1 19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.1a2 2 0 1 1 0 4H21a1.7 1.7 0 0 0-1.6 1Z"></path>
                </svg>
            </button>
            <div class="settings-panel" id="settings-panel" data-settings-panel hidden>
                <div class="preference-group">
                    <span><?= $e(__('notifications.title')) ?></span>
                    <div class="notification-switch" aria-label="<?= $e(__('notifications.title')) ?>">
                        <button class="notification-option" type="button" data-notification-toggle data-value="on" aria-pressed="false">
                            <?= $e(__('notifications.on')) ?>
                        </button>
                        <button class="notification-option" type="button" data-notification-toggle data-value="off" aria-pressed="true">
                            <?= $e(__('notifications.off')) ?>
                        </button>
                    </div>
                </div>

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
    </div>

    <nav class="workspace-nav" aria-label="<?= $e(__('ui.workspace')) ?>">
        <?php foreach ($navItems as $key => $item): ?>
            <a href="<?= $e($item['href']) ?>" <?= $activeNav === $key ? 'aria-current="page"' : '' ?>>
                <span class="nav-glyph" aria-hidden="true"><?= $navIcons[$key] ?? '' ?></span>
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
        <?php if (!empty($currentUser)): ?>
            <a class="user-chip" href="/account">
                <span><?= $e($currentUser['name'] ?: 'Demo User') ?></span>
            </a>
        <?php endif; ?>
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
                <a href="/reminders/create"><?= $e(__('quick_add.reminder')) ?></a>
                <a href="/important-dates/create"><?= $e(__('quick_add.important_date')) ?></a>
                <a href="/focus"><?= $e(__('quick_add.focus')) ?></a>
                <a href="/assistant"><?= $e(__('quick_add.assistant')) ?></a>
                <a href="/optimizer"><?= $e(__('quick_add.optimizer')) ?></a>
            </div>
        </div>
        <?php if (!empty($currentUser)): ?>
            <div class="notification-bell-menu">
                <button
                    class="icon-button notification-bell-trigger"
                    type="button"
                    data-notifications-toggle
                    aria-expanded="false"
                    aria-controls="notifications-panel"
                    aria-label="<?= $e(__('notifications.open')) ?>"
                >
                    <svg viewBox="0 0 24 24">
                        <path d="M6 17h12"></path>
                        <path d="M8 17V10a4 4 0 0 1 8 0v7"></path>
                        <path d="M10 19a2 2 0 0 0 4 0"></path>
                    </svg>
                    <span class="notification-badge" data-notifications-count hidden>0</span>
                </button>
                <div class="notifications-panel" id="notifications-panel" data-notifications-panel hidden>
                    <div class="notifications-panel-head">
                        <div>
                            <p class="eyebrow"><?= $e(__('notifications.title')) ?></p>
                            <h2><?= $e(__('notifications.today')) ?></h2>
                        </div>
                        <span class="notification-status-note" data-notification-status-note></span>
                    </div>
                    <div class="notification-tabs" role="group" aria-label="<?= $e(__('notifications.title')) ?>">
                        <button type="button" data-notification-filter="all" aria-pressed="true"><?= $e(__('notifications.all')) ?></button>
                        <button type="button" data-notification-filter="unread" aria-pressed="false"><?= $e(__('notifications.unread')) ?></button>
                    </div>
                    <div class="notifications-list" data-notifications-list></div>
                </div>
            </div>
        <?php endif; ?>
        <div class="language-switch" aria-label="<?= $e(__('language.switcher')) ?>">
            <a href="/lang?locale=vi" <?= $currentLocale === 'vi' ? 'aria-current="true"' : '' ?>>
                <?= $e(__('language.vi')) ?>
            </a>
            <a href="/lang?locale=en" <?= $currentLocale === 'en' ? 'aria-current="true"' : '' ?>>
                <?= $e(__('language.en')) ?>
            </a>
        </div>
        <?php if (!empty($currentUser)): ?>
            <form class="logout-inline" method="post" action="/logout">
                <button class="button ghost compact" type="submit"><?= $e(__('auth.logout')) ?></button>
            </form>
        <?php endif; ?>
    </div>
</header>

<div
    class="toast-config"
    data-toast-validation="<?= $e(__('toast.validation_error')) ?>"
    data-toast-dismiss="<?= $e(__('action.dismiss')) ?>"
    data-reminder-template="<?= $e(__('reminder.toast_template')) ?>"
    data-notification-granted="<?= $e(__('reminder.notification_granted')) ?>"
    data-notification-denied="<?= $e(__('reminder.notification_denied')) ?>"
    data-notification-unsupported="<?= $e(__('reminder.notification_unsupported')) ?>"
    data-notification-default-body="<?= $e(__('reminder.notification_default_body')) ?>"
    data-notification-status-default="<?= $e(__('notifications.status_default')) ?>"
    data-notification-status-granted="<?= $e(__('notifications.status_granted')) ?>"
    data-notification-status-denied="<?= $e(__('notifications.status_denied')) ?>"
    data-notification-status-unsupported="<?= $e(__('notifications.status_unsupported')) ?>"
    data-notification-enable-label="<?= $e(__('notifications.enable')) ?>"
    data-notification-enabled-label="<?= $e(__('notifications.enabled')) ?>"
    data-notification-empty="<?= $e(__('notifications.empty')) ?>"
    data-notification-all-empty="<?= $e(__('notifications.empty')) ?>"
    data-notification-unread-empty="<?= $e(__('notifications.unread_empty')) ?>"
    data-notification-upcoming-label="<?= $e(__('notifications.upcoming')) ?>"
    data-notification-past-label="<?= $e(__('notifications.past')) ?>"
    data-notification-reminder-title="<?= $e(__('notifications.reminder_title')) ?>"
    data-notification-on-label="<?= $e(__('notifications.on')) ?>"
    data-notification-off-label="<?= $e(__('notifications.off')) ?>"
    data-required-message="<?= $e(__('validation.required_field')) ?>"
    data-delete-label="<?= $e(__('action.delete')) ?>"
    data-delete-schedule-message="<?= $e(__('message.delete_schedule')) ?>"
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
