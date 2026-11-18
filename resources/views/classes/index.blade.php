@extends('layouts.app')

@section('title', 'Jadwal Kelas - CukipGym')
@section('description', 'Jadwal kelas kebugaran CukipGym untuk 7 hari ke depan.')
@section('main-class', 'w-full flex-1 bg-white')

@section('content')
@php
    $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $totalSessions = 0;
    foreach ($sessions as $daySessions) {
        $totalSessions += count($daySessions);
    }
@endphp

<section class="relative isolate overflow-hidden px-5 pb-12 pt-32 text-[#171717] sm:px-8 lg:px-12 lg:pb-14 lg:pt-40" aria-labelledby="classes-title" data-navbar-tone="light">
    <div class="pointer-events-none absolute -right-40 -top-56 -z-10 size-[40rem] rounded-full border border-black/[.05] p-16" aria-hidden="true"><div class="size-full rounded-full border border-black/[.04] p-16"><div class="size-full rounded-full border border-black/[.04]"></div></div></div>
    <div class="mx-auto grid max-w-[80rem] gap-8 lg:grid-cols-[1.25fr_.75fr] lg:items-end lg:gap-20">
        <div>
            <p class="section-kicker">Jadwal kelas</p>
            <h1 id="classes-title" class="mt-6 text-[clamp(3rem,6.7vw,6.5rem)] font-semibold leading-[.95] tracking-[-.065em]">Cari kelasmu.<br><span class="text-[#858585]">Temukan energimu.</span></h1>
        </div>
        <div class="max-w-sm lg:pb-1 lg:justify-self-end">
            <p class="text-justify text-sm leading-7 text-[#737373]">Satu jadwal untuk langkah berikutnya. Temukan sesi, kenali instruktur, dan pilih waktu latihan yang cocok dengan rutinitasmu.</p>
            <div class="mt-6 inline-flex items-center gap-3 rounded-full border border-black/10 px-4 py-3 text-xs font-medium">
                <svg class="size-4 text-[#858585]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M7 3v4m10-4v4M3 11h18"/></svg>
                {{ $start->translatedFormat('d M') }} – {{ $end->translatedFormat('d M Y') }}
            </div>
        </div>
    </div>
</section>

