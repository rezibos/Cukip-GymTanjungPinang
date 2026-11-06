@extends('layouts.app')

@section('title', 'Akses Ditolak - GymPinang')

@section('content')
<div class="py-12 text-center">
    <p class="text-5xl font-bold text-neutral-900">403</p>
    <h1 class="mt-3 text-xl font-semibold">Akses ditolak</h1>
    <p class="mt-2 text-gray-600">Anda tidak memiliki izin untuk membuka halaman ini.</p>
    <a href="{{ route('home') }}" class="mt-6 inline-block rounded bg-neutral-900 px-5 py-3 font-medium text-white hover:bg-black">Kembali ke Beranda</a>
</div>
@endsection