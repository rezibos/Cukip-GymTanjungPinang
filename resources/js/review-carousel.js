export function initReviewCarousels() {
    document.querySelectorAll('[data-review-carousel]').forEach((viewport) => {
        const track = viewport.querySelector('[data-review-track]');
        const group = viewport.querySelector('[data-review-group]');
        const cards = Array.from(group?.children ?? []);
        const motionPreference = window.matchMedia('(prefers-reduced-motion: reduce)');

        if (!track || !group || cards.length === 0) {
            return;
        }

        let cycleWidth = 0;
        let offset = 0;
        let velocity = 0;
        let pointer = null;
        let hovered = false;
        let focused = false;
        let paused = motionPreference.matches;
        let visible = false;
        let frame = null;
        let lastTimestamp = null;

        const render = () => {
            if (cycleWidth === 0) {
                return;
            }

            offset = ((offset % cycleWidth) + cycleWidth) % cycleWidth;
            track.style.transform = `translate3d(${-offset}px, 0, 0)`;
        };

        const canAnimate = () => visible && !document.hidden && !pointer
            && (Math.abs(velocity) > 0.01 || (!paused && !hovered && !focused));

        const animate = (timestamp) => {
            frame = null;
            if (!canAnimate()) {
                lastTimestamp = null;
                return;
            }

            const elapsed = lastTimestamp === null ? 0 : Math.min(50, timestamp - lastTimestamp);
            lastTimestamp = timestamp;
            if (Math.abs(velocity) > 0.01) {
                offset += velocity * elapsed;
                velocity *= Math.exp(-elapsed / 240);
            } else {
                velocity = 0;
                offset += elapsed * 0.028;
            }
            render();
            frame = requestAnimationFrame(animate);
        };

        const resume = () => {
            if (frame === null && canAnimate()) {
                lastTimestamp = null;
                frame = requestAnimationFrame(animate);
            }
        };

        const duplicateCard = (card) => {
            const duplicate = card.cloneNode(true);
            duplicate.setAttribute('aria-hidden', 'true');
            duplicate.inert = true;
            return duplicate;
        };

        const measure = () => {
            track.querySelectorAll('[data-review-clone]').forEach((clone) => clone.remove());
            Array.from(group.children).slice(cards.length).forEach((clone) => clone.remove());
            const minimumWidth = viewport.clientWidth + cards[0].getBoundingClientRect().width;
            if (group.getBoundingClientRect().width === 0) {
                return;
            }
            while (group.getBoundingClientRect().width < minimumWidth) {
                cards.forEach((card) => group.append(duplicateCard(card)));
            }
            cycleWidth = group.getBoundingClientRect().width;
            const duplicate = group.cloneNode(true);
            duplicate.dataset.reviewClone = '';
            duplicate.removeAttribute('data-review-group');
            duplicate.setAttribute('aria-hidden', 'true');
            duplicate.inert = true;
            track.append(duplicate);
            viewport.classList.add('is-ready');
            render();
        };

        viewport.addEventListener('pointerenter', (event) => {
            if (event.pointerType === 'mouse' && !pointer) {
                hovered = true;
                velocity = 0;
            }
        });
        viewport.addEventListener('pointerleave', () => {
            hovered = false;
            resume();
        });
        viewport.addEventListener('focusin', () => {
            focused = true;
            velocity = 0;
        });
        viewport.addEventListener('focusout', (event) => {
            focused = viewport.contains(event.relatedTarget);
            resume();
        });
        viewport.addEventListener('pointerdown', (event) => {
            if (!event.isPrimary || event.button !== 0) {
                return;
            }
            if (event.pointerType === 'mouse') {
                event.preventDefault();
            }
            velocity = 0;
            pointer = { id: event.pointerId, x: event.clientX, y: event.clientY, time: event.timeStamp, dragging: false };
            viewport.setPointerCapture(event.pointerId);
        });
        viewport.addEventListener('pointermove', (event) => {
            if (!pointer || pointer.id !== event.pointerId) {
                return;
            }
            const delta = event.clientX - pointer.x;
            if (!pointer.dragging) {
                if (Math.abs(event.clientY - pointer.y) > Math.abs(delta) && Math.abs(event.clientY - pointer.y) > 6) {
                    pointer = null;
                    viewport.releasePointerCapture(event.pointerId);
                    resume();
                    return;
                }
                if (Math.abs(delta) < 6) {
                    return;
                }
                pointer.dragging = true;
                viewport.classList.add('is-dragging');
            }
            event.preventDefault();
            const elapsed = Math.max(8, event.timeStamp - pointer.time);
            velocity = Math.max(-2.4, Math.min(2.4, -delta / elapsed));
            offset -= delta;
            pointer.x = event.clientX;
            pointer.time = event.timeStamp;
            render();
        });

        const releasePointer = (event) => {
            if (!pointer || pointer.id !== event.pointerId) {
                return;
            }
            if (event.type !== 'pointerup' || event.timeStamp - pointer.time > 100 || motionPreference.matches) {
                velocity = 0;
            }
            pointer = null;
            viewport.classList.remove('is-dragging');
            if (viewport.hasPointerCapture(event.pointerId)) {
                viewport.releasePointerCapture(event.pointerId);
            }
            hovered = event.pointerType === 'mouse' && viewport.matches(':hover');
            resume();
        };
        viewport.addEventListener('pointerup', releasePointer);
        viewport.addEventListener('pointercancel', releasePointer);
        viewport.addEventListener('lostpointercapture', releasePointer);
        viewport.addEventListener('dragstart', (event) => event.preventDefault());
        viewport.addEventListener('keydown', (event) => {
            if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') {
                return;
            }
            event.preventDefault();
            velocity = 0;
            offset += (event.key === 'ArrowRight' ? 1 : -1) * (cards[0].getBoundingClientRect().width + 20);
            render();
        });
        motionPreference.addEventListener('change', () => {
            paused = motionPreference.matches;
            velocity = 0;
            resume();
        });
        document.addEventListener('visibilitychange', () => {
            velocity = 0;
            resume();
        });
        new IntersectionObserver(([entry]) => {
            visible = entry.isIntersecting;
            resume();
        }).observe(viewport);
        new ResizeObserver(measure).observe(viewport);
        document.fonts.ready.then(measure);
        measure();
    });
}
