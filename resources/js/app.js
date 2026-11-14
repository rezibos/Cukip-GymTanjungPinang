import { initReviewCarousels } from './review-carousel';
import { initNotifications, showNotification } from './notifications';

initReviewCarousels();
initNotifications();

const updateCartTotals = (result) => {
    document.querySelectorAll('[data-cart-link]').forEach((cartLink) => {
        cartLink.setAttribute('aria-label', `Buka keranjang, ${result.cart_count} paket`);
    });
    document.querySelectorAll('[data-cart-count]').forEach((cartCount) => {
        cartCount.textContent = result.cart_count;
        cartCount.classList.toggle('hidden', result.cart_count === 0);
    });
    document.querySelectorAll('[data-cart-total-quantity]').forEach((totalQuantity) => {
        totalQuantity.textContent = result.cart_count;
    });
    document.querySelectorAll('[data-cart-total]').forEach((total) => {
        total.textContent = result.formatted_total;
    });
};

const submitCartForm = async (form, formData = new FormData(form)) => {
    const response = await fetch(form.getAttribute('action'), {
        method: 'POST',
        body: formData,
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    });
    const result = await response.json();

    if (!response.ok) {
        throw new Error(result.message ?? 'Keranjang belum berhasil diperbarui.');
    }

    return result;
};

document.querySelectorAll('[data-cart-add]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const button = form.querySelector('button[type="submit"]');
        button?.setAttribute('disabled', '');

        try {
            const result = await submitCartForm(form);
            updateCartTotals(result);
            showNotification(result.title, result.message);
        } catch (error) {
            showNotification('Belum masuk keranjang', error.message, 'error');
        } finally {
            button?.removeAttribute('disabled');
        }
    });
});

document.querySelectorAll('[data-cart-quantity-form]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const submitter = event.submitter;
        const item = form.closest('[data-cart-item]');

        if (!submitter || !item) {
            return;
        }

        const buttons = Array.from(form.querySelectorAll('button[type="submit"]'));
        const formData = new FormData(form);
        formData.set('operation', submitter.value);
        buttons.forEach((button) => button.setAttribute('disabled', ''));

        try {
            const result = await submitCartForm(form, formData);
            const decrease = form.querySelector('[data-cart-decrease]');
            const increase = form.querySelector('[data-cart-increase]');

            item.querySelector('[data-item-quantity]').textContent = result.quantity;
            item.querySelector('[data-item-subtotal]').textContent = result.formatted_subtotal;
            decrease.disabled = result.quantity <= 1;
            increase.disabled = false;
            updateCartTotals(result);
        } catch (error) {
            showNotification('Jumlah belum berubah', error.message, 'error');
        } finally {
            buttons.forEach((button) => {
                if (button.matches('[data-cart-decrease]')) {
                    button.disabled = Number(item.querySelector('[data-item-quantity]').textContent) <= 1;
                } else {
                    button.disabled = false;
                }
            });
        }
    });
});

document.querySelectorAll('[data-cart-remove]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const button = form.querySelector('button[type="submit"]');
        const item = form.closest('[data-cart-item]');
        button?.setAttribute('disabled', '');

        try {
            const result = await submitCartForm(form);
            item.remove();
            updateCartTotals(result);
            showNotification(result.title, result.message);

            if (result.cart_count === 0) {
                window.location.reload();
            }
        } catch (error) {
            showNotification('Paket belum dihapus', error.message, 'error');
            button?.removeAttribute('disabled');
        }
    });
});

document.querySelectorAll('[data-floating-help]').forEach((helpMenu) => {
    const summary = helpMenu.querySelector('summary');

    helpMenu.addEventListener('toggle', () => {
        summary?.setAttribute('aria-expanded', String(helpMenu.open));
    });

    document.addEventListener('click', (event) => {
        if (helpMenu.open && !helpMenu.contains(event.target)) {
            helpMenu.open = false;
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape' || !helpMenu.open) {
            return;
        }

        helpMenu.open = false;
        summary?.focus();
    });
});

document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    const input = document.getElementById(button.getAttribute('aria-controls'));
    if (!input) {
        return;
    }

    button.addEventListener('click', () => {
        const isVisible = input.type === 'password';
        input.type = isVisible ? 'text' : 'password';
        button.setAttribute('aria-pressed', String(isVisible));
        button.setAttribute('aria-label', isVisible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        button.textContent = isVisible ? 'Tutup' : 'Lihat';
    });
});