<section class="px-5 pb-20 text-[#171717] sm:px-8 lg:px-12 lg:pb-24" aria-labelledby="schedule-title" data-navbar-tone="light" data-class-schedule>
    <div class="mx-auto max-w-[80rem]">
        <div class="flex flex-col justify-between gap-5 border-y border-black/10 py-5 sm:flex-row sm:items-center sm:gap-8 sm:py-6">
            <div class="flex items-center gap-4">
                <span class="grid size-11 shrink-0 place-items-center rounded-full bg-black/[.045] text-[#525252]" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M6 8V6a6 6 0 0 1 12 0v2M5 8h14v13H5Z"/></svg></span>
                <div>
                    <h2 class="text-base font-semibold tracking-[-.025em] text-[#171717]">{{ $canBook ? 'Waktunya pilih sesi berikutnya.' : 'Latihan bareng, lebih terarah.' }}</h2>
                    <p class="mt-1 text-xs leading-6 text-[#737373]">{{ $canBook ? 'Pilih kelas dan amankan slotmu selama masih tersedia.' : 'Booking kelas tersedia untuk member dengan membership aktif.' }}</p>
                </div>
            </div>
            @guest
                <a href="{{ route('membership.index') }}#pilihan-paket" class="inline-flex min-h-11 w-full shrink-0 items-center justify-between gap-6 rounded-full bg-[#171717] px-5 text-xs font-semibold text-white transition hover:bg-[#404040] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717] sm:w-fit">Pilih membership <span aria-hidden="true">↗</span></a>
            @else
                @if (! $canBook)
                    <a href="{{ route('membership.index') }}" class="inline-flex min-h-11 w-full shrink-0 items-center justify-between gap-6 rounded-full bg-[#171717] px-5 text-xs font-semibold text-white transition hover:bg-[#404040] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717] sm:w-fit">Pilih membership <span aria-hidden="true">↗</span></a>
                @else
                    <span class="inline-flex w-fit shrink-0 items-center gap-2 rounded-full border border-black/10 px-4 py-2.5 text-xs font-medium text-[#525252]"><span class="size-1.5 rounded-full bg-[#171717]" aria-hidden="true"></span>Member aktif</span>
                @endif
            @endguest
        </div>

        <div class="mt-8 flex items-end justify-between gap-5">
            <h2 id="schedule-title" class="text-2xl font-semibold tracking-[-.04em]">Jadwal tujuh hari.</h2>
            <p class="text-xs text-[#858585]" aria-live="polite" data-class-count>{{ $totalSessions }} sesi tersedia</p>
        </div>
        <div class="mt-6 flex gap-2 overflow-x-auto pb-3" role="group" aria-label="Pilih hari latihan">
            <button type="button" class="class-day-button min-w-24 flex-1 rounded-2xl border border-black/10 px-5 py-4 text-left text-[#737373] transition" data-class-day="all" aria-pressed="true" aria-controls="class-results"><span class="block text-xs font-medium">Semua</span><span class="mt-2 block text-lg font-semibold tracking-[-.04em]">7 hari</span></button>
            @for ($dayOffset = 0; $dayOffset < 7; $dayOffset++)
                @php
                    $scheduleDay = $start->copy()->addDays($dayOffset);
                    $scheduleDate = $scheduleDay->toDateString();
                    $dayCount = isset($sessions[$scheduleDate]) ? count($sessions[$scheduleDate]) : 0;
                @endphp
                <button type="button" class="class-day-button min-w-24 flex-1 rounded-2xl border border-black/10 px-5 py-4 text-left text-[#737373] transition" data-class-day="{{ $scheduleDate }}" aria-pressed="false" aria-controls="class-results" aria-label="{{ $dayNames[$scheduleDay->dayOfWeek] }}, {{ $scheduleDay->format('d M') }}, {{ $dayCount }} sesi"><span class="block text-xs font-medium">{{ $dayNames[$scheduleDay->dayOfWeek] }}</span><span class="mt-2 block text-lg font-semibold tracking-[-.04em]">{{ $scheduleDay->format('d') }} <span class="text-[.65rem] font-normal opacity-50">{{ $scheduleDay->format('M') }}</span></span></button>
            @endfor
        </div>

        <div id="class-results" class="mt-5 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($sessions as $date => $daySessions)
                @foreach ($daySessions as $session)
                    @php
                        $remaining = max(0, $session->capacity - $session->active_bookings_count);
                        $booking = $myBookings->get($session->id);
                        $isBooked = $booking !== null;
                        $availability = $session->capacity > 0 ? min(100, $remaining / $session->capacity * 100) : 0;
                    @endphp
                    <article class="flex min-w-0 flex-col rounded-[1.5rem] border border-black/[.06] bg-[#f7f7f7] p-6 transition-colors hover:border-black/20 sm:p-7" data-class-session data-class-date="{{ $date }}" aria-labelledby="class-{{ $session->id }}">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-[.6rem] font-semibold uppercase tracking-[.14em] text-[#858585]">{{ $dayNames[$session->date->dayOfWeek] }}, {{ $session->date->format('d M') }}</p>
                            <span class="rounded-full border border-black/10 px-2.5 py-1 text-[.6rem] font-medium {{ $isBooked ? 'bg-[#171717] text-white' : 'text-[#737373]' }}">{{ $isBooked ? 'Terdaftar' : ($remaining > 0 ? 'Tersedia' : 'Penuh') }}</span>
                        </div>
                        <p class="mt-7 text-2xl font-semibold tracking-[-.05em]">{{ $session->gymClass->time_range }}</p>
                        <h3 id="class-{{ $session->id }}" class="mt-3 break-words text-xl font-semibold tracking-[-.035em]">{{ $session->gymClass->name }}</h3>
                        @if ($session->gymClass->description)
                            <p class="mt-3 text-sm leading-7 text-[#737373]">{{ $session->gymClass->description }}</p>
                        @endif
                        <div class="mb-7 mt-5 flex items-center gap-3">
                            <span class="grid size-9 shrink-0 place-items-center rounded-full bg-black/[.05] text-xs font-medium text-[#737373]" aria-hidden="true">{{ mb_strtoupper(mb_substr($session->gymClass->instructor ?? 'Tim', 0, 1)) }}</span>
                            <div><p class="text-[.6rem] text-[#a3a3a3]">Instruktur</p><p class="mt-0.5 text-xs font-medium text-[#525252]">{{ $session->gymClass->instructor ?: 'Tim CukipGym' }}</p></div>
                        </div>
                        <div class="mt-auto border-t border-black/[.07] pt-5">
                            <div class="flex justify-between gap-4 text-xs"><p class="font-medium {{ $remaining > 0 ? 'text-[#525252]' : 'text-[#a3a3a3]' }}">{{ $remaining > 0 ? "$remaining slot tersisa" : 'Kelas penuh' }}</p><p class="text-[#a3a3a3]">{{ $session->capacity }} peserta</p></div>
                            <div class="mt-3 h-1 overflow-hidden rounded-full bg-black/[.06]" aria-hidden="true"><div class="h-full rounded-full bg-[#737373]" style="width: {{ $availability }}%"></div></div>
                            @if ($isBooked)
                                <form method="POST" action="{{ route('classes.bookings.cancel', $booking) }}" class="mt-5" data-class-cancel-form data-class-name="{{ $session->gymClass->name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex min-h-11 w-full items-center justify-center gap-2 rounded-full border border-[#8f3f3f]/30 px-5 text-xs font-semibold text-[#8f3f3f] transition-colors hover:border-[#8f3f3f] hover:bg-[#8f3f3f] hover:text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#8f3f3f]">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 8l8 8M16 8l-8 8" /></svg>
                                        Batalkan booking
                                    </button>
                                </form>
                            @elseif ($remaining <= 0)
                                <button type="button" disabled class="mt-5 min-h-11 w-full cursor-not-allowed rounded-full bg-black/[.04] px-5 text-xs font-semibold text-[#a3a3a3]">Slot habis</button>
                            @elseif ($canBook)
                                <form method="POST" action="{{ route('classes.book', $session) }}" class="mt-5">
                                    @csrf
                                    <button type="submit" class="flex min-h-11 w-full items-center justify-between rounded-full bg-[#171717] px-5 text-xs font-semibold text-white transition hover:bg-[#404040] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717]">Booking kelas <span aria-hidden="true">↗</span></button>
                                </form>
                            @else
                                <a href="{{ route('membership.index') }}#pilihan-paket" class="mt-5 flex min-h-11 items-center justify-between rounded-full border border-black/15 px-5 text-xs font-semibold transition hover:bg-[#171717] hover:text-white">{{ auth()->check() ? 'Aktifkan membership' : 'Pilih membership' }} <span aria-hidden="true">↗</span></a>
                            @endif
                        </div>
                    </article>
                @endforeach
            @endforeach
        </div>
        <div class="mt-5 rounded-[1.5rem] border border-dashed border-black/15 bg-[#f7f7f7] px-7 py-14 text-center" data-class-empty @if ($totalSessions > 0) hidden @endif>
            <h3 class="text-xl font-semibold tracking-[-.03em]">Belum ada sesi untuk pilihan ini.</h3>
            <p class="mx-auto mt-3 max-w-sm text-sm leading-7 text-[#737373]">Pilih hari lain atau cek kembali nanti untuk melihat jadwal kelas yang tersedia.</p>
        </div>
        <p class="mt-8 text-xs leading-6 text-[#858585]">Sisa slot mengikuti booking yang terdaftar. Kamu bisa melihat kelas yang sudah dipilih melalui halaman profil.</p>
    </div>
</section>

@if ($trainers->isNotEmpty())
    @php
        $trainerImages = ['images/gym-kettlebell-editorial.webp', 'images/gym-cable-row-editorial.webp', 'images/gympinang-hero-color.png', 'images/gym-equipment-editorial.webp'];
    @endphp
    <section id="personal-trainer" class="bg-white px-5 py-20 text-[#171717] sm:px-8 lg:px-12 lg:py-28" aria-labelledby="personal-trainer-title" data-navbar-tone="light">
        <div class="mx-auto max-w-[90rem]">
            <div class="grid gap-8 lg:grid-cols-[1fr_.55fr] lg:items-end">
                <div>
                    <p class="section-kicker">Private training</p>
                    <h2 id="personal-trainer-title" class="mt-6 text-[clamp(2.8rem,5.5vw,5.8rem)] font-semibold leading-[1.04] tracking-[-.065em]">Temukan pelatih.<br><span class="text-[#858585]">Maksimalkan potensi.</span></h2>
                </div>
                <p class="max-w-md text-sm leading-7 text-[#737373] lg:justify-self-end">Pilih pelatih, tentukan sesi dari kalender, lalu selesaikan pembayaran. Satu slot berlaku untuk satu sesi latihan privat.</p>
            </div>

            <div class="mt-12 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($trainers as $trainer)
                    <x-personal-trainer-card :trainer="$trainer" :fallback-image="$trainerImages[$loop->index % count($trainerImages)]" />
                @endforeach
            </div>
        </div>
    </section>
@endif

<div class="relative -mb-px h-16 overflow-hidden bg-white sm:h-24" aria-hidden="true"><svg class="absolute inset-0 h-full w-full text-[#0a0a0a]" viewBox="0 0 1440 160" preserveAspectRatio="none" fill="currentColor"><path d="M0 94C260 28 470 34 740 104S1200 166 1440 62V160H0Z"/></svg></div>
@endsection

@push('scripts')
@if ($trainers->isNotEmpty())
<script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@endif
<script>
document.addEventListener('DOMContentLoaded', function () {
    const finishUrl = @json(route('payments.finish'));
    const pendingUrl = @json(route('payments.pending'));
    const errorUrl = @json(route('payments.error'));

    const paymentResultUrl = function (baseUrl, orderId) {
        const url = new URL(baseUrl, window.location.origin);
        url.searchParams.set('order_id', orderId);

        return url.toString();
    };

    const showNotification = function (title, message, type = 'success', target = document.body) {
        document.dispatchEvent(new CustomEvent('gym:notification', {
            detail: { title, message, type, target },
        }));
    };

    document.querySelectorAll('[data-class-cancel-form]').forEach(function (form) {
        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            const className = form.dataset.className || 'kelas ini';
            const shouldCancel = await window.gymConfirm({
                title: `Batalkan booking ${className}?`,
                message: 'Slotmu akan tersedia kembali untuk member lain.',
                confirmButtonText: 'Ya, batalkan',
                cancelButtonText: 'Tetap booking',
            });

            if (shouldCancel) {
                form.submit();
            }
        });
    });

    document.querySelectorAll('[data-trainer-dialog-open]').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById(button.dataset.trainerDialogOpen)?.showModal();
        });
    });

    document.querySelectorAll('[data-trainer-dialog-close]').forEach(function (button) {
        button.addEventListener('click', function () {
            button.closest('dialog')?.close();
        });
    });

    document.querySelectorAll('[data-trainer-booking-form]').forEach(function (form) {
        const total = form.querySelector('[data-trainer-total]');
        const count = form.querySelector('[data-trainer-count]');
        const submitButton = form.querySelector('[data-trainer-submit]');
        const dateButtons = form.querySelectorAll('[data-trainer-date-button]');
        const datePanels = form.querySelectorAll('[data-trainer-date-panel]');
        const price = Number(form.dataset.sessionPrice);
        const dateRail = form.querySelector('[data-trainer-date-rail]');
        const previousDates = form.querySelector('[data-trainer-calendar-prev]');
        const nextDates = form.querySelector('[data-trainer-calendar-next]');
        const submitLabel = form.querySelector('[data-trainer-submit-label]');
        const submitSpinner = form.querySelector('[data-trainer-submit-spinner]');
        const submitArrow = form.querySelector('[data-trainer-submit-arrow]');
        const paymentMessage = form.querySelector('[data-trainer-payment-message]');
        const cancelButton = form.querySelector('[data-trainer-cancel-payment]');
        const cancelLabel = form.querySelector('[data-trainer-cancel-label]');
        const trainerDialog = form.closest('dialog');
        const dialogScroller = form.querySelector('[data-trainer-dialog-scroll]');
        let paymentData = form.dataset.trainerPendingPayment
            ? JSON.parse(form.dataset.trainerPendingPayment)
            : null;
        let restoredPendingPayment = Boolean(paymentData);
        let isSubmitting = false;
        let pageScrollPosition = window.scrollY;
        let dialogScrollPosition = 0;
        let isPageScrollLocked = false;
        let bodyScrollStyles = null;

        const showPaymentMessage = function (message, isError = false) {
            paymentMessage.textContent = message;
            paymentMessage.classList.remove('hidden', 'bg-[#f4f4f2]', 'text-[#525252]', 'bg-red-50', 'text-red-600');
            paymentMessage.classList.add(isError ? 'bg-red-50' : 'bg-[#f4f4f2]', isError ? 'text-red-600' : 'text-[#525252]');
        };

        const clearPaymentMessage = function () {
            paymentMessage.textContent = '';
            paymentMessage.classList.add('hidden');
        };

        const setSubmitting = function (submitting) {
            isSubmitting = submitting;
            submitButton.disabled = submitting || (!paymentData && form.querySelectorAll('input[name="slot_ids[]"]:checked').length === 0);
            submitLabel.textContent = submitting ? 'Menyiapkan pembayaran...' : (paymentData ? 'Lanjutkan pembayaran' : 'Booking dan bayar');
            submitSpinner.classList.toggle('hidden', !submitting);
            submitArrow.classList.toggle('hidden', submitting);
        };

        const rememberScrollPosition = function () {
            pageScrollPosition = window.scrollY;
            dialogScrollPosition = dialogScroller?.scrollTop ?? 0;
        };

        const lockPageScroll = function () {
            if (isPageScrollLocked) {
                return;
            }

            bodyScrollStyles = {
                position: document.body.style.position,
                top: document.body.style.top,
                left: document.body.style.left,
                right: document.body.style.right,
                width: document.body.style.width,
            };
            document.body.style.position = 'fixed';
            document.body.style.top = `-${pageScrollPosition}px`;
            document.body.style.left = '0';
            document.body.style.right = '0';
            document.body.style.width = '100%';
            isPageScrollLocked = true;
        };

        const unlockPageScroll = function () {
            if (!isPageScrollLocked || !bodyScrollStyles) {
                return;
            }

            document.body.style.position = bodyScrollStyles.position;
            document.body.style.top = bodyScrollStyles.top;
            document.body.style.left = bodyScrollStyles.left;
            document.body.style.right = bodyScrollStyles.right;
            document.body.style.width = bodyScrollStyles.width;
            isPageScrollLocked = false;
            bodyScrollStyles = null;
            window.scrollTo(0, pageScrollPosition);
        };

        const restoreScrollPosition = function () {
            unlockPageScroll();

            window.requestAnimationFrame(function () {
                window.requestAnimationFrame(function () {
                    window.scrollTo(0, pageScrollPosition);

                    if (dialogScroller) {
                        dialogScroller.scrollTop = dialogScrollPosition;
                    }
                });
            });
        };

        const openSnapPayment = function () {
            if (!paymentData) {
                return;
            }

            if (!paymentData.snap_token || !window.snap || typeof window.snap.pay !== 'function') {
                showPaymentMessage('Popup Midtrans gagal dimuat. Kamu akan diarahkan ke halaman pembayaran.', true);

                if (paymentData.redirect_url) {
                    window.setTimeout(function () {
                        window.location.href = paymentData.redirect_url;
                    }, 1200);
                } else {
                    window.location.href = paymentData.invoice_url;
                }

                return;
            }

            clearPaymentMessage();
            rememberScrollPosition();
            lockPageScroll();
            trainerDialog?.close();
            window.snap.pay(paymentData.snap_token, {
                onSuccess: function () {
                    unlockPageScroll();
                    window.location.href = paymentResultUrl(finishUrl, paymentData.order_id);
                },
                onPending: function () {
                    unlockPageScroll();
                    window.location.href = paymentResultUrl(pendingUrl, paymentData.order_id);
                },
                onError: function () {
                    unlockPageScroll();
                    window.location.href = paymentResultUrl(errorUrl, paymentData.order_id);
                },
                onClose: function () {
                    unlockPageScroll();

                    if (trainerDialog && !trainerDialog.open) {
                        trainerDialog.showModal();
                    }

                    restoreScrollPosition();
                    setSubmitting(false);
                    cancelButton.classList.remove('hidden');
                    clearPaymentMessage();
                    showNotification('Pembayaran dijeda', 'Lanjutkan pembayaran atau batalkan booking untuk memilih jadwal lain.', 'info', trainerDialog);
                },
            });
        };

        if (dateRail) {
            const updateCalendarArrows = function () {
                previousDates.disabled = dateRail.scrollLeft <= 1;
                nextDates.disabled = dateRail.scrollLeft + dateRail.clientWidth >= dateRail.scrollWidth - 1;
            };
            const scrollDates = function (direction) {
                dateRail.scrollBy({ left: direction * Math.max(160, dateRail.clientWidth - 80), behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth' });
            };
            previousDates.addEventListener('click', function () { scrollDates(-1); });
            nextDates.addEventListener('click', function () { scrollDates(1); });
            dateRail.addEventListener('scroll', updateCalendarArrows, { passive: true });
            new ResizeObserver(updateCalendarArrows).observe(dateRail);
        }

        const updateDateColors = function () {
            dateButtons.forEach(function (button) {
                if (button.disabled) {
                    return;
                }

                const panel = Array.from(datePanels).find(function (item) {
                    return item.dataset.trainerDatePanel === button.dataset.trainerDateButton;
                });
                const hasSelectedSession = Boolean(panel?.querySelector('input[name="slot_ids[]"]:checked'));
                const isHighlighted = button.disabled || hasSelectedSession || button.getAttribute('aria-pressed') === 'true';

                ['border-[#171717]', 'bg-[#171717]', 'text-white'].forEach(function (className) {
                    button.classList.toggle(className, isHighlighted);
                });
                ['border-black/10', 'bg-white', 'text-[#737373]'].forEach(function (className) {
                    button.classList.toggle(className, !isHighlighted);
                });
            });
        };

        dateButtons.forEach(function (dateButton) {
            dateButton.addEventListener('click', function () {
                dateButtons.forEach(function (button) {
                    if (button.disabled) { return; }
                    button.setAttribute('aria-pressed', 'false');
                });

                dateButton.setAttribute('aria-pressed', 'true');
                updateDateColors();

                datePanels.forEach(function (panel) {
                    panel.hidden = panel.dataset.trainerDatePanel !== dateButton.dataset.trainerDateButton;
                });
            });
        });

        form.addEventListener('change', function () {
            if (paymentData) {
                return;
            }

            const selected = form.querySelectorAll('input[name="slot_ids[]"]:checked').length;
            count.textContent = selected;
            total.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(selected * price);
            submitButton.disabled = selected === 0;
            updateDateColors();
        });

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            if (isSubmitting) {
                return;
            }

            if (paymentData) {
                openSnapPayment();
                return;
            }

            clearPaymentMessage();
            setSubmitting(true);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new FormData(form),
                });
                const data = await response.json();

                if (!response.ok) {
                    const validationMessage = data.errors ? Object.values(data.errors).flat()[0] : null;
                    showPaymentMessage(validationMessage || data.message || 'Pembayaran belum dapat dimulai. Silakan coba lagi.', true);

                    if (data.invoice_url) {
                        submitButton.disabled = true;
                        submitLabel.textContent = 'Buka riwayat pembayaran';
                        window.setTimeout(function () {
                            window.location.href = data.invoice_url;
                        }, 1800);
                    } else {
                        setSubmitting(false);
                    }

                    return;
                }

                paymentData = data;
                restoredPendingPayment = false;
                form.querySelectorAll('input[name="slot_ids[]"]').forEach(function (input) {
                    input.disabled = true;
                });
                setSubmitting(false);
                openSnapPayment();
            } catch (error) {
                showPaymentMessage('Terjadi kesalahan jaringan. Periksa koneksi lalu coba lagi.', true);
                setSubmitting(false);
            }
        });

        cancelButton?.addEventListener('click', async function () {
            if (!paymentData?.cancel_url || cancelButton.disabled) {
                return;
            }

            const shouldCancel = await window.gymConfirm({
                title: 'Batalkan booking personal trainer?',
                message: 'Pembayaran akan dibatalkan dan jadwal yang ditahan akan tersedia kembali.',
                confirmButtonText: 'Ya, batalkan',
                cancelButtonText: 'Lanjutkan pembayaran',
                target: trainerDialog,
            });

            if (!shouldCancel) {
                return;
            }

            cancelButton.disabled = true;
            submitButton.disabled = true;
            cancelLabel.textContent = 'Membatalkan...';
            clearPaymentMessage();

            try {
                const response = await fetch(paymentData.cancel_url, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    },
                });
                const data = await response.json();

                if (!response.ok) {
                    showNotification('Booking belum dibatalkan', data.message || 'Booking belum dapat dibatalkan. Silakan coba lagi.', 'error', trainerDialog);
                    cancelButton.disabled = false;
                    submitButton.disabled = false;
                    cancelLabel.textContent = 'Batalkan booking';
                    return;
                }

                const shouldReloadAvailableSlots = restoredPendingPayment;
                paymentData = null;
                restoredPendingPayment = false;
                form.querySelectorAll('input[name="slot_ids[]"]').forEach(function (input) {
                    input.disabled = false;
                    input.checked = false;
                });
                cancelButton.disabled = false;
                cancelButton.classList.add('hidden');
                cancelLabel.textContent = 'Batalkan booking';
                count.textContent = '0';
                total.textContent = 'Rp0';
                setSubmitting(false);
                updateDateColors();
                clearPaymentMessage();
                showNotification('Booking dibatalkan', data.message, 'success', trainerDialog);

                if (shouldReloadAvailableSlots) {
                    window.setTimeout(function () {
                        window.location.reload();
                    }, 900);
                }
            } catch (error) {
                showNotification('Booking belum dibatalkan', 'Terjadi kesalahan jaringan saat membatalkan booking.', 'error', trainerDialog);
                cancelButton.disabled = false;
                submitButton.disabled = false;
                cancelLabel.textContent = 'Batalkan booking';
            }
        });
        setSubmitting(false);
        updateDateColors();
    });
});
</script>
@endpush
