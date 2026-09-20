@extends('layouts.app')

@section('title', 'Register - GymPinang')

@section('content')
<div class="mx-auto max-w-md rounded border bg-white p-6">
    <h1 class="mb-4 text-xl font-bold">Daftar Akun</h1>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium">Nama</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                   class="mt-1 w-full rounded border border-gray-300 px-3 py-2">
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                   class="mt-1 w-full rounded border border-gray-300 px-3 py-2">
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium">Password</label>
            <input id="password" name="password" type="password" required
                   class="mt-1 w-full rounded border border-gray-300 px-3 py-2">
            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium">Konfirmasi Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                   class="mt-1 w-full rounded border border-gray-300 px-3 py-2">
        </div>

        <button type="submit" class="w-full rounded bg-green-800 px-4 py-2 font-medium text-white hover:bg-green-900">
            Daftar
        </button>
    </form>

    <p class="mt-4 text-sm">Sudah punya akun? <a href="{{ route('login') }}" class="text-green-800 underline">Login</a></p>
</div>
@endsection