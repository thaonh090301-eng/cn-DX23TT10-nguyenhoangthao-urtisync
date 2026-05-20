# AGENTS.md

This repository is a plain PHP MVC project for the topic: **Personal Time Optimization System**.

## Project Structure

- Main source code is in `scr/`.
- Routes are defined in `scr/routes.php`.
- Controllers are in `scr/app/Controllers/`.
- Repositories are in `scr/app/Repositories/`.
- Views are in `scr/app/Views/`.
- Database schema is in `setup/database/schema.sql`.

## Working Rules

- Do not rewrite the entire architecture.
- Do not add Laravel, React, Vue, or any major framework unless explicitly requested.
- Prefer small, clear, reviewable changes.
- Keep the existing plain PHP MVC style.
- After editing PHP files, run `php -l` on every modified PHP file.
- After editing SQL files, ensure the schema remains compatible with MySQL/MariaDB.

## Final Response Expectations

When reporting completed work, include:

- Files changed
- What changed
- Why it changed
- Commands run
- Test results