const menuButton = document.getElementById('menu-button');

document.querySelectorAll('[data-class-schedule]').forEach((schedule) => {
    const buttons = Array.from(schedule.querySelectorAll('[data-class-day]'));
    const sessions = Array.from(schedule.querySelectorAll('[data-class-session]'));
    const emptyState = schedule.querySelector('[data-class-empty]');
    const count = schedule.querySelector('[data-class-count]');

    const selectDay = (day) => {
        let visibleCount = 0;
        sessions.forEach((session) => {
            session.hidden = day !== 'all' && session.dataset.classDate !== day;
            visibleCount += session.hidden ? 0 : 1;
        });
        buttons.forEach((button) => button.setAttribute('aria-pressed', String(button.dataset.classDay === day)));
        emptyState.hidden = visibleCount > 0;
        count.textContent = `${visibleCount} sesi tersedia`;
    };

    buttons.forEach((button) => button.addEventListener('click', () => selectDay(button.dataset.classDay)));
});

document.querySelectorAll('[data-plan-picker]').forEach((picker) => {
    const buttons = Array.from(picker.querySelectorAll('[data-plan-period]'));
    const options = Array.from(picker.querySelectorAll('[data-plan-option]'));

    const selectPeriod = (period) => {
        if (!buttons.some((button) => button.dataset.planPeriod === period)) {
            return;
        }

        picker.dataset.planSelected = period;
        buttons.forEach((button) => button.setAttribute('aria-pressed', String(button.dataset.planPeriod === period)));
        options.forEach((option) => {
            const isActive = option.dataset.planOption === 'all' || option.dataset.planOption === period;
            option.dataset.active = String(isActive);
            option.setAttribute('aria-hidden', String(!isActive));
            option.inert = !isActive;
        });
    };

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            selectPeriod(button.dataset.planPeriod);
        });

        button.addEventListener('keydown', (event) => {
            const index = buttons.indexOf(button);
            let nextIndex = index;

            if (event.key === 'ArrowRight') {
                nextIndex = (index + 1) % buttons.length;
            } else if (event.key === 'ArrowLeft') {
                nextIndex = (index - 1 + buttons.length) % buttons.length;
            } else {
                return;
            }

            event.preventDefault();
            selectPeriod(buttons[nextIndex].dataset.planPeriod);
            buttons[nextIndex].focus();
        });
    });

    const restorePeriod = () => selectPeriod(picker.dataset.planSelected ?? 'monthly');
    restorePeriod();
    window.addEventListener('pageshow', restorePeriod);
});
const mobileMenu = document.getElementById('mobile-menu');

if (menuButton && mobileMenu) {
    menuButton.addEventListener('click', () => {
        const isHidden = mobileMenu.classList.toggle('hidden');
        menuButton.setAttribute('aria-expanded', String(!isHidden));
    });

    document.addEventListener('click', (event) => {
        if (mobileMenu.classList.contains('hidden') || mobileMenu.contains(event.target) || menuButton.contains(event.target)) {
            return;
        }

        mobileMenu.classList.add('hidden');
        menuButton.setAttribute('aria-expanded', 'false');
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape' || mobileMenu.classList.contains('hidden')) {
            return;
        }

        mobileMenu.classList.add('hidden');
        menuButton.setAttribute('aria-expanded', 'false');
        menuButton.focus();
    });
}

if (document.querySelector('[data-map]')) {
    import('./gym-map').then(({ initMaps }) => initMaps());
}

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const revealElements = document.querySelectorAll('.reveal');

if (reduceMotion) {
    revealElements.forEach((element) => element.classList.add('is-visible'));
} else {
    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });

    revealElements.forEach((element) => revealObserver.observe(element));
}

const trainerSection = document.querySelector('.trainer-section');
const facilityRows = document.querySelectorAll('[data-facility-reveal]');

if (reduceMotion) {
    facilityRows.forEach((row) => row.classList.add('is-visible'));
} else {
    const facilityObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.2, rootMargin: '0px 0px -15% 0px' });

    facilityRows.forEach((row) => facilityObserver.observe(row));
}

