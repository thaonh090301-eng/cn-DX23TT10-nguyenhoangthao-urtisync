<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\DashboardRepository;
use App\Repositories\ReminderRepository;

class DashboardController extends Controller
{
    private const DEMO_USER_ID = 1;

    private DashboardRepository $dashboard;
    private ReminderRepository $reminders;

    public function __construct()
    {
        $this->dashboard = new DashboardRepository();
        $this->reminders = new ReminderRepository();
    }

    public function index(): string
    {
        $threshold = $this->personalThresholdMinutes();
        $summary = $this->dashboard->summary(self::DEMO_USER_ID);
        $personalMinutes = $this->dashboard->personalOrRecreationActualMinutesToday(self::DEMO_USER_ID);

        return $this->view('dashboard/index', [
            'title' => \__('nav.dashboard'),
            'summary' => $summary,
            'plannedByCategory' => $this->dashboard->plannedMinutesByCategoryThisWeek(self::DEMO_USER_ID),
            'actualByCategory' => $this->dashboard->actualMinutesByCategoryThisWeek(self::DEMO_USER_ID),
            'alerts' => $this->alerts($summary, $personalMinutes, $threshold),
            'personalThresholdMinutes' => $threshold,
            'personalActualMinutes' => $personalMinutes,
            'upcomingReminders' => $this->reminders->upcomingToday(self::DEMO_USER_ID, 4),
        ]);
    }

    private function personalThresholdMinutes(): int
    {
        $config = require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

        return (int) ($config['dashboard']['personal_daily_threshold_minutes'] ?? 180);
    }

    private function alerts(array $summary, int $personalMinutes, int $threshold): array
    {
        $alerts = [];

        if ($summary['actual_today_minutes'] > $summary['planned_today_minutes']) {
            $alerts[] = [
                'type' => 'danger',
                'message' => \__('alert.actual_over_planned'),
            ];
        }

        if ($personalMinutes > $threshold) {
            $alerts[] = [
                'type' => 'warning',
                'message' => \__('alert.personal_threshold_exceeded'),
            ];
        }

        if ($summary['planned_today_minutes'] > 0 && $summary['time_logs_today_count'] === 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => \__('alert.planned_without_actual'),
            ];
        } elseif ($summary['time_logs_today_count'] === 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => \__('alert.no_logs_today'),
            ];
        }

        return $alerts;
    }
}
