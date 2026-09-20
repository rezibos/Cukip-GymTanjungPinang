@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan - GymPinang')

@section('content')
<div class="py-12 text-center">
    <p class="text-5xl font-bold text-green-800">404</p>
    <h1 class="mt-3 text-xl font-semibold">Halaman tidak ditemukan</h1>
    <p class="mt-2 text-gray-600">Alamat yang Anda buka tidak ada atau sudah dipindahkan.</p>
    <a href="{{ route('home') }}" class="mt-6 inline-block rounded bg-green-800 px-5 py-3 font-medium text-white hover:bg-green-900">Kembali ke Beranda</a>
</div>
@endsection