const trainerKicker = trainerSection?.querySelector('.trainer-kicker');

if (trainerKicker && reduceMotion) {
    trainerKicker.classList.add('is-visible');
} else if (trainerSection && trainerKicker) {
    const trainerSectionObserver = new IntersectionObserver(([entry]) => {
        trainerKicker.classList.toggle('is-visible', entry.isIntersecting);
    }, { threshold: 0.05 });

    trainerSectionObserver.observe(trainerSection);
}

const counters = document.querySelectorAll('[data-counter]');
const counterObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
        if (!entry.isIntersecting) {
            return;
        }

        const counter = entry.target;
        const target = Number(counter.dataset.counter || 0);
        const padLength = Number(counter.dataset.counterPad || 0);
        const formatCounter = (value) => String(value).padStart(padLength, '0');

        if (reduceMotion || target === 0) {
            counter.textContent = formatCounter(target);
        } else {
            const startedAt = performance.now();
            const duration = 900;

            const updateCounter = (timestamp) => {
                const progress = Math.min((timestamp - startedAt) / duration, 1);
                const easedProgress = 1 - Math.pow(1 - progress, 3);
                counter.textContent = formatCounter(Math.round(target * easedProgress));

                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                }
            };

            requestAnimationFrame(updateCounter);
        }

        observer.unobserve(counter);
    });
}, { threshold: 0.6 });

counters.forEach((counter) => counterObserver.observe(counter));

const colorHero = document.querySelector('[data-color-hero]');

const navbarBrand = document.querySelector('[data-navbar-brand]');
const assemblingGallery = document.querySelector('[data-gallery-assemble]');
const faqPortrait = document.querySelector('[data-faq-portrait]');
const joiningJourney = document.querySelector('[data-joining-journey]');
const journeyScrollSection = document.querySelector('[data-journey-scroll]');
const journeyCamera = document.querySelector('[data-journey-camera]');
const journeyCards = Array.from(joiningJourney?.querySelectorAll('.joining-journey-card') ?? []);
const journeyRoute = joiningJourney?.querySelector('[data-journey-route]');
const journeyTrail = joiningJourney?.querySelector('[data-journey-trail]');
const journeyDot = joiningJourney?.querySelector('[data-journey-dot]');
let journeyCenters = [];
let journeyRouteLength = 0;
let journeyMiddleProgress = 0.5;
let journeyTargetProgress = 0;
let journeyRenderedProgress = 0;
let journeyAnimationFrame = null;
let journeyLastTimestamp = 0;

const clampProgress = (value) => Math.min(1, Math.max(0, value));
const easeJourney = (value) => value * value * value * (value * (value * 6 - 15) + 10);

const renderJourney = (progress) => {
    if (!journeyRoute || !journeyCamera || !joiningJourney || journeyCenters.length !== 3 || reduceMotion) {
        return;
    }

    let routeProgress = 0;
    let movingProgress = 0;
    let activeIndex = 0;

    if (progress < 0.48) {
        movingProgress = clampProgress((progress - 0.16) / 0.32);
        routeProgress = easeJourney(movingProgress) * journeyMiddleProgress;
        activeIndex = movingProgress > 0.65 ? 1 : 0;
    } else {
        movingProgress = clampProgress((progress - 0.64) / 0.28);
        routeProgress = journeyMiddleProgress + easeJourney(movingProgress) * (1 - journeyMiddleProgress);
        activeIndex = movingProgress > 0.65 ? 2 : 1;
    }

    const focus = journeyRoute.getPointAtLength(journeyRouteLength * routeProgress);
    const travelZoom = Math.sin(movingProgress * Math.PI) * 0.08;
    const scale = Math.min(1, journeyCamera.clientWidth / 680, journeyCamera.clientHeight / 440) * (1 + travelZoom);
    joiningJourney.style.setProperty('--journey-scale', String(scale));
    joiningJourney.style.setProperty('--journey-x', `${journeyCamera.clientWidth / 2 - focus.x * scale}px`);
    joiningJourney.style.setProperty('--journey-y', `${journeyCamera.clientHeight / 2 - focus.y * scale}px`);
    journeyTrail?.setAttribute('stroke-dashoffset', String(1 - routeProgress));
    journeyDot?.setAttribute('transform', `translate(${focus.x} ${focus.y})`);
    if (movingProgress > 0.01 && movingProgress < 0.99) {
        const direction = journeyTargetProgress >= journeyRenderedProgress ? 1 : -1;
        const ahead = journeyRoute.getPointAtLength(Math.max(0, Math.min(journeyRouteLength, journeyRouteLength * routeProgress + direction * 8)));
        journeyDot?.style.setProperty('--robot-facing', ahead.x >= focus.x ? '1' : '-1');
    }
    journeyDot?.classList.toggle('is-walking', movingProgress > 0.01 && movingProgress < 0.99 && Math.abs(journeyTargetProgress - journeyRenderedProgress) > 0.0001);
    journeyCards.forEach((card, index) => card.classList.toggle('is-current', index === activeIndex));
};

