@extends('layouts.app')

@section('title', $gym->name . ' - GymPinang')
@section('description', \Illuminate\Support\Str::limit(strip_tags($gym->description), 155))

@section('content')
<p class="text-sm">
    <a href="{{ route('gyms.index') }}" class="text-green-800 hover:underline">&larr; Semua gym</a>
</p>

{{-- Judul --}}
<div class="mt-2">
    <p class="text-xs font-medium uppercase tracking-wide text-green-800">{{ $gym->category->name }}</p>
    <h1 class="break-words text-2xl font-bold sm:text-3xl">{{ $gym->name }}</h1>
    <div class="mt-1">
        <x-rating-stars :rating="$gym->approved_reviews_avg_rating" :count="$gym->approved_reviews_count" />
    </div>
</div>

{{-- Foto utama --}}
<div class="mt-4 overflow-hidden rounded-lg border border-gray-200 bg-white">
    @if ($gym->image_url)
        <img src="{{ $gym->image_url }}" alt="Foto {{ $gym->name }}"
             class="aspect-[16/9] max-h-96 w-full object-cover">
    @else
        <div class="flex aspect-[16/9] max-h-96 w-full items-center justify-center bg-green-50 text-5xl font-semibold text-green-800"
             role="img" aria-label="Foto {{ $gym->name }} belum tersedia">
            {{ mb_strtoupper(mb_substr($gym->name, 0, 2)) }}
        </div>
    @endif
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-3">

    {{-- Info + kontak. Di mobile tampil lebih dulu, di desktop pindah ke kanan --}}
    <aside class="min-w-0 space-y-4 lg:order-2">
        <div class="rounded-lg border border-gray-200 bg-white p-4 text-sm">
            <h2 class="text-base font-semibold">Informasi</h2>

            <dl class="mt-3 space-y-3">
                <div>
                    <dt class="text-gray-500">Alamat</dt>
                    <dd class="break-words">{{ $gym->address }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Wilayah</dt>
                    <dd>{{ $gym->district ? $gym->district . ', ' : '' }}{{ $gym->region->value }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Jam operasional</dt>
                    <dd>{{ $gym->opening_hours }}</dd>
                </div>
                @if ($gym->phone)
                    <div>
                        <dt class="text-gray-500">Telepon</dt>
                        <dd><a href="tel:{{ preg_replace('/[^0-9+]/', '', $gym->phone) }}" class="text-green-800 hover:underline">{{ $gym->phone }}</a></dd>
                    </div>
                @endif
            </dl>

            <div class="mt-4 flex flex-col gap-2">
                @if ($gym->whatsapp_url)
                    <a href="{{ $gym->whatsapp_url }}" target="_blank" rel="noopener noreferrer"
                       class="rounded bg-green-800 px-4 py-3 text-center font-medium text-white hover:bg-green-900">
                        Hubungi Gym via WhatsApp
                    </a>
                @endif
                @if ($gym->maps_url)
                    <a href="{{ $gym->maps_url }}" target="_blank" rel="noopener noreferrer"
                       class="rounded border border-green-800 px-4 py-3 text-center font-medium text-green-800 hover:bg-green-50">
                        Buka di Google Maps
                    </a>
                @endif
            </div>
        </div>
    </aside>

    {{-- Konten utama --}}
    <div class="min-w-0 space-y-8 lg:order-1 lg:col-span-2">

        <section aria-labelledby="judul-tentang">
            <h2 id="judul-tentang" class="text-xl font-semibold">Tentang Gym</h2>
            <p class="mt-2 whitespace-pre-line break-words leading-relaxed text-gray-700">{{ $gym->description }}</p>
        </section>

        <section aria-labelledby="judul-fasilitas">
            <h2 id="judul-fasilitas" class="text-xl font-semibold">Fasilitas</h2>
            @if ($gym->facilities->count())
                <ul class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2 text-sm sm:grid-cols-3">
                    @foreach ($gym->facilities as $facility)
                        <li class="flex items-start gap-2">
                            <span class="text-green-800" aria-hidden="true">&#10003;</span>
                            <span>{{ $facility->name }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-2 text-sm text-gray-500">Informasi fasilitas belum tersedia.</p>
            @endif
        </section>

        <section aria-labelledby="judul-paket">
            <h2 id="judul-paket" class="text-xl font-semibold">Paket Gym</h2>
            @if ($gym->packages->count())
                <ul class="mt-3 divide-y divide-gray-200 rounded-lg border border-gray-200 bg-white">
                    @foreach ($gym->packages as $package)
                        <li class="flex items-center justify-between gap-4 p-4">
                            <div class="min-w-0">
                                <p class="font-medium">{{ $package->name }}</p>
                                <p class="text-sm text-gray-500">{{ $package->duration }}</p>
                                @if ($package->description)
                                    <p class="text-sm text-gray-500">{{ $package->description }}</p>
                                @endif
                            </div>
                            <p class="shrink-0 font-semibold">{{ $package->formatted_price }}</p>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-2 text-sm text-gray-500">Paket harga belum tersedia.</p>
            @endif
        </section>

        @if ($gym->images->count())
            <section aria-labelledby="judul-galeri">
                <h2 id="judul-galeri" class="text-xl font-semibold">Galeri</h2>
                <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($gym->images as $image)
                        <img src="{{ $image->url }}"
                             alt="{{ $image->caption ?: 'Foto ' . $gym->name }}"
                             loading="lazy" class="aspect-[4/3] w-full rounded-lg object-cover">
                    @endforeach
                </div>
            </section>
        @endif

        <section aria-labelledby="judul-lokasi">
            <h2 id="judul-lokasi" class="text-xl font-semibold">Lokasi</h2>
            <p class="mt-2 text-sm text-gray-700">{{ $gym->address }}</p>

            @if ($gym->latitude !== null && $gym->longitude !== null)
                <div class="mt-3">
                    <x-gym-map :markers="[$gym->toMapMarker(false)]" />
                </div>
            @else
                <p class="mt-2 text-sm text-gray-500">Koordinat lokasi belum tersedia.</p>
            @endif
        </section>

        {{-- Review --}}
        <section id="ulasan" aria-labelledby="judul-review">
            <h2 id="judul-review" class="text-xl font-semibold">Rating dan Review</h2>

            @auth
                <form method="POST" action="{{ route('reviews.store', $gym) }}"
                      class="mt-3 space-y-4 rounded-lg border border-gray-200 bg-white p-4">
                    @csrf

                    <p class="text-sm font-medium">
                        {{ $myReview ? 'Ubah review Anda' : 'Tulis review Anda' }}
                    </p>
                    @if ($myReview)
                        <p class="text-sm text-gray-500">Anda sudah pernah mereview gym ini. Mengirim lagi akan memperbarui review lama.</p>
                    @endif

                    <fieldset>
                        <legend class="text-sm font-medium">Rating</legend>
                        <div class="mt-1 flex flex-wrap gap-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <label class="flex cursor-pointer items-center gap-1 rounded border border-gray-300 px-3 py-2 text-sm has-[:checked]:border-green-800 has-[:checked]:bg-green-50">
                                    <input type="radio" name="rating" value="{{ $i }}" required class="accent-green-800"
                                           @checked((int) old('rating', $myReview?->rating) === $i)>
                                    {{ $i }} <span class="text-amber-500" aria-hidden="true">★</span>
                                </label>
                            @endfor
                        </div>
                        @error('rating') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </fieldset>

                    <div>
                        <label for="review" class="block text-sm font-medium">Komentar</label>
                        <textarea id="review" name="review" rows="4" required maxlength="1000"
                                  class="mt-1 w-full rounded border border-gray-300 px-3 py-2">{{ old('review', $myReview?->review) }}</textarea>
                        @error('review') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="w-full rounded bg-green-800 px-5 py-2 font-medium text-white hover:bg-green-900 sm:w-auto">
                        Kirim Review
                    </button>
                </form>
            @else
                <p class="mt-3 rounded-lg border border-dashed border-gray-300 bg-white p-4 text-sm">
                    Untuk memberi rating dan review,
                    <a href="{{ route('login') }}" class="text-green-800 underline">login</a>
                    atau <a href="{{ route('register') }}" class="text-green-800 underline">daftar</a> dulu.
                </p>
            @endauth

            <div class="mt-4 space-y-3">
                @forelse ($reviews as $review)
                    <article class="rounded-lg border border-gray-200 bg-white p-4 text-sm">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="font-medium">{{ $review->user->name }}</p>
                            <p class="text-gray-500">{{ $review->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div class="mt-1"><x-rating-stars :rating="$review->rating" /></div>
                        <p class="mt-2 whitespace-pre-line break-words text-gray-700">{{ $review->review }}</p>
                    </article>
                @empty
                    <p class="text-sm text-gray-500">Belum ada review untuk gym ini.</p>
                @endforelse
            </div>

            <div class="mt-4">{{ $reviews->fragment('ulasan')->links() }}</div>
        </section>

    </div>
</div>
@endsection