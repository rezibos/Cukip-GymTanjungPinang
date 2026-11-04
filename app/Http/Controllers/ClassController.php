<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use Illuminate\Support\Carbon;

class ClassController extends Controller
{
    public function index()
    {
        $start = Carbon::today();
        $end = Carbon::today()->addDays(6);

        $sessions = ClassSession::with('gymClass')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->withCount(['activeBookings'])
            ->orderBy('date')
            ->get()
            ->groupBy(fn (ClassSession $s) => $s->date->toDateString());

        // Booking milik user yang sedang login, untuk menandai tombol "Sudah Booking"
        $myBookedSessionIds = auth()->check()
            ? auth()->user()->classBookings()->where('status', 'booked')->pluck('class_session_id')->all()
            : [];

        return view('classes.index', compact('sessions', 'start', 'end', 'myBookedSessionIds'));
    }
}