const measureJourney = () => {
    if (!journeyRoute || reduceMotion) {
        return;
    }

    journeyCenters = journeyCards.map((card) => ({
        x: card.offsetLeft + card.offsetWidth / 2,
        y: card.offsetTop + card.offsetHeight / 2,
    }));
    const connectors = journeyCenters.slice(0, -1).map((start, index) => {
        const end = journeyCenters[index + 1];
        const middleY = (start.y + end.y) / 2;
        return `C${start.x} ${middleY + 80} ${end.x} ${middleY - 80} ${end.x} ${end.y}`;
    });
    const first = journeyCenters[0];
    const prefix = `M${first.x} ${first.y}`;
    journeyRoute.setAttribute('d', `${prefix}${connectors.join('')}`);
    journeyTrail?.setAttribute('d', journeyRoute.getAttribute('d'));
    const firstSegment = journeyRoute.cloneNode();
    firstSegment.setAttribute('d', `${prefix}${connectors[0]}`);
    journeyRouteLength = journeyRoute.getTotalLength();
    journeyMiddleProgress = firstSegment.getTotalLength() / journeyRouteLength;
    renderJourney(journeyRenderedProgress);
};

const animateJourney = (timestamp) => {
    const elapsed = journeyLastTimestamp ? Math.min(64, timestamp - journeyLastTimestamp) : 16;
    journeyLastTimestamp = timestamp;
    journeyRenderedProgress += (journeyTargetProgress - journeyRenderedProgress) * (1 - Math.exp(-elapsed / 100));
    renderJourney(journeyRenderedProgress);

    if (Math.abs(journeyTargetProgress - journeyRenderedProgress) > 0.0001) {
        journeyAnimationFrame = requestAnimationFrame(animateJourney);
    } else {
        journeyRenderedProgress = journeyTargetProgress;
        renderJourney(journeyRenderedProgress);
        journeyAnimationFrame = null;
        journeyLastTimestamp = 0;
    }
};

measureJourney();
window.addEventListener('resize', measureJourney, { passive: true });
document.fonts.ready.then(measureJourney);

let scrollFrameRequested = false;

const updateScrollEffects = () => {
    if (joiningJourney && journeyScrollSection && journeyCamera) {
        const bounds = journeyScrollSection.getBoundingClientRect();
        const progress = reduceMotion ? 1 : Math.min(1, Math.max(0, -bounds.top / Math.max(1, bounds.height - window.innerHeight)));
        if (journeyRoute && !reduceMotion) {
            journeyTargetProgress = progress;
            if (journeyAnimationFrame === null) {
                journeyAnimationFrame = requestAnimationFrame(animateJourney);
            }
        }
    }

    if (assemblingGallery) {
        const bounds = assemblingGallery.getBoundingClientRect();
        const progress = reduceMotion ? 1 : Math.min(1, Math.max(0, (window.innerHeight * 0.95 - bounds.top) / (window.innerHeight * 0.6)));
        const easedProgress = progress * progress * (3 - 2 * progress);

        assemblingGallery.style.setProperty('--gallery-scatter', String(1 - easedProgress));
    }

    if (faqPortrait) {
        const bounds = faqPortrait.getBoundingClientRect();
        const progress = reduceMotion ? 1 : clampProgress((window.innerHeight * 0.55 - bounds.top) / (window.innerHeight * 0.45));
        const easedProgress = progress * progress * (3 - 2 * progress);
        const remainingProgress = 1 - easedProgress;
        const lift = Math.min(128, bounds.height * 0.18);

        faqPortrait.style.setProperty('--faq-portrait-y', `${-lift * remainingProgress}px`);
        faqPortrait.style.setProperty('--faq-portrait-scale', String(1 + 0.12 * remainingProgress));
    }

    if (navbarBrand) {
        const surfacesAtBrand = document.elementsFromPoint(
            Math.min(64, window.innerWidth - 1),
            Math.min(48, window.innerHeight - 1),
        );
        const activeSurface = surfacesAtBrand
            .map((element) => element.closest?.('[data-navbar-tone]'))
            .find(Boolean);

        navbarBrand.classList.toggle('is-on-dark', activeSurface?.dataset.navbarTone === 'dark');
    }

    scrollFrameRequested = false;
};

