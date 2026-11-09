@extends('layouts.app')

@section('navbar', '')
@section('footer', '')
@section('floating-actions', '')
@section('main-class', 'flex min-h-svh w-full flex-1 items-center justify-center bg-[#f7f7f7] px-5 py-10 sm:px-8')

@section('content')
<div class="flex w-full max-w-[27rem] flex-col items-center gap-6">
    <section class="w-full max-w-[27rem] rounded-3xl border border-[#e5e5e5] bg-white px-6 py-8 shadow-[0_12px_40px_rgba(0,0,0,.05)] sm:px-10 sm:py-10" aria-labelledby="auth-title">
        <a href="{{ route('home') }}" class="mx-auto mb-9 block w-fit rounded text-[1.75rem] font-bold tracking-[-.06em] text-[#171717] transition hover:text-[#525252] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717]" aria-label="CukipGym beranda">CukipGym</a>

        @yield('auth-content')
    </section>
    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-[#737373] transition hover:bg-black/5 hover:text-[#171717] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717]">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 5-7 7 7 7m-7-7h14"/></svg>
        Kembali ke beranda
    </a>
</div>
@endsection
