@extends('layouts.app')

@section('title', $profile->name . ' - Gym & Fitness Tanjungpinang')
@section('description', $profile->description)
@section('main-class', 'w-full flex-1')

@section('content')
<div class="relative">
<section class="hero-shell sticky top-0 isolate flex min-h-svh items-end overflow-hidden bg-[#111111] text-white" aria-labelledby="hero-title" data-color-hero data-navbar-tone="dark">
    <div class="hero-image-layer hero-image-gray absolute inset-0 -z-30 bg-cover bg-[62%_center] sm:bg-center" style="background-image: url('{{ asset('images/gympinang-hero-gray.png') }}')" aria-hidden="true"></div>
    <div class="hero-image-layer hero-image-color absolute inset-0 -z-20 bg-cover bg-[62%_center] sm:bg-center" style="background-image: url('{{ asset('images/gympinang-hero-color.png') }}')" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-10 bg-[linear-gradient(90deg,rgba(8,8,8,.92)_0%,rgba(20,20,20,.7)_42%,rgba(25,25,25,.12)_78%),linear-gradient(0deg,rgba(5,5,5,.78)_0%,transparent_48%)]" aria-hidden="true"></div>
    <div class="pointer-events-none absolute inset-x-0 bottom-0 -z-[5] h-[38%] bg-black/[0.01] backdrop-blur-[5px] [-webkit-mask-image:linear-gradient(to_bottom,transparent,black_45%)] [mask-image:linear-gradient(to_bottom,transparent,black_45%)]" aria-hidden="true"></div>

    <div class="relative z-10 mx-auto grid w-full max-w-[90rem] gap-10 px-5 pb-10 pt-36 sm:px-8 sm:pb-14 lg:grid-cols-[1fr_auto] lg:items-end lg:px-12">
        <div class="max-w-4xl">
            <div class="reveal is-visible flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.24em] text-white/75 sm:text-sm">
                Gym lokal Tanjungpinang
            </div>
            <h1 id="hero-title" class="reveal is-visible mt-5 text-[clamp(4.25rem,9vw,8.5rem)] font-bold leading-[0.82] tracking-[-0.075em]" style="--reveal-delay: 100ms">
                Bukan cuma.<br>
                <span class="hero-outline">Latihan.</span>
            </h1>
            <p class="reveal is-visible mt-7 max-w-lg text-sm leading-7 text-white/72 sm:text-base" style="--reveal-delay: 200ms">
                Ruang untuk membangun tubuh, disiplin, dan versi terbaik dirimu. Mulai hari ini satu repetisi, satu progres.
            </p>
        </div>

        <div class="reveal is-visible flex flex-col items-start gap-5 lg:items-end" style="--reveal-delay: 300ms">
            <a href="{{ route('membership.index') }}" class="group inline-flex items-center gap-5 rounded-full bg-white py-2 pl-6 pr-2 text-sm font-semibold text-[#171717] transition hover:-translate-y-1 hover:bg-[#d4d4d4] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">
                Mulai sekarang
                <span class="grid size-11 place-items-center rounded-full bg-white text-[#171717] transition group-hover:rotate-45" aria-hidden="true">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </span>
            </a>
            <a href="#tentang" class="inline-flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.16em] text-white/70 transition hover:text-white">
                Jelajahi GymPinang
                <span class="grid size-9 place-items-center rounded-full border border-white/35" aria-hidden="true">↓</span>
            </a>
        </div>
    </div>
</section>

<section class="relative z-20 -mb-16 scroll-mt-[-6rem] snap-start snap-always bg-[#f5f5f5] lg:-mb-24" aria-labelledby="bmi-title" data-navbar-tone="dark">
    <div class="relative overflow-hidden bg-[#111111] px-5 pb-12 pt-14 text-white sm:px-8 sm:pb-14 lg:px-12 lg:pb-16 lg:pt-16">
        <div class="reveal relative z-10 mx-auto max-w-5xl text-center">
            <h2 id="bmi-title" class="text-[clamp(2rem,4vw,4rem)] font-bold leading-none tracking-[-0.055em]">Calculate Your BMI Now</h2>

            <form class="mx-auto mt-9 grid max-w-5xl gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_auto]" data-bmi-form novalidate>
                <label>
                    <span class="sr-only">Berat badan dalam kilogram</span>
                    <input type="number" name="weight" min="20" max="400" step="0.1" inputmode="decimal" placeholder="Berat badan (kg)" required class="h-12 w-full rounded-lg border border-white/15 bg-white/[0.05] px-5 text-sm text-white outline-none transition placeholder:text-white/35 focus:border-white/60 focus:bg-white/10">
                </label>
                <label>
                    <span class="sr-only">Tinggi badan dalam sentimeter</span>
                    <input type="number" name="height" min="100" max="250" step="0.1" inputmode="decimal" placeholder="Tinggi badan (cm)" required class="h-12 w-full rounded-lg border border-white/15 bg-white/[0.05] px-5 text-sm text-white outline-none transition placeholder:text-white/35 focus:border-white/60 focus:bg-white/10">
                </label>
                <label>
                    <span class="sr-only">Umur dalam tahun</span>
                    <input type="number" name="age" min="19" max="100" step="1" inputmode="numeric" placeholder="Umur (tahun)" required class="h-12 w-full rounded-lg border border-white/15 bg-white/[0.05] px-5 text-sm text-white outline-none transition placeholder:text-white/35 focus:border-white/60 focus:bg-white/10">
                </label>
                <button type="submit" class="h-12 rounded-lg bg-white px-8 text-xs font-semibold uppercase tracking-[0.12em] text-[#171717] transition hover:bg-[#d4d4d4] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white sm:col-span-2 lg:col-span-1">Calculate now</button>
            </form>

            <div class="mx-auto mt-6 hidden max-w-4xl rounded-[1.75rem] border border-white/15 bg-white/[0.07] px-6 py-7 text-left shadow-[0_24px_80px_rgba(0,0,0,.2)] sm:px-8 sm:py-8" role="status" aria-live="polite" data-bmi-result>
                <h3 class="text-2xl font-semibold tracking-[-0.04em] sm:text-3xl">
                    BMI Kamu: <span data-bmi-value></span> <span class="text-white/35">—</span> <span data-bmi-category></span>
                </h3>
                <p class="mt-4 text-sm text-white/60">
                    Berat badan: <span class="font-semibold text-white/85" data-bmi-weight></span> · Tinggi badan: <span class="font-semibold text-white/85" data-bmi-height></span>
                </p>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-white/75" data-bmi-message></p>

                <div class="mt-7 border-t border-white/10 pt-6" data-bmi-goals>
                    <h4 class="text-lg font-semibold tracking-[-0.025em]" data-bmi-goals-title></h4>
                    <p class="mt-1 text-sm text-white/50" data-bmi-goals-description></p>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <button type="button" class="rounded-full border border-white/20 px-4 py-2.5 text-xs font-semibold text-white/75 transition hover:border-white/50 hover:text-white" aria-pressed="false" data-bmi-goal></button>
                        <button type="button" class="rounded-full border border-white/20 px-4 py-2.5 text-xs font-semibold text-white/75 transition hover:border-white/50 hover:text-white" aria-pressed="false" data-bmi-goal></button>
                        <button type="button" class="rounded-full border border-white/20 px-4 py-2.5 text-xs font-semibold text-white/75 transition hover:border-white/50 hover:text-white" aria-pressed="false" data-bmi-goal></button>
                    </div>

                    <div class="mt-4 hidden flex-col items-start justify-between gap-4 rounded-2xl bg-white/[0.06] p-4 sm:flex-row sm:items-center" data-bmi-goal-result>
                        <div>
                            <p class="text-sm font-semibold" data-bmi-goal-title></p>
                            <p class="mt-1 text-xs leading-5 text-white/55" data-bmi-goal-message></p>
                        </div>
                        <a href="{{ route('classes.index') }}" class="shrink-0 text-xs font-semibold text-white/75 transition hover:text-white">Lihat jadwal kelas →</a>
                    </div>
                </div>
            </div>

            <p class="mx-auto mt-5 max-w-2xl text-[.65rem] leading-5 text-white/35">Gunakan hasil BMI sebagai gambaran awal untuk memilih fokus latihan. Mulai sesuai kemampuanmu, lalu tingkatkan durasi dan intensitas secara bertahap.</p>
        </div>
    </div>

    <svg class="pointer-events-none block h-24 w-full bg-[#f5f5f5] sm:h-32 lg:h-40" viewBox="0 0 1440 180" preserveAspectRatio="none" aria-hidden="true">
        <path d="M0 0H1440V59C1068 12 842 44 610 101C407 151 215 139 0 78Z" fill="#111111" />
    </svg>
