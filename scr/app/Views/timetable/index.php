<?php
$stateClass = static fn (string $state): string => in_array($state, ['current', 'next', 'logged', 'gap'], true) ? $state : 'planned';
$reminderTitle = static fn (array $schedule): string => display_activity_title($schedule['activity_title'] ?: $schedule['title']);
?>
<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('nav.timetable')) ?></title>
    <?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'theme_boot.php'; ?>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'timetable'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('timetable.eyebrow')) ?></p>
                <h1><?= $e(__('nav.timetable')) ?></h1>
            </div>
            <div class="header-actions">
                <a class="button" href="/calendar"><?= $e(__('timetable.action.open_calendar')) ?></a>
                <a class="button primary" href="/schedules/create"><?= $e(__('timetable.action.add_schedule')) ?></a>
            </div>
        </section>

        <section class="panel dashboard-section timetable-toolbar">
            <form class="filter-bar time-report-filter" method="get" action="/timetable">
                <label class="filter-field">
                    <span><?= $e(__('timetable.date')) ?></span>
                    <input type="date" name="date" value="<?= $e($selectedDate) ?>">
                </label>
                <div class="form-actions">
                    <button class="button primary" type="submit"><?= $e(__('timetable.action.view_day')) ?></button>
                </div>
            </form>

            <div class="timetable-focus">
                <?php if ($currentOrNext === null): ?>
                    <p class="eyebrow"><?= $e(__('timetable.focus.empty_label')) ?></p>
                    <h2><?= $e(__('timetable.focus.empty_title')) ?></h2>
                    <p><?= $e(__('timetable.focus.empty_copy')) ?></p>
                <?php else: ?>
                    <?php $focusState = (string) ($currentOrNext['focus_state'] ?? 'next'); ?>
                    <p class="eyebrow"><?= $e($focusState === 'current' ? __('timetable.focus.current') : __('timetable.focus.next')) ?></p>
                    <h2><?= $e($reminderTitle($currentOrNext)) ?></h2>
                    <p>
                        <?= $e(format_app_time($currentOrNext['start_at'])) ?>
                        -
                        <?= $e(format_app_time($currentOrNext['end_at'])) ?>
                        &middot;
                        <?= $e(display_category_name($currentOrNext['category_name'])) ?>
                    </p>
                <?php endif; ?>
            </div>
        </section>

        <section class="panel">
            <?php if ($items === []): ?>
                <div class="empty-state">
                    <p><?= $e(__('timetable.empty')) ?></p>
                    <a class="button primary" href="/schedules/create"><?= $e(__('timetable.action.add_schedule')) ?></a>
                </div>
            <?php else: ?>
                <div
                    class="timetable-list"
                    data-timetable-reminders
                    data-reminder-template="<?= $e(__('timetable.reminder_template')) ?>"
                >
                    <?php foreach ($items as $item): ?>
                        <?php if ($item['type'] === 'reminder'): ?>
                            <?php $reminder = $item['reminder']; ?>
                            <article class="timetable-item reminder">
                                <div class="timetable-time">
                                    <strong><?= $e(format_app_time($item['start_at'])) ?></strong>
                                    <span><?= $e(__('timetable.state.reminder')) ?></span>
                                </div>
                                <div class="timetable-card">
                                    <span class="status-pill info"><?= $e(__('timetable.reminder_label')) ?></span>
                                    <h2><?= $e($reminder['title']) ?></h2>
                                    <?php if (!empty($reminder['note'])): ?>
                                        <p><?= $e($reminder['note']) ?></p>
                                    <?php endif; ?>
                                    <p>
                                        <?= $e(__('reminder.repeat.' . $reminder['repeat_type'])) ?>
                                        <?php if ($reminder['repeat_type'] === 'weekly' && $reminder['day_of_week'] !== null): ?>
                                            &middot; <?= $e(__('day.' . $reminder['day_of_week'])) ?>
                                        <?php endif; ?>
                                    </p>
                                    <div class="actions">
                                        <a href="/reminders/<?= $e($reminder['id']) ?>/edit"><?= $e(__('action.edit')) ?></a>
                                    </div>
                                </div>
                            </article>
                            <?php continue; ?>
                        <?php endif; ?>

                        <?php if ($item['type'] === 'gap'): ?>
                            <article class="timetable-item gap">
                                <div class="timetable-time">
                                    <strong><?= $e(format_app_time($item['start_at'])) ?></strong>
                                    <span><?= $e(format_app_time($item['end_at'])) ?></span>
                                </div>
                                <div class="timetable-card">
                                    <span class="status-pill info"><?= $e(__('timetable.gap')) ?></span>
                                    <h2><?= $e(__('timetable.gap_title', ['minutes' => $item['minutes']])) ?></h2>
                                    <p><?= $e(__('timetable.gap_copy')) ?></p>
                                    <div class="actions">
                                        <a href="/optimizer"><?= $e(__('timetable.action.fill_gap')) ?></a>
                                    </div>
                                </div>
                            </article>
                            <?php continue; ?>
                        <?php endif; ?>

                        <?php
                            $schedule = $item['schedule'];
                            $state = $stateClass($item['state']);
                            $title = $reminderTitle($schedule);
                            $isConfirmed = $schedule['time_log_id'] !== null;
                            $statusClass = $isConfirmed ? 'success' : 'warning';
                            $statusLabel = $isConfirmed ? __('time_report.status.confirmed') : __('time_report.status.not_confirmed');
                        ?>
                        <article
                            class="timetable-item <?= $e($state) ?>"
                            data-reminder-item
                            data-reminder-id="<?= $e($schedule['id']) ?>"
                            data-reminder-title="<?= $e($title) ?>"
                            data-reminder-start="<?= $e(str_replace(' ', 'T', $schedule['start_at'])) ?>"
                        >
                            <div class="timetable-time">
                                <strong><?= $e(format_app_time($schedule['start_at'])) ?></strong>
                                <span><?= $e(format_app_time($schedule['end_at'])) ?></span>
                            </div>
                            <div class="timetable-card">
                                <div class="timetable-card-header">
                                    <span class="status-pill <?= $e($statusClass) ?>"><?= $e($statusLabel) ?></span>
                                </div>

                                <h2><?= $e($title) ?></h2>
                                <p>
                                    <span class="color-chip" style="--chip: <?= $e($schedule['category_color']) ?>"></span>
                                    <?= $e(display_category_name($schedule['category_name'])) ?>
                                    &middot;
                                    <?= $e(format_duration_minutes($item['minutes'])) ?>
                                    &middot;
                                    <?= $e(__('schedule_status.' . $schedule['status'])) ?>
                                </p>

                                <div class="actions">
                                    <?php if ($schedule['time_log_id'] !== null): ?>
                                        <a href="/time-logs/<?= $e($schedule['time_log_id']) ?>/edit"><?= $e(__('timetable.action.edit_log')) ?></a>
                                    <?php else: ?>
                                        <form method="post" action="/time-logs/schedules/<?= $e($schedule['id']) ?>/confirm">
                                            <button class="button compact primary" type="submit"><?= $e(__('timetable.action.add_time_log')) ?></button>
                                        </form>
                                    <?php endif; ?>
                                    <a href="/schedules/<?= $e($schedule['id']) ?>/edit"><?= $e(__('action.edit')) ?></a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const list = document.querySelector('[data-timetable-reminders]');
            const toastRegion = document.querySelector('[data-toast-region]');

            if (!list || !toastRegion) {
                return;
            }

            const template = list.dataset.reminderTemplate || 'Sắp đến giờ: :activity';
            const shown = new Set();
            const showToast = (message) => {
                const toast = document.createElement('div');
                toast.className = 'toast warning';
                toast.setAttribute('role', 'status');

                const messageElement = document.createElement('p');
                messageElement.textContent = message;

                const closeButton = document.createElement('button');
                closeButton.type = 'button';
                closeButton.className = 'toast-close';
                closeButton.textContent = <?= json_encode(__('action.dismiss'), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
                closeButton.addEventListener('click', () => toast.remove());

                toast.append(messageElement, closeButton);
                toastRegion.appendChild(toast);
                window.setTimeout(() => toast.remove(), 7000);
            };

            const checkReminders = () => {
                const now = Date.now();

                document.querySelectorAll('[data-reminder-item]').forEach((item) => {
                    const start = new Date(item.dataset.reminderStart || '').getTime();
                    const id = item.dataset.reminderId || item.dataset.reminderStart || '';

                    if (!Number.isFinite(start) || shown.has(id)) {
                        return;
                    }

                    const minutesUntilStart = Math.floor((start - now) / 60000);

                    if (minutesUntilStart >= 0 && minutesUntilStart <= 5) {
                        const title = item.dataset.reminderTitle || '';
                        const message = template.replace(':activity', title);

                        shown.add(id);
                        showToast(message);

                        if ('Notification' in window && Notification.permission === 'granted') {
                            new Notification(message);
                        }
                    }
                });
            };

            checkReminders();
            window.setInterval(checkReminders, 60000);
        });
    </script>
    <script src="assets/js/app.js"></script>
</body>
</html>
