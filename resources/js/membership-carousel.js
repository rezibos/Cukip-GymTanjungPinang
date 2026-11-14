const getCardsPerPage = () => {
    if (window.matchMedia('(min-width: 80rem)').matches) {
        return 3;
    }

    if (window.matchMedia('(min-width: 48rem)').matches) {
        return 2;
    }

    return 1;
};

export const initMembershipCarousels = () => {
    document.querySelectorAll('[data-membership-carousel]').forEach((carousel) => {
        const slider = carousel.closest('[data-membership-slider]');
        const previousButton = slider?.querySelector('[data-membership-previous]');
        const nextButton = slider?.querySelector('[data-membership-next]');
        const pageLabel = slider?.querySelector('[data-membership-page]');
        const cards = Array.from(carousel.children);

        if (!previousButton || !nextButton || !pageLabel || cards.length === 0) {
            return;
        }

        let activePage = 0;
        let pointerId = null;
        let pointerStartX = null;
        let pointerStartY = null;
        let isDragging = false;
        let suppressClick = false;

        const resetPointer = () => {
            pointerId = null;
            pointerStartX = null;
            pointerStartY = null;
            isDragging = false;
            carousel.classList.remove('is-dragging');
        };

        const renderPage = (behavior = 'smooth') => {
            const cardsPerPage = getCardsPerPage();
            const pageCount = Math.ceil(cards.length / cardsPerPage);
            activePage = Math.min(Math.max(activePage, 0), pageCount - 1);
            const targetCard = cards[activePage * cardsPerPage];
            const firstCard = cards[0];

            carousel.scrollTo({
                left: targetCard.offsetLeft - firstCard.offsetLeft,
                behavior,
            });
            previousButton.disabled = activePage === 0;
            nextButton.disabled = activePage === pageCount - 1;
            pageLabel.textContent = `${activePage + 1} / ${pageCount}`;
        };

        previousButton.addEventListener('click', () => {
            activePage -= 1;
            renderPage();
        });

        nextButton.addEventListener('click', () => {
            activePage += 1;
            renderPage();
        });

        carousel.addEventListener('pointerdown', (event) => {
            const isPrimaryMouseButton = event.pointerType !== 'mouse' || event.button === 0;
            const isInteractiveElement = event.target.closest('a, button, input, select, textarea');

            if (!event.isPrimary || !isPrimaryMouseButton || isInteractiveElement) {
                return;
            }

            pointerId = event.pointerId;
            pointerStartX = event.clientX;
            pointerStartY = event.clientY;
            carousel.setPointerCapture(event.pointerId);
        });

        carousel.addEventListener('pointermove', (event) => {
            if (event.pointerId !== pointerId || pointerStartX === null || pointerStartY === null) {
                return;
            }

            const horizontalDistance = event.clientX - pointerStartX;
            const verticalDistance = event.clientY - pointerStartY;

            if (Math.abs(horizontalDistance) > 8 && Math.abs(horizontalDistance) > Math.abs(verticalDistance)) {
                isDragging = true;
                carousel.classList.add('is-dragging');
                event.preventDefault();
            }
        });

        carousel.addEventListener('pointerup', (event) => {
            if (event.pointerId !== pointerId || pointerStartX === null || pointerStartY === null) {
                return;
            }

            const horizontalDistance = event.clientX - pointerStartX;
            const verticalDistance = event.clientY - pointerStartY;
            const isHorizontalSwipe = Math.abs(horizontalDistance) >= 50
                && Math.abs(horizontalDistance) > Math.abs(verticalDistance);

            suppressClick = isDragging;
            window.setTimeout(() => {
                suppressClick = false;
            });
            resetPointer();

            if (!isHorizontalSwipe) {
                return;
            }

            activePage += horizontalDistance < 0 ? 1 : -1;
            renderPage();
        });

        carousel.addEventListener('pointercancel', resetPointer);

        carousel.addEventListener('click', (event) => {
            if (!suppressClick) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            suppressClick = false;
        }, true);

        window.addEventListener('resize', () => renderPage('auto'));
        renderPage('auto');
    });
};