window.addEventListener('scroll', () => {
    if (scrollFrameRequested) {
        return;
    }

    requestAnimationFrame(updateScrollEffects);
    scrollFrameRequested = true;
}, { passive: true });

window.addEventListener('resize', updateScrollEffects, { passive: true });

updateScrollEffects();

if (colorHero && window.matchMedia('(hover: hover)').matches) {
    let pointerFrameRequested = false;
    let pointerX = 0;
    let pointerY = 0;

    const updatePointerReveal = () => {
        const bounds = colorHero.getBoundingClientRect();
        colorHero.style.setProperty('--cursor-x', `${pointerX - bounds.left}px`);
        colorHero.style.setProperty('--cursor-y', `${pointerY - bounds.top}px`);
        pointerFrameRequested = false;
    };

    colorHero.addEventListener('pointerenter', () => {
        colorHero.classList.add('is-pointer-active');
    });

    colorHero.addEventListener('pointermove', (event) => {
        pointerX = event.clientX;
        pointerY = event.clientY;

        if (!pointerFrameRequested) {
            requestAnimationFrame(updatePointerReveal);
            pointerFrameRequested = true;
        }
    });

    colorHero.addEventListener('pointerleave', () => {
        colorHero.classList.remove('is-pointer-active');
    });
}

const aboutReveal = document.querySelector('[data-about-reveal]');
const aboutTitle = document.querySelector('[data-about-title]');

if (aboutReveal && aboutTitle && window.matchMedia('(hover: hover)').matches) {
    let aboutPointerFrameRequested = false;
    let aboutPointerX = 0;
    let aboutPointerY = 0;

    const updateAboutPointerReveal = () => {
        const titleBounds = aboutTitle.getBoundingClientRect();
        aboutTitle.style.setProperty('--about-pointer-x', `${aboutPointerX - titleBounds.left}px`);
        aboutTitle.style.setProperty('--about-pointer-y', `${aboutPointerY - titleBounds.top}px`);
        aboutPointerFrameRequested = false;
    };

    aboutReveal.addEventListener('pointerenter', () => {
        aboutReveal.classList.add('is-pointer-active');
    });

    aboutReveal.addEventListener('pointermove', (event) => {
        aboutPointerX = event.clientX;
        aboutPointerY = event.clientY;

        if (!aboutPointerFrameRequested) {
            requestAnimationFrame(updateAboutPointerReveal);
            aboutPointerFrameRequested = true;
        }
    });

    aboutReveal.addEventListener('pointerleave', () => {
        aboutReveal.classList.remove('is-pointer-active');
    });
}

