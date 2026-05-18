<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('page.schedule_calendar')) ?></title>
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'calendar'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('nav.calendar')) ?></p>
                <h1><?= $e(__('page.schedule_calendar')) ?></h1>
            </div>
            <a class="button primary" href="/schedules/create"><?= $e(__('action.new_schedule')) ?></a>
        </section>

        <section class="panel calendar-panel">
            <div id="calendar"></div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales-all.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const calendarElement = document.getElementById('calendar');

            if (!calendarElement || typeof FullCalendar === 'undefined') {
                return;
            }

            const calendar = new FullCalendar.Calendar(calendarElement, {
                locale: <?= json_encode(\App\Core\Lang::locale(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
                initialView: 'timeGridWeek',
                height: 'auto',
                nowIndicator: true,
                eventDisplay: 'block',
                allDayText: <?= json_encode(__('calendar.all_day'), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
                buttonText: {
                    today: <?= json_encode(__('calendar.today'), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
                    month: <?= json_encode(__('calendar.month'), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
                    week: <?= json_encode(__('calendar.week'), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
                    day: <?= json_encode(__('calendar.day'), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
                    list: <?= json_encode(__('calendar.list'), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>
                },
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                events: '/api/schedules',
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                eventClick: (info) => {
                    window.location.href = `/schedules/${encodeURIComponent(info.event.id)}/edit`;
                }
            });

            calendar.render();
        });
    </script>
    <script src="../assets/js/app.js"></script>
</body>
</html>
