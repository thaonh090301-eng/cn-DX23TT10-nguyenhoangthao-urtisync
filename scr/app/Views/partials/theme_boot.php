<script>
    (() => {
        try {
            const root = document.documentElement;
            const storage = window.localStorage;
            const theme = storage.getItem('pto-theme');
            const accent = storage.getItem('pto-accent');
            const density = storage.getItem('pto-density');

            root.dataset.theme = ['dark', 'light'].includes(theme) ? theme : 'light';
            root.dataset.accent = ['blue', 'purple', 'green', 'orange'].includes(accent) ? accent : 'blue';
            root.dataset.density = ['comfortable', 'compact'].includes(density) ? density : 'comfortable';
        } catch (error) {
            // localStorage may be unavailable in private browsing or strict browser modes.
        }
    })();
</script>
