document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('app-ready');

    const themeToggle = document.querySelector('[data-theme-toggle]');
    const storedTheme = window.localStorage.getItem('pto-theme');

    if (storedTheme === 'dark') {
        document.documentElement.dataset.theme = 'dark';
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';

            if (nextTheme === 'dark') {
                document.documentElement.dataset.theme = 'dark';
                window.localStorage.setItem('pto-theme', 'dark');
                return;
            }

            delete document.documentElement.dataset.theme;
            window.localStorage.setItem('pto-theme', 'light');
        });
    }
});
