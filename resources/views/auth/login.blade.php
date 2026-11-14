@extends('layouts.auth')

@section('title', 'Masuk - CukipGym')
@section('description', 'Masuk ke akun CukipGym untuk melihat membership, jadwal kelas, dan aktivitas latihanmu.')
@section('auth-content')
<h1 id="auth-title" class="text-center text-lg font-semibold tracking-[-.035em]">Masuk akun</h1>

<form method="POST" action="{{ route('login') }}" class="mt-6 flex flex-col gap-4">
    @csrf
    <div>
        <label for="email" class="mb-2 block text-xs font-semibold text-[#404040]">Alamat email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" placeholder="nama@email.com" class="h-12 w-full rounded-xl border border-[#e5e5e5] bg-[#fafafa] px-4 text-sm outline-none transition placeholder:text-[#a3a3a3] hover:border-[#bdbdbd] focus:border-[#171717] focus:bg-white focus:ring-2 focus:ring-black/5" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
        @error('email')<p id="email-error" class="mt-2 flex items-start gap-2 text-xs leading-5 text-[#525252]" role="alert"><span aria-hidden="true">ⓘ</span>{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password" class="mb-2 block text-xs font-semibold text-[#404040]">Kata sandi</label>
        <div class="relative">
            <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" class="h-12 w-full rounded-xl border border-[#e5e5e5] bg-[#fafafa] pl-4 pr-24 text-sm outline-none transition placeholder:text-[#a3a3a3] hover:border-[#bdbdbd] focus:border-[#171717] focus:bg-white focus:ring-2 focus:ring-black/5" @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
            <button type="button" class="absolute inset-y-0 right-2 my-2 rounded-lg px-3 text-[.65rem] font-semibold text-[#737373] transition hover:bg-black/5 hover:text-[#171717] focus-visible:outline-2 focus-visible:outline-[#171717]" data-password-toggle aria-controls="password" aria-pressed="false" aria-label="Tampilkan kata sandi">Lihat</button>
        </div>
        @error('password')<p id="password-error" class="mt-2 flex items-start gap-2 text-xs leading-5 text-[#525252]" role="alert"><span aria-hidden="true">ⓘ</span>{{ $message }}</p>@enderror
    </div>
    <label class="flex w-fit cursor-pointer items-center gap-2.5 py-1 text-xs text-[#737373]">
        <input type="checkbox" name="remember" value="1" @checked(old('remember')) class="size-4 cursor-pointer rounded border-[#d4d4d4] accent-[#171717]">Ingat saya
    </label>
    <button type="submit" class="flex h-12 w-full items-center justify-center rounded-xl bg-[#171717] px-5 text-sm font-semibold text-white transition hover:bg-[#353535] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717]">
        Masuk akun
    </button>
</form>

<p class="mt-6 text-center text-xs leading-6 text-[#737373]">Belum punya akun? Pilih membership dan selesaikan pembayaran. Akun akan dikirim ke emailmu.</p>
@endsection
