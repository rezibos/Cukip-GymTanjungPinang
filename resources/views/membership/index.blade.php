@extends('layouts.app')

@section('title', 'Paket Membership - CukipGym')
@section('description', 'Pilih Day Pass, Regular, atau VIP CukipGym dan temukan durasi akses yang sesuai dengan rutinitas latihanmu.')
@section('main-class', 'w-full flex-1 bg-white')

@section('content')
@php
    $hasMonthlyPlans = false;
    $hasYearlyPlans = false;
    foreach ($plans as $plan) {
        if ($plan->duration_days >= 300) {
            $hasYearlyPlans = true;
        } elseif ($plan->duration_days >= 28) {
            $hasMonthlyPlans = true;
        }
    }
    $defaultPeriod = $hasMonthlyPlans ? 'monthly' : 'yearly';
@endphp

<section class="membership-backdrop relative isolate overflow-hidden px-5 pb-16 pt-32 text-[#171717] sm:px-8 lg:px-12 lg:pb-24 lg:pt-40" aria-labelledby="membership-title" data-navbar-tone="light" data-plan-picker data-plan-selected="{{ $defaultPeriod }}">
    <div class="pointer-events-none absolute -right-48 -top-56 size-[40rem] rounded-full border border-black/[.05] p-16" aria-hidden="true">
        <div class="size-full rounded-full border border-black/[.04] p-16"><div class="size-full rounded-full border border-black/[.04]"></div></div>
    </div>
    <div class="relative mx-auto max-w-[76rem]">
        <div class="grid gap-8 lg:grid-cols-[1.25fr_.75fr] lg:items-end lg:gap-20">
            <div>
                <p class="section-kicker">Membership CukipGym</p>
                <h1 id="membership-title" class="mt-6 text-[clamp(3rem,6.7vw,6.5rem)] font-semibold leading-[.95] tracking-[-.065em]">Pilih ritmemu.<br><span class="text-[#858585]">Mulai bergerak.</span></h1>
            </div>
            <div class="max-w-sm lg:pb-1 lg:justify-self-end">
                <p class="text-justify text-sm leading-7 text-[#737373]">Mulai dari satu kunjungan atau jadikan latihan bagian dari keseharian. Bandingkan akses, fasilitas, dan durasi untuk menemukan paket yang paling cocok denganmu.</p>
                <a href="#pilihan-paket" class="mt-6 inline-flex items-center gap-3 text-xs font-semibold text-[#404040] transition hover:text-[#858585]">Temukan paketmu <span aria-hidden="true">↓</span></a>
            </div>
        </div>

        <div id="pilihan-paket" class="mt-12 scroll-mt-28 lg:mt-14">
            <div class="flex flex-col gap-5 border-t border-black/10 pt-7 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold tracking-[-.035em]">Satu tujuan. Pilihanmu.</h2>
                    <p class="mt-2 text-xs leading-6 text-[#858585]">Day Pass tetap berlaku untuk satu hari.</p>
                </div>
                @if ($hasMonthlyPlans && $hasYearlyPlans)
                    <div class="inline-flex w-fit rounded-full border border-black/10 bg-[#f0f0f0] p-1" role="group" aria-label="Durasi membership">
                        <button type="button" class="plan-period-button rounded-full px-6 py-2.5 text-xs font-semibold transition" data-plan-period="monthly" aria-pressed="true">Bulanan</button>
                        <button type="button" class="plan-period-button rounded-full px-6 py-2.5 text-xs font-semibold transition" data-plan-period="yearly" aria-pressed="false">Tahunan</button>
                    </div>
                @endif
            </div>

            @if ($plans->isEmpty())
                <p class="mt-9 rounded-[1.5rem] border border-black/10 bg-white px-7 py-10 text-sm leading-7 text-[#737373]">Paket membership sedang diperbarui. Silakan cek kembali untuk melihat pilihan yang tersedia.</p>
            @else
                <div class="mt-9 grid gap-x-7 gap-y-10 lg:grid-cols-3 lg:gap-x-8">
                    @foreach ($plans->groupBy('name') as $planVariants)
                        <div class="plan-slot">
                            @foreach ($planVariants as $plan)
                                @php
                                    $planPeriod = $plan->duration_days >= 300 ? 'yearly' : ($plan->duration_days >= 28 ? 'monthly' : 'all');
                                    $isActive = $planPeriod === 'all' || $planPeriod === $defaultPeriod;
                                @endphp
                                <div class="plan-option" data-plan-option="{{ $planPeriod }}" data-active="{{ $isActive ? 'true' : 'false' }}" aria-hidden="{{ $isActive ? 'false' : 'true' }}" @if (! $isActive) inert @endif>
                                    <x-sculpted-plan-card :plan="$plan" :featured="$plan->name === 'VIP'" />
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mt-12 flex flex-wrap justify-center gap-x-8 gap-y-3 text-[.7rem] text-[#858585]">
            <p class="flex items-center gap-2"><span aria-hidden="true">✓</span> Pilih durasi sesuai rutinitas</p>
            <p class="flex items-center gap-2"><span aria-hidden="true">✓</span> Lihat fasilitas setiap paket</p>
            <p class="flex items-center gap-2"><span aria-hidden="true">✓</span> Pantau membership dari profil</p>
        </div>
    </div>
</section>

<section class="px-5 pb-20 text-[#171717] sm:px-8 lg:px-12 lg:pb-24" aria-labelledby="membership-steps-title" data-navbar-tone="light">
    <div class="mx-auto max-w-[76rem]">
        <div class="grid gap-8 border-t border-black/10 pt-10 lg:grid-cols-[.7fr_1.3fr] lg:gap-20">
            <div>
                <p class="section-kicker">Cara bergabung</p>
                <h2 id="membership-steps-title" class="mt-5 text-3xl font-semibold leading-[1.05] tracking-[-.045em] sm:text-4xl">Siap latihan.<br><span class="text-[#858585]">Dalam tiga langkah.</span></h2>
            </div>
            <ol class="grid gap-7 sm:grid-cols-3 sm:gap-6">
                <li>
                    <span class="grid size-9 place-items-center rounded-full border border-black/15 text-xs text-[#858585]">01</span>
                    <h3 class="mt-5 text-sm font-semibold">Pilih paketmu</h3>
                    <p class="mt-3 text-xs leading-6 text-[#737373]">Tentukan fasilitas dan masa akses yang sesuai dengan kebutuhan latihan.</p>
                </li>
                <li>
                    <span class="grid size-9 place-items-center rounded-full border border-black/15 text-xs text-[#858585]">02</span>
                    <h3 class="mt-5 text-sm font-semibold">Selesaikan pembayaran</h3>
                    <p class="mt-3 text-xs leading-6 text-[#737373]">Masuk ke akunmu, periksa pesanan, lalu ikuti petunjuk pada invoice.</p>
                </li>
                <li>
                    <span class="grid size-9 place-items-center rounded-full border border-black/15 text-xs text-[#858585]">03</span>
                    <h3 class="mt-5 text-sm font-semibold">Mulai bergerak</h3>
                    <p class="mt-3 text-xs leading-6 text-[#737373]">Pantau status di profil. Setelah membership aktif, mulai sesi pertamamu.</p>
                </li>
            </ol>
        </div>

        <div class="mt-14 grid overflow-hidden rounded-[2rem] bg-[#171717] text-white sm:grid-cols-[1fr_.65fr]">
            <div class="p-8 sm:p-10 lg:p-12">
                <p class="text-[.6rem] font-medium uppercase tracking-[.2em] text-white/45">Kenali tempat latihanmu</p>
                <h2 class="mt-4 text-3xl font-semibold leading-[1.05] tracking-[-.045em] sm:text-4xl">Ruangnya siap.<br><span class="text-white/45">Tinggal langkahmu.</span></h2>
                <a href="{{ route('facilities.index') }}" class="mt-7 inline-flex items-center gap-6 rounded-full border border-white/25 px-5 py-3 text-xs font-semibold transition hover:bg-white hover:text-[#171717] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">Jelajahi fasilitas <span aria-hidden="true">↗</span></a>
            </div>
            <figure class="relative min-h-52 sm:min-h-0">
                <img src="{{ asset('images/gym-equipment-editorial.webp') }}" alt="Gambaran peralatan beban bebas di area gym" class="absolute inset-0 h-full w-full object-cover grayscale" loading="lazy">
            </figure>
        </div>
    </div>
</section>

<div class="relative -mb-px h-16 overflow-hidden bg-white sm:h-24" aria-hidden="true">
    <svg class="absolute inset-0 h-full w-full text-[#0a0a0a]" viewBox="0 0 1440 160" preserveAspectRatio="none" fill="currentColor"><path d="M0 94C260 28 470 34 740 104S1200 166 1440 62V160H0Z"/></svg>
</div>
@endsection
