document.addEventListener('DOMContentLoaded', function () {
    const toggles = document.querySelectorAll('[data-toggle-password]');

    toggles.forEach(function (toggle) {
        const input = toggle.closest('.hs-pw')?.querySelector('input');
        if (!input) return;

        toggle.addEventListener('click', function () {
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            const icon = toggle.querySelector('i');
            if (icon) {
                icon.classList.toggle('bi-eye', !show);
                icon.classList.toggle('bi-eye-slash', show);
            }
        });
    });
});
