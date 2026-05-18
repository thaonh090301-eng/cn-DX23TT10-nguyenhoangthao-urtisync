# Project: Personal Time Optimizer

## Goal
Build a 10-day MVP web application for personal time optimization.

## Required stack
- PHP 8.x
- MySQL
- PDO
- HTML, CSS, JavaScript
- FullCalendar
- Localhost using XAMPP, WAMP or Laragon

## Required features
1. Login demo.
2. Manage categories.
3. Manage personal activities.
4. Create, update, delete schedules.
5. Show schedules in FullCalendar.
6. Track actual time logs.
7. Suggest free time slots using gap analysis.
8. Show simple dashboard and alerts.

## Architecture
Use simple PHP MVC.
Do not use Laravel, CakePHP, FuelPHP, React, Vue, Docker, Redis, Supabase, OAuth, or cloud deployment unless explicitly requested.

## Security rules
- Use PDO prepared statements.
- Escape output in views.
- Do not commit .env.
- Keep database config in .env.example.
- Use password_hash for passwords if login is implemented.

## Folder rules
- Main source code must be inside scr/.
- Database files must be inside setup/database/.
- Progress reports must be inside progress-report/.
- Thesis documents must be inside thesis/.
