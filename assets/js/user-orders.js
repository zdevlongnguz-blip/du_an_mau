document.addEventListener('DOMContentLoaded', () => {
    const filterButtons = Array.from(document.querySelectorAll('.hs-filter__btn'));
    const cards = Array.from(document.querySelectorAll('.hs-order-card'));
    const emptyState = document.querySelector('.hs-empty-filter');

    if (!filterButtons.length || !cards.length) {
        return;
    }

    const applyFilter = (activeFilter) => {
        let visibleCount = 0;

        cards.forEach((card) => {
            const matches = activeFilter === 'all' || card.dataset.status === activeFilter;
            card.style.display = matches ? '' : 'none';
            if (matches) {
                visibleCount += 1;
            }
        });

        filterButtons.forEach((button) => {
            const isActive = button.dataset.filter === activeFilter;
            button.setAttribute('aria-pressed', String(isActive));
        });

        if (emptyState) {
            emptyState.classList.toggle('d-none', visibleCount !== 0);
        }
    };

    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            applyFilter(button.dataset.filter || 'all');
        });
    });

    applyFilter('all');
});
