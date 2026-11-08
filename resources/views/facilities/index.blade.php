@extends('layouts.app')

@section('title', 'Fasilitas - CukipGym')
@section('description', 'Jelajahi peralatan latihan dan fasilitas pendukung CukipGym untuk membangun rutinitas latihanmu di Tanjungpinang.')
@section('main-class', 'w-full flex-1 bg-white')

@section('content')
@php
    $facilityCategories = [
        'kekuatan' => ['label' => 'Latihan kekuatan', 'number' => '01', 'description' => 'Dari repetisi pertama sampai set terakhir. Kenali peralatan yang bisa kamu gunakan untuk menyusun latihan beban sesuai kemampuan dan targetmu.', 'items' => []],
        'kardio' => ['label' => 'Kardio & stamina', 'number' => '02', 'description' => 'Temukan ritme gerakmu. Lengkapi rutinitas dengan sesi kardio yang durasi dan intensitasnya bisa kamu sesuaikan secara bertahap.', 'items' => []],
        'pemulihan' => ['label' => 'Pemulihan', 'number' => '03', 'description' => 'Luangkan waktu untuk mengembalikan kenyamanan tubuh setelah latihan. Jelajahi fasilitas yang mendukung jeda dan pemulihanmu.', 'items' => []],
        'kenyamanan' => ['label' => 'Fasilitas pendukung', 'number' => '04', 'description' => 'Latihan terasa lebih praktis saat kebutuhan lainnya sudah tersedia. Dari persiapan sebelum latihan sampai waktunya pulang.', 'items' => []],
    ];
    $facilityDescriptions = [
        'ac' => 'Area berpendingin untuk menjaga kenyamanan selama kamu berlatih.',
        'air minum' => 'Lengkapi kebutuhan minummu di sela aktivitas dan setelah latihan.',
        'barbell' => 'Beban bebas untuk menyusun variasi gerakan dan latihan kekuatan.',
        'bench press' => 'Bangku latihan untuk mendukung gerakan dorong dan latihan beban.',
        'dumbbell' => 'Peralatan beban bebas yang fleksibel untuk berbagai kelompok otot.',
        'locker' => 'Tempat menyimpan barang agar kamu bisa lebih fokus selama latihan.',
        'parkir' => 'Area untuk memarkir kendaraan sebelum memulai sesi latihanmu.',
        'ruang ganti' => 'Ruang untuk berganti pakaian sebelum dan setelah beraktivitas.',
        'toilet' => 'Fasilitas sanitasi yang melengkapi kebutuhan selama berada di gym.',
        'treadmill' => 'Atur kecepatan langkahmu untuk sesi berjalan atau berlari.',
    ];
    foreach ($facilities as $facility) {
        $categoryKey = match (true) {
            (bool) preg_match('/barbell|barbel|dumbbell|bench|rack|smith|cable|beban/i', $facility->name) => 'kekuatan',
            (bool) preg_match('/treadmill|sepeda|bike|elliptical|cardio|kardio/i', $facility->name) => 'kardio',
            (bool) preg_match('/stretch|recovery|shower|sauna|pijat|matras|foam|yoga/i', $facility->name) => 'pemulihan',
            default => 'kenyamanan',
        };
        $facilityCategories[$categoryKey]['items'][] = $facility;
    }
@endphp

