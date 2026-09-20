@extends('layouts.app')

@section('title', 'Login - GymPinang')

@section('content')
<div class="mx-auto max-w-md rounded border bg-white p-6">
    <h1 class="mb-4 text-xl font-bold">Login</h1>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   class="mt-1 w-full rounded border border-gray-300 px-3 py-2">
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium">Password</label>
            <input id="password" name="password" type="password" required
                   class="mt-1 w-full rounded border border-gray-300 px-3 py-2">
            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember" value="1"> Ingat saya
        </label>

        <button type="submit" class="w-full rounded bg-green-800 px-4 py-2 font-medium text-white hover:bg-green-900">
            Login
        </button>
    </form>

    <p class="mt-4 text-sm">Belum punya akun? <a href="{{ route('register') }}" class="text-green-800 underline">Daftar</a></p>
</div>
@endsection