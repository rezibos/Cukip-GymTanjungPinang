@props(['trainer', 'fallbackImage'])

@php
    $dialogId = 'trainer-dialog-'.$trainer->id;
    $photoUrl = $trainer->photo_path ? Storage::disk('public')->url($trainer->photo_path) : asset($fallbackImage);
    $groupedSlots = $trainer->slots->groupBy(fn ($slot) => $slot->starts_at->toDateString());
    $availableSlots = $trainer->slots->filter(fn ($slot) => $slot->status === 'available' || ($slot->status === 'held' && $slot->held_until?->isPast()));
    $firstAvailableDate = $availableSlots->first()?->starts_at->toDateString();
    $certificationDocuments = collect($trainer->certification_documents ?? [])->filter()->values();
    $trainerDayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
@endphp

<article class="group overflow-hidden rounded-[1.5rem] border border-black/10 bg-white shadow-[0_18px_45px_rgba(74,51,40,.08)]">
    <button type="button" class="relative block aspect-[4/5] w-full cursor-pointer overflow-hidden bg-[#292725] text-left focus-visible:outline-2 focus-visible:outline-offset-[-4px] focus-visible:outline-white" data-trainer-dialog-open="{{ $dialogId }}" data-trainer-card-open aria-label="Lihat detail {{ $trainer->name }}">
        <img src="{{ $photoUrl }}" alt="" class="size-full object-cover" loading="lazy">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
        <span class="pointer-events-none absolute right-4 top-4 grid size-11 place-items-center rounded-full bg-white text-xl text-[#171717] shadow-lg transition group-hover:rotate-45" aria-hidden="true">+</span>
        <div class="absolute inset-x-0 bottom-0 p-6 text-white">
            <h3 class="text-2xl font-semibold tracking-[-.04em]">{{ $trainer->name }}</h3>
            <p class="mt-2 text-[.62rem] font-semibold uppercase tracking-[.14em] text-white/65">{{ $trainer->specialization }}</p>
        </div>
    </button>
</article>