<section class="relative isolate overflow-hidden px-5 pb-12 pt-32 text-[#171717] sm:px-8 lg:px-12 lg:pb-16 lg:pt-40" aria-labelledby="facility-page-title" data-navbar-tone="light">
    <div class="pointer-events-none absolute -right-44 -top-56 -z-10 size-[40rem] rounded-full border border-black/[.05] p-16" aria-hidden="true">
        <div class="size-full rounded-full border border-black/[.04] p-16"><div class="size-full rounded-full border border-black/[.04]"></div></div>
    </div>
    <div class="mx-auto max-w-[80rem]">
        <div class="grid gap-8 lg:grid-cols-[1.3fr_.7fr] lg:items-end lg:gap-20">
            <div>
                <p class="section-kicker">Jelajahi fasilitas</p>
                <h1 id="facility-page-title" class="mt-6 text-[clamp(3rem,6.7vw,6.5rem)] font-semibold leading-[.95] tracking-[-.065em]">Ruang untuk<br><span class="text-[#858585]">setiap progres.</span></h1>
            </div>
            <div class="max-w-sm lg:pb-1 lg:justify-self-end">
                <p class="text-justify text-sm leading-7 text-[#737373]">Peralatan untuk bergerak. Ruang untuk fokus. Kenali fasilitas {{ $profile->name }} dan temukan cara latihan yang sesuai dengan ritmemu.</p>
                <div class="mt-7 flex items-center gap-4">
                    <span class="grid size-12 shrink-0 place-items-center rounded-full border border-black/10" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
                    <div>
                        <p class="text-[.6rem] font-medium uppercase tracking-[.16em] text-[#858585]">Jam operasional</p>
                        <p class="mt-1 text-lg font-semibold tracking-[-.03em]">{{ $profile->opening_hours }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 grid gap-4 lg:mt-14 lg:grid-cols-[1.35fr_.65fr]">
            <figure class="relative isolate min-h-80 overflow-hidden rounded-[1.75rem] bg-[#171717] sm:min-h-[26rem]">
                <img src="{{ asset('images/gym-equipment-editorial.webp') }}" alt="Gambaran area beban bebas dan perlengkapan gym" class="absolute inset-0 -z-20 h-full w-full object-cover grayscale" fetchpriority="high">
                <div class="absolute inset-0 -z-10 bg-gradient-to-t from-black/80 via-black/10 to-transparent" aria-hidden="true"></div>
                <figcaption class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-5 p-7 text-white sm:p-9">
                    <div>
                        <p class="text-[.6rem] font-medium uppercase tracking-[.2em] text-white/55">Di balik setiap repetisi</p>
                        <p class="mt-3 text-3xl font-semibold leading-[1.05] tracking-[-.045em] sm:text-4xl">Datang. Fokus.<br>Bergerak.</p>
                    </div>
                    <a href="#daftar-fasilitas" class="grid size-12 shrink-0 place-items-center rounded-full border border-white/35 bg-white/10 text-xl backdrop-blur-sm transition hover:bg-white hover:text-[#171717] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white" aria-label="Lihat daftar fasilitas">↓</a>
                </figcaption>
            </figure>
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-1 lg:grid-rows-[1fr_auto]">
                <figure class="relative min-h-44 overflow-hidden rounded-[1.75rem] bg-[#e5e5e5] sm:min-h-56 lg:min-h-0">
                    <img src="{{ asset('images/gym-kettlebell-editorial.webp') }}" alt="Gambaran latihan kettlebell di area gym" class="absolute inset-0 h-full w-full object-cover object-[center_35%] grayscale" loading="lazy">
                </figure>
                <div class="flex flex-col justify-between rounded-[1.75rem] bg-[#171717] p-6 text-white sm:p-7 lg:flex-row lg:items-end">
                    <p class="text-[clamp(3rem,5vw,4.5rem)] font-semibold leading-none tracking-[-.07em]">{{ str_pad((string) $facilities->count(), 2, '0', STR_PAD_LEFT) }}<span class="text-white/35">.</span></p>
                    <p class="mt-4 text-xs leading-6 text-white/55 lg:mt-0 lg:max-w-32">Fasilitas tersedia<br>untuk rutinitasmu.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="daftar-fasilitas" class="scroll-mt-28 px-5 pb-20 text-[#171717] sm:px-8 lg:px-12 lg:pb-24" aria-labelledby="facility-list-title" data-navbar-tone="light">
    <div class="mx-auto max-w-[80rem]">
        <div class="flex flex-col justify-between gap-6 border-b border-black/10 pb-8 sm:flex-row sm:items-center">
            <h2 id="facility-list-title" class="text-2xl font-semibold tracking-[-.04em]">Kenali ruang latihanmu.</h2>
            <nav class="flex flex-wrap gap-2" aria-label="Kategori fasilitas">
                @foreach ($facilityCategories as $categoryKey => $facilityCategory)
                    @if ($facilityCategory['items'] !== [])
                        <a href="#fasilitas-{{ $categoryKey }}" class="rounded-full border border-black/10 px-4 py-2.5 text-xs font-medium text-[#737373] transition hover:border-[#171717] hover:bg-[#171717] hover:text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717]">{{ $facilityCategory['label'] }} <span class="ml-2 text-[.6rem] opacity-50">{{ count($facilityCategory['items']) }}</span></a>
                    @endif
                @endforeach
            </nav>
        </div>

        @if ($facilities->isEmpty())
            <p class="rounded-2xl bg-[#f5f5f5] px-7 py-10 text-sm leading-7 text-[#737373]">Daftar fasilitas sedang diperbarui. Silakan cek kembali untuk melihat peralatan dan fasilitas yang tersedia.</p>
        @endif

        @foreach ($facilityCategories as $categoryKey => $facilityCategory)
            @if ($facilityCategory['items'] !== [])
                <section id="fasilitas-{{ $categoryKey }}" class="reveal grid scroll-mt-28 gap-8 border-b border-black/[.07] py-12 last:border-b-0 lg:grid-cols-[.75fr_1.25fr] lg:gap-20 lg:py-14" aria-labelledby="category-{{ $categoryKey }}">
                    <div class="max-w-sm">
                        <p class="text-[.6rem] font-semibold uppercase tracking-[.2em] text-[#a3a3a3]">{{ $facilityCategory['number'] }} / Fasilitas</p>
                        <h3 id="category-{{ $categoryKey }}" class="mt-4 text-3xl font-semibold leading-tight tracking-[-.045em] sm:text-4xl">{{ $facilityCategory['label'] }}</h3>
                        <p class="mt-5 text-justify text-sm leading-7 text-[#737373]">{{ $facilityCategory['description'] }}</p>
                    </div>
                    <ul class="grid gap-3 sm:grid-cols-2">
                        @foreach ($facilityCategory['items'] as $facility)
                            <li class="group rounded-[1.25rem] border border-black/[.04] bg-[#f5f5f5] p-6 transition-colors hover:border-black/15 hover:bg-[#eeeeee]">
                                <div class="flex items-start justify-between gap-4">
                                    <h4 class="text-base font-semibold tracking-[-.025em]">{{ $facility->name }}</h4>
                                    <span class="grid size-5 shrink-0 place-items-center rounded-full border border-black/20 text-[.6rem] text-[#737373]" aria-hidden="true">✓</span>
                                </div>
                                <p class="mt-3 text-justify text-xs leading-6 text-[#737373]">{{ $facility->description ?: ($facilityDescriptions[mb_strtolower($facility->name)] ?? 'Fasilitas untuk melengkapi kebutuhan selama sesi latihanmu.') }}</p>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        @endforeach

        <div class="relative isolate mt-10 overflow-hidden rounded-[2rem] bg-[#171717] p-8 text-white sm:p-12 lg:mt-14">
            <div class="pointer-events-none absolute -right-20 -top-36 -z-10 size-96 rounded-full border border-white/[.07] p-14" aria-hidden="true"><div class="size-full rounded-full border border-white/[.05]"></div></div>
            <div class="flex flex-col justify-between gap-8 sm:flex-row sm:items-end">
                <div>
                    <p class="text-[.6rem] font-medium uppercase tracking-[.2em] text-white/45">Sudah kenal ruangnya?</p>
                    <h2 class="mt-4 text-3xl font-semibold leading-[1.05] tracking-[-.045em] sm:text-4xl">Sekarang, mulai<br><span class="text-white/45">langkah pertamamu.</span></h2>
                </div>
                <a href="{{ route('membership.index') }}" class="inline-flex w-fit shrink-0 items-center gap-8 rounded-full bg-white px-6 py-4 text-sm font-semibold text-[#171717] transition hover:bg-[#d4d4d4] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">Pilih membership <span aria-hidden="true">↗</span></a>
            </div>
        </div>
    </div>
</section>

<div class="relative -mb-px h-16 overflow-hidden bg-white sm:h-24" aria-hidden="true">
    <svg class="absolute inset-0 h-full w-full text-[#0a0a0a]" viewBox="0 0 1440 160" preserveAspectRatio="none" fill="currentColor"><path d="M0 94C260 28 470 34 740 104S1200 166 1440 62V160H0Z"/></svg>
</div>
@endsection
