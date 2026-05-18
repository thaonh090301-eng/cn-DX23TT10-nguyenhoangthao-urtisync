<?php

$activeNav = $activeNav ?? '';
$navItems = [
    'home' => ['label' => 'Home', 'href' => '/'],
    'dashboard' => ['label' => 'Dashboard', 'href' => '/dashboard'],
    'optimizer' => ['label' => 'Optimizer', 'href' => '/optimizer'],
    'categories' => ['label' => 'Categories', 'href' => '/categories'],
    'activities' => ['label' => 'Activities', 'href' => '/activities'],
    'schedules' => ['label' => 'Schedules', 'href' => '/schedules'],
    'calendar' => ['label' => 'Calendar', 'href' => '/schedules/calendar'],
    'time_logs' => ['label' => 'Time Logs', 'href' => '/time-logs'],
];
?>
<nav class="top-nav">
    <?php foreach ($navItems as $key => $item): ?>
        <a href="<?= $e($item['href']) ?>" <?= $activeNav === $key ? 'aria-current="page"' : '' ?>>
            <?= $e($item['label']) ?>
        </a>
    <?php endforeach; ?>
</nav>