document.querySelectorAll('[data-trainer-carousel]').forEach((carousel) => {
    const cards = Array.from(carousel.querySelectorAll('[data-trainer-card]'));
    const dots = Array.from(carousel.querySelectorAll('[data-trainer-dot]'));
    const previousButton = carousel.querySelector('[data-trainer-prev]');
    const nextButton = carousel.querySelector('[data-trainer-next]');
    let activeIndex = 0;

    if (cards.length === 0) {
        return;
    }

    const updateTrainerCarousel = () => {
        cards.forEach((card, index) => {
            const offset = (index - activeIndex + cards.length) % cards.length;
            let position = 'hidden';

            if (offset === 0) {
                position = 'active';
            } else if (offset === 1) {
                position = 'next';
            } else if (offset === cards.length - 1) {
                position = 'previous';
            }

            card.dataset.position = position;
            card.setAttribute('aria-hidden', String(position === 'hidden'));

            const cardLink = card.querySelector('a');
            if (cardLink) {
                cardLink.tabIndex = position === 'active' ? 0 : -1;
            }

        });

        dots.forEach((dot, index) => {
            dot.classList.toggle('is-active', index === activeIndex);
        });
    };

    const showPreviousTrainer = () => {
        activeIndex = (activeIndex - 1 + cards.length) % cards.length;
        updateTrainerCarousel();
    };

    const showNextTrainer = () => {
        activeIndex = (activeIndex + 1) % cards.length;
        updateTrainerCarousel();
    };

    previousButton?.addEventListener('click', showPreviousTrainer);
    nextButton?.addEventListener('click', showNextTrainer);

    cards.forEach((card) => {
        card.addEventListener('click', (event) => {
            if (event.target.closest('a, button')) {
                return;
            }

            if (card.dataset.position === 'active') {
                card.dataset.flipped = card.dataset.flipped === 'true' ? 'false' : 'true';
            } else if (card.dataset.position === 'previous') {
                showPreviousTrainer();
            } else if (card.dataset.position === 'next') {
                showNextTrainer();
            }
        });
    });

    carousel.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft') {
            showPreviousTrainer();
        } else if (event.key === 'ArrowRight') {
            showNextTrainer();
        }
    });

    updateTrainerCarousel();
});

document.querySelectorAll('[data-member-story-carousel]').forEach((carousel) => {
    const slides = Array.from(carousel.querySelectorAll('[data-member-story-slide]'));
    const dots = Array.from(carousel.querySelectorAll('[data-member-story-dot]'));
    const previousButton = carousel.querySelector('[data-member-story-prev]');
    const nextButton = carousel.querySelector('[data-member-story-next]');
    let activeIndex = 0;

    if (slides.length === 0) {
        return;
    }

    const showMemberStory = (index) => {
        activeIndex = (index + slides.length) % slides.length;

        slides.forEach((slide, slideIndex) => {
            const isActive = slideIndex === activeIndex;

            slide.classList.toggle('is-active', isActive);
            slide.setAttribute('aria-hidden', String(!isActive));
        });

        dots.forEach((dot, dotIndex) => {
            const isActive = dotIndex === activeIndex;

            dot.classList.toggle('is-active', isActive);
            dot.setAttribute('aria-current', String(isActive));
        });
    };

    previousButton?.addEventListener('click', () => showMemberStory(activeIndex - 1));
    nextButton?.addEventListener('click', () => showMemberStory(activeIndex + 1));

    dots.forEach((dot, dotIndex) => {
        dot.addEventListener('click', () => showMemberStory(dotIndex));
    });

    slides.forEach((slide) => {
        const comparisonRange = slide.querySelector('[data-member-transform-range]');

        comparisonRange?.addEventListener('input', () => {
            slide.style.setProperty('--member-transform-position', `${comparisonRange.value}%`);
        });
    });

    carousel.addEventListener('keydown', (event) => {
        if (event.target instanceof HTMLInputElement) {
            return;
        }

        if (event.key === 'ArrowLeft') {
            showMemberStory(activeIndex - 1);
        } else if (event.key === 'ArrowRight') {
            showMemberStory(activeIndex + 1);
        }
    });

    showMemberStory(activeIndex);
});

const bmiForm = document.querySelector('[data-bmi-form]');

