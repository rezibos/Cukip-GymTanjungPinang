@extends('layouts.app')

@section('title', 'Paket Membership - CukipGym')

@section('content')
    <h1 class="text-2xl font-bold">Paket Membership</h1>
    <p class="mt-1 text-sm text-gray-600">Pilih paket yang sesuai kebutuhan Anda.</p>

    @if (session('error'))
        <div class="mt-4 rounded border border-red-300 bg-red-50 px-4 py-2 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($plans as $plan)
            <x-plan-card :plan="$plan" />
        @endforeach
    </div>
@endsection