<dialog id="{{ $dialogId }}" class="m-auto h-[min(88dvh,42rem)] w-[min(92vw,68rem)] overflow-hidden rounded-2xl bg-white p-0 text-[#171717] shadow-2xl backdrop:bg-black/60 max-lg:h-dvh max-lg:w-screen max-lg:max-w-none max-lg:rounded-none">
    <div class="flex size-full min-h-0 flex-col lg:grid lg:grid-cols-5">
        <div class="relative h-40 shrink-0 overflow-hidden bg-[#202020] sm:h-52 lg:col-span-2 lg:h-full">
            <img src="{{ $photoUrl }}" alt="{{ $trainer->name }}" class="absolute inset-0 size-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/35 via-transparent to-transparent"></div>
        </div>

        <div class="flex min-h-0 flex-1 flex-col overflow-hidden bg-white lg:col-span-3">
            <header class="flex items-start justify-between gap-5 border-b border-black/10 px-5 py-4 sm:px-7 sm:py-5">
                <div>
                    <p class="text-[.58rem] font-semibold uppercase tracking-[.18em] text-[#858585]">Personal trainer</p>
                    <h3 class="mt-2 text-3xl font-semibold tracking-[-.04em]">{{ $trainer->name }}</h3>
                    <p class="mt-1 text-sm text-[#737373]">{{ $trainer->specialization }}</p>
                </div>
                <button type="button" class="grid size-11 shrink-0 cursor-pointer place-items-center rounded-full border border-black/10 text-lg transition hover:bg-[#171717] hover:text-white" data-trainer-dialog-close aria-label="Tutup">×</button>
            </header>

            <div class="border-b border-black/10 px-5 py-4 sm:px-7">
                <p class="text-[.58rem] font-semibold uppercase tracking-[.14em] text-[#858585]">Tentang {{ $trainer->name }}</p>
                <p class="mt-2 text-sm leading-6 text-[#666]">{{ $trainer->description ?: 'Pelatih akan mendampingi latihanmu secara personal, membantu teknik gerakan, dan menyesuaikan program dengan kebutuhanmu.' }}</p>
                <div class="mt-4 grid grid-cols-3 divide-x divide-black/10 border-y border-black/10 py-3 text-center">
                    <div class="px-2"><strong class="block text-base">{{ $trainer->experience_years }}</strong><span class="mt-1 block text-[.55rem] uppercase tracking-[.1em] text-[#858585]">Tahun</span></div>
                    <div class="px-2"><strong class="block text-base">{{ $trainer->session_minutes / 60 }} jam</strong><span class="mt-1 block text-[.55rem] uppercase tracking-[.1em] text-[#858585]">Per sesi</span></div>
                    <div class="px-2"><strong class="block text-base">{{ count($trainer->certifications ?? []) }}</strong><span class="mt-1 block text-[.55rem] uppercase tracking-[.1em] text-[#858585]">Sertifikat</span></div>
                </div>
                @if (count($trainer->certifications ?? []) > 0 || $certificationDocuments->isNotEmpty())
                    <details class="mt-4" aria-label="Sertifikasi {{ $trainer->name }}">
                        <summary class="flex cursor-pointer list-none items-center justify-between text-xs font-semibold">Sertifikat & lisensi <span class="text-[#737373]">Lihat semua +</span></summary>
                        <div class="mt-2 max-h-40 overflow-y-auto divide-y divide-black/10">
                            @foreach ($trainer->certifications ?? [] as $index => $certificate)
                                @php($document = $certificationDocuments->get($index))
                                @if ($document)
                                    <a href="{{ Storage::disk('public')->url($document) }}" target="_blank" rel="noopener" class="flex items-center gap-3 py-3 text-left transition hover:text-[#737373]" data-trainer-certification data-trainer-certificate-document>
                                        <img src="{{ Storage::disk('public')->url($document) }}" alt="{{ $certificate }} milik {{ $trainer->name }}" class="h-12 w-16 rounded-lg object-cover">
                                        <span class="min-w-0"><strong class="block truncate text-[.65rem] font-semibold">{{ $certificate }}</strong><small class="mt-1 block text-[.55rem] text-[#737373]">Lihat dokumen <span aria-hidden="true">↗</span></small></span>
                                    </a>
                                @else
                                    <div class="flex items-center gap-3 py-3" data-trainer-certification>
                                        <span class="text-[#737373]" aria-hidden="true">✓</span>
                                        <strong class="text-xs font-medium leading-5">{{ $certificate }}</strong>
                                    </div>
                                @endif
                            @endforeach
                            @foreach ($certificationDocuments->slice(count($trainer->certifications ?? [])) as $document)
                                <a href="{{ Storage::disk('public')->url($document) }}" target="_blank" rel="noopener" class="flex items-center gap-3 py-3 text-left transition hover:text-[#737373]" data-trainer-certificate-document>
                                    <img src="{{ Storage::disk('public')->url($document) }}" alt="Dokumen sertifikat {{ $trainer->name }}" class="h-12 w-16 rounded-lg object-cover">
                                    <span><strong class="block text-[.65rem] font-semibold">Sertifikat tambahan</strong><small class="mt-1 block text-[.55rem] text-[#737373]">Lihat dokumen <span aria-hidden="true">↗</span></small></span>
                                </a>
                            @endforeach
                        </div>
                    </details>
                @endif
            </div>

            @auth
                <form method="POST" action="{{ route('trainer-bookings.store') }}" class="flex min-h-0 flex-1 flex-col" data-trainer-booking-form data-session-price="{{ $trainer->session_price }}">
                    @csrf
                    <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4 sm:px-7">
                        <div class="flex items-end justify-between gap-4">
                            <div><p class="text-[.58rem] font-semibold uppercase tracking-[.14em] text-[#858585]">Pilih jadwal</p><h4 class="mt-1 text-lg font-semibold">Tanggal dan jam sesi</h4></div>
                            <p class="text-right"><span class="block text-[.55rem] uppercase tracking-[.1em] text-[#858585]">Harga per sesi</span><strong class="mt-1 block text-lg">Rp{{ number_format($trainer->session_price, 0, ',', '.') }}</strong></p>
                        </div>

                        @if ($groupedSlots->isNotEmpty())
                            <div class="relative mt-4" data-trainer-calendar>
                            <div class="flex gap-2 overflow-x-auto px-10 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" role="group" aria-label="Pilih tanggal sesi" data-trainer-date-rail>
                                @foreach ($groupedSlots as $date => $slots)
                                    @php($isFullyBooked = ! $availableSlots->contains(fn ($slot) => $slot->starts_at->toDateString() === $date))
                                    <button type="button" class="min-w-[4.75rem] shrink-0 cursor-pointer rounded-xl border px-3 py-2.5 text-left transition disabled:cursor-not-allowed {{ $isFullyBooked ? 'border-[#171717] bg-[#171717] text-white' : ($date === $firstAvailableDate ? 'border-[#171717] bg-[#171717] text-white' : 'border-black/10 bg-white text-[#737373]') }}" data-trainer-date-button="{{ $date }}" aria-pressed="{{ $date === $firstAvailableDate ? 'true' : 'false' }}" @if ($isFullyBooked) disabled title="Semua sesi sudah dibooking" data-trainer-date-booked @endif>
                                        <span class="block text-[.55rem] font-semibold uppercase tracking-[.08em]">{{ $trainerDayNames[$slots->first()->starts_at->dayOfWeek] }}</span>
                                        <span class="mt-1 block text-base font-semibold">{{ $slots->first()->starts_at->format('d') }} <small class="text-[.58rem] font-normal">{{ $slots->first()->starts_at->format('M') }}</small></span>
                                        @if ($isFullyBooked)<span class="mt-1 block text-[.5rem] text-white/70">Sudah dibooking</span>@endif
                                    </button>
                                @endforeach
                            </div>
                            <button type="button" class="absolute left-0 top-1/2 grid size-9 -translate-y-1/2 cursor-pointer place-items-center rounded-full border border-black/15 bg-white text-lg shadow-sm disabled:cursor-default disabled:opacity-30" data-trainer-calendar-prev aria-label="Tanggal sebelumnya">‹</button>
                            <button type="button" class="absolute right-0 top-1/2 grid size-9 -translate-y-1/2 cursor-pointer place-items-center rounded-full border border-black/15 bg-white text-lg shadow-sm disabled:cursor-default disabled:opacity-30" data-trainer-calendar-next aria-label="Tanggal berikutnya">›</button>
                            </div>

                            <div class="mt-4 max-h-40 overflow-y-auto">
                                @foreach ($groupedSlots as $date => $slots)
                                    <fieldset data-trainer-date-panel="{{ $date }}" @if ($date !== $firstAvailableDate) hidden @endif>
                                        <legend class="sr-only">Jam sesi tersedia</legend>
                                        <div class="flex flex-col gap-2">
                                            @foreach ($slots as $slot)
                                                @continue(! $availableSlots->contains('id', $slot->id))
                                                <label class="cursor-pointer">
                                                    <input type="checkbox" name="slot_ids[]" value="{{ $slot->id }}" class="peer sr-only">
                                                    <span class="flex min-h-16 items-center justify-between gap-4 rounded-xl border border-black/10 bg-white px-4 py-3 text-[#171717] transition hover:border-black/30 peer-checked:border-[#171717] peer-checked:bg-[#171717] peer-checked:text-white peer-checked:[&_[data-session-check]]:bg-white peer-checked:[&_[data-session-check]]:text-black peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#171717]">
                                                        <span class="flex items-center gap-3">
                                                            <span class="grid size-5 shrink-0 place-items-center rounded-full border border-black/20 text-[.6rem] text-transparent" data-session-check aria-hidden="true">✓</span>
                                                            <span><strong class="block text-sm font-semibold">{{ $slot->starts_at->format('H:i') }} – {{ $slot->ends_at->format('H:i') }}</strong><small class="mt-0.5 block text-[.65rem] opacity-60">Latihan privat</small></span>
                                                        </span>
                                                        <span class="shrink-0 text-xs font-medium opacity-70">{{ $slot->starts_at->diffInMinutes($slot->ends_at) / 60 }} jam</span>
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </fieldset>
                                @endforeach
                            </div>
                            @if ($availableSlots->isEmpty())<p class="mt-3 text-xs text-[#737373]">Semua sesi sudah dibooking. Silakan cek jadwal berikutnya.</p>@endif
                        @else
                            <p class="mt-4 rounded-xl bg-[#f5f5f5] p-4 text-xs leading-6 text-[#737373]">Belum ada sesi tersedia. Cek kembali nanti.</p>
                        @endif
                    </div>

                    @if ($groupedSlots->isNotEmpty())
                        <div class="flex items-center gap-4 border-t border-black/10 bg-white px-5 py-3.5 sm:px-7" data-trainer-booking-summary>
                            <div class="min-w-0 flex-1"><p class="text-[.6rem] uppercase tracking-[.12em] text-[#858585]"><span data-trainer-count>0</span> sesi dipilih</p><strong class="mt-1 block text-xl" data-trainer-total>Rp0</strong></div>
                            <button type="submit" disabled class="inline-flex min-h-12 cursor-pointer items-center gap-8 rounded-full bg-[#171717] px-6 text-xs font-semibold text-white transition hover:bg-[#404040] disabled:cursor-not-allowed disabled:opacity-35" data-trainer-submit>Booking dan bayar <span aria-hidden="true">→</span></button>
                        </div>
                    @endif
                </form>
            @else
                <div class="mt-auto border-t border-black/10 p-6 sm:p-8"><p class="text-sm leading-6 text-[#737373]">Login terlebih dahulu untuk memilih jadwal dan melakukan pembayaran sesi privat.</p><a href="{{ route('login') }}" class="mt-4 flex min-h-12 items-center justify-between rounded-full bg-[#171717] px-6 text-xs font-semibold text-white">Login untuk booking <span aria-hidden="true">→</span></a></div>
            @endauth
        </div>
    </div>
</dialog>