if (bmiForm) {
    const bmiResult = document.querySelector('[data-bmi-result]');
    const bmiValue = document.querySelector('[data-bmi-value]');
    const bmiCategory = document.querySelector('[data-bmi-category]');
    const bmiWeight = document.querySelector('[data-bmi-weight]');
    const bmiHeight = document.querySelector('[data-bmi-height]');
    const bmiMessage = document.querySelector('[data-bmi-message]');
    const bmiGoals = document.querySelector('[data-bmi-goals]');
    const bmiGoalsTitle = document.querySelector('[data-bmi-goals-title]');
    const bmiGoalsDescription = document.querySelector('[data-bmi-goals-description]');
    const goalButtons = document.querySelectorAll('[data-bmi-goal]');
    const goalResult = document.querySelector('[data-bmi-goal-result]');
    const goalTitle = document.querySelector('[data-bmi-goal-title]');
    const goalMessage = document.querySelector('[data-bmi-goal-message]');

    const bmiGoalProfiles = {
        underweight: {
            title: 'Fokus membangun tubuh lebih kuat',
            description: 'Pilih fokus yang mendukung kekuatan, massa otot, dan kebugaranmu:',
            goals: [
                {
                    key: 'base-strength',
                    label: 'Tingkatkan Kekuatan',
                    title: 'Mulai dari kekuatan dasar',
                    message: 'Pilih latihan seluruh tubuh dengan beban yang nyaman, lalu tambah kemampuan secara bertahap.',
                },
                {
                    key: 'muscle-mass',
                    label: 'Bangun Massa Otot',
                    title: 'Bangun massa otot bertahap',
                    message: 'Utamakan latihan kekuatan yang konsisten dengan progres beban yang terukur.',
                },
                {
                    key: 'balanced-fitness',
                    label: 'Jaga Kebugaran',
                    title: 'Kebugaran yang seimbang',
                    message: 'Gabungkan latihan kekuatan, kardio ringan, dan mobilitas sesuai kemampuanmu.',
                },
            ],
        },
        normal: {
            title: 'Pertahankan kondisi dan pilih targetmu',
            description: 'BMI-mu berada dalam rentang normal. Pilih fokus latihan berikutnya:',
            goals: [
                {
                    key: 'muscle',
                    label: 'Bangun Otot',
                    title: 'Fokus membangun otot',
                    message: 'Prioritaskan latihan kekuatan dan tingkatkan beban atau repetisi secara bertahap.',
                },
                {
                    key: 'endurance',
                    label: 'Tingkatkan Stamina',
                    title: 'Fokus meningkatkan stamina',
                    message: 'Pilih aktivitas aerobik yang kamu sukai dan tambah durasinya secara bertahap.',
                },
                {
                    key: 'maintain',
                    label: 'Jaga Kebugaran',
                    title: 'Pertahankan kebugaran',
                    message: 'Seimbangkan latihan kekuatan, kardio, dan mobilitas dalam rutinitas mingguanmu.',
                },
            ],
        },
        overweight: {
            title: 'Bangun progres yang konsisten',
            description: 'Pilih fokus yang realistis dan dapat kamu tingkatkan secara bertahap:',
            goals: [
                {
                    key: 'fat-loss',
                    label: 'Turunkan Lemak',
                    title: 'Fokus menurunkan lemak',
                    message: 'Kombinasikan aktivitas aerobik dan latihan kekuatan dalam rutinitas yang konsisten.',
                },
                {
                    key: 'strength',
                    label: 'Bangun Kekuatan',
                    title: 'Bangun kekuatan tubuh',
                    message: 'Mulai dengan gerakan dasar dan beban yang nyaman sebelum menaikkan tantangannya.',
                },
                {
                    key: 'stamina',
                    label: 'Tingkatkan Stamina',
                    title: 'Tingkatkan stamina bertahap',
                    message: 'Mulai dari kardio intensitas ringan hingga sedang, lalu tambah durasi secara perlahan.',
                },
            ],
        },
        obesity: {
            title: 'Mulai nyaman, lalu tambah bertahap',
            description: 'Pilih fokus awal yang sesuai dengan kemampuan dan kenyamanan tubuhmu:',
            goals: [
                {
                    key: 'low-impact',
                    label: 'Aktivitas Low Impact',
                    title: 'Mulai dengan aktivitas low impact',
                    message: 'Pilih aktivitas seperti berjalan, bersepeda statis, atau latihan mobilitas dengan intensitas nyaman.',
                },
                {
                    key: 'consistency',
                    label: 'Bangun Kebiasaan',
                    title: 'Bangun rutinitas yang konsisten',
                    message: 'Mulai dari sesi singkat yang mudah diulang, lalu tambah durasinya sedikit demi sedikit.',
                },
                {
                    key: 'guided-strength',
                    label: 'Latihan Kekuatan',
                    title: 'Kekuatan sesuai kemampuan',
                    message: 'Gunakan gerakan dan beban yang nyaman; minta arahan trainer bila kamu memerlukannya.',
                },
            ],
        },
    };
    let activeGoalRecommendations = {};

    const formatMeasurement = (value) => new Intl.NumberFormat('id-ID', {
        maximumFractionDigits: 1,
    }).format(value);

    const renderGoalProfile = (profileKey) => {
        const profile = bmiGoalProfiles[profileKey];

        bmiGoalsTitle.textContent = profile.title;
        bmiGoalsDescription.textContent = profile.description;
        activeGoalRecommendations = {};

        goalButtons.forEach((button, index) => {
            const goal = profile.goals[index];

            button.textContent = goal.label;
            button.dataset.bmiGoal = goal.key;
            button.setAttribute('aria-pressed', 'false');
            button.classList.remove('border-white', 'bg-white', 'text-[#171717]', 'hover:text-[#171717]');
            button.classList.add('text-white/75', 'hover:text-white');
            activeGoalRecommendations[goal.key] = goal;
        });

        goalResult.classList.add('hidden');
        goalResult.classList.remove('flex');
    };

    const showBmiResult = ({ value, category, weight, height, message, profileKey = null, isValid = true }) => {
        bmiValue.textContent = value;
        bmiCategory.textContent = category;
        bmiWeight.textContent = weight;
        bmiHeight.textContent = height;
        bmiMessage.textContent = message;
        bmiGoals.classList.toggle('hidden', !isValid);

        if (profileKey) {
            renderGoalProfile(profileKey);
        }

        bmiResult.classList.remove('hidden');
    };

    goalButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const recommendation = activeGoalRecommendations[button.dataset.bmiGoal];

            goalButtons.forEach((goalButton) => {
                const isSelected = goalButton === button;

                goalButton.setAttribute('aria-pressed', String(isSelected));
                goalButton.classList.toggle('border-white', isSelected);
                goalButton.classList.toggle('bg-white', isSelected);
                goalButton.classList.toggle('text-[#171717]', isSelected);
                goalButton.classList.toggle('text-white/75', !isSelected);
                goalButton.classList.toggle('hover:text-[#171717]', isSelected);
                goalButton.classList.toggle('hover:text-white', !isSelected);
            });

            goalTitle.textContent = recommendation.title;
            goalMessage.textContent = recommendation.message;
            goalResult.classList.remove('hidden');
            goalResult.classList.add('flex');
        });
    });

    bmiForm.addEventListener('submit', (event) => {
        event.preventDefault();

        const formData = new FormData(bmiForm);
        const weight = Number(formData.get('weight'));
        const height = Number(formData.get('height'));
        const age = Number(formData.get('age'));

        if (!Number.isFinite(weight) || !Number.isFinite(height) || !Number.isInteger(age)
            || weight < 20 || weight > 400 || height < 100 || height > 250
            || age < 19 || age > 100) {
            showBmiResult({
                value: '—',
                category: 'Data belum valid',
                weight: '20–400 kg',
                height: '100–250 cm',
                message: 'Masukkan berat dan tinggi sesuai rentang, serta umur dewasa di atas 18 tahun.',
                isValid: false,
            });
            return;
        }

        const heightInMeters = height / 100;
        const bmi = weight / (heightInMeters ** 2);
        let category = 'Obesitas';
        let message = 'Berat badanmu berada dalam kategori obesitas berdasarkan BMI dewasa.';
        let profileKey = 'obesity';

        if (bmi < 18.5) {
            category = 'Berat badan kurang';
            message = 'Berat badanmu berada di bawah rentang normal berdasarkan BMI dewasa.';
            profileKey = 'underweight';
        } else if (bmi < 25) {
            category = 'Normal';
            message = 'Berat badanmu berada dalam rentang normal berdasarkan BMI.';
            profileKey = 'normal';
        } else if (bmi < 27) {
            category = 'Kelebihan berat badan';
            message = 'Berat badanmu berada di atas rentang normal berdasarkan BMI dewasa.';
            profileKey = 'overweight';
        }

        showBmiResult({
            value: bmi.toFixed(1).replace('.', ','),
            category,
            weight: `${formatMeasurement(weight)} kg`,
            height: `${formatMeasurement(height)} cm`,
            message,
            profileKey,
        });
    });
}
