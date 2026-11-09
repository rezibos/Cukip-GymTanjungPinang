@extends('layouts.auth')

@section('title', 'Daftar - CukipGym')
@section('description', 'Buat akun CukipGym dan mulai perjalanan latihanmu di Tanjungpinang.')
@section('auth-content')
<h1 id="auth-title" class="text-center text-lg font-semibold tracking-[-.035em]">Buat akun</h1>

<form method="POST" action="{{ route('register') }}" class="mt-6 flex flex-col gap-4">
    @csrf
    <div>
        <label for="name" class="mb-2 block text-xs font-semibold text-[#404040]">Nama lengkap</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="100" autocomplete="name" placeholder="Nama lengkapmu" class="h-12 w-full rounded-xl border border-[#e5e5e5] bg-[#fafafa] px-4 text-sm outline-none transition placeholder:text-[#a3a3a3] hover:border-[#bdbdbd] focus:border-[#171717] focus:bg-white focus:ring-2 focus:ring-black/5" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
        @error('name')<p id="name-error" class="mt-2 flex items-start gap-2 text-xs leading-5 text-[#525252]" role="alert"><span aria-hidden="true">ⓘ</span>{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="email" class="mb-2 block text-xs font-semibold text-[#404040]">Alamat email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="191" autocomplete="email" placeholder="nama@email.com" class="h-12 w-full rounded-xl border border-[#e5e5e5] bg-[#fafafa] px-4 text-sm outline-none transition placeholder:text-[#a3a3a3] hover:border-[#bdbdbd] focus:border-[#171717] focus:bg-white focus:ring-2 focus:ring-black/5" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
        @error('email')<p id="email-error" class="mt-2 flex items-start gap-2 text-xs leading-5 text-[#525252]" role="alert"><span aria-hidden="true">ⓘ</span>{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password" class="mb-2 block text-xs font-semibold text-[#404040]">Kata sandi</label>
        <div class="relative">
            <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" placeholder="Minimal 8 karakter" class="h-12 w-full rounded-xl border border-[#e5e5e5] bg-[#fafafa] pl-4 pr-24 text-sm outline-none transition placeholder:text-[#a3a3a3] hover:border-[#bdbdbd] focus:border-[#171717] focus:bg-white focus:ring-2 focus:ring-black/5" @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
            <button type="button" class="absolute inset-y-0 right-2 my-2 rounded-lg px-3 text-[.65rem] font-semibold text-[#737373] transition hover:bg-black/5 hover:text-[#171717] focus-visible:outline-2 focus-visible:outline-[#171717]" data-password-toggle aria-controls="password" aria-pressed="false" aria-label="Tampilkan kata sandi">Lihat</button>
        </div>
        @error('password')<p id="password-error" class="mt-2 flex items-start gap-2 text-xs leading-5 text-[#525252]" role="alert"><span aria-hidden="true">ⓘ</span>{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password_confirmation" class="mb-2 block text-xs font-semibold text-[#404040]">Konfirmasi kata sandi</label>
        <div class="relative">
            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" placeholder="Ulangi kata sandi" class="h-12 w-full rounded-xl border border-[#e5e5e5] bg-[#fafafa] pl-4 pr-24 text-sm outline-none transition placeholder:text-[#a3a3a3] hover:border-[#bdbdbd] focus:border-[#171717] focus:bg-white focus:ring-2 focus:ring-black/5" @error('password_confirmation') aria-invalid="true" aria-describedby="password-confirmation-error" @enderror>
            <button type="button" class="absolute inset-y-0 right-2 my-2 rounded-lg px-3 text-[.65rem] font-semibold text-[#737373] transition hover:bg-black/5 hover:text-[#171717] focus-visible:outline-2 focus-visible:outline-[#171717]" data-password-toggle aria-controls="password_confirmation" aria-pressed="false" aria-label="Tampilkan kata sandi">Lihat</button>
        </div>
        @error('password_confirmation')<p id="password-confirmation-error" class="mt-2 flex items-start gap-2 text-xs leading-5 text-[#525252]" role="alert"><span aria-hidden="true">ⓘ</span>{{ $message }}</p>@enderror
    </div>
    <button type="submit" class="mt-1 flex h-12 w-full items-center justify-center rounded-xl bg-[#171717] px-5 text-sm font-semibold text-white transition hover:bg-[#353535] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717]">
        Buat akun
    </button>
</form>

<p class="mt-6 text-center text-xs leading-6 text-[#737373]">Sudah punya akun? <a href="{{ route('login') }}" class="ml-1 font-semibold text-[#171717] underline decoration-[#d4d4d4] underline-offset-4 transition hover:decoration-[#171717]">Masuk</a></p>
@endsection
