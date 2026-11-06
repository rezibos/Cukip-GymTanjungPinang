@extends('layouts.app')

@section('title', 'Daftar - CukipGym')
@section('description', 'Buat akun CukipGym dan mulai perjalanan latihanmu di Tanjungpinang.')
@section('main-class', 'flex min-h-svh w-full flex-1 items-center bg-[#f5f5f5] px-5 pb-8 pt-28 sm:px-8 lg:px-12')
@section('footer', '')

@section('content')
<section class="mx-auto grid w-full max-w-[80rem] overflow-hidden rounded-[2rem] border border-black/[.06] bg-white shadow-[0_24px_80px_rgba(0,0,0,.04)] lg:min-h-[46rem] lg:grid-cols-2" aria-labelledby="register-title" data-navbar-tone="light">
    <div class="relative isolate h-48 overflow-hidden bg-[#171717] sm:h-60 lg:h-auto" data-navbar-tone="dark">
        <img src="{{ asset('images/gym-kettlebell-editorial.webp') }}" alt="Latihan kettlebell di area gym CukipGym" class="absolute inset-0 -z-20 h-full w-full object-cover object-center grayscale">
        <div class="absolute inset-0 -z-10 bg-[linear-gradient(180deg,rgba(0,0,0,.12)_0%,rgba(0,0,0,.2)_30%,rgba(0,0,0,.85)_100%)]" aria-hidden="true"></div>
        <div class="flex h-full flex-col justify-end p-7 text-white sm:p-9 lg:p-12">
            <div class="lg:pb-3">
                <p class="text-[clamp(2.25rem,4.5vw,4.5rem)] font-semibold leading-[.95] tracking-[-.06em]">Mulai hari ini.<br><span class="text-white/60">Tumbuh setiap hari.</span></p>
                <p class="mt-6 hidden max-w-xs text-sm leading-7 text-white/60 lg:block">Temukan ritme latihanmu, bangun kebiasaan baru, dan jadi bagian dari komunitas CukipGym.</p>
                <div class="mt-10 hidden items-center gap-2 text-[.6rem] font-medium uppercase tracking-[.18em] text-white/45 lg:flex">
                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    Tanjungpinang, Indonesia
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col justify-center px-6 py-9 sm:px-12 sm:py-10 lg:px-14 xl:px-16">
        <div class="mx-auto w-full max-w-sm">
            <p class="text-[.6rem] font-semibold uppercase tracking-[.22em] text-[#858585]">Bergabung dengan CukipGym</p>
            <h1 id="register-title" class="mt-4 text-[clamp(2rem,3vw,3rem)] font-semibold leading-[1.05] tracking-[-.055em]">Mulai perjalananmu<span class="text-[#a3a3a3]">.</span></h1>
            <p class="mt-4 text-sm leading-7 text-[#737373]">Buat akunmu. Langkah pertamamu dimulai di sini.</p>

            <form method="POST" action="{{ route('register') }}" class="mt-7 flex flex-col gap-4">
                @csrf
                <div>
                    <label for="name" class="mb-2 block text-xs font-semibold text-[#404040]">Nama lengkap</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="100" autocomplete="name" placeholder="Nama lengkapmu" class="h-13 w-full rounded-xl border border-[#e5e5e5] bg-[#fafafa] px-4 text-sm outline-none transition placeholder:text-[#a3a3a3] hover:border-[#bdbdbd] focus:border-[#171717] focus:bg-white focus:ring-2 focus:ring-black/5" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                    @error('name')<p id="name-error" class="mt-2 flex items-start gap-2 text-xs leading-5 text-[#525252]" role="alert"><span aria-hidden="true">ⓘ</span>{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="mb-2 block text-xs font-semibold text-[#404040]">Alamat email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="191" autocomplete="email" placeholder="nama@email.com" class="h-13 w-full rounded-xl border border-[#e5e5e5] bg-[#fafafa] px-4 text-sm outline-none transition placeholder:text-[#a3a3a3] hover:border-[#bdbdbd] focus:border-[#171717] focus:bg-white focus:ring-2 focus:ring-black/5" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                    @error('email')<p id="email-error" class="mt-2 flex items-start gap-2 text-xs leading-5 text-[#525252]" role="alert"><span aria-hidden="true">ⓘ</span>{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="mb-2 block text-xs font-semibold text-[#404040]">Kata sandi</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" placeholder="Minimal 8 karakter" class="h-13 w-full rounded-xl border border-[#e5e5e5] bg-[#fafafa] pl-4 pr-24 text-sm outline-none transition placeholder:text-[#a3a3a3] hover:border-[#bdbdbd] focus:border-[#171717] focus:bg-white focus:ring-2 focus:ring-black/5" @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                        <button type="button" class="absolute inset-y-0 right-2 my-2 rounded-lg px-3 text-[.65rem] font-semibold text-[#737373] transition hover:bg-black/5 hover:text-[#171717] focus-visible:outline-2 focus-visible:outline-[#171717]" data-password-toggle aria-controls="password" aria-pressed="false" aria-label="Tampilkan kata sandi">Lihat</button>
                    </div>
                    @error('password')<p id="password-error" class="mt-2 flex items-start gap-2 text-xs leading-5 text-[#525252]" role="alert"><span aria-hidden="true">ⓘ</span>{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="mb-2 block text-xs font-semibold text-[#404040]">Konfirmasi kata sandi</label>
                    <div class="relative">
                        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" placeholder="Ulangi kata sandi" class="h-13 w-full rounded-xl border border-[#e5e5e5] bg-[#fafafa] pl-4 pr-24 text-sm outline-none transition placeholder:text-[#a3a3a3] hover:border-[#bdbdbd] focus:border-[#171717] focus:bg-white focus:ring-2 focus:ring-black/5" @error('password_confirmation') aria-invalid="true" aria-describedby="password-confirmation-error" @enderror>
                        <button type="button" class="absolute inset-y-0 right-2 my-2 rounded-lg px-3 text-[.65rem] font-semibold text-[#737373] transition hover:bg-black/5 hover:text-[#171717] focus-visible:outline-2 focus-visible:outline-[#171717]" data-password-toggle aria-controls="password_confirmation" aria-pressed="false" aria-label="Tampilkan kata sandi">Lihat</button>
                    </div>
                    @error('password_confirmation')<p id="password-confirmation-error" class="mt-2 flex items-start gap-2 text-xs leading-5 text-[#525252]" role="alert"><span aria-hidden="true">ⓘ</span>{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="group mt-2 flex h-13 w-full items-center justify-between rounded-xl bg-[#171717] px-5 text-sm font-semibold text-white transition hover:bg-[#353535] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717]">
                    Buat akun
                    <svg class="size-5 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </button>
            </form>

            <p class="mt-6 text-center text-xs leading-6 text-[#858585]">Sudah punya akun? <a href="{{ route('login') }}" class="ml-1 font-semibold text-[#171717] underline decoration-[#d4d4d4] underline-offset-4 transition hover:decoration-[#171717]">Masuk sekarang</a></p>
            <a href="{{ route('home') }}" class="mx-auto mt-6 flex w-fit items-center gap-2 text-[.65rem] text-[#a3a3a3] transition hover:text-[#171717]"><span aria-hidden="true">←</span> Kembali ke beranda</a>
        </div>
    </div>
</section>
@endsection
