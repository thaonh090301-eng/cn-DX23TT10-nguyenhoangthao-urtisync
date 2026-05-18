<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class OptimizerRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    public function busySchedulesByUser(int $userId, string $rangeStart, string $rangeEnd): array
    {
        $statement = $this->db->prepare(
            'SELECT id, title, start_at, end_at, status
             FROM schedules
             WHERE user_id = :user_id
                AND status <> :cancelled_status
                AND start_at < :range_end
                AND end_at > :range_start
             ORDER BY start_at ASC, end_at ASC'
        );
        $statement->execute([
            'user_id' => $userId,
            'cancelled_status' => 'cancelled',
            'range_start' => $rangeStart,
            'range_end' => $rangeEnd,
        ]);

        return $statement->fetchAll();
    }
}
