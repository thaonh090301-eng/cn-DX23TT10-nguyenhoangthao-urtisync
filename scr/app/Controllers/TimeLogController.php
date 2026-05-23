<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\TimeLogRepository;
use DateTimeImmutable;

class TimeLogController extends Controller
{
    private TimeLogRepository $timeLogs;

    public function __construct()
    {
        $this->timeLogs = new TimeLogRepository();
    }

    public function index(): string
    {
        $date = $this->dateFromRequest();
        $reportRows = $this->timeLogs->dailyReportByUser($this->authUserId(), $date);

        return $this->view('time_logs/index', [
            'title' => \__('nav.time_logs'),
            'reportRows' => $reportRows,
            'selectedDate' => $date,
            'summary' => $this->reportSummary($reportRows),
            'flash' => $this->consumeFlash(),
        ]);
    }

    private function dateFromRequest(): string
    {
        $date = trim((string) ($_GET['date'] ?? date('Y-m-d')));

        if (!$this->isDate($date)) {
            return date('Y-m-d');
        }

        return $date;
    }

    private function isDate(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }

    private function reportSummary(array $rows): array
    {
        $summary = [
            'planned_minutes' => 0,
            'scheduled_count' => 0,
        ];

        foreach ($rows as $row) {
            if ($row['planned_minutes'] !== null) {
                $summary['planned_minutes'] += (int) $row['planned_minutes'];
                $summary['scheduled_count']++;
            }
        }

        return $summary;
    }
}
