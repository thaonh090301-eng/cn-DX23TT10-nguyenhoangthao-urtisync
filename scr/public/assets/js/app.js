document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('app-ready');

    const root = document.documentElement;
    const preferenceConfig = {
        theme: {
            defaultValue: 'light',
            storageKey: 'pto-theme',
            values: ['light', 'dark'],
        },
        accent: {
            defaultValue: 'blue',
            storageKey: 'pto-accent',
            values: ['blue', 'purple', 'green', 'orange'],
        },
        density: {
            defaultValue: 'comfortable',
            storageKey: 'pto-density',
            values: ['comfortable', 'compact'],
        },
    };

    const readStoredPreference = (preference) => {
        const config = preferenceConfig[preference];

        if (!config) {
            return '';
        }

        try {
            return window.localStorage.getItem(config.storageKey) || '';
        } catch (error) {
            return '';
        }
    };

    const writeStoredPreference = (preference, value) => {
        const config = preferenceConfig[preference];

        if (!config) {
            return;
        }

        try {
            window.localStorage.setItem(config.storageKey, value);
        } catch (error) {
            // Keep the controls usable if browser storage is blocked.
        }
    };

    const setActivePreference = (preference, value) => {
        document.querySelectorAll(`[data-preference="${preference}"]`).forEach((button) => {
            const isActive = button.dataset.value === value;
            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    };

    const applyPreference = (preference, value, persist = false) => {
        const config = preferenceConfig[preference];

        if (!config || !config.values.includes(value)) {
            return;
        }

        root.dataset[preference] = value;
        setActivePreference(preference, value);

        if (persist) {
            writeStoredPreference(preference, value);
        }
    };

    Object.keys(preferenceConfig).forEach((preference) => {
        const config = preferenceConfig[preference];
        const storedValue = readStoredPreference(preference);
        const value = config.values.includes(storedValue) ? storedValue : config.defaultValue;

        applyPreference(preference, value);
    });

    document.querySelectorAll('[data-preference]').forEach((button) => {
        button.addEventListener('click', () => {
            const preference = button.dataset.preference || '';
            const value = button.dataset.value || '';

            applyPreference(preference, value, true);
        });
    });

    const setupDisclosure = (containerSelector, toggleSelector, panelSelector) => {
        const container = document.querySelector(containerSelector);
        const toggle = document.querySelector(toggleSelector);
        const panel = document.querySelector(panelSelector);

        if (!container || !toggle || !panel) {
            return () => {};
        }

        const close = () => {
            panel.hidden = true;
            toggle.setAttribute('aria-expanded', 'false');
        };

        toggle.addEventListener('click', () => {
            const isOpen = !panel.hidden;

            panel.hidden = isOpen;
            toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        });

        document.addEventListener('click', (event) => {
            if (!container.contains(event.target)) {
                close();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                close();
            }
        });

        return close;
    };

    setupDisclosure('.personalization', '[data-personalization-toggle]', '[data-personalization-panel]');
    setupDisclosure('.quick-add', '[data-quick-add-toggle]', '[data-quick-add-panel]');

    const legacyThemeToggle = document.querySelector('[data-theme-toggle]');

    if (legacyThemeToggle) {
        legacyThemeToggle.addEventListener('click', () => {
            const nextTheme = root.dataset.theme === 'dark' ? 'light' : 'dark';
            applyPreference('theme', nextTheme, true);
        });
    }

    const toastRegion = document.querySelector('[data-toast-region]');
    const toastConfig = document.querySelector('.toast-config');
    const shownToasts = new Set();

    const showToast = (message, type = 'info') => {
        const text = String(message || '').trim();

        if (!toastRegion || text === '' || shownToasts.has(`${type}:${text}`)) {
            return;
        }

        shownToasts.add(`${type}:${text}`);

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.setAttribute('role', type === 'danger' ? 'alert' : 'status');

        const messageElement = document.createElement('p');
        messageElement.textContent = text;

        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'toast-close';
        closeButton.textContent = toastConfig?.dataset.toastDismiss || 'Close';
        closeButton.addEventListener('click', () => {
            toast.remove();
        });

        toast.append(messageElement, closeButton);
        toastRegion.appendChild(toast);

        window.setTimeout(() => {
            toast.remove();
        }, 5200);
    };

    document.querySelectorAll('.alert.success').forEach((alert) => {
        showToast(alert.textContent, 'success');
    });

    document.querySelectorAll('.alert.danger, .alert.warning').forEach((alert) => {
        showToast(alert.textContent, alert.classList.contains('warning') ? 'warning' : 'danger');
    });

    if (document.querySelector('.field-error')) {
        showToast(toastConfig?.dataset.toastValidation || 'Please fix the highlighted fields.', 'danger');
    }

    const notificationButton = document.querySelector('[data-notification-permission]');
    const reminderToastTemplate = toastConfig?.dataset.reminderTemplate || 'Coming up: :title';
    const shownReminderToasts = new Set();

    notificationButton?.addEventListener('click', async () => {
        if (!('Notification' in window)) {
            showToast(toastConfig?.dataset.notificationDenied || 'Notifications are not available in this browser.', 'warning');
            return;
        }

        const permission = await Notification.requestPermission();
        const message = permission === 'granted'
            ? toastConfig?.dataset.notificationGranted
            : toastConfig?.dataset.notificationDenied;

        showToast(message || '', permission === 'granted' ? 'success' : 'warning');
    });

    const checkPersonalReminders = async () => {
        try {
            const response = await fetch('/api/reminders/today', {
                headers: {
                    Accept: 'application/json',
                },
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            const reminders = Array.isArray(payload.reminders) ? payload.reminders : [];
            const now = Date.now();

            reminders.forEach((reminder) => {
                const start = new Date(reminder.remind_at || '').getTime();

                if (!Number.isFinite(start)) {
                    return;
                }

                const reminderKey = `${reminder.id}:${reminder.remind_at}`;
                const minutesUntilStart = Math.floor((start - now) / 60000);

                if (minutesUntilStart < 0 || minutesUntilStart > 5 || shownReminderToasts.has(reminderKey)) {
                    return;
                }

                const title = String(reminder.title || '').trim();
                const message = reminderToastTemplate.replace(':title', title);

                shownReminderToasts.add(reminderKey);
                showToast(message, 'warning');

                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification(message, {
                        body: String(reminder.note || '').trim(),
                    });
                }
            });
        } catch (error) {
            // Reminder polling should never interrupt the main UI.
        }
    };

    if (toastRegion) {
        checkPersonalReminders();
        window.setInterval(checkPersonalReminders, 60000);
    }

    const deleteModal = document.querySelector('[data-delete-modal]');
    const deleteModalMessage = document.querySelector('[data-delete-modal-message]');
    const deleteModalConfirm = document.querySelector('[data-delete-modal-confirm]');
    const deleteModalCancel = document.querySelector('[data-delete-modal-cancel]');
    let pendingDeleteForm = null;

    const closeDeleteModal = () => {
        if (!deleteModal) {
            return;
        }

        deleteModal.hidden = true;
        pendingDeleteForm = null;
    };

    document.querySelectorAll('form[data-confirm-delete]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === 'true' || !deleteModal) {
                return;
            }

            event.preventDefault();
            pendingDeleteForm = form;

            if (deleteModalMessage && form.dataset.confirmMessage) {
                deleteModalMessage.textContent = form.dataset.confirmMessage;
            }

            deleteModal.hidden = false;
            deleteModalConfirm?.focus();
        });
    });

    deleteModalConfirm?.addEventListener('click', () => {
        if (!pendingDeleteForm) {
            return;
        }

        pendingDeleteForm.dataset.confirmed = 'true';
        pendingDeleteForm.submit();
    });

    deleteModalCancel?.addEventListener('click', closeDeleteModal);
    deleteModal?.addEventListener('click', (event) => {
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && deleteModal && !deleteModal.hidden) {
            closeDeleteModal();
        }
    });

    document.querySelectorAll('[data-filter-controls]').forEach((controls) => {
        const targetId = controls.dataset.filterTarget || '';
        const table = document.getElementById(targetId);

        if (!table) {
            return;
        }

        const rows = Array.from(table.querySelectorAll('[data-filter-row]'));
        const filterEmpty = document.querySelector(`[data-filter-empty="${targetId}"]`);
        const searchInput = controls.querySelector('[data-filter-search]');
        const selectFilters = Array.from(controls.querySelectorAll('[data-filter-select]'));

        selectFilters.forEach((select) => {
            const field = select.dataset.filterSelect || '';
            const values = [...new Set(rows.map((row) => row.dataset[field] || '').filter(Boolean))].sort((a, b) => a.localeCompare(b));

            values.forEach((value) => {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = value;
                select.appendChild(option);
            });
        });

        const applyFilters = () => {
            const search = (searchInput?.value || '').trim().toLowerCase();
            let visibleCount = 0;

            rows.forEach((row) => {
                const searchText = (row.dataset.search || row.textContent || '').toLowerCase();
                const matchesSearch = search === '' || searchText.includes(search);
                const matchesSelects = selectFilters.every((select) => {
                    const field = select.dataset.filterSelect || '';
                    return select.value === '' || row.dataset[field] === select.value;
                });
                const isVisible = matchesSearch && matchesSelects;

                row.hidden = !isVisible;

                if (isVisible) {
                    visibleCount += 1;
                }
            });

            if (filterEmpty) {
                filterEmpty.hidden = visibleCount > 0;
            }
        };

        searchInput?.addEventListener('input', applyFilters);
        selectFilters.forEach((select) => {
            select.addEventListener('change', applyFilters);
        });
        applyFilters();
    });
});
