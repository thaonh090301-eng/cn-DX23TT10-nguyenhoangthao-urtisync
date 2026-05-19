USE personal_time_optimizer;

INSERT INTO users (name, email, password_hash, role)
VALUES (
    'Demo User',
    'demo@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'demo'
)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    role = VALUES(role);

SET @demo_user_id = (
    SELECT id FROM users WHERE email = 'demo@example.com' LIMIT 1
);

INSERT INTO categories (user_id, name, color, sort_order)
VALUES
    (@demo_user_id, 'Study', '#2563eb', 1),
    (@demo_user_id, 'Work', '#16a34a', 2),
    (@demo_user_id, 'Health', '#dc2626', 3),
    (@demo_user_id, 'Personal', '#9333ea', 4)
ON DUPLICATE KEY UPDATE
    color = VALUES(color),
    sort_order = VALUES(sort_order);

INSERT INTO activities (user_id, category_id, title, description, priority, estimated_minutes)
VALUES
    (
        @demo_user_id,
        (SELECT id FROM categories WHERE user_id = @demo_user_id AND name = 'Study' LIMIT 1),
        'Review PHP MVC notes',
        'Read routing, controller, and PDO connection notes.',
        'high',
        90
    ),
    (
        @demo_user_id,
        (SELECT id FROM categories WHERE user_id = @demo_user_id AND name = 'Health' LIMIT 1),
        'Exercise',
        'Simple daily workout.',
        'medium',
        45
    );

INSERT INTO schedules (user_id, activity_id, title, start_at, end_at, status, notes)
VALUES
    (
        @demo_user_id,
        (SELECT id FROM activities WHERE user_id = @demo_user_id AND title = 'Review PHP MVC notes' LIMIT 1),
        'Review PHP MVC notes',
        TIMESTAMP(CURDATE(), '08:00:00'),
        TIMESTAMP(CURDATE(), '09:30:00'),
        'scheduled',
        'Sample schedule for FullCalendar integration later.'
    ),
    (
        @demo_user_id,
        (SELECT id FROM activities WHERE user_id = @demo_user_id AND title = 'Exercise' LIMIT 1),
        'Exercise',
        TIMESTAMP(CURDATE(), '17:30:00'),
        TIMESTAMP(CURDATE(), '18:15:00'),
        'scheduled',
        'Sample personal activity.'
    );

INSERT INTO time_logs (user_id, activity_id, schedule_id, started_at, ended_at, duration_minutes, note)
VALUES (
    @demo_user_id,
    (SELECT id FROM activities WHERE user_id = @demo_user_id AND title = 'Review PHP MVC notes' LIMIT 1),
    (SELECT id FROM schedules WHERE user_id = @demo_user_id AND title = 'Review PHP MVC notes' LIMIT 1),
    TIMESTAMP(CURDATE(), '08:05:00'),
    TIMESTAMP(CURDATE(), '09:20:00'),
    75,
    'Sample actual time log.'
);

INSERT INTO reminders (user_id, title, note, remind_time, repeat_type, day_of_week, is_active)
SELECT @demo_user_id, 'Uống nước', 'Nhắc nhẹ uống một ly nước.', '09:00:00', 'daily', NULL, 1
WHERE NOT EXISTS (
    SELECT 1 FROM reminders WHERE user_id = @demo_user_id AND title = 'Uống nước' AND remind_time = '09:00:00'
);

INSERT INTO reminders (user_id, title, note, remind_time, repeat_type, day_of_week, is_active)
SELECT @demo_user_id, 'Tập thể dục', 'Vận động nhẹ để giữ năng lượng.', '17:00:00', 'daily', NULL, 1
WHERE NOT EXISTS (
    SELECT 1 FROM reminders WHERE user_id = @demo_user_id AND title = 'Tập thể dục' AND remind_time = '17:00:00'
);

INSERT INTO reminders (user_id, title, note, remind_time, repeat_type, day_of_week, is_active)
SELECT @demo_user_id, 'Học tiếng Anh', 'Ôn từ vựng hoặc nghe 15 phút.', '20:30:00', 'daily', NULL, 1
WHERE NOT EXISTS (
    SELECT 1 FROM reminders WHERE user_id = @demo_user_id AND title = 'Học tiếng Anh' AND remind_time = '20:30:00'
);