</section>

@php
    $openingPeriods = preg_split('/\s*[-–—]\s*/', $profile->opening_hours, 2);
    [$openingHour, $openingMinute] = array_pad(explode(':', trim($openingPeriods[0] ?? '06:00'), 2), 2, '00');
    [$closingHour, $closingMinute] = array_pad(explode(':', trim($openingPeriods[1] ?? '22:00'), 2), 2, '00');
@endphp

<section id="tentang" class="relative z-10 isolate overflow-hidden bg-[#f5f5f5] px-5 py-20 text-[#171717] sm:px-8 lg:px-12 lg:py-28" aria-labelledby="about-title" data-navbar-tone="light" data-about-reveal>
    <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <div class="about-background-grid absolute inset-0"></div>
        <div class="about-orbit absolute -left-56 top-[22rem] size-[34rem] rounded-full border border-[#171717]/[0.06]">
            <span class="absolute inset-14 rounded-full border border-[#171717]/[0.05]"></span>
            <span class="absolute inset-28 rounded-full border border-[#171717]/[0.05]"></span>
            <span class="absolute left-1/2 top-0 size-2 -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#171717]/15"></span>
        </div>
        <div class="about-orbit about-orbit-reverse absolute -right-64 bottom-24 size-[42rem] rounded-full border border-[#171717]/[0.055]">
            <span class="absolute inset-20 rounded-full border border-[#171717]/[0.05]"></span>
            <span class="absolute inset-40 rounded-full border border-[#171717]/[0.05]"></span>
            <span class="absolute left-0 top-1/2 size-2 -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#171717]/15"></span>
        </div>
    </div>
    <div class="relative mx-auto flex max-w-[80rem] flex-col gap-12 lg:gap-16">
        <div class="reveal flex items-center justify-between gap-5 text-[.6rem] font-semibold uppercase tracking-[0.22em] text-[#737373]">
            <p>Tentang {{ $profile->name }}</p>
            <p>Tanjungpinang, Indonesia</p>
        </div>

        <div class="grid gap-9 lg:grid-cols-[1.1fr_.9fr] lg:items-end lg:gap-20">
            <h2 id="about-title" class="about-title-reveal reveal relative text-[clamp(3.5rem,7.5vw,7rem)] font-bold leading-[0.82] tracking-[-0.065em]" data-about-title>
                <span class="about-title-outline flex flex-col gap-[0.3em]">
                    <span>Tempatmu</span>
                    <span>menjadi</span>
                    <span>lebih kuat.</span>
                </span>
                <span class="about-title-half absolute inset-0 flex flex-col gap-[0.3em]" aria-hidden="true">
                    <span>Tempatmu</span>
                    <span>menjadi</span>
                    <span class="text-[#85857f]">lebih kuat.</span>
                </span>
                <span class="about-title-fill absolute inset-0 flex flex-col gap-[0.3em]" aria-hidden="true">
                    <span>Tempatmu</span>
                    <span>menjadi</span>
                    <span class="text-[#85857f]">lebih kuat.</span>
                </span>
            </h2>
            <div class="reveal flex max-w-lg flex-col gap-6 lg:pb-2" style="--reveal-delay: 120ms">
                <p class="text-lg leading-8 text-[#525252]">Dari latihan pertamamu sampai rutinitas yang kamu banggakan. {{ $profile->name }} adalah ruang untuk bergerak, membangun kekuatan, dan terus berkembang.</p>
                <p class="text-sm leading-7 text-[#737373]">Temukan peralatan yang kamu butuhkan dan pilihan membership yang sesuai. Mulai dari kemampuanmu hari ini, lalu jalani progresmu satu sesi demi satu sesi.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('membership.index') }}" class="group inline-flex w-fit items-center gap-6 rounded-full bg-[#171717] px-6 py-4 text-sm font-semibold text-white transition hover:bg-[#454545] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717]">Mulai latihanmu <span class="transition group-hover:translate-x-1" aria-hidden="true">↗</span></a>
                    <a href="#ulasan" class="group inline-flex w-fit items-center gap-6 rounded-full border border-[#171717]/25 px-6 py-4 text-sm font-semibold text-[#171717] transition hover:border-[#171717] hover:bg-[#171717] hover:text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717]">Review tempat <span class="transition group-hover:translate-x-1" aria-hidden="true">↓</span></a>
                </div>
            </div>
        </div>

        <figure class="about-gallery grid h-[36rem] grid-cols-2 grid-rows-3 gap-2 sm:h-[34rem] sm:grid-cols-12 sm:grid-rows-2 sm:gap-3" data-gallery-assemble>
            <div class="about-gallery-tile group relative col-span-2 overflow-hidden rounded-[1.25rem] sm:col-span-5 sm:row-span-2" style="--tile-index: 0">
                <img src="{{ asset('images/gym-equipment-editorial.webp') }}" alt="Area dumbbell dan bangku latihan di gym" class="h-full w-full object-cover object-left transition duration-700 group-hover:scale-105" loading="lazy">
                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent px-5 pb-5 pt-16 text-white sm:px-7 sm:pb-7">
                    <p class="text-[.6rem] font-semibold uppercase tracking-[.2em] text-white/65">Ruang untuk progresmu</p>
                    <p class="mt-2 text-xl font-medium tracking-[-.04em] sm:text-3xl">Latihanmu.<br>Ritmemu.</p>
                </div>
            </div>
            <div class="about-gallery-tile group overflow-hidden rounded-[1.25rem] sm:col-span-4" style="--tile-index: 1">
                <img src="{{ asset('images/gym-cable-row-editorial.webp') }}" alt="Member berlatih dengan mesin kabel" class="h-full w-full object-cover object-[38%_center] transition duration-700 group-hover:scale-105" loading="lazy">
            </div>
            <div class="about-gallery-tile group overflow-hidden rounded-[1.25rem] sm:col-span-3" style="--tile-index: 2">
                <img src="{{ asset('images/gym-kettlebell-editorial.webp') }}" alt="Latihan kettlebell di area gym" class="h-full w-full object-cover object-center transition duration-700 group-hover:scale-105" loading="lazy">
            </div>
            <div class="about-gallery-tile group overflow-hidden rounded-[1.25rem] sm:col-span-3" style="--tile-index: 3">
                <img src="{{ asset('images/gympinang-hero-color.png') }}" alt="Suasana latihan di CukipGym" class="h-full w-full object-cover object-center transition duration-700 group-hover:scale-105" loading="lazy">
            </div>
            <div class="about-gallery-tile group overflow-hidden rounded-[1.25rem] sm:col-span-4" style="--tile-index: 4">
                <img src="{{ asset('images/gym-equipment-editorial.webp') }}" alt="Detail peralatan beban CukipGym" class="h-full w-full object-cover object-right transition duration-700 group-hover:scale-105" loading="lazy">
            </div>
            <figcaption class="sr-only">Galeri area latihan dan fasilitas {{ $profile->name }}</figcaption>
        </figure>

        <div class="grid gap-10 lg:grid-cols-[.75fr_1.25fr] lg:gap-20">
            <div class="flex flex-col gap-4">
                <p class="text-[.6rem] font-semibold uppercase tracking-[.22em] text-[#737373]">Satu tempat, banyak kemungkinan</p>
                <h3 class="max-w-sm text-3xl font-semibold leading-tight tracking-[-.045em] sm:text-4xl">Bangun rutinitas<br>yang cocok untukmu.</h3>
            </div>
            <div class="grid gap-8 sm:grid-cols-2 sm:gap-10">
                <a href="{{ route('membership.index') }}" class="group flex flex-col gap-4 focus-visible:outline-2 focus-visible:outline-offset-8 focus-visible:outline-[#171717]">
                    <div class="flex items-center justify-between">
                        <span class="text-5xl font-medium tracking-[-.065em]" data-counter="{{ $plans->count() }}" data-counter-pad="2">00</span>
                        <span class="grid size-10 place-items-center rounded-full bg-[#dededb] text-xl transition group-hover:bg-[#171717] group-hover:text-white" aria-hidden="true">↗</span>
                    </div>
                    <div class="flex flex-col gap-2">
                        <h4 class="text-lg font-semibold">Pilihan membership</h4>
                        <p class="text-sm leading-6 text-[#737373]">Pilih paket dan durasi yang sesuai dengan rencana latihanmu.</p>
                    </div>
                </a>
                <a href="{{ route('facilities.index') }}" class="group flex flex-col gap-4 focus-visible:outline-2 focus-visible:outline-offset-8 focus-visible:outline-[#171717]">
                    <div class="flex items-center justify-between">
                        <span class="text-5xl font-medium tracking-[-.065em]" data-counter="{{ $facilities->count() }}" data-counter-pad="2">00</span>
                        <span class="grid size-10 place-items-center rounded-full bg-[#dededb] text-xl transition group-hover:bg-[#171717] group-hover:text-white" aria-hidden="true">↗</span>
                    </div>
                    <div class="flex flex-col gap-2">
                        <h4 class="text-lg font-semibold">Fasilitas latihan</h4>
                        <p class="text-sm leading-6 text-[#737373]">Kenali area dan peralatan untuk mendukung setiap sesi latihanmu.</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="reveal flex flex-col gap-10 py-6 sm:gap-12 lg:py-10">
            <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
                <div class="flex flex-col gap-4">
                    <p class="text-[.6rem] font-semibold uppercase tracking-[.24em] text-[#737373]">Jam operasional</p>
                    <h3 class="text-[clamp(2rem,4vw,3.5rem)] font-semibold leading-[1.05] tracking-[-.055em]">Pagi atau malam.<br><span class="text-[#85857f]">Tentukan waktumu.</span></h3>
                </div>
                <p class="max-w-60 text-justify text-sm leading-6 text-[#737373]">Sesuaikan waktu kunjungan dengan rutinitasmu. Ini jam buka dan tutup gym kami.</p>
            </div>
            <dl class="operating-hours reveal mx-auto grid w-full max-w-5xl grid-cols-[1fr_auto_1fr] grid-rows-[auto_auto] items-center gap-x-4 gap-y-4 py-4 sm:gap-x-12 sm:py-6">
                <div class="operating-time operating-time-open col-start-1 row-span-2 row-start-1 grid min-w-0 grid-rows-subgrid">
                    <dt class="text-[.6rem] font-semibold uppercase tracking-[.22em] text-[#737373] sm:text-xs">Buka</dt>
                    <dd class="whitespace-nowrap text-[clamp(2.5rem,8vw,8rem)] font-medium leading-none tracking-[-.065em] text-[#171717] tabular-nums">{{ str_pad($openingHour, 2, '0', STR_PAD_LEFT) }}<span class="operating-colon text-[#999993]">:</span>{{ str_pad($openingMinute, 2, '0', STR_PAD_LEFT) }}</dd>
                </div>
                <div class="col-start-2 row-start-2 flex items-center justify-center text-[#85857f]" aria-hidden="true">
                    <svg class="h-5 w-6 sm:h-8 sm:w-14" viewBox="0 0 56 32" fill="none"><path class="operating-arrow-path" d="M1 16H53M40 3L53 16L40 29" stroke="currentColor" stroke-width="1.5"/></svg>
                </div>
                <div class="operating-time operating-time-close col-start-3 row-span-2 row-start-1 grid min-w-0 grid-rows-subgrid text-right">
                    <dt class="text-[.6rem] font-semibold uppercase tracking-[.22em] text-[#737373] sm:text-xs">Tutup</dt>
                    <dd class="whitespace-nowrap text-[clamp(2.5rem,8vw,8rem)] font-medium leading-none tracking-[-.065em] text-[#171717] tabular-nums">{{ str_pad($closingHour, 2, '0', STR_PAD_LEFT) }}<span class="operating-colon text-[#999993]">:</span>{{ str_pad($closingMinute, 2, '0', STR_PAD_LEFT) }}</dd>
                </div>
            </dl>
        </div>
    </div>
</section>
</div>

@if ($instructors->isNotEmpty())
    @php
        $trainerImages = [
            'images/gym-kettlebell-editorial.webp',
            'images/gym-cable-row-editorial.webp',
            'images/gympinang-hero-color.png',
            'images/gym-equipment-editorial.webp',
        ];
    @endphp

    <div class="relative h-12 overflow-hidden bg-[#f5f5f5] sm:h-20 lg:h-28" aria-hidden="true">
        <svg class="absolute inset-0 h-full w-full text-[#171717]" viewBox="0 0 1440 112" preserveAspectRatio="none" fill="currentColor">
            <path d="M0 0C360 144 1080 144 1440 0V112H0Z"/>
        </svg>
    </div>
    <section class="trainer-section relative isolate overflow-hidden bg-[#171717] px-5 pb-12 pt-8 text-white sm:px-8 sm:pb-16 sm:pt-10 lg:px-12 lg:pb-20 lg:pt-12" aria-labelledby="trainers-title" data-navbar-tone="dark">
        <div class="mx-auto max-w-[80rem]">
            <div class="mx-auto flex max-w-2xl flex-col items-center gap-4 text-center">
                <p class="trainer-kicker scroll-reveal text-[.65rem] font-semibold uppercase tracking-[.24em] text-white/45">Coach CukipGym</p>
                <h2 id="trainers-title" class="text-[clamp(2.15rem,4.4vw,4.1rem)] font-bold leading-[.95] tracking-[-.065em]">Latihanmu.<br><span class="text-white/35">Dipandu tim kami.</span></h2>
                <p class="max-w-xl text-sm leading-6 text-white/55">Kenali instruktur yang mendampingi kelas dan membantu setiap gerakan terasa lebih terarah.</p>
            </div>

            <div class="trainer-carousel reveal relative mx-auto mt-6 max-w-4xl" data-trainer-carousel role="region" aria-roledescription="carousel" aria-label="Instruktur CukipGym" tabindex="0">
                <p class="pointer-events-none absolute left-1/2 top-1/2 z-0 -translate-x-1/2 -translate-y-1/2 text-center text-[clamp(3.75rem,11vw,8.75rem)] font-bold leading-[.68] tracking-[-.09em] text-white/[0.055]" aria-hidden="true">Tim<br><span class="whitespace-nowrap">CukipGym.</span></p>
                <div class="relative h-full" aria-live="polite">
                    @foreach ($instructors as $instructor)
                        <article class="trainer-card overflow-hidden rounded-[1.75rem] shadow-[0_30px_80px_rgba(40,45,55,.18)]" data-trainer-card>
                            <div class="trainer-card-inner">
                                <div class="trainer-card-face trainer-card-front">
                                    <img src="{{ asset($trainerImages[$loop->index % count($trainerImages)]) }}" alt="Visual kelas bersama {{ $instructor->instructor }}" class="h-full w-full rounded-[1.75rem] object-cover" loading="lazy">
                                    <div class="absolute inset-x-0 bottom-0 rounded-b-[1.75rem] bg-gradient-to-t from-[#111318]/95 via-[#111318]/55 to-transparent px-6 pb-7 pt-24 text-white sm:px-8 sm:pb-8">
                                        <p class="text-2xl font-semibold tracking-[-.04em] sm:text-3xl">{{ $instructor->instructor }}</p>
                                        <p class="mt-2 text-xs uppercase tracking-[.16em] text-white/65">Instruktur {{ $instructor->name }}</p>
                                    </div>
                                    <span class="pointer-events-none absolute right-5 top-5 grid size-12 place-items-center rounded-full bg-white text-xl text-[#171717]" aria-hidden="true">+</span>
                                </div>
                                <div class="trainer-card-face trainer-card-back flex flex-col rounded-[1.75rem] bg-[#f5f5f5] p-5 text-[#171717] sm:p-7">
                                    <div class="flex items-center justify-between">
                                        <span class="text-lg font-bold tracking-[-.06em]">CukipGym</span>
                                        <span class="pointer-events-none grid size-10 place-items-center rounded-full bg-[#e8eaf0] text-2xl text-[#171717]" aria-hidden="true">×</span>
                                    </div>
                                    <div class="mt-auto flex flex-col gap-2 pb-2">
                                        <h3 class="text-[clamp(1.8rem,3vw,2.5rem)] font-semibold leading-tight tracking-[-.05em]">{{ $instructor->instructor }}</h3>
                                        <p class="text-sm font-medium">{{ $instructor->name }}</p>
                                        @if ($instructor->description)
                                            <p class="mt-2 text-sm leading-6 text-[#646873]">{{ $instructor->description }}</p>
                                        @endif
                                        <a href="{{ route('classes.index') }}" class="mt-3 inline-flex w-fit items-center gap-3 text-sm font-semibold text-[#171717] underline decoration-[#171717]/25 underline-offset-4 transition hover:decoration-[#171717]">Lihat jadwal kelas <span aria-hidden="true">↗</span></a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <button type="button" class="trainer-nav trainer-nav-prev absolute left-0 top-1/2 z-20 grid size-12 -translate-y-1/2 place-items-center rounded-full border border-white/15 bg-white/10 text-xl text-white backdrop-blur transition hover:bg-white hover:text-[#171717] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white sm:left-[8%] lg:left-[14%]" data-trainer-prev aria-label="Instruktur sebelumnya">‹</button>
                <button type="button" class="trainer-nav trainer-nav-next absolute right-0 top-1/2 z-20 grid size-12 -translate-y-1/2 place-items-center rounded-full border border-white/15 bg-white/10 text-xl text-white backdrop-blur transition hover:bg-white hover:text-[#171717] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white sm:right-[8%] lg:right-[14%]" data-trainer-next aria-label="Instruktur berikutnya">›</button>

                <div class="absolute inset-x-0 bottom-0 z-20 flex justify-center gap-2" aria-hidden="true">
                    @foreach ($instructors as $instructor)
                        <span class="trainer-dot h-1.5 w-1.5 rounded-full bg-white/20 transition-all duration-300" data-trainer-dot></span>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif

<section class="bg-[#171717] px-5 py-16 text-white sm:px-8 sm:py-20 lg:px-12 lg:py-24" aria-label="Statistik CukipGym" data-navbar-tone="dark">
    <div class="mx-auto grid max-w-[90rem] gap-12 text-center sm:grid-cols-3 sm:gap-6">
        <div class="reveal flex flex-col items-center gap-4">
            <p class="text-[clamp(4rem,8vw,7.5rem)] font-semibold leading-none tracking-[-.075em] text-white">
                <span data-counter="{{ $activeMembershipCount }}">0</span>@if ($activeMembershipCount > 0)<span aria-hidden="true">+</span>@endif
            </p>
            <p class="text-[.65rem] font-semibold uppercase tracking-[.18em] text-white/45">Member aktif</p>
        </div>
        <div class="reveal flex flex-col items-center gap-4" style="--reveal-delay: 100ms">
            <p class="text-[clamp(4rem,8vw,7.5rem)] font-semibold leading-none tracking-[-.075em] text-[#d4d4d4]">
                <span data-counter="{{ $activeClassCount }}">0</span>@if ($activeClassCount > 0)<span aria-hidden="true">+</span>@endif
            </p>
            <p class="text-[.65rem] font-semibold uppercase tracking-[.18em] text-white/45">Kelas aktif</p>
        </div>
        <div class="reveal flex flex-col items-center gap-4" style="--reveal-delay: 200ms">
            <p class="text-[clamp(4rem,8vw,7.5rem)] font-semibold leading-none tracking-[-.075em] text-[#85857f]">
                <span data-counter="{{ $facilityCount }}">0</span>@if ($facilityCount > 0)<span aria-hidden="true">+</span>@endif
            </p>
            <p class="text-[.65rem] font-semibold uppercase tracking-[.18em] text-white/45">Fasilitas tersedia</p>
        </div>
    </div>
</section>

<div class="relative h-14 overflow-hidden bg-white sm:h-20 lg:h-24" aria-hidden="true">
    <svg class="absolute inset-0 h-full w-full" viewBox="0 0 1440 120" preserveAspectRatio="none">
        <path d="M0 0H1440V20C1120 0 1040 90 720 76S240 8 0 64Z" fill="#171717"/>
    </svg>
</div>

@php
    $memberStoryVisuals = [
        [
            'before' => 'images/member-transform-3-before.webp',
            'after' => 'images/member-transform-3-after.webp',
        ],
        [
            'before' => 'images/member-transform-2-before.webp',
            'after' => 'images/member-transform-2-after.webp',
        ],
        [
            'before' => 'images/member-transform-1-before.webp',
            'after' => 'images/member-transform-1-after.webp',
        ],
    ];
@endphp

<section id="ulasan" class="relative isolate scroll-mt-24 overflow-hidden bg-white px-5 py-20 text-[#171717] sm:px-8 lg:px-12 lg:py-24" aria-labelledby="reviews-title" data-member-stories data-navbar-tone="light">
    <div class="pointer-events-none absolute inset-0 -z-10 [background-image:radial-gradient(circle,rgba(23,23,23,.13)_1px,transparent_1px)] [background-size:24px_24px] [mask-image:linear-gradient(110deg,black,transparent_55%,black)]" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -right-24 top-8 -z-10 size-[40rem] rounded-full border border-[#171717]/10" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -bottom-5 left-1/2 -z-10 -translate-x-1/2 whitespace-nowrap text-[clamp(5rem,17vw,18rem)] font-bold leading-none tracking-[-.065em] text-transparent [-webkit-text-stroke:1px_rgba(23,23,23,.1)]" aria-hidden="true">PROGRES.</div>
    <div class="relative mx-auto grid max-w-[68rem] gap-10 lg:grid-cols-[1fr_1fr] lg:items-center lg:gap-20">
        <div class="relative z-10 max-w-xl lg:pr-4">
            <p class="section-kicker reveal">Cerita member</p>
            <h2 id="reviews-title" class="reveal mt-7 text-[clamp(3rem,3.8vw,4rem)] font-bold leading-[0.94] tracking-[-0.055em]" style="--reveal-delay: 80ms">
                Orang nyata.<br><span class="text-[#8c8c87]">Progres nyata.</span>
            </h2>
            <p class="reveal mt-8 max-w-md text-justify text-base leading-8 text-[#656561]" style="--reveal-delay: 140ms">
                Dari membangun kekuatan dan menjaga berat badan hingga meningkatkan mobilitas serta kebugaran secara menyeluruh, pendampingan yang tepat membantu member menciptakan perubahan nyata yang dapat dipertahankan dalam jangka panjang.
            </p>
        </div>

        <div class="reveal relative w-full max-w-[27rem] justify-self-center lg:justify-self-end" style="--reveal-delay: 140ms" data-member-story-carousel tabindex="0" aria-label="Galeri ilustrasi transformasi">
            <div class="member-story-stage relative aspect-[9/11] overflow-hidden rounded-[1.5rem] bg-[#e7e7e4] ring-1 ring-black/5">
                @foreach ($memberStoryVisuals as $storyIndex => $storyVisual)
                    <figure class="member-story-slide {{ $loop->first ? 'is-active' : '' }} absolute inset-0 overflow-hidden" data-member-story-slide aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
                        <img src="{{ asset($storyVisual['before']) }}" alt="Kondisi sebelum menjalani latihan" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                        <div class="member-transform-after absolute inset-0">
                            <img src="{{ asset($storyVisual['after']) }}" alt="Kondisi setelah menjalani latihan" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                        </div>
                        <div class="member-transform-divider" aria-hidden="true"><span>‹ ›</span></div>
                        <input class="member-transform-range" type="range" min="0" max="100" value="50" aria-label="Geser untuk membandingkan sebelum dan sesudah" data-member-transform-range>
                    </figure>
                @endforeach

                <button type="button" class="absolute left-4 top-1/2 z-20 grid size-11 -translate-y-1/2 place-items-center rounded-full bg-black/35 text-white backdrop-blur-sm transition hover:bg-black/55 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white" data-member-story-prev aria-label="Cerita sebelumnya">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m14 6-6 6 6 6"/></svg>
                </button>
                <div class="absolute bottom-4 left-1/2 z-20 flex -translate-x-1/2 items-center gap-2 rounded-full bg-black/35 px-4 py-3 backdrop-blur-sm" aria-label="Pilih cerita member">
                    @foreach ($memberStoryVisuals as $storyVisual)
                        <button type="button" class="member-story-dot h-1.5 w-1.5 rounded-full bg-white/45 transition-all duration-300 {{ $loop->first ? 'is-active' : '' }}" data-member-story-dot aria-label="Tampilkan cerita {{ $loop->iteration }}" aria-current="{{ $loop->first ? 'true' : 'false' }}"></button>
                    @endforeach
                </div>
                <button type="button" class="absolute right-4 top-1/2 z-20 grid size-11 -translate-y-1/2 place-items-center rounded-full bg-black/35 text-white backdrop-blur-sm transition hover:bg-black/55 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white" data-member-story-next aria-label="Cerita berikutnya">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m10 6 6 6-6 6"/></svg>
                </button>
            </div>
        </div>
    </div>
</section>

<div class="relative h-12 overflow-hidden bg-white sm:h-20 lg:h-24" aria-hidden="true">
    <svg class="absolute inset-0 h-full w-full text-[#171717]" viewBox="0 0 1440 120" preserveAspectRatio="none" fill="currentColor">
        <path d="M0 68C260 126 450 12 740 38S1190 122 1440 30V120H0Z"/>
    </svg>
</div>

<section id="fasilitas-home" class="overflow-hidden bg-[#171717] px-5 py-20 text-white sm:px-8 lg:px-12 lg:py-24" aria-labelledby="facilities-title" data-navbar-tone="dark">
    @php
        $strengthFacilities = $facilities->filter(fn ($facility) => preg_match('/barbell|barbel|dumbbell|bench|rack|smith|cable/i', $facility->name));
        $cardioFacilities = $facilities->filter(fn ($facility) => preg_match('/treadmill|sepeda|bike|elliptical|cardio/i', $facility->name));
        $recoveryFacilities = $facilities->filter(fn ($facility) => preg_match('/stretch|recovery|shower|sauna|pijat/i', $facility->name));
        $comfortFacilities = $facilities->diff($strengthFacilities)->diff($cardioFacilities)->diff($recoveryFacilities);
        $facilityGroups = [
            ['title' => 'Strength Training Area', 'description' => 'Bangun kekuatan dan massa otot melalui latihan beban yang bertahap. Gunakan barbell, dumbbell, dan bench press untuk menyusun variasi gerakan sesuai kemampuan serta target latihanmu. Mulai dengan beban yang nyaman, utamakan teknik gerakan, lalu tingkatkan tantangan seiring progresmu.', 'image' => 'images/facility-strength-3d-v2.png', 'items' => $strengthFacilities],
            ['title' => 'Cardio & Endurance', 'description' => 'Bangun stamina dan temukan ritme gerak yang nyaman untuk tubuhmu. Latihan kardio dapat menjadi bagian dari pemanasan maupun sesi utama, disesuaikan dengan intensitas dan durasi yang kamu pilih. Tanyakan pilihan latihan yang tersedia agar kamu bisa menyusun rutinitas yang seimbang dengan latihan kekuatan.', 'image' => 'images/facility-cardio-nike-3d.png', 'items' => $cardioFacilities],
            ['title' => 'Recovery & Comfort', 'description' => 'Beri tubuh waktu untuk beristirahat setelah menyelesaikan sesi latihan. Luangkan beberapa menit untuk pendinginan, gerakan mobilitas ringan, dan mencukupi kebutuhan minum. Sesuaikan aktivitas dengan kenyamanan tubuhmu, lalu tanyakan fasilitas pendukung yang tersedia untuk melengkapi waktu pemulihanmu.', 'image' => 'images/facility-recovery-3d-v2.png', 'items' => $recoveryFacilities],
            ['title' => 'Kenyamanan', 'description' => 'Nikmati sesi latihan dengan fasilitas pendukung yang melengkapi kebutuhanmu sejak datang hingga pulang. Area parkir, ruang ganti, dan locker membantu kamu bersiap dengan lebih praktis, sementara AC serta air minum mendukung kenyamanan selama berada di gym. Fokuskan waktumu untuk bergerak dan membangun rutinitas.', 'image' => 'images/facility-comfort-3d-v2.png', 'items' => $comfortFacilities],
        ];
    @endphp
    <div class="mx-auto max-w-[72rem]">
        <div class="reveal relative z-0 text-center">
            <h2 id="facilities-title" class="whitespace-nowrap text-[clamp(3.25rem,14.8vw,12.2rem)] font-bold uppercase leading-[.85] tracking-[-.065em] text-[#e8e8df]">Fasilitas</h2>
        </div>

        <ol class="facility-feature-list relative z-10 mt-24 flex flex-col gap-28 md:-mt-8 md:gap-20 lg:-mt-12">
            @foreach ($facilityGroups as $facilityGroup)
                <li class="facility-scroll-item relative" data-facility-reveal>
                    <article class="facility-feature group relative rounded-[2rem] border border-white/10 bg-[#222222] transition-colors duration-300 hover:border-white/25 {{ $loop->even ? 'facility-feature-reversed' : '' }}">
                        <div class="facility-feature-copy">
                            <p class="text-[.55rem] font-semibold uppercase tracking-[.2em] text-white/35">CukipGym / {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                            <h3 class="mt-3 text-[clamp(1.35rem,2.5vw,2rem)] font-semibold leading-tight tracking-[-.04em]">{{ $facilityGroup['title'] }}</h3>
                            <p class="mt-5 text-justify text-sm leading-7 text-white/55">{{ $facilityGroup['description'] }}</p>
                            @if ($facilityGroup['items']->isNotEmpty())
                                <ul class="mt-6 flex flex-col gap-3">
                                    @foreach ($facilityGroup['items'] as $facility)
                                        <li class="flex items-center gap-3 text-sm text-white/75"><span class="grid size-4 shrink-0 place-items-center rounded-full border border-[#b9b9ae] text-[.6rem] text-[#d4d4cc]" aria-hidden="true">✓</span>{{ $facility->name }}</li>
                                    @endforeach
                                </ul>
                            @else
                                @php
                                    $trainingHighlights = $loop->iteration === 2
                                        ? ['Treadmill', 'Sepeda Statis', 'Elliptical']
                                        : ['Matras Peregangan', 'Foam Roller', 'Yoga Block'];
                                @endphp
                                <ul class="mt-6 flex flex-col gap-3">
                                    @foreach ($trainingHighlights as $trainingHighlight)
                                        <li class="flex items-center gap-3 text-sm text-white/75"><span class="grid size-4 shrink-0 place-items-center rounded-full border border-[#b9b9ae] text-[.6rem] text-[#d4d4cc]" aria-hidden="true">✓</span>{{ $trainingHighlight }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <figure class="facility-feature-art pointer-events-none">
                            <div class="facility-art-tilt h-full w-full">
                                <img src="{{ asset($facilityGroup['image']) }}" alt="Ilustrasi 3D {{ $facilityGroup['title'] }}" class="facility-art-float h-full w-full object-contain drop-shadow-2xl" loading="lazy">
                            </div>
                        </figure>
                    </article>
                </li>
            @endforeach
        </ol>

        <div class="mt-12 sm:px-8">
            <a href="{{ route('facilities.index') }}" class="inline-flex items-center gap-4 border-b border-white/25 pb-2 text-sm font-medium transition hover:border-white hover:text-white/70">Jelajahi semua fasilitas <span aria-hidden="true">↗</span></a>
        </div>
    </div>
</section>

<div class="relative -mb-px h-16 overflow-hidden bg-[#171717] sm:h-24 lg:h-32" aria-hidden="true">
    <svg class="absolute inset-0 h-full w-full" viewBox="0 0 1440 160" preserveAspectRatio="none" fill="none">
        <path d="M0 66C240 168 440 160 720 92S1180 24 1440 98V160H0Z" fill="white"/>
    </svg>
</div>

<section class="membership-backdrop relative isolate overflow-hidden bg-white px-5 py-20 text-[#171717] sm:px-8 lg:px-12 lg:py-28" aria-labelledby="plans-title" data-navbar-tone="light" data-plan-picker>
    <div class="pointer-events-none absolute -right-48 -top-56 size-[36rem] rounded-full border border-black/[.06] p-14 sm:-right-24" aria-hidden="true">
        <div class="size-full rounded-full border border-black/[.05] p-14">
            <div class="size-full rounded-full border border-black/[.04]"></div>
        </div>
    </div>
    <div class="pointer-events-none absolute -bottom-64 -left-48 size-[40rem] rounded-full border border-black/[.05] p-20" aria-hidden="true">
        <div class="size-full rounded-full border border-black/[.04]"></div>
    </div>
    <div class="relative mx-auto max-w-[76rem]">
        <div class="grid gap-6 lg:grid-cols-[1fr_.65fr] lg:items-end">
            <div>
                <p class="section-kicker">Paket membership</p>
                <h2 id="plans-title" class="reveal mt-6 text-[clamp(2.6rem,4.5vw,4.25rem)] font-bold leading-[1.02] tracking-[-.06em]">Pilih akses.<br><span class="text-[#737373]">Mulai bergerak.</span></h2>
            </div>
            <p class="reveal max-w-sm text-justify text-sm leading-7 text-[#73736d] lg:justify-self-end" style="--reveal-delay: 100ms">Mulai dari satu hari atau bangun rutinitas jangka panjang. Semua paket dibuat sederhana, transparan, dan mudah dipilih.</p>
        </div>

        @if ($plans->contains(fn ($plan) => $plan->duration_days >= 300))
            <div class="mt-9 flex flex-col items-center gap-3">
                <div class="inline-flex rounded-xl border border-[#171717]/10 bg-[#e5e5e5] p-1" role="group" aria-label="Durasi paket jangka panjang">
                    <button type="button" class="plan-period-button rounded-lg px-6 py-2.5 text-xs font-semibold transition" data-plan-period="monthly" aria-pressed="true">Monthly</button>
                    <button type="button" class="plan-period-button rounded-lg px-6 py-2.5 text-xs font-semibold transition" data-plan-period="yearly" aria-pressed="false">Yearly</button>
                </div>
                <p class="text-[.65rem] text-[#737373]">Day Pass tetap berlaku untuk satu hari.</p>
            </div>
        @endif

        <div class="mt-9 grid gap-x-7 gap-y-10 lg:grid-cols-3 lg:gap-x-8">
            @foreach ($plans->groupBy('name') as $planVariants)
                <div class="plan-slot">
                    @foreach ($planVariants as $plan)
                        <div class="plan-option" data-plan-option="{{ $plan->duration_days >= 300 ? 'yearly' : ($plan->duration_days >= 28 ? 'monthly' : 'all') }}" data-active="{{ $plan->duration_days >= 300 ? 'false' : 'true' }}" aria-hidden="{{ $plan->duration_days >= 300 ? 'true' : 'false' }}" @if ($plan->duration_days >= 300) inert @endif>
                            <x-sculpted-plan-card :plan="$plan" :featured="$plan->name === 'VIP'" />
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="journey-scroll relative bg-white text-[#171717]" aria-labelledby="steps-title" data-navbar-tone="light" data-journey-scroll>
    <div class="journey-sticky sticky top-0 flex h-svh flex-col overflow-hidden px-5 pb-8 pt-28 sm:px-8 lg:px-12">
    <div class="absolute -right-28 -top-28 size-96 rounded-full border border-white/25" aria-hidden="true"></div>
    <div class="absolute -right-10 -top-10 size-56 rounded-full border border-white/35" aria-hidden="true"></div>
    <div class="relative mx-auto flex min-h-0 w-full max-w-[72rem] flex-1 flex-col">
        <p class="section-kicker border-[#171717]/25 text-[#171717]">Cara bergabung</p>
        <h2 id="steps-title" class="mt-5 text-[clamp(2rem,4vw,3.5rem)] font-bold leading-[1.05] tracking-[-0.06em]">Perjalananmu dimulai di sini.</h2>

        <div class="journey-camera relative mt-8 min-h-0 flex-1" data-journey-camera>
        <div class="joining-journey relative mx-auto max-w-5xl" data-joining-journey>
        <svg class="journey-road pointer-events-none absolute inset-0 h-full w-full" viewBox="0 0 1700 1900" fill="none" aria-hidden="true">
            <path data-journey-route d="M260 160C260 420 870 290 870 550S290 680 290 940" stroke="#b1b1a9" stroke-width="2" stroke-dasharray="4 10" stroke-linecap="round"/>
            <path data-journey-trail pathLength="1" stroke="#45453e" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="1" stroke-dashoffset="1"/>
            <g class="journey-traveler" data-journey-dot>
                <ellipse cx="0" cy="14" rx="27" ry="6" fill="#171717" opacity=".12"/>
                <g class="journey-runner-facing">
                    <g class="journey-walker">
                        <svg x="-60" y="-102" width="120" height="120" viewBox="0 0 512 512" overflow="hidden">
                            <image class="journey-runner-sprite" href="{{ asset('images/journey-runner-sprites.png') }}" width="1536" height="1024"/>
                        </svg>
                    </g>
                </g>
            </g>
        </svg>
        <ol class="relative">
            <li class="joining-journey-card">
                <p class="text-sm font-semibold">01 / Pilih</p>
                <h3 class="mt-8 text-3xl font-semibold tracking-[-0.04em]">Temukan paketmu</h3>
                <p class="mt-5 text-justify text-base leading-7 text-[#404040]/72">Mulai dengan memilih paket membership yang sesuai dengan jadwal, target, dan ritme latihanmu. Bandingkan pilihan durasi serta harga yang tersedia, lalu tentukan masa akses yang paling cocok untuk rutinitasmu. Baik untuk mencoba sesi pertama maupun membangun kebiasaan jangka panjang, pilih langkah awal yang nyaman untuk kamu jalani secara konsisten.</p>
            </li>
            <li class="joining-journey-card self-end">
                <p class="text-sm font-semibold">02 / Bayar</p>
                <h3 class="mt-8 text-3xl font-semibold tracking-[-0.04em]">Aktifkan membership</h3>
                <p class="mt-5 text-justify text-base leading-7 text-[#404040]/72">Isi data diri dan selesaikan pembayaran tanpa perlu membuat akun terlebih dahulu. Setelah pembayaran berhasil diverifikasi, email dan password akun akan dikirim ke email yang kamu masukkan. Gunakan akun tersebut untuk memantau membership dan masa aktifnya.</p>
            </li>
            <li class="joining-journey-card">
                <p class="text-sm font-semibold">03 / Bergerak</p>
                <h3 class="mt-8 text-3xl font-semibold tracking-[-0.04em]">Mulai latihan</h3>
                <p class="mt-5 text-justify text-base leading-7 text-[#404040]/72">Saat membership sudah aktif, datanglah sesuai jam operasional dan siapkan waktu untuk sesi pertamamu. Kenali area serta peralatan gym, lalu pilih latihan atau kelas yang sesuai dengan kemampuan dan tujuanmu. Mulai dengan intensitas yang nyaman, beri tubuh waktu beradaptasi, dan bangun progres melalui rutinitas yang bisa kamu ulang secara konsisten.</p>
            </li>
        </ol>
        </div>
        </div>
    </div>
    </div>
</section>

<div class="relative -mb-px h-16 overflow-hidden bg-white sm:h-24 lg:h-32" aria-hidden="true">
    <svg class="absolute inset-0 h-full w-full" viewBox="0 0 1440 160" preserveAspectRatio="none" fill="none">
        <path d="M0 94C260 28 470 34 740 104S1200 166 1440 62V160H0Z" fill="black"/>
    </svg>
</div>

<section class="bg-black px-5 py-20 text-white sm:px-8 lg:px-12 lg:py-24" aria-labelledby="location-title" data-navbar-tone="dark" data-location-explorer>
    @php
        $initialLocation = $marker[0] ?? [
            'name' => $profile->name,
            'region' => $profile->address,
            'image' => $profile->image_url ?? asset('images/gym-equipment-editorial.webp'),
            'hours' => $profile->opening_hours,
            'maps_url' => $profile->maps_url,
            'whatsapp_url' => $profile->whatsapp_url,
        ];
    @endphp
    <div class="mx-auto max-w-[90rem]">
        <div class="mb-10 flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="w-fit border-b border-white/25 pb-[.45rem] text-[.7rem] font-semibold uppercase tracking-[.18em]">Temukan kami</p>
                <h2 id="location-title" class="reveal mt-5 text-[clamp(2.5rem,4.5vw,4.5rem)] font-semibold leading-none tracking-[-.055em]">Tempat latihanmu.<br><span class="text-[#858585]">Lebih dekat.</span></h2>
            </div>
            <p class="max-w-64 text-sm leading-7 text-white/60">Jelajahi peta dan pilih penanda lokasi untuk melihat foto serta informasi gym.</p>
        </div>

        <div class="grid overflow-hidden rounded-[1.75rem] border border-white/15 bg-[#202020] lg:grid-cols-[.85fr_1.15fr]">
            <div class="flex min-w-0 flex-col">
                <figure class="relative h-72 bg-[#303030] sm:h-96 lg:h-[25rem]">
                    <img src="{{ $initialLocation['image'] }}" alt="Visual {{ $initialLocation['name'] }}" class="absolute inset-0 h-full w-full object-cover" style="object-position: {{ $initialLocation['image_position'] ?? 'center' }}" loading="lazy" data-location-photo>
                    <span class="absolute bottom-5 left-6 rounded-full bg-black/55 px-4 py-2 text-[.6rem] font-semibold uppercase tracking-[.16em] text-white backdrop-blur-sm">Tanjungpinang, Indonesia</span>
                </figure>
                <div class="flex flex-1 flex-col gap-6 p-7 text-white sm:p-9" data-location-details aria-live="polite" aria-atomic="true">
                    @if (config('gym_locations.preview_branches', []) !== [])
                        <p class="text-[.55rem] font-semibold uppercase tracking-[.2em] text-white/35">Cabang contoh · Tanjungpinang</p>
                    @endif
                    <div class="flex items-start justify-between gap-5">
                        <h3 class="text-3xl font-semibold tracking-[-.04em] sm:text-4xl" data-location-name>{{ $initialLocation['name'] }}</h3>
                        <span class="pt-2 text-xs tabular-nums text-white/40" data-location-count>{{ count($marker) ? '01 / ' . str_pad((string) count($marker), 2, '0', STR_PAD_LEFT) : '—' }}</span>
                    </div>
                    <div>
                        <p class="max-w-sm text-sm leading-7 text-white/65" data-location-address>{{ $initialLocation['region'] }}</p>
                        <p class="mt-2 text-xs text-white/45">Jam operasional <span class="ml-2 text-white/80" data-location-hours>{{ $initialLocation['hours'] }}</span></p>
                    </div>
                </div>
            </div>
            <div class="relative min-w-0">
                <x-gym-map :markers="$marker" :selectable="true" height="h-[28rem] lg:h-full lg:min-h-[43rem]" />
                @if (count($marker) === 0)
                    <p class="absolute left-4 top-4 z-[500] rounded-lg bg-white px-4 py-3 text-xs text-[#171717] shadow-sm">Koordinat lokasi belum tersedia.</p>
                @endif
            </div>
        </div>
    </div>
</section>
<section id="review-member" class="relative isolate overflow-hidden bg-black px-5 py-20 text-white sm:px-8 lg:px-12 lg:py-24" aria-labelledby="member-reviews-title" data-navbar-tone="dark">
    @php
        $reviewCards = [];
        foreach ($reviews as $review) {
            $reviewCards[] = [
                'name' => $review->name ?? $review->user?->name ?? 'Member CukipGym',
                'rating' => $review->rating,
                'text' => $review->review,
                'caption' => 'Member CukipGym',
            ];
        }
        if ($reviewCards === []) {
            $reviewCards = [
                ['name' => 'Raka', 'rating' => 5, 'text' => 'Awalnya cuma ingin coba latihan. Setelah menemukan ritme yang cocok, datang ke gym jadi bagian paling saya tunggu setelah aktivitas seharian.', 'caption' => 'Contoh review'],
                ['name' => 'Nadia', 'rating' => 5, 'text' => 'Sebagai pemula, saya suka suasana latihan yang nyaman. Bisa mulai pelan-pelan, kenalan dengan alat, dan membangun kebiasaan tanpa merasa terburu-buru.', 'caption' => 'Contoh review'],
                ['name' => 'Dimas', 'rating' => 4, 'text' => 'Pilihan paketnya mudah dipahami. Saya bisa menyesuaikan waktu latihan dengan jadwal kerja dan tetap punya alasan untuk bergerak setiap minggu.', 'caption' => 'Contoh review'],
            ];
        }
    @endphp
    <div class="pointer-events-none absolute -right-48 top-12 -z-10 size-[36rem] rounded-full border border-white/[.04]" aria-hidden="true"></div>
    <div class="mx-auto max-w-[80rem]">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="w-fit border-b border-white/20 pb-2 text-[.65rem] font-semibold uppercase tracking-[.2em] text-white/60">Review member</p>
                <h2 id="member-reviews-title" class="reveal mt-6 text-[clamp(2.5rem,4.5vw,4.5rem)] font-semibold leading-[1.02] tracking-[-.06em]">Kata mereka.<br><span class="text-[#858585]">Tentang langkah pertama.</span></h2>
            </div>
            <p class="max-w-xs text-sm leading-7 text-white/50">Setiap orang punya tujuan berbeda. Pengalaman merekalah yang membuat tempat ini berarti.</p>
        </div>
        @if ($reviews->isEmpty())
            <p class="mt-7 text-xs text-white/40">Contoh review untuk pratinjau tampilan.</p>
        @endif
        <div id="review-carousel" class="review-carousel mt-9" data-review-carousel role="region" aria-roledescription="carousel" aria-label="Review member" tabindex="0">
            <div class="review-carousel-track" data-review-track>
                <div class="review-carousel-group" data-review-group>
            @foreach ($reviewCards as $reviewCard)
                <figure class="review-carousel-card flex flex-col rounded-[1.5rem] border border-white/10 bg-[#171717] p-7 text-white transition-colors hover:border-white/25 sm:p-8">
                    <figcaption class="flex items-center gap-3">
                        <span class="grid size-11 shrink-0 place-items-center rounded-full bg-white/10 text-sm font-semibold text-white/70" aria-hidden="true">{{ mb_strtoupper(mb_substr($reviewCard['name'], 0, 1)) }}</span>
                        <div>
                            <p class="text-sm font-semibold">{{ $reviewCard['name'] }}</p>
                            <span class="mt-1 block text-sm tracking-[.1em] text-white/85" role="img" aria-label="Rating {{ $reviewCard['rating'] }} dari 5"><span aria-hidden="true">{{ str_repeat('★', $reviewCard['rating']) }}<span class="text-white/20">{{ str_repeat('★', 5 - $reviewCard['rating']) }}</span></span></span>
                        </div>
                    </figcaption>
                    <p class="mt-5 text-[.65rem] text-white/40">{{ $reviewCard['caption'] }}</p>
                    <blockquote class="mt-5 flex-1 text-sm leading-7 text-white/65">
                        <p>“{{ $reviewCard['text'] }}”</p>
                    </blockquote>
                </figure>
            @endforeach
                </div>
            </div>
        </div>
        <details id="write-review" class="group mt-12 scroll-mt-28 border-t border-white/10 pt-8" @if ($errors->getBag('review')->any() || session('review_success')) open @endif>
            <summary class="flex cursor-pointer list-none flex-col gap-5 rounded-lg focus-visible:outline-2 focus-visible:outline-offset-8 focus-visible:outline-white sm:flex-row sm:items-center sm:justify-between [&::-webkit-details-marker]:hidden">
                <span class="text-lg font-medium tracking-[-.03em] text-white/80">Sudah pernah latihan di sini? Bagikan ceritamu.</span>
                <span class="inline-flex w-fit shrink-0 items-center gap-6 rounded-full bg-white px-6 py-3.5 text-sm font-semibold text-[#171717] transition group-hover:bg-[#d4d4d4]">{{ $ownReview ? 'Perbarui review' : 'Tulis review' }} <span class="transition group-open:rotate-45" aria-hidden="true">＋</span></span>
            </summary>
            <div class="mt-8 rounded-[1.5rem] border border-white/10 bg-[#171717] p-6 sm:p-8">
                @if (session('review_success'))
                    <p class="mb-6 rounded-xl border border-white/15 bg-white/[.05] px-4 py-3 text-sm text-white/85" role="status">{{ session('review_success') }}</p>
                @endif
                @guest
                    <h3 class="text-xl font-semibold">Masuk untuk menulis review.</h3>
                    <p class="mt-3 max-w-xl text-sm leading-7 text-white/55">Review terhubung dengan akun member yang pernah latihan di CukipGym.</p>
                    <div class="mt-5 flex flex-wrap gap-4 text-sm font-medium">
                        <a href="{{ route('login') }}" class="rounded-full bg-white px-6 py-3 text-[#171717] hover:bg-[#d4d4d4]">Masuk akun</a>
                        <a href="{{ route('membership.index') }}#pilihan-paket" class="rounded-full border border-white/20 px-6 py-3 hover:border-white/50">Pilih membership</a>
                    </div>
                @else
                    @if ($canReview)
                        <form method="POST" action="{{ route('reviews.store') }}" class="grid gap-6 lg:grid-cols-[.75fr_1fr]">
                            @csrf
                            <div class="flex flex-col gap-6">
                                <div>
                                    <label for="review-name" class="mb-2 block text-sm font-medium">Nama tampilan</label>
                                    <input id="review-name" type="text" name="name" value="{{ is_string(old('name')) ? old('name') : ($ownReview?->name ?? auth()->user()->name) }}" required maxlength="100" autocomplete="name" class="w-full rounded-xl border border-white/15 bg-black/20 px-4 py-3 text-sm text-white outline-none focus:border-white/60" @error('name', 'review') aria-invalid="true" aria-describedby="review-name-error" @enderror>
                                    @error('name', 'review')<p id="review-name-error" class="mt-2 text-xs text-white/70">{{ $message }}</p>@enderror
                                </div>
                                <fieldset @error('rating', 'review') aria-describedby="review-rating-error" @enderror>
                                    <legend class="mb-2 text-sm font-medium">Beri bintang</legend>
                                    <div class="review-rating">
                                        @for ($rating = 5; $rating >= 1; $rating--)
                                            <label>
                                                <input type="radio" name="rating" value="{{ $rating }}" class="sr-only" required @checked(is_scalar(old('rating', $ownReview?->rating)) && (string) old('rating', $ownReview?->rating) === (string) $rating)>
                                                <span class="sr-only">{{ $rating }} bintang</span><span aria-hidden="true">★</span>
                                            </label>
                                        @endfor
                                    </div>
                                    @error('rating', 'review')<p id="review-rating-error" class="mt-2 text-xs text-white/70">{{ $message }}</p>@enderror
                                </fieldset>
                            </div>
                            <div>
                                <label for="review-description" class="mb-2 block text-sm font-medium">Ceritakan pengalamanmu</label>
                                <textarea id="review-description" name="review" rows="5" required minlength="10" maxlength="2000" placeholder="Bagaimana suasana, fasilitas, atau pengalaman latihanmu?" class="w-full resize-y rounded-xl border border-white/15 bg-black/20 px-4 py-3 text-sm leading-7 text-white outline-none placeholder:text-white/30 focus:border-white/60" @error('review', 'review') aria-invalid="true" aria-describedby="review-description-error" @enderror>{{ is_string(old('review')) ? old('review') : ($ownReview?->review ?? '') }}</textarea>
                                @error('review', 'review')<p id="review-description-error" class="mt-2 text-xs text-white/70">{{ $message }}</p>@enderror
                                <p class="mt-2 text-xs text-white/35">10–2.000 karakter. Satu review per akun, bisa diperbarui kapan saja.</p>
                            </div>
                            <div class="lg:col-span-2">
                                <button type="submit" class="rounded-full bg-white px-7 py-3.5 text-sm font-semibold text-[#171717] transition hover:bg-[#d4d4d4] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">{{ $ownReview ? 'Simpan perubahan' : 'Kirim review' }} <span class="ml-4" aria-hidden="true">↗</span></button>
                            </div>
                        </form>
                    @else
                        <h3 class="text-xl font-semibold">Ceritakan setelah mencoba latihan.</h3>
                        <p class="mt-3 max-w-xl text-sm leading-7 text-white/55">Review tersedia untuk akun yang pernah memiliki membership aktif. Pilih paketmu dan mulai kunjungan pertama.</p>
                        <a href="{{ route('membership.index') }}" class="mt-5 inline-flex rounded-full bg-white px-6 py-3 text-sm font-semibold text-[#171717] hover:bg-[#d4d4d4]">Lihat membership <span class="ml-4" aria-hidden="true">↗</span></a>
                    @endif
                @endguest
            </div>
        </details>
    </div>
</section>

<div class="relative -mb-px h-16 overflow-hidden bg-black sm:h-24 lg:h-28" aria-hidden="true">
    <svg class="absolute inset-0 h-full w-full text-white" viewBox="0 0 1440 160" preserveAspectRatio="none" fill="currentColor">
        <path d="M0 90C280 12 480 30 760 98S1210 138 1440 54V160H0Z"/>
    </svg>
</div>

<section id="faq" class="bg-white px-5 pb-20 pt-16 text-[#171717] sm:px-8 lg:px-12 lg:pb-28 lg:pt-24" aria-labelledby="faq-title" data-navbar-tone="light">
    @php
        $frequentlyAskedQuestions = [
            ['question' => 'Baru pertama kali ke gym, boleh ikut?', 'answer' => 'Tentu. Kamu bisa mulai sesuai kemampuan dan membangun rutinitas secara bertahap. Kenali area latihan terlebih dahulu dan tanyakan kepada tim gym jika membutuhkan arahan penggunaan alat.'],
            ['question' => 'Bagaimana cara mendaftar membership?', 'answer' => 'Pilih paket, isi data diri, lalu selesaikan pembayaran tanpa perlu login. Setelah pembayaran berhasil, kredensial akun dikirim ke emailmu untuk memantau membership.'],
            ['question' => 'Bisa mencoba latihan untuk satu hari?', 'answer' => 'Bisa. Pilih Day Pass untuk akses satu hari. Paket ini cocok untuk mengenal suasana gym sebelum memilih membership dengan durasi yang lebih panjang.'],
            ['question' => 'Apa saja fasilitas yang tersedia?', 'answer' => 'Tersedia area latihan beban, peralatan kardio, dan fasilitas pendukung latihan. Cek daftar fasilitas pada paket pilihanmu karena akses dan manfaat setiap paket dapat berbeda.'],
            ['question' => 'Apakah tersedia kelas kebugaran?', 'answer' => 'Kamu bisa melihat pilihan dan jadwal pada halaman Kelas. Periksa juga fasilitas paket membership untuk memastikan akses kelas sudah termasuk dalam paketmu.'],
            ['question' => 'Jam berapa gym buka?', 'answer' => 'Jam operasional ditampilkan pada informasi setiap cabang di peta. Pilih penanda lokasi yang ingin kamu kunjungi untuk melihat jam buka serta area cabangnya.'],
            ['question' => 'Di mana saya melihat masa aktif membership?', 'answer' => 'Masuk ke akunmu dan buka halaman profil untuk melihat status pembayaran serta masa aktif membership. Kamu bisa memilih paket berikutnya melalui halaman Membership.'],
        ];
    @endphp
    <div class="relative mx-auto max-w-[80rem] rounded-[2rem] bg-[#f5f5f5] lg:grid lg:grid-cols-[.9fr_1fr] lg:gap-8">
        <div class="faq-portrait relative h-80 sm:h-[26rem] lg:h-full lg:min-h-[48rem]" data-faq-portrait aria-hidden="true">
            <div class="pointer-events-none absolute bottom-12 left-1/2 size-64 -translate-x-1/2 rounded-full border border-black/[.06] sm:size-96 lg:bottom-32"></div>
            <img src="{{ asset('images/faq-athlete-cutout.png') }}" alt="" loading="lazy">
        </div>
        <div class="relative px-5 pb-7 pt-5 sm:px-9 sm:pb-10 lg:py-14 lg:pl-0 lg:pr-14">
            <p class="section-kicker">Tanya jawab</p>
            <h2 id="faq-title" class="mt-5 text-[clamp(2rem,3.4vw,3.25rem)] font-semibold leading-[1.05] tracking-[-.055em]">Ada pertanyaan?<br><span class="text-[#737373]">Kami bantu jawab.</span></h2>
            <p class="mb-8 mt-4 max-w-md text-sm leading-7 text-[#737373]">Kenali membership, fasilitas, dan cara memulai latihan di CukipGym sebelum kunjungan pertamamu.</p>
            <div class="flex flex-col gap-3">
                @foreach ($frequentlyAskedQuestions as $frequentlyAskedQuestion)
                    <details class="faq-item group rounded-2xl border border-black/[.035] bg-white" name="gym-faq" @if ($loop->first) open @endif>
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-5 rounded-2xl px-5 py-5 text-sm font-medium focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717] sm:px-6">
                            {{ $frequentlyAskedQuestion['question'] }}
                            <span class="grid size-7 shrink-0 place-items-center rounded-full bg-[#ededed] text-[#737373] transition duration-200 group-open:rotate-180 group-open:bg-[#171717] group-open:text-white" aria-hidden="true">
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                            </span>
                        </summary>
                        <p class="px-5 pb-6 pr-7 text-sm leading-7 text-[#737373] sm:px-6">{{ $frequentlyAskedQuestion['answer'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </div>
</section>

@endsection
