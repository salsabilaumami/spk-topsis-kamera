document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-confirm]').forEach((element) => {
        element.addEventListener('submit', (event) => {
            const message = element.dataset.confirm || 'Apakah Anda yakin?';
            if (!window.confirm(message)) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-auto-dismiss]').forEach((element) => {
        window.setTimeout(() => bootstrap.Alert.getOrCreateInstance(element).close(), 5000);
    });
});
